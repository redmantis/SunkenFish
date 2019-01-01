<?php

/*
 * 用户余额表
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-7 10:13:36
 * @Modify  2017-12-7 10:13:36
 * @CopyRight:  2017 by RDM
 */

namespace Common\Umodel;
use Think\Model;

class CusMoneyModel   extends Model {

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
     * 用户余额及押金
     * @param type $data
     * @return type
     */
    public function getusermoney($data) {
        $money = $this->where(array('userid' => ":userid"))->bind(":userid", $data['userid'])->field('money,deposit')->find();
        if(empty($money)){
           $money=array('money'=>0,'deposit'=>0);
        }
        return $money;
    }

    /**
     * 设置余额及押金
     * @param type $model
     * @param type $Trans  使用事务进行回滚
     */
    public function setmoney($data, $Trans = 1) {
        $rs = array('status' => 0, 'msg' => '');
        $data['log_ip'] = get_client_ip();
        $data['lasttime'] = time();
        $map = array('userid' => $data['userid']);

        $mark = 0;
        if ($Trans) {
            $this->startTrans();
        }
        $lock = $this->where($map)->setField('lock', 1); //锁定记录

        $model = $this->where($map)->find();
        $nmodel = array('userid' => $data['userid']);
        $nmodel['log_ip'] = $data['log_ip'];
        $nmodel['lasttime'] = $data['addtime'];

        if ($model) {//余额数据更新
            if ($lock) {
                $data['moneybefore'] = $model['money'];
                $data['depositbefore'] = $model['deposit'];
                $this->where($map)->save($nmodel);
                $money = $this->where($map)->setInc('money', $data['money']); //修改余额     
                $deposit = $this->where($map)->setInc('deposit', $data['deposit']); //修改押金
            } else {
                $rs['msg'] = '用户余额锁定失败';
            }
        } else {//余额数据不存在时
            $lock = 1;
            $data['lock'] = 1;
            if (($data['money'] >= 0) && ($data['deposit'] >= 0)) {
                $money = $this->add($data);
                $deposit = 1;
            } else {
                $rs['msg'] = '订单余额或定金不能为负数';
            }
        }

        $score = $this->getusermoney(array('userid' => $data['userid']));
        $data['moneyafter'] = $score['money'];
        $data['depositafter'] = $score['deposit'];
        $db = new LogMoneyModel();
        $log = $db->add($data); //添加变更日志
        if ($lock && $money && $deposit && $log && ($score['money'] >= 0) && ($score['deposit'] >= 0)) {
            $this->where($map)->setField('lock', 0);
            if ($Trans) {
                $this->commit();
            }
            $rs['status'] = 1;
            $rs['msg'] = 'DataAddSucc';
        } else {
            if ($Trans) {
                $this->rollback();
            }
            $rs['msg'] = '余额或定金不能为负数';
        }
        return $rs;
    }
}
