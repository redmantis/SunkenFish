<?php
return array(
    'CRON_WAITIME'=>10,
    'SESSIONEXPIRE'              =>  7200,
    'CRON_CONFIG_ON' => true, // 是否开启自动运行
    'CRON_CONFIG' => array(   
        '在线列表刷新' => array('Manage/Login/crons', '10', ''), //路径(格式同R)、间隔秒（0为一直运行）、指定一个开始时间  
        '标记活动用户' => array('Manage/Login/active', '5', ''), //路径(格式同R)、间隔秒（0为一直运行）、指定一个开始时间   
    ),  
    
    'SESSION_OPTIONS'         =>  array(
        'name'                =>  'RDMSESSION',                 
        'expire'              =>  7200,
        'use_trans_sid'       =>  1,                    
        'use_only_cookies'    =>  0,
    ),
);