<?php

/**
 * 支付完成修改订单
 * @param $order_sn 订单号
 * @param array $ext 额外参数
 * @return bool|void
 */
function update_pay_status($order_sn, $order_amount, $ext = array()) {
    if (stripos($order_sn, 'recharge') !== false) {
        //用户在线充值
        $order = M('recharge')->where(['order_sn' => $order_sn, 'pay_status' => 0])->find();
        if (!$order)
            return false; // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        M('recharge')->where("order_sn", $order_sn)->save(array('pay_status' => 1, 'pay_time' => time()));
        accountLog($order['user_id'], $order['account'], 0, '会员在线充值');
    }else {
        $db = new Common\Zfmodel\ZfOrderModel();
        $rs = $db->crcak($order_sn, $order_amount);
        if(!$rs){
            return $rs;
        }
        
        $order=$db->getmodel($id);


        // 如果这笔订单已经处理过了
        $count = M('order')->master()->where("order_sn = :order_sn and pay_status = 0 OR pay_status = 2")->bind(['order_sn' => $order_sn])->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if ($count == 0)
            return false;
        // 找出对应的订单
        $order = M('order')->master()->where("order_sn", $order_sn)->find();
        //预售订单
        if ($order['order_prom_type'] == 4) {
            $orderGoodsArr = M('OrderGoods')->where(array('order_id' => $order['order_id']))->find();
            // 预付款支付 有订金支付 修改支付状态  部分支付
            if ($order['total_amount'] != $order['order_amount'] && $order['pay_status'] == 0) {
                //支付订金
                M('order')->where("order_sn", $order_sn)->save(array('order_sn' => date('YmdHis') . mt_rand(1000, 9999), 'pay_status' => 2, 'pay_time' => time(), 'paid_money' => $order['order_amount']));
                M('goods_activity')->where(array('act_id' => $order['order_prom_id']))->setInc('act_count', $orderGoodsArr['goods_num']);
            } else {
                //全额支付 无订金支付 支付尾款
                M('order')->where("order_sn", $order_sn)->save(array('pay_status' => 1, 'pay_time' => time()));
                $pre_sell = M('goods_activity')->where(array('act_id' => $order['order_prom_id']))->find();
                $ext_info = unserialize($pre_sell['ext_info']);
                //全额支付 活动人数加一
                if (empty($ext_info['deposit'])) {
                    M('goods_activity')->where(array('act_id' => $order['order_prom_id']))->setInc('act_count', $orderGoodsArr['goods_num']);
                }
            }
        } else {
            // 修改支付状态  已支付
            $updata = array('pay_status' => 1, 'pay_time' => time());
            if (isset($ext['transaction_id']))
                $updata['transaction_id'] = $ext['transaction_id'];
            M('order')->where("order_sn", $order_sn)->save($updata);
            if (is_weixin()) {
                $wx_user = M('wx_user')->find();
                $jssdk = new app\common\logic\JssdkLogic($wx_user['appid'], $wx_user['appsecret']);
                $jssdk->send_template_message($order);
            }
        }
        // 减少对应商品的库存
        minus_stock($order['order_id']);
        // 给他升级, 根据order表查看消费记录 给他会员等级升级 修改他的折扣 和 总金额
        update_user_level($order['user_id']);
        // 记录订单操作日志
        if (array_key_exists('admin_id', $ext)) {
            logOrder($order['order_id'], $ext['note'], '付款成功', $ext['admin_id']);
        } else {
            logOrder($order['order_id'], '订单付款成功', '付款成功', $order['user_id']);
        }
        //分销设置
        M('rebate_log')->where("order_id", $order['order_id'])->save(array('status' => 1));
        // 成为分销商条件
        $distribut_condition = tpCache('distribut.condition');
        if ($distribut_condition == 1)  // 购买商品付款才可以成为分销商
            M('users')->where("user_id", $order['user_id'])->save(array('is_distribut' => 1));

        //用户支付, 发送短信给商家
        $res = checkEnableSendSms("4");
        if (!$res || $res['status'] != 1)
            return;

        $sender = tpCache("shop_info.mobile");
        if (empty($sender))
            return;
        $params = array('order_id' => $order['order_id']);
        sendSms("4", $sender, $params);
    }
}

/**
 * 添加订单日志
 * @param type $data
 * @param type $post
 */
function addorderlog($data, $post) {
    $db = new \Common\Umodel\LogOrderModel();
    return $db->addnew($data, $post);
}
