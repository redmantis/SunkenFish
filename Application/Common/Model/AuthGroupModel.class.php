<?php
namespace Common\Model;

use Common\Base;
/**
 * Description of AuthGroupModel
 *
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AuthGroupModel extends Base\SModel {
    
     //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "auth_group";
    

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc ,id asc',
        ),        
        'orderby2' => array(
            'order' => 'sortid desc,id desc',
        ),
    );   

    protected $_validate = array(
        array('sid_id','require','请给角色指定站点！'), //默认情况下用正则进行验证
        array('sid_id',0,'请给角色指定站点！',1,'notequal'), //默认情况下用正则进行验证
        array('title', 'require', '请填写角色名称！'), //默认情况下用正则进行验证  
        array('title', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '角色名称只能包含中英文字母和数字！'), 
        array('status', array(1, 0), '请勿恶意修改字段', 0, 'in'), // 当值不为空的时候判断是否在一个范围内
        array('sortid', 'number', '序号必须是数字！'), //默认情况下用正则进行验证
    );
    
    protected $fields = array('id',
        'title',
        'status',
        'rules',
        'sortid',
        'sid_id',
    );
     //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="列表显示项">
        public $ShowMap = array(
        'id' => '序号',
        'title' => '角色',  
        'sid_name'=>'站点',
        'status' => array(
            'type' => 'switch',
            'format' => 'opcolse',
            'table' => 'AuthGroup',
            'title' => '禁用',
        ),       
        'btngroup' => array(
            'title' => '操作',
            'type' => 'btngroup', 
            'btnlist' => array(                
                'detail' => array(
                    'title' => '分配权限',
                    'link' => array(
                        'mod' => 'diag',
                        'url' => 'detail',
                        'fields' => array(
                            'id' => 'id',
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
    //</editor-fold>


    protected function trance_model($model) {
        $db=new SubsidiaryModel();
        $sud=$db->getmodebyid($model['sid_id']);
        $model['sid_name']=$sud['sid_name'];
        return $model;
    }

}
