<?php

/*
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-10-16 15:54:12
 * @Modify  2017-10-16 15:54:12
 * @CopyRight:  2017 by RDM
 */

namespace Admin\Controller;

use Common\Model;
use Common\Umodel;
use Common\ViewModel;
/**
 * 会员基础信息管理
 *
 * @author RDM:默鱼
 */
class CustomerController extends BaseController {
      public function _initialize() {
        parent::_initialize();
        $this->assign('tabelname', 'CusCustomer');
        $plist = array('update',  'show');
        $this->getuserpower($plist);  
    }

    /**
     * 用户列表
     * @return [type] [description]
     */
    public function index($key = "") {
        $db = new Umodel\CusCustomerModel();
        if (IS_POST) {
            $data=getpost(0);
            if ($key !== "") {
                $where['username'] = array('like', "%$key%");
                $where['nickname'] = array('like', "%$key%");
                $where['_logic'] = 'or';
            }
            
            $map=array();
            if(!empty($data['key'])){
                $map[$data['keyname']]=array('like', "%{$data['key']}%");
            }
            if($data['status']!=-1){
                $map['status']=$data['status'];
            }
           
            
            $newrs = $db->getlist($map, $this->pagesize);
            $Page = new \Extend\Page($newrs['count'], $this->pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show(); // 分页显示输出
            
            $rs = showTranceDatabymap($db->ShowMap, $newrs['list'], $this->userPower, get_class($db));
            $drs = array('list' => $rs, 'totalSize' => $newrs['count']);
            $this->ajaxReturn($drs);
            die;
        }  
        
        $dataset = C('CUSTOMERILTER');
        foreach ($dataset as $key => $v) {
            switch ($key) {
                  case 'keyname':
                    $map['sid_id'] = array('in', "0,{$this->manages_id}");
                    $dx[] = show_list($v, $key, false, '', $map);
                    break;
                 case 'status':
                    $map['sid_id'] = array('in', "0,{$this->manages_id}");
                    $dx[] = show_list($v, $key, false, '', $map);
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
     * 更新管理员信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function update() {
        //默认显示添加表单
        $db = new Umodel\CusCustomerModel();
        $id = I('id', 0, intval);
        if (!IS_POST) {
            $model = $db->getmodel($id);   
            $this->assign('id', $id);
            $dataset = C('USERDETAIL');
            foreach ($dataset as $key => $v) {
                 switch ($key) { 
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
        }else{
            $data = getpost();
            unset($data['password']);
            if (I('password') != '') {                      
                $salt = createRandCode(6);
                $data['password'] = creatpassword(I('password'), $salt);
                $data['salt'] = $salt;
            }        
            if (!$db->create($data, 2)) {
                $msg['returnCode'] = 0;
                $msg['returnMessage'] = showTagbyMark($db->getError());
                $this->ajaxReturn($msg);
                die;
            }

            $map = array();
            $map['id'] = $data['id'];          
            //更新
            if ($db->where($map)->save($data)) {
                $this->adminloginfo['status'] = 1;
                $this->adminloginfo['statusstr'] = '成功';       //文本状态
                $this->adminloginfo['opcontent'] = $data['username'];      //操作内容
                $this->adminloginfo['objectid'] = $data['id'];       //操作对象ID
                $this->adminloginfo['objecttable'] = 'CusCustomer';     //操作对象表
                $this->adminlog->addlog($this->adminmodel, $this->adminloginfo);
                $msg['returnCode'] = 1;
                $msg['returnMessage'] = "用户信息更新成功";
            } else {
                $msg['returnCode'] = 1;
                $msg['returnMessage'] = "未做任何修改,用户信息更新失败";
            }
            $this->ajaxReturn($msg);
        }   
    }
    
    /**
     * 更新管理员信息
     * @param  [type] $id [管理员ID]
     * @return [type]     [description]
     */
    public function show() {
        //默认显示添加表单
        $db = new Umodel\CusCustomerModel();
        $id = I('id', 0, intval);
        if (!IS_POST) {
            $model = $db->getmodel($id);
            $this->assign('model', $model);
            $dataset = C('USERDETAIL');
            foreach ($dataset as $key => $v) {
                $v['readonly'] = 2;
                switch ($key) {
                    case 'repassword':
                        continue;
                        break;
                    case 'password':
                        continue;
                        break;
                    default :
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display('Commonf/comshow');
        }
    }
}
