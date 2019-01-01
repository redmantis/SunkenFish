<?php

namespace Common\Model;

use Think\Model;

/**
 * Description of AdminModel
 * 第三方登陆
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AdminOauthModel extends Model {

    protected $_validate = array(
        array('openid', 'require', 'openid不能为空！'), //默认情况下用正则进行验证      
        array('oauth', 'require', '请指明第三方来源！'), //默认情况下用正则进行验证          
    );
    protected $_auto = array(
        array('m_id', 'replacezero', 3, 'function'), // 新增时置0
        array('token', 'replaceempety', 3, 'function'), // 新增时置 空格  oracle 字段不能为空
    );

    const WEIXIN = 'weixin';
    const QQOAUTH = 'qq';

    public function getinfo($data) {
        $map = array('oauth' => $data['oauth'], 'openid' => $data['openid']);
        $model = $this->where($map)->find();
        if ($model) {
            return $model;
        } else {
            C('TOKEN_ON', false);
            if ($this->create($data)) {
                $this->add();
                return $data;
            }
        }
    }
    
    /**
     * 绑定帐号
     * @param type $mid
     * @param type $map
     */
    public function binduser($mid,$map){
        $this->where($map)->setField('m_id',$mid);
    }

}
