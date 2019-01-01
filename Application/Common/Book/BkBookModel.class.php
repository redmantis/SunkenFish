<?php

namespace Common\Book;

use Common\Base\BModel;

/**
 * Description of DepartmentModel
 * 小说
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class BkBookModel extends BModel {
    
    protected $basetable = "bk_book";
    protected $_link = array(
        'BkBooktext' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'BkBooktext',
            'foreign_key' => 'extid',
        ),
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order2' => 'sortid desc,id desc',
        ),
        'orderby1' => array(
            'order' => 'sortid asc,id desc',
        ),
        //最新更新
        'orderby10' => array(
            'order' => 'updatetime desc,id desc',
        ),
        //最新入库
         'orderby20' => array(
            'order' => 'posttime desc,id desc',
        ),
    );

    /**
     * 列表显示项
     * @var type 
     */
    public $ShowMap = array(
        'id' => '序号',
        'title' => '品名', 
        'remotethumb' => array(   
            'type' => 'img',   
            'title' => '图片',
            'titlefild'=>'title',
        ),        
        'is_full' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'BkBook',
            'title' => '完本',
        ),     
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'BkBook',
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
        $map['id'] = $id; 
        $this->where($map)->setInc('click_count');
        $data = $this->where($map)->field('click_count,comment_count')->select(); //getField('hits,flowers');
        return $data[0];
    }    
    
    public function get_list($map, $skip = 0, $pagesize = 0) {
        $rs = parent::get_list($map, $skip, $pagesize);
        $list = $this->trance_list($rs['list']);
        $rs['list'] = $list;
        return $rs;
    }

    /**
     * 数据格式转换
     * @param array $model
     */
    public function trance_model($model) {
        $model['catename_short'] = getkeyname($model['cat_id'], "novelcate",'','shorttitle');
        $model['catename'] = getkeyname($model['cat_id'], "novelcate");     
        $model['cateurl']= showdetail('', "book/{$model['cat_id']}");
        $model['rewriteurl']= showdetail('', "bookinfo/{$model['id']}");
        return $model;
    }

}
