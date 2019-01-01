<?php
namespace Common\Model;
use Common\Base;
/**
 * Description of SubConfigModel
 * 站点参数设置
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class SubConfigModel extends Base\SModel {
    
    //<editor-fold defaultstate="collapsed" desc="参数设置">
    protected $basetable = "sub_config";
    protected $_scope = array(
        'orderby' => array(
            'order' => 'id asc',
        ),
        'orderby2' => array(
            'order' => 'id desc',
        ),
    );
    protected $_validate = array(
        array('sid_id', 'number', '后台分页记录必须是数字'),
    );
    protected $fields = array('id',
        'sid_id',
        'key',
        'val',
    );
    //</editor-fold>

    /**
     * 根据$sid_id key  取值 缓存
     * @param type $sid_id
     * @param type $key
     * @return type
     */ 
     public function getConfig($sid_id, $key = 'baseinfo') {
        $data = $this->where(array('sid_id' => (int) $sid_id, 'key' => $key))->find();
        if (!$data) {          
            $model['key'] = $key;
            $model['sid_id'] = $sid_id;
            $data['id'] = $this->add($model);
        } else {
            $confg = unserialize($data['val']);
        }
        $confg['id'] = $data['id'];
        return $confg;
    }

    /**
     * 更新配置
     * @param type $data
     */
    public function update($data) {
        if (isset($data['base'])) {
            $base = $data['base'];
            $id = $base['id'];
        } else {
            $id = $data['id'];
        }
        $model = $this->getmodelbyid($id);
        $catchpath = "lancatch/config/{$model['sid_id']}/{$model['key']}";
        F($catchpath, $data);
        unset($data['id']);
        $set['val'] = serialize($data);
        $set['id'] = $id;
        return parent::update($set);
    }

    protected function trance_model($model) {
        return $model;
    }

}
