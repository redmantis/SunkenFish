<?php

namespace Common\Gmodel;

use Common\Base;
/**
 * Description of DepartmentModel
 * 商品分类
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class GcateModel extends Base\BtreeModel  {
    
    protected $deltree=true;

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc,id asc',
        ),
        'orderby2' => array(
            'order' => 'sortid desc,id desc',
        ),
    );

    /**
     * 列表显示项  
     */
    public $ShowMap = array(
        'id' => '序号',
        'title' => '标题',
        'shorttitle' => '短标题',
        'viewpath' => array(
            'title' => '访问路径',
        ),
        'thumb' => array(
            'title' => '图片',        
        ),
         'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'GoodsCate',
            'idd' => 'id',
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
    protected $_link = array(
        'GcatetextModel' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'GcatetextModel',
            'foreign_key' => 'extid',
        ),
    );
    
    protected $basetable = "goods_cate";
    

    protected function trance_model($model) {
        return $model;
    }

}
