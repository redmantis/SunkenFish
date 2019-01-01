<?php
namespace Common\News;
use Common\Base;

/**
 * 新闻栏目
 */
class NewsSingleModel extends Base\BtreeModel {
    
    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "news_single";
    protected $viewpath = "viewpath";
    protected $deltree = false;
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc,id asc',
        ),
        'orderby2' => array(
            'order' => 'sortid desc,id desc',
        ),
    );
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="列表显示项">
    public $ShowMap = array(
        'id' => '序号',
        'title' => '标题',
        'shorttitle' => '短标题',
        'viewpath' => array(
            'title' => '访问路径',
        ),
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'SubColumns',
            'idd' => 'id',
        ),
        'shownav' => array(
            'type' => 'rotation',
            'format' => 'menuShow',
            'table' => 'SubColumns',
            'title' => '导航位置',
        ),
        'isshow' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'SubColumns',
            'title' => '审核',
        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            //'class'=>'layui-btn-group',
            'btnlist' => array(
                'add' => array(
                    'title' => '添加',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'add',
                        'fields' => array(
                            'parentid' => 'id',
                            'istopic' => 'istopic'
                        ),
                    )
                ),
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'update',
                        'fields' => array(
                            'id' => 'id',
                            'istopic' => 'istopic'
                        ),
                    )
                ),
                'del' => array(
                    'title' => '删除',
                    'link' => array(
                        'mod' => 'diag_del',
                        'url' => 'del',
                        'cls' => 'tree_del',
                        'fields' => array(
                            'id' => 'id',
                            'istopic' => 'istopic'
                        ),
                    )
                ),
            ),
        ),
    );
    //</editor-fold>

    protected function trance_model($model) {
        return $model;
    }
}
