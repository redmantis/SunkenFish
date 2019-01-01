<?php

/*
 * UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2018-1-24 14:33:57
 * @Modify  2018-1-24 14:33:57
 * @CopyRight:  2018 by RDM
 */

namespace Org\Alipay;

require_once dirname(__FILE__) . '/AopSdk.php';

class Alipay {

    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm';
    protected $redirect_uri;
    //支付宝网关地址
    public $gateway_url = "https://openapi.alipay.com/gateway.do";
    //支付宝公钥
    public $alipay_public_key;
    //商户私钥
    public $private_key;
    //应用id
    public $appid = '2018012302043469';
    //编码格式
    public $charset = "UTF-8";
    public $token = NULL;
    //返回数据格式
    public $format = "json";
    //签名方式
    public $signtype = "RSA2";
    public $aopclent;
    public $aopversion = '1.0';
    public $callback;

    function __construct($alipay_config) {

        $this->GetRequestCodeURL=$alipay_config['requestcodeurl'];
        $this->gateway_url = $alipay_config['gatewayUrl'];
        $this->appid = $alipay_config['app_id'];
        $this->private_key = $alipay_config['merchant_private_key']; // '请填写开发者私钥去头去尾去回车，一行字符串';
        $this->alipay_public_key = $alipay_config['alipay_public_key']; // '请填写支付宝公钥，一行字符串';
        $this->callback = $alipay_config['redirect_uri'];
        $this->redirect_uri=$alipay_config['redirect_uri'];

        if (empty($this->appid) || trim($this->appid) == "") {
            throw new Exception("appid should not be NULL!");
        }
        if (empty($this->private_key) || trim($this->private_key) == "") {
            throw new Exception("private_key should not be NULL!");
        }
        if (empty($this->alipay_public_key) || trim($this->alipay_public_key) == "") {
            throw new Exception("alipay_public_key should not be NULL!");
        }

        if (empty($this->gateway_url) || trim($this->gateway_url) == "") {
            throw new Exception("gateway_url should not be NULL!");
        }

        $this->aopclent = new \AopClient();
        $this->aopclent->gatewayUrl = $this->gateway_url;
        $this->aopclent->appId = $this->appid;
        $this->aopclent->rsaPrivateKey = $this->private_key;
        $this->aopclent->alipayrsaPublicKey = $this->alipay_public_key;
        $this->aopclent->apiVersion = $this->aopversion;
        $this->aopclent->signType = $this->signtype;
        $this->aopclent->postCharset = $this->charset;
        $this->aopclent->format = $this->format;
    }

    public function getRequestCodeURL($redirect = '') {

        $redirect = empty($redirect) ? $this->redirect_uri : $redirect;
        //Oauth 标准参数
        $params = array(
            'app_id' => $this->appid,
            'scope' => 'auth_user',
            'redirect_uri' => $redirect,
        );
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }

    /**
     * 换取授权访问令牌
     * @param type $code
     * @param type $grant_type 值为authorization_code时，代表用code换取；值为refresh_token时，代表用refresh_token换取
     */
    public function getAccessToken($code, $grant_type) {
        $aop = $this->aopclent;
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType($grant_type);
        if ($grant_type === "authorization_code") {
            $request->setCode($code);
        } else {
            $request->setRefreshToken($code);
        }
        $result = $aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        return $result->$responseNode;
    }
    
    /**
     * 读取用户信息
     * @param type $token
     * @return type
     */
    public function getuserinfo($token) {
        $aop = $this->aopclent;
        $request = new \AlipayUserInfoShareRequest ();
        $result = $aop->execute($request, $token['access_token']);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $rs = $result->$responseNode;

        $resultCode = $rs->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $userInfo['type'] = 'ALI';
            $username = $rs->nick_name;
            if (empty($username)) {
                $username = $rs->user_id;
            }
            $userInfo['name'] = $username;
            $userInfo['nick'] = $username;
            $userInfo['head'] = $rs->avatar;
            $euserInfo = array();
            $euserInfo['province'] = $rs->province;
            $euserInfo['city'] = $rs->city;
            $euserInfo['is_student_certified'] = $rs->is_student_certified;
            $euserInfo['user_type'] = $rs->user_type;
            $euserInfo['user_status'] = $rs->user_status;
            $euserInfo['is_certified'] = $rs->is_certified;
            $euserInfo['user_id'] = $rs->user_id;
            $euserInfo['gender'] = $rs->gender;
            $userInfo['ext']=$euserInfo;
            return $userInfo;
        }
        else{
            return $resultCode;
        }
    }
    
    /**
     * 芝麻认证初始化
     */
    public function certi_init($bizContent, $returnUrl) {
        $aop = $this->aopclent;
        $request = new \ZhimaCustomerCertificationInitializeRequest ();
        $bizstr = json_encode($bizContent);
        $request->setBizContent($bizstr);
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $rs = $result->$responseNode;
        $resultCode = $rs->code;

        $msg = array("status" => 0, 'msg' => $rs->sub_msg);
        if (!empty($resultCode) && $resultCode == 10000) {
            $rq = new \ZhimaCustomerCertificationInitializeRequest();
            $rq->setBizContent("{\"biz_no\":\"{$rs->biz_no}\"}");
            $returnUrl .= U("/Ucent/Login/certify", array('biz_no' => $rs->biz_no));
            $rq->setReturnUrl($returnUrl);
            $url = $aop->pageExecute($rq, 'GET');
            $msg = array("status" => 1, 'url' => $url);
            $msg['biz_no'] = $rs->biz_no;
        }
        return $msg;
    }

    public function certi_query($biz_no) {
        $aop = $this->aopclent;
        $request = new \ZhimaCustomerCertificationQueryRequest ();
        $request->setBizContent("{\"biz_no\":\"{$biz_no}\"}");
        $result = $aop->execute($request);
        var_dump($result);
    }
}
