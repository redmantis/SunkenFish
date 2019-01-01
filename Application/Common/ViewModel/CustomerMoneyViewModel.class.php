<?php

/*
 * 会员余额
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-8 12:46:28
 * @Modify  2017-12-8 12:46:28
 * @CopyRight:  2017 by RDM
 */

namespace Common\ViewModel;
use Think\Model\ViewModel;

class CustomerMoneyViewModel  extends ViewModel {

    Public $viewFields = array(
        'CusCustomer' => array(
            'id',
            'username',
            'nickname',
            'email',
            'status',   
            'sign',
            'signtime',
            'signcount',
            '_type' => 'LEFT'),
        'CusMoney' => array('base_money','money','base_deposit','deposit','lasttime','log_ip',
            '_on' => 'CusCustomer.id=CusMoney.userid'),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'CusMoney.lasttime desc',
        ),   
         'orderby10' => array(
            'order' => 'CusMoney.lasttime asc',
        ),          
    );
    
    public $ShowMap = array(
        'id' => '序号',
        'username' => '帐户',
        'nickname' => '昵称',
        'email' => '邮件',
        'deposit' => '押金',
        'money' => '余额',
        'log_ip' => 'IP',
        'lasttime' => array(
            'type' => 'time',
            'format' => 'Y-m-d H:i:s',
            'title' => '变更时间',
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

}
