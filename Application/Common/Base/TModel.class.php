<?php

/*
 * 多语言基类
 */

namespace Common\Base;
use Think\Model;

class TModel extends Model {      
    
    function __construct($basetable) {
        if ($basetable) {
            $this->tableName = $basetable . "text";
        }
        parent::__construct();
    }

    /**
     * 更新语言包
     * @param type $dl
     * @param type $extid
     */
    public function updatelist($dl,$extid,$del=1){  
        $map = array('extid' => $extid);
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        if ($del) {
            $this->where($filedarray)->bind($valuearray)->delete();
        }
        $i = 0;
        foreach ($dl as $v) {
            $content = htmlspecialchars_decode(stripslashes($v['content']));
            $v['content'] = $content;      
            $i += $this->add($v);
        }
        return $i;      
    }
    
    /**
     * 取语言包
     * @param type $extid
     * @return type
     */
    public function getlist($extid) {
        $map = array('extid' => $extid);
        return $this->where($map)->select();
    }
    
    public function del($extid){
        $map = array('extid' => $extid);
        return $this->where($map)->delete();
    }
    
    /**
     * 批量删除
     * @param type $idlist
     * @return type
     */
    public function batchDelete($idlist = array()) {      
        $map = array();
        $map['extid'] = array('in', $idlist); 
        return $this->where($map)->delete();   
    } 
}
