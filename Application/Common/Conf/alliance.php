<?php

/* 
 * 邮件: feiyufly001@hotmail.com
 * 创建：redmantis <默鱼 at feiyufly001@hotmail.com> 2016-7-6 15:06:50
 * 最终：Rdm
 * 全站基本配置
 */

return array(
    'ALLIANCE' => array(
        'ADMIN_DETAIL' => 1, //后台记录开启
        'AUTH_SUPERADMIN' => 'admins', //设置超级管理员名称
        'AUTH_GLOBAL_SID' => 2, //全局管理员站点ID
        'USER_AUTH_MODEL' => 'admin', //用户表名称
        'USER_AUTH_KEY' => 'manageid', //用户认证SESSION中的识别号
        'USER_AUTH_FLASH' => 'manageflash', //用户认证SESSION刷新标志
        'SELECT_ALL' => 0, //1 结果集中显示全部站点资料，0 只显示单个站点资料  （全局帐号有效）
        'MYSITEURL' => 'myurl', //用户登录后台时，保存在COOKIE中的域名
        'USER_LOGIN_KEY' => 'fakeruhecom', //用户认证SESSION中的识别号
    ),

    'VAR_PAGE' => 'p', //分页参数
    
    'ENABLE_READONLY' => true,//只读模式开启
    
    //日志配置
    'LOG_RECORD' => false, // 进行日志记录
    'LOG_EXCEPTION_RECORD' => true, // 是否记录异常信息日志
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO', // 允许记录的日志级别    
    'LOG_EXCEPTION_RECORD' => false, // 是否记录异常信息日志
    'LOG_FILE_SIZE' => 1024 * 1024, // 日志文件大小限制
    'LOG_TYPE' => 'File', // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    
    // 文件服务器的配置
    'IMAGESEVURL' => '/Uploads/image/', //图片服务器根路径
    'SYSIMAGESEVURL' => '/Uploads/system/', //系统图片目录
    'IMAGEROOTPATH' => '/Uploads/image/', //图片保存根路径   
    'IMAGESEVRICE' => '', //图片服务器
    'VIDEOSEVURL' => '/Uploads/video/', //视频服务器根路径
    'VIDEOROOTPATH' => '/Uploads/video/', //视频保存根路径   
    'VIDEOSEVRICE' => '', //视频服务器
    'FILESEVURL' => '/Uploads/file/', //文件服务器根路径
    'FILEROOTPATH' => '/Uploads/file/', //文件保存根路径   
    'FILESEVRICE' => '', //文件服务器
    
    'UPLOADTYPE' => array(//上传文件类型控制
        'pic' => array('jpg', 'gif', 'png', 'jpeg', 'ico'),
        'file' => array('rar', 'txt', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip', 'exe', 'crd', 'ceb'),
        'video' => array('wmv', 'avi', 'mp4'),
        'pic_size'=>20480,
        'file_size'=>20480
    )    
);
