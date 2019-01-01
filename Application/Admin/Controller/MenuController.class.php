<?php
namespace Admin\Controller;

use Common\Model;
use Common\ViewModel;

/**
 * Description of 菜单管理
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class MenuController extends BaseController {

    protected $mtype;
    public function _initialize() {
        parent::_initialize();
        $this->assign('tabelname', 'Menu');
        $menutype = I("menutype", 0, intval);
        $this->mtype = array('menutype' => $menutype);
        $exparm = array('menutype' => $menutype);
        $this->getuserpower(null, $exparm);
    }

    public function index($menutype = null) {
        $db = new Model\MenuModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        if (!is_null($menutype)) {
            $map['menutype'] = $menutype;
        }
       
        $dl = $db->getTree($map);     
        $this->assign('dl', $dl);
        $this->assign('showmap', $db->ShowMap);
        $this->display('Commonf/tree');
    }

    //添加
    public function add($menutype) {       
        $db=new Model\MenuModel(); 
        $dataset = C("MENUMOBAN");  
        $this->assign('basecard', $dataset['table_card']);
        $this->colmap['menutype'] = $menutype;
        if (!IS_POST) {           
            $map['isshow'] = 1;
            $parentid = I('get.parentid', 0, 'intval');
         
            foreach ($dataset as $key => $v) {
                switch ($key) {                   
                    case 'parentid':
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $map['menutype'] = $menutype;
                        $map['id'] = $id;                      
                        $dl[] = show_list($v, $key, false, $parentid, $map);
                        break;
                     case 'menutype':                     
                        $dl[] = show_list($v, $key, false, $menutype );
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }       
            $this->assign('setdata', $dl);   
            $this->display("Commonf/lang_edit");
        }
        else {
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
    public function update($id,$menutype) {
        $id = intval($id);
        $menutype = intval($menutype);
        $db=new Model\MenuModel();
        //默认显示添加表单
        if (!IS_POST) {         
            $dataset = C("MENUMOBAN");
            $this->assign('basecard', $dataset['table_card']);
            $model = $db->getmodel($id);
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                switch ($key) {
                    case 'parentid':
                        $map = getselectmap($this->manageid, $this->manages_id);
                        $map['menutype'] = $menutype;
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
            $this->assign('actionurl', U("update"));
            $this->display("Commonf/lang_edit");
        }
        else {
            $data = getLangPost(1);            
            $rs = $db->updater($data);  
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 删除栏目
     * @param type $id
     */
    public function del($id) {
        $id = intval($id);
        $db = new Model\MenuModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        $rs = $db->del($map, $id);
        $this->ajaxReturn($rs);
    }

}
