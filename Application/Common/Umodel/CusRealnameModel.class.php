<?php

/*
 * 用户实名认证资料
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2018-2-7 15:06:07
 * @Modify  2018-2-7 15:06:07
 * @CopyRight:  2018 by RDM
 */
namespace Common\Umodel;

use Think\Model;

class CusRealnameModel extends \Think\Model {

    //put your code here
    protected $_validate = array(
        array('userid', 'number', '关键参数缺失！', '', '', 3), //默认情况下用正则进行验证     
        array('auth_type', array('ali', 'mayi'), '类型错误', 3, 'in'), // 当值不为空的时候判断是否在一个范围内
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'userid desc',
        ),
    );
    
    /**
     * 更新实名认证信息
     * @param type $data
     * @return type
     */
    public function authentication($data) {

        if ($data['cert_stauts'] == 0) {
            $rs = array('status' => 0, 'msg' => '非实名用户，认证失败');
        } else {
            $token = C('TOKEN_ON');
            C('TOKEN_ON', false);
            if (!$this->create($data)) {
                $rs['status'] = 0;
                $rs['data'] = $data;
                $rs['msg'] = $this->getError();
            } else {
                $data['cert_time'] = time();
                $model = $this->where(array('userid' => ":userid"))->bind(":userid", $data['userid'])->find();
                $data['cert_times'] = $model['cert_times'] + 1;
                $data['biz_result'] = serialize($data['biz_result']);
                if ($model) {
                    $ds = $this->where(array('userid' => ":userid"))->bind(":userid", $data['userid'])->save($data);
                } else {
                    $data['cert_times'] = 1;
                    $data['addtime'] = time();
                    $ds = $this->add($data);
                }
                if ($ds) {
                    $rs['status'] = 1;
                    $rs['msg'] = "认证成功";
                } else {
                    $rs['status'] = 0;
                    $rs['msg'] = "认证失败";
                }
            }
            C('TOKEN_ON', $token);
        }
        return $rs;
    }
    
    /**
     * 查询用户实名认证信息
     * @param type $data
     * @return string
     */
    public function getauthent($data) {
        if (is_number($data['userid'])) {
            $model = $this->where(array('userid' => ":userid"))->bind(":userid", $data['userid'])->find();
        }
        if ($model) {
            $rs['status'] = 1;
            $rs['data'] = $model;
        } else {
            $rs['status'] = 0;
            $rs['msg'] = "查询失败";
        }
        return $rs;
    }

}
