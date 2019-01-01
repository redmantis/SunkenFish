<?php

/*
 * 用户积分
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-5 11:03:11
 * @Modify  2017-12-5 11:03:11
 * @CopyRight:  2017 by RDM
 */

namespace Common\Umodel;
use Think\Model;

class CusScoreModel  extends Model {

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'userid desc',
        ),
    );

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

    /**
     * 用户积分
     * @param type $data
     * @return type
     */
    public function getusercore($data) {
        $score = $this->where(array('userid' => ":userid"))->bind(":userid", $data['userid'])->getField('score');
        return $score ? $score : 0;
    }

    /**
     * 设置用户积分
     * @param type $model
     * @param type $Trans  使用事务进行回滚  匹配外部事务，避免错误 commit
     */
    public function setscore($data, $Trans = 1, $score = null) {
        
        $rs = array('status' => 0, 'msg' => '');
        $data['log_ip'] = get_client_ip();
        $data['addtime'] = time();
        $map = array('userid' => $data['userid']);
        $model = $this->where($map)->find();       
        $lock = 0;
        $mark = 0; 
        
        $config = getAttrbymark($data['scoretype']); //读取配置属性
        if (is_number($config['tagvalue'])) {//配置数据为简单数字 
            $default = $config['tagvalue'];
        } else {//复杂数据配置  json字符串
            $d = htmlspecialchars_decode($config['tagvalue']);
            $arr = json_decode($d, true);
            $default = $arr['default'];
        }
        if ($score === null) {
            $data['score'] = $default;
        } else {
            $data['score'] = $score;
            $absscore = abs($score);
            if ($absscore < $arr['min'] || $absscore > $arr['max']) {
                return array('status' => 0, 'msg' => '输入值超出配置区间', 'scoretype' => $data['scoretype']);
            }
        }
        if ($data['score'] == 0) {//积分通道关闭
            return array('status' => 1, 'msg' => '积分通道关闭', 'scoretype' => $data['scoretype']);
        }

        if ($Trans) {
            $this->startTrans();
        }
        $cussign=0;
        if ($data['scoretype'] === 'sign_score'){//每日签到  单独处理
            $db = new CusCustomerModel();
            $cusmodel = $db->getmodel( $data['userid']);
            if (date('Y-m-d', $cusmodel['signtime']) !== date('Y-m-d')) {//同日签到时失败
                if (date('Y-m-d', $cusmodel['signtime']) == date('Y-m-d', strtotime("-1 day"))) {
                    $sign = $cusmodel['sign'] + 1; //连续签到
                } else {
                    $sign = 1;
                }
                $signmax=  getConfig('web_sign_max',$data['sid_id'],'baseinfo');
                $signx = $sign < $signmax ? $sign : $signmax; //最多连续7天积分加成
                $data['score'] = $data['score'] * $signx;

                $cmodel['id'] = $cusmodel['id'];
                $cmodel['signcount'] = $cusmodel['signcount'] + 1;
                $cmodel['sign'] = $sign;
                $cmodel['signtime'] = time();
                $crs = $db->saveinfo($cmodel);
                $db->getmodel($cusmodel['id']);//更新用户资料
                $cussign = $crs['status'];
                
                $rs['signcount']=$cmodel['signcount'];
                $rs['sign']=$cmodel['sign'];
            }
        } 
        else {
            $cussign = 1;
        }

        if ($cussign) {
            if ($model) {//积分数据更新
                $data['changebefore'] = $model['score'];
                $nmodel = array('userid' => $data['userid']);
                $nmodel['log_ip'] = $data['log_ip'];
                $nmodel['lasttime'] = $data['addtime'];
                $lock = $this->where($map)->setField('lock', 1); //锁定积分操作
                if ($lock) {
                    $this->where($map)->save($nmodel); //修改积分     
                    $mark = $this->where($map)->setInc('score', $data['score']);
                }
            } else {//积分数据不存在时
                $lock = 1;
                $data['lock'] = 1;
                if ($data['score'] > 0) {
                    $mark = $this->add($data);
                }
            }
        }

        $score = $this->where($map)->getField('score');
        $data['changeafer'] = $score;
        $db = new LogScoreModel();
        $log = $db->add($data);

        if ($lock && $mark && $log && ($score >= 0)) {
            $this->where($map)->setField('lock', 0);
            if ($Trans) {
                $this->commit();
            }
            $rs['status'] = 1;
            $rs['logid'] = $log;
            $rs['msg'] = 'DataAddSucc';
        } else {
            if ($Trans) {
                $this->rollback();
            }
            if ($score < 0) {
                $rs['msg'] = '积分不足';
            } else {
                $rs['msg'] = 'DataAddFailed';
            }
        }
        return $rs;
    }

}
