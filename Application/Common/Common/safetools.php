<?php

/**
 * 保存管理员登录信息
 * @param type $mid
 */
function setmanage($userinfo) {
    $mid = $userinfo['m_id'];
    $mid = jp_encrypt($mid . 'rdm9527muyu');
    $user = array("mid" => $mid, 'mtype' => 1, 'sid_id' => $userinfo['sid_id']);
    session(C('ALLIANCE.USER_AUTH_KEY'), $user);
//    cookie(C('ALLIANCE.USER_AUTH_KEY'), $user);
}

/**
 * 清除登录信息，退出登录
 */
function clearmange() {
    session(C('ALLIANCE.USER_AUTH_KEY'), null);
    cookie('isonline', null);
    cookie(C('ALLIANCE.USER_AUTH_KEY'), null);
    clearLangueInfo();
}

/**
 * 获取管理员ID
 * @return type
 */
function getmanage() {
    $user = session(C('ALLIANCE.USER_AUTH_KEY'));
//    $user = cookie(C('ALLIANCE.USER_AUTH_KEY'));
    $mid = $user['mid'];
    $mid = jp_decrypt($mid);
    $mid = str_replace('rdm9527muyu', '', $mid);
    $user['mid'] = $mid;
    return $user;
}

/**
 * Passport 加密函数
 * @param   $txt 等待加密的原字串
 * @param   $key 私有密匙(用于解密和加密)
 * @return       原字串经过私有密匙加密后的结果
 */
function passport_encrypt($txt, $key) {

    // 使用随机数发生器产生 0~32000 的值并 MD5()
    srand((double) microtime() * 1000000);
    $encrypt_key = md5(rand(0, 32000));

    // 变量初始化
    $ctr = 0;
    $tmp = '';

    // for 循环，$i 为从 0 开始，到小于 $txt 字串长度的整数
    for ($i = 0; $i < strlen($txt); $i++) {
        // 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        // $tmp 字串在末尾增加两位，其第一位内容为 $encrypt_key 的第 $ctr 位，
        // 第二位内容为 $txt 的第 $i 位与 $encrypt_key 的 $ctr 位取异或。然后 $ctr = $ctr + 1
        $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
    }

    // 返回结果，结果为 passport_key() 函数返回值的 base64 编码结果
    return base64_encode(passport_key($tmp, $key));
}

/**
 * Passport 解密函数
 * @param                string          加密后的字串
 * @param                string          私有密匙(用于解密和加密)
 * @return       string          字串经过私有密匙解密后的结果
 */
function passport_decrypt($txt, $key) {

    // $txt 的结果为加密后的字串经过 base64 解码，然后与私有密匙一起，
    // 经过 passport_key() 函数处理后的返回值
    $txt = passport_key(base64_decode($txt), $key);

    // 变量初始化
    $tmp = '';

    // for 循环，$i 为从 0 开始，到小于 $txt 字串长度的整数
    for ($i = 0; $i < strlen($txt); $i++) {
        // $tmp 字串在末尾增加一位，其内容为 $txt 的第 $i 位，
        // 与 $txt 的第 $i + 1 位取异或。然后 $i = $i + 1
        $tmp .= $txt[$i] ^ $txt[++$i];
    }

    // 返回 $tmp 的值作为结果
    return $tmp;
}

/**
 * Passport 密匙处理函数
 * @param                string          待加密或待解密的字串
 * @param                string          私有密匙(用于解密和加密)
 * @return       string          处理后的密匙
 */
function passport_key($txt, $encrypt_key) {

    // 将 $encrypt_key 赋为 $encrypt_key 经 md5() 后的值
    $encrypt_key = md5($encrypt_key);

    // 变量初始化
    $ctr = 0;
    $tmp = '';

    // for 循环，$i 为从 0 开始，到小于 $txt 字串长度的整数
    for ($i = 0; $i < strlen($txt); $i++) {
        // 如果 $ctr = $encrypt_key 的长度，则 $ctr 清零
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        // $tmp 字串在末尾增加一位，其内容为 $txt 的第 $i 位，
        // 与 $encrypt_key 的第 $ctr + 1 位取异或。然后 $ctr = $ctr + 1
        $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
    }

    // 返回 $tmp 的值作为结果
    return $tmp;
}

/**
 * Passport 信息(数组)编码函数
 * @param                array           待编码的数组
 * @return       string          数组经编码后的字串
 */
function passport_encode($array) {

    // 数组变量初始化
    $arrayenc = array();

    // 遍历数组 $array，其中 $key 为当前元素的下标，$val 为其对应的值
    foreach ($array as $key => $val) {
        // $arrayenc 数组增加一个元素，其内容为 "$key=经过 urlencode() 后的 $val 值"
        $arrayenc[] = $key . '=' . urlencode($val);
    }

    // 返回以 "&" 连接的 $arrayenc 的值(implode)，例如 $arrayenc = array('aa', 'bb', 'cc', 'dd')，
    // 则 implode('&', $arrayenc) 后的结果为 ”aa&bb&cc&dd"
    return implode('&', $arrayenc);
}

function testenable_writing() {
    $rs = ['msg' => '', 'status' => 0];
    if (C("ENABLE_READONLY")) {
         $rs = ['msg' => 'readonly_model', 'status' => 20000011];
    }    
    return $rs;
}
