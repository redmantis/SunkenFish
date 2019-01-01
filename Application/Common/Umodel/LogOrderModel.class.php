<?php

/*
 * 订单日志
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-11-30 13:20:29
 * @Modify  2017-11-30 13:20:29
 * @CopyRight:  2017 by RDM
 */

namespace Common\Umodel;

use Think\Model;

class LogOrderModel extends Model {

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'id desc',
        ),
    );

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

    /**
     * 日志记录
     * @param type $model
     * @param type $post
     */
    public function addnew($model, $post) {
        $data['orderid'] = $post['id'];
        if (isset($post['status']))
            $data['order_status'] = $post['status'];
        if (isset($post['ispay']))
            $data['order_ispay'] = $post['ispay'];
        if (isset($post['memo']))
            $data['memo'] = $post['memo'];
        if (isset($post['imglist']))
            $data['imglist'] = $post['imglist'];

        $data['userid'] = $post['userid'];
        $data['postdata'] = serialize($post);
        $data['curdata'] = serialize($model);
        $data['curdata'] = serialize($model);     
        if (isset($post['roomftid'])) {
            $data['roomftid'] = $post['roomftid'];
        }
        if (empty($model['usertype'])) {
            $data['usertype'] = 1;
        }

        $data['optype'] = $model['optype'];
        $data['log_ip'] = get_client_ip();
        $data['addtime'] = time();
        return $this->add($data);
    }
    
     /**
     * 删除订单日志
     * @param type $id
     * @return string
     */
    public function del($map) {
        if(!is_array($map)){
            $map=array('id'=>$map);
        }
        
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);        
        //删除
        $rs = $this->where($filedarray)->bind($valuearray)->delete();
        if ($rs) {
            $data['status'] = 1;
            $data['count']=$rs;
            $data["msg"] = 'DataDelSuc';
        } else {
            $data['status'] = 0;
            $data["msg"] = 'DataDelFailed';
        }
        return $data;
    }  
}
