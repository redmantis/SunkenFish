<?php

/*
 * 第三方登录
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2018-1-8 13:28:39
 * @Modify  2018-1-8 13:28:39
 * @CopyRight:  2018 by RDM
 */

namespace Common\Umodel;
use Think\Model;

class CusOauthModel extends \Think\Model {

    //put your code here
    protected $_validate = array(
        array('openid', 'require', '标识必填！', '', '', 3), //默认情况下用正则进行验证     
        array('oauth', array('qq', 'sina', 'weixin','ali'), '类型错误', 3, 'in'), // 当值不为空的时候判断是否在一个范围内
    );
    
        protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'userid desc',
        ),
    );
    
    /**
     * 第三方登录
     * @param type $info
     * @return type
     */
    public function login($info) {  
        $token = C('TOKEN_ON');
        C('TOKEN_ON', false);     
        if (!$this->create($info)) {
            $rs['status'] = 0;
            $rs['data'] = $data;
            $rs['msg'] = $this->getError();
        } else {
            $map['oauth'] = strtolower($info['oauth']);
            $map['openid'] = $info['openid'];
            $valuearray = array();
            $filedarray = array();
            tracemaptobind($map, $filedarray, $valuearray);
            $model = $this->where($filedarray)->bind($valuearray)->find();
            if ($model) {
                $user=new CusCustomerModel();
                $usermodel= $user->getmodel($model['userid']);
                return $user->login($usermodel);
            } else {
                $rs = ['status' => -1, 'msg' => '新用户，请先绑定帐号', 'data' => $info];
                $pram = get_langue_parm();
                $url = U("/Ucent/login/bind", $pram);
                $rs['location'] = $url;                
            }
        }
        C('TOKEN_ON', $token);
        return $rs;
    }

    /**
     * 解除绑定
     * @param type $data
     * @return type
     */
    public function unbinduser($data) {
        $token = C('TOKEN_ON');
        C('TOKEN_ON', false);
        $rule = array(
            array('userid', 'number', '关键参数缺失！'), //默认情况下用正则进行验证     
            array('oauth', array('qq', 'sina', 'weixin', 'ali'), '类型错误', 3, 'in'), // 当值不为空的时候判断是否在一个范围内
        );
        if (!$this->validate($rule)->create($data)) {
            $rs['status'] = 0;
            $rs['data'] = $data;
            $rs['msg'] = $this->getError();
        } else {
            $map = array();
            $map['oauth'] = strtoupper($data['oauth']);
            $map['userid'] = $data['userid'];
            $valuearray = array();
            $filedarray = array();
            tracemaptobind($map, $filedarray, $valuearray);
            $model = $this->where($filedarray)->bind($valuearray)->find();
            if ($this->where($filedarray)->bind($valuearray)->delete()) {
                if ($data['oauth'] === 'ali') {//解绑支付宝同时，清除帐号实名标记
                    $db = new CusCustomerModel();
                    $db->where(array('id' => ":id", 'realname' => 1))->bind(":id", $data['userid'])->setField('realname', 0);
                    $db->clearcatch($data['userid']);
                }
                $rs = array("status" => 1, 'msg' => "解绑成功", 'data' => $model);
            } else {
                $rs = array("status" => 0, 'msg' => "解绑失败", 'data' => $data, 'sql' => $this->getLastSql());
            }
        }
        C('TOKEN_ON', $token);
        return $rs;
    }

    /**
     * 第三方绑定
     * @param array $data
     * @return string
     */
    public function binduser($data) {
        $db = new CusCustomerModel();
        if (isset($data['username'])) {
            $user = $db->where(array('username' => ":username"))->bind(":username", $data['username'])->find();
            $rs = array();
            $rs['status'] = 0;
            if (!$user) {
                $rs['msg'] = 'Ucenter_nouser';
                return $rs;
            } else {
                $p = creatpassword($data['password'], $user['salt']);
                if ($p !== $user['password']) {
                    $rs['msg'] = 'Ucenter_passworderror';
                    return $rs;
                }
            }
        } else {
            $user = $db->where(array('id' => ":id"))->bind(":id", $data['userid'])->find();
        }
        
  

        $map = array();
        $map['oauth'] = strtolower($data['oauth']);
        $map['openid'] = $data['openid'];
        $valuearray = array();
        $filedarray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $model = $this->where($filedarray)->bind($valuearray)->find();
        if ($model) {
            $rs['msg'] = "操作失败，该【{$data['oauth']}】用户已与其他帐号绑定。";
            return $rs;
        }
        
        $map = array();
        $map['oauth'] = strtolower($data['oauth']);
        $map['userid'] = $user['id'];
        $valuearray = array();
        $filedarray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $model = $this->where($filedarray)->bind($valuearray)->find();
        if ($model) {
            $rs['msg'] = "操作失败，该帐号已与其他【{$data['oauth']}】用户绑定。";
            return $rs;
        }        
        unset($data['username']);
        unset($data['password']);
        $data['userid'] = $user['id'];
        if ($this->add($data)) {

            if ($data['realname'] == 1 && $user['realname'] == 0) {//支付宝帐号实名
                $db->where(array('id' => ":id"))->bind(":id", $user['id'])->setField('realname', 1);
                $db->clearcatch($user['id']);
            }

            $rs['status'] = 1;
            $db->login($user);
            $rs['location'] = cookie('oauth_ref');
            $rs['msg'] = '绑定成功。';
        } else {
            $rs['status'] = 0;
            $rs['msg'] = '绑定失败。';
        }
        return $rs;
    }

    /**
     * 第三方注册绑定
     * @param array $data
     * @return string
     */
    public function userbindreg($data) {
        $db = new CusCustomerModel();
        $rs = array();
        $rs['status'] = 0;
        if ($data['xieyi'] != 1) {
            $fyname = C("fyname");
            $rs['msg'] = "必须同意{$fyname['xieyi']}才能成为会员。";
            return $rs;
        }
        
        $map = array();
        $map['oauth'] = strtolower($data['oauth']);
        $map['openid'] = $data['openid'];
        $valuearray = array();
        $filedarray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $model = $this->where($filedarray)->bind($valuearray)->find();
        if ($model) {
            $rs['msg'] = "操作失败，该【{$data['oauth']}】用户已与其他帐号绑定。";
            return $rs;
        }

        $regrs = $db->autoreg($data);
        if ($regrs['status'] === 0) {
            return $regrs;
        }
        
        $data['userid'] = $regrs['id'];
        if ($this->add($data)) {
            $rs['status'] = 1;
            $db->login($user);
            $rs['location'] = cookie('oauth_ref');
            $rs['msg'] = '登录成功。';
        } else {
            $rs['status'] = 0;
            $rs['msg'] = '登录失败。';
        }
        return $rs;
    }
    
      /**
     * 读取列表
     * @param type $map
     * @param type $skip
     * @param type $pagesize
     * @return type
     */
    public function getuseroauth($data) {
        if (isset($data['userid'])) {
            $map = array("userid" => $data['userid'], 'sid_id' => $data['sid_id']);
            $filedarray = array();
            $valuearray = array();
            tracemaptobind($map, $filedarray, $valuearray);
            $rs = $this->where($filedarray)->bind($valuearray)->scope('orderby')->getField('oauth,openid,head_pic,nickname');
            $drs = array('list' => $rs, 'status' => 1);
        } else {
            $drs = array('msg' => '关键参数缺失', 'status' => 0, 'data' => $data);
        }
        return $drs;
    }

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
