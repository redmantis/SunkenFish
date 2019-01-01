<?php

/*
 * 用户Access管理
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-7 10:13:36
 * @Modify  2017-12-7 10:13:36
 * @CopyRight:  2017 by RDM
 */

namespace Common\Umodel;
use Think\Model;

class CusAccessModel extends Model {

    protected $_validate = array(
        array('access_token', 'require', 'access_token不能为空', 1), //默认情况下用正则进行验证
        array('deviceid', 'require', '设备ID不能为空！', 1),
    );
    protected $patchValidate = true; //批量验证数据  
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'userid desc',
        ),
    );

    /**
     * 读取用户信息
     * @param type $map
     * @return type.
     */
    public function getuserinfo($data) {
        if (!$this->create($map)) {
            $rs['status'] = 0;
            $rs['msg'] = $this->getError();
        } else {
            $filedarray = array();
            $valuearray = array();
            $map = array('access_token' => $data['access_token'], 'deviceid' => $data['deviceid']);
            $map['expires_in'] = array('gt', time());
            $filedarray = array();
            $valuearray = array();
            tracemaptobind($map, $filedarray, $valuearray);
            $userid = $this->where($filedarray)->bind($valuearray)->getField('userid');
            if ($userid) {
                $db = new CusCustomerModel();
                return $db->getmodelbyid($userid);
            }
        }
    }

    /**
     * 令牌续期
     * @param type $data
     * @return type
     */
    public function refresh_token($data) { 
        if (!$this->create($data)) {
            $rs['status'] = 0;
            $rs['msg'] = $this->getError();
            return $rs;
        } else {
            $filedarray = array();
            $valuearray = array();
            $map = array('access_token' => $data['access_token'], 'deviceid' => $data['deviceid']);
            $map['expires_in']=array('gt',  time());
            tracemaptobind($map, $filedarray, $valuearray);
            $old = $this->where($filedarray)->bind($valuearray)->find();
            $rs = array();
            $rs['status'] = 0;
            $rs['msg'] = "令牌续期失败";
            if ($old) {
                $access_token_expires = getConfig('access_token_expires');
                $newtoken['expires_in'] = time() + $access_token_expires;
                $newtoken['access_token'] = \Org\Util\Stringtools::keyGen();
                if ($this->where($filedarray)->bind($valuearray)->save($newtoken)) {
                    $rs['status'] = 1;
                    $rs['data'] = $newtoken;
                    $rs['msg'] = "令牌续期成功";
                }
            }
            return $rs;
        }
    }

    /**
     * 获取token
     * @param type $userid
     * @param type $deviceid
     */
    public function get_access_token($data) {
        $username = $data['username'];
        $password = $data['password'];
        $deviceid = $data['deviceid'];
        $rules = array(
            array('username', 'require', 'username_require',1), //默认情况下用正则进行验证
            array('username', '/^[a-zA-Z0-9]{3,20}$/u', 'username_safe_crcak',1), //默认情况下用正则进行验证         
            array('password', 'require', 'password_empty', 1, ''), //默认情况下用正则进行验证   
            array('deviceid', 'require', '设备ID不能为空！',1),
        );
        if (!$this->validate($rules)->create($data)) {
            $rs['status'] = 0;
            $rs['msg'] = $this->getError();
            return $rs;
        } else {
            unset($data['deviceid']);
            $db=new CusCustomerModel();
            $user = $db->where(array('username' => ":username"))->bind(":username", $username)->find();
            $rs = array();
            $rs['status'] = 0;
//            $rs['data']=$username;
            if (!$user) {
                $rs['msg'] = 'Ucenter_nouser'; //用户不存在
                return $rs;
            } else {
                $p = creatpassword($password, $user['salt']);
                if ($p !== $user['password']) {
                    $rs['msg'] = 'Ucenter_passworderror'; //密码错误
                    return $rs;
                }
            }
            if ($user['status'] == 0) {//帐号被禁
                $rs['msg'] = 'Ucent_AccountDisabled';
                return $rs;
            }

            $rs = array('status' => 0);
            $map = array('userid' => $user['id'], 'deviceid' => $deviceid);
            $filedarray = array();
            $valuearray = array();
            tracemaptobind($map, $filedarray, $valuearray);
            $this->where($filedarray)->bind($valuearray)->delete();
            $access_token_expires = getConfig('access_token_expires');
            $map['expires_in'] = time() + $access_token_expires;
            $map['access_token'] = \Org\Util\Stringtools::keyGen();
            if ($this->add($map)) {
                $rs['status'] = 1;
                $rs['data'] = array('access_token' => $map['access_token'], 'expires_in' => $map['expires_in']);
            } else {
                $rs['msg'] = "令牌创建失败，请重试";
            }
            return $rs;
        }
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
