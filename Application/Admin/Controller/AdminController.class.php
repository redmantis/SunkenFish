<?php

namespace Admin\Controller;

use Common\Model;
use Common\ViewModel;

/**
 * 用户管理
 */
class AdminController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('tabelname', 'Admin');
        $plist = array('del', 'update', 'add', 'access');
        $this->getuserpower($plist);
    }

    /**
     * 用户列表
     * @return [type] [description]
     */
    public function index($key = "") {
        $db = new Model\AdminModel();
        if (IS_POST) {
            if ($key !== "") {
                $where['m_name'] = array('like', "%$key%");
                $where['truename'] = array('like', "%$key%");
                $where['_logic'] = 'or';
            }
            $status = I('status',1, intval);
            $map = getselectmap($this->manageid, $this->manages_id);
            if ($status != -1) {
                $map['status'] = $status;
            }
            if ($where) {
                $map['_complex'] = $where;
            }
            
            $map['pagesize'] = $this->pagesize;
            $rs = $db->get_list($map);
            $newrs = showTranceDatabymap($db->ShowMap, $rs['list'], $this->userPower);
            $drs = array('list' => $newrs, 'totalSize' => $rs['totalSize'], 'status' => 0);
            $this->ajaxReturn($drs);
            exit();            
        }
        $dataset = C('ADMINFILTER');
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
        $this->assign('showmap', $db->ShowMap);
        $this->display('Commonf/index');
    }

    /**
     * 添加用户
     */
    public function add() {
        //默认显示添加表单
        if (!IS_POST) {
            $dataset = C('ADMINPANLE');
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case "m_name":
                        unset($v['readonly']);
                        $dl[] = show_list($v, $key, false);
                        break;
                    case "deptid":
                        $map['sid_id'] = $this->manages_id;
                        $map['parentid'] = 0;
                        $dl[] = show_list($v, $key, false, 0, $map);
                        break;
                    case "postid":
                        $map['sid_id'] = $this->manages_id;
                        $map['parentid'] = 0;
                        $dl[] = show_list($v, $key, false, 0, $map);
                        break;               
                    default :
                        $dl[] = show_list($v, $key);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display('Commonf/commonf');
        } else {
            //如果用户提交数据
            $db = new Model\AdminModel();
            $data = getpost();
            $data['sid_id'] = $this->manages_id;
            $rs = $db->addnew($data);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 更新管理员信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function update() {
        //默认显示添加表单
        $db = new Model\AdminModel();
        $m_id = I('m_id', 0, intval);
        if (!IS_POST) {
            $model = $db->getmodel($m_id);     
            $this->assign('model', $model);  
            $dataset = C('ADMINPANLE');
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case 'repassword':
                        continue;
                        break;
                    case 'password':
                        $dl[] = show_list($v, $key, false, '');
                        break;                   
                    default :
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display('Commonf/commonf');
        } else {
            $data = getpost();           
            $rs=$db->update($data);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 管理员更新自已的信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function set() {
        //默认显示添加表单
        $db = new Model\AdminModel();
        if (!IS_POST) {
            $model = $db->getmodel($this->manageid);
            $model['birthday'] = date('Y-m-d', $model['birthday']);
            $dataset = C('ADMINPANLE');
            foreach ($dataset as $key => $v) {
                if (crackin($key, 'm_grade,status,tel,deptid,postid,usertype'))
                    continue;
                switch ($key) {                
                    case 'repassword':
                    case 'password':
                        continue;
                    default :
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $conf=array('id'=>  $this->manageid);
            $this->assign('conf', $conf);//避免页面刷新
            $this->assign('setdata', $dl);
            $this->display('Commonf/commonf');
        } else {
            $data = getpost();
            $data['m_id'] = $this->manageid;          
            $rs = $db->saveinfo($data);
            $this->ajaxReturn($rs);
        }
    }
    
    /**
     * 管理员更新自已的信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function setpass() {
        //默认显示添加表单
        $db = new Model\AdminModel();
        if (!IS_POST) {
            $model = $db->getmodel($this->manageid);
            $model['birthday'] = date('Y-m-d', $model['birthday']);
            $dataset = C('ADMINPANLE');
            foreach ($dataset as $key => $v) {
                if (crackin($key, 'm_grade,status,tel,deptid,postid,usertype'))
                    continue;
                switch ($key) {
                    case "password" :
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                    case "repassword" :
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $conf=array('id'=>  $this->manageid);
            $this->assign('conf', $conf);//避免页面刷新
            $this->assign('setdata', $dl);
            $this->display('Commonf/commonf');
        } else {
            $data = getpost();
            $data['m_id'] = $this->manageid;
            $rs = $db->saveinfo($data);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 删除管理员
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function del($m_id) {        
        $db = new Model\AdminModel();
        $rs = $db->sdel($m_id);        
        $this->ajaxReturn($rs);      
    }

    /**
     * 分配管理员角色
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function access() {
 
        $m_id = I("get.m_id", 0, intval);
        $db = new Model\AuthGroupAccessModel();
        if (!IS_POST) {
            $mbd=new Model\AdminModel();
            $model = $mbd->getmodel($m_id);
           
            $aces = $db->where(array('m_id' => $m_id))->getField('group_id',true);
            $acess = implode($aces, ',');  
            
            $dbg = new Model\AuthGroupModel();
            $map['status'] = 1;
            $map['sid_id'] = $model['sid_id'];
            $group = $dbg->getlist($map);
            $this->assign('dl', $group);
            $showmap = $dbg->ShowMap;
            unset($showmap['btngroup']);
            unset($showmap['status']);
            $this->assign('showmap', $showmap);
            $titleinfo = "给【{$model['truename']}】分配角色";
            $this->assign('titleinfo', $titleinfo);
            $this->assign('checkarray',$acess);
            $this->assign('idval', $m_id);
            $this->display('Commonf/seltree');
        }
        else { 
            $data = getpost();          
            $rs=$db->setAcess($m_id, $data['ids']);           
            $this->ajaxReturn($rs);
        }
    }
}
