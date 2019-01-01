<?php
namespace Admin\Controller;
use Common\Model;
use Common\ViewModel;

class AdController extends BaseController {

    public function _initialize() {
        parent::_initialize();        
        $plist = array('del', 'update', 'add', 'batchoperate');
        $this->getuserpower($plist);       
        $this->assign('uploadfilemod',UploadFileModSys);        
    }

    /**
     * 文章列表
     * @param type $key
     */
    public function index() {     
        $position_id=  I('position_id',0,  intval);      
        $key=I('key');
   
        $db = new Model\AdModel(); 
        if(IS_POST){
        $map = getselectmap($this->manageid, $this->manages_id);        
        if ($position_id > 0) {
           $map['position_id'] = array('eq', $position_id);
        }
        if ($key !== "") {
                $map['ad_name'] = array('like', "%$key%");
            }
            $map['pagesize'] = $this->pagesize;
            $rs = $db->get_list($map);
            $newrs = showTranceDatabymap($db->ShowMap, $rs['list'], $this->userPower);
            $drs = array('list' => $newrs, 'totalSize' => $rs['totalSize'], 'status' => 0);
            $this->ajaxReturn($drs);
            die;
        }

        $dataset = C('LINKSEARCH');
        foreach ($dataset as $key => $v) {
            switch ($key) {               
                case 'position_id':
                    $map['sid_id'] = array('in', "0,{$this->manages_id}");
                    $dx[] = show_list($v, $key, false, $position_id,$map);
                    break;
            }
        }
        $this->assign('showmap', $db->ShowMap);
        $this->assign('searchdata', $dx);        
        $this->display('Commonf/index');
    }
    
    

    /**
     * 添加新闻
     * @param type $colid
     */
    public function add() {
        $dataset = C('ADPANEL');
        if (!IS_POST) { 
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case 'position_id':
                        $map['sid_id'] = array('in', "0,{$this->manages_id}");
                        $dl[] = show_list($v, $key, false, '', $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display("Commonf/commonf");
        }
       else {
            $db = new Model\AdModel();
            $data = getpost();
            $data['sid_id'] = $this->manages_id;
            $msg = $db->addnew($data);
            $this->ajaxReturn($msg);
        }
    }

    /**
     * 更新文章
     * @param type $id
     */
    public function update() {
        $id = I("get.id", 0, intval);
        $db = new Model\AdModel();
        $model = $db->getmodel($id);         
        if (!$model) {
            $msg['returnCode'] = 0;
            $msg['returnMessage'] = "数据不存在";
            $this->ajaxReturn($msg);
            die;
        }
        $dataset = C("ADPANEL");
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case 'position_id':
                        $map['sid_id'] = array('in', "0,{$this->manages_id}");
                        $dl[] = show_list($v, $key, false, $model[$key], $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('id', $id);
            $this->display("Commonf/commonf");
        } else {
            $data = getpost(0);        
            $db = new Model\AdModel();
            $msg = $db->update($data);
            $this->ajaxReturn($msg);
        }
    }

    /**
     * 更改发布状态
     * @param type $id
     * @param type $ispost
     */
    function del($id) {
        $id = intval($id);
        $db = new Model\AdModel();
        $rs = $db->del($id);     
        $this->ajaxReturn($rs);
    }
    
    /**
     * 批处理文章
     * @param type $id
     */
    public function batchoperate() {
         if (IS_POST) {
            $idlist = I('idlist');
            $suffix = I("suffix", 1);
            $map = ['idlist' => $idlist, 'suffix' => $suffix];
            $db = new Model\AdModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }        
    }
}
