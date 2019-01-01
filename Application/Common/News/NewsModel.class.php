<?php

namespace Common\News;

use Common\Base;

/**
 * Description of DepartmentModel
 * 商品分类
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class NewsModel extends Base\BModel {

    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "news";
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc,posttime desc ,id desc',
        ),
        'orderby2' => array(
            'order' => 'sortid desc,posttime asc ,id asc',
        ),
    );
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="列表显示项">  
    public $ShowMap = array(
        'id' => '序号',
        'page_title' => '标题',
        'columns' => '栏目',
//        'colid' => array(
//            'type' => 'langmodel',
//            'title' => '栏目',
//            'source' => array('idfiled' => 'id',
//                'valuefiled' => 'title',
//                'dbmodel' => '\Common\News\ColumnsModel',
//            ),
//        ),
        'hits' => '点击',
        'posttime' => array(
            'type' => 'time',
            'format' => 'Y-m-d',
            'title' => '发布',
        ),
        'isvouch' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'News',
            'title' => '推荐',
        ),
        'ishot' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'News',
            'title' => '轮播',
        ),
        'istop' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'News',
            'title' => '置顶',
        ),
         'isshow' => array(
            'type' => 'rotation',
            'format' => 'isshow_status',
            'table' => 'News',
            'title' => '审核',
        ),
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
        ),
//        'ispost' => array(
//            'type' => 'switch',
//            'format' => 'opcolse',
//            'table' => 'News',
//            'title' => '定时发布',
//        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            //'class'=>'layui-btn-group',
            'btnlist' => array(           
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',//card
                        'url' => 'update',
                        'fields' => array(
                            'id' => 'id',
                            'istopic' => 'istopic',
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
                            'id' => 'id'
                        ),
                    )
                ),
            ),
        ),
    );
    //</editor-fold>




    public function addnew($data) {
        $model = $data['base'];      
        if ($model['posttime']) {
            $model['posttime'] = strtotime($model['posttime']);
        }
        $db = new ColumnsModel();
        $rs = $db->getmodelbyid($model['colid']);
        $model['istopic'] = $rs['istopic'];
        $model['parentid'] = $rs['parentid'];
        $data['base'] = $model;        
        return parent::addnew($data);
    }
    
    public function updater($data) {
        $model = $data['base'];     
        if ($model['posttime']) {
            $model['posttime'] = strtotime($model['posttime']);
        }
        $db = new ColumnsModel();
        $rs = $db->getmodelbyid($model['colid']);
        $model['istopic'] = $rs['istopic'];
        $model['parentid'] = $rs['parentid'];
        $data['base'] = $model;
        return parent::updater($data);
    }

    /**
     * 点赞统计
     * @param type $id
     * @param type $colid
     * @return type
     */
    public function getflower($id, $colid, $inc) {
        $map['id'] = intval($id);
        $map['colid'] = intval($colid);
        $this->where($map)->setInc('flowers', $inc);
        $data = $this->where($map)->field('hits,flowers')->select(); //getField('hits,flowers');
        return $data[0];
    }

    /**
     * 点击实时统计
     * @param type $id
     * @param type $colid
     * @return type
     */
    public function gethits($id, $colid) {
        $id = intval($id);
        $colid = intval($colid);
        $map['id'] = $id;
        $map['colid'] = $colid;
        $this->where($map)->setInc('hits');
        $data = $this->where($map)->field('hits,flowers')->select(); //getField('hits,flowers');
        return $data[0];
    }

    /**
     * 定时发布
     * @return type
     */
    public function timepost() {
        $map['isdel'] = 0;
        $map['ispost'] = 1;
        $amp['posttime'] = array('lt', time());
        $data['ispost'] = 0;
        $rs = $this->where($map)->save($data);
        return $rs;
    }

    protected function trance_model($model) {
        $db = new ColumnsModel();
        $m = $db->getmodelbyid($model['colid']);
        $model['columns'] = $m['title'];
        $model['summary'] = showsummary($model['content']);
        return $model;
    }

}
