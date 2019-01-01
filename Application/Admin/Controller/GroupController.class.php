<?php

namespace Admin\Controller;
use Common\Model;
use Common\ViewModel;


class GroupController extends BaseController {

    /**
     * 角色列表
     * @return [type] [description]
     */
    public function _initialize() {
        parent::_initialize();       
        $plist = array('del', 'update', 'add', 'detail','batchoperate');
        $this->getuserpower($plist);
    }

    public function index() {
        $db = new Model\AuthGroupModel();
        if (IS_POST) {
            $map = getselectmap($this->manageid, $this->manages_id);
            $map['pagesize'] = $this->pagesize;
            $rs = $db->get_list($map);
            $newrs = showTranceDatabymap($db->ShowMap, $rs['list'], $this->userPower);
            $drs = array('list' => $newrs, 'totalSize' => $rs['totalSize'], 'status' => 0);
            $this->ajaxReturn($drs);
            exit();
        }
        $this->assign('showmap', $db->ShowMap);
        $this->display('Commonf/index');
    }

    /**
     * 添加角色
     */
    public function add() {
        //默认显示添加表单
        if (!IS_POST) {
            $dataset = C('GROUPPANLE');
            foreach ($dataset as $key => $v) {
                switch ($key) {                   
                    default :
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display('Commonf/commonf');
        } else {
            $db = new Model\AuthGroupModel;
            $data = getpost(1);
            $data['sid_id'] = $this->manages_id;
            $data['rules'] = '';
            $msg = $db->addnew($data);
            $this->ajaxReturn($msg);
        }
    }

    /**
     * 更新文章信息
     * @param  [type] $id [文章ID]
     * @return [type]     [description]
     */
    public function update() {
        $id = I("get.id", 0, intval);
        $db = new Model\AuthGroupModel;
        //默认显示添加表单
        if (!IS_POST) {
            $model = $db->getmodel($id);
            $dataset = C("GROUPPANLE");
            foreach ($dataset as $key => $v) {
                switch ($key) {                    
                    default :
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display("Commonf/commonf");
        } else {
            $data = getpost(1);
            $data['id'] = $id;
            $db = new Model\AuthGroupModel;
            $msg = $db->update($data);
            $this->ajaxReturn($msg);
        }
    }

    public function del($id) {      
        $db = new Model\AuthGroupModel();
        $rs = $db->del($id);       
        $this->ajaxReturn($rs);     
    }

    /**
     * 角色分配权限
     */
    public function detail() {
        $id = I("get.id", 0, intval);
        $db = new Model\AuthGroupModel();
        //默认显示添加表单
        if (!IS_POST) {
            $group = $db->getmodel($id);
            $dbrule = new Model\AuthRuleModel();
            $map['status'] = 1;//启用
            $map['issys'] = array('neq', 2);//非系统权限
            $dl = $dbrule->getTree($map);
            $this->assign('dl', $dl);
            $showmap = $dbrule->ShowMap;

            unset($showmap['btngroup']);
            unset($showmap['ismenu']);
            unset($showmap['issys']);
//            unset($showmap['name']);
            unset($showmap['status']);
            unset($showmap['sortid']);
            
            $this->assign('showmap', $showmap);
            $titleinfo = "给【{$group['title']}】角色分配权限";
            $this->assign('titleinfo', $titleinfo);
            $this->assign('checkarray', $group['rules']);
            $this->assign('idval', $group['id']);
            $this->display('Commonf/seltree');
        } else {
            $data = getpost(0);
            $model['id'] = $id;
            $model['rules'] = $data['ids'];
            $db = new Model\AuthGroupModel();
            $msg = $db->update($model);
            $this->ajaxReturn($msg);
        }
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
            $db = new Model\AuthGroupModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }        
    }

}
