<?php

namespace Common\Book;
use Common\Base\BModel;

/**
 * Description of DepartmentModel
 * 小说章节
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class BkArticleModel extends BModel {
    
    protected $_link = array(
        'BkArticletext' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'BkArticletext',
            'foreign_key' => 'extid',
        ),
    );
    
    protected $basetable = "bk_article";

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid desc,id desc',
        ),
        'orderby2' => array(
            'order' => 'sortid asc,id asc',
        ),        
    );
    
    protected $partition = array(
//        'field' => 'bookid', // 要分表的字段 通常数据会根据某个字段的值按照规则进行分表,我们这里按照用户的id进行分表
//        'type' => 'mod', // 分表的规则 包括id year mod md5 函数 和首字母，此处选择mod（求余）的方式
//        'expr' => '', // 分表辅助表达式 可选 配合不同的分表规则，这个参数没有深入研究
//        'num' => '5', // 分表的数目 可选 实际分表的数量，在建表阶段就要确定好数量，后期不能增减表的数量
    );

    /**
     * 列表显示项
     * @var type 
     */
    public $ShowMap = array(
        'id' => '序号',
        'title' => '章节',
        'ischapter' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'BkArticle',
            'title' => '章节',
        ),     
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'BkArticle',
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
                            'bookid' => 'bookid',
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
                            'bookid' => 'bookid',
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
    public function gethits($id) {
        $id = intval($id);    
        $map['id'] = $id; 
        $this->where($map)->setInc('hits');
        $data = $this->where($map)->getField('hits');
        return $data;
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
//        $model['catename_short'] = getkeyname($model['cat_id'], "novelcate",'','shorttitle');
//        $model['catename'] = getkeyname($model['cat_id'], "novelcate");     
//        $model['cateurl']= parent::showdetail('', "books/{$model['cat_id']}");

        $model['content']=preg_replace('/([^br])+$/','</div>',$model['content']);       

        $model['rewriteurl'] = showdetail('', "article/{$model['id']}");
        return $model;
    }

}
