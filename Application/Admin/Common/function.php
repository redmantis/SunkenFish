<?php

/**
 * 设置主题
 * @param type $theme
 */
function set_theme($theme = '') {

    //判断是否存在设置的模板主题
    if (empty($theme)) {
        $theme = C('DEFAULT_THEME');      
    } 
    //替换COMMON模块中设置的模板值    
    if (C('Current_Theme')) {
        C('TMPL_PARSE_STRING', str_replace(C('Current_Theme'), $theme, C('TMPL_PARSE_STRING')));
    } else {       
        C('TMPL_PARSE_STRING', str_replace("DEFAULT_THEME", $theme, C('TMPL_PARSE_STRING')));
    }
    C('Current_Theme', $theme);
    C('DEFAULT_THEME', $theme);
}

/**
 * 帐号安全登录
 * @param type $user
 * @param type $qian  检测用户登录标识  true  正常登入  false  强制登入
 * @return type
 */
function usersafelogin($user, $qian = true) {
    $isonline=cookie('isonline');
    if(empty($isonline)){
        $isonline= mt_rand(1000, 999999999);
        $expire=C('SESSIONEXPIRE');
        cookie('isonline',$isonline,$expire);
    }
    
    if(!$user){
        return array('sucess'=>1,'msg'=>'没有用户资料');
    }
    else{         
        $db=new Common\Model\SaferuleModel();
        $model=$db->getmodelbyid($user['m_id']);
        if ($qian) {
            if ($model['isonline']) {
                if ($model['isonline'] == $isonline) {
                    return array('sucess' => 0, 'msg' => '');
                } else {
                    return array('sucess' => 2, 'msg' => '该帐号已在其他地方登陆，是否强制登陆');
                }
            }
        }
        $model['useip']=  get_client_ip();
        $model['lastvis']=  time();
        $model['isonline']=  $isonline; 
        $db->savemode($model);
         return array('sucess'=>0,'msg'=>'');
    }
}

/**
 * 更新由用户自定义的配置参数 
 * 需要删除runtime 文件夹才会生效
 * @param type $sysconfig
 */
function creatconfig($sysconfig) {
    
    if ((C('CRON_WAITIME') != $sysconfig['onlinereflash']) | (C('SESSION_OPTIONS.expire') != $sysconfig['passsavetime'])) {//如果参类被修改，重写config文件：customset.php

        $config_html = "<?php
return array(
    'CRON_WAITIME'=>{$sysconfig['onlinereflash']},
     'SESSIONEXPIRE'              =>  {$sysconfig['passsavetime']},
    'CRON_CONFIG_ON' => true, // 是否开启自动运行
    'CRON_CONFIG' => array(   
        '在线列表刷新' => array('Manage/Login/crons', '{$sysconfig['onlinereflash']}', ''), //路径(格式同R)、间隔秒（0为一直运行）、指定一个开始时间  
        '标记活动用户' => array('Manage/Login/active', '5', ''), //路径(格式同R)、间隔秒（0为一直运行）、指定一个开始时间   
    ),  
    
    'SESSION_OPTIONS'         =>  array(
        'name'                =>  'RDMSESSION',                 
        'expire'              =>  {$sysconfig['passsavetime']},
        'use_trans_sid'       =>  1,                    
        'use_only_cookies'    =>  0,
    ),
);";
        file_put_contents("./Application/Common/Conf/customset.php", $config_html);       
        clear_runtime();                  
    }
}


/**
 * 验证密码
 * @param type $pattern
 * @param type $pwd
 * @return type
 */
function checkpwd($pwd,$pattern='\A(?=\S*?[A-Z])(?=\S*?[a-z])(?=\S*?[0-9])\S{6,}\z') {
//    $pattern = stripcslashes($pattern);
    return preg_match("/{$pattern}/", $pwd);
}

/**
 * 返回查询条件（根据用户组显示列表内容）
 * @param type $uid  用户ID
 * @param type $sid_id　用户所属站点
 * @return type
 */
function getselectmap($uid, $sid_id) {
    $map['sid_id'] = $sid_id;
    return $map;
}

/**
 * 清除指定目录下的文件
 * @param type $dir
 */
function deldir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
}

/**
 * 查看权限
 * @param type $checkrule　　根据规则验证的权限
 * @param type $uid   当前用户ID
 * @return boolean
 */
function checkPower($checkrule, $uid, $utype) {

    if ($uid == C('AUTH_CONFIG.AUTH_SUPERMAN'))
        return true; //系统超级管理员怱略权限控制
    if ($utype == 1)
        return true; //网站超级管理员怱略权限控制
    return $checkrule;
}


/**
 * 生成HTML控件
 * @param type $model
 * @param type $name
 * @param type $selected
 * @param type $map
 * @param type $cssclass
 * @return type
 */
function show_list($model, $name, $ishidden = false, $selected = '', $map = '', $cssclass = 'layui-input') {
    return htmlhelper::make_htmlcontorl($model, $name, $ishidden, $selected, $map, $cssclass);
}