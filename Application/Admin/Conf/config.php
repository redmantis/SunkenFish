<?php
return array(   
    'DATA_CACHE_TIME' => '300', //缓存时间    
    'VIEW_PATH' => './Tplmag/', // 改变某个模块的模板文件目录
    'DEFAULT_THEME' => 'View', //h83 //当模块中没有设置主题，则模块主题会设置为此处设置的主题,主题名和模块名不能重复，如不能采用“Home”
    'TMPL_DETECT_THEME' => true,
    
    'LAYOUT_ON' => false,
    'LAYOUT_NAME' => 'Public/layout',   
    //主题静态文件路径
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Tplmag/View/static',
        '__PUBLIC__' => __ROOT__ . '/Public',
        '__EDITOR__' => __ROOT__ . '/Public/ueditor',
    ),  
);
