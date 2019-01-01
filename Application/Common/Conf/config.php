<?php

return array(
    //'配置项' =>'配置值'
    'MODULE_ALLOW_LIST' => array('Api','Admin','Home','Worker'),
    //我们用了入口版定 所以下面这行可以注释掉
    'DEFAULT_MODULE' => 'Home',                 // 默认模块	
    'SHOW_PAGE_TRACE' => false,
    'LOAD_EXT_CONFIG' => 'db,authpanle,alliance,oauth,subsidiary,sitebase,searchpanle,columnspanel,adminpanel,customset,goods,customer',
    'LOAD_EXT_FILE'=>'tools,htmlhelper,tplapi,api,order,attribute,safetools,gather',        //加载公用函数
    'URL_CASE_INSENSITIVE' => true,             //url不区分大小写
    'URL_MODEL' => 2,
    'TMPL_TEMPLATE_SUFFIX'=>'.html',
    'URL_HTML_SUFFIX' => 'html',
//    'DEFAULT_FILTER' => 'htmlspecialchars',
    'DEFAULT_FILTER'=>'htmlspecialcharsx',
    
    //路由配置
    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array(
        'v/:id' => 'vip/index/index',
        'book/:catid' => 'home/books/index',
        'bookinfo/:bkid' => 'home/books/detail',
        'article/:atid' => 'home/books/articl',        
    ),
    
    
    'TAGLIB_BUILD_IN'       =>  'Cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
    
    'SHOW_ERROR_MSG' => true,
//    'TMPL_EXCEPTION_FILE'   =>'./Public/tpl/404.html',
//      'TMPL_EXCEPTION_FILE'   =>'./Public/tpl/system_404.html',
    'TMPL_ACTION_ERROR'     => './Public/tpl/dispatch_jump.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => './Public/tpl/dispatch_jump.html', // 默认成功跳转对应的模板文件
    //用户注册默认信息    

    'COOKIE_HTTPONLY'=>1,                     // Cookie httponly设置
    'COOKIE_PREFIX'=>'rdm',                    // Cookie 前缀
    'COOKIE_KEY'=>'rdmjianbinnet',                  // Cookie 前缀
    'COOKIE_CITYNAME'=>'city_code',                  // Cookie 前缀
    
    'DATA_CACHE_TYPE'=>'File',                  //缓存类型
    'DATA_CACHE_TIME'=>'30',                    //缓存时间
    'DATA_CACHE_KEY'=>'rdmbinsj',              //缓存密钥 避免缓存文件名被猜测到
    
    "DEFAULT_LANG"=>'cn',                       //网站黙认语言
    "LANGUE_TAG"=>'lg',                          //前台语言参数黙认语言
    
    'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__rdmshkjhash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true 
    
    //插件目录
    'PAYMENT_PLUGIN_PATH' => PLUGIN_PATH . 'payment',
    'LOGIN_PLUGIN_PATH' => PLUGIN_PATH . 'login',
    'SHIPPING_PLUGIN_PATH' => PLUGIN_PATH . 'shipping',
    'FUNCTION_PLUGIN_PATH' => PLUGIN_PATH . 'function',
    
    /**
     * 语音合成文件存储目录
     */
    'AIPSAVEPATH'=>array(
        'basepath'=>'voice',
        'rooms'=>'voice/rooms/',
        'build'=>'voice/builds/',
        'loupan'=>'voice/loupans/',
        'news'=>'voice/news/'
    ),

);
