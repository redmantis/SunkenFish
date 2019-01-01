<?php
namespace Admin\Controller;
use Common\Model;
use Common\ViewModel;

/**
 * Description of SitesetController
 * 网站参数设置
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class SitesetController extends BaseController {
    
    public function _initialize() {
        parent::_initialize();       
        $this->assign('tabelname', 'SubConfig');    
        $this->assign('uploadfilemod',UploadFileModSys);    
    }
    
    public function index() {
        $db = new Model\SubConfigModel();
        $configkey = 'baseinfo';
        $dataset = C("SITEBASE");
        $this->assign('basecard', $dataset['table_card']);
        if (!IS_POST) {
            $baseconfig = $db->getConfig($this->manages_id, $configkey);          
            $setx = $baseconfig['base'];
            foreach ($dataset as $key => $v) {
                $dl[] = show_list($v, $key, false, $setx[$key]);
            }
            
            $this->assign('setdata', $dl);
            $this->assign('lanmodel', $baseconfig);
            $this->assign('id', $baseconfig['id']);
            $this->display('Commonf/lang_edit');
        } else {
            $data = getLangPost(0);         
            $rule = $dataset['validate_rule']; // 
            $db->setProperty('_validate', $rule);
            if (!$db->create($data['base'])) {
                $msg['status'] = 0;
                $msg['msg'] = $db->getError();
            } else {
                $msg = $db->update($data);
            }
            $this->ajaxReturn($msg);
        }
    }

    /**
     * 系统配置  全局
     */
    public function system() {
        $db = new Model\SubConfigModel();
        $configkey = 'system';
        $actionpost = U('Siteset/system');
        $this->assign('actionpost', $actionpost);
        $dataset = C("SYSTEM");
        $this->assign('tablecard', $dataset['table_card']);
        if (!IS_POST) {
            $baseconfig = $db->getConfig(0, $configkey);
            $setx = $baseconfig;
            foreach ($dataset as $key => $v) {
                $dl[] = show_list($v, $key, false, $setx[$key]);
            }
            $this->assign('setdata', $dl);
            $this->assign('conf', $baseconfig);
            $this->assign('id', $baseconfig['id']);
            $this->display('Commonf/tablecard');
        }
        else {            
            $data = getpost(0);         
            $msg = $db->update($data);
            $this->ajaxReturn($msg);
        }
    }
}