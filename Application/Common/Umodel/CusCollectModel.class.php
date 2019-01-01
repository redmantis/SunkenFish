<?php
/**
 * 软件功能说明：
 * 用户收集袋
 * 包括  点赞、阅读、评论、收藏等
 */

namespace Common\Umodel;
use Think\Model;

//用户收集清单
class CusCollectModel extends Model {

    protected $fields = array(
        'id',
        'userid', //用户ID
        'collectid', //收集对像ID
        'collecttable', //收集对像表
        'collecttype', // 收集类型
        'addtime', //收集时间
        'sid_id', //所在站点
        'sortid', //排序
        'collectstatus',//预留
    );
    
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid desc ,id desc',
        ),
    );

    /**
     * 收集记录
     * @param type $map 收集条件
     * $userid, $collectid,$collecttable,$collecttype,$sid_id     
     * 点赞  CollectType_Like =10;
     * 阅读  CollectType_Read =20;
     * 评论  CollectType_Review =30;
     * 收藏  CollectType_Favorite =40;
     * @showmod  查看模式  1：本人收藏  2：全部收藏  3：本人和全收藏   
     *                      4：本人点击统计  5：全部点击统计  6：本人和全部点击统计
     * @return int
     */
    public function collecting($map) {
        if ($map['showmod']) {
            $rs = $this->showcollect($map);
        } else {
            switch ($map['collecttype']) {
                case CollectType_Like://点赞
                    $rs = $this->switchcollect($map);
                    break;
                case CollectType_Read://点击、阅读
                    $rs=  $this->histcollect($map);                        
                    break;
                case CollectType_Review://回复，评论
                    break;
                case CollectType_Favorite://收荿
                    $rs = $this->switchcollect($map);
                    break;
            }
        }
        return $rs;
    }

    /**
     * 收集模式
     * @param type $map
     */
    public function collect($map) {
        $rs['status'] = 1;
        if (!$this->iscollecting($map)) {
            $map['addtime'] = time();
            $map['sortid'] = 0;
            $map['collectstatus'] = 1;
            if ($this->add($map)) {
                $rs['status'] = 1;
            } else {
                $rs['status'] = 0;
            }
        }
        $rs['data'] = $map;
        return $rs;
    }

    /**
     * 切换模式
     * @param type $map
     */
    public function switchcollect($map) {
        $rs['status'] = 0;
        if ($this->iscollecting($map)) {
            $rs['status'] = $this->where($map)->delete(); //清除后收藏减一  
            $rs['favstatus'] = 0;
        } else {
            $map['addtime'] = time();
            $map['sortid'] = 0;
            $map['collectstatus'] = 1;
            $rs['status'] = $this->add($map);
            $rs['favstatus'] = 1;
        }
        unset($map['userid']);
        $rs['favcount'] = $this->where($map)->count(); //收藏统计
        $rs['data'] = $map;
        return $rs;
    }
    
    /**
     * 点击、阅读
     * @param type $map
     */
    public function histcollect($map) {
        if (isset($map['userid'])) {
            if ($this->iscollecting($map)) {
                $rs['status'] = $this->where($map)->setInc('collectstatus');
                $rs['favstatus'] = $this->where($map)->getField('collectstatus');
            } else {
                $map['addtime'] = time();
                $map['sortid'] = 0;
                $map['collectstatus'] = 1;
                $rs['status'] = $this->add($map);
                $rs['favstatus'] = 1;
            }
        }
        $db = M($map['collecttable']);
        $rs2['status'] = $db->where(array('id' => $map['collectid']))->setInc('hits');
        $rs2['favstatus'] = $db->where(array('id' => $map['collectid']))->getField('hits');
        $rs2['user'] = $rs;
        return $rs2;
    }

    /**
     * 显示收藏状态
     * @param type $map
     */
    public function showcollect($map) {
        $showmod = $map['showmod'];
        unset($map['showmod']);
        switch ($showmod) {
            case 1:
                $rs['favstatus'] = $this->where($map)->count(); //收藏状态              
                break;
            case 2:
                unset($map['userid']);
                $rs['favcount'] = $this->where($map)->count(); //收藏统计
                break;
            case 3:
                $rs['favstatus'] = $this->where($map)->count(); //收藏状态 
                unset($map['userid']);
                $rs['favcount'] = $this->where($map)->count(); //收藏统计
                break;
            case 4:
                $rs['favstatus'] = $this->where($map)->getField('collectstatus'); //本人点击   
                break;
            case 5:
                unset($map['userid']);
                $rs['favcount'] = $this->where($map)->sum('collectstatus'); //点击统计
                break;
            case 6:
                $rs['favstatus'] = $this->where($map)->getField('collectstatus'); //本人点击   
                $rs['favcount'] = $this->where($map)->sum('collectstatus'); //点击统计
                break;
        }
        $rs['favcount'] = $this->where($map)->count(); //收藏统计
        $rs['data'] = $map;
        $rs['status'] = 1;
        return $rs;
    }

    /**
     * 删除收藏
     * @param type $id
     * @return string
     */
    public function del($map) {
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        //删除
        $rs = $this->where($filedarray)->bind($valuearray)->delete();
        if ($rs) {
            $data['status'] = 1;
            $data['count'] = $rs;
            $data["msg"] = 'DataDelSuc';
        } else {
            $data['status'] = 0;
            $data["msg"] = 'DataDelFailed';
        }
        return $data;
    }

    /**
     * 检测是否已经被收集
     * @param type $map
     * @return type
     */
    public function iscollecting($map){
        return $this->where($map)->find();
    }

    /**
     * 提取收藏列表
     * $userid, $collecttable,$collecttype,$sid_id
     * @param type $map
     */
    public function getcollect($map){
        return $this->where($map)->scope('orderby')->select();        
    }
    
    /**
     * 读取列表
     * @param type $map
     * @param type $skip
     * @param type $pagesize
     * @return type
     */
    public function getlist($map, $skip = 0, $pagesize = 0) {
        unset($map['size']);
        $sortstr = "";
        if (isset($map['sortstr'])) {
            $sortstr = $map['sortstr'];
            unset($map['sortstr']);
        }
        $count = 0;
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        if ($pagesize > 0) {
            $count = $this->where($filedarray)->bind($valuearray)->count();
            $rs = $this->where($filedarray)->bind($valuearray)->scope('orderby' . $sortstr)->limit($skip, $pagesize)->select();
        } else {
            $rs = $this->where($filedarray)->bind($valuearray)->scope('orderby' . $sortstr)->select();
        }
        $drs = array('list' => $rs, 'totalSize' => $count, 'status' => 1);
        return $drs;
    }
}
