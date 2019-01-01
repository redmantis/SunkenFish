<?php

namespace Admin\Controller;

use Think\Controller;
use Common\ViewModel;
use Common\Model;

class BaseController extends Controller {

    public $manageid;                           //当前用户ID
    
    public $manages_id;                         //站点ID
    //当前用户所属站点
    public $hidde_sid_list = true;              //隐藏站点选择列表开关
    public $selectmap;                          //查询条件
    public $managegrade = 0;                      //管理员类型  1 超管  2 普通管理员
    public $managememu = '';                      //管理员管理的栏目列表
    public $bconfig;                            //网站基本配置
    public $sysconfig;                          //系统设置
    public $imageservice;                       //图片服务器
    public $manage;                             //管理员资料
    public $operatetype;                        //当前操作类型
    public $adminlog;                           //日志类 
    public $adminmodel;                         //日志实体
    public $adminloginfo;                       //日志详情
    public $usergrade;                          //管理员权限等级
    public $colmap;                             //当前用户可管理栏目筛选条件
    public $manageruls;                         //用户权限列表
    public $languelist;                         //当前语言列表
    public $langueinfo;                         //当前语言设置   
    public $userPower;                          //用户当前控制器中具备的操作权限
    public $pagesize;

    public function _initialize() {
        set_csp_header(0);

        $this->imageservice = C('IMAGESEVRICE');
        $this->assign('imageservice', C('IMAGESEVRICE'));
        $user = getmanage();
        $this->manageid = $user['mid'];

        if (empty($this->manageid)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $msg = array("status" => 0, "msg" => '请重新登录');
                $this->ajaxReturn($msg);
                return;
            } else {
                $url = U('Login/logout');
                exit('<script language="javascript">top.location.href="' . $url . '"</script>');
            }
        }

        /* 获取当前用户资料 */
        $db = new Model\AdminModel();
        $this->manage = $db->getmodelbyid($this->manageid);
        if ($this->manage['status'] == 0) {
            redirect(U('Login/index'));
        }

        $this->managegrade = $this->manage['m_grade'];
        $this->managememu = $this->manage['collist'];
        $this->manages_id = $this->manage['sid_id'];
        $this->assign('manage', $this->manage);
        

        /* 取得日志公共部分 */
        $this->adminlog = new Model\AdminLogModel();
        $this->adminmodel['m_id'] = $this->manageid;
        $this->adminmodel['sid_id'] = $this->manages_id;
        $this->adminloginfo = getlogtype(CONTROLLER_NAME . '/' . ACTION_NAME);
     

        /* 取得当前站点基本配置 */
        $baseconfig = getConfig('', $this->manages_id, 'baseinfo');
        $this->pagesize = $baseconfig['web_list_pagesize'];
        $this->bconfig = $baseconfig;
        $this->assign("webconfig", $baseconfig);

        $stitle = $this->adminloginfo['opstr'];
        if (empty($stitle)) {
            $stitle = $this->bconfig['web_title'];
        }
        $this->assign('subtitle', $stitle);

        //取得全站系统配置
        $this->sysconfig = getConfig();
        $this->assign('sysconfig', $this->sysconfig);

        /* 用户权限验证 */
        $auth = new \Think\Auth();
        $rule_name = CONTROLLER_NAME . '/' . ACTION_NAME;

        $result = $auth->check($rule_name, $this->manageid); //模块鉴权
             
        if (!checkPower($result, $this->manageid, $this->manage['m_grade'])) {//超级管理员鉴权  
            if (IS_AJAX) {
                $error['status'] = 10000010;
                $error['msg'] = '权限不足';
                $this->ajaxReturn($error);
                exit();
            } else {
                exit('您没有权限访问');
            }
        }

        $usergrade = 0;

        if ($this->manage['m_grade'] == 2) {//普通管理员菜单权限控制
            $g = $auth->getGroups($this->manageid);
            $rules = "";
            foreach ($g as $key => $v) {
                $rules .= $v['rules'];
            }
            $this->manageruls = $rules;
        }

        if ($this->manageid == C('AUTH_CONFIG.AUTH_SUPERMAN')) {
            $usergrade = 2; //系统超管
        }
        $this->usergrade = $usergrade;

        $this->pubassign();
    }

    public function pubassign() {
        $this->assign('sid_id', $this->manages_id);

        /* 获取当前语言信息 */
        $langdata = getLangueInfo();

        $languetable = getAttrsElementList("Langue");
        $tablecard = array();
        $languelist = array();
        foreach ($languetable as $k => $v) {
            $tablecard[$v['title']] = $v['tagvalue'];
            $languelist[$v['tagvalue']] = $v;
        }

        if (!isset($langdata['sid_id'])) {
            $lang = $languelist[$langdata['curent_lang']];
            $langdata = array(
                'default_lang' => $this->bconfig['default_lang'],
                'curent_lang' => $lang['tagvalue'],
                'langtitle' => $lang['title'],
                'shortlangtitle' => $lang['shorttitle'],
                'sid_id' => $this->manages_id
            );
            setLangueInfo($langdata);
        }


        $this->languelist = $languelist;
        $this->assign('tablecard', $tablecard);
        $this->assign('languelist', $languelist);
        $this->assign('default_lang', $this->bconfig['default_lang']);

        $this->langueinfo = $langdata;
        $this->assign('langueinfo', $langdata);


        $rul = getruls();
        foreach ($rul as $key => $v) {
            if (in_array($v['ismenu'], array('2', '3'))) {
                if ($this->manage['m_grade'] == 2) {
                    if (in_array($v['id'], explode(',', $this->manageruls)))
                        $rule[] = $v;
                }
                else {
                    $rule[] = $v;
                }
            }
        }

        $this->assign('topMenu', $rule);

        $this->assign('myurl', $this->bconfig['web_site']);

        $map = array();
        $rule = array();
        $map['issys'] = array('elt', $this->usergrade);
        $map['parentid'] = array('eq', 0);
        $map['ismenu'] = array('in', [1,3]);

        $mapsub['issys'] = array('elt', $this->usergrade);
        $mapsub['ismenu'] = array('in',[1,3]);

        if ($this->manage['m_grade'] == 2) {
            if (empty($this->manageruls)) {
                redirect(U('index/errormsg', array('msg' => '该帐号尚未配置权限，请联系网站超级管理员')));
                die();
            }
            $mapsub['id'] = array('in', $this->manageruls);
            $map['id'] = array('in', $this->manageruls);
        }

        $r = rtrim($this->manageruls, ',');
        $managerule = explode(',', $r);

        foreach ($rul as $key => $val) {
            if ($this->checkcondition($this->manage, $val)) {
                if ($val['issys'] <= $this->usergrade && in_array($val['ismenu'],[1,3]) && $val['parentid'] == 0) {

                    if ($this->manage['m_grade'] == 2) {
                        if (in_array($val['id'], $managerule))
                            $rule[] = $val;
                    }
                    else {
                        $rule[] = $val;
                    }
                }
            }
        }

        foreach ($rule as $k => $v) {
            $subarry = array();
            foreach ($rul as $key => $val) {
                $pram = getlinkmap($val['extpram']);
                $pram['menuid'] = $val['id'];
                $val['pram'] = $pram;
                if ($this->checkcondition($this->manage, $val)) {
                    if ($val['issys'] <= $this->usergrade & in_array($val['ismenu'], array('1', '3')) & $val['parentid'] == $v['id']) {
                        if ($this->manage['m_grade'] == 2) {
                            if (in_array($val['id'], $managerule))
                                $subarry[] = $val;
                        }
                        else {
                            $subarry[] = $val;
                        }
                    }
                }
            }
            $rule[$k]['sub'] = $subarry;
        }

        $this->assign("pmenu", $this->adminloginfo['parentid']);
        $this->assign('smenu', $this->adminloginfo['op']);
        $this->assign('SideMenu', $rule);
        $this->assign('idfiledname', 'id');
        $self_url = I('server.REQUEST_URI');
        $this->assign("self_url", $self_url);
    }

    /**
     * 取得用户权限
     * @param type $plist
     */
    public function getuserpower($plist, $exparm = null) {
        if (empty($plist)) {
            $plist = array('del', 'update', 'add');
        }
        $powerlist = array();
        foreach ($plist as $v) {
            $pw = $this->checkpower(CONTROLLER_NAME . "/{$v}");
            if ($pw) {
                $powerlist[] = $v;
            }
        }

        $this->userPower = $powerlist;
        $b = showButtonArray($powerlist, CONTROLLER_NAME, $exparm);
        $this->assign("buttonlist", $b);
        $this->assign('userPower', $powerlist);
    }

    /**
     * 用户权限验证
     * @param type $rule_name
     * $rule_name = CONTROLLER_NAME . '/' . ACTION_NAME;
     */
    public function checkpower($rule_name, $ckmod = 'url') {
        $auth = new \Think\Auth();
        $result = $auth->check($rule_name, $this->manageid, $ckmod); //模块鉴权
//        echo $rule_name . "-" . $result . "<br />";
//        var_dump($result);
        return checkPower($result, $this->manageid, $this->manage['m_grade']); //超级用户鉴权
    }

    /**
     * 鉴权附加规则
     */
    public function checkcondition($user, $rule) {
        $cond= trim($rule['condition']);
        if ($rule['type'] == 1 && !empty($cond)) {
            $com = explode(',', $rule['condition']);
            $command = '$user[\'' . $com[0] . '\']' . $com[1] . $com[2];
            @(eval('$condition=(' . $command . ');'));
            return $condition;
        } else {
            return true;
        }
    }
    
    
    public function ajaxReturn($rs) {
        $data= trancemessage($rs);
        parent::ajaxReturn($data);
    }

}
