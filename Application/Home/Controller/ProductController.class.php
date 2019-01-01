<?php

namespace Home\Controller;

use Think\Controller;
use Common\Model;
use Common\Gmodel;
use Common\ViewModel;

class ProductController extends BaseController {

    public $subcollist; //子栏目列表
    public $subcolid; //子栏目ID列表   
    public $titlelist = array();
    public $model;

    public function _initialize() {
        parent::_initialize();

        if (ACTION_NAME == "detail") {
            $idrs = I("id", 0, checkcid);
            $pddb = new Gmodel\GoodsModel();
            $pmodel = $pddb->getmodelbyid($idrs['id']);
            $this->model = $pmodel;
            $db = new Gmodel\GcateModel();
            $colmodel = $db->getmodelbyid($idrs['colid']);      
            $pagemod = $colmodel['viewpath'];
        } 
        else {            
            $db = new Gmodel\GcateModel();
            $pagemod = I('mod', 'single');
            $viewpath=$db->getviewpath();
            $mp=[$viewpath=>$pagemod,'sid_id'=> $this->bsid_id];    
            $colmodel = $db->getmodelbypath($mp); //取得栏目的详细资料       
            
            $endid = $db->getendnode($colmodel['id']);
            if ($endid !== $colmodel['id']) {
                $colmodel = $db->getmodelbyid($endid);          
            }
        }

        if ($colmodel) {       
            $db = new Gmodel\GcateModel();
            $colpath = $db->getParentPath($colmodel['id']);
          
            
            /* 面包屑 */
            $mbx = array();
            $mbx[] = array(
                'url' => getcolmnurl($this->bsid_id, 'index', 0),
            );
            $groupmark = "";
            foreach ($colpath as $v) {
                array_unshift($this->titlelist, $v['title']);
                $url = makeNavUrl($v);
                $mbx[] = array(
                    'url' => "<a href=\"{$url}\" target=\"{$v['target']}\">{$v['title']}</a>",
                    'viewpath' => $v['viewpath']
                );
                if (!empty(trim($v['groupmark']))) {
                    $groupmark = $v['groupmark'];
                }
            }

            $this->assign('mbx', $mbx);
            $this->assign('groupmark', $groupmark); //当前导航标记
            $pmap = array('sid_id' => $this->bsid_id);
            $pmap['groupmark'] = $groupmark;
            $pmap['parentid'] = 0;
    
            $map = array('sid_id' => $this->bsid_id);
            $dl = $db->getTree($map);                   
            $this->assign('leftmenu', $dl);            
        }

        $this->colmap = array('sid_id' => $this->bsid_id);
        $this->colmap['isshow'] = 1;
        $this->colmap['shownav'] = array('in', '1,2');

        $rootcol = $colpath[0];
        $sublist = $db->getsublist($this->colmap, $rootcol['id'], false); //全栏目树        
        $coltree = genTree9($sublist);
        $this->assign('roottree', $coltree); //全栏目树  

        $suntree = $db->getsublist($this->colmap, $colmodel['id'], false); //子栏目树        
        $sontree = genTree9($suntree);
        $this->assign('sontree' . $sontree); //子栏目树        

        if (count($sontree) > 0) {//当前栏目为根栏目且包含子栏目时
            $this->bcolums = $sontree[0]; //当前栏目默认为第一个子栏目
            $suntree = $db->getsublist($this->colmap, $this->bcolums['id'], false); //子栏目树    
            $sontree = genTree9($suntree); //子栏目树  
        } else {
            $this->bcolums = $colmodel; //当前栏目默认为第一个子栏目
        }
        $this->subcollist = $suntree;

        $idlst = array_keys($suntree);
        $idlst[] = $this->bcolums['id']; //当前栏目及目栏目 id 列表  

        $this->subcolid = $idlst;
        if ($this->bcolums['parentid']) {
            $parent = $db->getmodel($this->bcolums['parentid']);
            $parentcol = $parent['all'];
        }
        $this->assign('parentcol', $parentcol); //父栏目
        $this->assign('bcolums', $this->bcolums); //当前栏目        
        $this->assign('parentpath', $colpath); //当前栏目路径
        $this->assign('coltree', $coltree); //全栏目树 
        $taglist = getAttrsElementList('Goodstag');
        $this->assign('taglist', $taglist);        
        $this->assign('searchurl', creat_url_lan());
//        $this->creattemplate(CONTROLLER_NAME, ACTION_NAME, $colmodel);
    }

    public function index() {
        $coldb = new Gmodel\GcateModel();
        $psize = $coldb->getdefaultvaue($this->bcolums['id'], "pagelist");
        $pagesize = $psize ? $psize : $this->bconfig['front_list_pagesize'];

        $data['sid_id'] = $this->bsid_id;
        $data['size'] = $pagesize;
        $data['collist'] = $this->subcolid;

        $rs = common_data_readapi('get_goodslist', $data);

        $count = $rs['count'];
        $Page = new \Extend\Page($count, $pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25) 
        $Page->parameter['mod'] = urlencode($this->bcolums['viewpath']);
        $show = $Page->showarray();

        $this->assign('dlist', $rs['list']);
        $this->assign('pagestr', $show);
        $this->makemeta($this->titlelist, array($this->bcolums['keyword']), array($this->bcolums['summary']));
        $this->display();
    }

    public function detail() {
        array_unshift($this->titlelist, $this->model['title'], $this->model['keywords']);
        $this->makemeta($this->titlelist);
        $photolist = explode(',', $this->model['photolist']);
        if ($this->model['thumb']) {
            array_unshift($photolist, $this->model['thumb']);
        }
        $this->assign("photolist", $photolist);        
        $this->model['click_count'] = showgoodshits($this->model);

        $data['tagmar'] = "Goodstag";
        $data['taglist'] = $this->model['summary'];
        $rs = common_data_readapi('getTaglist', $data);
        $this->assign('newstaglist',$rs);
        
        $this->assign('model', $this->model);
        $this->display();
    }
    
    public function search(){
        $tag=I('tag','',checkinput);  
        $key=I('key','',checkinput);  
        $pagesize =  $this->bconfig['front_list_pagesize'];
        $data['sid_id'] = $this->bsid_id;
        $data['size'] = $pagesize;
        if($tag){
             $data['tag'] = $tag;
             $tagname=  getkeyname($tag,'Goodstag');
             array_unshift($this->titlelist, $tagname);
        }
        
        if ($key) {
            $data['key'] = $key;
            array_unshift($this->titlelist, $key);
        }

        $rs = common_data_readapi('get_goodslist', $data);

        $count = $rs['count'];
        $Page = new \Extend\Page($count, $pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25) 
        $Page->parameter['mod'] = urlencode($this->bcolums['viewpath']);
        $show = $Page->showarray();

        $this->assign('dlist', $rs['list']);
        $this->assign('pagestr', $show);
        $this->makemeta($this->titlelist, array($this->bcolums['keyword']), array($this->bcolums['summary']));
        $this->display();
    }
}
