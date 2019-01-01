<?php

return array(
    //<editor-fold defaultstate="collapsed" desc="基础搜索条件">
    'BASEFILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'linkman_keyname',
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="分类信息搜索条件">
    'GBOOKFILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'hash',
                'hash' => array('regionlist' => '求租区域', 'loupanname' => '意向小区', 'linkman' => '称呼', 'mobile' => '手机')
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),       
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="用户查询条件">
    'USERFILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'search_keyname',
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="余额变更日志搜索项">
    'MONEYFILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'search_keyname',
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'moneytype' => array('des' => '变更项目',
            'model' => 'dropdown',
            'listtopid' => '',
            'listtopname' => '全部项目',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagmark',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'money_taglist',
            ),
            'value' => ''
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="积分日志搜索项">
    'SCOREFILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'search_keyname',
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'scoretype' => array('des' => '变更项目',
            'model' => 'dropdown',
            'listtopid' => '',
            'listtopname' => '全部项目',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagmark',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'score_taglist',
            ),
            'value' => ''
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="订单搜索项">
    'ORDERFILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'order_keyname',
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'placeholder' => '订单号/联系人/联系电话',
            'model' => 'input',
            'value' => '',
        ),
        'status' => array('des' => '订单状态',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '全部',
            'value' => -1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'order_status',
            ),
            'value' => ''
        ),
        'startday' => array('des' => '发布时间',
            'tableindex' => 'tableCart_base',
            'model' => 'datepicker',
            'dateformat' => array('type' => 'date', 'format' => 'yyyy-MM-dd'),
            'width' => '120',
            'value' => "",
        ),
        'endday' => array('des' => '发布时间',
            'tableindex' => 'tableCart_base',
            'model' => 'datepicker',
            'dateformat' => array('type' => 'date', 'format' => 'yyyy-MM-dd'),
            'width' => '120',
            'value' => "",
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="插件搜索">
    'PLUGINFILTER' => array(
        'plugintype' => array('des' => '插件类型',
            'model' => 'dropdown',
            'value' => 'payment',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'plugin_type',
            ),
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="新闻搜索条件">
    'NEWSRFILTER' => array(
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'm_id' => array('des' => '用户',
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '所有用户',
            'source' => array('idfiled' => 'm_id',
                'valuefiled' => 'truename', //m_name
                'table' => 'admin',
            ),
            'value' => ''
        ),
        'colid' => array('des' => '栏目',
            'model' => 'tree',
            'mod' => 'all',
            'listtopid' => 0,
            'listtopname' => '根栏目',
            'source' => array(
                'idfiled' => 'id',
                'valuefiled' => 'title',
                'sourcetyp' => 'dbmodel',
                'dbmodel' => '\Common\News\ColumnsModel',
            ),
            'value' => 0,
        ),
        'isshow' => array('des' => '审核',
            'model' => 'dropdown',
            'value' => -1,
            'listtopid' => -1,
            'listtopname' => '全部',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'shorttitle',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'isshow_status',
            ),
        ),
        'attr' => array('des' => '属性',
            'model' => 'attrcklist',
            'width' => '350',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'news_attrlist',
            ),
            'value' => '0'
        ),
//        'isvouch' => array('des' => '通知',
//            'model' => 'dropdown',
//            'value' => -1,
//            'source' => 'VOUCHRDL'
//        ),
//        'istop' => array('des' => '置顶',
//            'model' => 'dropdown',
//            'value' => -1,
//            'source' => 'TOPRDL'
//        ),
//        'ishot' => array('des' => '轮播',
//            'model' => 'dropdown',
//            'value' => -1,
//            'source' => 'HOTRDL'
//        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="相册搜索项">
    'PHOTOFILTER' => array(
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'phototype' => array('des' => '相册类型',
//              'des2'=>'相册的类型',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '全部',
            'value' => -1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'photo_type',
            ),
            'value' => ''
        ),
        'is_show' => array('des' => '审核',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '全部',
            'value' => -1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'isshow_status',
            ),
            'value' => ''
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="会员搜索项">
    'CUSTOMERILTER' => array(
        'keyname' => array('des' => '查询对象',
            'model' => 'dropdown',
            'value' => '',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'search_keyname',
            ),
            'value' => ''
        ),
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'status' => array('des' => '状态',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '全部',
            'value' => -1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opcolse',
            ),
            'value' => ''
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="乡镇街道搜索">
    'SUBREGIONSFILTER' => array(
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'gbcode' => array('des' => '关键词',
            'model' => 'hidden',
            'value' => '',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="地区搜索">
    'REGIONSFILTER' => array(
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'province' => array('des' => '省份',
            'model' => 'dropdown',
            'listtopid' => '',
            'listtopname' => '选择省份',
            'value' => '',
            'cls' => 'changeprovince',
            'source' => array(
                'idfiled' => 'gbcode',
                'valuefiled' => 'province',
                'table' => 'LbsRegion',
                'orderstr' => 'gbcode asc',
            ),
        ),
        'is_hot' => array('des' => '热门', 'des2' => '热门',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '全部',
            'value' => -1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'open_status',
            ),
            'value' => ''
        ),
        'is_region_hot' => array('des' => '地区热门', 'des2' => '地区热门',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '全部',
            'value' => -1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'open_status',
            ),
            'value' => ''
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="楼宇搜索">
        'BUILDINGSFILTER' => array(
    //         'keyname' => array('des' => '查询对象',
    //            'model' => 'dropdown',          
    //            'value' => '',
    //            'model' => 'dropdown',
    //            'source' => array('idfiled' => 'tagvalue',
    //                'valuefiled' => 'title',
    //                'sourcetyp' => 'attrmap',
    //                'tagmark' => 'search_keyname',
    //            ),
    //            'value' => ''
    //        ),
            'key' => array('des' => '关键词',
                'model' => 'input',
                'value' => '',
            ),
            'isshow' => array('des' => '审核',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => -1,
                'model' => 'dropdown',
                'source' => array('idfiled' => 'tagvalue',
                    'valuefiled' => 'title',
                    'sourcetyp' => 'attrmap',
                    'tagmark' => 'isshow_status',
                ),
                'value' => ''
            ),
            'modid' => array('des2' => '房源类型',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => -1,
                'model' => 'dropdown',
                'source' => array('idfiled' => 'tagvalue',
                    'valuefiled' => 'title',
                    'sourcetyp' => 'attrmap',
                    'tagmark' => 'YunyinType',
                ),
                'value' => ''
            ),
            'province' => array('des' => '省份',
                'model' => 'dropdown',
                'listtopid' => '',
                'listtopname' => '选择省份',
                'value' => '',
                'cls' => 'changeprovince',
                'source' => array(
                    'idfiled' => 'gbcode',
                    'valuefiled' => 'province',
                    'table' => 'LbsRegion',
                    'orderstr' => 'gbcode asc',
                ),
            ),
        ),
        //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="客房搜索">
        'ROOMSFILTER' => array(
            'key' => array('des' => '关键词',
                'model' => 'input',
                'value' => '',
            ),
            'isshow' => array('des2' => '审核',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => -1,
                'model' => 'dropdown',
                'source' => array('idfiled' => 'tagvalue',
                    'valuefiled' => 'title',
                    'sourcetyp' => 'attrmap',
                    'tagmark' => 'isshow_status',
                ),
                'value' => ''
            ),
            'is_vouch' => array('des2' => '推荐类型',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => -1,
                'model' => 'dropdown',
                'source' => array('idfiled' => 'tagvalue',
                    'valuefiled' => 'title',
                    'sourcetyp' => 'attrmap',
                    'tagmark' => 'vouch_type',
                ),
                'value' => ''
            ),
            'buildid' => array('des' => '所在楼宇',
                'model' => 'hidden',
                'value' => 0,
            ),
            'province' => array('des' => '省份',
                'model' => 'dropdown',
                'listtopid' => '',
                'listtopname' => '选择省份',
                'value' => '',
                'cls' => 'changeprovince',
                'source' => array(
                    'idfiled' => 'gbcode',
                    'valuefiled' => 'province',
                    'table' => 'LbsRegion',
                    'orderstr' => 'gbcode asc',
                ),
            ),
        ),
        //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="楼盘搜索">
        'LOUPANFILTER' => array(
            'key' => array('des' => '关键词',
                'model' => 'input',
                'value' => '',
            ),
            'isshow' => array('des' => '审核',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => -1,
                'model' => 'dropdown',
                'source' => array('idfiled' => 'tagvalue',
                    'valuefiled' => 'shorttitle',
                    'sourcetyp' => 'attrmap',
                    'tagmark' => 'isshow_status',
                ),
                'value' => ''
            ),
            'is_vouch' => array('des' => '推荐类型',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => -1,
                'model' => 'dropdown',
                'source' => array('idfiled' => 'tagvalue',
                    'valuefiled' => 'title',
                    'sourcetyp' => 'attrmap',
                    'tagmark' => 'vouch_type',
                ),
                'value' => ''
            ),
    //         'regionlink' => array('des' => '城市选择',
    //            'tableindex' => 'tableCart_base',
    //            'model' => 'linkage',
    //            'search' => 'lay-search',
    //            'inputtype' => 'hidden', //隐藏文本域            
    //            'value' => '',
    //            'listtopid' => '',    
    //            'listtopname' => '选择省份',
    //            'source' => array(
    //                'idfiled' => 'gbcode',
    //                'valuefiled' => 'province',
    //                'table' => 'LbsRegion',
    //                'orderstr' => 'sortid asc, gbcode asc',
    //                'extmap'=>['gbcode'=>['like','%0000']]
    //            ),
    //            'relation-set' => array(
    //                'valuemodel' => 2, //取值方式 1：取联合值  2：取终点值
    //                'delimiter' => '/', //值间连接符  取联合值时，值间的连扫符
    //                'valuectrl' => 'streetcode', //储值控件  取值后存储的对像
    //            ),
    //            'relation' => array(
    //                array(
    //                    "id" => "provinceid", //当前控件ID
    //                    "next" => "cityid", //联动控件ID
    //                    "pramname" => "regioncode", //传递参数名称
    //                    'action' => 'get_citylist', //调用联动数据的API接口 入口名称
    ////                    'extmap'=>['sortid'=>'gt|0']
    //                ),
    //                array(
    //                    "id" => 'cityid',
    //                    "next" => "regionid", //联动控件ID
    //                    "pramname" => "regioncode", //传递参数名称
    //                    'action' => 'get_regionidlist', //调用联动数据的API接口 入口名称
    ////                    'extmap'=>['sortid'=>'gt|0']
    //                ),
    //                 array(
    //                    "id" => 'regionid',
    //                    "next" => "streetcodeid", //联动控件ID
    //                    "pramname" => "gbcode", //传递参数名称
    //                    'action' => 'get_streetlist', //调用联动数据的API接口 入口名称
    //                    'extmap'=>['linkage'=>'1']
    //                ),
    //                 array(
    //                    "id" => 'streetcodeid',
    //                    'isend' => 1, //终点标记
    //                ),
    //                
    //            ),
    //        ),
            'province' => array('des' => '省份',
                'model' => 'dropdown',
                'listtopid' => '',
                'listtopname' => '选择省份',
                'value' => '',
                'cls' => 'changeprovince',
                'source' => array(
                    'idfiled' => 'gbcode',
                    'valuefiled' => 'province',
                    'table' => 'LbsRegion',
                    'orderstr' => 'gbcode asc',
                ),
            ),
        ),
        //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="管理员搜索项">
        'ADMINFILTER' => array(
            'key' => array('des' => '关键词',
                'model' => 'input',
                'value' => '',
            ),
            'status' => array('des' => '状态',
                'model' => 'dropdown',
                'listtopid' => -1,
                'listtopname' => '全部',
                'value' => 1,
                'source' => 'ADMINSTATUS',
            ),
        ),
        //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="网站配置项">
//    'TOPRDL' => array(
//        '全部' => -1,
//        '置顶' => 1, //单页页栏目
//        '普通' => 0, //没有子栏目   
//    ),
//    'VOUCHRDL' => array(
//        '全部' => -1,
//        '通知' => 1, //单页页栏目
//        '普通' => 0, //没有子栏目   
//    ),
//    'HOTRDL' => array(
//        '全部' => -1,
//        '轮播' => 1, //单页页栏目
//        '普通' => 0, //没有子栏目   
//    ),
//    'SHOWRDL' => array(
//        '全部' => -1,
//        '待审核' => 0, //没有子栏目
//        '已审核' => 1, //单页页栏目       
//        '已退回' => 2, //单页页栏目
//    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="统计过滤条件">
    'COUNTERFILTER' => array(
        'colid' => array('des' => '栏目',
            'model' => 'dropdowncol',
            'listtopid' => 0,
            'listtopname' => '选择栏目',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'table' => 'SubColumns',
            ),
            'value' => ''
        ),
        'key' => array('des' => '用户名',
            'model' => 'input',
            'value' => '',
            'width' => '200',
        ),
        'start' => array('des' => '开始日期',
            'model' => 'input',
            'value' => '',
            'cls' => 'startday',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
        'end' => array('des' => '截止日期',
            'model' => 'input',
            'cls' => 'endday',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="统计过滤条件">
    'COUNTERFILTER2' => array(
        'm_id' => array('des' => '用户',
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '所有用户',
            'source' => array('idfiled' => 'm_id',
                'valuefiled' => 'truename',
                'table' => 'admin',
            ),
            'value' => ''
        ),
        'start' => array('des' => '开始日期',
            'model' => 'input',
            'cls' => 'startday',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
        'end' => array('des' => '截止日期',
            'model' => 'input',
            'cls' => 'endday',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="来源统计过滤条件">
    'AUTHORCOUNTERFILTER' => array(
        'deparmentid1' => array('des' => '部门',
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '选择部门',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'table' => 'Department',
            ),
            'cls' => 'deparmentchange',
            'value' => '',
        ),
        'author1' => array('des' => '作者',
            'model' => 'autocomplete',
            'cls' => 'autocomplete',
            'handdleid' => 'deparmentid1',
            'width' => 100,
            'value' => '',
        ),
        'colid' => array('des' => '栏目',
            'model' => 'dropdowncol',
            'listtopid' => 0,
            'listtopname' => '选择栏目',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'table' => 'SubColumns',
            ),
            'value' => ''
        ),
        'start' => array('des' => '开始日期',
            'model' => 'input',
            'value' => '',
            'cls' => 'startday',
            'value' => date('Y-m-1', time()),
//            'value' => date('Y-m-d', strtotime('-1 month')), 
            'width' => '100',
        ),
        'end' => array('des' => '截止日期',
            'model' => 'input',
            'cls' => 'endday',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="来源统计过滤条件">
    'DEPARTMENTCOUNTERFILTER' => array(
        'deparmentid' => array('des' => '部门',
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '选择部门',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'table' => 'Department',
            ),
            'value' => '',
        ),
        'colid' => array('des' => '栏目',
            'model' => 'dropdowncol',
            'listtopid' => 0,
            'listtopname' => '选择栏目',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'table' => 'SubColumns',
            ),
            'value' => ''
        ),
        'start' => array('des' => '开始日期',
            'model' => 'input',
            'value' => '',
            'cls' => 'startday',
            'value' => date('Y-m-1', time()),
//            'value' => date('Y-m-d', strtotime('-1 month')), 
            'width' => '100',
        ),
        'end' => array('des' => '截止日期',
            'model' => 'input',
            'cls' => 'endday',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="操作日志搜索项">
    'OPFILTER' => array(
        'm_id' => array('des' => '用户',
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '所有用户',
            'source' => array('idfiled' => 'm_id',
                'valuefiled' => 'truename',
                'table' => 'admin',
            ),
            'value' => ''
        ),
        'logtype' => array('des' => '操作',
            'model' => 'dropdown',
            'listtopid' => -1,
            'listtopname' => '所有操作',
            'source' => 'LOGTYPE',
            'value' => -1,
        ),
        'start' => array('des' => '开始日期',
            'model' => 'input',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
        'end' => array('des' => '截止日期',
            'model' => 'input',
            'value' => '',
            'value' => date('Y-m-d', time()),
            'width' => '100',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="模板搜索项">
    'THEMEFILTER' => array(
        'key' => array('des' => '', //关键字
            'model' => 'input',
            'value' => '',
            'width' => '200',
            'cls' => 'form-control form-control-solid placeholder-no-fix form-group'
        ),
        'theme' => array('des' => '', //主题类型
            'tableindex' => 'tableCart_base',
            'model' => 'modeltree',
            'mod' => 'FinalNode',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'dbmodel' => '\Common\Gmodel\AttrsModel',
                'pmap' => array('tagmark' => 'NEWS_SKIN')
            ),
        ),
        'collev' => array('des' => '', //类型
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '选择模板类型',
            'value' => 0,
            'source' => 'COLLEVLIST',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="部门收索项">
    'DEPARTMENTSEARCH' => array(
//        'department' => array('des' => '部门名称',
//            'model' => 'input',
//            'value' => ''
//        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="站点配置项">
    'ATTRSSEARCH' => array(
        'opmodel' => array('des' => '选择操作项',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opmodel',
            ),
            'value' => ''
        ),
        'tagtype' => array('des' => '属性类型',
            'tableindex' => 1,
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'tagtype',
            ),
            'value' => ''
        ),
        'aimcol' => array('des' => '选择目标属性',
            'tableindex' => 1,
            'model' => 'modeltree',
            'mod' => 'all', //FinalNode,all,NotSelf
            'listtopid' => 0,
            'listtopname' => '根属性',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'dbmodel' => '\Common\Gmodel\AttrsModel',
            ),
            'value' => 0
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="属性分类搜索项">
    'ATTRSSEARCH' => array(
        'opmodel' => array('des' => '选择操作项',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opmodel',
            ),
            'value' => ''
        ),
        'tagtype' => array('des' => '属性类型',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'tagtype',
            ),
            'value' => ''
        ),
        'aimcol' => array('des' => '选择目标属性',
            'tableindex' => 1,
            'model' => 'modeltree',
            'mod' => 'all', //FinalNode,all,NotSelf
            'listtopid' => 0,
            'listtopname' => '根属性',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'dbmodel' => '\Common\Gmodel\AttrsModel',
            ),
            'value' => 0
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="商品分类搜索项">
    'GOODSCATESEARCH' => array(
        'opmodel' => array('des' => '选择操作项',
            'model' => 'dropdown',
            'source' => array('idfiled' => 'tagvalue',
                'valuefiled' => 'title',
                'sourcetyp' => 'attrmap',
                'tagmark' => 'opmodel',
            ),
            'value' => ''
        ),
        'aimcol' => array('des' => '选择目标属性',
            'tableindex' => 1,
            'model' => 'modeltree',
            'mod' => 'all', //FinalNode,all,NotSelf
            'listtopid' => 0,
            'listtopname' => '根属性',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'dbmodel' => '\Common\Gmodel\GcateModel',
            ),
            'value' => 0
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="商品搜索项">
    'GOODSSEARCH' => array(
        'key' => array('des' => '关键词',
            'model' => 'input',
            'value' => '',
        ),
        'cat_id' => array('des' => '分类',
            'model' => 'tree',
            'mod' => 'FinalNode',
            'listtopid' => 0,
            'listtopname' => '选择分类',
            'source' => array(
                'idfiled' => 'id',
                'valuefiled' => 'title',
                'sourcetyp' => 'dbmodel',
                'dbmodel' => '\Common\Gmodel\GcateModel',
            ),
            'value' => 0
        ),
//        'brand_id' => array('des' => '品牌',
//            'tableindex' => 'tableCart_base',
//            'model' => 'modeltree',
//            'mod' => 'all', //FinalNode,all,NotSelf
//            'listtopid' => 0,
//            'listtopname' => '选择品牌',
//            'source' => array('idfiled' => 'tagvalue',
//                'valuefiled' => 'title',
//                'dbmodel' => '\Common\Gmodel\AttrsModel',
//                'pmap' => array('tagmark' => 'AboutGoods_brand')
//            ),
//            'value' => 0
//        ),       
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="列表查询按钮组">
    'BUTTONLIST' => array(
        'add' => array(
            'title' => '添加',
            'mod' => 'diag',
            'url' => 'add',
        ),
        'batchoperate' => array(
            'title' => '批处理',
            'mod' => 'diag_deleteall',
            'cls' => 'diag_deleteall',
            'url' => 'batchoperate',
        ),
        'pub_menu' => array(
            'title' => '生成菜单',
            'message' => '生成菜单',
            'mod' => 'diag_deleteall',
            'cls' => 'diag_deleteall',
            'url' => 'pub_menu',
        ),
    ),
    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="广告查询">
    'LINKSEARCH' => array(
        'position_id' => array(
            'des' => '广告位置',
            'model' => 'dropdown',
            'listtopid' => 0,
            'listtopname' => '选择类型',
            'source' => array(
                'sourcetyp' => 'hash',
                'hash' => [1 => 'PC',
                    2 => 'MOB',
                    3 => 'APP'],
            ),
            'value' => 0
        ),
    ),
    //</editor-fold>
);
