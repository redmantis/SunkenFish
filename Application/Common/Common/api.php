<?php

/**
 * 通用数据读取接口
 * @param type $data  传入参数
 * @param type $notreturnJson  返回結数据 黙认输出json格式
 * @param type $user  用户身份信息（用于安全校验）
 * @return type
 */
function common_data_readapi($action = "", $data = NULL, $notreturnJson = 1, $user = null) {
    $log = new Common\Umodel\LogOperationModel();
    $logdata = array();
    $logdata['usertype'] = 1;
    $loguserid = $data['userid'];
    $usermodel = $data['usermodel'];
    unset($data['usermodel']);
    switch ($usermodel) {
        case 'manage':
            $loguserid = $user['manage'];            
            $logdata['usertype'] = 2;
            break;
        case 'pcusers':
            $loguserid = $user['user']['id'];
            break;
        case 'mobileusers':
            $loguserid = $user['user']['id'];
            break;
    }
    
    $logdata['userid'] = $loguserid;
    $logdata['postdata'] = serialize($data);
    $logdata['log_action'] = $action;
    $logdata['log_id'] = $data['id'] ? $data['id'] : 0;

    //敏感词检测
    $rs = check_sensitive($data);
    if ($rs['status'] !== 0) {
        $rs['msg'] = showTagbyMark($rs['msg']);
        return $rs;
    }
    switch ($action) {
        case 'getaction'://获取控制器中方法列表
            $list = get_actionarray($data['ctrlname'],"Admin\\Controller\\");
            $l=array();
            foreach ($list as $k => $v) {
                $l[] = array('idvalue' => $v, 'idname' => $v);
            }
            $rs['list'] = $l;          
            $rs['status'] = 0;
            break;
        case "get_adlist"://获取广告           
            $db = new Common\Model\AdModel();
            $map = array();
            $map['isshow'] = 1;
            $map['position_id'] = $data['position_id'];
            $adrs = $db->getlist($map);
            $rs['count'] = count($adrs);
            $rs['list'] = $adrs;
            $rs['status'] = 1;
            break;
        case "get_newslist"://取得新闻列表
            $sid_id = $data['sid_id'];
            $size = $data['size'];
            $skip = get_firstrow($size, $data['skip']);
            $rs = getnewslistbycolid($sid_id, $data, $size, $skip);
            break;
        case "get_goodslist"://取得产品列表
            $sid_id = $data['sid_id'];
            $size = $data['size'];
            $skip = get_firstrow($size);
            $rs = getgoodslistbycatid($sid_id, $data, $size, $skip);
            break;
        case "userreg"://用户注册
            $db = new Common\Umodel\CusCustomerModel();
            $rs = $db->reg($data);
            $logdata['addlog'] = 1;
            break;
        case 'userbind'://第三方登录绑定
            $db = new \Common\Umodel\CusOauthModel();
            $rs = $db->binduser($data);
            break;
        case 'unbinduser'://解除绑定
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $db = new \Common\Umodel\CusOauthModel();
                $rs = $db->unbinduser($data);
            }
            break;
        case 'userbindreg'://注册绑定
            $db = new \Common\Umodel\CusOauthModel();
            $rs = $db->userbindreg($data);
            break;
        case 'getuserinfo':
            $rs = array('data' => $user, 'status' => 1);
            break;
        case "getTaglist"://取得标签列表
            $rs = getTaglist($data['tagmar'], $data['taglist']);
            break;
        case 'newshit'://新闻点击
            $id = checkcid($data['cid']);
            $db = new \Common\Model\NewsModel();
            $rs = $db->gethits($id['id'], $id['colid']);
            $rs['status'] = 1;
            break;
        case 'goodshit'://产品点击
            $id = checkcid($data['cid']);
            $db = new \Common\Gmodel\GoodsModel();
            $rs = $db->gethits($id['id'], $id['colid']);
            $rs['status'] = 1;
            break;
        case 'userlogin'://用户登录
            $db = new Common\Umodel\CusCustomerModel();
            $rs = $db->userlogin($data);
            $logdata['userid'] = $rs['userid'];
            $logdata['addlog'] = 1;
            break;
        case "forget"://找回密码           
            $map = array("username" => $data['username'], 'email' => $data['email']);
            $bconfig = getConfig('', $data['sid_id'], 'baseinfo');
            $db = new Common\Umodel\CusResetpsdModel();
            $rs = $db->creatmail($map, $bconfig);
            $logdata['addlog'] = 0;
            break;
        case 'checkmail':
            $rs = sendemail($data);
            break;
        case "leaving_message":
            $rs= leavingMessage($data);
            break;
        case 'checksms':
            $rs = sendsms($data);
            break;
        case 'resetpass'://重置密码
            $db = new Common\Umodel\CusResetpsdModel();
            $rs = $db->resetpass($data);
            $logdata['addlog'] = 1;
            break;
        case 'modifyuserinfo'://修改用户资料
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['id']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusCustomerModel();
                $rs = $db->saveinfo($data);
            }
            break;
        case 'changeemail'://修改用户绑定邮箱
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['id']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusCustomerModel();
                $rs = $db->changeMail($data);
            }
            break;
        case 'changemobile'://修改用户绑定手机
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['id']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusCustomerModel();
                $rs = $db->changemobile($data);
            }
            break;
        case 'crcak_shangjia'://实名信息输入
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusShangjiaModel();
                $rs = $db->update($data);
            }
            break;
        case 'setnewpassw'://设置新密码
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['id']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusCustomerModel();
                $rs = $db->chanagepwd($data);
            }
            break;

        case 'set_curent_city'://设置当前城市
            $db = new \Common\Gmodel\LbsRegionModel();
            $rs = $db->set_curentcity($data);
            break;
        case 'get_taglist'://取标签列表
            $rs = array('msg' => 'DataSearchSucc', 'status' => 1);
            $rs['data'] = getAttrsElementList($data['tagmark']);
            break;      
        case 'everyday_sign'://每日签到
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusScoreModel();
                $data['scoretype'] = 'sign_score';
                $data['objid'] = $data['userid'];
                $rs = $db->setscore($data);
            }
            break;
        case 'sign_info'://获取用户签到信息
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $db = new \Common\Umodel\CusCustomerModel();
                $rs = $db->get_signinfo($data);
            }
            break;
        case 'get_score_list'://积分变更日志
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $size = $data['size'];
                $skip = get_firstrow($size, $data['skip']);
                $db = new Common\Umodel\LogScoreModel();
                $rs = $db->getlist($data, $skip, $size);
            }
            break;
        case 'get_money_list'://余额变更日志
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $size = $data['size'];
                $skip = get_firstrow($size, $data['skip']);
                $db = new Common\ViewModel\LogMoneyViewModel();
                $rs = $db->getlist($data, $skip, $size);
            }
            break;
        case 'collect'://用户数据 收藏\评论\点赞\阅读     
            $logdata['log_id'] = $data['collectid'];
            switch ($data['collecttype']) {
                case CollectType_Like://点赞
                    if (!$data['showmod']) {
                        $logdata['addlog'] = 1;
                    }
                    $rs = crcakuserinfo($user, $data['userid']);
                    $msg = "";
                    break;
                case CollectType_Read://点击、阅读
                    $rs['status'] = 1;
                    break;
                case CollectType_Review://回复，评论
                    if (!$data['showmod']) {
                        $logdata['addlog'] = 1;
                    }
                    $rs = crcakuserinfo($user, $data['userid']);
                    break;
                case CollectType_Favorite://收藏
                    if (!$data['showmod']) {
                        $logdata['addlog'] = 1;
                    }
                    $rs = crcakuserinfo($user, $data['userid']);
                    $msg = "登录后才能收藏";
                    break;
            }
            if ($rs['status']) {
                $db = new \Common\Umodel\CusCollectModel();
                $rs = $db->collecting($data);
            } else {
                $rs['msg'] = $msg;
            }
            break;
        case 'callbackaddress'://逆地理编码取地理信息            
            $db = new Extend\LbsAddress($data);
            unset($data['sid_id']);
            return $db->callbackaddress($data);
            break;
        case "sortset"://通用排序字段更改
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status'] === 0) {
                $rs = sortset($data);
            }
            break;
        case "prompt"://操作日志弹出
            $rs = trancePrompt($data);
            break;
        case "commitprompt"://操作日志提交
            $rs = commitPrompt($data);
            break;
        case 'get_user_oauth'://用户第三方帐号绑定
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusOauthModel();
                $rs = $db->getuseroauth($data);
            }
            break;
        case 'identification'://登记实名信息
            $logdata['addlog'] = 1;
            $rs = crcakuserinfo($user, $data['userid']);
            if ($rs['status']) {
                $db = new Common\Umodel\CusRealnameModel();
                $rs = $db->authentication($data);
            }
            break;
        case 'get_identification':
            $db = new Common\Umodel\CusRealnameModel();
            $rs = $db->getauthent($data);
            break;
        case "nearest_subway"://最近地铁站 
            return nearest_subway($data);
            break;
        case 'regioncode'://读取行政区域编码
            $db = new \Common\Gmodel\LbsRegionModel();
            $rs = $db->getRegionCode($data);
            break;
        case 'regionlist'://读取行政区域列表（省市区联动）
            $db = new \Common\Gmodel\LbsRegionModel();
            $rs = $db->getRegionList($data);
            break;
        case 'get_citylist'://读取行政区域列表（省市区联动）
            $db = new \Common\Gmodel\LbsRegionModel();
            $rs = $db->getCitylist($data, 'city');
            break;
        case "get_regionidlist"://读取行政区域列表（省市区联动）
            $db = new \Common\Gmodel\LbsRegionModel();
            $rs = $db->getCitylist($data, 'region');
            break;
        case "get_citylistcount": //读取地区可租房源统计
            $db = new \Common\Gmodel\LbsRegionModel();
            $rs = $db->getCitylistCount($data);
            break;
        case 'get_streetlist'://读取街道列表
            $db = new \Common\Gmodel\LbsSubregionModel();
            $rs = $db->getSubRegionList($data);
            break;
        case 'get_streetcode'://读取街道编码
            $db = new \Common\Gmodel\LbsSubregionModel();
            $rs = $db->getbyname($data);
            break;
        
        case "get_siglepage"://取单页信息
            $dbn = new \Common\News\NewsSingleModel();
            $pagemodel = $dbn->getmodelbypath($data);
            $rs = formatResult($pagemodel);
            break;
        
        case "get_access_token":
            $db = new \Common\Umodel\CusAccessModel();
            $rs = $db->get_access_token($data);
            break;
        case "refresh_token":
            $db = new \Common\Umodel\CusAccessModel();
            $rs = $db->refresh_token($data);
            break;
        default:
            $rs = array('status' => 0, 'msg' => 'UndefinedCommand', 'data' => $data);
            break;
    }

    //将标签转为对应语种的文字内容

    if (isset($rs['msg'])) {
        $msg = $rs['msg'];
        if (is_array($msg)) {
            $message = "";
            $messagedetail = array();
            foreach ($msg as $k => $v) {
                $m = showTagbyMark($v);
                $message .="{$m}<br />";
                $messagedetail[$k] = $m;
            }
            $rs['msg'] = $message;
            $rs['msgdetail'] = $messagedetail;
        } else {
            $rs['msg'] = showTagbyMark($msg);
        }
    }

    $logdata['rsdata'] = serialize($rs);
    $logdata['rsstatus'] = $rs['status'];
    $log->addnew($logdata);

    if ($notreturnJson) {
        return $rs;
    } else {
        echo json_encode($rs);
    }
}


function formatResult($result){
    if($result){
        return ['status'=>0,'data'=>$result];
    }else{
         return ['status'=>20000013,'msg'=>"no_data"];
    }
}

/**
 * 验证用户身份信息
 * @param type $uinfo
 * @param type $uid
 * @param type $mod
 * @return string
 */
function crcakuserinfo($uinfo, $uid = 0) {
    $rs = array("status" => 20000007, 'msg' => "DisagreeUserinfo");
    if (empty($uid)) {
        $rs['uid'] = $uid;
        return $rs;
    }
    $mod = isset($uinfo['checktp']) ? $uinfo['checktp'] : 'u';
    switch ($mod) {
        case "u":
            $uuid = $uinfo['user']['id'];
            break;
        case 'm':
            $uuid = $uinfo['manage']['mid'];
            break;
        default :
            break;
    }
    if ($uuid == $uid) {
        $rs['status'] = 0;
        $rs['msg'] = '';
    }

    $rs['uid'] = $uid;
    $rs['uinfo'] = $uinfo;
    return $rs;
}

/**
 * 获取分页的起始记录
 * @param type $pagesize
 * @param type $skip
 * @return type
 */
function get_firstrow($pagesize, $skip = null) {
    if ($skip !== null) {
        return $skip;
    }
    $page = I("p", 1, intval) - 1;
    $first = 0;
    if ($page > 0) {
        $first = $page * $pagesize;
    }
    return $first;
}

/**
 * 获取用户ID
 * @return type
 */
function getuserinfo() {
    $mid = cookie(C('ALLIANCE.USER_LOGIN_KEY'));
    $mid = jp_decrypt($mid);
    $user = str_replace('rdm2017', '', $mid);
    $user = unserialize($user);
    $db = new \Common\Umodel\CusCustomerModel();
    $u = $db->getmodelbyid($user['id']);
    if ($u) {
        $user = $u;
    }
    return $user;
}

/**
 * 保存用户登录信息
 * @param type $mid
 */
function setuserinfo($user) {
    $info = serialize($user);
    $mid = jp_encrypt($info . 'rdm2017');
    cookie(C('ALLIANCE.USER_LOGIN_KEY'), $mid);
}

/**
 * 清除用户登录信息，退出登录
 */
function clearuserinfo() {
    cookie(C('ALLIANCE.USER_LOGIN_KEY'), null);
}

/**
 * 格式化显示用户资料
 * @param type $user
 * @param type $key
 * @return type
 */
function showuserinfo($user, $key = 'nickname') {
    switch ($key) {
        case "nickname":
            $val = empty($user[$key]) ? $user['username'] : $user[$key];
            break;
        case 'gender':
            $val = getkeyname($user[$key], 'gender');
            break;
        default :
            $val = $user[$key];
            break;
    }
    return $val;
}

/**
 * 服务器cookeid处理
 * @param type $data
 * @return string
 */
function cookieinfo($data) {
    $rs['status'] = 1;
    if (isset($data['value'])) {
        cookie($data['name'], $data['value']);
        $rs['msg'] = 'DataModifySuc';
    } else {
        $rs['data'] = cookie($data['name']);
        $rs['msg'] = 'DataSearchSucc';
    }
    return $rs;
}
