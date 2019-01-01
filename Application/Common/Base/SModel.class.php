<?php

/*
 * 单语言表基类
 */

namespace Common\Base;

use Think\Model\RelationModel;

abstract class SModel extends RelationModel {

    public $basetablec = "";
    protected $basetable = "";
    protected $basetable_cah = "";
    protected $langtextnameroot;
    protected $langdb;
    protected $idname = "id"; //键名
    protected $delmark = 'isdel'; //删除标记字段
    protected $partition = array(
        'field' => 'tablesuf', // 要分表的字段 通常数据会根据某个字段的值按照规则进行分表,我们这里按照用户的id进行分表
        'type' => 'mod', // 分表的规则 包括id year mod md5 函数 和首字母，此处选择mod（求余）的方式
        'expr' => '', // 分表辅助表达式 可选 配合不同的分表规则，这个参数没有深入研究
        'num' => '1', // 分表的数目 可选 实际分表的数量，在建表阶段就要确定好数量，后期不能增减表的数量
    );

    function __construct($data) {
        if ($data) {
            if (isset($data['link'])) {
                $this->_link = $data['link'];
            }
            if (isset($data['basetable'])) {
                $this->basetable = $data['basetable'];
            }
            if (isset($data['partition'])) {
                $this->partition = $data['partition'];
            }
            if (isset($data['idname'])) {
                $this->idname = $data['idname'];
            }
        }
        $this->tableName = $this->basetable;
        $this->basetablec = $this->basetable; //当前基础表
        $prefix = C("DB_PREFIX");
        $this->basetable_cah = $prefix . $this->basetable; //当前基表全名
        parent::__construct();
    }

    /**
     * 得到分表的的数据表名
     * @access public
     * @param array $data 操作的数据
     * @return string
     */
    public function getPartitionTableName($data = array()) {
        // 对数据表进行分区
        $seq = "";
        if (isset($data[$this->partition['field']])) {
            $field = $data[$this->partition['field']];
            switch ($this->partition['type']) {
                case 'id':
                    // 按照id范围分表
                    $step = $this->partition['expr'];
                    $seq = floor($field / $step) + 1;
                    break;
                case 'year':
                    // 按照年份分表
                    if (!is_numeric($field)) {
                        $field = strtotime($field);
                    }
                    $seq = date('Y', $field) - $this->partition['expr'] + 1;
                    break;
                case 'mod':
                    // 按照id的模数分表
                    $seq = ($field % $this->partition['num']) + 1;
                    break;
                case 'md5':
                    // 按照md5的序列分表
                    $seq = (ord(substr(md5($field), 0, 1)) % $this->partition['num']) + 1;
                    break;
                default :
                    if (function_exists($this->partition['type'])) {
                        // 支持指定函数哈希
                        $fun = $this->partition['type'];
                        $seq = (ord(substr($fun($field), 0, 1)) % $this->partition['num']) + 1;
                    } else {
                        $seq = $field;
                    }
            }
        }
        if ($seq == 1) {
            $seq = "";
        }
        $prefix = C("DB_PREFIX");
        $this->basetable = $this->basetablec . $seq;
        $this->basetable_cah = $prefix . $this->basetable;
        return $seq;
    }

    /**
     * 取所有表
     * @return type
     */
    public function getAllPartition($tp = 1) {
        if ($this->partition['num'] < 2) {
            return null;
        }
        $tableName = array();
        for ($i = 0; $i < $this->partition['num']; $i++) {
            $k = $i + 1;
            $tableName[$k] = "分表：{$k}";
        }
        $c = array('des' => '选择表格',
            'model' => 'dropdown',
            'source' => array(
                'sourcetyp' => 'hash',
                'hash' => $tableName,
            ),
            'value' => 1,
        );
        if ($tp == 1) {
            return $c;
        } else {
            return $tableName;
        }
    }

    /**
     * 创建同类型表
     * @param type $suffix
     * @return type
     */
    public function create_table($suffix) {
        $prefix = C("DB_PREFIX");
        $sql = <<<EOC
                create  table  `{$prefix}{$this->basetable}{$suffix}` like `{$prefix}{$this->basetable}`;             
EOC;
        $db = M();
        return $db->execute($sql);
    }

    /**
     * 获取记录统计
     * @param type $map
     * @param type $lang
     * @return type
     */
    public function getcount($map) {
        if (isset($map['sortstr'])) {
            unset($map['sortstr']);
        }
        $this->getPartitionTableName($map);

        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $rs = $this->table($this->basetable_cah)->where($filedarray)->bind($valuearray)->count();
        return $rs;
    }

    /**
     * 读取列表
     * @param type $map
     * @param type $skip
     * @param type $pagesize
     * @return type
     */
    public function get_list($map, $skip = 0, $pagesize = 0) {
        if (isset($map['pagesize'])) {
            $pagesize = $pagesize ? $pagesize : $map['pagesize'];
            unset($map['pagesize']);
        }
        if ($pagesize) {
            if (!$skip) {
                $pagevar = C('VAR_PAGE');
                if (isset($map[$pagevar])) {
                    $page = $map[$pagevar];
                    unset($map[$pagevar]);
                } else {
                    $page = I($pagevar, 1, intval);
                }
                if ($page > 0) {
                    $skip = ($page - 1) * $pagesize;
                }
            }
        }
        $rs = $this->getlist($map, $skip, $pagesize);
        $count = $this->getcount($map);
        $drs = array('list' => $rs, 'totalSize' => $count, 'status' => 1);
        return $drs;
    }

    /**
     * 列表转换
     * @param type $list
     * @return type
     */
    public function trance_list($list) {
        $l = [];
        foreach ($list as $v) {
            $l[] = $this->trance_model($v);
        }
        return $l;
    }

    /**
     * 数据格式转换
     * @param array $model
     */
    abstract protected function trance_model($model);

    /**
     * 列表转换
     * @param type $list
     * @return type
     */
//     abstract protected function trance_list($list);

    /**
     * 读取单一语言列表
     * @param type $map  查询条件
     * @param type $firstRow 偏移量
     * @param type $listRows 页数
     * @param type $lang     语言
     * @return type
     */
    public function getlist($map, $firstRow = 0, $listRows = 0) {
        unset($map[C('TOKEN_NAME')]);
        $sortstr = "";
        if (isset($map['sortstr'])) {
            $sortstr = $map['sortstr'];
            unset($map['sortstr']);
        }
        $this->getPartitionTableName($map);

        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        if ($listRows) {
            $rs = $this->table($this->basetable_cah)->where($filedarray)->bind($valuearray)->scope("orderby{$sortstr}")->limit($firstRow, $listRows)->select();
        } else if ($firstRow) {
            $rs = $this->table($this->basetable_cah)->where($filedarray)->bind($valuearray)->scope("orderby{$sortstr}")->limit($firstRow)->select();
        } else {
            $rs = $this->table($this->basetable_cah)->where($filedarray)->bind($valuearray)->scope("orderby{$sortstr}")->select();
        }
        $dl = [];
        foreach ($rs as $v) {
            $dl[] = $this->trance_model($v);
        }
        return $dl;
    }

    /**
     * 读取单一语言列表
     * @param type $map  查询条件
     * @param type $firstRow 偏移量
     * @param type $listRows 页数
     * @param type $lang     语言
     * @return type
     */
    public function getfieldlist($map, $filed = "") {
        $filedstr = empty($filed) ? "id,parentid,title,shorttitle" : $filed;
        unset($map[C('TOKEN_NAME')]);
        $sortstr = "";
        if (isset($map['sortstr'])) {
            $sortstr = $map['sortstr'];
            unset($map['sortstr']);
        }
        $this->getPartitionTableName($map);
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $rs = $this->table($this->basetable_cah)->where($filedarray)->bind($valuearray)->scope("orderby{$sortstr}")->getField($filedstr, true);
        return $rs;
    }

    public function addnew($model) {
        $rs = testenable_writing();
        if ($rs['status'] !==0) {
            return $rs;
        }
        unset($model[C('TOKEN_NAME')]);
        $this->getPartitionTableName($model);
        $id = $this->table($this->basetable_cah)->add($model);
        if ($id) {
            return array('status' => 0, 'msg' => 'DataAddSucc', 'id' => $id);
        } else {
            return ['status' => 20000002, 'msg' => 'DataAddFailed'];
        }
    }

    /**
     * 读取完整实例
     * @param type $id
     * @return type
     */
    public function getmodel($map) {
        if (is_array($map)) {
            if (isset($map[$this->idname])) {
                $this->getPartitionTableName($map);
                $id = $map[$this->idname];
            } else {
                return null;
            }
        } else {
            $id = $map;
            $map = [$this->idname => $id];
        }
        $base = $this->table($this->basetable_cah)->where($map)->find();
        return $this->trance_model($base);
    }

    /**
     * 读取上下页
     * @param type $model
     * @param type $map
     * @return type
     */
    public function getprenext($id, $map) {
        $map[$this->idname] = ['lt', $id];
        $preid = $this->table($this->basetable_cah)->where($map)->scope("orderby")->getField($this->idname);
        $map[$this->idname] = ['gt', $id];
        $nextid = $this->table($this->basetable_cah)->where($map)->scope("orderby2")->getField($this->idname);
        $data['pre'] = $this->getmodelbyid($preid);
        $data['next'] = $this->getmodelbyid($nextid);
        return $data;
    }

    /**
     * 获取缓存
     * @param type $id
     * @return type
     */
    public function getmodelbyid($map) {
        if (is_array($map)) {
            if (isset($map[$this->idname])) {
                $this->getPartitionTableName($map);
                $id = $map[$this->idname];
            } else {
                return null;
            }
        } else {
            $id = $map;
            $map = [$this->idname => $id];
        }
        $catchpath = "details/{$this->basetable}/{$id}";
        $value = F($catchpath);
        if ($value) {
            return $value;
        } else {
            $value = $this->getmodel($id);
            F($catchpath, $value);
            return $value;
        }
    }

    /**
     * 清除缓存
     * @param type $id
     * @return type
     */
    public function clearcatch($id) {
        $catchpath = "details/{$this->basetable}/{$id}";
        F($catchpath, null);
    }

    /**
     * 更新数据 
     * @param type $data
     * @return type
     */
    public function update($model) {
        $rs = testenable_writing();
        if ($rs['status'] !== 0) {
            return $rs;
        }
        unset($model[C('TOKEN_NAME')]);
        $this->getPartitionTableName($model);
        $id = $model[$this->idname];
        unset($model[$this->idname]);
        if ($this->table($this->basetable_cah)->where([$this->idname => $id])->save($model)) {
            $this->clearcatch($id);
            return array('status' => 0, 'msg' => 'DataModifySuc');
        } else {
            return array('status' => 20000002, 'msg' => 'DataModifyFailed');
        }
    }

    /**
     * 软删除
     * @param type $map
     * @return string
     */
    public function sdel($map) {
        $rs = testenable_writing();
        if ($rs['status'] !== 0) {
            return $rs;
        }
        if (is_array($map)) {
            if (isset($map[$this->idname])) {
                $this->getPartitionTableName($map);
                $id = $map[$this->idname];
            } else {
                return array('status' => 20000010, "msg" => 'MissingKeyData');
            }
        } else {
            $id = $map;
            $map = [$this->idname => $id];
        }
        if ($this->table($this->basetable_cah)->where(array($this->idname => $id))->setField($this->delmark, 0)) {
            $data = array('status' => 0, "msg" => 'DataDelSuc');
        } else {
            $data = array('status' => 20000002, "msg" => 'DataDelFailed', 'data' => $this->getLastSql());
        }
        return $data;
    }

    public function del($map) {
        $rs = testenable_writing();
        if ($rs['status'] !==0) {
            return $rs;
        }
        if (is_array($map)) {
            if (isset($map[$this->idname])) {
                $this->getPartitionTableName($map);
                $id = $map[$this->idname];
            } else {
                return array('status' => 20000010, "msg" => 'MissingKeyData');
            }
        } else {
            $id = $map;
            $map = [$this->idname => $id];
        }
        if ($this->table($this->basetable_cah)->where(array($this->idname => $id))->delete()) {
            $data = array('status' => 0, "msg" => 'DataDelSuc');
        } else {
            $data = array('status' => 20000002, "msg" => 'DataDelFailed');
        }
        return $data;
    }

    /**
     * 批量删除栏目，
     * @param type $idlist
     * @return type
     */
    public function batchDelete($data) {
        $rs = testenable_writing();
        if ($rs['status'] !==0) {
            return $rs;
        }
        if (isset($data['idlist'])) {
            if (isset($data['suffix']) && $data['suffix']) {
                $suffix = $data['suffix'] - 1;
                $data[$this->partition['field']] = $suffix;
                $this->getPartitionTableName($data);
            }
            $idl = $data['idlist'];
        } else {
            $idl = $data;
        }
        $idl = trim($idl, ',');
        $idlist = explode(',', $idl);
        if (empty($idl) || count($idlist) == 0) {
            $msg['status'] = 20000004;
            $msg['msg'] = "object_to_selected";
            return $msg;
        }

        $map = array();
        $map[$this->idname] = array('in', $idlist);
        $c = $this->table($this->basetable_cah)->where($map)->delete();
        if ($c) {
            $msg['status'] = 0;
            $msg['msg'] = "DataDelSuc";
        } else {
            $msg['status'] = 20000002;
            $msg['msg'] = "DataDelFailed";
        }
        return $msg;
    }

    public function showtablename() {
        return $this->basetable;
    }

}
