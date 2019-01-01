<?php
namespace Common\Model;
use Common\Base;
/**
 * Description of AuthRuleModel
 *权限明细
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AuthRuleModel  extends  Base\BtreeModel {
    
    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "auth_rule";
    protected $viewpath = "name"; 
    protected $deltree = true;
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc,id asc',
        ),
        'orderby2' => array(
            'order' => 'sortid desc,id desc',
        ),
    ); 
     protected $_validate = array(
        array('name', 'require', '请填写规则标识！'), //默认情况下用正则进行验证
        array('name', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\/]+$/u', '规则标识只能包含中英文字母和数字！'), //默认情况下用正则进行验证
        array('status', array(1, 0), '请勿恶意修改字段', 0, 'in'), // 当值不为空的时候判断是否在一个范围内
        array('sortid', 'number', '序号必须是数字！'), //默认情况下用正则进行验证         
    );
    
    protected $_auto = array(
        array('extpram', 'htmldecode', 3, 'function'),
    );
    //</editor-fold>
    
   //<editor-fold defaultstate="collapsed" desc="列表显示项">
    public $ShowMap = array(
//        'id' => '序号',
        'title' => '名称',
        'name' => '标识',
        'status' => '状态',
        'sortid' => array(          
            'width'=>'60',
            'type' => 'sortid',
            'title' => '排序',
            'table' => 'AuthRule',
            'idd' => 'id',
        ),
        'status' => array(
            'type' => 'switch',
            'format' => 'open_status',
            'table' => 'AuthRule',
            'title' => '状态',
        ),
        'issys' => array(
            'type' => 'rotation',
            'format' => 'rule_issys',
            'table' => 'AuthRule',
            'title' => '类型',
        ),
        'ismenu' => array(
            'type' => 'rotation',
            'format' => 'rule_ismenu',
            'table' => 'AuthRule',
            'title' => '菜单',
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
                            'parentid' => 'id'
                        ),
                    )
                ),
                'update' => array(
                    'title' => '编辑',
                    'link' => array(
                        'mod' => 'diag',
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
    
    public function addnew($data) {
        $model = $data['base'];
        unset($model['id']);
        unset($model['ctrl']);
        unset($model['actionval']);
        unset($model['action']);
        unset($model['sid_id']);
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
        unset($model['ctrl']);
        unset($model['actionval']);
        unset($model['action']);
        unset($model['sid_id']);
        $data['base'] = $model;
        return parent::updater($data);
    }

    /**
     * 更新ruls缓存
     * @return type
     */
    public function rewrit() {
        $lang = getLangueInfo();
        $catchpath = "lancatch/{$lang['sid_id']}/authrule/{$lang['curent_lang']}";
        $map['status'] = 1;
        $authrule = $this->getlist($map);
        $a = [];
        foreach ($authrule as $v) {
            $a[$v['id']] = $v;
        }
        F($catchpath, $a);
        return $authrule;
    }

    protected function trance_model($model) {
        return $model;
    }
}
