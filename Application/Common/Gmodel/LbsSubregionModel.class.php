<?php

/*
 * 字符：UTF-8
 * @author  RDM:范利丰
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-10-19 16:41:08
 * @Modify  2017-10-19 16:41:08
 * @CopyRight:  2017 by RDM
 */

/**
 * Description
 * 行政区划表
 */

namespace Common\Gmodel;
use Think\Model;

class LbsSubregionModel extends Model {
    
    protected $_validate = array(
        array('areaname', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '非法数据(areaname)！'),
        array('gbcode', 'require', 'gbcode必填！'),
    );
    public $ShowMap = array(
        'id' => '序号',
        'areaname' => '地区名',
        'areacode' => '地区编码',
        'gbcode' => '编码',
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            'btnlist' => array(
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'update',
                        'fields' => array(
                            'id' => 'id',
                        ),
                    )
                ),
                'del' => array(
                    'title' => '删除',
                    'link' => array(
                        'mod' => 'diag_del',
                        'url' => 'del',
                        'cls' => 'diag_del',
                        'fields' => array(
                            'id' => 'id'
                        ),
                    )
                ),
            ),
        ),
    );

    /**
     * 返回列表
     * @param type $map
     * @return int
     */
    public function getSubRegionList($data) {
        $regioncode = $data['gbcode'];
        $regioncode = str_replace('00', '', $regioncode);     
        if ($regioncode < 100) {
            $regioncode .='%00';
        } else {
            $regioncode .='%';
        }
        if(isset($data['linkage'])){
            $linkage=$data['linkage'];
        }
        $map['gbcode']=array('like',$regioncode); 
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $l = $this->where($filedarray)->bind($valuearray)->field('areacode,areaname')->select();
        $list = array();
        if ($linkage == 1) {
            $a['idvalue'] = "";
            $a['idname'] = "选择街道";
            $list[] = $a;
            foreach ($l as $k => $v) {
                $a['idvalue'] = $v['areacode'];
                $a['idname'] = $v['areaname'];
                $list[] = $a;
            }
            $rs['list'] = $list;
            $rs['status'] = 0;
        } else {
            $rs['list'] = $l;
            $rs['status'] = 0;
        }
        $rs['msg'] = $regioncode;   
        return $rs;
    }
    
    /**
     * 获取一个实例
     * @param type $map
     * @return string
     */
    public function getmodel($map) {
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $model = $this->where($filedarray)->bind($valuearray)->find();
        if ($model) { 
            $data['status'] = 1;
            $data['data'] = $model;
        } else {
            $data['status'] = 0;
            $data["msg"] = 'DataNotFind';
        }
        return $data;
    }
    
    /**
     * 通过code读取地理信息
     * @param type $code
     * @return type
     */
    public function getbycode($code){
        $path="regioncode/subregion/$code";
        $data=F($path);
        if(!$data){
           $rs = $this->getmodel(array('areacode'=>$code));
           $data=$rs['data'];
           F($path,$data);
        }
        return $data;
    }
    
    /**
     * 自动加乡镇数据
     * @param type $map
     * areaname
     * gbcode
     * @return type
     */
    public function getbyname($map) {
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $model = $this->where($filedarray)->bind($valuearray)->find();
        if(!$model){
            $rs=  $this->addnew($map);
            if($rs['status']){
                $model=$rs['data'];
            }
        }
        if ($model) {
            $rs = array('status' => 1);
            $rs['data'] = $model;
        } else {
             $rs = array('status' => 0);
        }
        return $rs;
    }

    /**
     * 新建
     * @param type $model
     * @return string
     */
    public function addnew($model) {
        C('TOKEN_ON', false);
        $rs = array('status' => 0, 'msg' => '');
        if (!$this->create($model)) {
            $rs['data'] = $model;
            $rs['msg'] = $this->getError();
        } else {
            if (empty($model['areacode'])) {
                $c = $this->where(array('gbcode' => $model['gbcode']))->count() + 1;
                if ($c < 10) {
                    $model['areacode'] = "{$model['gbcode']}0{$c}";
                } else {
                    $model['areacode'] = "{$model['gbcode']}{$c}";
                }
            }
            $id = $this->add($model);
            if ($id >= 0) {
                $model['id'] = $id;
                $rs['data'] = $model;
                $rs['status'] = 1;
                $rs['msg'] = 'DataAddSucc';
            } else {
                $rs['msg'] = 'DataAddFailed';
            }
        }
        return $rs;
    }
    
    /**
     * 批量添加乡镇
     * @param type $model
     * @return string
     */
    public function addbatch($model) {
        $c = $this->where(array('gbcode' => $model['gbcode']))->count() + 1;
        $split= trim($model['split']);
        unset($model['split']);
        $areaname = explode($split, $model['areaname']);
        
        $subreg = array();
        $gbcode = $model['gbcode'];
        foreach ($areaname as $v) {
            $model['areaname'] = trim($v);
            if ($c < 10) {
                $model['areacode'] = "{$gbcode}0{$c}";
            } else {
                $model['areacode'] = "{$gbcode}{$c}";
            }
            $c = $c + 1;
            $subreg[] = $model;
        }
        $count = $this->addAll($subreg);
        if ($count >= 0) {
            $model['id'] = $count;
            $rs['status'] = 1;
            $rs['msg'] = 'DataAddSucc';
        } else {
            $rs['msg'] = 'DataAddFailed';
        }
        return $rs;
    }

    /**
     * 删除辖区
     * @param type $id
     * @return string
     */
    public function del($id) {
        $rs = $this->where(array('id' => ":id"))->bind(':id', $id)->delete();
        if ($rs) {
            $data['status'] = 1;
            $data["msg"] = 'DataDelSuc';
        } else {
            $data['status'] = 0;
            $data["msg"] = 'DataDelFailed';
        }
        return $data;
    }
    
    /**
     * 更新数据
     * @param type $data
     * @return string
     */
    public function update($data) {
        $rs = array('status' => 0, 'msg' => '');
        if (!$this->create($data)) {
            $rs['data'] = $data;
            $rs['msg'] = $this->getError();
        } else {
            $id = $data['id'];
            unset($data['id']);
            $r0 = $this->where(array('id' => ":id"))->bind(":id", $id)->save($data);
            if ($r0) {
                $rs['status'] = 1;
                $rs["msg"] = 'DataModifySuc';
            } else {
                $rs["msg"] = 'DataModifyFailed';
            }
        }
        return $rs;
    }
    
    /**
     * 缓存读行政区域
     * @param type $id
     */
    public function getmodelbyid($areacode) {
        $catchpath = "details/subregiondetails/{$areacode}";
        $value = F($catchpath);
        if ($value) {
            return $value;
        } else {
            $value = $this->getmodel(array('areacode' => $areacode));
            F($catchpath, $value['data']);
            return $value['data'];
        }
    }
}
