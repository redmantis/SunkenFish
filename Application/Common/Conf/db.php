<?php

return array(
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => 'localhost', //服务器地址
    'DB_NAME' => 'SunkenFish',//shzf
    'DB_USER' => 'root', // 用户名  
    'DB_PWD' => '', // 密码 
    'DB_PORT' => 3306, // 端口  
    'DB_PREFIX' => 'jp_', // 数据库表前缀  
    'DB_CHARSET' => 'utf8', // 字符集  
    'DB_DEBUG' => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_PARAMS' => array(
        PDO::ATTR_PERSISTENT => true,
    ),
);
