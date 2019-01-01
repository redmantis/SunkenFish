<?php

/*
 * 余额变更日志
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-8 10:22:36
 * @Modify  2017-12-8 10:22:36
 * @CopyRight:  2017 by RDM
 */

namespace Common\ViewModel;
use Think\Model\ViewModel;

class LogMoneyViewModel  extends ViewModel {

    Public $viewFields = array(
        'LogMoney' => array(
            'id',
            'userid',
            'moneytype',
            'addtime',
            'log_ip',
            'objid',
            'moneybefore',
            'money',
            'moneyafter',
            'depositbefore',
            'deposit',
            'depositafter',
            'sid_id',
            '_type' => 'LEFT'),
        'ZfOrder' => array('roomid',
            '_on' => 'LogMoney.objid=ZfOrder.id'),
        'CusCustomer' => array('username', 'nickname', 'email',
            '_on' => 'LogMoney.userid=CusCustomer.id'),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'LogMoney.addtime desc',
        ),   
         'orderby10' => array(
            'order' => 'LogMoney.addtime asc',
        ),          
    );
    
    public $ShowMap = array(
        'id' => '序号',
        'username' => '帐户',
        'nickname' => '昵称',
//        'email' => '邮件',
        'moneytype' => array(
            'type' => 'tagkey',
            'format' => 'money_taglist',
            'title' => '项目',
        ),
        'moneybefore' => '变更前',
        'money' => '余额',
        'moneyafter' => '变更后',
        'log_ip' => 'IP',
        'depositbefore' => '变更前',
        'deposit' => '押金',
        'depositafter' => '变更后',
        'addtime' => array(
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
