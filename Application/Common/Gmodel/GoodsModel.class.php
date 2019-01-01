<?php

namespace Common\Gmodel;

use Think\Model;
use Common\Base;

/**
 * Description of DepartmentModel
 * 商品分类
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class GoodsModel extends Base\BModel {

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid desc,id desc',
        ),
        'orderby2' => array(
            'order' => 'sortid asc,id desc',
        ),
    );
    
     protected $basetable = "goods";


    /**
     * 列表显示项
     * @var type 
     */
    public $ShowMap = array(
        'id' => '序号',
        'title' => '品名',
        'goods_sn' => '编号',
        'thumb' => array(   
            'type' => 'img',   
            'title' => '图片',
            'titlefild'=>'title',
        ),
       'cat_id' => array(
            'type' => 'langmodel',
            'title' => '分类',
            'source' => array('idfiled' => 'id',
                'valuefiled' => 'title',
                'dbmodel' => '\Common\Gmodel\GcateModel',
            ),
        ),
        'brand_id' => array(
            'type' => 'config',
            'format' => 'AboutGoods_brand',
            'title' => '品牌',
        ),
        'is_on_sale' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'Goods',
            'title' => '上架',
        ),
        'is_new' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'Goods',
            'title' => '新品',
        ),
        'is_hot' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'Goods',
            'title' => '热卖',
        ),
        'is_recommend' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'Goods',
            'title' => '推荐',
        ),
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'Goods',
            'idd' => 'id',
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
                    'link' => array(
                        'mod' => 'diag_del',
                        'url' => 'del',
                        'cls' => 'diag_del',
                        'fields' => array(
                            'id' => 'id',
                        ),
                    )
                ),
            ),
        ),
    );

    /**
     * 点击实时统计
     * @param type $id
     * @param type $colid
     * @return type
     */
    public function gethits($id, $cat_id) {
        $id = intval($id);
        $colid = intval($cat_id);
        $map['id'] = $id;
        $map['cat_id'] = $cat_id;
        $this->where($map)->setInc('click_count');
        $data = $this->where($map)->field('click_count,comment_count')->select(); //getField('hits,flowers');
        return $data[0];
    }

    protected function trance_model($model) {
        return $model;
    }
}
