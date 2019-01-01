<?php
namespace Admin\Controller;

use Common\Model;
use Common\Gmodel;
use Common\ViewModel;

/**
 * Description of CountController
 * 数据统计
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class GoodsController extends BaseController {
   
    public $basetable="goods";
    public function _initialize() {
        parent::_initialize();
        $suffix = I("suffix", '');
        if ($suffix == '1') {
            $suffix = "";
        }
        $this->basetable = $this->basetable . $suffix;
        $plist = array('del', 'update', 'add', 'batchoperate');
        $this->getuserpower($plist);
    }

    public function index($key = "") {
        $db = new Gmodel\GoodsModel();
        $brand_id = I('brand_id', 0, intval);
        $cat_id = I('cat_id', 0, intval);
        if (IS_POST) {
            $d = getpost(0);          
            $map = getselectmap($this->manageid, $this->manages_id);
            if ($cat_id > 0) {
                $subc = new Gmodel\GcateModel();
                $subids = $subc->getsublistid($map, $cat_id);
                $map['cat_id'] = array('in', $subids);
            }

            if ($key !== "") {
                $map['title'] = array('like', "%$key%");
            }

            if ($brand_id > 0) {
                $map['brand_id'] = $brand_id;
            }    

            $map['pagesize']= $this->pagesize;
            $rs=$db->get_list($map);
            $newrs = showTranceDatabymap($db->ShowMap, $rs['list'], $this->userPower);
            $drs = array('list' => $newrs, 'totalSize' => $rs['totalSize'],'status'=>0);

            $this->ajaxReturn($drs);
            exit();
        }
        
        $this->assign('showmap', $db->ShowMap);

        $map = getselectmap($this->manageid, $this->manages_id);
        //查询参数面板输出
        $dataset = C('GOODSSEARCH');
        foreach ($dataset as $key => $v) {
            switch ($key) {
                case 'brand_id':
                    $map['sid_id'] = array('in', "0,{$this->manages_id}");
                    $dx[] = show_list($v, $key, false, $value, $map);
                    break;
                case 'cat_id':
                    $map = getselectmap($this->manageid, $this->manages_id);
                    $dx[] = show_list($v, $key, false, $value, $map);
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
        $dataset = C('GOODS');
        $this->assign('basecard', $dataset['table_card']);
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {
                $value = '';
                switch ($key) {
                    case 'cat_id':                              
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;
                     case 'brand_id':                     
                        $map['sid_id'] = array('in', "0,{$this->manages_id}");
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;  
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }     
            $this->assign('setdata', $dl);
            $this->display("Commonf/lang_edit");
        } else {
            $data = getLangPost(0);
            $db = new Gmodel\GoodsModel();
            $rs = $db->addnew($data);
            $this->ajaxReturn($rs);
        }
    }

    public function update($id) {
        $id = intval($id);
        $db = new Gmodel\GoodsModel(['basetable'=> $this->basetable]);
        $dataset = C('GOODS');
        $this->assign('basecard', $dataset['table_card']);
        $model = $db->getmodel($id);
        if (!IS_POST) {          
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                switch ($key) {
                    case 'cat_id':
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $map['id'] = $id;                     
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;
                    case 'brand_id':
                        $map['sid_id'] = array('in', "0,{$this->manages_id}");
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
            $db = new Gmodel\GoodsModel(['basetable'=> $this->basetable]);
            $data = getLangPost(0);         
            $rs = $db->updater($data);          
            $this->ajaxReturn($rs);
        }
    }
    
    public function del($id) {
        $id = intval($id);
        $db = new Gmodel\GoodsModel();
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
            $db = new Gmodel\GoodsModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }
    }
}