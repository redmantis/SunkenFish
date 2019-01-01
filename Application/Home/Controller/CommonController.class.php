<?php

/*
 * 字符：UTF-8
 * 邮件: feiyufly001@hotmail.com
 * 创建：redmantis <默鱼 at feiyufly001@hotmail.com> 2016-8-4 20:39:17
 * 最终：Rdm
 */

namespace Home\Controller;
use Think\Controller;
use Common\Model;

/**
 * Description of CommonController
 *
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class CommonController extends Controller {

    public $bsubsid;                             //当前站点信息
    public $userId;                             //用户ID
    public $userInfo;                           //用户基本资料  

    protected function _initialize() {
        header('X-Frame-Options:Deny');
        $csidid = cookie('csidid');
        $db = new \Common\Model\SubsidiaryModel;
        $subinfo = $db->getmodebyid($csidid);
        $this->bsubsid = $subinfo;
        $this->userId = getuserinfo();
    }

    public function error() {
        $this->display('./Public/tpl/error.html');
    }

    public function nosite(){
         $this->display("./Public/tpl/nosite.html");
    }
    
    /**
     * 参数url跳转地址
     */
    public function parajump() {
        $map = I('post.');
        $str = para_index($map['code'], $map['key'], $map['contrl']."/");
        $this->ajaxReturn($str);
    }
}
