<?php
namespace Admin\Controller;
use Common\News;

/**
 * 单页资料管理
 */
class SingleController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        
        $istopic = I('get.istopic', 0, intval);
        $exparm = array('istopic' => $istopic);        
        
        $plist = array('del', 'update', 'add', 'updatesingle');
        $this->getuserpower($plist,$exparm);
        $this->assign('uploadfilemod',UploadFileModSys);
    }

    public function index($istopic = 0) {
        $db = new News\NewsSingleModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        $map['istopic'] = $istopic;
        $dl = $db->getTree($map);       
        $this->assign('dl', $dl);
        $this->assign('showmap', $db->ShowMap);
        $this->display('Commonf/tree');
    }
    
    //添加
    public function add($istopic) {       
        $db = new News\NewsSingleModel();
        $dataset = C("SINGLEPANEL");  
        $this->assign('basecard', $dataset['table_card']);
        $this->colmap['istopic'] = $istopic;
        if (!IS_POST) {           
            $map['isshow'] = 1;
            $parentid = I('get.parentid', 0, 'intval');
            foreach ($dataset as $key => $v) {
                switch ($key) {                     
                    case 'parentid':
//                        $cmap = $this->colmap;         
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $map['istopic'] = $istopic;
                        $dl[] = show_list($v, $key, false, $parentid, $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }       
            $this->assign('setdata', $dl);          
            $this->display("Commonf/lang_edit");
        }else {
            $data = getLangPost(1);
            $model = $data['base'];
            $rs = $db->addnew($data);            
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 更新单页信息
     * @param  [type] $id [单页ID]
     * @return [type]     [description]
     */
    public function update($id,$istopic) {
        $id = intval($id);
        $istopic = intval($istopic);
        $db = new News\NewsSingleModel();
        //默认显示添加表单
        if (!IS_POST) {         
            $dataset = C("SINGLEPANEL");
            $this->assign('basecard', $dataset['table_card']);
            $model = $db->getmodel($id);
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                $map=array();
                $map['sid_id'] = array('in', "0,{$this->manages_id}");
                switch ($key) {
                    case 'parentid':
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $map['istopic'] = $istopic;
                        $map['id'] = $id;
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }      
            $this->assign('setdata', $dl);
            $this->assign('id', $id);
            $this->assign('lanmodel', $model);          
            $this->display("Commonf/lang_edit");
        }
        else {
            $data = getLangPost(1);
            $db = new News\NewsSingleModel();
            $rs = $db->updater($data);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 删除
     * @param type $id
     */
    public function del($id) {
        $id = intval($id);
        $db = new News\NewsSingleModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        $rs = $db->del($map, $id);
        $this->ajaxReturn($rs);
    }
    
    /**
     * 批量删除
     * @param type $id
     */
    public function batchoperate() {        
          if (IS_POST) {
            $idlist = I('idlist');
            $suffix = I("suffix", 1);
            $map = ['idlist' => $idlist, 'suffix' => $suffix];
            $db = new News\NewsSingleModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }
    }
}
