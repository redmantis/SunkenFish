<?php

namespace Common\Model;

use Common\Base;

/**
 * Description of SubColumnsModel
 * 菜单
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class MenuModel extends Base\BtreeModel {

    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "menu";
    protected $viewpath = "viewmod"; 
    protected $deltree = true;
    
    protected $_validate = array(
        array('title', 'require', '请填写菜单名称！'), //默认情况下用正则进行验证
        array('viewpath', 'require', '请填写菜单链接！'), //默认情况下用正则进行验证
    );
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc ,id desc',
        ),        
        'orderby2' => array(
            'order' => 'sortid desc,id desc',
        ),
    );

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="列表显示项">
    public $ShowMap = array(
        'id' => '序号',
        'title' => '菜单名称',
        'viewpath' => '链接',
        'groupmark' => '分组标签',
        'shownav' => array(
            'type' => 'rotation',//rotation config
            'format' => 'menuShow',
            'table' => 'Menu', 
            'title' => '菜单显示',
        ),
        'target' => array(
            'type' => 'rotation',//rotation config
            'format' => 'linkTarget',
            'table' => 'Menu', 
            'title' => '打开方式',
        ),
        'isshow' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'Menu',
            'title' => '显示',
        ),
        'sortid' => array(
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'Menu',
            'idd' => 'id',
        ),
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup',
            //'class'=>'layui-btn-group',
            'btnlist' => array(
                'add' => array(
                    'title' => '子菜单',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'add',
                        'fields' => array(
                            'parentid' => 'id',
                            'menutype' => 'menutype',
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
                            'menutype' => 'menutype',
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
     * 根据条件读取菜单
     * @param type $map
     * @return type
     */
    public function getmodelbymap($map) {
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $id = $this->where($filedarray)->bind($valuearray)->getField('id');
        return $this->getmodelbyid($id);
    }

    /**
     * 取得子栏目ID列表
     * @param type $id　　父类ID
     * @param type $byself 包含本身
     * @return string
     */
    public function getdefaultid($map) {
        $map['isshow'] = 1;
        $id = $this->where($map)->scope('orderby')->getField('id');
        return $id;
    }

    public function getsublist($pid = 0, $map, $onlyid = true, $byself = true) {
        $rs = $this->where($map)->scope('orderby')->select();
        $dl = array();
        formatTreeList($rs, $pid, $dl, '');
        if ($onlyid) {
            $ids = '';
            if ($byself)
                $ids .= $id . ',';
            foreach ($dl as $key => $v) {
                $ids .=$v['id'] . ',';
            }
            return $ids;
        } else {
            return $dl;
        }
    }

    //测试路径是否合法
    public function checkpath($path, $sid_id, $id = 0) {
        $map['sid_id'] = $sid_id;
        $map['viewpath'] = $path;
        $map['id'] = array('neq', $id);
        $res = $this->where($map)->find();
        if ($res)
            return true;
        else {
            return false;
        }
    }
    
    /**
     * 取得导航栏目
     * @param type $sid_id
     */
    public function getnav($sid_id, $menutype, $pmap = null) {
        $map['sid_id'] = $sid_id;
        $map['isshow'] = 1;
        $map['menutype'] = $menutype;
        $map['shownav'] = array('in', '1,2,3');
        $dl = $this->getTree($map, $pmap);
        $dl = genTree9($dl);
        return $dl;
    }



    /**
     * 取得侧边导栏目
     * @param type $sid_id          网站ID
     * @param type $colid           当前菜单ID
     * @param type $topic           是否专题
     * @param type $abssub          必取子栏目
     * @return type
     */
    public function getleftnav($sid_id, $column, $topic = 0, $abssub = 0) {
        if ($abssub) {
            $colid = $column['id'];
        } else {
            $colid = $column['parentid'] == 0 ? $column['id'] : $column['parentid'];
        }
        $catchname = "web_left_nav_{$sid_id}_{$colid}_{$abssub}";
        $value = S($catchname);
        if ($value) {
            return $value;
        } else {
            if ($abssub) {
                $where['parentid'] = $colid;
            } else {
                $where['parentid'] = $colid;
                $where['id'] = $colid;
                $where['_logic'] = 'or';
            }

            $map['sid_id'] = $sid_id;
            $map['isshow'] = 1;
            $map['menutype'] = $topic;
            $map['shownav'] = array('in', '1,2');

            $map['_complex'] = $where;
            $dl = $this->where($map)->scope('orderby')->select();

            $navname = $column['parentid'] == 0 ? $column['title'] : ''; //取得主导航的名称
            if (!$dl)
                return NULL;
            else {
                $rs['dl'] = $dl;
                $navname = $this->where(array('id' => $colid))->getField('title');
                $viewpath = $this->where(array('id' => $colid))->getField('viewpath');
                $rs['navname'] = $navname;
                $rs['viewpath'] = $viewpath;
                S($catchname, $rs);
            }
            return $rs;
        }
    }

    protected function trance_model($model) {
        return $model;
    }

}
