<?php

namespace Admin\Controller;
use Common\Model;
use Common\ViewModel;

class IndexController extends BaseController {

    public function index() {
        $db = new Model\SubsidiaryModel();
        $sidlist = $db->builarray();
        $this->assign("curentsid",$sidlist[$this->manages_id]);
        $this->assign('subsidlist', $sidlist);
        $this->display();
    }

    public function setLangue($l = "cn") {
        $lang = $this->languelist[$l];
        if (!$lang) {
            $lang = $this->languelist[$this->bconfig['default_lang']];
        }
        $langdata = array(
            'default_lang' => $this->bconfig['default_lang'],
            'curent_lang' => $lang['tagvalue'],
            'langtitle' => $lang['title'],
            'shortlangtitle' => $lang['shorttitle'],
            'sid_id' => $this->manages_id
        );
        setLangueInfo($langdata);
        $ref = I('server.HTTP_REFERER');
        $this->success('当前语言修改完毕', $ref);
    }
    
    public function setcursid($sd) {
        $db = new Model\SubsidiaryModel();
        $sidlist = $db->builarray();
        if ($sidlist[$sd]) {
            $admin = new Model\AdminModel();
            $admin->chanagesid($this->manageid, $sd);
            $fu = new \Org\Util\FileUtil();
            $fu->unlinkDir(RUNTIME_PATH);
            $fu->unlinkDir(DATA_PATH);
            makeclearmark($sidlist[$sd]['sid_dir'], 'clearall');
        }
        $ref = I('server.HTTP_REFERER');
        $this->success("切换到站点{$sidlist[$sd]['sid_name']}", $ref);
    }

    public function welcome() {
        $this->display();
    }

    /*踢人下线*/
    public function offline($mid = 0) {
        $mid = intval($mid);
        if ($mid > 0) {
            $db = new Model\SaferuleModel();
            $db->offline($mid);
             $this->success("操作完成！", U('index/welcome'));
        }       
    }

    /**
     * 清除缓存
     */
    public function cache_clear() {
        $fu = new \Org\Util\FileUtil();
        $fu->unlinkDir(RUNTIME_PATH);
        $fu->unlinkDir(DATA_PATH); 

        $db = new Model\SubsidiaryModel();
        $model = $db->getmodebyid($this->manages_id);
        $sid_dir = $model['sid_dir'];
        makeclearmark($sid_dir, 'clearall');
//        $ref = I('server.HTTP_REFERER');
        $ref=U('index/welcome');
        $this->success('缓存清理完成', $ref);
    }

    /**
     * 刷新页面
     */
    public function reviewfile($mark) {             
        $db=new Model\SubsidiaryModel();
        $model=$db->getmodebyid($this->manages_id);
        $sid_dir="{$model['sid_dir']}";
        makeclearmark($sid_dir,$mark);  
    }
    
    /**
     * 顶部页面
     */
    public function top(){       
        $rul= getruls(); 
        foreach ($rul as $key => $v) {
            if ($v['issys'] <= $this->usergrade && in_array($v['ismenu'], array('2', '3'))) {
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
        
        $db=new Model\SubsidiaryModel();
        $model=$db->getmodebyid($this->manages_id);
        $myrul= explode(',',$model['sid_sitname']);
        $this->assign('myurl', $myrul[0]); 
        cookie(C('ALLIANCE.MYSITEURL'), "http://{$myrul[0]}");
        $this->display();
    }
    
    public function left() {
        $map['issys'] = array('elt', $this->usergrade);
        $map['parentid'] = array('eq', 0);
        $map['ismenu'] = array('in', '1,3,');
        
        $mapsub['issys'] = array('elt', $this->usergrade);
        $mapsub['ismenu'] = array('in', '1,3,');
    
        if ($this->manage['m_grade'] == 2) {
            if (empty($this->manageruls)) {
                redirect(U('index/errormsg', array('msg' => '该帐号尚未配置权限，请联系网站超级管理员')));
                die();
            }
            $mapsub['id'] = array('in', $this->manageruls);
            $map['id'] = array('in', $this->manageruls);
        }
 
        $r=rtrim($this->manageruls,',');
        $managerule=explode(',', $r);
        $rul =  getruls(); 
        foreach ($rul as $key => $val) { 
            if ($this->checkcondition($this->manage, $val)) {
                if ($val['issys'] <= $this->usergrade && in_array($val['ismenu'], array('1', '3')) && $val['parentid'] == 0) {

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
                $val['pram']=  getlinkmap($val['extpram']);
               if ($this->checkcondition($this->manage, $val)) {
                if ($val['issys'] <= $this->usergrade & in_array($val['ismenu'], array('1', '3')) & $val['parentid'] == $v['id']) {
                    if ($this->manage['m_grade'] == 2) {
                        if (in_array($val['id'],$managerule))
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

        $this->assign('SideMenu', $rule);
        $this->assign('sysconfig', $this->sysconfig);

        $this->display();
    }

    public function main() {      
        $db = new Model\SubColumnsModel();
        $map = getselectmap($this->manageid, $this->manages_id);    
        $map['istopic'] = 0;
        $map['parentid'] = 0;
        $map['lev'] = array('in', '2,3,4');
        if($this->managegrade==2){
            
            if ($this->bconfig['enable_tel'] == 1) {//岗位鉴权
                if(crackin($this->manage['tel'], '1,2')){
                    unset($this->colmap['id']);//移除下拉列表限制
                }
                else{
                    $map['colid'] = array('in', $this->managememu);
                }
            } else {
                $map['colid'] = array('in', $this->managememu);
            }
        }
        //$map['m_id'] = array('eq', $this->manageid);      
        $tree = $db->gettree($map);
        $this->assign('dl', $tree);
        
        
        $map['istopic'] = 1;
        $tree2 = $db->gettree($map);
        $this->assign('dl2', $tree2);

        $this->assign('countmap', $xmap); //统计全部
        $xmap['isshow'] = 1;
        $this->assign('countmapshow', $xmap); //统计审核
        $xmap['isshow'] = 2;
        $this->assign('countmapunshow', $xmap); //统计未审          
        $xmap['isshow'] = 0;
        $this->assign('countmapwatshow', $xmap); //统计待审  
        
        $this->assign('m_id',$this->manageid);
        $this->assign('sysconfig',$this->sysconfig);
        
        $cancheck=  $this->checkpower('news/index');
        $canadd=  $this->bconfig['enable_post'] ? true : $this->checkpower('news/add');
        //$canadd=  $this->checkpower('news/add');
        $this->assign('cancheck',$cancheck);
        $this->assign('canadd',$canadd);
        
        $tcancheck=  $this->checkpower('topic/index');
        $tcanadd=  $this->bconfig['enable_post']==1 ? true : $this->checkpower('topic/add');
        $this->assign('tcancheck',$tcancheck);
        $this->assign('tcanadd',$tcanadd);
        $this->display();
    }
    
    /*没有权限时跳转*/
    public function errormsg($msg){
        $msg=htmlspecialchars($msg);
        $this->assign('message',$msg);
        $this->display();
    }
}
