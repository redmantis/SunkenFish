<?php
namespace Common\Model;
use Common\Base;
/**
 * Description of AdminModel
 * 管理员实例
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AdminModel  extends Base\SModel {
    
    //<editor-fold defaultstate="collapsed" desc="参数设置">
    const USERTYPE_MANAGER = 1; //后台管理员
    const USERTYPE_USER = 100; //普通会员
    const USERTYPE_VIP = 200; //vip会员

    protected $basetable = "admin";
    protected $idname="m_id";
    protected $delmark="status";
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'm_id asc',
        ),
        'orderby2' => array(
            'order' => 'm_id desc',
        ),
    );
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="列表显示项">
    public $ShowMap = array(
        'm_id' => '序号',
        'm_name' => '帐号',
        'truename' => '姓名',
        'sid_name' => '站点',
        'last_login' => array(
            'type' => 'time',
            'format' => 'Y-m-d',
            'title' => '最后登录',
        ),
        'status' => array(
            'type' => 'switch', //switch|config
            'format' => 'opcolse',
            'table' => 'admin',
            'title' => '状态',
            'idd' => 'm_id',
        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            //'class'=>'layui-btn-group',
            'btnlist' => array(
                'access' => array(
                    'title' => '分配角色',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'access',
                        'fields' => array(
                            'm_id' => 'm_id',
                        ),
                    )
                ),
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'update',
                        'fields' => array(
                            'm_id' => 'm_id',
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
                            'm_id' => 'm_id',
                        ),
                    )
                ),
            ),
        ),
    );
    //</editor-fold>

    protected $_validate = array(
        array('m_name','require','请填写用户名！'), //默认情况下用正则进行验证
        array('m_name', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '用户名只能包含中英文字母和数字！'), //默认情况下用正则进行验证
        array('truename','require','请填写真实姓名！'), //默认情况下用正则进行验证
        array('truename', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '真实姓名只能包含中英文字母和数字！'), //默认情况下用正则进行验证
      
        //array('tel','require','请填写联系方式！'), //默认情况下用正则进行验证
        array('password','require','请填写密码！','','',self::MODEL_INSERT), //默认情况下用正则进行验证
     	array('repassword','password','确认密码不正确',0,'confirm',self::MODEL_INSERT), // 验证确认密码是否和密码一致
        array('m_name','','用户名已存在！',0,'unique',self::MODEL_INSERT), // 在新增的时候验证name字段是否唯一
        //array('email','','邮箱已存在！',0,'unique',self::MODEL_BOTH), // 在新增的时候验证name字段是否唯一
        array('staus',array(0,1),'请勿恶意修改字段',3,'in'), // 当值不为空的时候判断是否在一个范围内
        array('type',array(1,2),'请勿恶意修改字段',3,'in'), // 当值不为空的时候判断是否在一个范围内
    );
    
     /**
     * 新建
     * @param type $model
     * @return string
     */
    public function addnew($model) {         
        if (!$this->create($model)) {
            $rs['status'] = '20000008';       
            $rs['msg'] = $this->getError();     
        } else {
            $model['collist'] = ',';
            unset($model['m_id']);
            $model['last_ip'] = get_client_ip();
            $salt = createRandCode(6);
            $model['password'] = creatpassword($model['password'], $salt);
            unset($model['repassword']);
            unset($model['id']);
            $model['addtime'] = time();
            $model['last_login'] = $model['addtime'];
            $model['salt'] = $salt;
            $model['birthday'] = strtotime($model['birthday']);
            $rs = parent::addnew($model);
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
    
    public function update($model) {
        unset($model['id']);
        if (isset($model['password']) && $model['password']) {
            $psd = $model['password'];
            $passrule = getConfig('passrule');
            if (!checkpwd($psd, $passrule)) {
                $msg['status'] = 10000003;
                $msg['msg'] = 'passrulealert';
                return $msg;
            }
            $salt = createRandCode(6);
            $model['password'] = creatpassword($psd, $salt);
            $model['salt'] = $salt;
        } else {
            unset($model['password']);
        }
        //强制更改超级管理员用户类型
        if (C('ALLIANCE.USER_AUTH_KEY') == $model[$this->idname]) {
            $model['m_grade'] = 1;
        }
        if (!$this->create($model, 2)) {
            $msg['status'] = 20000005;
            $msg['msg'] = $this->getError();
        } else {            
            if (isset($model['birthday'])) {
                $model['birthday'] = strtotime($model['birthday']);
            }
            $msg = parent::update($model);
            $this->clearcatch($model[$this->idname]);
        }
        return $msg;
    }

    /**
     * 修改用户资料
     * @param type $mid
     * @param type $data
     * @return type
     */
    public function saveinfo($model) {
        unset($model['id']);
        if (!$this->create($data, 2)) {
            $msg['status'] = 20000005;
            $msg['msg'] = $this->getError();
            return $msg;
        }
        if (isset($model['password']) && $model['password'] && isset($model['repassword']) && $model['repassword']) {
            $psd = $model['password'];
            $rpsd = $model['repassword'];
            $passrule = getConfig('passrule');
            if (!checkpwd($psd, $passrule)) {
                $msg['status'] = 10000003;
                $msg['msg'] = 'passrulealert';
                return $msg;
            }
            if ($psd !== $rpsd) {
                $msg['msg'] = "password_same";
                $msg['status'] = 10000007;
                return $msg;
            }

            $salt = createRandCode(6);
            $model['password'] = creatpassword($psd, $salt);
            $model['salt'] = $salt;
        } else {
            unset($model['password']);
            unset($model['repassword']);
        }
        unset($model['repassword']);
        if (isset($model['birthday'])) {
            $model['birthday'] = strtotime($model['birthday']);
        }
        //强制更改超级管理员用户类型
        if (C('ALLIANCE.USER_AUTH_KEY') == $model[$this->idname]) {
            $model['m_grade'] = 1;
        }
        $msg = parent::update($model);
        $this->clearcatch($model[$this->idname]);
        if ($msg['status'] == 0) {
            $dbs = new SaferuleModel();
            $m = $dbs->getmodelbyid($model[$this->idname]);
            $m['modifytimes'] = $m['modifytimes'] + 1;
            $m['lastmodify'] = time();
            $dbs->savemode($m);
        }
        return $msg;
    }

    public function sdel($mid) {       
        if (C('AUTH_CONFIG.AUTH_SUPERMAN') == $mid) {
            $msg['status'] = 10000009;
            $msg['msg'] = "superman_disable";
        }else{
            $msg =  parent::sdel($mid);
        }       
        return $msg;
    }

    public function del($mid) {
        if (C('AUTH_CONFIG.AUTH_SUPERMAN') == $mid) {
            $msg['status'] = 10000009;
            $msg['msg'] = "superman_disable";
        }else{
            $msg =  parent::del($mid);
        }       
        return $msg;
    }

    /**
     * 管理员切换站点
     * @param type $mid
     * @param type $sid_id
     */
    public function chanagesid($mid, $sid_id) {
        $this->where(array('m_id' => $mid, 'm_grade' => 1))->setField('sid_id', $sid_id);
        $user = $this->where(array('m_id' => $mid))->find();        
        setmanage($user);
    } 

    protected function trance_model($model) {
        $db = new SubsidiaryModel();
        $sid = $db->getmodebyid($model['sid_id']);
        $model['sid_name'] = $sid['sid_name'];
        unset($model['password']);
        unset($model['salt']);
        return $model;
    }

}
