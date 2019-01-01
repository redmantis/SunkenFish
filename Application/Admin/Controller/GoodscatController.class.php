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
class GoodscatController extends BaseController {
    
    public $basetable="goods_cate";
    public function _initialize() {
        parent::_initialize();
        $plist = array('del', 'update', 'add', 'batchoperate');
        $this->getuserpower($plist);
    }

    public function index($key = "") {
        $db = new Gmodel\GcateModel();        
        $map = getselectmap($this->manageid, $this->manages_id);
        $rs = $db->getTree($map);        
        $this->assign('dl', $rs);        
        $this->assign('showmap', $db->ShowMap);

        //查询参数面板输出
        $dataset = C('GOODSCATESEARCH');
        foreach ($dataset as $key => $v) {
            switch ($key) {
                case 'aimcol':
                    $dx[] = show_list($v, $key, false, I($key), $map);
                    break;
                default:
                    $dx[] = show_list($v, $key, false, I($key));
                    break;
            }
        }
        $this->assign('searchdata', $dx);
        $this->display('Commonf/tree');
    }

    public function add() {
        $dataset = C('GOODSCAT');
        $this->assign('basecard', $dataset['table_card']);
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {
                $value = '';
                switch ($key) {
                    case 'parentid':
                        $parentid = I('get.parentid', 0, 'intval');
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $dl[] = show_list($v, $key, false, $parentid, $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display("Commonf/lang_edit");
        } else {
            $data = getLangPost(1);
            $db = new Gmodel\GcateModel();
            $rs = $db->addnew($data);                
            $this->ajaxReturn($rs);
        }
    }

    public function update($id) {
        $id = intval($id);
        $db = new Gmodel\GcateModel();
        $dataset = C('GOODSCAT');
        $this->assign('basecard', $dataset['table_card']);
        $model = $db->getmodel(['id'=>$id]);  
        if (!IS_POST) {          
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                switch ($key) {
                    case 'parentid':           
                        $map = getselectmap($this->manageid, $this->manages_id);
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
            $db = new Gmodel\GcateModel();
            $data = getLangPost(1);           
            $rs = $db->updater($data);
            $this->ajaxReturn($rs);
        }
    }
    
    public function del($id) {
        $id = intval($id);
        $db = new Gmodel\GcateModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        $rs = $db->del($map, $id);
        $this->ajaxReturn($rs);
    }
    
    public function batchoperate() {
        if (IS_POST) {
            $idlist = I('idlist');
            $suffix = I("suffix", 1);
            $map = ['idlist' => $idlist, 'suffix' => $suffix];
            $db = new Gmodel\GcateModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }
    }

}