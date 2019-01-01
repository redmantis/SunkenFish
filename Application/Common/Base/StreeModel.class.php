<?php

namespace Common\Base;
use Common\Base;

/**
 * 单语言树形数据基类
 * 树形数据不分割表
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
abstract class StreeModel extends Base\SModel {
    
    /**
     * 唯一标记字段名
     * @var type 
     */
    protected $viewpath = "viewpath";
    
    /**
     *允许删除子树
     */
    protected $deltree = false;

    function __construct($data) {
        if ($data) {
            if (isset($data['link'])) {
                $this->_link = $data['link'];
            }
            if (isset($data['basetable'])) {
                $this->basetable = $data['basetable'];
            }
            if (isset($data['txtroot'])) {
                $this->langtextnameroot = $data['txtroot'];
            } 
            if (isset($data['deltree'])) {
                $this->deltree = $data['deltree'];
            }
        }
        $this->tableName = $this->basetable;
        $this->basetablec = $this->basetable; //当前基础表
        $prefix = C("DB_PREFIX");
        $this->basetable_cah = $prefix . $this->basetable; //当前基表全名
        parent::__construct();
    }
    
     public function addnew($model) {        
        $map['sid_id'] = $model['sid_id'];
        if ($this->checkTagmark($map, $model[$this->viewpath])) {
            $rs = ['status' => 0, 'msg' => 'KeywordRepeat'];
        } else {
            $rs = parent::addnew($model);
        }
        return $rs;
    }
    
    
    public function getviewpath(){
        return $this->viewpath;
    }

        /**
     * 更新数据 
     * @param type $data
     * @return type
     */
    public function update($model) {
        $id = $model['id'];

        $map['sid_id'] = $model['sid_id'];
        $map['id'] = array('neq', $model['id']);
        $ck = $this->checkTagmark($map, $model['viewpath']);
        if ($ck) {
            return array('status' => 0, 'msg' => 'KeywordRepeat');
        }
        $idlist = $this->getsublistid($map, $id);
        if (in_array($model['parentid'], $idlist)) {
            return array('status' => 0, 'msg' => 'notMutualFatherElement');
        }
        $rs = parent::update($model);
        return $rs;
    }

    /**
     * 根据 viewpath 取得栏目实体 缓存方式
     * @param type $viewpath
     * @param type $sid_id
     */
    public function getmodelbypath($map) {
        if (!isset($map[$this->viewpath])) {
            return null;
        }        
        $id = $this->getidbytagmark($map[$this->viewpath]);
        if ($id) {
            return $this->getmodelbyid(['id' => $id]);
        }
    }

    /**
     * 获取根节点id
     */
    public function getendnode($id){
        $newid=  $this->where(array('parentid'=>$id))->scope('orderby')->getField('id');
        if($newid){
            return $this->getendnode($newid);
        }else{
            return $id;
        }
    }

    /**
     * 测试 $tagmark 是否已经存在
     * @param array $map
     * @param type $tgmark
     * @return string
     */
    public function checkTagmark($map, $tgmark) {
        $tagmark = trim($tgmark);
        //tagmark 为空时不进行唯一性检测
        if (empty($tagmark)) {
            return false;
        }
        $map[$this->viewpath] = $tagmark;
        $rs = $this->where($map)->getField('id');
        if ($rs) {
            return $rs;
        } else {
            return false;
        }
    }
    
    /**
     * 读$tagmark的ID
     * @param type $tagmark
     * @param type $selectlangue
     * @return int
     */
    public function getidbytagmark($tagmark,$selectlangue="") {
        $map[$this->viewpath] = $tagmark;
        $lang = getLangueInfo($selectlangue);
        if (isset($lang['sid_id']) && $lang['sid_id']) {
            $map['sid_id'] = array('in', "0,{$lang['sid_id']}");
        }
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $rs = $this->where($filedarray)->bind($valuearray)->getField('id');
        if ($rs) {
            return $rs;
        } else {
            return 0;
        }
    }

    /**
     * 读取栏目路径信息
     * @param type $id
     * @param type $ppth
     * @return type
     */
    public function getParent($id, &$ppth) {        
        $parentid = $this->where(['id' => $id])->getField("parentid");
        if ($id == $parentid) {
            return "栏目不能自为父栏目，看到这条信息说明程序有进入死循环的风险";
        }
        if ($parentid) {
            array_unshift($ppth, $parentid);            
            $this->getParent($parentid, $ppth);
        } 
    }
    
    public function getParentPath($id, $byself = true) {
        $ppth = [];
        $this->getParent($id, $ppth);
        if ($byself) {
            $ppth[] = $id;
        }
        $smap = ['id' => ['in', $ppth]];
        $rs = $this->getlist($smap);
        $dl = array();
        formatTreeList($rs, 0, $dl, '', '', 0);
        return $dl;
    }
    
    /**
     * 树形结构
     * @param type $sid_id
     */
    public function getTree3($map, $filedstr) {
        $dl = $this->getfieldlist($map, $filedstr);
        $dtree = genTree5($dl);
        return $dtree;
    }

    /**
     * 树形结构
     * @param type $sid_id
     */
    public function getTree2($map, $pmap = null) {
        $dl = $this->getTree($map, $pmap);
        $dtree = genTree9($dl);
        return $dtree;
    }

    /**
     * 树形列表
     * @param type $map
     * @param type $pmap
     * @return array
     */
    public function getTree($map, $pmap = null) {
        $pid = 0;
        if ($pmap) {
            $pid = $this->where($pmap)->getField('id');
        }
        if (!$pid) {
            $pid = 0;
        }
        $rs = $this->getlist($map);
        $dl = array();
        formatTreeList($rs, $pid, $dl, '', '', 0);
        return $dl;
    }

    /**
     * 获取默认值
     * @param type $id
     * @param type $key
     * @return type
     */
    public function getdefaultvaue($id, $key) {
        $rs = $this->where(array('id' => $id))->field("id,parentid,{$key}")->find();
        if ($rs[$key] || ($rs['parentid'] == 0)) {
            return $rs[$key];
        } else {
            return $this->getdefaultvaue($rs['parentid'], $key);
        }
    }
    
    /**
     * 取得子栏目列表
     * @param type $id　　父类ID
     * @param type $byself 包含本身
     * @return string
     */
    public function getsublist($map, $id = 0, $byself = true) {
        $idlist = $this->getsublistid($map, $id, $byself);
        if(count($idlist)==0){
            return null;
        }
        $smap = ['id' => ['in', $idlist]];
        $rs = $this->getlist($map);
        if ($byself) {
            $dl = array();
            formatTreeList($rs, $id, $dl, '', '', 0);
            return $dl;
        } else {
            return $rs;
        }
    }

    /**
     * 取得子栏目ID列表
     * @param type $id
     * @param type $byself
     * @return type
     */
    public function getsublistid($map, $id = 0, $byself = true) {
        $pid = $id;
        $list = $this->where($map)->getField("id,parentid,{$this->viewpath}");    
        $dl = array();
        formatTreeList($list, $pid, $dl, '', '', 0);
        $idlst = array_keys($dl);
        if($byself){
            array_unshift($idlst, $id);
        }
        return $idlst;
    }
    
    public function del($map, $id) {
        $idlst = $this->getsublistid($map, $id, false);
        if (count($idlst) == 0 || $this->deltree) {
            array_unshift($idlst, $id);
            $idll = implode(',', $idlst);
            $rs = $this->batchDelete(['idlist' => $idll]);
        } else {
            $rs = array('status' => 0, "msg" => 'DataDelFailedBySub');
        }
        return $rs;
    }
}
