<?php

/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 *  @param string $rl 替换长度
 * @return string 格式化后的姓名
 */
function substr_cut($user_name, $rl = 4) {
    $strlen = mb_strlen($user_name, 'utf-8');
    $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
    if ($strlen == 2) {
        return $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1);
    }
    if ($strlen < 1 + $rl) {
        return $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    } else {
        $firstlen = ceil(($strlen - $rl) / 2);
        $lastlen = floor(($strlen - $rl) / 2);
        $firstStr = mb_substr($user_name, 0, $firstlen, 'utf-8');
        $lastStr = mb_substr($user_name, $firstlen + $rl, $lastlen, 'utf-8');
        return $firstStr . str_repeat("*", $rl) . $lastStr;
    }
}

/**
 * 相册首图
 * @param type $photo
 * @param type $defalut 黙认图片
 */
function get_photo_firstimg($photo, $defalut = "/Public/images/select.png") {
    if (!empty($photo)) {
        $newvalue = json_decode($photo, true);
        foreach ($newvalue['card'] as $v) {
            $sort = array(
                'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field' => 'id', //排序字段
            );
            $nlist = arraysort($newvalue['list'][$v['tagvalue']], $sort);
            foreach ($nlist as $w) {
                if (file_exists('.' . $w['thumb'])) {
                    return $w['thumb'];
                }
            }
        }
    }
    return $defalut;
}

function get_photo($photo, $type = 0) {
    $newvalue = json_decode($photo, true);
    if ($type) {
        return $newvalue['list'][$type];
    }
    $plist = array();
    foreach ($newvalue['list'] as $v) {
        foreach ($v as $w) {
            $plist[] = $w;
        }
    }
    return $plist;
}

 
/**
 * 转换编码，将Unicode编码转换成可以浏览的utf-8编码 
 * @param type $sname
 * @return type
 */
function unicodeDecode($sname) { 
    $name= str_replace('u', '\u', $sname);
    if($name==$sname){
        return $sname;
    }
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';   
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches)) {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++) {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0) {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code) . chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            } else {
                $name .= $str;
            }
        }
    }
    return $name;
}

function trance_photo($photo) {
    if (!empty($photo)) {
        $newvalue = json_decode($photo, true);
        $newlist = array();
        $newcard = array();
        $allphpoto = get_photo($photo);
        if (count($allphpoto) > 0) {
            $newlist[] = $allphpoto;
            $newcard[] = array('title' => "全部图片");
        } else {
            return null;
        }
        foreach ($newvalue['card'] as $v) {
            $sort = array(
                'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
                'field' => 'id', //排序字段
            );
            $nlist = arraysort($newvalue['list'][$v['tagvalue']], $sort);
            if (count($nlist) > 0) {
                $newcard[] = $v;
                $newlist[] = $nlist;
            }
        }
        $newphoto=array('card'=>$newcard,'nlist'=>$newlist);
        //$newvalue['nlist'] = $newlist;
    }
    return $newphoto;
}



/**
 * 根据viewpaht获得栏目信息
 * @param type $view
 * @param type $sid_id
 * @return type
 */
function getcolumsbypath($view, $sid_id) {
    $db = new Common\News\ColumnsModel();
    $map['viewpath'] = $view;
    $map['sid_id'] = $sid_id;
    return $db->getmodelbypath($map);
}

/**
 * 保存语言信息
 * @param type $mid
 */
function setLangueInfo($data) {
    cookie('LangueInfo', $data);
//    session("LangueInfo", $data);
}

/**
 * 清除语言信息
 */
function clearLangueInfo() {
    session("LangueInfo", null);
    cookie('LangueInfo', null);
}


/**
 * 获取语言信息
 * @param type $langue  指定语种
 * @param type $sid_id  站点ID
 * @return type
 */
function getLangueInfo($langue = '', $sid_id = 0) {
    
    $default = C('DEFAULT_LANG');
    if (!empty($langue)) {
        if (empty($sid_id)) {
            $sid_id = cookie('csidid');
        }
        $langdata = array(
            'default_lang' => $default,
            'curent_lang' => $langue,
            'sid_id' => $sid_id,
            'langtitle' => "",
            'shortlangtitle' => "",
        );
        return $langdata;
    }


    if (MODULE_NAME == "Admin") {//后台语言
        $curent = $default;
//        $langdata = session("LangueInfo");
        $langdata = cookie("LangueInfo");
        if (!$langdata['curent_lang']) {
            $langdata = array(
                'default_lang' => $default,
                'curent_lang' => $curent,
                'langtitle' => "",
                'shortlangtitle' => "",
            );
        }
    }
    else {//前台语言
        $curent = I(C('LANGUE_TAG'), '');
        if (empty($curent)) {
            $curent = $default;
        }
        if (empty($sid_id)) {
            $sid_id = cookie('csidid');
        }
        $langdata = array(
            'default_lang' => $default,
            'curent_lang' => $curent,
            'sid_id' => $sid_id,
            'langtitle' => "",
            'shortlangtitle' => "",
        );
    }
    return $langdata;
}

/**
 * 计算半径区域坐标
 * @param type $lat纬度
 * @param type $lon 经度
 * @param type $raidus 单位米
 */
function getAround($lat, $lon, $raidus) {
    $PI = 3.14159265;

    $latitude = $lat;
    $longitude = $lon;

    $degree = (24901 * 1609) / 360.0;
    $raidusMile = $raidus;

    $dpmLat = 1 / $degree;
    $radiusLat = $dpmLat * $raidusMile;
    $minLat = $latitude - $radiusLat;
    $maxLat = $latitude + $radiusLat;

    $mpdLng = $degree * cos($latitude * ($PI / 180));
    $dpmLng = 1 / $mpdLng;
    $radiusLng = $dpmLng * $raidusMile;
    $minLng = $longitude - $radiusLng;
    $maxLng = $longitude + $radiusLng;
    $rs['minlat'] = $minLat;
    $rs['maxlat'] = $maxLat;
    $rs['minlng'] = $minLng;
    $rs['maxlng'] = $maxLng;
    $rs['lat'] = array($minLat, $maxLat);
    $rs['lng'] = array($minLng, $maxLng);
    return $rs;
    //echo $minLat . '#' . $maxLat . '@' . $minLng . '#' . $maxLng;
}

/**
 * 返回允许上传文件的类型
 * @param type $type
 * @return type
 */
function getUploadExts($type = "pic") {
    $haystack = C("UPLOADTYPE.{$type}");
    $exts = implode($haystack, "|");
    return $exts;
}

/**
 * 获取允行上传文件的大小（单位 K）
 * @param type $type
 * @return type
 */
function getUploadSize($type = "pic") {
    return C("UPLOADTYPE.{$type}_size");
}

/**
 * 过滤xss
 * @param type $val
 * @return type
 */
function htmlspecialcharsx($val) {
    $val = safeHtml($val);
    $val = htmlspecialchars($val, ENT_QUOTES);
    return $val;
}

/**
  +----------------------------------------------------------
 * 输出安全的html，用于过滤危险代码
  +----------------------------------------------------------
 * @access public
  +----------------------------------------------------------
 * @param string $text 要处理的字符串
 * @param mixed $tags 允许的标签列表，如 table|td|th|td
  +----------------------------------------------------------
 * @return string by www.jbxue.com
  +----------------------------------------------------------
 */
function safeHtml($text) {
    $text = preg_replace("/<!--?.*-->/", "", $text);
    $text = preg_replace("/<script[\s\S]*?<\/script>/i", "", $text);
    $text = preg_replace("/<(i?frame|style|html|body|title|link|meta)([\s\S]*?)>/i", "", $text);
    $text = preg_replace("/(<[^>]*)on[a-zA-Z]+s*=([^>]*>)/isU", "", $text);
    return $text;
}

function removexss($val) {
    static $obj = null;
    if ($obj == null) {
        //include './HTMLPurifier/HTMLPurifier.includes.php';
        Vendor('HTMLPurifier.HTMLPurifier_includes');
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,a');
        $config->set('AutoFormat.AutoParagraph', true);
        $config->set('AutoFormat.RemoveEmpty', true); // 清除空标签
        $config->set('HTML.Allowed', 'a[href]');
        $config->set('HTML.TargetBlank', true);
        $obj = new HTMLPurifier($config);
    }
    return $obj->purify($val);
}

/**
 * 添加文本日志
 * @param type $message
 */
function addtextlog($message, $logpath = "Data/", $mod = FILE_APPEND) {
    //./Data/text.log
    $str= json_encode($message);
    file_put_contents("./{$logpath}text.log", $str . PHP_EOL, $mod);
}

/**
 * 获取排序后的分类
 * @param  [type]  $data  [description]
 * @param  integer $pid   [description]
 * @param  string  $html  [description]
 * @param  integer $level [description]
 * @return [type]         [description]
 */
function getSortedCategory($data, $pid = 0, $html = "|---", $level = 0) {
    $temp = array();
    foreach ($data as $k => $v) {
        if ($v['pid'] == $pid) {
            $str = str_repeat($html, $level);
            $v['html'] = $str;
            $temp[] = $v;
            $temp = array_merge($temp, getSortedCategory($data, $v['id'], '|---', $level + 1));
        }
    }
    return $temp;
}

/**
 * 根据key，返回当前行的所有数据
 * @param  string  $key  字段key
 * @return array         当前行的所有数据
 */
//function getSettingValueDataByKey($key) {
//    return M('setting')->getByKey($key);
//}

/**
 * 根据key返回field字段
 * @param  string $key   [description]
 * @param  string $field [description]
 * @return string        [description]
 */
//function getSettingValueFieldByKey($key, $field) {
//    return M('setting')->getFieldByKey($key, $field);
//}

/**
 * 测试栏目路径是否合法,只允行包含小写字母
 * @param type $path
 * @return boolean
 */
function checksafepath($path) {
    return preg_match("/[^a-z0-9]+$/", $path);
}

/**
 * 检测并解析详情ID
 * @param type $cid
 * 格式  $idn$cid
 */
function checkcid($cid) {
    $regex = '/^([\d.]+)n([\d.]+)$/i';
    $matches = array();
    if (preg_match($regex, $cid, $matches)) {
//        var_dump($matches);
        $rs['id'] = $matches[1];
        $rs['colid'] = $matches[2];
        return $rs;
    } else {
        echo showTagbyMark("DisagreeInput");
        die;
    }
}

/**
 * 测试ID是否数字
 * @param type $id      被测试变量
 * @param type $return  是否返回测试结果
 * @return type
 */
function checkid($id, $return = false) {
    $rs = array("status" => 0, 'msg' => 'DisagreeInput');
    if (preg_match("/^\d*$/", $id)) {
        $rs['status'] = 1;
        $rs['id'] = $id;
    } else {
        if ($return) {
            echo showTagbyMark($rs['msg']);
            die;
        }
    }
    return $rs;
}

/**
 * 输入检测，防止sql注入
 * @param type $sql_str
 * @return type
 */
function checkinput($sql_str) {
    $check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|script', $sql_str);
    if ($check) {
        echo showTagbyMark("DisagreeInput");
        die;
    } else {
        return $sql_str;
    }
}

/**
 * 获取内容中的所有图片列表
 * @param type $content  内容
 * @param type $order  选择返回数据  all  
 * @return string
 */
function getImgs($content, $order = 'ALL') {
    $p = '/<img(.*)src=\"([^\"]+)\"[^>]+>/isU';
    preg_match_all($p, $content, $match);
    if ($order === 'ALL') {
        return $match[2];
    }
    if (is_numeric($order) && isset($match[2][$order])) {
        return $match[2][$order];
    }
    return '';
}

/**
 * 显示地区简名
 * 移除省市等后缀
 * @param type $model
 * @param type $keyname
 */
function show_region_name($model, $keyname = 'region', $showkey = '') {
    $keyname = strtolower($keyname);
    $value = $model[$keyname];
    $codekey = 'region';
    switch ($keyname) {
        case 'province':
            $value = str_replace(['市', '省'], '', $value);
            $codekey = 'pgb';
            break;
        case 'city':
            $value = str_replace('市', '', $value);
            $codekey = 'cgb';
            break;
        case 'region':
            $value = str_replace(['市', '区', '县'], '', $value);
            $codekey = 'gbcode';
            break;
        default :
            $value = trim($value);
            break;
    }
    if ($showkey == 'link') {
        $value = region_index($model, $codekey);
    }
    return $value;
}

/**
 * 生成面包屑导航
 * @param type $sch
 * @return type
 */
function make_search_mbx($sch) {
    $dbc = new \Common\Gmodel\LbsRegionModel();
    $region = $dbc->getbycode($sch['regioncode']);
    $data['region'] = $region;
    if ($sch['streetcode']) {
        $dbs = new \Common\Gmodel\LbsSubregionModel();
        $subregion = $dbs->getbycode($sch['streetcode']);
        $data['subregion'] = $subregion;
    }
    $rs['status'] = 1;
    $rs['data'] = $data;
    return $rs;
}

/**
 * 显示客户信息
 * @param type $model
 * @param type $infotype
 */
function show_room_info($model, $infotype = 'roomtype', $byid = 0) {
    if ($byid === 1) {
        $rm = new Common\Zfmodel\ZfRoomsModel();
        $model = $rm->getmodelbyid($model);
    }
    $infostr = "Null";
    switch ($infotype) {
        case 'roomtype':
            $infostr = "";
            if ($model['roomnum']) {
                $infostr.=$model['roomnum'] . "室";
            }
            if ($model['tingnum']) {
                $infostr.=$model['tingnum'] . "厅";
            }
            if ($model['toiletnum']) {
                $infostr.=$model['toiletnum'] . "卫";
            }
            if ($model['balcony']) {
                $infostr.= "{$model['balcony']}阳台";
            }
            break;
        case 'area':
            $infostr = $model['area'] . "㎡";
            break;
        case 'price':
            $infostr = floor($model['monthprice']) . "元";
            break;
        case 'monthprice':
            $infostr = floor($model['monthprice']);
            break;
        case 'description':
            $infostr = nl2br($model['description']);
            break;
        case 'special':
            $infostr = nl2br($model['special']);
            break;
        case "floor":
            $dl = getAttrsElementList('floor_type');
            foreach ($dl as $k => $v) {
                $value = $v['tagvalue'];
                $pattern = "/(\d*)-(\d*)/is"; //价格
                preg_match($pattern, $value, $matches);
                //$infostr = $matches[1] . "-" . $matches[2];
                if (empty($matches[1]) && $matches[2] > $model['floor']) {
                    $infostr = $v['title'];
                    break;
                }
                if (empty($matches[2]) && $matches[2] < $model['floor']) {
                    $infostr = $v['title'];
                    break;
                }
                if (($matches[1] < $model['floor']) && ($matches[2] > $model['floor'])) {
                    $infostr = $v['title'];
                    break;
                }
            }

//            if ($model['floor'] < 3) {
//                $infostr = "低层";
//            } elseif ($model['floor'] < 8) {
//                $infostr = "中层";
//            } else {
//                $infostr = "高层";
//            }
            //$infostr= floor($model['floor'])."F";
            break;
        case 'roomface':
            $infostr = getkeyname($model['roomface'], 'RoomFace');
            break;
        case 'zhuangxiu':
            $infostr = getkeyname($model['roomface'], 'RoomZhuangxiu');
            break;
        case 'modid':
            $infostr = getkeyname($model['modid'], 'yunyintype');
            break;
        case 'rentingtype':
            $infostr = getkeyname($model['rentingtype'], 'rentingtype');
            break;
        case 'gongnuan':
            $infostr = getkeyname($model['gongnuan'], 'gongnuan');
            break;
        case 'flashtime':
            $infostr = date('Y-m-d', $model['flashtime']);
            break;
        case 'sebei':
            $rs = getTaglist('RoomSebei', $model['sebei']);
            if ($rs['status']) {
                $infostr = "";
                foreach ($rs['list'] as $k => $v) {
                    $infostr.=$v['title'] . "、";
                }
                $infostr = rtrim($infostr, '、');
            }
            break;
        case 'sebeilist':
            $rs = getTaglist('RoomSebei', $model['sebei']);
            if ($rs['status']) {
                $infostr = array();
                foreach ($rs['list'] as $k => $v) {
                    $infostr[] = $v;
                }
            }
            break;
        case 'rentingmod':
            $rs = getTaglist('Renttag', $model['rentingmod']);
            if ($rs['status']) {
                $infostr = "";
                foreach ($rs['list'] as $k => $v) {
                    $infostr.=$v['title'] . "、";
                }
                $infostr = rtrim($infostr, '、');
            }
            break;
        case "nearest_subway":
            if ($model['nearest_subway'] > 0) {
                $infostr = "距离最近地铁站 <span style='color:red;'>{$model['nearest_subwayname']}</span>{$model['nearest_subway']}米";
            } else {
                $infostr = "附近没有地铁站";
            }
            break;
        case 'paymod':
            $rs = getTaglist('Paymod', $model['paymod']);
            if ($rs['status']) {
                $infostr = "";
                foreach ($rs['list'] as $k => $v) {
                    $infostr.=$v['title'] . "、";
                }
                $infostr = rtrim($infostr, '、');
            }
            break;
        case 'link':
            $infostr = roomsdetail($model, '/');
            break;
        case 'roomstatus':
            $ft = new Common\Zfmodel\ZfRoomftModel();
            $m = $ft->getmodel(array('roomid' => $model['id']));
            $infostr = show_roomft_info($m['data'], 'roomstatus');
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示房态信息
 * @param type $model
 * @param type $infotype
 * @return type
 */
function show_roomft_info($model, $infotype = 'title', $byid = 0) {
    $infostr = "Null";
    $nodata = showTagbyMark('no_data');
    $unknow_data = showTagbyMark('unknow_data');
    if ($byid === 1) {
        $rm = new Common\Zfmodel\ZfRoomftModel();        
        $model = $rm->getmodelbyid(array('id'=>$model));
        $model = $model['data'];
    }
    switch ($infotype) {
        case 'checkindata'://入住日期
            if ($model['roomstatus'] == 2) {
                $infostr = date('Y-m-d', $model[$infotype]);
            } else {
                $infostr = '-';
            }
            break;
        case 'paymod':
            $infostr = getkeyname($model['paymod'], 'Paymod');
            break;
        case 'duedata'://到期时间
            if ($model['roomstatus'] == 2) {
                $infostr = date('Y-m-d', $model[$infotype]);
            } else {
                $infostr = '-';
            }
            break;
        case 'buyunit'://购买的单位
            $infostr = getkeyname($model['paymod'], 'Paymod', '', 'shorttitle'); //showTagbyMark($model['paymod'], '','Paymod','','shorttitle');
            break;
        case 'deposit'://押金
            $infostr = $model[$infotype];
            break;
        case 'rent'://租金
            $infostr = $model[$infotype];
            break;
        case 'buyrent':
            $infostr = $model['rent'] + $model['deposit'];
            break;
        case 'isrenew'://当前房态
            $infostr = getkeyname($model['isrenew'], 'isshow_status');
            break;
        case 'roomstatus'://当前房态
            $infostr = getkeyname($model['roomstatus'], 'room_status');
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示订单信息
 * @param type $model
 * @param type $infotype
 * @param type $suffix  前缀，附加信息
 * @return type
 */
function show_order_info($model, $infotype = 'title', $suffix = '') {
    $infostr = "Null";
    $nodata = showTagbyMark('no_data');
    $unknow_data = showTagbyMark('unknow_data');
    switch ($infotype) {
        case 'livetime'://入住日期
            $infostr = date('Y-m-d', $model[$infotype]);
            break;
        case 'duetime':
            $infostr = date('Y-m-d', $model[$infotype]);
            break;
        case 'ordertype':
            $infostr = getkeyname($model['ordertype'], 'order_type');
            break;
        case 'roomid':
            $db = new Common\Zfmodel\ZfRoomsModel();
            $room = $db->getmodelbyid($model['roomid']);
            $roomdetail = roomsdetail($room, $suffix);
            $infostr = "<a href=\"{$roomdetail}\" target=\"_blank\">{$room['title']}</a>";
            break;
        case 'paymod':
            $infostr = getkeyname($model['paymod'], 'Paymod');
            break;
        case 'ispay':
            $infostr = getkeyname($model['ispay'], 'order_ispay');
            break;
        case 'status':
            $infostr = getkeyname($model['status'], 'order_status');
            break;
        case 'addtime'://下间时间     
            $infostr = date('Y-m-d', $model[$infotype]);
            break;
        case 'lasttime'://最后操作时间     
            $infostr = date('Y-m-d', $model[$infotype]);
            break;
        case 'buyunit'://购买的单位
            $infostr = getkeyname($model['paymod'], 'Paymod', '', 'shorttitle');
            break;
        case 'deposit'://押金
            $infostr = $model[$infotype];
            break;
        case 'price'://单价金
            $infostr = $model[$infotype];
            break;
        case 'totalprice':
            $infostr = $model[$infotype];
            break;
        case 'isrenew'://续租
            $infostr = getkeyname($model['isrenew'], 'isshow_status');
            break;
        case 'roomstatus'://当前房态
            $infostr = getkeyname($model['roomstatus'], 'room_status');
            break;
        case 'reply'://是否有需要回复的新评论
            //($v.status eq 6) AND ($v.judge gt 0) AND ($v.reply eq 0)
            $infostr = 0;
            if ($model['status'] == 6) {
                if ($model['judge'] > 0) {
                    $db = new Common\Zfmodel\ZfJudgeModel();
                    $jmodel = $db->getmodel($model['judge']);
                    if ($jmodel['status']) {
                        $infostr = $jmodel['data']['reply'];
                    }
                }
            }
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示订单信息
 * @param type $model
 * @param type $infotype
 * @param type $suffix  前缀，附加信息
 * @return type
 */
function show_orderlog_info($model, $infotype = 'title', $suffix = '') {
    $infostr = "Null";
    switch ($infotype) {
        case 'paymod':
            $infostr = getkeyname($model['paymod'], 'Paymod');
            break;
        case 'order_ispay':
            $infostr = getkeyname($model['order_ispay'], 'order_ispay');
            break;
        case 'order_status':
            $infostr = getkeyname($model['order_status'], 'order_status');
            break;
        case 'addtime'://下间时间     
            $infostr = date('Y-m-d H:i', $model[$infotype]);
            break;
        case 'optype'://当前房态
            $infostr = getkeyname($model['optype'], 'order_optype');
            break;
        case 'username':
//            $infostr="2";
            switch ($model['usertype']) {
                case 1:
                    $db = new Common\Umodel\CusCustomerModel();
                    $user = $db->getmodelbyid($model['userid']);
                    $infostr = $user['username'];
                    break;
                case 2:
                    $db = new \Common\Model\AdminModel();
                    $user = $db->getmodelbyid($model['userid']);
                    $infostr = $user['m_name'];
                    break;
            }
            break;
//        case 'imglist':
//            $infostr=  showimglist($model['imglist']);
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示列表图片
 * @param type $list
 * @return type
 */
function showimglist($list, $templ) {
    if(empty($templ)){
        $templ = "<a href=\"_IMGURL_\" target=\"_blank\">[_IMGKEY_]</a>";
    }
    $imglist = explode(',', $list);
    $infostr = '';
    if (!empty($imglist)) {
        foreach ($imglist as $k => $v) {
            $key = $k + 1;
            if (!empty(trim($v))) {
                $img = str_replace(['_IMGURL_', '_IMGKEY_'], [$v, $key], $templ);
                $infostr .=$img;
            }
        }
    }
    return $infostr;
}

/**
 * 显示地理位置相关信息
 * @param type $addinfo  完整的地理信息
 * @param type $infotype  需要返回显示的数据类型
 * @param type $showkey   需要显示的字段类型   link  返回指向该信息的链接
 * @return type
 */
function show_area_info($addinfo, $infotype = 'loupan', $showkey = 'alldata') {
    if ($addinfo['status']) {
        $data = $addinfo['data'];
        if (empty($showkey)) {//返实未经处理的整个实列
            $rs = $data[$infotype];
        } else {
            switch ($infotype) {
                case 'loupan'://楼盘信息处理
                    $rs = show_loupan_info($data[$infotype], $showkey);
                    break;
                case 'build'://楼宇信息处理
                    $rs = show_build_info($data[$infotype], $showkey);
                    break;
                case 'rooms'://客房信息处理
                    $rs = show_room_info($data[$infotype], $showkey);
                    break;
                case 'region'://省市区行政信息
                    $rs = show_region_name($data[$infotype], $infotype, $showkey);
                    break;
                case 'subregion'://乡镇信息
                    if ($showkey == 'link') {
                        $rs = subregion_index($data[$infotype]);
                    } else {
                        $rs = str_replace(['镇', '区'], '', $data[$infotype]['areaname']);
                    }
                    break;
                case 'linkarray'://完整的链接数组
                    $rs = array();
                    $url['title'] = '首页';
                    $url['url'] = "/";
                    $rs[] = $url;

                    $url['title'] = show_region_name($data['region'], 'city', '') . $showkey;
                    $url['url'] = show_region_name($data['region'], 'city', 'link');
                    $rs[] = $url;
                    if ($data['region']['city'] !== $data['region']['region']) {
                        $url['title'] = show_region_name($data['region'], 'region', '') . $showkey;
                        $url['url'] = show_region_name($data['region'], 'region', 'link');
                        $rs[] = $url;
                    }
                    if (!empty($data['subregion'])) {
                        $url['title'] = str_replace(['镇', '区'], '', $data['subregion']['areaname']) . $showkey;
                        $url['url'] = subregion_index($data['subregion']);
                        $rs[] = $url;
                    }
                    if (!empty($data['loupan'])) {
                        $url['title'] = show_loupan_info($data['loupan'], 'title');
                        $url['url'] = show_loupan_info($data['loupan'], 'link');
                        $rs[] = $url;
                    }
                    if (!empty($data['build'])) {
                        $url['title'] = show_build_info($data['build'], 'title');
                        $url['url'] = show_build_info($data['build'], 'link');
                        $rs[] = $url;
                    }
                    break;
                default :
                    $rs = $data[$infotype][$showkey];
                    break;
            }
        }
        return $rs;
    } else {
        return null;
    }
}

/**
 * 
 * @param type $model
 * @param type $infotype
 * @return type
 */
function show_build_info($model, $infotype = 'title', $byid = 0) {
    if ($byid === 1) {
        $bd = new Common\Zfmodel\ZfBuildingsModel();
        $model = $bd->getmodelbyid($model);
    }
    $infostr = "Null";
    switch ($infotype) {
        case 'jiaofan'://交房时间
            $infostr = date('Y-m-d', $model[$infotype]);
            break;
        case 'price'://
            $infostr = floor($model['monthprice']) . "元";
            break;
        case 'wuyie'://物业类型
            $infostr = getkeyname($model['wuyie'], 'wuyie_type');
            break;
        case 'rentingmod':
            $rs = getTaglist('Renttag', $model['rentingmod']);
            if ($rs['status']) {
                $infostr = "";
                foreach ($rs['list'] as $k => $v) {
                    $infostr.=$v['title'] . "、";
                }
                $infostr = rtrim($infostr, '、');
            }
            break;
        case 'title':
            $infostr = $model['title'];
            break;
        case 'housenumber':
            $infostr = $model['housenumber'];
            break;
        case 'modid':
            $infostr = getkeyname($model['modid'], 'yunyintype');
            break;
        case 'description':
            $infostr = $model['description'];
            break;
        case 'floornumber':
            $infostr = $model['floornumber'] . "层";
            break;
        case "nearest_subway":
            if ($model['nearest_subway'] > 0) {
                $infostr = "距离最近地铁站 <span style='color:red;'>{$model['nearest_subwayname']}</span>{$model['nearest_subway']}米";
            } else {
                $infostr = "附近没有地铁站";
            }
            break;
        case 'link':
            $infostr = build_detail($model, '/');
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示新闻字段
 * @param type $model
 * @param type $infotype
 * @param type $suffix
 */
function show_news_info($model, $infotype = 'title', $suffix = '') {
    $infostr = "Null";
    $nodata = showTagbyMark('no_data');
    $unknow_data = showTagbyMark('unknow_data');
    switch ($infotype) {
        case 'summary':
            $rs = getTaglist('news_tag', $model['summary']);
            if ($rs['status']) {
                $infostr = "";
                foreach ($rs['list'] as $k => $v) {
                    $infostr.="<span>{$v['title']}</span>";
                }
                $infostr = rtrim($infostr, '、');
            }
            break;
        case 'link':
            $infostr = makeNavUrl($model);
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示用户信息
 * @param type $model
 * @param type $infotype
 * @return type
 */
function show_customer_info($model, $infotype = 'title',$defalut=0) {
    if (is_number($model)) {
        $db = new \Common\Umodel\CusCustomerModel();
        $model = $db->getmodelbyid($model);
    }
    $infostr = "Null";
    switch ($infotype) {
        case 'nickname'://是否有需要回复的新评论
            $infostr = $model['nickname'];
            if (empty($infostr)) {
                $infostr = $model['username'];
            }
            $infostr = substr_cut($infostr, 3);
            break;
//        case "truename":
//            break;
        case 'thumb':
            $infostr = $model['thumb'];
            if (file_exists($infostr)) {
                return $infostr;
            } else {
                $pk = $defalut % 4 + 1;
                $infostr = "/Application/Home/View/baozhu/Public/static/images/useravatar_0{$pk}.jpg";
                return $infostr;
            }
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }

    return $infostr;
}

/**
 * 显示评论详情信息
 * @param type $model
 * @param type $infotype
 * @param type $suffix  上下文
 * @return type
 */
function show_judge_info($model, $infotype = 'title', $suffix = '') {
    $infostr = "Null";
    switch ($infotype) {
        case 'replymemo'://是否有需要回复的新评论
            if ($model['reply'] > 0) {
                $infostr = show_judgereply_info($model['reply'], 'replymemo');
            }
            break;
        case 'replytime'://是否有需要回复的新评论
            if ($model['reply'] > 0) {
                $infostr = show_judgereply_info($model['reply'], 'addtime');
            }
            break;
        case 'addtime':
            $suffix = empty($suffix) ? "Y-m-d H:i:s" : $suffix;
            $infostr = date( $suffix,$model['addtime']);
            break;
        case 'objid':
            $infostr = show_room_info($model['objid'], 'title', 1);
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示积分日志信息
 * @param type $model
 * @param type $infotype
 * @param type $suffix  前缀，附加信息
 * @return type
 */
function show_scorelog_info($model, $infotype = 'title', $suffix = '') {
    $infostr = "Null";
    switch ($infotype) {
        case 'scoretype'://是否有需要回复的新评论  
            $infostr = showTagbyMark($model[$infotype], '', 'score_taglist');
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示积分日志信息
 * @param type $model
 * @param type $infotype
 * @param type $suffix  前缀，附加信息
 * @return type
 */
function show_moneylog_info($model, $infotype = 'title', $suffix = '') {
    $infostr = "Null";
    switch ($infotype) {
        case 'moneytype'://是否有需要回复的新评论  
            $infostr = showTagbyMark($model[$infotype], '', 'money_taglist');
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 读取产器分类
 * @param type $path
 */
function getgoodscatebypath($sid_id, $path) {
    $db = new Common\Gmodel\GcateModel();
    $viewpath = $db->getviewpath();   
    $map = [$viewpath => $path, 'sid_id' => $sid_id];    
    $model = $db->getmodelbypath($map);
    return $model;
}

/**
 * 显示回复、追加评论信息
 * @param type $model
 * @param type $infotype
 * @param type $suffix  前缀，附加信息
 * @return type
 */
function show_judgereply_info($model, $infotype = 'title',$imgtemp="") {
    if (is_number($model)) {
        $db = new \Common\Zfmodel\ZfJudgereplyModel();
        $model = $db->getmodelbyid($model);
    }

    $infostr = "Null";
    switch ($infotype) {
        case 'replymemo'://是否有需要回复的新评论        
            $infostr = $model['memo'];                  
            break;
        case 'imglist'://是否有需要回复的新评论 
            $infostr = showimglist($model['imglist'],$imgtemp);
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 显示新闻字段
 * @param type $model
 * @param type $infotype
 * @param type $suffix
 */
function show_favorite_info($model, $infotype = 'title', $suffix = '') {
    switch ($model['collecttable']) {
        case 'rooms':
            $db = new Common\Zfmodel\ZfRoomsModel();
            $obj = $db->getmodelbyid($model['collectid']);
            if ($infotype == 'link') {
                return show_room_info($obj, $infotype);
            }
            break;
        case 'build':
            $db = new Common\Zfmodel\ZfBuildingsModel();
            $obj = $db->getmodelbyid($model['collectid']);
            if ($infotype == 'link') {
                return show_build_info($obj, $infotype);
            }
            break;
        case 'loupan':
            $db = new Common\Zfmodel\ZfLoupanModel();
            $obj = $db->getmodelbyid($model['collectid']);
            if ($infotype == 'link') {
                return show_loupan_info($obj, $infotype);
            }
            break;
    }
    $infostr = "Null";
    switch ($infotype) {
        default :
            $infostr = $obj[$infotype];
            break;
    }
    return $infostr . $suffix;
}

/**
 * 显示楼盘信息
 * @param type $model
 * @param type $infotype
 * @return type
 */
function show_loupan_info($model, $infotype = 'title', $suffix = '') {

    $infostr = "Null";
    $nodata = showTagbyMark('no_data');
    $unknow_data = showTagbyMark('unknow_data');
    switch ($infotype) {
        case 'jiaofan'://交房时间
            $infostr = date('Y-m-d', $model[$infotype]);
            break;
        case 'ave_rent'://月租
            $infostr = floor($model['ave_rent']);
            $infostr .=$suffix;
//            if ($infostr) {
//                $infostr .=$suffix;
//            } else {
//                $infostr = $nodata;
//            }
            break;
        case 'ave_price'://房价
            $infostr = floor($model['ave_price']);
            $infostr .=$suffix;
//            if ($infostr) {
//                $infostr .=$suffix;
//            }
//             else {
//                $infostr = $nodata;
//            }
            break;
        case 'wuyielei'://物业类型
            $infostr = getkeyname($model['wuyielei'], 'wuyie_type');
            break;
        case 'tag':
            $rs = getTaglist('loupan_tag', $model['tag']);
            if ($rs['status']) {
                $infostr = "";
                foreach ($rs['list'] as $k => $v) {
                    $infostr.=$v['title'] . "、";
                }
                $infostr = rtrim($infostr, '、');
            }
            break;
        case "nearest_subway":
            if ($model['nearest_subway'] > 0) {
                $infostr = "距离最近地铁站 <span style='color:red;'>{$model['nearest_subwayname']}</span>{$model['nearest_subway']}米";
            } else {
                $infostr = "附近没有地铁站";
            }
            break;
        case 'link':
            $infostr = loupan_detail($model, '/');
            break;
        default :
            $infostr = $model[$infotype];
            break;
    }
    return $infostr;
}

/**
 * 行政区域编码处理
 * @param type $v
 * @param type $key
 */
function trance_regioncode($v, $key = 'cgb') {
    $code = $v[$key];
    switch ($key) {
        case 'cgb':
            $code = str_replace('00', '', $code);
            $code = str_replace('00', '', $code);
            if ($code < 100) {
                $code .="01";
            }
            $code .="%";
            break;
        case 'pgb':
            $code = str_replace('0000', '', $code) . "%";
            break;
//        case 'areacode':
//            $code .="%";
//            break;
    }
    return $code;
}

/**
 * 显示时间格式
 * @param type $dtime
 * @param type $frmat
 * @return type
 */
function trancetime($dtime, $frmat = 'Y-m-d') {
    return date($frmat, $dtime);
}

/**
 * 数据编码
 * @param type $plain_text
 * @param type $key
 * @return type
 */
function jp_encrypt($string, $key = '') {
    $skey = C('COOKIE_KEY');
//    $plain_text = trim($plain_text);
//    $iv = substr(md5($key), 0, mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB));
//    $c_t = mcrypt_cfb(MCRYPT_CAST_256, $key, $plain_text, MCRYPT_ENCRYPT, $iv);
//    return trim(chop(base64_encode($c_t)));
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 数据解密
 * 原名decrypt  因与alipay sdk重名而修
 * @param type $c_t
 * @param type $key
 * @return type
 */
function jp_decrypt($string, $key = '') {
    $skey = C('COOKIE_KEY');
//    $c_t = trim(chop(base64_decode($c_t)));
//    $iv = substr(md5($key), 0, mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB));
//    $p_t = mcrypt_cfb(MCRYPT_CAST_256, $key, $c_t, MCRYPT_DECRYPT, $iv);
//    return trim(chop($p_t));
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'cxphp') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string = '', $skey = 'cxphp') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

/**
 * 字符串分割成指定长度的数组，应对超长文本存储问题
 * @param type $string
 * @param type $len
 * @return type
 */
function mbStrSplit($string, $len = 500) {
    $start = 0;
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string, $start, $len, "utf8");
        $string = mb_substr($string, $len, $strlen, "utf8");
        $strlen = mb_strlen($string);
    }
    return $array;
}

/**
 * 读取用户字段
 * @param type $m_id
 * @param type $f
 * @return type
 */
function getadminfiled($m_id, $f) {
    $db = new \Common\Model\AdminModel();
    $model=$db->getmodelbyid($m_id);
    return $model[$f];
}

/**
 * 测试字段为空
 * @param type $str
 * @return type
 */
function checkempety($str) {
    $str = trim($str);
    return empty($str);
}

/**
 * 字段为空时返回一个空格
 * @param type $str
 * @return string
 */
function replaceempety($str) {
    if (checkempety($str)) {
        return '';
    } else {
        return $str;
    }
}

/**
 * 字段为空时返回一个空格
 * @param type $str
 * @return string
 */
function replacezero($str) {
    if (checkempety($str)) {
        return 0;
    } else {
        return $str;
    }
}

/**
 * 还原html编码
 * @param type $data
 * @return type
 */
function htmldecode($data) {
    return htmlspecialchars_decode(stripslashes($data), ENT_QUOTES);
}

/**
 * 简单测试字符串是否不为文件名
 * @param type $filename
 * @return type
 */
function checknotisfile($filename) {
    $arr = explode('.', $filename);
    if (count($arr) == 2) {
        return false;
    } else {
        return true;
    }
}

/*
 * 返回新闻实时点击数
 */

function showhits($model) {
    $sc .="<script type=\"text/javascript\">
    var cid='{$model['id']}n{$model['colid']}';
   </script><span id=\"hitcounter\">{$model['hits']}</span>";
    return $sc;
}

/*
 * 返回产品实时点击数
 */

function showgoodshits($model) {
    $sc .="<script type=\"text/javascript\">  
    var cid='{$model['id']}n{$model['cat_id']}';
   </script><span id=\"goodshitcounter\">{$model['click_count']}</span>";
    return $sc;
}

/*
 * 返回实时点击赞数
 */

function showflowers($model) {
    $url = U('Common/flowers');
    $sc .="<script type=\"text/javascript\">
    var flowersurl='{$url}';
    var cid='{$model['id']}n{$model['colid']}';
   </script><span id=\"flowerscounter\">{$model['flowers']}</span>";
    return $sc;
}

/**
 * 清除格式
 * @param type $descclear
 * @return type
 */
function clearformat($descclear) {
    $descclear = htmlspecialchars_decode($descclear);

    $descclear = str_replace("\r", "", $descclear); //过滤换行
    $descclear = str_replace("\n", "", $descclear); //过滤换行
    $descclear = str_replace("\t", "", $descclear); //过滤换行
    $descclear = str_replace("\r\n", "", $descclear); //过滤换行
    $descclear = preg_replace("/\s+/", " ", $descclear); //过滤多余回车
    $descclear = preg_replace("/<[ ]+/si", "<", $descclear); //过滤<__("<"号后面带空格)
    $descclear = preg_replace("/<\!--.*?-->/si", "", $descclear); //过滤html注释
    $descclear = preg_replace("/<(\!.*?)>/si", "", $descclear); //过滤DOCTYPE
    $descclear = preg_replace("/<(\/?html.*?)>/si", "", $descclear); //过滤html标签
    $descclear = preg_replace("/<(\/?head.*?)>/si", "", $descclear); //过滤head标签
    $descclear = preg_replace("/<(\/?meta.*?)>/si", "", $descclear); //过滤meta标签
    $descclear = preg_replace("/<(\/?body.*?)>/si", "", $descclear); //过滤body标签
    $descclear = preg_replace("/<(\/?link.*?)>/si", "", $descclear); //过滤link标签
    $descclear = preg_replace("/<(\/?form.*?)>/si", "", $descclear); //过滤form标签
    $descclear = preg_replace("/cookie/si", "COOKIE", $descclear); //过滤COOKIE标签
    $descclear = preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si", "", $descclear); //过滤applet标签
    $descclear = preg_replace("/<(\/?applet.*?)>/si", "", $descclear); //过滤applet标签
    $descclear = preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si", "", $descclear); //过滤style标签
    $descclear = preg_replace("/<(\/?style.*?)>/si", "", $descclear); //过滤style标签
    $descclear = preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si", "", $descclear); //过滤title标签
    $descclear = preg_replace("/<(\/?title.*?)>/si", "", $descclear); //过滤title标签
    $descclear = preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si", "", $descclear); //过滤object标签
    $descclear = preg_replace("/<(\/?objec.*?)>/si", "", $descclear); //过滤object标签
    $descclear = preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si", "", $descclear); //过滤noframes标签
    $descclear = preg_replace("/<(\/?noframes.*?)>/si", "", $descclear); //过滤noframes标签
    $descclear = preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si", "", $descclear); //过滤frame标签
    $descclear = preg_replace("/<(\/?i?frame.*?)>/si", "", $descclear); //过滤frame标签
    $descclear = preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si", "", $descclear); //过滤script标签
    $descclear = preg_replace("/<(\/?script.*?)>/si", "", $descclear); //过滤script标签
    $descclear = preg_replace("/javascript/si", "Javascript", $descclear); //过滤script标签
    $descclear = preg_replace("/vbscript/si", "Vbscript", $descclear); //过滤script标签
    $descclear = preg_replace("/on([a-z]+)\s*=/si", "On\\1=", $descclear); //过滤script标签
    $descclear = preg_replace("/&#/si", "&＃", $descclear); //过滤script标签，如javAsCript:alert();
//使用正则替换
    $pat = "/<(\/?)(script|i?frame|style|html|body|li|i|map|title|img|link|span|u|font|table|tr|b|marquee|td|strong|div|a|meta|\?|\%)([^>]*?)>/isU";
    $descclear = preg_replace($pat, "", $descclear);
    return $descclear;
}

//设置当前参数
function setcurentjp($model, $key = 'curentjpid') {
    $id = json_encode($model);
    $mid = jp_encrypt($id . 'rdm9527');
    session($key, $mid);
}

/**
 * 取当前参数
 * @param type $key
 * @return type
 */
function getcurentjp($key = 'curentjpid') {
    $mid = session($key);
    $mid = jp_ecrypt($mid);
    $x = str_replace('rdm9527', '', $mid);
    return json_decode($x, true);
}

/**
 * 设置更新标记
 * @param type $htmlpath  站点缓存目录
 * @param type $mark  更新标记
 * @return type
 */
function makeclearmark($htmlpath, $mark = 'clearall') {
    if (empty($htmlpath))
        return;
    $sidroot = $htmlpath;
    $htmlpath = "Html/{$htmlpath}/";
    $catchmark = F('catchmark/catchmark');
    $m = array($htmlpath, 1, 1, 1);
    $catchmark[$sidroot . '-' . $mark] = $m;
    F('catchmark/catchmark', $catchmark);
}

/**
 * @param $arr
 * @param $key_name
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名 
 */
function convert_arr_key($arr, $key_name) {
    $arr2 = array();
    foreach ($arr as $key => $val) {
        $arr2[$val[$key_name]] = $val;
    }
    return $arr2;
}

/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method, $postfields = null, $headers = array(), $debug = false) {
    $method = strtoupper($method);
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
    curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case "POST":
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
            }
            break;
        default:
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
            break;
    }
    $ssl = preg_match('/^https:\/\//i', $url) ? TRUE : FALSE;
    curl_setopt($ci, CURLOPT_URL, $url);
    if ($ssl) {
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
    }
    //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ci, CURLOPT_MAXREDIRS, 2); /* 指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的 */
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
    /* curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
    $response = curl_exec($ci);
    $requestinfo = curl_getinfo($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);
        echo "=====info===== \r\n";
        print_r($requestinfo);
        echo "=====response=====\r\n";
        print_r($response);
    }
    curl_close($ci);
    return $response;
    //return array($http_code, $response,$requestinfo);
}

function sendMail($to, $title, $content) {
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    
    $cfg = getConfig();
    $mail->Host = $cfg['mail_host']; //smtp服务器的名称（这里以QQ邮箱为例）
    $smtpauth = false;
    if ($cfg['mail_smtpauth'] == 1) {
        $smtpauth = true;
    }
    $mail->SMTPAuth = $smtpauth; //启用smtp认证
    $mail->Username = $cfg['mail_username']; //你的邮箱名
    $mail->Password = $cfg['mail_password']; //邮箱密码
    $mail->From = $cfg['mail_from']; //发件人地址（也就是你的邮箱地址）
    $mail->FromName = $cfg['mail_fromname']; //发件人姓名
    $mail->AddAddress($to, "尊敬的用户");
    $mail->WordWrap = 50; //设置每行字符长度
    $html = false;
    if ($cfg['mail_ishtml'] == 1) {
        $html = true;
    }
    $mail->IsHTML($html); // 是否HTML格式邮件
    $mail->CharSet = $cfg['mail_charset']; //设置邮件编码
    $mail->Subject = $title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = ""; //邮件正文不支持HTML的备用显示
//    return $cfg;
//    echo  $cfg['mail_username'];
    return($mail->Send());
}

/**
 * 
 * @param type $from 发件人地址
 * @param type $fromname 发件人姓名
 * @param type $message 邮件内容
 * @param type $to  收件人
 * @param type $title  邮件标题
 * @return type
 */
function leavingMessage($data) {
    Vendor('PHPMailer.PHPMailerAutoload');
    $title = empty($data['title']) ? "用户留言-" . date("Y-m-d h:i") : $data['title'];
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host = C('MAILER.MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C('MAILER.MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAILER.MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAILER.MAIL_PASSWORD'); //邮箱密码
    $mail->From = $data['from']; //发件人地址（也就是你的邮箱地址）
    $mail->FromName = $data['fromname']; //发件人姓名
    $mail->AddAddress($data['to'], "亲爱的朋友");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAILER.MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet = C('MAILER.MAIL_CHARSET'); //设置邮件编码
    $mail->Subject = $title; //邮件主题
    $mail->Body = nl2br($data['message']); //邮件内容
    $mail->AltBody = ""; //邮件正文不支持HTML的备用显示
    $s = $mail->Send();
    if ($s) {
        $rs = array('status' => 0, 'mail_success！');
    } else {
        $rs = array('status' => 1, 'mail_failure！');
    }
    return $s;
}

function ubuntumail() {
    try {
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new PHPMailer(true); //New instance, with exceptions enabled

        $body ="13355716451@163.com"; //Strip backslashes
        // $body="asdasdasdasdasd";

        $mail->IsSMTP();                           // tell the class to use SMTP
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->Port = 25;                    // set the SMTP server port
        $mail->Host =  "13355716451@163.com"; // 如果是qq  为smtp.qq.com
        $mail->Username = "13355716451@163.com";     // SMTP  username用户名
        $mail->Password = "123456789a";            // SMTP server password密码(不是自己登录邮箱的密码是开通SMTP时自己重新设定的那个密码)
        //$mail->IsSendmail();  // tell the class to use Sendmail

        $mail->AddReplyTo("123456789a", "First Last");

        $mail->From = "8888888@163.com";
        $mail->FromName = "哈哈哈哈哈哈哈";

        $to = "179911014@qq.com";

        $mail->AddAddress($to);

        $mail->Subject = "First PHPMailer Message";

        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->WordWrap = 80; // set word wrap

        $mail->MsgHTML($body);

        $mail->IsHTML(true); // send as HTML

        $mail->Send();
        echo 'Message has been sent.';
    } catch (phpmailerException $e) {
        echo $e->errorMessage();
    }
}

/**
 * 给用户发送密码找回邮件
 * @param type $config
 * @param type $getpassword  
 * @param type $username
 */
function getPasswordMail($config, $getpassword, $username, $to, $title) {
    $imgpath = "{$config["web_site"]}/Public/images";
    $headerpic = $config["web_site"] . $config['mailer_header'];
    $mailbody = <<<STR
                
                <div id="contentDiv" onmouseover="getTop().stopPropagation(event);" onclick="getTop().preSwapLink(event, 'html', 'ZC4708-6oz5jbTf_0ZnUFoCT~VAH6b');" style="position:relative;font-size:14px;height:auto;padding:15px 15px 10px 15px;z-index:1;zoom:1;line-height:1.7;" class="body">    <div id="qm_con_body"><div id="mailContentContainer" class="qmbox qm_con_body_content qqmail_webmail_only">
<style>
	.mmsgLetter				{ 	width:580px;margin:0 auto;padding:10px;color:#333;background:#fff;border:0px solid #aaa;border:1px solid #aaa\9;border-radius:5px;-webkit-box-shadow:3px 3px 10px #999;-moz-box-shadow:3px 3px 10px #999;box-shadow:3px 3px 10px #999;font-family:Verdana, sans-serif; }
	.mmsgLetter a:link,
	.mmsgLetter a:visited 	{	color:#407700; }  
	.mmsgLetterContent 		{	text-align:left;padding:30px;font-size:14px;line-height:1.5;
								background:url({$imgpath}/mmsgletter_2_bg_wmark.jpg) no-repeat top right; }
	.mmsgLetterContent h3	{ 	color:#000;font-size:20px;font-weight:bold; 
								margin:20px 0 20px;border-top:2px solid #eee;padding:20px 0 0 0;
								font-family:"微软雅黑","黑体", "Lucida Grande", Verdana, sans-serif;}
	.mmsgLetterContent p 	{	margin:20px 0;padding:0; }
	.mmsgLetterContent .salutation 		{ font-weight:bold;}
	.mmsgLetterContent .mmsgMoreInfo 	{ }
	.mmsgLetterContent a.mmsgButton	 	{	display:block;float:left;height:40px;text-decoration:none;text-align:center;cursor:pointer;}
	.mmsgLetterContent a.mmsgButton	span 	{	display:block;float:left;padding:0 25px;height:40px;line-height:36px;font-size:14px;font-weight:bold;color:#fff;text-shadow:1px 0 0 #235e00;}
	
	.mmsgLetterContent a.mmsgButton:link,
	.mmsgLetterContent a.mmsgButton:visited { background:#338702 url({$imgpath}/mmsgletter_2_btn.png) no-repeat right -40px; }
	
	.mmsgLetterContent a.mmsgButton:link span,
	.mmsgLetterContent a.mmsgButton:visited span { background:url({$imgpath}/mmsgletter_2_btn.png) no-repeat 0 0; }
	
	.mmsgLetterContent a.mmsgButton:hover,
	.mmsgLetterContent a.mmsgButton:active { background:#338702 url({$imgpath}/mmsgletter_2_btn.png) no-repeat right -120px; }
	
	.mmsgLetterContent a.mmsgButton:hover span,
	.mmsgLetterContent a.mmsgButton:active span { background:url({$imgpath}/mmsgletter_2_btn.png) no-repeat 0 -80px; }
	
	.mmsgLetterInscribe 	{	padding:40px 0 0;}
	.mmsgLetterInscribe .mmsgAvatar	{	float:left; }
	.mmsgLetterInscribe .mmsgName	{ margin:0 0 10px; }
	.mmsgLetterInscribe .mmsgSender	{ margin:0 0 0 54px;}
	.mmsgLetterInscribe .mmsgInfo	{ font-size:12px;margin:0;line-height:1.2; }
	
	.mmsgLetterHeader		{	height:23px;background:url({$imgpath}/mmsgletter_2_bg_topline.png) repeat-x 0 0; }
	.mmsgLetterFooter 		{	margin:16px;text-align:center;font-size:12px;color:#888;
								text-shadow:1px 0px 0px #eee;}
	.mmsgLetterClr { clear:both;overflow:hidden;height:1px; }
	
	
	 .mmsgLetterUser { padding:10px 0; }
	 .mmsgLetterUserItem { padding:0 0 20px 0;}
	 .mmsgLetterUserAvatar { height:40px;border:1px solid #ccc;padding:2px;display:block;float:left; }
	 .mmsgLetterUserAvatar img { width:40px;height:40px; }
	 .mmsgLetterInfo { margin-left:48px; }
	 .mmsgLetterName { display:block;color:#5fa207;font-weight:bold;margin-left:10px; }
	 .mmsgLetterDesc { font-size:12px;float:left;height:43px;background:url({$imgpath}/mmsgletter_chat_right.gif) no-repeat right top; }
	 .mmsgLetterDesc div{ white-space:nowrap;float:left;height:43px;padding:0 20px;line-height:40px;background:url({$imgpath}/mmsgletter_chat_left.gif) no-repeat left top; }
	 
	 .mmsgLetterUser {}
	 .mmsgLetterAvatar { float:left;}
	 .mmsgLetterInfo { margin:0 0 0 60px; }
	 .mmsgLetterNickName { font-size:14px;font-weight:bold;}
	 .mmsgLetterUin { font-size:12px;color:#666;}
	 
</style>

<div style="background-color:#d0d0d0;background-image:url({$imgpath}/mmsgletter_2_bg.png);text-align:center;padding:40px;">
	<div class="mmsgLetter" style="width:580px;margin:0 auto;padding:10px;color:#333;background-color:#fff;border:0px solid #aaa;border-radius:5px;-webkit-box-shadow:3px 3px 10px #999;-moz-box-shadow:3px 3px 10px #999;box-shadow:3px 3px 10px #999;font-family:Verdana, sans-serif; ">

		<div class="mmsgLetterHeader" style="height:23px;background:url({$imgpath}/mmsgletter_2_bg_topline.png) repeat-x 0 0;">
			
		</div>
		<div class="mmsgLetterContent" style="text-align:left;padding:30px;font-size:14px;line-height:1.5;background:url({$imgpath}/fake.png) no-repeat top right;">
		
			<div>
			
				<p class="salutation" style="font-weight:bold;">
					Hi,<span id="mailUserName">{$username}</span>：
				</p>
				 <p>忘记您的密码了吗？别着急，请点击以下链接，我们协助您找回密码：<br>
<a href="{$getpassword}" style="word-break:break-all;word-wrap:break-word;display:block" target="_blank">{$getpassword}</a><br><br>如果这不是您的邮件请忽略，很抱歉打扰您，请原谅。</p>
			</div>	

			<div class="mmsgLetterInscribe" style="padding:40px 0 0;">
				<img class="mmsgAvatar" src="{$headerpic}" style="float:left; width:40px;">
				<div class="mmsgSender" style="margin:0 0 0 54px;">
					<p class="mmsgName" style="margin:0 0 10px;">{$config["mailer_manager"]}</p>
					<p class="mmsgInfo" style="font-size:12px;margin:0;line-height:1.2;">
						{$config["mailer_said"]}<br>
						<a href="mailto:{$config["mailer_mail"]}" style="color:#407700;" target="_blank">{$config["mailer_mail"]}</a>
					</p>
				</div>
			</div>
			
		</div>
	
		<div class="mmsgLetterFooter" style="margin:16px;text-align:center;font-size:12px;color:#888;text-shadow:1px 0px 0px #eee;">
		
			
		</div>
	</div>
	
	
</div>
<style type="text/css">.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}</style></div></div><!-- --><style>#mailContentContainer .txt {height:auto;}</style>  </div>
STR;
    $rs = sendMail($to, $title, $mailbody);
    return $rs;
}


/**
 * 验证用户身份邮箱
 * @param type $config
 * @param type $getpassword  
 * @param type $username
 */
function checkMail($config, $body, $username, $to, $title) {
    $imgpath = "{$config["web_site"]}/Public/images";
    $headerpic = $config["web_site"] . $config['mailer_header'];
    $mailbody = <<<STR
                
                <div id="contentDiv" onmouseover="getTop().stopPropagation(event);" onclick="getTop().preSwapLink(event, 'html', 'ZC4708-6oz5jbTf_0ZnUFoCT~VAH6b');" style="position:relative;font-size:14px;height:auto;padding:15px 15px 10px 15px;z-index:1;zoom:1;line-height:1.7;" class="body">    <div id="qm_con_body"><div id="mailContentContainer" class="qmbox qm_con_body_content qqmail_webmail_only">
<style>
	.mmsgLetter				{ 	width:580px;margin:0 auto;padding:10px;color:#333;background:#fff;border:0px solid #aaa;border:1px solid #aaa\9;border-radius:5px;-webkit-box-shadow:3px 3px 10px #999;-moz-box-shadow:3px 3px 10px #999;box-shadow:3px 3px 10px #999;font-family:Verdana, sans-serif; }
	.mmsgLetter a:link,
	.mmsgLetter a:visited 	{	color:#407700; }  
	.mmsgLetterContent 		{	text-align:left;padding:30px;font-size:14px;line-height:1.5;
								background:url({$imgpath}/mmsgletter_2_bg_wmark.jpg) no-repeat top right; }
	.mmsgLetterContent h3	{ 	color:#000;font-size:20px;font-weight:bold; 
								margin:20px 0 20px;border-top:2px solid #eee;padding:20px 0 0 0;
								font-family:"微软雅黑","黑体", "Lucida Grande", Verdana, sans-serif;}
	.mmsgLetterContent p 	{	margin:20px 0;padding:0; }
	.mmsgLetterContent .salutation 		{ font-weight:bold;}
	.mmsgLetterContent .mmsgMoreInfo 	{ }
	.mmsgLetterContent a.mmsgButton	 	{	display:block;float:left;height:40px;text-decoration:none;text-align:center;cursor:pointer;}
	.mmsgLetterContent a.mmsgButton	span 	{	display:block;float:left;padding:0 25px;height:40px;line-height:36px;font-size:14px;font-weight:bold;color:#fff;text-shadow:1px 0 0 #235e00;}
	
	.mmsgLetterContent a.mmsgButton:link,
	.mmsgLetterContent a.mmsgButton:visited { background:#338702 url({$imgpath}/mmsgletter_2_btn.png) no-repeat right -40px; }
	
	.mmsgLetterContent a.mmsgButton:link span,
	.mmsgLetterContent a.mmsgButton:visited span { background:url({$imgpath}/mmsgletter_2_btn.png) no-repeat 0 0; }
	
	.mmsgLetterContent a.mmsgButton:hover,
	.mmsgLetterContent a.mmsgButton:active { background:#338702 url({$imgpath}/mmsgletter_2_btn.png) no-repeat right -120px; }
	
	.mmsgLetterContent a.mmsgButton:hover span,
	.mmsgLetterContent a.mmsgButton:active span { background:url({$imgpath}/mmsgletter_2_btn.png) no-repeat 0 -80px; }
	
	.mmsgLetterInscribe 	{	padding:40px 0 0;}
	.mmsgLetterInscribe .mmsgAvatar	{	float:left; }
	.mmsgLetterInscribe .mmsgName	{ margin:0 0 10px; }
	.mmsgLetterInscribe .mmsgSender	{ margin:0 0 0 54px;}
	.mmsgLetterInscribe .mmsgInfo	{ font-size:12px;margin:0;line-height:1.2; }
	
	.mmsgLetterHeader		{	height:23px;background:url({$imgpath}/mmsgletter_2_bg_topline.png) repeat-x 0 0; }
	.mmsgLetterFooter 		{	margin:16px;text-align:center;font-size:12px;color:#888;
								text-shadow:1px 0px 0px #eee;}
	.mmsgLetterClr { clear:both;overflow:hidden;height:1px; }
	
	
	 .mmsgLetterUser { padding:10px 0; }
	 .mmsgLetterUserItem { padding:0 0 20px 0;}
	 .mmsgLetterUserAvatar { height:40px;border:1px solid #ccc;padding:2px;display:block;float:left; }
	 .mmsgLetterUserAvatar img { width:40px;height:40px; }
	 .mmsgLetterInfo { margin-left:48px; }
	 .mmsgLetterName { display:block;color:#5fa207;font-weight:bold;margin-left:10px; }
	 .mmsgLetterDesc { font-size:12px;float:left;height:43px;background:url({$imgpath}/mmsgletter_chat_right.gif) no-repeat right top; }
	 .mmsgLetterDesc div{ white-space:nowrap;float:left;height:43px;padding:0 20px;line-height:40px;background:url({$imgpath}/mmsgletter_chat_left.gif) no-repeat left top; }
	 
	 .mmsgLetterUser {}
	 .mmsgLetterAvatar { float:left;}
	 .mmsgLetterInfo { margin:0 0 0 60px; }
	 .mmsgLetterNickName { font-size:14px;font-weight:bold;}
	 .mmsgLetterUin { font-size:12px;color:#666;}
	 
</style>

<div style="background-color:#d0d0d0;background-image:url({$imgpath}/mmsgletter_2_bg.png);text-align:center;padding:40px;">
	<div class="mmsgLetter" style="width:580px;margin:0 auto;padding:10px;color:#333;background-color:#fff;border:0px solid #aaa;border-radius:5px;-webkit-box-shadow:3px 3px 10px #999;-moz-box-shadow:3px 3px 10px #999;box-shadow:3px 3px 10px #999;font-family:Verdana, sans-serif; ">

		<div class="mmsgLetterHeader" style="height:23px;background:url({$imgpath}/mmsgletter_2_bg_topline.png) repeat-x 0 0;">
			
		</div>
		<div class="mmsgLetterContent" style="text-align:left;padding:30px;font-size:14px;line-height:1.5;background:url({$imgpath}/fake.png) no-repeat top right;">
		
			<div>
			
				{$body}
			</div>	

			<div class="mmsgLetterInscribe" style="padding:40px 0 0;">
				<img class="mmsgAvatar" src="{$headerpic}" style="float:left; width:40px;">
				<div class="mmsgSender" style="margin:0 0 0 54px;">
					<p class="mmsgName" style="margin:0 0 10px;">{$config["mailer_manager"]}</p>
					<p class="mmsgInfo" style="font-size:12px;margin:0;line-height:1.2;">
						{$config["mailer_said"]}<br>
						<a href="mailto:{$config["mailer_mail"]}" style="color:#407700;" target="_blank">{$config["mailer_mail"]}</a>
					</p>
				</div>
			</div>
			
		</div>
	
		<div class="mmsgLetterFooter" style="margin:16px;text-align:center;font-size:12px;color:#888;text-shadow:1px 0px 0px #eee;">
		
			
		</div>
	</div>
	
	
</div>
<style type="text/css">.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}</style></div></div><!-- --><style>#mailContentContainer .txt {height:auto;}</style>  </div>
STR;
    $rs = sendMail($to, $title, $mailbody);
    return $rs;
}

/**
 * 保存当前语种
 * @param type $mid
 */
function setlang($lang) {
    cookie('CURENT_LANG', $lang);
}

/**
 * 获取管理员ID
 * @return type
 */
function getlang() {
    $lang = cookie('CURENT_LANG');
    if (empty($lang)) {
        return 'cn';
    } else {
        return $lang;
    }
}

/**
 * 获取当前url
 * @return type
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 导航比较
 * @param type $model
 * @param type $groupmark
 * @return string
 */
function compareq($model, $groupmark) {
    return comparenav($groupmark, $model['groupmark'], 'on');
}

/**
 * 字符串比较
 * @param type $cp1
 * @param type $cp2  
 * @param type $cpvalue
 * @return type
 */
function comparenav($cp1, $cp2, $cpvalue) {
    $arr = explode(',', $cp2);
    foreach ($arr as $v) {
        $cp = strcasecmp($cp1, $v);
        if ($cp === 0) {
            return $cpvalue;
        }
    }
}

/**
 * 字符串比较 
 * @param type $cp1 测试字段
 * @param type $cp2 测试条件
 * @param type $inshow 匹配输出
 * @param type $outshow 不匹配输出
 * @return type
 */
function compare_hasin($cp1, $cp2, $inshow = "", $outshow = "") {
    $cp1 = strtolower($cp1);
    $cp2 = strtolower($cp2);
    $arr = explode(',', $cp2);
    if (in_array($cp1, $arr)) {
        return $inshow;
    } else {
        return $outshow;
    }
}

/**
     * 生成详情路径
     * @param type $id
     * @param type $showpath
     * @return type
     */
    function showdetail($id, $showpath) {
        $pram = get_langue_parm();
        if (!empty($id)) {
            $pram['id'] = passport_encrypt($id, "rdng871#s");
        }
        return U($showpath, $pram);
    }

/**
 * 单项html控件显示
 * @param type $selected  数据实例
 * @param type $setkey    控件配置数组 config
 * @param type $name_css  , 分割传递多个参数  0 字段名  1 控件附加样式表
 * @param type $map     数据源映射条件
 * @param type $extvalue     控件预定义值
 * @param type $filter     数据源过滤条件
 * @return type
 */
function showhtmlcontrol($selected, $setkey, $name_css, $map = '', $extvalue = null, $filter = null) {
    $a = C($setkey);
    $x = explode(',', $name_css);
    $name = $x[0];
    if (count($x) > 1) {
        $cssclass = $x[1];
    }
    $model = $a[$name];

    $model['parentkey'] = isset($model['parentkey']) ? $model['parentkey'] : $setkey;
    $model['titletag'] = isset($model['titletag']) ? $model['titletag'] : $name;

    $val = $selected[$name];
    if (!empty($extvalue)) {
        $val = $extvalue;
    }

    if (!empty($filter)) {
        $model['source']['filter'] = $filter;
    }
    
    $rs = htmlhelper::make_htmlcontorl($model, $name, false, $val, $map, $cssclass);
    return $rs['content'];
}

/**
 * 显示帐号绑定信息
 * @param type $data
 * @param type $oauth
 */
function show_oauth_info($data, $oauth, $filed = 'nickname') {
    if (isset($data[$oauth])) {
        $rs = $data[$oauth];
        $rs['bind'] = 1;
    } else {
        $rs = array('nickname' => '未绑定');
        $rs['bind'] = 0;
    }
    $key = strtolower($oauth);
    switch ($key) {
        case "qq"://QQ
            $rs['bindurl'] = "";
            $rs['name'] = "QQ";
            $rs['pic'] = "login/qq.png";
            break;
        case 'sina'://新浪
            $rs['bindurl'] = "";
            $rs['name'] = "新浪";
            $rs['pic'] = "login/xinlang.png";
            break;
        case 'ali'://阿里
            $rs['bindurl'] = "";
            $rs['name'] = "支付宝";
            $rs['pic'] = "login/ali.png";
            break;
        case 'weixin':
            $rs['bindurl'] = "";
            $rs['name'] = "微信";
            $rs['pic'] = "login/weixin.png";
            break;
        default :
            break;
    }
    return $rs[$filed];
}
