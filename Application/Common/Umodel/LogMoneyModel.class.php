<?php

/*
 * 余额变更日志
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-7 10:11:31
 * @Modify  2017-12-7 10:11:31
 * @CopyRight:  2017 by RDM
 */


namespace Common\Umodel;
use Think\Model;

class LogMoneyModel extends Model {

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
    public function addnew($model) {     
        $data['log_ip'] = get_client_ip();
        $data['addtime'] = time();
        return $this->add($data);
    }    
}
