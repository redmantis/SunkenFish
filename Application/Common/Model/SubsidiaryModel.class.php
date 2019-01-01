<?php

/**
 * 软件功能说明：
 * 下属单位列表
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */

namespace Common\Model;
use Common\Base;

//站点列表
class SubsidiaryModel extends Base\SModel {

    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "subsidiary";
    protected $idname = "sid_id";//键名    
    protected $delmark = 'isshow';//删除标记字段

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'sortid asc ,sid_id asc',
        ),        
        'orderby2' => array(
            'order' => 'sortid desc,sid_id desc',
        ),
    );

    protected $_validate = array(
        array('sid_name', 'require', '请填写单位名称！'), //默认情况下用正则进行验证
        array('sid_name', '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', '单位名称只能包含中英文字母和数字！'), //默认情况下用正则进行验证
        array('sid_sitname', 'require', '请填写单位域名！'), //默认情况下用正则进行验证
        array('sid_sitname', '/^[a-zA-Z0-9.,]+$/', '单位域名包含非法字符！'), //默认情况下用正则进行验证
        array('sid_dir', 'require', '请填写静态目录！'), //默认情况下用正则进行验证
        array('sid_dir', '/^[a-zA-Z0-9]+$/', '静态目录只能包含英文字母和数字！'), //默认情况下用正则进行验证
        array('sid_name', '', '网站名称已存在！', 0, 'unique', self::MODEL_INSERT), // 在新增的时候验证name字段是否唯一
        //array('sid_sitname','','单位域名已存在！',0,'unique',self::MODEL_BOTH), // 在新增的时候验证name字段是否唯一
        array('sid_dir', '', '该目录已被使用！', 0, 'unique', self::MODEL_INSERT), // 在新增的时候验证name字段是否唯一
        array('sortid', 'number', '序号必须是数字！'), //默认情况下用正则进行验证   
    );
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="列表显示项">
    public $ShowMap = array(
        'sid_id' => '序号',
        'sid_name' => '站点名称',
        'sid_sitname' => '域名',
        'sid_dir' => '目录',
        'theme' => '模板',
        'isshow' => array(
            'type' => 'prompt',
            'format' => 'open_status',
            'mustinput' => '1,0', //必须输入处理原因的操作项 
           // 'filter' => array('all' => '0'), //过滤的操作项
            'showfiled' => 'shorttitle',
            'table' => 'ZfRooms',
            'title' => '审核',
            'idd' => 'sid_id'
        ),
//        'isshow' => array(
//            'type' => 'rotation',
//            'format' => 'open_status',
//            'table' => 'Subsidiary',
//            'title' => '审核',
//            'idd' => 'sid_id'
//        ),
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
                            'sid_id' => 'sid_id',
                        ),
                    )
                ),
//                'del' => array(
//                    'title' => '删除',
//                    'link' => array(
//                        'mod' => 'diag_del',
//                        'url' => 'del',
//                        'cls' => 'diag_del',
//                        'fields' => array(
//                            'sid_id' => 'sid_id'
//                        ),
//                    )
//                ),
            ),
        )
    );
    //</editor-fold>

    public function addnew($model) {
        if (!$this->create($model)) {
            $err = $this->getError();
            return ['status' => 20000005, 'msg' => $err];
        }       
        $model['isshow'] = 1;
        unset($model['id']);
        return parent::addnew($model);      
    }

    /**
     * 更新数据 
     * @param type $data
     * @return type
     */
    public function update($model) {
        if (!$this->create($model, 2)) {
            $err = $this->getError();
            return ['status' => 20000005, 'msg' => $err];
        }
        unset($model['id']);
        return parent::update($model);
    }

    /**
     * 根据域名取得当前站点配置(缓存方式减轻对数据库的压力)
     * @param type $siturl
     */
    public function getmodebysite($siturl) {
        $map['sid_sitname'] = array('like', "%$siturl%");
        $map['isshow'] = 1;
        $catchpath = "catchmark/subsidarray/{$siturl}";
        $model = F($catchpath);
        if (!$model) {
            $filedarray = array();
            $valuearray = array();
            tracemaptobind($map, $filedarray, $valuearray);
            $model = $this->where($filedarray)->bind($valuearray)->find();
            F($catchpath, $model);
        }
        return $model;
    }

    public function clearcatch() {
        F('catchmark/subsidarray', null);
    }

    
    /**
     * 根据ID取得当前站点配置(缓存方式减轻对数据库的压力)
     * @param type $siturl
     */
    public function getmodebyid($id) {
        $dl = $this->builarray();
        return $dl[$id];
    }

    public function builarray() {
        $rs = F('catchmark/subsidarray');
        if (!$rs) {
            $rs = $this->where(['isshow' => 1])->getField('sid_id,sid_name,sid_sitname,sid_dir,sid_key,isshow,sid_logo,theme,sortid');
            F('catchmark/subsidarray', $rs);
        }
        return $rs;
    }

    protected function trance_model($model) {
        return $model;
    }

}
