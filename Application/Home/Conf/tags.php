<?php
return array(
//注意这里，官方的文档解释感觉有误（大家自行分辨），
//TP3.2.3用Behavior\CheckLang会出错，
//提示：Class 'Behavior\CheckLang' not found
//    'app_begin' => array('Behavior\CheckLangBehavior','Behavior\CronRunBehavior'),
    
    // 定时任务
    // 必须使用app_end 原因还未查明，知道的请告知我一下谢谢
         'app_begin' => array('Home\\Behavior\\SetthemeBehavior','Home\\Behavior\\ReadHtmlCacheBehavior','Home\\Behavior\\CronRunBehavior'),    
      // 读取静态缓存
);
