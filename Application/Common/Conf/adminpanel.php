<?php

return array(
    //<editor-fold defaultstate="collapsed" desc="后台用户注册模板">
    'ADMINPANLE' => array(
        'm_id' => array('des' => '',
            'model' => 'hidden',
            'value' => 0,
        ),  
        'm_name' => array('des' => '用户名',
            'model' => 'input',
            'width' => 200,
            'readonly' => 1,
            'value' => '',
        ),
        'truename' => array('des' => '姓名',
            'model' => 'input',
            'width' => 200,
            'tableindex' => 'base',
            'value' => '',
        ),
        'head_pic' => array('des' => '头像', 'des2' => '参考尺寸：120 * 120',
            'model' => 'image',
            'inputtype'=>'hidden',
//            'tableindex'=>'base',
            'value' => ''
        ),
        'tel' => array('des' => '岗位', 'des2' => '<br>版主:修改本版块结构，审核本版块稿件,<br>编辑:审核所有稿件<br><b>注：岗位识别开启后生效，必须配合角色权限控制</b>',
            'model' => 'hidden',
            'value' => ' ',
            'source' => 'ADMINTYPE',
            'titlecls' => 'help-block',
//              'tableindex'=>'base',
        ),
        'password' => array('des' => '密码', 'des2' => '不修改时请保持为空，密码必须由大写英文字母、小写英文字母和数字组合，长度不小于8位',
            'model' => 'password',
            'verify'=>'required',
            'width' => 200,
            'value' => ''
        ),
        'repassword' => array('des' => '确认密码',
            'model' => 'password',
            'verify'=>'required',
            'width' => 200,
            'value' => ''
        ),
        'm_grade' => array('des' => '用户类型',
            'model' => 'rdlist',
            'value' => 2,
            'source' => array(
                'idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'managegrade', 
            ),
        ),

        'sex' => array('des' => '性别',
            'model' => 'rdlist',
            'value' => '',
            'verify'=>'mustradio',//输入验证类型
            'source' => array(
                'idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'gender', //数据源类型为 attrmap 时的辅助字段 取该标签所包含的所有子标签
            ),
            'tableindex' => 'base',
        ),
        'birthday' => array('des' => '生日',
            'model' => 'datepicker',//input
            'dateformat' => array(
                'type' => 'date',
                'format' => 'yyyy-MM-dd',
                'trancemat' => 'Y-m-d',
            ),
            'value' => "",
            'tableindex' => 'base',
        ),

        'usertype' => array('des' => '用户类型',
            'model' => 'hidden',
            'value' => 1,
            'source' => 'USERTYPE'
        ),
        'status' => array('des' => '用户状态',
            'model' => 'rdlist',
            'value' => 1,
            'source' => array(
                'idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opcolse', //数据源类型为 attrmap 时的辅助字段 取该标签所包含的所有子标签
            ),
        ),
    ),
    //</editor-fold>  

);
