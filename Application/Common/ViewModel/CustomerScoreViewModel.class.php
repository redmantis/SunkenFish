<?php

/*
 * 会员积分
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-8 13:53:10
 * @Modify  2017-12-8 13:53:10
 * @CopyRight:  2017 by RDM
 */

/**
 * Description
 * 
 */

namespace Common\ViewModel;
use Think\Model\ViewModel;

class CustomerScoreViewModel  extends ViewModel {

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
        'CusScore' => array('base_score','score','lasttime','log_ip',
            '_on' => 'CusCustomer.id=CusScore.userid'),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'CusScore.lasttime desc',
        ),   
         'orderby10' => array(
            'order' => 'CusScore.lasttime asc',
        ),          
    );
    
    public $ShowMap = array(
        'id' => '序号',
        'username' => '帐户',
        'nickname' => '昵称',
        'email' => '邮件',
        'sign' => '连续签到',
        'signcount' => '总签到',
        'signtime' => array(
            'type' => 'time',
            'format' => 'Y-m-d H:i:s',
            'title' => '最新签到',
        ),
        'score' => '积分',
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