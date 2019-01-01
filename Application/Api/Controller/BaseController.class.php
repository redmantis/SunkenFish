<?php
namespace Api\Controller;
use Think\Controller;

/**
 * 如果某个控制器必须用户登录才可以访问  
 * 请继承该控制器
 */
class BaseController extends Controller {

    public $xconfg;
    public $upinfo;
    public $sid_id;
    public $sid_dir;
    public $curuser;

    public function _initialize() {
        header('Access-Control-Allow-Origin: *'); //设置http://www.baidu.com允许跨域访问
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        header("Content-Type: text/html; charset=utf-8");
        $this->xconfg = C("UEDITOR_CONFIG");
                
        $usermodel = I("usermodel");

        switch ($usermodel) {
            case 'pcusers':
                $userinfo = getuserinfo();
                $uinfo['userid'] = $userinfo['id'];
                $uinfo['usertype'] = 2; //会员
                $uinfo['sid_id'] = $userinfo['sid_id'];
                $uinfo['checktp'] = 'u';
                break;
            default :
                $userinfo = getmanage();
                $uinfo['userid'] = $userinfo['mid'];
                $uinfo['usertype'] = $userinfo['mtype'];
                $uinfo['sid_id'] = $userinfo['sid_id'];
                $uinfo['checktp'] = 'm';
                break;
        }

        /**
         * 用户鉴权
         */
        if (!$uinfo['userid']) {
            $data['status'] = 0;
            $data['msg'] = showTagbyMark('DisagreeUserinfo'); //regioncode;
            $data['state'] = "";
            $this->ajaxReturn($data);
            die;
        }
        
        $db = new \Common\Model\SubsidiaryModel();
        $model = $db->getmodebyid($uinfo['sid_id']);
        $this->sid_dir = $model['sid_dir'];        
        $this->curuser = $uinfo;
    }
}
