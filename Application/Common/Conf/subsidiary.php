<?php
/**
 * @author 默鱼 <feiyufly001@hotmail.com>
 * 站点参数定义
 */
return array(
    'SUBSIDIARY' => array(
        'sid_id' => array('des' => '所属网站',
            'model' => 'hidden',
            'listtopid' => 0,
            'listtopname' => '所有网站',
            'source' => array('idfiled' => 'sid_id',
                'valuefiled' => 'sid_name',
                'table' => 'subsidiary',
            ),
            'value' => ''
        ),
    ),
    
    /* 站点信息输入面板 */
    'SUBSIDIARYPANLE' => array(
        'sid_name' => array(
            'des' => '站点名称',
            'parentkey' => 'Subsid',
            'model' => 'input',            
            'value' => '',
        ),
        'sid_sitname' => array('des' => '站点域名',
            'parentkey' => 'Subsid',
            'model' => 'input',
            'value' => '',
        ),
        'sid_dir' => array('des' => '静态目录', 'des2' => '输入后不可更改',
            'model' => 'input',
            'parentkey' => 'Subsid',
            'readonly' => 0,
            'value' => ''
        ),
        
        'theme' => array('des' => '主题类型',
            'tableindex' => 'tableCart_base',
            'parentkey' => 'Subsid',
            'model' => 'dropdown',
            'listtopid' => '',
            'listtopname' => '选择主题',
            'source' => array(
                'idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'theme_skin',
            ),
        ),
        
        'cache' => array(
            'des' => '缓存', 
            'des2' => '最大缓存时间，单位（秒）',
             'parentkey' => 'Subsid',
            'model' => 'input',
            'value' => '0'
        ),
        'sortid' => array('des' => '排序',
            'model' => 'input',
            'value' => '0'
        ),
    ),
   
);

