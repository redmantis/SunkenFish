<?php

namespace Common\Gmodel;

use Common\Base;

/**
 * Description of DepartmentModel
 * 网站属性参数管理
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AtbModel extends Base\BtreeModel {

    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "attrs";
    protected $viewpath = "tagmark";
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
        'title' =>array(
            'width'=>'',
            'class'=>'layui-table-cell layui-table-cell-over',
            'title' =>'标题',
        ),        
        'tagmark' =>array(     
             'width'=>'160',
            'title' =>'键名',
            'parentkey' => 'AttrsConfig',
        ),   
        'tagvalue' => array(
            'width' => '160',
            'title' => '键值',
            'class' => 'layui-table-cell layui-table-cell-over',
            'parentkey' => 'AttrsConfig',
        ),
        'tagtype' => array(
            'width'=>'60',
            'type' => 'switch',
            'format' => 'attrstatus',      
            'title' => '类型',  
            'table' => 'Attrs',
            'parentkey' => 'AttrsConfig',
        ),
        'sortid' => array(
             'width'=>'60',
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'Attrs',
            'idd' => 'id',
        ),
        'btngroup' => array(
             'width'=>'260',
            'title' => '操作',
            'type' => 'btngroup',
            'btnlist' => array(
                'add' => array(
                    'title' => '添加',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'add',
                        'fields' => array(
                            'parentid' => 'id'
                        ),
                    )
                ),
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag', //diag,self
                        'url' => 'update',
                        'fields' => array(
                            'id' => 'id'
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
                            'id' => 'id'
                        ),
                    )
                ),
            ),
        ),
    );
    //</editor-fold>
    
    /**
     * 获取子元素（子树）
     * @param array $map
     * @param type $tagmark
     * @return type
     */
    public function getElementList($map, $tagmark) {
        $lang = getLangueInfo();
        $data[$this->viewpath] = $tagmark;
        $data['sid_id'] = $lang['sid_id'];
        $id = $this->getidbytagmark($data);
        if ($id) {
            $map['sid_id'] = array('in', "0,{$lang['sid_id']}");
            $pmap['id'] = $id;
            return $this->getTree($map, $pmap);
        }
    }

    public function addnew($data) {
        $model = $data['base'];
        if ($model['tagtype'] == 1) {
            $model['sid_id'] = 0;
        }
        unset($model['id']);
        if (is_array($model['verify'])) {//验证条件
            $model['verify'] = implode($model['verify'], "|");
        }
        $data['base'] = $model;
        return parent::addnew($data);    
    }



    /**
     * 更新数据 
     * @param type $data
     * @return type
     */
    public function updater($data) {
        $model = $data['base'];
        if ($model['tagtype'] == 1) {
            $model['sid_id'] = 0;
        }
        if (is_array($model['verify'])) {//验证条件
            $model['verify'] = implode($model['verify'], "|");
        } else {
            $model['verify'] = "";
        }
        $data['base'] = $model;
        return parent::updater($data);
    }
    
    protected function trance_model($model) {
         return $model;
    }

}
