<?php

namespace Admin\Controller;

use Think\Controller;
use Common\Model;
use Common\ViewModel;

class LoginController extends Controller {

    public $sysconfig;                         //系统设置   

    public function _initialize() {
        set_csp_header(0);
        // 取得全站系统配置
        $this->sysconfig =   getConfig();
        $this->assign('sysconfig', $this->sysconfig);
    } 

    //登陆主页
    public function index() {
        header('X-Frame-Options:Deny');       
        $this->display();
    }

    //登陆验证
    public function login() {
        if (!IS_POST)
            $this->error("非法请求", U('Login/index'));
        
        
        $member = new Model\AdminModel();
        $username = I('username', '', 'addslashes');
        $password = I('password');
        $code = I('verify', '', 'strtolower');
        //验证验证码是否正确
        if (!($this->check_verify($code))) {
            //$this->error('验证码错误', U('Login/index'));
            die();
        }
        //验证账号密码是否正确
        $user = $member->where(array('m_name' => $username))->find();

        if (!$user) {
            $this->error('账号或密码错误 ', U('Login/index'));
            die();
        } else {
            $p = creatpassword($password, $user['salt']);
            if ($p !== $user['password']) {
                $this->error('账号或密码错误', U('/manage/login/index'));
                die();
            }
        }

        //验证账户是否被禁用
        if ($user['status'] == 0) {
            $this->error('账号被禁用，请联系超级管理员 :(', U('Login/index'));
            die();
        }

        $safe = usersafelogin($user);
        switch ($safe['sucess']) {
            case 1:
                $this->error($safe['msg'], U('Login/index'));
                die;
                break;
            case 2:
                session('adminuserinfo', $user);
                $this->redirect('/manage/login/login2');
                break;
        }

        $log = new Model\AdminLogModel();
        $logm = $log->baseModel;
        $logm['m_id'] = $user['m_id'];
        $logm['sid_id'] = $user['sid_id'];
        $info = getlogtype(CONTROLLER_NAME . '_' . ACTION_NAME);
        $info['status'] = 1;
        $info['statusstr'] = '成功';       //文本状态
        $info['opcontent'] = $username;      //操作内容
        $info['objectid'] = $user['m_id'];       //操作对象ID
        $info['objecttable'] = 'admin';     //操作对象表
        $log->addlog($logm, $info);
        setmanage($user);

        //更新登陆信息
        $data['last_ip'] = get_client_ip();
        $data['last_login'] = time();

        $mark = $member->where(array('m_id' => $user['m_id']))->save($data);
        if ($mark) {
            topredirect(U('Index/index'));
        }
    }

    /**
     * 登录验证
     */
    public function checklogin(){
         if (IS_POST) {
            $msgdata = array('code' => 0,
                'msg' => "",
                'sub_code' => "",
                'ub_msg' => "",
                'localhost' => '');
            $member = new Model\AdminModel();
            $username = I('username', '', 'addslashes');
            $password = I('password');
            if ($this->sysconfig['login_code']) {
                $code = I('verify', '', 'strtolower');
                if (!($this->check_verify($code))) { //验证验证码是否正确
                    $msgdata['code']='10000000';
                    $msgdata['msg']='登录失败';
                    $msgdata['sub_code']='10000001';
                    $msgdata['sub_msg']='验证码错误';                    
                    $this->ajaxReturn($msgdata);
                }
            }
            //验证账号密码是否正确
            $user = $member->where(array('m_name' => $username))->find();

            if (!$user) {
                $msgdata['code'] = '10000000';
                $msgdata['msg'] = '登录失败';
                $msgdata['sub_code'] = '10000002';
                $msgdata['sub_msg'] = '账号或密码错误';
                $this->ajaxReturn($msgdata);
            } else {
                $p = creatpassword($password, $user['salt']);
                if ($p !== $user['password']) {
                    $msgdata['code'] = '10000000';
                    $msgdata['msg'] = '登录失败';
                    $msgdata['sub_code'] = '10000003';
                    $msgdata['sub_msg'] = '账号或密码错误';
                    $this->ajaxReturn($msgdata);
                    die();
                }
            }
            //验证账户是否被禁用
            if ($user['status'] == 0) {
                $msgdata['code'] = '10000000';
                $msgdata['msg'] = '登录失败';
                $msgdata['sub_code'] = '10000004';
                $msgdata['sub_msg'] = '账号被禁用';
                $this->ajaxReturn($msgdata);
                die();
            }

            $safe = usersafelogin($user);
            switch ($safe['sucess']) {
                case 1:
                    $msgdata['code'] = '10000000';
                    $msgdata['msg'] = '登录失败';
                    $msgdata['sub_code'] = '10000005';
                    $msgdata['sub_msg'] = $safe['msg'];                    
                    $this->ajaxReturn($msgdata);
                    die();
                    break;
                case 2: 
                    //单点登录
                    if ($this->sysconfig['single_sign']) {
                        session('adminuserinfo', $user);//强制登录
                        $msgdata['code'] = '10000000';
                        $msgdata['msg'] = '登录失败';
                        $msgdata['sub_code'] = '10000006';
                        $msgdata['sub_msg'] = $safe['msg'];
                        $msgdata['localhost'] = U('Login/loginqz');
                        $this->ajaxReturn($msgdata);
                        die();
                    }
                    break;
            }

            $log = new Model\AdminLogModel();
            $logm = $log->baseModel;
            $logm['m_id'] = $user['m_id'];
            $logm['sid_id'] = $user['sid_id'];
            $info = getlogtype(CONTROLLER_NAME . '_' . ACTION_NAME);
            $info['status'] = 1;
            $info['statusstr'] = '成功';       //文本状态
            $info['opcontent'] = $username;      //操作内容
            $info['objectid'] = $user['m_id'];       //操作对象ID
            $info['objecttable'] = 'admin';     //操作对象表
            $log->addlog($logm, $info);
            setmanage($user);     

            //更新登陆信息
            $data['last_ip'] = get_client_ip();
            $data['last_login'] = time();
            $mark = $member->where(array('m_id' => $user['m_id']))->save($data);

            session('adminuserinfo', $user);
            $msgdata['code'] = '0';
            $msgdata['msg'] = '登录成功';
            $msgdata['sub_code'] = '0';
            $msgdata['sub_msg'] = '';
            $msgdata['localhost'] = U('Index/index');
            $this->ajaxReturn($msgdata);
        }
    }

        //登陆验证
    public function loginqz() {

        $member = D('admin');
        //验证账号密码是否正确
        $user = session('adminuserinfo');
        session('adminuserinfo', null);
        if (!$user) {
            topredirect(U('Login/index'));
//            $this->success('请重新登录', U('Login/index'));
            die();
        }

        //验证账户是否被禁用
        if ($user['status'] == 0) {
            $this->success('账号被禁用，请联系超级管理员 :(', U('Login/index'));
            die();
        }

        $safe = usersafelogin($user, false);
        switch ($safe['sucess']) {
            case 1:
                $this->success($safe['msg'], U('Login/index'));
                die;
                break;
        }

        $log = new Model\AdminLogModel();
        $logm = $log->baseModel;
        $logm['m_id'] = $user['m_id'];
        $logm['sid_id'] = $user['sid_id'];
        $info = getlogtype(CONTROLLER_NAME . '_' . ACTION_NAME);
        $info['status'] = 1;
        $info['statusstr'] = '成功';       //文本状态
        $info['opcontent'] = $username;      //操作内容
        $info['objectid'] = $user['m_id'];       //操作对象ID
        $info['objecttable'] = 'admin';     //操作对象表
        $log->addlog($logm, $info);

        setmanage($user);

        //更新登陆信息
        $data['last_ip'] = get_client_ip();
        $data['last_login'] = time();
        //如果数据更新成功  跳转到后台主页
        if ($member->where(array('m_id' => $user['m_id']))->save($data)) {
            topredirect(U('Index/index'));
        }
    }

    //验证码
    public function verify() {
        $Verify = new \Think\Verify();
        $Verify->codeSet = '0123456789';
        $Verify->useCurve = false;            // 是否画混淆曲线
        $Verify->useNoise = false;            // 是否添加杂点	
        $Verify->fontSize = 18;
        $Verify->length = 4;
        $Verify->bg = array(238, 238, 238);  // 背景颜色
        $Verify->entry();
    }

    protected function check_verify($code) {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }

    /*
     * 退出登录
     */

    public function logout($type = 0) {
        $logouttype = array(
            0 => '正常退出',
            1 => '当前IP与登录IP不一致下线',
            2 => '超时下线',
            3 => '异地登入,强制下线'
        );

        $muid = getmanage();       
        $uid=$muid['mid'];
        if ($uid) {
            if (crackin($type, '0,2')) {// 登录冲突退出时，不清除在线标记
                $db = new Model\SaferuleModel(); //清除在线标记     
                $db->where(array('m_id' => $uid))->setField('isonline', 0); //
            }
            
            $db = new Model\AdminModel();
            $user = $db->getmodelbyid($uid);
            $log = new Model\AdminLogModel();
            $logm = $log->baseModel;
            $logm['m_id'] = $user['m_id'];
            $logm['sid_id'] = $user['sid_id'];
            $info = getlogtype(CONTROLLER_NAME . '_' . ACTION_NAME);
            $info['status'] = 1;
            $info['statusstr'] = '成功';       //文本状态
            $info['opcontent'] = $user['m_name'] . "-" . $logouttype[$type];      //操作内容
            $info['objectid'] = $user['m_id'];       //操作对象ID
            $info['objecttable'] = 'admin';     //操作对象表
            
            $log->addlog($logm, $info);
        }
        clearmange();
        topredirect(U('Login/index'));
    }

    /**
     * 定时任务
     * 更新在线列表
     */
    public function crons() {
//        $onlinereflash = $this->sysconfig['onlinereflash'];
//        $onlinereflash = C('SESSIONEXPIRE');
        $db = new Model\SaferuleModel();
        $rs = $db->reflash();
        $db = new Model\NewsModel();
        $db->timepost();
    }

    /**
     * 标记活跃用户
     */
    public function active() {
        $onlinereflash = C('SESSIONEXPIRE');
        $db = new Model\SaferuleModel();
        $rs = $db->reflash($onlinereflash);

        $mid = cookie(C('ALLIANCE.USER_AUTH_KEY'));
        $mid = jp_decrypt($mid);
        $manageid = str_replace('rdm1978', '', $mid);
        if ($manageid) {
            $t = markactive($manageid, $this->sysconfig);
            //cookie(C('ALLIANCE.USER_AUTH_FLASH'), 0);               
        }
    }   

}
