<?php
namespace Common\Umodel;
use Think\Model;
use Common\Zfmodel;
/**
 * Description of CusCustomer
 * 用戶管理
 */
class CusCustomerModel extends Model {

    protected $_validate = array(
        array('password', 'require', '请填写密码！', '', '', self::MODEL_INSERT), //默认情况下用正则进行验证     
        array('staus', array(0, 1), '请勿恶意修改字段', 3, 'in'), // 当值不为空的时候判断是否在一个范围内
    );
    protected $patchValidate = true; //批量验证数据  
    public $ShowMap = array(
        'id' => '序号',
        'username' => '帐号',
        'nickname' => '昵称',
        'email' => '邮箱',
        'lasttime' => array(
            'type' => 'time',
            'format' => 'Y-m-d h:i:s',
            'title' => '最后登录',
        ),
          'modid' => array(
            'type' => 'prompt',//switch|config
            'callback'=>'chg_user_mod',
            'format' => 'YunyinType',
            'table' => 'CusCustomer',
            'mustinput' => '1,0', //必须输入处理原因的操作项  
            'title' => '业主类型',
            'idd'=>'id',
        ), 
         'status' => array(
            'type' => 'prompt',//switch|config
            'format' => 'opcolse',
            'table' => 'CusCustomer',
            'mustinput' => '1,0', //必须输入处理原因的操作项  
            'title' => '状态',
            'idd'=>'id',
        ),     
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            //'class'=>'layui-btn-group',
            'btnlist' => array(
                'show' => array(
                    'title' => '查看',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'show',
                        'fields' => array(
                            'id' => 'id',
                        ),
                    )
                ),
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'update',
                        'fields' => array(
                            'id' => 'id',
                        ),
                    )
                ),
                'del' => array(
                    'title' => '禁用',
                    'link' => array(
                        'mod' => 'diag_del',
                        'url' => 'del',
                        'cls' => 'diag_del',
                        'fields' => array(
                             'id' => 'id',
                        ),
                    )
                ),
            ),
        ),
    );
    
    /**
     * 获取用戶基本资料
     * @param type $mid
     * @return type
     */
     public function getmodel($id) {
        $model = $this->where(array('id' => ":id"))->bind(":id", $id)->find();
        $catchpath = "details/customerdetails/{$id}";
        $value = $model;
        unset($value['password']);
        unset($value['salt']);
        F($catchpath, $value);
        return $model;
    }

    /**
     * 获取签到资料
     * @param type $data
     * @return int
     */
    public function get_signinfo($data) {
        $rs = $this->where(array('id' => ":id"))->bind(":id", $data['userid'])->field('sign,signcount,signtime')->find();
        if (date('Y-m-d', $rs['signtime']) == date('Y-m-d')) {
            $rs['enablesign'] = 1;
        } else {
            $rs['enablesign'] = 0;
        }
        $rs['status'] = 1;
        return $rs;
    }

    /**
     * 缓存读用户资料
     * @param type $id
     */
    public function getmodelbyid($id) {
        $catchpath = "details/customerdetails/{$id}";
        $value = F($catchpath);
        if ($value) {
            return $value;
        } else {
            $value = $this->getmodel($id);
            $db = new CusRealnameModel();
            $map = array("userid" => $id);
            $real = $db->getauthent($map);

            if ($real['status']) {
                $real = $real['data'];
                $value['cert_stauts'] = $real['cert_stauts'];
                $value['is_admittance'] = $real['is_admittance'];
            }

            unset($value['password']);
            unset($value['salt']);
            F($catchpath, $value);
            return $value;
        }
    }

    public function reg($model) {
        $rules = array(
            array('xieyi', 1, '必须同意用户协议', 1, 'equal'), // 在新增的时候验证name字段是否唯一   
            array('username', 'require', 'username_require'), //默认情况下用正则进行验证
            array('username', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{6,11}$/u', 'username_safe_crcak'), //默认情况下用正则进行验证         
            array('password', 'require', 'password_empty', '', ''), //默认情况下用正则进行验证
            array('repassword', 'password', 'password_same', 0, 'confirm'), // 验证确认密码是否和密码一致
            array('username', '', 'username_exists', 0, 'unique'), // 在新增的时候验证name字段是否唯一               
        );
        
        C('TOKEN_ON',false);
        if (!$this->validate($rules)->create($model)) {
            $rs['status'] = 0;
            $rs['data']=$model;
            $rs['msg'] = $this->getError();
        } else {
           if (I("usermodel", '', trim) == 'pcusers') {
            $front_login_code = getConfig('front_login_code'); //前台验证码
            }
            if ($front_login_code) {
                $code = $model['verify'];
                $verify = new \Think\Verify();
                if (!($verify->check($code))) { //验证验证码是否正确
                    $rs['status'] = 0;
                    $rs['data'] = $model;
                    $rs['msg'] = 'SafetyFailed';
                    return $rs;
                }
            }          
            
            $model['last_ip'] = get_client_ip();
            $salt = createRandCode(6);
            $model['password'] = creatpassword($model['password'], $salt);
            $model['regtime'] = time();
            $model['lasttime'] = time();
            $model['salt'] = $salt;
            $model['status'] = 1;
            $this->startTrans();            
            $id= $this->add($model);
            if($id){
            $db=new CusScoreModel();
            $cusdata = array('userid' => $id, 'scoretype' => 'reg_score', 'sid_id' => $model['sid_id'], 'objid' => $id);
                $scorers = $db->setscore($cusdata, 0);
            }
            if ($id && $scorers['status']) {
                $this->commit();
                $user = $this->where(array('id' => ":id"))->bind(":id", $id)->find();
                setuserinfo($user);
                $rs['status'] = 1;
                $rs['msg'] = 'reg_sucess';
                $rs['location'] = creat_url_lan('ucent/index/index');
                $rs['id'] = $id;
            } else {
                $this->rollback();
                $rs['status'] = 0;
                $rs['msg'] = 'reg_sucess';
            }
        }
        return $rs;
    }
    
    public function autoreg($data) {
        $model = array();
        $model['username'] = creatOrderNo("sh");
        $model['password'] = \Org\Util\Stringtools::randString(8);
        $model['repassword'] = $model['password'];
        $model['nickname'] = $data['nickname'];
        $model['xieyi'] = $data['xieyi'];
        $model['verify'] = $data['verify'];
        $model['sid_id'] = $data['sid_id'];
        $model['thumb'] = $data['head_pic'];
        $model['realname'] = $data['realname'];
//        if ($data['head_pic']) {
//            $face = file_get_contents($data['head_pic']);
//            $thumb = C('SYSIMAGESEVURL') . 'third/' . $model['username'] . '.png';
//            file_put_contents($thumb, $face);
//            $model['thumb'] = $data['head_pic'];
//        }
        $rs = $this->reg($model);
        if ($rs['status'] == 1) {
            $pre=C('fyname');
            $uname = makeOrderNo($rs['id'], $pre['usernamepre'], 10);
            $this->where(array('id' => $rs['id']))->setField('username', $uname);
        }
        return $rs;
    }

    public function chanagepwd($data) {
        C('TOKEN_ON', false);
        $rules = array(
            array('oldpassword', 'require', 'oldpassword_empty'), //默认情况下用正则进行验证      
            array('password', 'require', 'password_empty', '', ''), //默认情况下用正则进行验证
            array('repassword', 'password', 'password_same', 0, 'confirm'), // 验证确认密码是否和密码一致       
            array('id', '/^[1-9]\d*$/', 'DisagreeUserinfo',0,'regex'), //默认情况下用正则进行验证      
        );
        $ra=array('status'=>0,'msg'=>'');
        if (!$this->validate($rules)->create($data)) {
            $rs['status'] = 0;      
            $rs['msg'] = $this->getError();
        } else {    
            $model =  $this->where(array('id'=>":id"))->bind(":id",$data['id'])->field('password,salt')->find();
            $oldpwd = creatpassword($data['oldpassword'], $model['salt']);            
            if ($oldpwd == $model['password']) {
                $salt = createRandCode(6);
                $npwd['password'] = creatpassword($data['password'], $salt);
                $npwd['salt'] = $salt;
                if ($this->where(array('id' => ":id"))->bind(":id", $data['id'])->save($npwd)) {
                    $rs['status'] = 1;
                    $rs['msg'] = 'DataModifySuc';
                } else {
                    $rs['msg'] = 'DataModifyFailed';
                }
            } else {
                $rs['msg'] = 'oldpassword_error';
            }
        }
        return $rs;
    }

    /**
     * 读取用户资料
     * @param type $mid
     * @return type
     */
    public function getUserinfo($mid){
        $map['m_id']=$mid;
//        $map['sid_id']=0;
        $model=  $this->where($map)->field('password,salt',true)->find();
        
        $db=new AdminOauthModel();
        $map=array('m_id'=>$mid);
        $rs=$db->where($map)->select();
        foreach ($rs as $k=>$v){
            $model[$v['oauth'].'_openid']=$v['openid'];
        }
        return $model;
    }
    
    /**
     * 检查用户邮箱
     * @param type $map
     * @return type
     */
    public function checkMail($map){
        if(empty($map['username'])||empty($map['email'])){
            return 0;
        }
        return $this->where($map)->getField('id');
    }
   
    /**
     * 修改用户资料
     * @param type $mid
     * @param type $data
     * @return type
     */
    public function saveinfo($data) {
        $rs = checkid($data['id']);
        unset($data['password']);
        unset($data['salt']);
        unset($data['id']);
        if ($rs['status']) {
            $id = $rs['id'];
            $status = $this->where(array('id' => ":id"))->bind(":id", $id)->save($data);

           
            $this->clearcatch($id);

            $rs['status'] = $status;           
            if ($status == 0) {
                $rs['msg'] = "DataModifyFailed";
//                $rs['data'] = $data;
//                $rs['sql'] = $this->getLastSql();
            }else{
                 $rs['msg'] = "DataModifySuc";
            }
        }
        return $rs;
    }
    
     /**
     * 修改用户绑定邮箱
     * @param type $mid
     * @param type $data
     * @return type
     */
    public function changeMail($data) {
        $rules = array(
            array('email', 'email', '请输入正确的邮箱地址'), //默认情况下用正则进行验证      
            array('mailcode', 'require', '邮箱验证码不能为空'), //默认情况下用正则进行验证          
            array('id', '/^[1-9]\d*$/', 'DisagreeUserinfo', 0, 'regex'), //默认情况下用正则进行验证      
        );
        $ra = array('status' => 0, 'msg' => '');
        if (!$this->validate($rules)->create($data)) {
            $rs['status'] = 0;
            $rs['msg'] = $this->getError();
        } else {
            if (!checkmailcode($data['email'], "crcakmail", $data['mailcode'])) {
                $rs['status'] = 0;
                $rs['msg'] = "邮箱验证码错误";
            } else {
                $rs = checkid($data['id']);
                unset($data['id']);
                if ($rs['status']) {
                    $id = $rs['id'];
                    $status = $this->where(array('id' => ":id"))->bind(":id", $id)->save($data);
                    $this->clearcatch($id);
                    $rs['status'] = $status;
                    if ($status == 0) {
                        $rs['msg'] = "DataModifyFailed";
                    } else {
                        $rs['msg'] = "DataModifySuc";
                    }
                }
            }
        }
        return $rs;
    }
    
    /**
     * 修改用户绑定手机
     * @param type $mid
     * @param type $data
     * @return type
     */
    public function changemobile($data) {
        $rules = array(
            array('mobile', 'number', '请输入正确的手机号码'), //默认情况下用正则进行验证      
            array('mobilecode', 'require', '短信验证码不能为空'), //默认情况下用正则进行验证          
            array('id', '/^[1-9]\d*$/', 'DisagreeUserinfo', 0, 'regex'), //默认情况下用正则进行验证      
        );
        $ra = array('status' => 0, 'msg' => '');
        if (!$this->validate($rules)->create($data)) {
            $rs['status'] = 0;
            $rs['msg'] = $this->getError();
        } else {
            if (!checkmailcode($data['mobile'], "crcaksms", $data['mobilecode'])) {
                $rs['status'] = 0;
                $rs['msg'] = "短信验证码错误";
            } else {
                $rs = checkid($data['id']);
                unset($data['id']);
                if ($rs['status']) {
                    $id = $rs['id'];
                    $status = $this->where(array('id' => ":id"))->bind(":id", $id)->save($data);
                    $this->clearcatch($id);
                    $rs['status'] = $status;
                    if ($status == 0) {
                        $rs['msg'] = "DataModifyFailed";
                    } else {
                        $rs['msg'] = "DataModifySuc";
                    }
                }
            }
        }
        return $rs;
    }

    public function clearcatch($id) {
        $catchpath = "details/customerdetails/{$id}"; //清缓存
        F($catchpath, NULL);
    }

    /**
     * 变更业主类型
     * 同时联动房屋和房间房源类型
     */
    public function chg_user_mod($data) {
        if (isset($data['enablerollback'])) {
            $enablerollback = $data['enablerollback'];
        } else {
            $enablerollback = true;
        }
        $rs=array('status'=>0);
        
        $oldmodid = $this->where(array('id' => ":id"))->bind(":id", $data['id'])->getField('modid');
        if ($oldmodid != $data['modid']) {
            if ($enablerollback) {
                $this->startTrans();
            }
            $s = $this->where(array('id' => ":id"))->bind(":id", $data['id'])->setField('modid', $data['modid']);
            
            $db = new Zfmodel\ZfBuildingsModel();
            $count = $db->where(array('userid' => $data['id']))->count();
            if ($count) {
                $b = $db->where(array('userid' => $data['id']))->setField('modid', $data['modid']);
            }else{
                $b=1;
            }
            
            $db = new Zfmodel\ZfRoomsModel();
            $count = $db->where(array('userid' => $data['id']))->count();
            if ($count) {
                $r = $db->where(array('userid' => $data['id']))->setField('modid', $data['modid']);
            }else{
                $r=1;
            }
            
            if($s && $r & $b){
                if ($enablerollback) {
                    $this->commit();
                }     
            }else{
                 if ($enablerollback) {
                    $this->rollback();
                }
                  $rs['status'] = 101;
                $rs['msg'] = "用户属性修改失败";
            }
        } 
        return $rs;
    }

    /**
     * 会员登录
     * @param type $m_id
     * @param type $sid_id
     */
    public function userlogin($model) {
        $rules = array(
            array('username', 'require', 'username_require'), //默认情况下用正则进行验证
            array('username', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{6,11}$/u', 'username_safe_crcak'), //默认情况下用正则进行验证         
            array('password', 'require', 'password_empty', '', ''), //默认情况下用正则进行验证    
        );
        C('TOKEN_ON', false);
        if (!$this->validate($rules)->create($model)) {
            $rs['status'] = 0;
            $rs['data'] = $model;
            $rs['msg'] = $this->getError();
            return $rs;
        } else {
            $username = $model['username'];
            $password = $model['password'];
            if (I("usermodel", '', trim) == 'pcusers') {
            $front_login_code = getConfig('front_login_code'); //前台验证码
            if ($front_login_code) {
                $code = $model['verify'];
                $verify = new \Think\Verify();
                if (!($verify->check($code))) { //验证验证码是否正确
                    $rs['status'] = 0;
                    $rs['data'] = $model;
                    $rs['msg'] = 'SafetyFailed';
                    return $rs;
                }
            }
            }
            $user = $this->where(array('username' => ":username"))->bind(":username", $username)->find();
            $rs = array();
            $rs['status'] = 0;
            if (!$user) {
                $rs['msg'] = 'Ucenter_nouser';
                return $rs;
            } else {
                $p = creatpassword($password, $user['salt']);
                if ($p !== $user['password']) {
                    $rs['msg'] = 'Ucenter_passworderror';
                    return $rs;
                }
            }
            return $this->login($user);
        }
    }

    /**
     * 用户登录保持
     * @param array $user
     * @return string
     */
    public function login($user) {
        $rs = array();
        $rs['status'] = 0;
        if (!$user) {
            $rs['userid'] = 0;
            $rs['msg'] = 'Ucenter_nouser';
            return $rs;
        }
        //验证账户是否被禁用
        if ($user['status'] == 0) {
            $rs['msg'] = 'Ucent_AccountDisabled';
            return $rs;
        }
        unset($user['password']);
        unset($user['salt']);  
        
        $data = array("lasttime" => time(), 'last_ip' => get_client_ip());
        $this->where(array('id' => $user['id']))->save($data);
        $user['lasttime'] = $data['lasttime'];
        $user['last_ip'] = $data['last_ip'];

        setuserinfo($user);
        $rs['status'] = 1;
        $rs['msg'] = 'login_sucess';
        $rs['userid'] = $user['id'];
        $pram = get_langue_parm();
        $url = U("/Ucent/index/index", $pram);
        $rs['location'] = $url;
        if (I("deviceid", '', trim)) {
            $dca = new CusAccessModel();
            $token = $dca->creat_token($user['id'], I("deviceid"));
            array('access_token' => $map['access_token'], 'expires_in' => $map['expires_in']);
            $rs['token'] = $token['data'];
        }
        return $rs;
    }

    /**
     * 
     * @param type $map
     * @param type $pagesize
     * @return string
     */
    public function getlist($map, $pagesize) {      
        $valuearray = array();
        $filedarray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $count = $this->where($filedarray)->bind($valuearray)->count();
        $skip = get_firstrow($pagesize);   
        $list=  $this->where($filedarray)->bind($valuearray)->scope()->limit($skip,$pagesize)->select();
        $rs['count']=$count;
        $rs['list']=$list;
        $rs['status']=1;
        $rs['msg']='';
        return $rs;
    }
}
