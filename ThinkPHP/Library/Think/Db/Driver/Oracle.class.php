<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 2016-6-23  默鱼 参照网上资料修改  http://www.thinkphp.cn/code/1099.html

namespace Think\Db\Driver;
use Think\Db\Driver;

/**
 * Oracle数据库驱动
 */
class Oracle extends Driver{

    private     $table        = '';
    protected   $selectSql    = 'SELECT * FROM (SELECT thinkphp.*, rownum AS numrow FROM (SELECT  %DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%) thinkphp ) %LIMIT%%COMMENT%';

    /**
     * 解析pdo连接的dsn信息
     * @access public
     * @param array $config 连接信息
     * @return string
     */
    protected function parseDsn($config){
        $dsn  =   'oci:dbname=//'.$config['hostname'].($config['hostport']?':'.$config['hostport']:'').'/'.$config['database'];
        if(!empty($config['charset'])) {
            $dsn  .= ';charset='.$config['charset'];
        }
        return $dsn;
    }

    /**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @param boolean $fetchSql  不执行只是获取SQL     
     * @return integer
     */
    public function execute($str,$fetchSql=false) {
         $bind = $this->bind; //新增这句
        $this->initConnect(true);
        if ( !$this->_linkID ) return false;
        $this->queryStr = $str;
        if(!empty($this->bind)){
            $that   =   $this;
            $this->queryStr =   strtr($this->queryStr,array_map(function($val) use($that){ return '\''.$that->escapeString($val).'\''; },$this->bind));
        }
        if($fetchSql){
            return $this->queryStr;
        }
        $flag = false;
        if(preg_match("/^\s*(INSERT\s+INTO)\s+(\w+)\s+/i", $str, $match)) {
            $this->table = C("DB_SEQUENCE_PREFIX").str_ireplace(C("DB_PREFIX"), "", $match[2]);
            $flag = (boolean)$this->query("SELECT * FROM user_sequences WHERE sequence_name='" . strtoupper($this->table) . "'");
        }
        //释放前次的查询结果
        if ( !empty($this->PDOStatement) ) $this->free();
        $this->executeTimes++;
        N('db_write',1); // 兼容代码        
        // 记录开始执行时间
        $this->debug(true);
        $this->PDOStatement	=	$this->_linkID->prepare($str);
        
     
        if(false === $this->PDOStatement) {
            $this->error();
            return false;
        }
        
        $this->bind = $this->bind ? $this->bind : $bind; //新增这句
        foreach ($this->bind as $key => $val) {
       
            if(is_array($val)){
                $this->PDOStatement->bindValue($key, $val[0], $val[1]);
            }else{
                $this->PDOStatement->bindValue($key, $val);
            }
        }

        $this->bind =   array();        
        $result	=	$this->PDOStatement->execute();
        $this->debug(false);
        if ( false === $result) {
            $this->error();
            return false;
        } else {
            $this->numRows = $this->PDOStatement->rowCount();
            if($flag || preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $str)) {
//                $this->lastInsID = $this->_linkID->lastInsertId();
                //$this->lastInsID = $this->_linkID->lastInsertId();
                  $this->lastInsID = $this->lastInsertId($this->table);
            }
            return $this->numRows;
        }
    }
    
    
    public function execProcedure($pName,$pValue) {
        $pValue = array_change_key_case($pValue, CASE_LOWER);
        
        //释放前次的查询结果
        if ( $this->queryID ) $this->free();
        
        //启动事务，开始过程处理
        if(false === $this->startTrans()){
            $this->error();
            return FALSE;
        }
        
        N('db_write',1);
        // 记录开始执行时间
        G('queryStartTime'); 
        
        //开始组存储过程执行语句
        $argSql = "SELECT ARGUMENT_NAME,IN_OUT,DATA_TYPE FROM All_ARGUMENTS WHERE OBJECT_NAME = '".strtoupper($pName)."' ORDER BY SEQUENCE";
        $argRow = $this->query($argSql);
        $ParStr = '';
        foreach($argRow as $key=>$item){
           $ParStr .= ",:".$item['argument_name'];
        }
        $ParStr = ltrim($ParStr,',');
        $this->queryID = oci_parse($this->_linkID, 'BEGIN '.$pName.'('.$ParStr.');END;');
        
        //开始绑定存储过程参数
        foreach($argRow as $key=>$value){
            $parmName = strtolower($value['argument_name']);
            $parmType = strtolower($value['in_out']);
            $dataType = strtolower($value['data_type']);
            
            if(!isset($pValue[$parmName]['value'])){
                $pValue[$parmName]['value']='';
            }
            if(!isset($pValue[$parmName]['type'])){
                $pValue[$parmName]['type'] = SQLT_CHR;
            }
            if(!isset($pValue[$parmName]['length'])){
                $pValue[$parmName]['length']= -1;
            }            
            if($dataType === 'clob'){
                $clobTemp = $pValue[$parmName]['value'];
                $pValue[$parmName]['value'] = oci_new_descriptor($this->_linkID, OCI_D_LOB);;
                $pValue[$parmName]['type'] = OCI_B_CLOB;
                $pValue[$parmName]['length']= -1;
            }
                        
            if($parmType === 'in'){                
                oci_bind_by_name($this->queryID, ":".$parmName, $pValue[$parmName]['value'],$pValue[$parmName]['length'],$pValue[$parmName]['type']);
            }else{
                $OutData[$parmName] = $pValue[$parmName]['value'];
                oci_bind_by_name($this->queryID, ":".$parmName, $OutData[$parmName],$pValue[$parmName]['length'],$pValue[$parmName]['type']);
                if($dataType === 'clob'){
                    $OutData[$parmName]->WriteTemporary($clobTemp);
                }
            }
        }
        oci_execute($this->queryID,$this->mode);
        $this->debug();
        if (!$this->queryID) {
            $this->error();
            $this->rollback();
            return $this->queryID;
        }else {
            foreach($argRow as $key=>$value){
                $parmName = strtolower($value['argument_name']);
                $parmType = strtolower($value['in_out']);
                $dataType = strtolower($value['data_type']);
                if($dataType === 'clob' && isset($OutData[$parmName])){
                    $resultTemp = $OutData[$parmName]->load();
                    $OutData[$parmName]->free();
                    $OutData[$parmName] = $resultTemp;
                }
            }
            $this->commit();
            $this->free();
            return $OutData;
        }
    }

        /**
     * 取得Oracle最近插入的ID
     * @access public
     */
    public function lastInsertId($sequence = '') {
        try {
            $lastInsID = $this->_linkID->lastInsertId();
        } catch(\PDOException $e) {
            //对于驱动不支持PDO::lastInsertId()的情况
            try {
                $lastInsID = 0;
                $seqPrefix = C("DB_SEQUENCE_PREFIX") ? C("DB_SEQUENCE_PREFIX") : 'seq_';
                $sequence = $sequence ? $sequence : $seqPrefix.$this->table;
                $q = $this->query("SELECT {$sequence}.CURRVAL as t FROM DUAL");
                if($q) {
                    $lastInsID = $q[0]['t'];
                }
            } catch(\Exception $e) {
                //print "Error!: " . $e->getMessage() . "</br>";
                //exit;
            }
        }
        return $lastInsID;
    }
    
    /**
     * 取得数据表的字段信息
     * @access public
     */
     public function getFields($tableName) {
        list($tableName) = explode(' ', $tableName);
        $result = $this->query("select a.column_name,data_type,decode(nullable,'Y',0,1) notnull,data_default,decode(a.column_name,b.column_name,1,0) pk "
                  ."from user_tab_columns a,(select column_name from user_constraints c,user_cons_columns col "
          ."where c.constraint_name=col.constraint_name and c.constraint_type='P'and c.table_name='".strtoupper($tableName)
          ."') b where table_name='".strtoupper($tableName)."' and a.column_name=b.column_name(+)");
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $info[strtolower($val['column_name'])] = array(
                    'name'    => strtolower($val['column_name']),
                    'type'    => strtolower($val['data_type']),
                    'notnull' => $val['notnull'],
                    'default' => $val['data_default'],
                    'primary' => $val['pk'],
                    'autoinc' => $val['pk'],
                );
            }
        }
        return $info;
    }

    /**
     * 取得数据库的表信息（暂时实现取得用户表信息）
     * @access public
     */
    public function getTables($dbName='') {
        $result = $this->query("select table_name from user_tables");
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL指令
     * @return string
     */
    public function escapeString($str) {
        return str_ireplace("'", "''", $str);
    }

    /**
     * limit
     * @access public
     * @return string
     */
	public function parseLimit($limit) {
        $limitStr    = '';
        if(!empty($limit)) {
            $limit	=	explode(',',$limit);
            if(count($limit)>1)
                $limitStr = "(numrow>" . $limit[0] . ") AND (numrow<=" . ($limit[0]+$limit[1]) . ")";
            else
                $limitStr = "(numrow>0 AND numrow<=".$limit[0].")";
        }
        return $limitStr?' WHERE '.$limitStr:'';
    }

    /**
     * 设置锁机制
     * @access protected
     * @return string
     */
    protected function parseLock($lock=false) {
        if(!$lock) return '';
        return ' FOR UPDATE NOWAIT ';
    }
}
