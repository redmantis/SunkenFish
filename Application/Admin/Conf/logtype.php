<?php

/* 
 * 文件：logtype.php
 * 字符：UTF-8
 * 软件为杭州鼎易信息科技有限公司所有，未经授权许可不得使用！
 * 作者：鼎易php技术团队
 * 官网：www.doing.net.cn
 * 邮件: feiyufly001@hotmail.com
 * 创建：redmantis <默鱼 at feiyufly001@hotmail.com> 2016-8-12 20:18:12
 * 最终：Rdm
 */
return array(
 /* 日志类型定义 */
    'LOGTYPE' => array(
        'login_login' => array(
            'name' => '用户登录',
            'value' => 10,
        ),
        'login_loginqz' => array(
            'name' => '用户强制登录',
            'value' => 12,
        ),
         'Login_logout2' => array(
            'name' => '用户被强制登出',
            'value' => 13,
        ),
        
        'login_logout' => array(
            'name' => '用户退出',
            'value' => 11,
        ),
        'ad_add' => array(
            'name' => '添加广告',
            'value' => 21,
        ),
        'ad_update' => array(
            'name' => '修改广告',
            'value' => 22,
        ),
         'ad_del' => array(
            'name' => '删除广告',
            'value' => 23,
        ),
        'news_add' => array(
            'name' => '发布文章',
            'value' => 31,
        ),
        'news_update' => array(
            'name' => '修改文章',
            'value' => 32,
        ),
          'news_single' => array(
            'name' => '修改单页资料',
            'value' => 34,
        ),        
        
        'news_batchoperate' => array(
            'name' => '批处理文章',
            'value' => 33,
        ),
        
         'topic_add' => array(
            'name' => '发布专题',
            'value' => 35,
        ),
        'topic_update' => array(
            'name' => '修改专题',
            'value' => 36,
        ),
        
        'topic_batchoperate' => array(
            'name' => '批处理专题',
            'value' => 37,
        ),
        
        'common_sortset' => array(
            'name' => '排序',
            'value' => 41,
        ),
        
        'common_changeattr' => array(
            'name' => '修改属性',
            'value' => 42,
        ),
        /* 此项占位，具体实现由 42 判断 */
        'common_isshow' => array(
            'name' => '审核',
            'value' => 43,
        ),
        /* 此项占位，具体实现由 42 判断 */
        'common_chanagebaner' => array(
            'name' => '修改栏目banner',
            'value' => 45,
        ),
        /* 此项占位，具体实现由 42 判断 */
        'auth_add' => array(
            'name' => '增加权限规则',
            'value' => 61,
        ),
        'auth_update' => array(
            'name' => '修改权限规则',
            'value' => 62,
        ),
        'auth_delete' => array(
            'name' => '修改权限规则',
            'value' => 63,
        ),

        'category_add' => array(
            'name' => '添加栏目分类',
            'value' => 71,
        ),
        'category_update' => array(
            'name' => '修改栏目分类',
            'value' => 72,
        ),
        
        'columns_add' => array(
            'name' => '添加栏目',
            'value' => 81,
        ),
        'columns_update' => array(
            'name' => '修改栏目',
            'value' => 82,
        ),
        
        'group_add' => array(
            'name' => '添加角色',
            'value' => 91,
        ),
        'group_update' => array(
            'name' => '修改角色',
            'value' => 92,
        ),
        
         'group_delete' => array(
            'name' => '删除角色',
            'value' => 93,
        ),
        'group_detail' => array(
            'name' => '修改角色权限',
            'value' => 94,
        ),       
      
        
        'member_add' => array(
            'name' => '添加管理员',
            'value' => 101,
        ),
        'member_update' => array(
            'name' => '修改管理员',
            'value' => 102,
        ),
        
         'member_delete' => array(
            'name' => '禁用管理员',
            'value' => 103,
        ),
        'member_set' => array(
            'name' => '管理员修改自身资料',
            'value' => 104,
        ),
         'member_access' => array(
            'name' => '修改管理员角色',
            'value' => 105,
        ),
         'member_accesscol' => array(
            'name' => '分配管理栏目',
            'value' => 106,
        ),
        
            'siteset_index' => array(
            'name' => '修改网站配置',
            'value' => 111,
        ),
            'siteset_system' => array(
            'name' => '修改系统配置',
            'value' => 112,
        ),
            'siteset_extent' => array(
            'name' => '修改扩展配置',
            'value' => 113,
        ),
        
            'category_del' => array(
            'name' => '删除栏目分类',
            'value' => 121,
        ),
     
            'category_add' => array(
            'name' => '新增栏目分类',
            'value' => 123,
        ),
               'category_update' => array(
            'name' => '修改栏目',
            'value' => 124,
        ),
        
            'theme_del' => array(
            'name' => '删除模版',
            'value' => 131,
        ),
     
            'theme_add' => array(
            'name' => '新增模版',
            'value' => 133,
        ),
            'theme_update' => array(
            'name' => '修改模版',
            'value' => 134,
        ),
        'birthday_add' => array(
            'name' => '添加员工',
            'value' => 141,
        ),
        'birthday_update' => array(
            'name' => '修改员工',
            'value' => 142,
        ),
         'birthday_del' => array(
            'name' => '删除员工',
            'value' => 143,
        ),
        
          'subsidiary_add' => array(
            'name' => '添加站点',
            'value' => 151,
        ),
        
        'subsidiary_update' => array(
            'name' => '修改站点',
            'value' => 152,
        ),
        
         'subsidiary_updateck' => array(
            'name' => '修改考准标准',
            'value' => 153,
        ),
          'subsidiary_reflash' => array(
            'name' => '刷新考核榜',
            'value' => 154,
        ),
        
        'siteset_cleartrash' => array(
            'name' => '清空回收站',
            'value' => 160,
        ),
        
        'siteset_siteclear' => array(
            'name' => '清空站点',
            'value' => 161,
        ),
        
        'siteset_restore' => array(
            'name' => '站点还原',
            'value' => 162,
        ),
        
        'no_no' => array(
            'name' => '未知操作',
            'value' => 0,
        ),
        
        
         'department_add' => array(
            'name' => '添加部门',
            'value' => 161,
        ),
        
         'department_update' => array(
            'name' => '修改部门',
            'value' => 162,
        ),
        
          'author_add' => array(
            'name' => '添加作者',
            'value' => 163,
        ),
        
         'author_update' => array(
            'name' => '修改作者',
            'value' => 164,
        ),
        
        'counter_bmcounter' => array(
            'name' => '部门统计',
            'value' => 166,
        ),
        
         'counter_counter' => array(
            'name' => '投稿统计',
            'value' => 165,
        ),
        
         'author_index' => array(
            'name' => '作者管理',
            'value' => -1,
        ),
        
         'department_index' => array(
            'name' => '部门管理',
            'value' => -1,
        ),
        
        'member_index' => array(
            'name' => '用户管理',
            'value' => -1,
        ),
        'counter_index' => array(
            'name' => '用户统计',
            'value' => -1,
        ),
        'counter_detail' => array(
            'name' => '用户明细统计',
            'value' => -1,
        ),
        
          'counter_op' => array(
            'name' => '用户日志',
            'value' => -1,
        ),
          'group_index' => array(
            'name' => '角色管理',
            'value' => -1,
        ),
        
        'columns_index' => array(
            'name' => '栏目管理',
            'value' => -1,
        ),
        
        'columns_single' => array(
            'name' => '单页管理',
            'value' => -1,
        ),
         'news_newslist' => array(
            'name' => '新闻管理',
            'value' => -1,
        ),
        'news_index' => array(
            'name' => '投稿审核',
            'value' => -1,
        ),
        'category_index' => array(
            'name' => '栏目分类管理',
            'value' => -1,
        ),
        'columns_topic' => array(
            'name' => '专题管理',
            'value' => -1,
        ),
         'topic_index' => array(
            'name' => '投稿审核',
            'value' => -1,
        ),
        'topic_newslist' => array(
            'name' => '文章管理',
            'value' => -1,
        ),
        'ad_index' => array(
            'name' => '广告管理',
            'value' => -1,
        ),
        'theme_index' => array(
            'name' => '模板管理',
            'value' => -1,
        ),
        'auth_index' => array(
            'name' => '权限明细管理',
            'value' => -1,
        ),
        'subsidiary_index' => array(
            'name' => '站点管理',
            'value' => -1,
        ),
         'subsidiary_check' => array(
            'name' => '考核标准',
            'value' => -1,
        ),
         'news_trash' => array(
            'name' => '回收站',
            'value' => -1,
        ),
         'birthday_index' => array(
            'name' => '员工管理',
            'value' => -1,
        ),       
    ),
);
