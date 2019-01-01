<?php
namespace Admin\Controller;
use Common\News;

class NewsController extends BaseController {
    public $istopic;
    public $userPower;//用户具备的操作权限
    public function _initialize() {
        parent::_initialize();
        
        /* 当前页必须参数 */        
        $istopic = I('get.istopic', 0, intval);
        $this->istopic = $istopic;
        $mp = array('istopic' => $istopic);
        
        $this->colmap['istopic'] = $istopic;
        $this->colmap['sid_id'] = $this->manages_id;
        
        /* 组装用户权限 */
        $plist = array('del', 'update', 'add', 'batchoperate');
        $this->getuserpower($plist, $mp);

    }

    public function index($key = '') {
        $db = new News\NewsModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        $map['istopic'] = $this->istopic;
        
        $pid = 0;
        if (IS_POST) {
            $colid = I('colid', 0, intval);
            $m_id = I('m_id', 0, intval);
            $isvouch = I('isvouch', -1, intval);
            $istop = I('istop', -1, intval);
            $ishot = I('ishot', -1, intval);
            $isshow = I('isshow', -1, intval);
            if ($key !== "") {
                $map['page_title'] = array('like', "%$key%");      
            }

            $map['isdel'] = 0;
            if ($isvouch != -1) {
                $map['isvouch'] =  $isvouch;
            }

            if ($istop != -1) {
                $map['istop'] =  $istop;
            }

            if ($ishot != -1) {
                $map['ishot'] = $ishot;
            }

            if ($isshow != -1) {
                $map['isshow'] =  $isshow;
            }

            if ($m_id > 0) {
                $map['m_id'] = $m_id;
            }

            if ($this->managegrade == 2) {
                if ($this->bconfig['enable_tel'] == 1) {//岗位鉴权
                    if (crackin($this->manage['tel'], '1,2')) {
                        unset($this->colmap['id']); //移除下拉列表限制
                    } else {
                        $map['colid'] = array('in', $this->managememu);
                    }
                } else {
                    $map['colid'] = array('in', $this->managememu);
                }
            }

            if ($colid > 0) {
                $subc = new News\ColumnsModel();
                $subids = $subc->getsublistid($this->colmap, $colid);
                $map['colid'] = array('in', $subids);
            }
            
            $map['pagesize'] = $this->pagesize;
            $rs = $db->get_list($map);
            $newrs = showTranceDatabymap($db->ShowMap, $rs['list'], $this->userPower, "Common\News\NewsModel");
            $drs = array('list' => $newrs, 'totalSize' => $rs['totalSize'], 'status' => 0);
            $this->ajaxReturn($drs);
            die;
        }
        $this->assign('showmap', $db->ShowMap);

        $map = getselectmap($this->manageid, $this->manages_id);
        //查询参数面板输出
        $dataset = C('NEWSRFILTER');
        foreach ($dataset as $key => $v) {
            switch ($key) {
                case 'colid':
                    $dx[] = show_list($v, $key, false, I($key), $this->colmap);
                    break;
                default:
                    $dx[] = show_list($v, $key, false, I($key));
                    break;
            }
        }
        $this->assign('searchdata', $dx);
        $this->display('Commonf/index');
    }

    public function add() {
        $sc = new News\ColumnsModel();
        $colid = I('colid', 0, intval);       
        $dataset = C("BASEPANEL");
        $this->assign('basecard', $dataset['table_card']);
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {
                $value = '';
                switch ($key) {
                     case 'extcol'://扩展栏目  
                        $extcolmap = array();
                        $extcolmap['sid_id'] = array('eq', $this->manages_id);
                        $extcolmap['showlist'] = array('eq', 1);
                        $extcolmap['lev'] = array('in', '2,3,');
                        
                        $dbc = new News\ColumnsModel();
                        $exlist=$dbc->getlist($extcolmap);

                        $dl[] = show_list($v, $key, false, $value, $exlist);
                        break;                   
                    case 'colid':                     
                        $dl[] = show_list($v, $key, false, $colid, $this->colmap);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }     
            $this->assign('setdata', $dl);
            $this->display("Commonf/lang_edit");
        } 
        else {          
            $data = getLangPost(1);           
            $data['base']['m_id'] = $this->manageid;
            $data['base']['isshow'] = $this->bconfig['news_autocheck'];    
            
            $db = new News\NewsModel();
            $rs = $db->addnew($data);           
            $this->ajaxReturn($rs);
        }
    }

    public function update($id) {
        $id = intval($id);
        $db = new News\NewsModel();
        $sc = new News\ColumnsModel();
        $model = $db->getmodel($id);      
        $colid = I('colid', 0, intval);
        if ($colid > 0)
            $model['base']['colid'] = $colid;
        else {
            $colid = $model['base']['colid'];
        }

        $dataset = C("BASEPANEL");
        $this->assign('basecard', $dataset['table_card']);
       
        if (!IS_POST) {          
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                $base= $model['base'];
                $base['posttime'] = date($base['posttime'], 'Y-m-d H:i:s');
                switch ($key) {
                   case 'extcol'://扩展栏目  
                        $extcolmap = array();
                        $extcolmap['sid_id'] = array('eq', $this->manages_id);
                        $extcolmap['showlist'] = array('eq', 1);     
                        $extcolmap['lev'] = array('in', '2,3,');
                        $dbc = new News\ColumnsModel();
                        $exlist = $dbc->getlist($extcolmap);                        
                        $dl[] = show_list($v, $key, false,  $base[$key], $exlist);
                        break;                   
                    case 'colid':
                        $dl[] = show_list($v, $key, false, $base[$key], $this->colmap);
                        break;
                    case 'attr':
                        $dl[] = show_list($v, $key, false, $base);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $base[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('id', $id);       
            $this->assign('lanmodel', $model);       
            $this->display("Commonf/lang_edit");
        }
        else {
            $data = getLangPost(0);
            $db = new News\NewsModel();
            $rs = $db->updater($data);             
            $this->ajaxReturn($rs);
        }
    }
    
    public function del($id) {
        $id = intval($id);
        $db = new News\NewsModel();
        $rs = $db->del($id);    
        $this->ajaxReturn($rs);;     
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
            $db = new News\NewsModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }
    }
}