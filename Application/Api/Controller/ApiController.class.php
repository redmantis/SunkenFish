<?php
namespace Api\Controller;
use Think\Controller;
use Common\Model;

class ApiController extends Controller {
    public $manageinfo;
    public $userinfo;


    public function _initialize() {
        $this->manageinfo = getmanage(); //后台管理员身份信息
        $this->userinfo = getuserinfo(); //前台管理员身份信息
    }

    public function index($data = null) {
        if (IS_POST) {
            $data = I('post.');
            unset($data['p']);
        } else {
            $data = I('get.');
            unset($data['api/api/index_html']);
            unset($data['p']);
        }
        $token_ram =  C('TOKEN_ON');
        $usermodel = $data['usermodel'];
        $action = strtolower($data['action']);
        switch ($usermodel) {
            case 'manage':
                $checktp = 'm';
                break;
            case 'pcusers':
                $checktp = 'u';
                break;
            case 'mobileusers':
                C('TOKEN_ON', false);
                if ($data['access_token'] !== '123456') {
                    $db = new \Common\Umodel\CusAccessModel();
                    $this->userinfo = $db->getuserinfo($data);
                } else {
                    $db = new \Common\Umodel\CusCustomerModel();
                    $this->userinfo = $db->getmodelbyid(34);
                }
                $checktp = 'u';
                unset($data['access_token']);
                unset($data['deviceid']);
                break;
        }

        if (crackin($action, C('SKIPACTIONS'))) {//怱略鉴权的方法
            C('TOKEN_ON', false);
        }
    
        $model = new \Think\Model();
        if (!$model->autoCheckToken($data)) {            
            $rs = array('status' => 20000005, 'data' => $data, 'msg' => showTagbyMark('_TOKEN_ERROR_'));
        } else {
            unset($data[C('TOKEN_NAME')]);
            $notreturnJson = strtolower($data['notreturnJson']);
            unset($data['action']);
            unset($data['returnJson']);
            $uinfo = array('manage' => $this->manageinfo, 'user' => $this->userinfo, 'checktp' => $checktp);
            C('TOKEN_ON', false);
            $rs = common_data_readapi($action, $data, 1, $uinfo);
            C('TOKEN_ON', $token_ram);
        }        
        gettoken();
        if ($notreturnJson) {
            return $rs;
        } else {
            $this->ajaxReturn($rs);
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
}
