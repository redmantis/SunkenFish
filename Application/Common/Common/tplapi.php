<?php
/**
 * 显示新闻列表
 * @param type $sid_id      站点ID
 * @param type $viewlist    栏目列表
 * @param type $size        读取条数
 * @param type $skip        指针移动
 * @return type
 */
function getnewlist($sid_id, $viewlist, $size, $skip = 0) {
    $map['sid_id'] = $sid_id;
    $map['viewpath'] = array('in', $viewlist);
    $db = new Common\News\ColumnsModel();
    $collist = $db->where($map)->getField('id',true);
    $mp['collist']=$collist;
    $rs = getnewslistbycolid($sid_id,$mp, $size, $skip);  
    return $rs;
}

/**
 * 概据栏目ID列表读取数据
 * @param type $sid_id  站点ID
 * @param type $mp 栏目ID列表
 * @param type $size    读取条数
 * @param type $skip    指针移动
 */
function getnewslistbycolid($sid_id, $mp, $size, $skip = 0) {
    $db = new Common\News\NewsModel();
    $map['sid_id'] = $sid_id;
    $map['isshow'] = 1;
    $map['isdel'] = 0;
    $map['ispost'] = 0;
    
    if (isset($mp['collist'])) {
        $map['colid'] = array('in', $mp['collist']);
    }
    if (isset($mp['tag'])) {
        $map['summary'] = array('like', "%{$mp['tag']}%");
    }

    if (isset($mp['key'])) {
        $map['page_title'] = array('like', "%{$mp['key']}%");
    }

    if (isset($mp['ishot'])) {
        $map['ishot'] = $mp['ishot'];
    }
    
    if (isset($mp['isvouch'])) {
        $map['isvouch'] = $mp['isvouch'];
    }
    if (isset($mp['selectlangue'])) {
        $map['selectlangue'] = $mp['selectlangue'];
    }    
    
    return $db->get_list($map, $skip, $size);
}

/**
 * 概据栏目ID列表读取数据
 * @param type $sid_id  站点ID
 * @param type $mp 查询条件
 * @param type $size    读取条数
 * @param type $skip    指针移动
 */
function getgoodslistbycatid($sid_id, $mp, $size, $skip = 0) {
 
    $db = new \Common\Gmodel\GoodsModel();
    $map['sid_id'] = $sid_id;
    $map['is_on_sale'] = 1;
    if (isset($mp['collist'])) {
        $map['cat_id'] = array('in', $mp['collist']);
    }
    if (isset($mp['tag'])) {
        $map['summary'] = array('like', "%{$mp['tag']}%");
    }
    
    if (isset($mp['key'])) {
        $map['title'] = array('like', "%{$mp['key']}%");
    }
    
    $rs['count'] = $db->getcount($map);
    $rs['list'] = $db->getlist($map, $skip, $size);
    $rs['status']=1;    
    return $rs;
}

function showjumpandflash(){
    $status = cookie('jumpandflash');
    $checked = "";
    if ($status == 'true') {
        $checked = "checked";
    }
    $str="<input type='checkbox' atr='{$status}' id='jumpandflash' lay-skin='switch' lay-filter='jumpandflash' lay-text='自动|手动' tips='提交后自动刷新数据' {$checked}>";
    return $str;
}

/**
 * 读取广告列表
 * @param type $sid_id
 * @param type $position
 * @return type
 */
function getadlist($sid_id, $position, $position_mark = '') {
    $db = new \Common\Model\AdModel();
    $map['sid_id'] = $sid_id;
    $map['isshow'] = 1;
    $map['position_id'] = $position;
    if (!empty($link_email)) {
        $map['position_mark'] = $position_mark;
    }
    return $db->getlist($map);
}

/**
 * 生成站点菜单
 * @param type $sid_id  站点
 * @param type $menutype 菜单类型
 */
function creatMenu($sid_id, $menutype, $pmap = null) {
    /* 获取导航 */
    $db = new \Common\Model\MenuModel();
    $menumap['sid_id'] = $sid_id;
    $menumap['menutype'] = $menutype;
    $menumap['isshow'] = 1;
    $menumap['shownav'] = array('gt', 0);
    $pcnav = $db->getTree($menumap, $pmap);
    foreach ($pcnav as $key=>$val){
        $pcnav[$key]['title']= clearpre($val['title']);
}
    $dtree = genTree9($pcnav);
    return $dtree;
}

/**
 * 产品列表链接
 * @param type $model
 * @param type $backmod
 * @param type $class
 * @return type
 */
function getGoodsCateUrl($model, $backmod = 1, $class = '', $sid_id = 2) {
    if (!is_array($model)) {
        $db = new Common\Gmodel\GcateModel();
        if (is_number($model)) {
            $model = $db->getmodelbyid($model);
        } else {
            $model = $db->getmodelbypath($model, $sid_id);
        }
    }
    $title = $model['title'];
    $title = clearpre($title);
    $pram = get_langue_parm();
    $pram['mod']=$model['viewpath'];
    $href = U("/product/index", $pram);
    switch ($backmod) {
        case '0':
            $url = "<a href=\"{$href}\"  class=\"$class\">{$title}</a>";
            break;
        case '1':
            $url = $href;
            break;
        case '2':
            $url = $title;
            break;
    }
    return $url;
}

/**
 * 根据栏目路径获取链接
 * @param type $sid_id
 * @param type $view
 * @param type $backmod
 * @param type $class
 * @param type $fomart
 * @return type
 */
function getcolmnurl($sid_id, $view,$backmod=0,$class,$fomart='title') {        
    $v = getcolumsbypath($view, $sid_id);
    return makecolmnnav($v, $backmod , $class, $fomart) ;
}


/**
 * 
 * @param type $v
 * @param type $backmod
 * @param type $class
 * @param type $fomart
 * @return type
 */
function makecolmnnav($v, $backmod = 1, $class, $fomart = 'title') {
    $href = creatnewsnav($v);    
    $title = $v['title'];
    if (!empty($fomart))
        $text = str_replace('title', $title, $fomart);
    $title = clearpre($title);
    switch ($backmod) {
        case '0':
            $url = "<a href=\"{$href}\"  class=\"$class\">{$title}</a>";
            break;
        case '1':
            $url = $href;
            break;
        case '2':
            $url = $title;
            break;
    }
    return $url;
}

/**
 * 生成新闻栏目导航
 * @param type $model
 * @return type
 */
function creatnewsnav($model) {
    if (getConfig('url_router', $model['sid_id'], 'baseinfo')) {  
        return trance_news_nav_route($model);
    } else {
        return trance_news_nav($model);
    }
}

/**
 * 返回前台网站语言参数
 * @return type
 */
function get_langue_parm() {
    $lantag = C('LANGUE_TAG');
    $lang = I($lantag, '');
    $pram = array();
    if ($lang && $lang !== C("DEFAULT_LANG")) {
        $pram[$lantag] = $lang;
    }
    return $pram;
}

/**
 * 普通新闻导航生成
 * @param type $model
 */
function trance_news_nav($model) {
    $pram = get_langue_parm();
    $pram['mod'] = $model['viewpath'];
    $href = U('/news/news', $pram);
    return $href;
}

/**
 * 启用路由规则导航生成
 * @param type $model
 */
function trance_news_nav_route($model) {
    return U("/news/{$model['viewpath']}");
}

/**
 * 清除树状数据前缀
 * @param type $title
 * @return type
 */
function clearpre($title) {
    $s = str_replace('|--', '', $title);
    return $s;
}

/**
 * 生成菜单导航
 * @param type $menuMod
 * @param type $v
 * @return type
 */
function makeNavUrl($v) {
    switch ($v['datatype']) {
        case "newstree"://新闻树导航
            return makecolmnnav($v, 1);
        case 'newscolumn':
            return makecolmnnav($v, 1);
            break;
        case 'goodstree':
            return getGoodsCateUrl($v, 1);
            break;
        default :
            return $v['viewpath'];
    }
}

/**
 * 生成HTML摘要
 * @param string $html html内容
 * @param int $max 摘要长度
 * @param string $suffix 后缀
 * [url=home.php?mod=space&uid=320818]@return[/url] string
 */
function htmlSummary($html, $max, $suffix = '') {
    $non_paired_tags = array('br', 'hr', 'img', 'input', 'param'); // 非成对标签
    $html = trim($html);
    $count = 0; // 有效字符计数(一个HTML实体字符算一个有效字符)
    $tag_status = 0; // (0:非标签, 1:标签开始, 2:标签名开始, 3:标签名结束)
    $nodes = array(); // 存放解析出的节点(文本节点:array(0, '文本内容', 'text', 0), 标签节点:array(1, 'tag', 'tag_name', '标签性质:0:非成对标签,1:成对标签的开始标签,2:闭合标签'))
    $segment = ''; // 文本片段
    $tag_name = ''; // 标签名
    for ($i = 0; $i < strlen($html); $i++) {
        $char = $html[$i]; // 当前字符

        $segment .= $char; // 保存文本片段

        if ($tag_status == 4) {
            $tag_status = 0;
        }

        if ($tag_status == 0 && $char == '<') {
            // 没有开启标签状态,设置标签开启状态
            $tag_status = 1;
        }

        if ($tag_status == 1 && $char != '<') {
            // 标签状态设置为开启后,用下一个字符来确定是一个标签的开始
            $tag_status = 2; //标签名开始
            $tag_name = ''; // 清空标签名
            // 确认标签开启,将标签之前保存的字符版本存为文本节点
            $nodes[] = array(0, substr($segment, 0, strlen($segment) - 2), 'text', 0);
            $segment = '<' . $char; // 重置片段,以标签开头
        }

        if ($tag_status == 2) {
            // 提取标签名
            if ($char == ' ' || $char == '>' || $char == "\t") {
                $tag_status = 3; // 标签名结束
            } else {
                $tag_name .= $char; // 增加标签名字符
            }
        }

        if ($tag_status == 3 && $char == '>') {
            $tag_status = 4; // 重置标签状态
            $tag_name = strtolower($tag_name);

            // 跳过成对标签的闭合标签
            $tag_type = 1;
            if (in_array($tag_name, $non_paired_tags)) {
                // 非成对标签
                $tag_type = 0;
            } elseif ($tag_name[0] == '/') {
                $tag_type = 2;
            }

            // 标签结束,保存标签节点
            $nodes[] = array(1, $segment, $tag_name, $tag_type);
            $segment = ''; // 清空片段
        }

        if ($tag_status == 0) {
            //echo $char.')'.$count."\n";
            if ($char == '&') {
                // 处理HTML实体,10个字符以内碰到';',则认为是一个HTML实体
                for ($e = 1; $e <= 10; $e++) {
                    if ($html[$i + $e] == ';') {
                        $segment .= substr($html, $i + 1, $e); // 保存实体
                        $i += $e; // 跳过实体字符所占长度
                        break;
                    }
                }
            } else {
                // 非标签情况下检查有效文本
                $char_code = ord($char); // 字符编码
                if ($char_code >= 224) { // 三字节字符
                    $segment .= $html[$i + 1] . $html[$i + 2]; // 保存字符
                    $i += 2; // 跳过下2个字符的长度
                } elseif ($char_code >= 129) { // 双字节字符
                    $segment .= $html[$i + 1];
                    $i += 1; // 跳过下一个字符的长度
                }
            }

            $count ++;
            if ($count == $max) {
                $nodes[] = array(0, $segment . $suffix, 'text');
                break;
            }
        }
    }

    $html = '';
    $tag_open_stack = array(); // 成对标签的开始标签栈
    for ($i = 0; $i < count($nodes); $i++) {
        $node = $nodes[$i];
        if ($node[3] == 1) {
            array_push($tag_open_stack, $node[2]); // 开始标签入栈
        } elseif ($node[3] == 2) {
            array_pop($tag_open_stack); // 碰到一个结束标签,出栈一个开始标签
        }
        $html .= $node[1];
    }

    while ($tag_name = array_pop($tag_open_stack)) { // 用剩下的未出栈的开始标签补齐未闭合的成对标签
        $html .= '</' . $tag_name . '>';
    }
    return $html;
}

/**
 * 显示摘要
 * @param type $model
 * @param type $length
 * @return type
 */
function showsummary($content, $length = 0) {
    $content = strip_tags($content);
    $content = str_replace('&nbsp;', ' ', $content);
    $content = trim($content);
    if ($length > 0) {
        $content = getSubstr($content, $length);
    }
    return $content;
}

/**
 * 读取检签列表
 * @param type $tagmar  标签属性目录
 * @param type $taglist 具备的属性列表
 */
function getTaglist($tagmar, $taglist) {
    $tagarr = getAttrsElementList($tagmar);
    $dl = array();
    foreach ($tagarr as $v) {
        if (crackin($v['tagvalue'], $taglist)) {
            $dl[$v['tagvalue']] = $v;
        }
    }
    $rs['count'] = count($dl);
    $rs['list'] = $dl;
    $rs['status'] = 1;
    return $rs;
}

/**
 * 
 * @param type $string
 * @param type $delimiter
 * @param type $warp
 * @return type
 */
function showcongfigarr($string,$delimiter='|',$warp="<p>__MARK__</p>"){
    $arr=  explode($delimiter, $string);
    $nstr="";
    foreach ($arr as  $v){
        $nstr.= str_replace("__MARK__", $v, $warp);
    }
    return $nstr;
}

/**
 * 翻译返回的信息
 * @param type $rs
 * @return type
 */
function trancemessage($rs) {
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
    gettoken();
    return $rs;
}

/**
 * 设置当前城市
 * @param type $citycode
 */
function set_curcity($citycode){
     cookie(C('COOKIE_CITYNAME'), $citycode);
}

/**
 * 获取当前城市
 * @return type
 */
function get_curcity(){
    return cookie(C('COOKIE_CITYNAME'));
}

/**
 * 清除当前城市
 */
function clear_curcity(){
    cookie(C('COOKIE_CITYNAME'), NULL);
}

/**
 * 检索最近地铁站
 * @param type $data
 * @return type
 */
function nearest_subway($data) {
    $db = new \Extend\LbsAddress($data);
    $congig = getConfig('', $data['sid_id'], 'baseinfo');
    $data['query'] = "地铁站";
    $data['tag'] = "地铁站";
    $data['radius'] = $congig['lbs_subway']; // 3000;//检索半径
    $data['scope'] = 2;    
    $filter['industry_type'] = 'life'; //生添设施
    $filter['sort_name'] = 'distance';  //距离排序
    $filter['sort_rule'] = 1; //从低到高
    $data['filter']=$filter;
    $rs = $db->searchaddress($data);
    $arr=array();
    foreach ($rs['results'] as $k=>$v){
        $arr[]=array('name'=>$v['name'],'distance'=>$v['detail_info']['distance'],'detail'=>$v);
    }
    $msg = array();
    if (empty($arr)) {
        $msg['status'] = 0;
        $msg['fistrname'] = '';
        $msg['distance'] = 0;
    } else {
        $msg['status'] = 1;
        $rss = arraysort($arr, array('field' => 'distance', 'direction' => 'SORT_ASC'));
        $msg['data'] = $rss;
        $msg['fistrname'] = $rss[0]['name'];
        $msg['distance'] = $rss[0]['distance'];
    }
    return $msg;
}

/**
 * 敏感词检测系统
 * @param type $data
 */
function check_sensitive($data) {
    $blacklist = getConfig('blacklist');
    $blackarry = LinefeedToArray($blacklist);
    $blackreg = "/" . implode("|", $blackarry) . "/i";
    $rs = array('status' => 0, 'msg' => '', 'sensitive' => '');
    foreach ($data as $dkey => $dval) {
        if (!is_numeric($dval)) {
            if (!empty($dval)) {
                $matches=array();
                if (preg_match($blackreg, $dval, $matches)) {
                    $rs['status'] = 20000006;
                    $rs['msg'] = 'SensitiveWords';
                    $rs['sensitive'] .= $matches[0] . ",";
                    $rs['matches'] = $blackreg;
                }
            }
        }
    }
    return $rs;
}

/**
 * 转义操作命令
 * @param type $data
 * @return type
 */
function trancePrompt($data) {
    $rs = array('status' => 1);
    $filedval = $data['filedval'];
    $nameroot = $data['nameroot'];
    @(eval('$db=new ' . "{$nameroot}();"));
    $showmap = $db->ShowMap[$data['filedname']];
    $title = $showmap['showfiled'] ? $showmap['showfiled'] : 'title';
    $filter = $showmap['filter'];
    $format = $showmap['format'];
    $list = getAttrsElementList($format);

    if (isset($showmap['score'])) {
        $config = getAttrbymark($showmap['score']['config']);
        $effect = $showmap['score']['effect'];
        $rs['showscore'] = 1;
        if (is_number($config['tagvalue'])) {
            $default = $config['tagvalue'];
            $rs['score'] = "<input type=\"hidden\" placeholder=\"\" name=\"score\" value=\"{$default}\" class=\"layui-input\">{$default}";
        } else {
            $d = htmlspecialchars_decode($config['tagvalue']);
            $arr = json_decode($d, true);
            $rs['score'] = "<input type=\"text\" placeholder=\"可选区间：{$arr['min']}-{$arr['max']}\" name=\"score\" id=\"score\" value=\"{$arr['default']}\" class=\"layui-input\" min=\"{$arr['min']}\" max=\"{$arr['max']}\" />";
        }
    }
    if (isset($showmap['inputdate'])) {
        $inputdate = $showmap['inputdate']; 
        $dateattr = " data-type=\"{$inputdate['type']}\" data-format=\"{$inputdate['format']}\" ";
        if (empty($filedval)) {
            $v = date($inputdate['trancemat']);
            $dateattr .= " value=\"{$v}\" ";
        } else {
            $v = date($inputdate['trancemat'], $filedval);
            $dateattr .= " value=\"{$v}\" ";
        }
        $rs['inputdate'] = "<input type=\"text\" name=\"inputdate\"  class=\"trimblank layui-input datepicker\"  placeholder=\"\" {$dateattr} />";
    }else{
        $rs['inputdate']="";
    }
    $btnlist = "";
    foreach ($list as $v) {
        $f = isset($filter[$v['tagvalue']]) ? $filter[$v['tagvalue']] : $filter['all'];
        if (empty(crackin($v['tagvalue'], $f))) {
            $mustinput = crackin($v['tagvalue'], $showmap['mustinput']);//必须填写操作理由
            $sefc='';
            if (isset($effect[$v['tagvalue']])) {//对积分的操作
                $sefc = $effect[$v['tagvalue']];
            }
            $btnlist.="<button class=\"layui-btn {$v['tagclass']} changeopmod \" lay-submit=\"\" data-val=\"{$v['tagvalue']}\" data-mustinput=\"{$mustinput}\" data-effectscore=\"{$sefc}\" data-callback=\"{$showmap['callback']}\" lay-filter=\"prompt_submit\">{$v[$title]}</button>";
        }
    }

    $rs['btnlist']=$btnlist;
    
    $promptlog = new \Common\Umodel\LogPromptModel();
    $map = array('nameroot' => $nameroot, 'objid' => $data['id'], 'filedname' => $data['filedname']);
    $log = $promptlog->getlist($map);    
    $logrs = $log['list'];
    foreach ($logrs as $k => $v) {
        $config = unserialize($v['config']);
        $logrs[$k]['filedval'] = getkeyname($v['filedval'], $config['format'], '-', $config['showfiled']);
        $logrs[$k]['newval'] = getkeyname($v['newval'], $config['format'], '-', $config['showfiled']);
        $logrs[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
        $logrs[$k]['username'] = getusername($v['userid'], $v['usertype'], $data['usertype']);
        $logrs[$k]['memo'] = nl2br($v['memo']);
    }

    $rs['log'] =$logrs;
    return $rs;
}

/**
 * 提交操作命令
 * @param type $data
 * @return string
 */
function commitPrompt($data) {
    $rs = testenable_writing();
    if ($rs['status'] !== 0) {
        return $rs;
    }

    $nameroot = $data['nameroot'];
    @(eval('$db=new ' . "{$nameroot}();"));
    $showmap = $db->ShowMap[$data['filedname']];
    
    $rs = array('status' => 0, 'msg' => '');
    if (empty(in_array($data['idname'], ['id', 'm_id', 'sid_id']))) {
        $rs['msg'] = '非法的命令';
    } else {
        $where = array($data['idname'] => $data['id']);
        $save = array($data['filedname'] => $data['newval']);
        $db->startTrans();
        
        if (isset($data['callback']) && !empty($data['callback'])) {//调用对象方法
            $map = array($data['idname'] => $data['id']);
            $map[$data['filedname']] = $data['newval'];
            $map['inputdate'] = $data['inputdate'];
            $map['enablerollback'] = false; //禁用内部的回滚事务
            $rs = call_user_func(array($db, $data['callback']), $map);
            if ($rs['status'] == 0) {
                $rt = 1;
            } else {
                $rt = 0;
            }
        } else {//调用通用方法
            $rt = $db->where($where)->save($save);
        }
        if ($rt) {
            $score = 0;
            if ($nameroot === 'Common\Umodel\CusCustomerModel') {
                $userid = $data['id'];
            } else {
                $fields = $db->getDbFields();
                if (in_array('userid', $fields)) {
                    $userid = $db->where($where)->getField("userid");
                } else {
                    $userid = 0;
                }
            }
            switch ($data['effectscore']) {
                case '+'://奖劢积分
                    $score = $data['score'];
                    break;
                case '-'://扣除积分
                    $score = $data['score'] * (-1);
                    break;
                default ://无积分操作
                    $score = 0;
                    break;
            }
            $prompt = $data;
            $promptlog = new \Common\Umodel\LogPromptModel();
            $prompt['objid'] = $data['id'];
            if (isset($showmap['score'])) {
                $prompt['filedformat'] = $showmap['score']['config'];
            }
            $prompt['filedtitle'] = $showmap['title'];
            $prompt['config'] = serialize($showmap);
            $prompt['score'] = $score;
            unset($prompt['id']);
            $promptid = $promptlog->addnew($prompt);
            if ($promptid) {
                if ($userid && $score !== 0) {
                    $cusScore = new Common\Umodel\CusScoreModel();
                    $scdata = array('userid' => $userid, 'scoretype' => $showmap['score']['config'], 'objid' => $data['id'], 'sid_id' => $data['sid_id']);
                    $crs = $cusScore->setscore($scdata, 0, $score);
                    if ($crs['status']) {
                        if ($promptlog->where(array('id' => $promptid))->setField('scorelogid', $crs['logid'])) {
                            $db->commit();
                            $rs['status'] = 0;
                            $rs['score'] = $score;
                            $rs['userid'] = $userid;
                        } else {
                            $rs['status'] = 1;
                            $rs["msg"] = '积分日志关联失败';
                        }
                    } else {
                        $rs = $crs;
                    }
                } else {
                    $db->commit();
                    $rs['status'] = 0;                   
                }
                if ($rs['status']===0) {
                    $rs["msg"] = 'DataModifySuc';
                    $format = $showmap['format'];
                    $rs['curentval'] = $data['newval'];
                    $rs['curenttext'] = getkeyname($data['newval'], $format, '', 'title');
                    $rs['removeclass'] = getkeyname($data['filedval'], $format, '', 'tagclass');
                    $rs['curent'] = getkeyname($data['newval'], $format, '', 'tagclass');
                    $rs['f'] = $array;
                }
            } else {
                $db->rollback();                
            }
        }
        else {           
            $rs["msg"] = 'DataModifyFailed';
        }
    }
    return $rs;
}

/**
 * 读取用户名
 * @param type $userid 用户ID
 * @param type $usertype 用户类型
 * @param type $showdir 显示位置
 * @return type
 */
function getusername($userid, $usertype, $showdir = 0) {
    $username = "";
    switch ($usertype) {
        case 1:
            if ($showdir !== '1') {
                $username = "管理员";
            } else {
                $db = new Common\Model\AdminModel();
                $model = $db->getmodelbyid($userid);
                $username = $model['truename'] ? $model['truename'] : $model['m_name'];
            }
            break;
        case 2:
            $db = new \Common\Umodel\CusCustomerModel();
            $model = $db->getmodelbyid($userid);
            $username = $model['nickname'] ? $model['nickname'] : $model['username'];
            break;
        default :
            break;
    }
    return $username;
}

/**
 * 发送验证邮件
 * @param type $data
 * @return int
 */
function sendemail($data) {
    switch ($data['mailtype']) {
        case "crcakmail":
            $code = Org\Util\Stringtools::randString(4, 1);
            session($data['mailto'] . "crcakmail", $code);
            break;
        default :
            break;
    }

    $bconfig = getConfig('', $data['sid_id'], 'baseinfo');
    $body = <<<EOC
             <p class="salutation" style="font-weight:bold;">
					Hi,<span id="mailUserName">{$data['username']}</span>：<br />
                                        您的邮件验证码是：{$code}
				</p>
				 <p>此邮件验证码将用于验证身份，修改密码等，请勿将验证码透漏给其他人。<br>
                                        本邮件由系统自动发送，请勿直接回复！
                                    <br><br>如果这不是您的邮件请忽略，很抱歉打扰您，请原谅。</p>
EOC;
    $rs = checkMail($bconfig, $body, $data['username'], $data['mailto'], $data['mailtitle']);
    if ($rs) {
        $rs = array("status" => 1, "msg" => "邮件验证码发送成功，请前往邮箱查看");
    } else {
        $rs = array("status" => 0, "msg" => "邮件验证码发送失败");
    }
    return $rs;
}

/**
 * 发送短信验证码
 * @param type $data
 */
function sendsms($data) {
    switch ($data['smstype']) {
        case "crcaksms":
            $code = Org\Util\Stringtools::randString(4, 1);
            session($data['mobile'] . "crcaksms", $code);
            break;
        default :
            break;
    }
    $rs = array("status" => 1, "msg" => "短信验证码发送成功" . $code);
    return $rs;
}

/**
 * 比较邮箱验证码
 * @param type $mail
 * @param type $code
 * @return type
 */
function checkmailcode($mail, $mailtype, $code) {
    if (empty($code)) {
        return false;
    }
    $c = session($mail . $mailtype);
    return $c === $code;
}

//通用排序操作
function sortset($data) {
    $rs = testenable_writing();
    if ($rs['status'] !== 0) {
        return $rs;
    }
    $db = M($data['table']);
    $where = array($data['idname'] => $data['idval']);
    $r = $db->where($where)->setField($data['filedname'], $data['filedval']);
    if ($r) {
        $rs = array('status' => 0, 'data' => $data, 'msg' => 'DataModifySuc');
    } else {
        $rs = array('status' => 20000002, 'msg' => "DataModifyFailed");
    }
    return $rs;
}
