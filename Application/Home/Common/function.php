<?php 
/**
 * 设置主题
 * @param type $theme
 */
function set_theme($theme = '') {
    $siturl = $_SERVER['HTTP_HOST'];
    $db = new \Common\Model\SubsidiaryModel;
    $model = $db->getmodebysite($siturl);
    cookie('csidid', $model['sid_id']);
    $htmlpath = '';
    $cachetime = 300;
    if ($model) {
        $htmlpath = $model['sid_dir'];
        $cachetime = $model['cache'];
    }
    define('HTML_CACHE_TIME', $cachetime); //HTML缓存时间
    define('HTML_PATH', "Html/{$htmlpath}/"); //静态缓存文件目录，HTML_PATH可任意设置，此处设为当前项目下新建的html目录
    define('CURENT_HTMLPATH', "{$htmlpath}");

    //判断是否存在设置的模板主题
    if (empty($theme)) {
        $theme_name = C('DEFAULT_THEME');
        if ($model) {
            $theme_name = $model['theme'];
            C('HTML_CACHE_TIME', $model['cache']);
        }
        C('DEFAULT_THEME', $theme_name);
    } else {
        $theme_name = C('DEFAULT_THEME');
    }
    //替换COMMON模块中设置的模板值    
    if (C('Current_Theme')) {
        C('TMPL_PARSE_STRING', str_replace(C('Current_Theme'), $theme_name, C('TMPL_PARSE_STRING')));
    } else {
        C('TMPL_PARSE_STRING', str_replace("MODULE_NAME", MODULE_NAME, C('TMPL_PARSE_STRING')));
        C('TMPL_PARSE_STRING', str_replace("DEFAULT_THEME", $theme_name, C('TMPL_PARSE_STRING')));
    }
    C('Current_Theme', $theme_name);
    C('DEFAULT_THEME', $theme_name);
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
function show_list($model, $name, $ishidden = false, $selected = 0, $map = '', $cssclass = '') {
    $rs = htmlhelper::make_htmlcontorl($model, $name, $ishidden, $selected, $map, $cssclass);
    $DefaultClass = C('DefaultClass');
    switch ($model['model']) {
        case 'rdlist':
            $pid = $selected;   
            $db = htmlhelper::getdb($model, $map);
            if ($pid == '')  $pid = $model['value'];
            $str = "";
            foreach ($db as $key => $v) {
                if ($pid == $v['idvalue'])
                    $checked = 'checked';
                else
                    $checked = '';
                $str .="<input type='radio'  class='radio-la' {$checked} value=\"{$v['idvalue']}\" name='{$name}' id=\"{$name}{$v['idvalue']}\" hidden /><label for=\"{$name}{$v['idvalue']}\" class=\"group-T-1 icon-uniE940\">{$v['idname']}</label> ";
            }
            $titlecls = isset($model['titlecls']) ? $model['titlecls'] : $DefaultClass["rdlist"];
            $content = "<div class=\"group-T\">{$str}</div>";
            $rs['content'] = $content;
            break;
    } 
    return $rs;
}
