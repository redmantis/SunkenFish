<?php
/**
 * Ajax提交时，页面token重置
 * 前端脚本配合
 */
function gettoken() {
//    if (IS_AJAX) {
        $tokenName = C('TOKEN_NAME', null, '__hash__');
        $tokenType = C('TOKEN_TYPE', null, 'md5');
        if (!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = array();
        }
        // 标识当前页面唯一性
        $tokenKey = md5($_SERVER['REQUEST_URI']);
        if (isset($_SESSION[$tokenName][$tokenKey])) {// 相同页面不重复生成session
            $tokenValue = $_SESSION[$tokenName][$tokenKey];
        } else {
            $tokenValue = is_callable($tokenType) ? $tokenType(microtime(true)) : md5(microtime(true));
            $_SESSION[$tokenName][$tokenKey] = $tokenValue;
        }
        header($tokenName . ': ' . $tokenKey . '_' . $tokenValue); //ajax需要获得这个header并替换页面中meta中的token值
//    }
}

function tranceIsshow($isshow) {
    if ($isshow && (!is_array($isshow))) {
        $isshow = array('in', explode(',', $isshow));
    }
    return $isshow;
}

/**
 * 生成随机字符串
 * @param type $codelen  长度
 * @param type $charset  可用字符
 * @return type string
 */
function createRandCode($codelen, $charset = 'abcdefghkmnprstuvwxyz23456789') {
    $_len = strlen($charset) - 1;
    for ($i = 0; $i < $codelen; $i++) {
        $code .= $charset[mt_rand(0, $_len)];
    }
    return $code;
}

/**
 * 密码加密
 * @param type $pass  原密码
 * @param type $salt  杂凑
 */
function creatpassword($pass, $salt) {
    $_pass = md5(md5($pass) . $salt);
    return $_pass;
}

/**
 * 测试IP是否合法
 * @param type $str
 * @return boolean
 */
function is_ip($str) {
    $ip = explode('.', $str);
    for ($i = 0; $i < count($ip); $i++) {
        if ($ip[$i] > 255) {
            return false;
        }
    }
    return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
}

/**
 * 获取post数据，并对空字符串进行处理，以适应orcal字符串字段不能插入的问题
 * @param type $CheckToken  表单验证
 * @return string
 */
function getpost($CheckToken = FALSE, $filter = NULL) {
    if (IS_POST) {
        if ($filter == null) {
            $data = I('post.');
        } else {
            $data = I('post.', null, $filter);
        }
        if ($CheckToken) {
            $db = new \Think\Model();
            if (!$db->autoCheckToken($data)) {

                if (IS_AJAX) {
                    $rs['data'] = $data;
                    $rs['msg'] = "_TOKEN_ERROR_";
                    $data = trancemessage($rs);
                    exit(json_encode($data));
                } else {
                    throw_exception('表单验证失败！');
                }
                exit();
            }
            unset($data[C('TOKEN_NAME')]);
        }
        unset($data['file']);      
        return $data;
    }
}

/**
 * 读取多语言版数据
 * @param type $CheckToken
 * @param type $filter
 * @return type
 */
function getLangPost($CheckToken = FALSE, $filter = NULL) {
    $data = getpost($CheckToken, $filter);
    $model = array();
    foreach ($data as $k => $v) {
        $lang = strstr($k, '__', true);
        if (!$lang) {
            $lang = "base";
            $key = $k;
        } else {
            $key = str_replace($lang . '__', '', $k);
        }
        $model[$lang][$key] = $v;
    }
    return $model;
}

/**
 * 多语言版数据转换
 * @param type $base
 * @param type $dl
 * @return type
 */
function tranceLangModel($base, $dl, $selectlangue = "") {
    $model = array();
    $model['base'] = $base;
    foreach ($dl as $k => $v) {
        $lang = $v['lang'];
        unset($v['lang']);
        unset($v['extid']);
        $model[$lang] = $v;
    }

    $langinfo = getLangueInfo($selectlangue);
    $extmodel = mergLangModel($model[$langinfo['curent_lang']], $model[$langinfo['default_lang']]);
    $all = array_merge($model['base'], $extmodel);
    $model['all'] = $all;
    return $model;
}

/**
 * 合并当前语言和黙认语言，当前语言性属为空时，以黙认版本填充（列表）
 * @param type $curent
 * @param type $default
 */
function mergLangArray($curent,$default){
    $da=array();
    foreach ($default as $k=>$v){
        $da[$v['id']]=$v;
    }    
     
    $dl=array();
    foreach ($curent as $k=>$v){
        $dl[]=  mergLangModel($v,$da[$v['id']]);
    }
    return $dl;
}

/**
 * 合并当前语言和黙认语言，当前语言性属为空时，以黙认版本填充（单个实例）
 * @param type $curent
 * @param type $default
 */
function mergLangModel($curent, $default) {
    if (!$default) {
        return $curent;
    }
    foreach ($curent as $k => $v) {
        $v=  trim($v);
        if(empty($v)){
            $curent[$k]=$default[$k];
        }
    }
    return $curent;
}

/**
 * 测试 id 是否包含在 rules
 * @param type $id
 * @param type $rules
 * @return string
 */
function crackin($id, $rules) {
    $rules2 = str_replace('|', ',', $rules);
    if (in_array($id, explode(",", $rules2))) {
        return 'checked';
    } else {
        return '';
    }
}

//通用排序操作
function cat_sort($name = '', $idname) {
    //如果单击了排序
    $pst = I('post.');
    if (isset($pst['submit'])) {
        unset($pst['submit']);
    }
    $db = M($name);
    foreach ($pst as $key => $v) {
        $where = array($idname => $key);
        $db->where($where)->setField(array('sortid' => $v));
    }
    return $name;
}

/**
 * 将后配友情链接分类设置组装成数组
 */
function getlinkcat($linkcat) {
    $la = LinefeedToArray($linkcat); 
    foreach ($la as $v) {
        if (!empty($v)) {
            $idd = explode('|', trim($v));
            $dblist[] = array('idvalue' => $idd[0], 'idname' => $idd[1]);
        }
    }
    return $dblist;
}

/**
 * 将一组配置参数组装成键值对数组
 * 每行一对
 * @param type $linkcat
 * 键|值
 * 键|值
 * 键|值
 * @return type
 */
function getlinkmap($linkcat) {
    $la = LinefeedToArray($linkcat); 
    $dblist=array();
    foreach ($la as $v) {
        if (!empty($v)) {
            $idd = explode('|', trim($v));
            $key=trim($idd[0]);
            $value=  trim($idd[1]);
            $dblist[$key] = $value;
        }
    }
    return $dblist;
}

/**
 * 将字符串按换行符分割
 * @param type $line
 * @return type
 */
function LinefeedToArray($line) {
     //将分行符"\r\n"转义成HTML的换行符"<br />"
    $lktp = nl2br($line); 
    //"<br />"作为分隔切成数组
    $la = explode("<br />", $lktp);
    $newla=array();
    foreach ($la as $v) {
        $v = trim($v);
        if (!empty($v)) {
            $newla[] = $v;
        }
    }
    return $newla;
}

function br2nl($text){
    $text=preg_replace('/<br\\s*?\/??>/i',chr(13),$text);
    return preg_replace('/ /i',' ',$text);
}

function checklinkcat($linkcat) {
    $la = LinefeedToArray($linkcat); 
    foreach ($la as $v) {
        if (!empty($v)) {
            $idd = explode('|', trim($v));
            if ((!is_number($idd[0])) | (!is_text($idd[1]))) {
                return false;
            }
        }
    }
    return true;
}

function is_number($val) {
    return is_numeric($val);
//    if (empty($val)) {
//        $val = 0;
//    }
//    return preg_match("/^[0-9]+$/", $val);
}

function is_text($val) {
    if (preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $val)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 返回链接类型
 * @param type $lkid
 * @param type $linkcat
 * @return type
 */
function viewlnktyp($lkid, $linkcat) {
    $map = getlinkcat($linkcat);
    foreach ($map as $v) {
        if ($v['idvalue'] == $lkid)
            return $v['idname'];
    }
}

//返回数据键名
//@value 数组键值
//@$pname 数组名称
function viewkeyname($value, $pname) {
    $a = array_keys(C($pname), $value);
    return $a[0];
}

/**
 * 　获取操作类型ID
 * @param type $opstr
 * @param type $checkid  检测ID
 * @return type
 */
function getlogtype($opstr,$checkid=1) {
    if ($checkid) {
        $menuid = I('get.menuid');
    }
    $authrule = getruls();
    if ($menuid) {
        $op = $authrule[$menuid];
        if ($op) {
            $data['opstr'] = $op['title'];
            $data['op'] = $op['id'];
            $data['parentid'] = $op['parentid'];
            return $data;
        }
    }
    $str = strtolower($opstr); 
    foreach ($authrule as $k => $v) {
        $x = strtolower($v['name']);
        if ($x == $str) {
            $data['opstr'] = $v['title'];
            $data['op'] = $v['id'];
            $data['parentid'] = $v['parentid'];
            return $data;
        }
    }
    $data['opstr'] = "未知操作({$str})";
    $data['op'] = 0;
    return $data;
}

/**
 * 返回日志类型
 * @param type $tp
 * @return type
 */
function getlogtypename($tp) {
    $authrule = getruls();
    foreach ($authrule as $k => $v) {
        if ($tp == $v['id']) {
            return $v['title'];
        }
    }
}

/**
 * 获取权限明细
 * @return type
 */
function getruls() {
    $lang = getLangueInfo();
    $catchpath = "lancatch/{$lang['sid_id']}/authrule/{$lang['curent_lang']}";
    $authrule = F($catchpath);
    if (!$authrule) {
        $db = new \Common\Model\AuthRuleModel();
        $authrule = $db->rewrit();
    }
    return $authrule;
}

/**
 * 右上角快捷导航工具
 * @param type $data
 * @return type
 */
function showtoprighttools($data) {
    $str = '';
    foreach ($data as $v) {
        $url = U($v['actionpath'], $v['prame']);
        $op = getlogtype($v['actionpath']);
        $text = $op['opstr'];
        $str .=" <td  class=\"{$v['active']}\"><a href=\"{$url}\" class=\"view\">{$text}</a></td>";
    }
    return $str;
}

/**
 * 
 * @param type $info
 */
function trancloginfo($info) {
    /** $info['status']          //数字状态
     * $info['statusstr']       //文本状态
     * $info['op']              //数字操作
     * $info['opstr']           //文本操作
     * $info['opcontent']       //操作内容
     * $info['objectid']        //操作对象ID
     * $info['objecttable']     //操作对象表
     */
    $confg = unserialize($info);
    return "命令：{$confg['opstr']}操作：{$confg['opcontent']}，表：{$confg['objecttable']}，ID：{$confg['objectid']}";
}

/**
 * 取得模版名称
 * @param type $viewmodel
 * @param type $viewname
 * @return type
 */
function gettemplate($viewmodel, $viewname) {
    switch ($viewmodel) {
        case 0:
            $rs['isdefault'] = 1;
            $rs['default'] = $viewname;
            break;
        case -1:
            $rs['isdefault'] = -1;
            break;
        default :
            $rs['isdefault'] = 0;
            $rs['tpid'] = $viewmodel; //模板ID
            $rs['template'] = $viewname . $viewmodel; //当前模板
            $rs['default'] = $viewname; //默认模板（当 tpid 不存在时有效）
            break;
    }
    return $rs;
}

/**
 * 当子栏目未设置或设置为默认时，读取上缓栏目的配置
 * @param type $colums
 * @param type $key
 * @return type
 */
function getdefaultval($colums,  $key) {  
    $db = new Common\Model\SubColumnsModel();
    return $db->getdefaultvaue($colums['id'], $key);
}

/**
 * 根据control  action 获取当前模板
 * @param type $contorl
 * @param type $action
 * @param type $colmodel
 */
function gettpl($contorl, $action, $colmodel) {
    switch ($contorl) {
        case 'Index':
            switch ($action) {
                case 'index':
                    $curentv = getdefaultval($colmodel,  'homepage');
                    $template = gettemplate($curentv, 'index');
                    break;
                case 'page':
                    $curentv = getdefaultval($colmodel,  'homepage');
                    $template = gettemplate($curentv, $colmodel['viewpath']);
                    break;
                case 'test':
                    $template['isdefault'] = 1;
                    $template['default'] = '';
                    break;
                case 'pinlun':
                    $template['isdefault'] = 1;
                    $template['default'] = '';
                    break;
                default :
                    $template = gettemplate(-1, '');
                    break;
            }
            break;
        case 'Lis':
            switch ($action) {
                case 'nlist'://栏目列表
                    $curentv = getdefaultval($colmodel,  'listmod');
                    $template = gettemplate($curentv, 'nlist');
                    break;
                case 'ajaxlist'://ajax列表
                    $curentv = getdefaultval($colmodel,  'listmod');
                    $template = gettemplate($curentv, 'ajaxlist');
                    break;
                case 'index'://栏目主页
                    $curentv = getdefaultval($colmodel,  'homepage');
                    $template = gettemplate($curentv, 'index');
                    break;
                case 'search'://搜索模板
                    $template = gettemplate(0, 'search');
                    break;
                default :
                    $template = gettemplate(-1, '');
                    break;
            }
            break;
        case 'Detail':
            switch ($action) {
                case 'detail':
                    $curentv = getdefaultval($colmodel,  'pagemod');
                    $template = gettemplate($curentv, 'detail');
                    break;
                case 'vdetail':
                    $curentv = getdefaultval($colmodel,  'pagemod');
                    $template = gettemplate($curentv, 'vdetail');
                    break;
                default :
                    $template = gettemplate(-1, '');
                    break;
            }
            break;
        default :
            $template = gettemplate(-1, '');
            break;
    }
    return $template;
}

/**
 * 判断原有模板并决定是否更新
 * @param type $thememod
 * @param type $path
 * @param type $tpl
 */
function creattpl($thememod, $path, $tpl) {
    $newtpl = $thememod['modifytime2'];
    $tplpath = C('TMPL_PARSE_STRING.__Theme__') . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $tpl . C('TMPL_TEMPLATE_SUFFIX');
    if (file_exists($tplpath)) {
        $a = filemtime($tplpath);
        if ($newtpl > $a) {//检查模板是否已经过时
            creathtml($tplpath, $thememod['content']);
        }
    } else {
        creathtml($tplpath, $thememod['content']);
    }
}

/**
 * 创建模板文件
 * @param type $path
 * @param type $content
 */
function creathtml($path, $content) {
    $root = $_SERVER['DOCUMENT_ROOT'];
    file_put_contents($root . DIRECTORY_SEPARATOR . $path, $content);
}

/**
 * 根据id 取得站点域名（缓存） 用于预览
 * @param type $sid_id
 * @return type
 */
function showsidurl($sid_id) {
    $sid_id = intval($sid_id);
    $sub = new Common\Model\SubsidiaryModel();
    $model = $sub->getmodebyid($sid_id);
    $url = explode(',', $model['sid_sitname']);
    return 'http://' . $url[0];
}

/**
 * 关键字组合
 * @param type $glue
 * @param type $pieces
 */
function combinationkeyword($glue, $pieces) {
    $v = implode($glue, $pieces);
    return $v;
}

/**
 *   实现中文字串截取无乱码的方法
 */
function getSubstr($string, $length, $start = 0) {
    if (mb_strlen($string, 'utf-8') > $length) {
        $str = mb_substr($string, $start, $length, 'utf-8');
        return $str . '…';
    } else {
        return $string;
    }
}

/**
 * 生成商品详情
 * @param type $model
 */
function productdetail($model) {
    if(!$model){
        return "#";
    }
    $pram = get_langue_parm();
    $pram['id'] = $model['id'] . 'n' . $model['cat_id'];
    $x = U('product/detail', $pram);
    return $x;
}


/**
 * 生成客房详情
 * @param type $model
 * @param type $modname  模块名称
 * @return string
 */
function roomsdetail($model, $modname = "") {
    if (!$model) {
        return "#";
    }
    $pram = get_langue_parm();
    if (is_array($model)) {
        $roomid = $model['id'];
    } else {
        $roomid = $model;
    }
    $pram['id'] = $roomid;
    $x = U($modname . 'rooms/roominfo', $pram);
    return $x;
}

/**
 * 生成楼宇详情
 * @param type $model
 */
function build_detail($model, $modname = "") {
    if (!$model) {
        return "#";
    }
    $pram = get_langue_parm();
    if (!is_array($model)) {
        $db = new \Common\Zfmodel\ZfBuildingsModel();
        $model = $db->getmodelbyid($model);
    }
    if ($model['vip'] > 0 && $model['mubanid'] > 0) {
        $x = U('/v/' . $model['id'], $pram);
    } else {
        $pram['id'] = $model['id'];
        $x = U($modname . 'Building/buildinginfo', $pram);
    }
    return $x;
}

/**
 * 生成楼盘详情
 * @param type $model
 */
function loupan_detail($model, $modname = "") {
    if (!$model) {
        return "#";
    }
    $pram = get_langue_parm();
    $pram['id'] = $model['id'];
    $x = U($modname . 'loupan/loupaninfo', $pram);
    return $x;
}

/**
 * 生成行政区划链接
 * @param type $model
 */
function region_index($model, $keyname = 'gbcode') {
    if (!$model) {
        return "#";
    }
    $pram = get_langue_parm();
    $curcity = get_curcity();
    if ($curcity['cgb'] != $model[$keyname]) {
        $pram['para'] = replay_para('regioncode', $model[$keyname]);
    }

    $x = U('index', $pram);
    return $x;
}

/**
 * 生成行政区划链接
 * @param type $model
 */
function subregion_index($model) {
    if (!$model) {
        return "#";
    }
    $pram = get_langue_parm();
    $pram['para'] = replay_para('streetcode', $model['areacode']);  // "ac" . $model['areacode'];
    $x = U('index', $pram);
    return $x;
}


/**
 * 生成查询参数链接
 * @param type $code  参数值
 * @param type $key   参数名称
 * @param type $control  控制器名称
 * @param type $parastr  参数字串
 * @param type $exp  忽略参数
 * @return type
 */
function para_index($code, $key, $control = '',$parastr='',$exp=[]) {
    $pram = get_langue_parm();
    $pram['para'] = replay_para($key, $code,$parastr,$exp);  // "ac" . $model['areacode'];
    $x = U($control . 'index', $pram);
    return $x;
}

/**
 * 传入参数传递加工
 * @param type $key  参数名
 * @param type $code 参数值
 * @param type $parastr  参数字符串
 * @param type $exp  忽略参数
 */
function replay_para($key = '', $code = '', $parastr = '',$exp=[]) {
    $map = explain_para($parastr);
    foreach ($exp as $ek){
        unset($map[$ek]);   
    }
    if ($key !== 'lp') {
        unset($map['lp']);
    }
    if (!empty($key)) {
        switch ($key) {
            case 'regioncode'://市区级链接  清除乡镇数据
                $map[$key] = $code;
                unset($map['streetcode']);
                $curcity = get_curcity();
                if ($curcity['cgb'] == $code){
                    unset($map['regioncode']);
                }
                break;
            case 'kf'://独立看房
            case 'yt'://阳台
            case 'tl'://卫生间
            case 'me'://近地铁
                if (isset($map[$key])) {
                    unset($map[$key]);
                } else {
                    $map[$key] = $code;
                }
                break;
            default :
                $map[$key] = $code;
                break;
        }
    }
    $para = "";
    if (isset($map['regioncode'])) {//市区
        $para = "gb" . $map['regioncode'];
    }
    if (isset($map['streetcode'])) {//乡镇
        $para = "ac" . $map['streetcode'];
    }

    $para = mosaic_para($map, 'price', $para);//价格
    $para = mosaic_para($map, 'area', $para);//面积
    $para = mosaic_para($map, 'rm', $para);//客房数量
    $para = mosaic_para($map, 'rf', $para);//朝向
    $para = mosaic_para($map, 'rt', $para);//出租方式
    $para = mosaic_para($map, 'fl', $para);//楼层选择
    $para = mosaic_para($map, 'zx', $para);//装修
    $para = mosaic_para($map, 'me', $para);//地铁
    $para = mosaic_para($map, 'kf', $para);//随时看房
    $para = mosaic_para($map, 'yt', $para);//独立阳台
    $para = mosaic_para($map, 'tl', $para);//独立卫生间
    $para = mosaic_para($map, 'st', $para);//排序参数
    $para = mosaic_para($map, 'lp', $para);//楼盘ID
    $para = mosaic_para($map, 'wy', $para);//物业

    return $para;
}

/**
 * 拼接参数字符吕
 * @param type $map
 * @param type $ky
 * @param string $para
 * @return string
 */
function mosaic_para($map,$ky,$para){
    if (!empty($map[$ky])) {//楼层选择
        $para .= $ky . $map[$ky];
    }
    return $para;
}

/**
 * 输入参数解析
 * 解决伪静态路径过深问题
 * @param type $parastr
 */
function explain_para($parastr = '') {
    if (empty($parastr)) {
        $parastr = I("get.para", '');
    }
    
    $map = array();
    $matches = array();
    $pattern = "/(gb)(\d*)/is";  //行政区域编吗
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['regioncode'] = $matches[2];
    }
    
    $pattern = "/(ac)(\d*)/is"; //街道编码
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $streetcode = $matches[2];
        $map['regioncode'] = floor($streetcode / 100);
        $map['streetcode'] = $matches[2];
    }

    $pattern = "/(price)(\d*)-(\d*)/is"; //价格
    preg_match($pattern, $parastr, $matches);
    if ($matches) {    
        $map['price'] = $matches[2]."-".$matches[3];
    }
    
    $pattern = "/(area)(\d*)-(\d*)/is"; //面积
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['area'] = $matches[2] . "-" . $matches[3];
    }

    $pattern = "/(rf)(\d*)/is"; //朝向
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['rf'] = $matches[2];
    }
    
    $pattern = "/(rm)(\d*)/is"; //房间数
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['rm'] = $matches[2];
    }
    
    $pattern = "/(rt)(\d*)/is"; //出租方式
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['rt'] = $matches[2];
    }

   $pattern = "/(fl)(\d*)-(\d*)/is"; //楼层
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['fl'] = $matches[2] . "-" . $matches[3];
    }
    
    $pattern = "/(zx)(\d*)/is"; //装修
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['zx'] = $matches[2];
    }
    
    $pattern = "/(me)(\d*)/is"; //地铁
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['me'] = $matches[2];
    }
    
    $pattern = "/(kf)(\d*)/is"; //独立看房
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['kf'] = $matches[2];
    }

    $pattern = "/(yt)(\d*)/is"; //阳台
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['yt'] = $matches[2];
    }

    $pattern = "/(tl)(\d*)/is"; //卫生间
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['tl'] = $matches[2];
    }
    
    $pattern = "/(st)(\d*)/is"; //排序
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['st'] = $matches[2];
    }  
    
    $pattern = "/(lp)(\d*)/is"; //卫生间
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['lp'] = $matches[2];
    }   
    
    $pattern = "/(wy)(\d*)/is"; //物业
    preg_match($pattern, $parastr, $matches);
    if ($matches) {
        $map['wy'] = $matches[2];
    }  

    return $map;
    
    
}

/**
 * 解析单个传入数据
 * @param type $vstr
 */
function explain_single_para($parastr) {
    $pattern = "/^(\d*)-(\d*)$/is"; //楼层
    preg_match($pattern, $parastr, $matches);     
    if ($matches) {
        $min = $matches[1];
        $max = $matches[2];
        if (!empty($min) && !empty($max)) {
            $rs = array('between', array($min, $max));
        } elseif (empty($min)) {
            $rs = array('lt', $max);
        } else {
            $rs = array('gt', $min);
        }
    } else {
        $pattern = "/^(\d*)/is"; //卫生间
        preg_match($pattern, $parastr, $matches);
        if ($matches) {
            $rs = $matches[1];
        }
    }
    return $rs;
}

/**
 * 生成带当前语言参数的访问地址
 * @param type $url
 * @param type $map
 * @return type
 */
function creat_url_lan($url = "search", $data = []) {
    $pram = get_langue_parm();
    $pram = array_merge($pram, $data);
    $urlx = explode("#", $url);
    $x = U($urlx[0], $pram);
    if (count($urlx) > 1) {
        $x .= "#" . $urlx[1];
    }
    return $x;
}

/**
 * 生成详情页面URL
 * @param type $model
 */
function makeurl($model, $showfile = 1) {
    if(empty($model)){
        return "#";
    }
//        return $model['pic'];
    $e = trim($model['exturl']);
    if (!empty($e)) {//填写外部链接时，跳转到外部链接
        $x = $e;
    } else {
        $e = trim($model['pic']);
        if ($showfile && !checknotisfile($e)) {
            $ext = strtolower(pathinfo($e, PATHINFO_EXTENSION));
            if (!crackin($ext, 'jpg,gif,png,jpeg,wmv,avi,mp4')) {//附件不是图片时，跳转到附件链接
                return viewfile($e);
            }
        }
        $pram = get_langue_parm();
        $pram['cid'] = "{$model['id']}n{$model['colid']}";
        $x = U('news/detail', $pram);
    }
    return $x;
}

/**
 * 生成预览详情页面URL
 * @param type $model
 */
function makeviewurl($model, $showfile = 1) {
    $e = trim($model['exturl']);
    if (!empty($e)) {//填写外部链接时，跳转到外部链接
        $x = $e;
    } else {
        $e = trim($model['pic']);
        if (empty(trim($model['content'])))
            $showfile = 1;
        if ($showfile && !empty($e)) {
            $ext = strtolower(pathinfo($e, PATHINFO_EXTENSION));
            if (!crackin($ext, 'jpg,gif,png,jpeg,wmv,avi,mp4')) {//附件不是图片时，跳转到附件链接
                return viewfile($e);
            }
        }
        $site = cookie(C('ALLIANCE.MYSITEURL'));
        $x = U('/detail/vdetail', array('cid' => $model['id'] . 'n' . $model['colid']));
        $x = $site . $x;
    }
    return $x;
}

/**
 * 生成下载链接
 * @param type $model
 */
function makedownload($model, $id = 0, $colid = 0) {
    //  return $colid;
    $e = trim($model['exturl']);
    if (!empty($e))//填写外部链接时，跳转到外部链接
        $x = $e;
    else
        $x = viewfile($model['pic']);
    return $x;
}

/**
 * 读取excle表格
 * @param type $filename
 */
function exceltoarray($filename) {
    //创建PHPExcel对象，注意，不能少了\
    import("Org.Util.PHPExcel");
    $PHPExcel = new \PHPExcel();
    //如果excel文件后缀名为.xls，导入这个类
    import("Org.Util.PHPExcel.Reader.Excel5");
    //如果excel文件后缀名为.xlsx，导入这下类
    //import("Org.Util.PHPExcel.Reader.Excel2007");
    //$PHPReader=new \PHPExcel_Reader_Excel2007();        

    $PHPReader = new \PHPExcel_Reader_Excel5();
    //载入文件
    $PHPExcel = $PHPReader->load($filename);
    //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
    $currentSheet = $PHPExcel->getSheet(0);
    //获取总列数
    $allColumn = $currentSheet->getHighestColumn();
    //获取总行数
    $allRow = $currentSheet->getHighestRow();
    //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始

    for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
        //从哪列开始，A表示第一列
        for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
            //数据坐标
            $address = $currentColumn . $currentRow;
            $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
        }
    }
    return $arr;
}

/**
 * 读取excle表格，带字段名称
 * @param type $filename
 */
function exceltoarrayp($filename) {
    //创建PHPExcel对象，注意，不能少了\
    import("Org.Util.PHPExcel");
    $PHPExcel = new \PHPExcel();
    //如果excel文件后缀名为.xls，导入这个类
    import("Org.Util.PHPExcel.Reader.Excel5");
    //如果excel文件后缀名为.xlsx，导入这下类
    //import("Org.Util.PHPExcel.Reader.Excel2007");
    //$PHPReader=new \PHPExcel_Reader_Excel2007();        

    $PHPReader = new \PHPExcel_Reader_Excel5();
    //载入文件
    $PHPExcel = $PHPReader->load($filename);
    //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
    $currentSheet = $PHPExcel->getSheet(0);
    //获取总列数
    $allColumn = $currentSheet->getHighestColumn();
    //获取总行数
    $allRow = $currentSheet->getHighestRow();
    ++$allColumn; //应对超过26的场景
    //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
    //从哪列开始，A表示第一列
    for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {//应对超过26的场景
        //for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
        //数据坐标
        $address = $currentColumn . "1";
        $header[$currentColumn] = $currentSheet->getCell($address)->getValue();
    }

    for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
        //从哪列开始，A表示第一列
        for ($currentColumn = 'A'; $currentColumn != $allColumn; $currentColumn++) {//应对超过26的场景
            //for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
            //数据坐标
            $address = $currentColumn . $currentRow;
            $arr[$currentRow][$header[$currentColumn]] = $currentSheet->getCell($address)->getValue();
        }
    }
    return $arr;
}

/**
 * 取得生日提醒列表
 * @param type $dlist
 * @param type $abs
 * @return type
 */
function getbirthday($dlist, $abs = 8) {
    $cuday = date('-m-d', time());
    $cudayx = date('Y-m-d', time());
    foreach ($dlist as $key => $v) {
        if ($v['md_time'] == $cuday) {
            $v['start_time'] = date('m-d', strtotime($v['start_time']));
            $clist[] = $v;
        } else {
            $ex = getday($v['md_time'], $cudayx);
            if ($ex < $abs && $ex > 0) {
                $v['start_time'] = date('m-d', strtotime($v['start_time']));
                $nlist[] = $v;
            }
        }
    }

    if (!empty($nlist)) {
        $sort = array(
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field' => 'md_time', //排序字段
        );
        $nlist = arraysort($nlist, $sort);
    }

    $birth['cur'] = $clist;
    $birth['next'] = $nlist;
    if ($clist | $nlist) {
        return $birth;
    } else {
        return null;
    }
}

/**
 *  二维数组根据某个字段排序
 *  功能：按照用户的年龄倒序排序
 * @param type $data
 * @param type $sort
 *   $sort = array(
  'direction' => 'SORT_DESC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
  'field' => 'age', //排序字段
  );
 * @return type
 */
function arraysort($data, $sort) {
    $arrSort = array();
    foreach ($data AS $uniqid => $row) {
        foreach ($row AS $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    if ($sort['direction']) {
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data);
    }
    return $data;
}

/**
 * 获取生日与当前时间的差值
 */
function getday($datstring, $curent) {
    $ty = date('Y', time()) . $datstring;
    $jg = getChaBetweenTwoDate($ty, $curent);
    return $jg;
}

/*
 *
 * 函数功能：计算两个以YYYY-MM-DD为格式的日期，相差多少天（日）
 * return int
 */
function getChaBetweenTwoDate($date1, $date2) {
    $Date_List_a1 = explode("-", $date1);
    $Date_List_a2 = explode("-", $date2);
    $d1 = mktime(0, 0, 0, $Date_List_a1[1], $Date_List_a1[2], $Date_List_a1[0]);
    $d2 = mktime(0, 0, 0, $Date_List_a2[1], $Date_List_a2[2], $Date_List_a2[0]);
    $Days = round(($d1 - $d2) / 3600 / 24);
    return $Days;
}

/**
 * 日期计算
 */
function datacount($curentday, $num = '+1', $mod = 'day') {
    switch ($mod) {
        case 'm':
            $d = date("Y-m-d", strtotime("{$num} month", strtotime($curentday)));
            break;
        case 'y':
            $d = date("Y-m-d", strtotime("{$num} year", strtotime($curentday)));
            break;
        case 'w':
            $d = date("Y-m-d", strtotime("{$num} week", strtotime($curentday)));
            break;
        default :
            $d = date("Y-m-d", strtotime("{$num} day", strtotime($curentday)));
            break;
    }
    return $d;
}

/**
 * 对IP进行处理
 * @param string $ip
 * @return type
 */
function epip($ip) {
    return preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/is', "$1.$2.$3.*", $ip);
}

/**
 * 将数据保存到excel表格中
 * @param type $rs        待保存数据
 * @param type $savepath  保存路径
 * @param type $model     保存方式  0 保存到服务器，1 直接下载
 * @param type $headermap   自定义栏目标题
 * @param type $showformat   设置excel格式
 * @return type
 */
function outputexcel($rs, $savepath = '', $model = 0, $headermap = null, $showformat = false) {
    if ($headermap) {
        foreach ($headermap as $key => $v) {
            $headArr[] = $v;
        }
    } else {
        foreach ($rs[0] as $key => $v) {
            $headArr[] = $key;
        }
    }

    return getExcel($headArr, $rs, $savepath, $model, $headermap, $showformat);
}

function getExcel($headArr, $data, $savepath = '', $model = 0, $headermap = null, $showformat = false) {
    //对数据进行检验
    if (empty($data) || !is_array($data)) {
        return false;
    }

    //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
    import("Org.Util.PHPExcel");
    import("Org.Util.PHPExcel.Writer.Excel5");
    import("Org.Util.PHPExcel.IOFactory.php");

    //创建PHPExcel对象，注意，不能少了\
    $objPHPExcel = new \PHPExcel();
    $objProps = $objPHPExcel->getProperties();

    //设置表头  
    foreach ($headArr as $k => $v) {
        $colum = PHPExcel_Cell::stringFromColumnIndex($k);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        if ($showformat) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle($colum . '1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        }
    }

    $titlecell = "A1:{$colum}1";

    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach ($data as $key => $rows) { //行写入
        $span = 0;
        if ($headermap) {
            foreach ($headermap as $keyName => $value) {// 列写入      
                $j = PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j . $column, $rows[$keyName], PHPExcel_Cell_DataType::TYPE_STRING);
                $span++;
                if ($showformat) {
//                $objPHPExcel->getActiveSheet()->getColumnDimension($j . $column)->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        } else {
            foreach ($rows as $keyName => $value) {// 列写入
                $j = PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j . $column, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                $span++;
                if ($showformat) {
//                $objPHPExcel->getActiveSheet()->getColumnDimension($j . $column)->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getStyle($j . $column)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        }
        $column++;
    }

    //重命名表
    // $objPHPExcel->getActiveSheet()->setTitle('test');
    //设置活动单指数到第一个表,所以Excel打开这是第一个表
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->getStyle($titlecell)->applyFromArray(
            array(
                'font' => array(
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    ),
                ),
            )
    );

    if ($model) {
        if (empty($savepath)) {
            $fileName = time() . '.xls';
        } else {
            $fileName = $savepath;
        }
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载            
    } else {
        //直接生成文件，不下载
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($savepath);
        return true;
    }
}

/**
 * 取得控制器列表
 * @return type
 */
function getctrllist($controllerpath = "Admin/Controller") {
    $planPath = APP_PATH . $controllerpath;
    $planList = array();
    $dirRes = opendir($planPath);
    while ($dir = readdir($dirRes)) {
        if (!in_array($dir, array('.', '..', '.svn'))) {
            $x = basename($dir, '.class.php');
            if (!in_array($x, array('BaseController', 'index.html'))) {
                $x = str_replace('Controller', '', $x);
                $planList[] = $x;
            }
        }
    }
    asort($planList);
    return $planList;
}

/**
 * 获取控制器中方法列表
 * @param string $control
 * @return type
 */
function get_actionarray($control = '', $module = "Mdmin\\Controller\\") {
    $control .='Controller';
    $advContrl = get_class_methods($module . $control);
    //dump($advContrl);
    $baseContrl = get_class_methods($module . 'BaseController');
    $diffArray = array_diff($advContrl, $baseContrl);
    $actionarray = array();
    foreach ($diffArray as $val) {
        $actionarray[] = $val;
    }
    return $actionarray;
}

/**
 * 清除缓存
 */
function clear_runtime() {
    $fu = new \Org\Util\FileUtil();
    $fu->unlinkDir(RUNTIME_PATH);
    $fu->unlinkDir(DATA_PATH);
}

/**
 * 获取站点配置
 * @param type $key  键名
 * @param type $sid_id  站点ID  0 全局配置
 * @param type $cnfname  配置项名称
 * @return type
 */
function getConfig($key = '', $sid_id = 0, $cnfname = 'system') {
    $catchpath = "lancatch/config/{$sid_id}/{$cnfname}";
    $value = F($catchpath);
    if (!$value) {
        /* 取得当前站点基本配置 */
        $db = new Common\Model\SubConfigModel();
        $value = $db->getConfig($sid_id, $cnfname);
        F($catchpath, $value);
    }
    $langdata = getLangueInfo();
    if ($cnfname == 'baseinfo') {
        $data = array_merge($value['base'], $value[$langdata['curent_lang']]);
    } else {
        $data = $value;
    }    
    if (empty($key)) {
        return $data;
    } else {
        return $data[$key];
    }
}

/**
 * 无限分类数据的树形格式化  需要key配合
 * @param type $items
 * @param type $sonname
 * @return type
 */
function genTree5($items,$sonname="son") { 
    foreach ($items as $item) 
        $items[$item['parentid']][$sonname][$item['id']] = &$items[$item['id']]; 
    return isset($items[0][$sonname]) ? $items[0][$sonname] : array(); 
} 

/**
 * 无限分类数据的树形格式化
 * @param type $items
 * @return type
 */
function genTree9($items,$sonname="son") {
    $tree = array(); //格式化好的树
    foreach ($items as $item)
        if (isset($items[$item['parentid']]))
            $items[$item['parentid']][$sonname][] = &$items[$item['id']];
        else
            $tree[] = &$items[$item['id']];
    return $tree;
}

/**
 *  缩进形式表现树形结构(递归格式化列表)
 * @param type $array
 * @param type $pid
 * @param string $dl
 * @param type $pretext
 */
function formatTreeList($array, $pid = 0, &$dl, $pretext = "", $path = '', $deep = 0, $keymap = array('id' => 'id', 'title' => 'title', 'parentid' => 'parentid')) {  
  
    $endcol = 0;
    foreach ($array as $k => $v) {
        if ($v[$keymap['id']] == 0) {
            continue;
        }
        if ($v[$keymap['parentid']] == $pid) {
            $v['title'] = $pretext . $v[$keymap['title']];
            $v['xpath'] = $path . $v[$keymap['id']] . '|';
            $v['deep'] = $deep;
            unset($v['numrow']);
            $dl[$v[$keymap['id']]] = $v;
            $endcol = 1;       
            formatTreeList($array, $v[$keymap['id']], $dl, '|--' . $pretext, $path . $v[$keymap['id']] . '|', $deep + 1, $keymap);
        }
    }
    if ($dl[$pid]) {
        $dl[$pid]['haschild'] = $endcol;
    }
}

/**
 * 缩略图 原始图来裁切出来的
 * @param type $goods_id  商品id
 * @param type $width     生成缩略图的宽度
 * @param type $height    生成缩略图的高度
 * @param type $key    图片字段
 * @param type $content    图片提取字段字
 * @param type $type      生成缩略图的类型
 *    1 ; 标识缩略图等比例缩放类型
 *    2 ; 标识缩略图缩放后填充类型
 *    3 ; 标识缩略图居中裁剪类型
 *    4 ; 标识缩略图左上角裁剪类型
 *    5 ; 标识缩略图右下角裁剪类型
 *    6 ; 标识缩略图固定尺寸缩放类型
 */
function creatThumbImages($v,$width, $height, $key='thumb',$content='content', $type = 2) {
    if (is_array($v)) {
        $img = trim($v[$key]);
        if (empty($img)) {
            $img = getImgs($v[$content], 0); //取文章内含第一张图           
        }
    } else {
        $img = $v;
    }

    if (isurl($img)) {//远程图片不进行压给缩
        return $img;
    }
    $img = htmlhelper::crcakext($img);
    $imgpath = $img;

    if (!is_file(ltrim($imgpath, '/')))
        $imgpath = C('TMPL_PARSE_STRING.__PUBLIC__') . '/images/nopic.jpg';

    $imgpath = ltrim($imgpath, '/');
    $basefile = basename($imgpath);
    $newfile = "thumb_{$width}_{$height}_{$type}_{$basefile}";

    $path = "Uploads/thumb/";
    $pathfile = $path . $newfile;
    if (file_exists($pathfile) && (filemtime($pathfile) > filemtime($imgpath))) {
        return '/' . $pathfile;
    }

    $image = new \Think\Image();
    $image->open($imgpath);

    // 生成缩略图
    if (!is_dir($path))
        mkdir($path, 0777, true);
    $image->thumb($width, $height, $type)->save($pathfile, NULL, 100);
    return '/' . $pathfile;
}

/**
 * 判断是否为网址
 * @param type $url
 * @return type
 */
function isurl($url) {
    $regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';
    return preg_match($regex, $url);
}

/**
 * 生成UUID 单机使用
 * @access public
 * @return string
 */
function creatGuid() {
    return Org\Util\String::uuid();
}

/**
 * 生成Guid主键
 * @return Boolean
 */
function creatKeyGen() {
    return Org\Util\String::keyGen();
}

/**
 * 设置http header
 */
function set_csp_header($noframe=1) {
   if($noframe){
       header('X-Frame-Options:Deny');
   }  else {
       header('X-Frame-Options:SAMEORIGIN');
   }
    header("Content-type: text/html; charset=utf-8");
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' map.baidu.com tts.baidu.com *.map.bdimg.com *.map.baidu.com at.alicdn.com; img-src 'self' data: about: qzapp.qlogo.cn www.37zw.net *.map.bdimg.com *.map.baidu.com *.bdimg.com at.alicdn.com static.tieba.baidu.com *.imgsrc.baidu.com *.hiphotos.baidu.com *.map.bdstatic.com *.sinaimg.cn *.alipayobjects.com;connect-src 'self' wss://ws.fakeruhe.com;");
}

/**
 * 传入参数格式化
 * @param type $str
 * @return type
 */
function parse_url_param($str) {
    $data = array();
    $parameter = explode('&', end(explode('?', $str)));
    foreach ($parameter as $val) {
        $tmp = explode('=', $val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}


/**
 * 生成定长字符串（订单号）
 * @param type $seed
 * @param type $pre
 * @param type $length
 * @return type
 */
function makeOrderNo($seed = 0, $pre = '', $length = 11) {
    return $pre . str_pad($seed, $length, '0', STR_PAD_LEFT);
}

/**
 * 生成订单号
 * @param type $pre
 * @return type
 */
function creatOrderNo($pre = '') {
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $orderSn = $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $pre . $orderSn;
}

/**
 * 从序列化字段中读取单个属性
 * @param type $style
 * @param type $field
 * @return type
 */
function getProessStyle($style, $field) {
    $s = unserialize($style);
    return $s[$field];
}

/**
 * 显示头像
 * @param type $model
 * @param type $width
 * @param type $height
 */
function showHeadPic($model, $width = 40, $height = 40) {
    if (isset($model['m_id'])) {
        $id = $model['m_id'];
    } else {
        $id = $model['userid'];
    }
    $db = new Common\Model\AdminModel();
    $m = $db->where(array('m_id' => $id))->cache()->find();
    $m['thumb'] = $m['head_pic'];
    return creatThumbImages($m, $width, $height);
}


/**
 * 判断是微信浏览器
 * @return type
 */
function isWeixin() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
}

/**
 * 判断是移动终端
 * @return boolean
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

        
/**
 * 获取文章评论数
 * @param type $newsid
 * @return type
 */
function showNewsPinlun($newsid){
    $db=new \Common\Model\NewsPinlunModel();
    return $db->getplcount($newsid);
}

/**
 * 发送微信客服消息
 * @param type $sid_id
 * @param type $mid
 * @param array $data
 * @return boolean
 */
function wxSendCustomMessage($sid_id, $mid, $data) {
    $db = new Common\Model\AdminModel();
    $user = $db->getUserinfo($mid);

    $data['touser'] = $user['weixin_openid'];
    if ($user['weixin_openid']) {     
        $baseconfig = getConfig('',$sid_id,'baseinfo');
        $wechat['token'] = $baseconfig['wx_token'];
        $wechat['appid'] = $baseconfig['wx_appid'];
        $wechat['appsecret'] = $baseconfig['wx_appsecret'];
        $wechat['encodingaeskey'] = $baseconfig['wx_encodingaeskey'];
        $weixin = new \Extend\Wechat($wechat);
        return $weixin->sendCustomMessage($data);
    } else {
        return false;
    }
}

/**
 * 保存openid
 * @param type $openid
 */
function set_openid($openid) {
    session("Openid", $openid);
}

/**
 * 取得openid
 */
function get_openid() {
    $openid = session("Openid");
    return $openid;
}

function clear_openid() {
    session("Openid", null);
}

/**
 * 清除指定目前下的文件缓存
 * @param type $path
 */
function clearCatch($path){
    F($path.'/*',NULL);
}


/**
 * 删除数组中指定的
 * @param type $array
 * @param type $value
 */
function removeArrayValue($value, $array) {
    $newarray = array();
    foreach ($array as $v) {
        if ($v != $value) {
            $newarray[] = $v;
        }
    }
    return $newarray;
}

/**
 * 创建当前语言关联查询
 * @param type $tablename
 * @param type $lang
 */
function creatLangJion($tablename, $lang) {
    $tablename = strtoupper($tablename);
    $join = "LEFT JOIN __{$tablename}TEXT__ ON __{$tablename}__.id = __{$tablename}TEXT__.extid and __{$tablename}TEXT__.lang = '{$lang}'";
    return $join;
}

/**
 * 针对参数绑定，查询条件转化
 * @param type $map  查询条件
 * @param type $filedarray 绑定参数
 * @param type $valuearray 绑定数据
 */
function tracemaptobind($map, &$filedarray, &$valuearray) {
    if (isset($map['_complex'])) {
        $where = $map['_complex'];
        unset($map['_complex']);
    }
    foreach ($map as $k => $v) {
        if (is_array($v)) {
            switch ($v[0]) {
                case "between":
                    if (is_array($v[1])) {
                        $vx = $v[1];
                    } else {
                        $vx = explode(',', $v[1]);
                    }
                    $filedarray[$k] = array($v[0], array(":start{$k}", ":end{$k}"));
                    $valuearray[":start{$k}"] = $vx[0];
                    $valuearray[":end{$k}"] = $vx[1];
                    break;
                case "like":
                case 'notlike':
                    if (is_array($v[1])) {
                        $vx = $v[1];
                    } else {
                        $vx = [$v[1]];
                    }
                    $inarray = array();
                    foreach ($vx as $key => $val) {
                        $inarray[] = ":" . $k . $key;
                        $valuearray[":" . $k . $key] = $val;
                    }
                    $filedarray[$k] = array($v[0], $inarray);
                    break;
                case "not in":
                case "in":                
                    if (is_array($v[1])) {
                        $vx = $v[1];
                    } else {
                        $st = rtrim($v[1], ',');
                        $vx = explode(',', $st);
                    }
                    $inarray = array();
                    foreach ($vx as $key => $val) {
                        $inarray[] = ":" . $k . $key;
                        $valuearray[":" . $k . $key] = $val;
                    }
                    $filedarray[$k] = array($v[0], $inarray);
                    break;
                default :
                    $filedarray[$k] = array($v[0], ":{$k}");
                    $valuearray[':' . $k] = $v[1];
                    break;
            }
        } else {
            $filedarray[$k] = ":{$k}";
            $valuearray[':' . $k] = $v;
        }
    }
    if ($where) {
        $filedarray['_complex'] = $where;
    }
}

/**
 * 活跃标记
 * @param type $mid
 */
function markactive($mid, $sysconfig) {
    $db = new Common\Model\SaferuleModel();
    $model = $db->getmodelbyid($mid);
    if ($model['isonline']) {
        if ($sysconfig['single_sign']) {//开启单点登录检测
            if ($model['useip'] !== get_client_ip()) {
                cookie(C('ALLIANCE.USER_AUTH_KEY'), null);
                topredirect(U('/manage/Login/logout', array('type' => 1))); //当前IP与登录IP不一致下线
            }
            $isonline = cookie('isonline');
            if ($model['isonline'] !== $isonline) {
                cookie(C('ALLIANCE.USER_AUTH_KEY'), null);
                topredirect(U('/manage/Login/logout', array('type' => 3))); //异地登入
            }
        }
    } else {
        cookie(C('ALLIANCE.USER_AUTH_KEY'), null);   
        topredirect(U('/manage/Login/logout', array('type' => 2))); //超时下线
        exit();
    }
    return $db->activemark($mid);
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function topredirect($url, $time=0, $msg='') {
    //多行URL地址支持
    $url        = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
           echo("<script type=\"text/javascript\">top.location.href=\"{$url}\"</script>");
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str    = "<script type=\"text/javascript\">top.location.href=\"{$url}\"</script>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

function download($file_url, $new_name = '') {
    if (!isset($file_url) || trim($file_url) == '') {
        echo '500';
    }
    if (!file_exists($file_url)) { //检查文件是否存在 
        echo '404';
    }
    $file_name = basename($file_url);
    $file_type = explode('.', $file_url);
    $file_type = $file_type[count($file_type) - 1];
    $file_name = trim($new_name == '') ? $file_name : urlencode($new_name);
    $file_type = fopen($file_url, 'r'); //打开文件 
    //输入文件标签 
    header("Content-type: application/octet-stream");
    header("Accept-Ranges: bytes");
    header("Accept-Length: " . filesize($file_url));
    header("Content-Disposition: attachment; filename=" . $file_name);
    //输出文件内容 
    echo fread($file_type, filesize($file_url));
    fclose($file_type);
}

/**
 * 清除指定的静态缓存
 * @param type $catchpath
 */
function clear_static_catch($catchpath) {
    $languetable = getAttrsElementList("Langue");
    foreach ($languetable as $k => $v) {
        F("{$catchpath}/{$v['tagvalue']}", null);   
    }
}

/**
 *  obj 转 query string
 * @param type $obj
 * @return type
 */
function json2str($obj) {
    ksort($obj);
    $arr = array();
    foreach ($obj as $key => $val) {
        array_push($arr, $key . '=' . $val);
    }
    return join('&', $arr);
}

/**
 * 读取站点列
 * @return type
 */
function getsubsidlist() {
    $db = new \Common\Model\SubsidiaryModel();
    $rs = $db->builarray();
    return $rs;
}
