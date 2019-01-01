<?php

/*
 * 积分日志
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-7 15:10:02
 * @Modify  2017-12-7 15:10:02
 * @CopyRight:  2017 by RDM
 */


namespace Common\ViewModel;
use Think\Model\ViewModel;

class LogScoreViewModel  extends ViewModel {

    Public $viewFields = array(
        'LogScore' => array(
            'id',
            'userid',
            'scoretype',
            'addtime',
            'log_ip',
            'objid',
            'changebefore',
            'score',
            'changeafer',
            'sid_id',
            '_type' => 'LEFT'),
        'CusCustomer' => array('username', 'nickname', 'email',
            '_on' => 'LogScore.userid=CusCustomer.id'),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'LogScore.addtime desc',
        ),   
         'orderby10' => array(
            'order' => 'LogScore.addtime asc',
        ),          
    );
    
    public $ShowMap = array(
        'id' => '序号',
        'username' => '帐户',
        'nickname' => '昵称',
        'email' => '邮件',
        'scoretype' => array(
            'type' => 'tagkey',
            'format' => 'score_taglist',
            'title' => '变更项目',
        ),
        'changebefore' => '变更前',
        'score' => '变更',
        'changeafer' => '变更后',
        'log_ip' => 'IP',
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
