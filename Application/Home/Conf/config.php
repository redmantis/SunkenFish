<?php
return array(

    //是否开启模板布局 根据个人习惯设置 
    'LAYOUT_ON' => false,
    'LAYOUT_NAME' => 'Public/layout',
    'HTML_CACHE_ON' => false, // 开启静态缓存   
    'HTML_CACHE_TIME' => HTML_CACHE_TIME, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(// 定义静态缓存规则
        // 定义格式1 数组方式
        //'静态地址'    =>     array('静态规则', '有效期', '附加规则'), 
        'index:index'=>array('{:module}_{:controller}_{:action}',HTML_CACHE_TIME), 
        'index:page'=>array('{:module}_{:controller}_{:action}_{mod}',HTML_CACHE_TIME),  
        'lis:nlist'=>array('{:module}_{:controller}_{:action}_{lg}_{mod}_{p}',HTML_CACHE_TIME),  
        'lis:index'=>array('{:module}_{:controller}_{:action}_{mod}',HTML_CACHE_TIME),
        'detail:detail'=>array('{:module}_{:controller}_{:action}_{cid}',HTML_CACHE_TIME),
    ), 
    
    // 'VIEW_PATH' =>  '/Templates/',
    // 设置默认的模板主题
    'DEFAULT_THEME' => 'tmp485', //当模块中没有设置主题，则模块主题会设置为此处设置的主题,主题名和模块名不能重复，如不能采用“Home”
    'DEFAULT_THEME_NAME' => 'theme_name',//当前动态主题保存cookie名称
    // 'THEME_LIST' => 'default,base',
    'TMPL_DETECT_THEME' => true,
    /* 模板相关配置 */
    //此处只做模板使用，具体替换在COMMON模块中的set_theme函数,该函数替换MODULE_NAME,DEFAULT_THEME两个值为设置值
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__STATIC__' => __ROOT__ . '/Application/MODULE_NAME/View/DEFAULT_THEME/static',
    ),
);