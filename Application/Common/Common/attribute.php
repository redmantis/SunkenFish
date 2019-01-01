<?php

/*
 * UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2018-4-4 21:14:11
 * @Modify  2018-4-4 21:14:11
 * @CopyRight:  2018 by RDM
 * 属性相关函数
 */

/**
 * 读取字段相关配置
 * 名称、描述、验证规则、验证消息等
 * @param type $model   //字段配置
 * @param type $name    //字段名
 */
function get_attr_config($model, $name) {
    if ($model['tableindex'] === 'langue' && !isset($model['titletag'])) {//多语言字段
        $t = explode('__', $name);
        $titletag = $t[1];
    } else {
        $titletag = isset($model['titletag']) ? $model['titletag'] : $name;
    }
    $parentkey = isset($model['parentkey']) ? $model['parentkey'] : 'CommTag';
  
    
    $tag = getTagbyMark($titletag, $parentkey, "tagvalue");

    if ($tag) {
        $langTitle = $tag['title'];
        $titledesc = $tag['description'];
        $verify = $tag['verify'];
        $verifymsg = $tag['verifymsg'];

        $verify = isset($model['verify']) ? $model['verify'] : $verify;
        $verifymsg = isset($model['verifymsg']) ? $model['verifymsg'] : $verifymsg;
        $msg = "";
        if (is_array($verifymsg)) {
            foreach ($verifymsg as $k => $v) {
                $msg .= "lay-verify-msg-{$k}=\"{$v}\" ";
            }
        } else {
            $msg = "lay-verify-msg=\"{$verifymsg}\"";
        }
    } else {
        $langTitle = empty($langTitle) ? $model['des'] : $langTitle;
        $langTitle = empty($langTitle) ? $titletag : $langTitle;
    }

    $verifystring = ""; //验证规则
    if (!empty($verify)) {
        $verifystring = "{$msg} lay-vertype=\"tips\" lay-verify=\"{$verify}\"";
    }
    
    $data = array();
    $data['title'] = $langTitle;
    $data['description'] = $titledesc;
    $data['verify'] = $verify;
    $data['verifymsg'] = $verifymsg;
    $data['verifystring'] = $verifystring;
    return $data;
}

/**
 * 获取数组键名
 * @param type $value  鍵值
 * @param type $pname  数组名
 * @param type $default 黙认值
 * @return type
 */
function getkeyname($value = 0, $pname = 'StatusList', $default = '', $title = 'title') {    
    return showTagbyMark($value,$default,$pname,'tagvalue',$title);
}

/**
 * 显示标签文本
 * @param type $cmart  当前标签
 * @param type $default  默认值
 * @param type $compmod 比较模式  tagmark｜标签，tagvalue｜键值
 * @param type $parentmark 上级标签
 * @return type 
 */
function showTagbyMark($cmart, $default = "", $parentmark = "", $compmod = '', $showtitle = '') {    
    if (empty($cmart) && ($compmod == "CommTag" || empty($compmod))) {
        return "";
    }
    $showtitle = empty($showtitle) ? 'title' : $showtitle; 
    $parentmark = empty($parentmark) ? 'MessageInfo' : $parentmark;
    $model = getTagbyMark($cmart,$parentmark,$compmod);
    if($model){
        return $model[$showtitle];
    }    
    if (empty($default)) {
        return $cmart;
    }
    return $default;
}

/**
 * 显示标签文本  以 tagvalue 为参照
 * @param type $cmart
 * @param type $showtitle
 * @return type
 */
function showTagbyValue($cmart, $showtitle = "",$parent="MessageInfo") {
    $showtitle = empty($showtitle) ? 'title' : $showtitle;
    $tag = getTagbyMark($cmart, $parent, "tagvalue");
    if ($tag) {
        return $tag[$showtitle];
    }
    return $cmart;
}

/**
 * 获取标签定义
 * @param type $cmart  当前标签
 * @param type $parentmark 上级标签
 * @param type $compmod 比较模式  tagmark｜标签，tagvalue｜键值
 * @return type 
 */
function getTagbyMark($cmart, $parentmark = "", $compmod = '') {

    $parentmark = empty($parentmark) ? 'CommTag' : $parentmark;
    $compmod = empty($compmod) ? 'tagmark' : $compmod;

    $lang = getLangueInfo();

    $sid_id = $lang['sid_id'];
    $catchpath = "lancatch/{$sid_id}/attr_model/{$parentmark}/{$cmart}/{$lang['curent_lang']}";
    $value = F($catchpath);
    if ($value) {
        return $value;
    }
    if ($compmod == 'tagmark') {
        $db = new \Common\Gmodel\AtbModel();
        $viewpath = $db->getviewpath();
        $model = $db->getmodelbypath([$viewpath => $cmart, 'sid_id' => ['in', "0,{$lang['sid_id']}"]]);
        F($catchpath, $model);
        return $model;
    }

    $langlist = getAttrsElementList($parentmark);
    foreach ($langlist as $k => $v) {
        if (strcasecmp($v[$compmod], $cmart) == 0) {    //忽略大小写 
            $v['title'] = str_replace("|--", '', $v['title']);
            F($catchpath, $v);
            return $v;
        }
    }
}

/**
 * 根据键名取属性
 * @param type $tagmark
 * @return type
 */
function getAttrbymark($tagmark) {
    $lang = getLangueInfo();
    $sid_id = $lang['sid_id'];
    $catchpath = "lancatch/{$sid_id}/attrst/{$tagmark}/{$lang['curent_lang']}";
    $value = F($catchpath);
    if ($value) {
        return $value;
    } else {
        $db = new \Common\Gmodel\AtbModel();
        $viewpath = $db->getviewpath();
        $value = $db->getmodelbypath([$viewpath => $tagmark, 'sid_id' => ['in', "0,{$lang['sid_id']}"]]);        
        F($catchpath, $value);
        return $value;
    }
}

/**
 * 取得属性子元素列表
 * @param type $tagmark
 * @return type
 */
function getAttrsElementList($tagmark) {
    $lang = getLangueInfo();   
    $sid_id = $lang['sid_id'];
    $map['sid_id'] = array('in', "0,{$sid_id}");
    $catchpath = "lancatch/{$sid_id}/attrs/{$tagmark}/{$lang['curent_lang']}";
    $langlist = F($catchpath);
    if (!$langlist) {
        $db = new Common\Gmodel\AtbModel();
        $langlist = $db->getElementList($map, $tagmark);
        F($catchpath, $langlist);
    }
    return $langlist;
}

/**
 * 显示列表标题
 * @param type $model   字段配置
 * @param type $key     字段名
 * @return type
 */
function showtableTitle($model, $key = null) {
    if (is_array($model)) {
        $model['des'] = $model['title'];
        $con = get_attr_config($model, $key);
        switch ($model['type']) {
            case 'chekbox':
                $str = "<input type=\"checkbox\" class=\"selectall\" id=\"selectall\" /><label for=\"selectall\">{$con['title']}</label>"; //date($val['format'], $value);
                break;
            default :
                $str = $con['title'];
                break;
        }
    } else {
        $str = $model;
    }
    return $str;
}

/**
 * 动态列表格式化中内容的显示
 * @param type $v
 * @param type $k
 */

/**
 * 格式化输出列表中的内容
 * @param type $v           数据记录
 * @param type $key         字段名
 * @param type $val         表头字段配置
 * @param type $userpower   当前用户权限表
 * @param type $nameroot    模形命名间
 * @return type
 */
function showTableContent($v, $key, $val, $userpower = NULL, $nameroot = '') {
    $value = '';
    
    //分表数据库后缀计算
    $tabsuffix="";
    if (!empty($nameroot)) {
        @(eval('$rdb=new ' . "{$nameroot}();"));
        if (isset($val['table']) && method_exists($rdb, "getPartitionTableName")) {
            $tabsuffix = $rdb->getPartitionTableName($v);
            $val['table'] = $val['table'] . $tabsuffix;
        }
    }
    if (is_array($val)) {
        $value = isset($v[$key]) ? $v[$key] : $val['title'];
        switch ($val['type']) {
            //显示时间
            case 'time':
                if (is_number($value))
                    $value = date($val['format'], $value);
                else {
                    $value = '';
                }
                break;
            case 'img':
                $img = "";
                if (!empty($value)) {
                    $nv = date($val['format'], $value);
                    $lazyimg= getConfig('lazyimg');
                    $imghandle = "src";
                    if ($lazyimg) {
                        $imghandle = "lay-src";
                    }
                    $img = "<img layer-pid=\"{$val['id']}\" layer-src=\"{$value}\" {$imghandle}=\"{$value}\" alt=\"{$v[$val["titlefild"]]}\" />";
                }
                $value = $img;
                break;
            //显示选择框
            case 'chekbox':
                $value = "<input type=\"checkbox\" name=\"tid[]\" class=\"tid\" value=\"{$v[$key]}\" />"; //date($val['format'], $value);
//                return $nv;
                break;

            /**
              'scoretype' => array(
              'type' => 'tagkey',
              'format' => 'score_taglist',
              'title' => '变更项目',
              ),
             */
            case 'tagkey':
                $value = showTagbyMark($value, '', $val['format']);
                break;
            //配置文件中参数或属性配置字段
            /**
              'brand_id' => array(
              'type' => 'config',
              'format' => 'AboutGoods_brand',//配置关键字或属性字段
              'title' => '品牌',
              ),
             */
            case 'config':
                $tt = showTagbyMark($value, '', $val['format'], 'tagvalue');
                $color = 'layui-bg-blue';
                if (isset($val['color'])) {
                    $color = 'layui-bg-' . $val['color'];
                }
                $value = "<span class=\"layui-badge {$color}\">{$tt}</span>";
                break;
            /**
             * 数据表中字段
             *  'roomid' => array(
              'type' => 'table',
              'title' => '房间',
              'idfiled' => 'id',
              'namefiled' => 'title',
              'table' => 'ZfRooms',
              ),
             */
            case 'table':
                $db = M($val['table']);
                $map = array($val['idfiled'] => $v[$key]);
                $value = $db->where($map)->cache()->getField($val['namefiled']);
                break;
            case 'langmodel':
                $dbsource = $val['source'];
                @(eval('$db=new ' . "{$dbsource['dbmodel']}();"));
                $model = $db->getmodel($v[$key]);
                $value = $model['all'][$dbsource['valuefiled']]; //$v[$key];//
                break;
            //显示开关命令行
            case 'switch':
                $key1 = getkeyname(1, $val['format']);
                $key0 = getkeyname(0, $val['format']);
                $btnclass0 = getkeyname(0, $val['format'], '', 'tagclass');
                $btnclass1 = getkeyname(1, $val['format'], '', 'tagclass');
                if ($value) {
                    $checked = 'checked';
                    $rstr = $key1;
                    $btnclass = $btnclass1;
                } else {
                    $checked = '';
                    $rstr = $key0;
                    $btnclass = $btnclass0;
                }
                $idname = isset($val['idd']) ? $val['idd'] : 'id';
                $value = <<<EOC
                        <a class="layui-btn layui-btn-sm {$btnclass} changeattr" data-class1='{$btnclass1}' data-class0='{$btnclass0}' data-idname='{$idname}' data-id='{$v[$idname]}' data-filedname='{$key}' data-filedval='{$value}' data-tb='{$val['table']}' data-key0="{$key0}" data-key1="{$key1}" >
                       <i class="layui-icon">{$rstr}</i>
                       </a>
EOC;
                break;
            //显示属性轮换
            case 'rotation':
                $rstr = getkeyname($value, $val['format']);
                $btnclass = getkeyname($value, $val['format'], '', 'tagclass');
                $idname = isset($val['idd']) ? $val['idd'] : 'id';
                $value = <<<EOC
                        <a class="layui-btn layui-btn-sm {$btnclass} rotationattr" data-idname='{$idname}' data-id='{$v[$idname]}' data-filedname='{$key}' data-filedval='{$value}' data-tb='{$val['table']}' data-format="{$val['format']}">
                       <i class="layui-icon">{$rstr}</i>
                       </a>
EOC;
                break;
            //属性输入管理（管理及输入相关理由等）
            case 'prompt':
                $tips = "";
                $switchcls = "";
                if (isset($val['inputdate']) && $value > 0) {
                    $rstr = getkeyname(1, $val['format']);
                    $btnclass = getkeyname(1, $val['format'], '', 'tagclass');
                    $trancemat = isset($val['inputdate']['trancemat']) ? $val['inputdate']['trancemat'] : "Y-m-d";
                    $tips = date($trancemat, $value);
                    $switchcls = "switchcls";
                } else {
                    $rstr = getkeyname($value, $val['format']);
                    $btnclass = getkeyname($value, $val['format'], '', 'tagclass');
                    }

                $idname = isset($val['idd']) ? $val['idd'] : 'id';
                $value = <<<EOC
                        <div class="layui-btn-group">                        
                        <a class="layui-btn layui-btn-sm {$btnclass} {$switchcls}" data-vtv="{$tips}" id="{$key}{$v[$idname]}" data-nameroot='{$nameroot}' data-idd='{$idname}' data-idval='{$v[$idname]}' data-filedname='{$key}' data-filedval='{$value}' data-tb='{$val['table']}' data-format="{$val['format']}"  data-title="{$val['title']}">
                       <i class="layui-icon">{$rstr}</i>
                       </a>
                       <button class="layui-btn layui-btn-sm layui-btn-normal prompt" handle="{$key}{$v[$idname]}"><i class="layui-icon">&#xe631;</i></button>
                       </div>
EOC;
                break;
            //显示排序框
            case 'sortid':
                $idname = isset($val['idd']) ? $val['idd'] : 'id';
                $sortfiled = isset($val['sortfiled']) ? $val['sortfiled'] : 'sortid';
                $value = "<input class=\"layui-input sortset\" type='text' data-idname='{$idname}' data-table='{$val['table']}' data-sortfiled='{$sortfiled}'  name='{$v[$idname]}' value='{$value}'/>";
                break;
            /**
             * 显示一个命令按钮
             */
            case 'link':
                $value = showTableLink($val, $v, $key);
                break;
            case 'btngroup':
                foreach ($val['btnlist'] as $k => $link) {
                    if (empty($userpower) || in_array($k, $userpower)) {
                        $s .= showTableLink($v, $link, $k);
                    }
                }
                $value = $s;
                break;
        }
    } else {
        if ($v[$key] !== null)
            $value = $v[$key];
    } 
    return $value;
}

/**
 * 动态列表中链接、按钮输入
 * @param type $model           //数据对象实例
 * @param type $conf            //字段配置
 * @param type $filedname       //字段名
 * @return type
 */
function showTableLink($model, $conf, $filedname) {
    $link = $conf['link'];
    $pram = array();
    foreach ($link['fields'] as $k => $w) {
        $pram[$k] = $model[$w];
    }

    $url = U($link['url'], $pram);

    $title = showtableTitle($conf, $filedname);
    $btn = traccIcon($link['url'], $title);

    $returnstr = "";
    switch ($link['mod']) {
        case 'shownews'://文章预览
            $db = new \Common\Model\NewsModel();
            $nmodel = $db->getmodel($pram);
            $url = makeviewurl($nmodel);
            $title = $filedname;
            $returnstr = $conf['title'];
            break;
        case 'diag'://显示模态窗口
            $returnstr = <<<EOC
                    <a class="layui-btn layui-btn-sm diag_modal" data-url="{$url}" data-title="{$title}" title="{$title}">
                        {$btn}</a>
EOC;
            break;
        case "card"://在选项卡中显示
            $returnstr = <<<EOC
                    <a class="layui-btn layui-btn-sm showby-tab" data-url="{$url}" data-id="{$url}" data-title="{$model['title']}({$title})" title="{$title}">
                        {$btn}</a>
EOC;
            break;
        case 'blank':
            $returnstr = "<a class=\"{$link['class']} layui-btn layui-btn-sm\" href=\"{$url}\" target='_blank' title='{$title}' >{$title}</a>";
            break;
        case 'self':
            $returnstr = "<a class=\"{$link['class']} layui-btn layui-btn-sm\" href=\"{$url}\" target='_self' title='{$title}' >{$title}</a>";
            break;
        case 'diag_del':
            if (isset($conf['message'])) {
                $message = showTagbyMark($conf['message']);
            } else {
                $message = showTagbyMark('DelWarning');
            }
            $returnstr = <<<EOC
                    <a class="layui-btn layui-btn-sm layui-btn-danger {$link['cls']}" data-url="{$url}" data-title="{$title}"  data-message="{$message}" title="{$title}">
                        {$btn}</a>
EOC;
            break;
    }
    return $returnstr;
}

/**
 * 显示查询附加按钮列表
 * @param type $powerlist  权限列表
 * @param type $controlname  控制器名称
 * @param type $exparm  额外参数
 * @return type
 */
function showButtonArray($powerlist,$controlname,$exparm) {
    $xblist = C('BUTTONLIST');
    $blist = array();
    foreach ($powerlist as $v) {
        $butt = $xblist[$v];
        $op = getlogtype($controlname . '/' . $butt['url'], 0);
        if ($op['op']) {
            $butt['title'] = $op;
        }
        $butt['fields'] = $mp;
        $blist[$v] = $butt;
    }
    $bstr = "";
    foreach ($blist as $k => $v) {
        $pram = array();
        foreach ($v['fields'] as $k => $w) {
            $pram[$k] = $w;
        }
        if(!empty($exparm)){
            $pram=  array_merge($pram,$exparm);
        }
        $url = U($v['url'], $pram);
        if (is_array($v['title'])) {
            $title = $v['title']['opstr'];
        } else {
            $title = $v['title'];
        }
        switch ($v['mod']) {
            case 'diag':
                  $btn = traccIcon($v['url'], $title);
                $bstr .= <<<EOC
                    <div class="layui-inline">
                    <a class="layui-btn diag_modal" data-url="{$url}" data-title="{$title}">
                        {$btn}</a></div>
EOC;
                break;
            case "diag_deleteall":
                if (isset($v['message'])) {
                    $message = showTagbyMark($v['message']);
                } else {
                    $message = showTagbyMark('DelAllWarning');
                }
                 $btn = traccIcon($v['url'], $title);
                $bstr .= <<<EOC
                    <div class="layui-inline">
                    <a class="layui-btn diag_deleteall layui-bg-red" data-url="{$url}" data-title="{$title}" data-message="{$message}">
                        {$btn}</a></div>
EOC;
                break;
            case 'blank':
                $bstr .= "<div class=\"layui-inline\"><a href=\"{$url}\" class=\"layui-btn layui-btn-sm\" target='_blank' title='{$title}'>{$title}</a></div>";
                break;
        }
    }
    return $bstr;
}

/**
 * 按键名转换按钮图标
 * @param type $key
 * @param type $title
 */
function traccIcon($key, $title="") {
    //$title="";
    switch ($key) {
        case "add"://添加
            $t = "<i class=\"layui-icon layui-icon-add-circle\"></i> {$title}";
            break;
        case "del"://删除
            $t = "<i class=\"layui-icon layui-icon-delete\"></i> {$title}";
            break;
        case 'update'://编辑
            $t = "<i class=\"layui-icon layui-icon-edit\"></i> {$title}";
            break;
        case "batchoperate"://批处理
             $t = "<i class=\"layui-icon layui-icon-fire\"></i> {$title}";
            break;
        case "access"://权限配置
             $t = "<i class=\"layui-icon layui-icon-auz\"></i> {$title}";
            break;
        default ://其他
            $t = $title;
    }
    return $t;
}

/**
 * 动态列表内容显示模板生成
 * @param type $showmap  显示模板
 * @param type $idfiledname 主键名称
 */
function showtplbymap($showmap, $idfiledname) {
    $tdlist = "<td><input type=\"checkbox\" name=\"layTableCheckbox\" lay-skin=\"primary\" lay-filter=\"layTableAllChoose\" value=\"{{ item.{$idfiledname} }}\"></td>";
    foreach ($showmap as $k => $v) {
        if ($k === 'btngroup') {
            $tdlist .="<td ttid=\"{$k}\">{{ item.{$k} }}</td>";
        } else {
            switch ($v['type']) {
                case 'img':
                    $tdlist .="<td ttid=\"{$k}\" class=\"imgview\">{{ item.{$k} }}</td>";
                    break;
                case 'prompt':
                case 'time':
                case 'config':
                case 'rotation':
                    $tdlist .="<td ttid=\"{$k}\">{{ item.{$k} }}</td>";
                    break;
                default :
                    $tdlist .="<td ttid=\"{$k}\"><div  class=\"layui-table-cell-over\">{{ item.{$k} }}</div></td>";
                    break;
            }
        }
    }
    $tpl = <<<EOC
              <tr id="{{ item.{$idfiledname} }}">
              {$tdlist}
        </tr>
EOC;
    return $tpl;
}

/**
 * 将数据转换为显示格式（配合前端模板用）
 * @param type $showmap  显示模板
 * @param type $dl       数据
 * @param type $userpower 用户权限
 * @param type $nameroot 用户权限
 * @return type
 */
function showTranceDatabymap($showmap, $dl, $userpower, $nameroot = '') {
    $newdata = array();
    foreach ($dl as $k => $v) {
        $model = array();
        foreach ($showmap as $key => $val) {
            $model[$key] = showTableContent($v, $key, $val, $userpower, $nameroot);
        }
        $newdata[] = $model;
    }
    return $newdata;
}