<?php
/**
 * Description of authpanle
 * auth类输入模板
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
return array(
    
    //<editor-fold defaultstate="collapsed" desc="权限配置">
    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'auth_group_access', //用户组明细表
        'AUTH_RULE' => 'auth_rule', //权限规则表
        'AUTH_USER' => 'admin', //用户信息表
        'AUTH_SUPERMAN' => 1, //系统管理员id 删除用户的时候用这个禁止删除
        'AUTH_EXCLUDE' => array('help/index', 'index/index', 'index/top', 'index/top', 'index/main', 'index/left', 'index/cache_clear', 'index/errormsg', 'index/getcheckcount', 'index/welcome', 'Common/reviewfile', 'Common/getauthorlist','Common/getaction'),
    ),  
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="权限组配置">
   'GROUPPANLE' => array(
        'title' => array('des' => '角色名称',
            'model' => 'input',
            'value' => ''
        ),
       'status' => array('des' => '规则状态',
            'tableindex' => 'tableCart_base',
            'model' => 'rdlist',
            'value' => 1,
            'source' => array(
                'idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opcolse', //数据源类型为 attrmap 时的辅助字段 取该标签所包含的所有子标签
            ),
        ),
        'sortid' => array('des' => '排序',
            'model' => 'input',
            'value' => '0'
        ),
    ),
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="权限规则输入模版">
     'AUTHPANLE' => array(
         'title' => array('des' => '规则名称',
            'tableindex' => 'langue',
            'model' => 'input',
            'value' => ''
        ),
        'shorttitle' => array('des' => '短标题',
            'tableindex' => 'langue',
            'model' => 'input',
            'value' => ''
        ),
        'icon' => array('des' => '图标',
            'tableindex' => 'langue',
            'model' => 'input',
            'value' => ''
        ),
        'thumb' => array('des' => '图片',
            'tableindex' => 'langue',
            'model' => 'image',
            'inputtype' => 'hidden',
            'value' => ''
        ), 
        'name' => array('des' => '规则',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'linkage',
            'search' => 'lay-search',
            'inputtype'=>'hidden',//隐藏文本域            
            'value' => '',
            'source' => array(
                'sourcetyp' => 'map2',
            ),
            'relation-set' => array(
                'valuemodel' => 1, //取值方式 1：取联合值  2：取终点值
                'delimiter' => '/', //值间连接符  取联合值时，值间的连扫符
                'valuectrl' => 'name', //储值控件  取值后存储的对像
            ),
            'relation' => array(
                array(
                    "id" => "ctrlid",
                    "next" => "actionid",
                    "pramname" => "ctrlname",
                    'action' => 'getaction',
                ),
                array(
                    "id" => 'actionid',
                    'isend' => 1, //终点标记
                ),
            ),
        ),         
        'menuflag' => array('des' => '菜单图标',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'input',
            'value' => ''
        ),         
        'extpram' => array('des' => '附加参数',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'text',
            'width' => '60',
            'height' => '2',
            'value' => '',
        ),
         
        'parentid' => array('des' => '根规则',
            'tableindex' => 'tableCart_base',
            'model' => 'tree',
            'mod'=>'NotSelf',
            'listtopid' => 0,
            'listtopname' => '顶级菜单',
           'ignore'=>'lay-ignore',
            'source' => array(
                'idfiled' => 'id',
                'valuefiled' => 'title',
                'sourcetyp' => 'dbmodel',
                'dbmodel' => '\Common\Model\AuthRuleModel',
            ),
            'value' => 0
        ),
         
        'status' => array('des' => '规则状态',
            'tableindex' => 'tableCart_base',
            'model' => 'rdlist',
            'value' => 1,
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opcolse',
            ),
        ),
        'issys' => array('des' => '规则类型', 'des2' => '普通：分配给一般用户使用，总站：全局管理员使用，系统：只有系统管理员才能使用',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'rdlist',
            'value' => 0,
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'rule_issys',
            ),
        ),
        'ismenu' => array('des' => '菜单位置',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'rdlist',
            'value' => 0,
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'rule_ismenu',
            ),
        ),
        'type' => array('des' => '启用附加规则',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'rdlist',
            'value' => 1,
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opcolse',
            ),
        ),
        'condition' => array('des' => '附加规则',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'AuthConfig',
            'model' => 'input',
            'value' => ''
        ),
        'sortid' => array('des' => '排序',
            'tableindex' => 'tableCart_base',
            'model' => 'input',
            'value' => '0'
        ), 
            'table_card' => array(
            "tableCart_base" => '基础参数',          
        ),
    ), 
     //</editor-fold>
);
