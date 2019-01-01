<?php

namespace Admin\Controller;

use Common\Model;

/**
 * 权限管理
 */
class AuthController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $plist = array('del', 'update', 'add', 'batchoperate');       
        $this->getuserpower($plist);
    }

    /**
     * 规则列表
     * @return [type] [description]
     */
    public function index() { 
        $db = new Model\AuthRuleModel();
        $pid = 0;
        $rs = $db->getTree($map, $pid);
        $this->assign('dl', $rs);
        $this->assign('showmap', $db->ShowMap);
        $this->display('Commonf/tree');
    }

    /**
     * 添加规则
     */
    public function add() {
      
        //默认显示添加表单
        if (!IS_POST) {
            $parentid = I('get.parentid', 0, 'intval');
            if ($parentid > 0){
                    $db = new Model\AuthRuleModel();
                    $m=$db->getmodel($parentid);
                    $parentname=$m['title'];
            }
            $dataset = C('AUTHPANLE');
            $this->assign('basecard', $dataset['table_card']);
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case 'parentid':
                        $map['parentid'] = 0;
                        $dl[] = show_list($v, $key, false, $parentid, $map);
                        break;
                    case 'name':
                        $xmap = getctrllist();              
                        $dl[] = show_list($v, $key, false, $parentid, $xmap);
                        break;
                    default :
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }

            $this->assign('setdata', $dl);
            $this->assign("parentname", $parentname);
            $this->assign("parentid", $parentid);
            $this->assign('actionurl', U('add'));
            $this->display("Commonf/lang_edit");
        }
        if (IS_POST) {
            $data = getLangPost(1);         
            $db = new Model\AuthRuleModel();
            $rs = $db->addnew($data);           
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 更新
     * @param  [type] $id [文章ID]
     * @return [type]     [description]
     */
    public function update($id) {
        $id = intval($id);
        $db = new Model\AuthRuleModel();
        $model = $db->getmodel($id);
        //默认显示添加表单
        if (!IS_POST) {
            $dataset = C('AUTHPANLE');
            $this->assign('basecard', $dataset['table_card']);         
            $dataset = C('AUTHPANLE');
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                switch ($key) {
                    case 'parentid':
                        $map['parentid'] = 0;
                        $map['id']=$id;
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;
                    case 'name':
                        $xmap = getctrllist();                     
                        $dl[] = show_list($v, $key, false, $value, $xmap);
                        break;                 
                    default :
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('id', $id);
            $this->assign('lanmodel', $model);
            $this->assign('actionurl', U('update'));
            $this->display("Commonf/lang_edit");
        }
        if (IS_POST) {
            $db = new Model\AuthRuleModel();
            $data = getLangPost(1);
            $rs = $db->updater($data);         
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 删除文章
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function del($id) {   
        $db = new Model\AuthRuleModel();
        $rs = $db->del(null, $id);
        $this->out();
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
            $db = new Model\AuthRuleModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }       
    }

    public function out() {
        $data = array();
        $db = new Model\AuthRuleModel();
        $db->rewrit();
    }

}
