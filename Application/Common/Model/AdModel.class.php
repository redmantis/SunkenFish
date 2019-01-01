<?php
namespace Common\Model;

use Common\Base;

//广告列表
class AdModel extends Base\SModel {


    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "ad";
    

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc ,id asc',
        ),        
        'orderby2' => array(
            'order' => 'sortid desc,id desc',
        ),
    );
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="列表显示项">
    public $ShowMap = array(
        'id' => '序号',
        'ad_name' => '标题',
        'ad_link' => '链接',
        'position_mark' => '位置标签',
        'ad_code' => array(
            'type' => 'img',
            'title' => '图片',
            'titlefild' => 'ad_name',
        ),
        'position' => '类型',
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
        ),
//        'start_time' => array(
//            'type' => 'time',
//            'format' => 'Y-m-d H:i:s',
//            'title' => '修改时间',
//        ),
        'isshow' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'Ad',
            'title' => '显示',
        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            'btnlist' => array(
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'update',
                        'fields' => array(
                            'id' => 'id',
                        ),
                    )
                ),
                'del' => array(
                    'title' => '删除',
//                    'message'=>'DelAllWarning',
                    'link' => array(
                        'mod' => 'diag_del',
                        'url' => 'del',
                        'cls' => 'diag_del',
                        'fields' => array(
                            'id' => 'id'
                        ),
                    )
                ),
            ),
        ),
    );

    //</editor-fold>

    public function addnew($model) {
        $model['media_type'] = 0;
        $model['link_phone'] = "";
        $model['link_email'] = "";
        $model['click_count'] = 0;
        $rs = parent::addnew($model);
        return $rs;
    }

    protected function trance_model($model) {
        $n = C("ADPANEL.position_id");
        $mp = $n['source']['hash'];
        $model['position'] = $mp[$model['position_id']];
        return $model;
    }

}
