<?php

/*
 * 文件：SaferuleModel.class.php
 * 邮件: feiyufly001@hotmail.com
 * 创建：redmantis <默鱼 at feiyufly001@hotmail.com> 2016-11-15 9:13:00
 * 最终：Rdm
 */

namespace Common\Model;
use Think\Model;
/**
 *登录安全控制
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class SaferuleModel extends Model {
     protected $fields = array('m_id',//用户ID
        'lastmodify',//最后修改时间
        'modifytimes',//修改次数
        'resttimes',//重置次数
        'hasmodify',//需要进行修改
        'isonline',//在线标志
        'lastvis',//最后一次活动时间
        'useip',//用户当前IP
        'userext',//其它扩展信息
    );
    public $baseModel = array('m_id' => 0,
        'lastmodify' =>0,
        'modifytimes' => 0,
        'resttimes' => 0,
        'hasmodify' => 0,
        'isonline' => 0,
        'lastvis' => 0,
        'useip' => '',
        'userext' => '',   
    );
    
    public function getmodelbyid($mid) {
        $model = $this->where(array('m_id' => $mid))->find();
        if ($model) {
            return $model;
        } else {
            $this->baseModel['m_id'] = $mid;
            $this->add($this->baseModel);
            return $this->baseModel;
        }
    }
    
    /**
     * 保存信息
     * @param type $model
     * @return type
     */
    public function savemode($model) {
        $map['m_id'] = $model['m_id'];
        return $this->where($map)->save($model);
    }
    
    /**
     * 活跃标记
     * @param type $mid
     */
    public function activemark($mid) {
        $map['m_id'] = $mid;
        $act = time();
        return $this->where($map)->setField('lastvis', $act);
    }

    /**
     * 定时任务，更新用户在线状态
     */
    public function reflash() {
        $onlinereflash = C('SESSIONEXPIRE');
        $last = time();
        $m = $last - $onlinereflash;
        $d = date("H:i;s", $m);
        $r = date("H:i;s", $last);
        $map['lastvis'] = array('lt', $m);
        $rs = $this->where($map)->setField('isonline', 0);
        return "{$rs}-{$d}-{$r}";
    }

    /*强制下线*/
    public function offline($mid){
        $map['m_id']=$mid;
        $this->where($map)->setField('isonline',0);
    }
}
