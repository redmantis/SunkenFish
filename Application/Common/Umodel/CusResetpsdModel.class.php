<?php

namespace Common\Umodel;

use Think\Model;

/**
 * Description of CusResetpsdModel
 * 重置密码记录
 * @author Redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class CusResetpsdModel extends Model {

    /**
     * 创建密码找回邮件
     * @param type $map
     * @param type $config
     * @return string
     */
    public function creatmail($map, $config) {
        $db = new CusCustomerModel();
        $mid = $db->checkMail($map);
        if ($mid) {
            $salt = createRandCode(6);
            $checkstr = \Org\Util\Stringtools::keyGen();
            $checkstr2 = creatpassword($checkstr, $salt);

            $model = array(
                'm_id' => $mid,
                'email' => $map['email'],
                'checkstr' => $checkstr2,
                'salt' => $salt,
                'endtime' => time() + 3600 * 26,
                'addtime' => time(),
                'status' => 0,
                'validtime' => 0,
            );
            $id = $this->add($model);
            if ($id) {
                $pam = array("chkid" => $id, "chkstr" => $checkstr);
                $getpassword = $config['web_site'] . U("/Ucent/login/resetpassword1", $pam);
                $s = getPasswordMail($config, $getpassword, $map['username'], $map['email'], "{$config["web_title"]}密码找回");
                if ($s) {
                    $data['status'] = 1;
                    $data['msg'] = $config['mailer_sucess'];
                } else {
                    $data['status'] = 0;
                    $data['data'] = $map;
                    $data['msg'] = "邮件发送失败，请重试";
                }
            } else {
                $data['status'] = 0;
                $data['msg'] = "邮件记录创建失败，请重试";
            }
        } else {
            $data['status'] = 0;
            $data['msg'] = "帐号不存在或邮箱错误，请确认你的帐号邮箱";
        }
        return $data;
    }

    /**
     * 验证用户邮箱
     * @param type $id
     * @param type $checkstr
     * @return type
     */
    public function checkback($id, $checkstr) {
        $map['id'] = $id;
        $map['endtime'] = array('gt', time());
        $map['status'] = 0;
        $map['validtime'] = 0;
        $model = $this->where($map)->find();
        if ($model) {
            $crcakstr = creatpassword($checkstr, $model['salt']);
            if ($crcakstr == $model['checkstr']) {
                return $model;
            }
        }
        return NULL;
    }

    /**
     * 密码重置
     * @param type $data
     * password
     * repassword
     * m_id
     * email
     * id
     * @return string
     */
    public function resetpass($data) {
        $rs = array('status' => 0, 'msg' => '');
        $pass = $data['password'];
        if (empty($pass)) {
            $rs['msg'] = "password_empty";
            return $rs;
        }
        if ($pass !== $data['repassword']) {
            $rs['msg'] = "password_same";
            return $rs;
        }

        $map = array('id' => $data['id'], 'm_id' => $data['m_id'], 'email' => $data['email'], 'status' => 0);        
        
        $valuearray = array();
        $filedarray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $m = $this->where($filedarray)->bind($valuearray)->find();
        if (empty($m)) {
            $rs['msg'] = "link_false"; //链接无效
            return $rs;
        }
     
        $dbm = new CusCustomerModel();
        $map = array('id' => $data['m_id'], 'email' => $data['email']);  
           
        $valuearray = array();
        $filedarray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $model = $dbm->where($filedarray)->bind($valuearray)->find();
 
        if ($model) {
            $salt = createRandCode(6);
            $npwd = array();
            $npwd['password'] = creatpassword(I('password'), $salt);
            $npwd['salt'] = $salt;
            $this->startTrans();
            $r = $dbm->where($filedarray)->bind($valuearray)->save($npwd);
            if ($r) {
                $sd = array('status' => 1, 'validtime' => time());
                $m = $this->where(array('id' => ":id"))->bind(":id", $data['id'])->save($sd);
                if ($m) {
                    $this->commit();
                } else {
                    $this->rollback();
                }
                $rs['location'] = creat_url_lan('/Ucent/login/result', array('st' => 'success'));
                $rs['status'] = 1;
                $rs['msg'] = "password_sucess";
                return $rs;
            } else {
                $this->rollback();
            }
        }
        $rs['location'] = creat_url_lan('/Ucent/login/result', array('st' => 'fail'));
        $rs['msg'] = "password_false";
        return $rs;
    }
}
