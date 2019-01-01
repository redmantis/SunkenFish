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

class LbsRegionModel extends Model {

    protected $_validate = array(
        array('province', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '非法数据(province)！'),
        array('city', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '非法数据(city)！'),
        array('region', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '非法数据(region)！'),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc,id asc',
        ),
    );
    public $ShowMap = array(
        'id' => '序号',
        'province' => '省',
        'city' => '市',
        'region' => '地区',
        'pgb' => '省码',
        'cgb' => '市码',
        'gbcode' => '编码',
        'host' => '域名',
        'is_hot' => array(
            'type' => 'rotation',
            'format' => 'open_status',
            'table' => 'LbsRegion',
            'title' => '热门',
        ),
        'is_region_hot' => array(
            'type' => 'rotation',
            'format' => 'open_status',
            'table' => 'LbsRegion',
            'title' => '地区热门',
        ),
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            'btnlist' => array(
                'subregion_index' => array(
                    'title' => '乡镇',
                    'exttitle'=>'region',
                    'link' => array(
                        'mod' => 'card',
                        'url' => 'subregion/index',
                        'fields' => array(
                            'gbcode' => 'gbcode',
                        ),
                    )
                ),
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
     * 返回行政区域编码
     * @param type $map
     * @return int
     */
    public function getRegionCode($map) {

        $rs = array('status' => 0, 'msg' => '');
        if (!$this->create($model)) {
            $rs['data'] = $model;
            $rs['msg'] = $this->getError();
        } else {
            $rs['data'] = $this->where($map)->getField('gbcode');
            $rs['msg'] = "";
            $rs['status'] = 1;
        }
        return $rs;
    }

    /**
     * 设置当前城市
     * @param type $data
     */
    public function set_curentcity($map) {

        if (isset($map['host'])) {
            $catchname = "lbsregion/host/{$map['host']}";
            $city = F($catchname);
        }
        if (empty($city)) {
            $city = $this->where($map)->find();
            $url = $this->where(array('gbcode' => $city['cgb']))->getField('host');
            if (empty($url)) {
                $url = $this->where(array('gbcode' => $city['pgb']))->getField('host');
            }
            $cgb = $city['cgb'];
            if (empty($cgb)) {
                $cgb = $city['gbcode'];
                $cgb = str_replace('00', '', $cgb);
                $cgb = str_replace('00', '', $cgb);
                if ($cgb < 100) {
                    $cgb .="01";
                }
                $cgb .="00";
                $city['cgb'] = $cgb;
            }
            if (!empty($city['host'])) {
                $catchname = "lbsregion/host/{$city['host']}";
                F($catchname, $city);
            }
        }
        set_curcity($city);
        $rs['data'] = $city;
        $rs['msg'] = "";
        $rs['host'] = $url;
        $rs['status'] = 1;
        return $rs;
    }

    /**
     * 获取默认城市编码
     * @param type $map
     */
    public function getDefaultCode($map) {
        $rs['data'] = $this->where($map)->getField('gbcode');
        $rs['msg'] = "";
        $rs['status'] = 1;
        return $rs;
    }

    /**
     * 返回列表
     * @param type $map
     * @return int
     */
    public function getRegionList($data) {
        if (isset($data['regioncode'])) {
            $regioncode = $data['regioncode'];
            if ($regioncode < 100) {
                $regioncode .='%00';
            } else {
                $regioncode .='%';
            }
            $map['gbcode'] = array('like', $regioncode);
        }
        if (isset($data['is_hot'])) {
            $map['is_hot'] = $data['is_hot'];
        }
        if (isset($data['is_region_hot'])) {
            $map['is_region_hot'] = $data['is_region_hot'];
        }
        if (isset($data['cgb'])) {
            $map['cgb'] = $data['cgb'];
        }

        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $rs['list'] = $this->where($filedarray)->bind($valuearray)->scope('orderby')->field('is_hot,is_region_hot,cgb,gbcode,city,region,province,host')->select();
        $rs['msg'] = "$regioncode";
        $rs['status'] = 1;
        return $rs;
    }
    
       /**
     * 返回列表
     * @param type $map
     * @return int
     */
    public function getCitylist($data,$type="city") {
        if (!isset($data['regioncode'])) {
            $rs['msg'] = "参数错误";
            $rs['status'] = 1;
            return $rs;
        }

        $regioncode = $data['regioncode'];
        if (empty($regioncode)) {
            $rs['list'] = [];
            $rs['status'] = 0;
            $rs['msg'] = "regioncode 必须提交";
            return $rs;
        }

        $regioncode = str_replace('00', '', $regioncode);
        if ($type == 'city') {          
             $regioncode .= '%00';
        }
        else {
            $regioncode .= '%';
        }
        $map['gbcode'] = array('like', $regioncode);

        if (isset($data['is_hot'])) {
            $map['is_hot'] = $data['is_hot'];
        }
        if (isset($data['is_region_hot'])) {
            $map['is_region_hot'] = $data['is_region_hot'];
        }
        if (isset($data['cgb'])) {
            $map['cgb'] = $data['cgb'];
        }

        if (isset($data['sortid'])) {
            $map['sortid'] = explode('|', $data['sortid']);
        }

        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $l = $this->where($filedarray)->bind($valuearray)->scope('orderby')->field('gbcode,city,region')->select();
        $sql= $l;
        $list = array();
        foreach ($l as $k => $v) {
            if ($k == 0) {
                $a['idvalue'] = "";
                switch ($type) {
                    case "city":
                        $a['idname'] = "选择城市";
                        break;
                    case "region":
                        $a['idname'] = "选择地区";
                        break;
                }
                $list[] = $a;
                $a['idvalue'] = $v['gbcode'];
                $a['idname'] = $v['region'];
            } else {
                $a['idvalue'] = $v['gbcode'];
                $a['idname'] = $v['region'];
            }
            $list[] = $a;
        }

        $rs['list'] = $list;
        $rs['msg'] = "$regioncode";
        $rs['sql'] = $sql;
        $rs['status'] = 0;
        return $rs;
    }

    /**
     * 读取区域统计
     * @param type $data
     * @param type $data-regioncode  地区编码
     * @param type $data-type 区域类型  city|region
     */
    public function getCitylistCount($data) {
        $type = $data['type'];
        unset($data['type']);
        
        $roommap=array();
        if(isset($data['monthprice'])&&!empty($data['monthprice'])){
            $roommap['monthprice']=$data['monthprice'];
        }
        unset($data['monthprice']);
        
        if(isset($data['wuyie'])&&!empty($data['wuyie'])){
            $roommap['wuyie']=$data['wuyie'];
        }
        unset($data['wuyie']);
        if(isset($data['roomnum'])&&!empty($data['roomnum'])){
            $roommap['roomnum']=$data['roomnum'];
        }
        unset($data['roomnum']);
        
        if(isset($data['keyword'])&&!empty($data['keyword'])){
            $roommap['searchword']=$data['keyword'];
        }
        unset($data['keyword']);
        
        
        $catchpath = "lbsregion/{$type}list/{$data['regioncode']}";
        $ctlist = F($catchpath);
        if (!$ctlist) {
            if ($type === "street") {
                $db = new LbsSubregionModel();
                $map = array('gbcode' => $data['regioncode'], 'linkage' => 1);
                $rs = $db->getSubRegionList($map);
            } else {
                $rs = $this->getCitylist($data, $type);
            }
            $ctlist = $rs['list'];
            unset($ctlist[0]);
            F($catchpath, $ctlist);
        }
        foreach ($ctlist as $k=>$v){
            $db=new \Common\Zfmodel\ZfRoomsModel();          
            $roommap['streetcode']=$v['idvalue'];
            $count=$db->getregioncount($roommap);
            $ctlist[$k]['roomcount']=$count;
            $simname= str_replace(['镇','街道'], '', $v['idname']);
            $ctlist[$k]['simname']=$simname;
        }
        return ['status'=>1,'list'=>$ctlist];
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
    public function getbycode($code) {
        if (is_array($code)) {
            $path = "regioncode/region/$code[1]";
        } else {
            $path = "regioncode/region/$code";
        }
        $data = F($path);
        if (!$data) {
            $rs = $this->getmodel(array('gbcode' => $code));
            $data = $rs['data'];
            F($path, $data);
        }
        return $data;
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
            $id = $this->add($model);
            if ($id >= 0) {
                $rs['status'] = 1;
                $rs['msg'] = 'DataAddSucc';
            } else {
                $rs['msg'] = 'DataAddFailed';
            }
        }
        return $rs;
    }

    /**
     * 缓存读行政区域
     * @param type $id
     */
    public function getmodelbyid($gbcode) {
        $catchpath = "details/regiondetails/{$gbcode}";
        $value = F($catchpath);
        if ($value) {
            return $value;
        } else {
            $value = $this->getmodel(array('gbcode' => $gbcode));
            F($catchpath, $value['data']);
            return $value['data'];
        }
    }

}
