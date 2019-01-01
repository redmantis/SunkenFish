<?php

/*
 * 字符：UTF-8
 * 邮件: feiyufly001@hotmail.com
 * 创建：redmantis <默鱼 at feiyufly001@hotmail.com> 2016-8-4 20:39:17
 * 最终：Rdm
 */

namespace Home\Controller;

use Think\Controller;
use Common\News;

/**
 * Description of LisController
 *  文章列表
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class NewsController extends BaseController {

    public $subcollist; //子栏目列表
    public $subcolid; //子栏目ID列表   
    public $titlelist = array();

    public function _initialize() {
        parent::_initialize();
    }

    public function _empty() {

        $pagemod = ACTION_NAME;
        $pagemod = strtolower($pagemod);
        if (checksafepath($pagemod))
            $this->redirect('Common/error');

        $subinfo = $this->bsubsid;
        $db =new News\ColumnsModel();
        
        $cmap=['viewpath'=>$pagemod,'sid_id'=> $this->bsid_id];
        $colmodel= $db->getmodelbypath($cmap); //取得栏目的详细资料        
       

        $colpath = $db->getParentPath($colmodel['id']);

        /* 面包屑 */
        $mbx = array();    
        foreach ($colpath as $v) {
            $title = clearpre($v['title']);
            array_unshift($this->titlelist, $title);
            $mbx[] = [
                'viewpath' => $v['viewpath'],
                'title' => $title,
                'shorttitle' => $v['shorttitle'],
            ];             
        }

        $this->assign('mbx', $mbx);


        $xmap = array('sid_id' => $this->bsid_id);
        $xmap['istopic'] = $colmodel['istopic'];
        $xmap['isshow'] = 1;

        reset($colpath);
        $rootcol = current($colpath);
        
        $this->assign('rootcol',$rootcol);
        
        $sublist = $db->getsublist($xmap, $rootcol['id'], false); //全栏目树  
        $coltree = genTree9($sublist);
        $this->assign('roottree', $coltree); //全栏目树   
        
        if ($mbx[1]) {
            foreach ($coltree as $v) {
                if ($v['viewpath'] == $mbx[1]['viewpath']) {                   
                    $fristcol = $v;
                }
            }
        }
        
        if ($mbx[2] && $fristcol['son']) {
            foreach ($fristcol['son'] as $v) {
                if ($v['viewpath'] == $mbx[2]['viewpath']) {                  
                    $seccol = $v;
                }
            }
        }
        $this->assign("fristcol", $fristcol);  
        $this->assign("seccol", $seccol);  

        $suntree = $db->getsublist($xmap, $colmodel['id'], false); //子栏目树        
        $sontree = genTree9($suntree);
        $this->assign('sontree' . $sontree); //子栏目树  

        $this->bcolums = $colmodel; //当前栏目默认为第一个子栏目      
        $this->subcollist = $suntree;

        $idlst = array_keys($suntree);
        $idlst[] = $this->bcolums['id']; //当前栏目及目栏目 id 列表
        $this->subcolid = $idlst;
  
        $this->assign('bcolums', $this->bcolums); //当前栏目  
        $viewmod = $colmodel['viewmod'];
        $groupmark = $colmodel['groupmark'];
        $ct = count($colpath);
        if (empty($viewmod)) {           
            for ($i = $ct; $i > 0; $i--) {
                $x = $i - 1;
                if (!empty($colpath[$x]['viewmod'])) {
                    $viewmod = $colpath[$x]['viewmod'];
                }
            }
        }
        if (empty($groupmark)) {           
            for ($i = $ct; $i > 0; $i--) {
                $x = $i - 1;
                if (!empty($colpath[$x]['groupmark'])) {
                    $groupmark = $colpath[$x]['groupmark'];
                }
            }
        } 
        $this->assign('groupmark', $groupmark); //当前导航标记
        $this->assign("pagemod",$pagemod);
        $pagesize= 10;
        $data['sid_id'] = $this->bsid_id;
        $data['size'] = $pagesize;
        $data['collist'] = $this->subcolid;
        $data['action']='get_newslist';
        
        $rs=  common_data_readapi('get_newslist',$data);
        $count = $rs['totalSize']; 
        $Page = new \Extend\Page($count, $pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25) 
        $Page->parameter['mod'] = urlencode($this->bcolums['viewpath']);   
        $show = $Page->showarray();        


        $this->assign('dlist', $rs['list']);
        $this->assign('pagestr', $show);
        
        $this->assign("schdata", json_encode($data));

        $this->makemeta($this->titlelist, array($this->bcolums['keyword']), array($this->bcolums['summary']));
        $this->display($viewmod);
    }

    public function search() {          
        $key = I('key', '', checkinput);
        $data['sid_id'] = $this->bsid_id;
        $data['size'] = 10;
        $data['key']=$key;        
        $data['action']='get_newslist'; 
        $this->assign("keyword",$key);
        $this->assign("schdata", json_encode($data));
        $this->makemeta();
        $this->display();
    }

    public function detail() {
        
        $id = I('id', 0, intval);
        $dbn = new News\NewsModel();
        $mp = ['id' => $id, "selectlangue" => 'cn'];
        $model = $dbn->getmodelbyid($mp);
        $mp['selectlangue']='en';
        $emodel = $dbn->getmodelbyid($mp);
        $this->assign('emodel', $emodel);
        $db = new News\ColumnsModel();
        $colmodel = $db->getmodelbyid($model['colid']);        

        $colpath = $db->getParentPath($colmodel['id']);

        /* 面包屑 */
        $mbx = array();
        $groupmark = $pagemod;
        foreach ($colpath as $v) {
            $title = clearpre($v['title']);
            array_unshift($this->titlelist, $title);
            $mbx[] = [
                'viewpath' => $v['viewpath'],
                'title' => $title,
                'shorttitle' => $v['shorttitle'],
            ];             
        }    

        $this->assign('mbx', $mbx);

        $xmap = array('sid_id' => $this->bsid_id);
        $xmap['istopic'] = $colmodel['istopic'];
        $xmap['isshow'] = 1;

        reset($colpath);
        $rootcol = current($colpath);
        
        $this->assign('rootcol',$rootcol);
        
        $sublist = $db->getsublist($xmap, $rootcol['id'], false); //全栏目树  
        $coltree = genTree9($sublist);
        $this->assign('roottree', $coltree); //全栏目树   
        
        if ($mbx[1]) {
            foreach ($coltree as $v) {
                if ($v['viewpath'] == $mbx[1]['viewpath']) {                   
                    $fristcol = $v;
                }
            }
        }
        
        if ($mbx[2] && $fristcol['son']) {
            foreach ($fristcol['son'] as $v) {
                if ($v['viewpath'] == $mbx[2]['viewpath']) {                  
                    $seccol = $v;
                }
            }
        }
        $this->assign("fristcol", $fristcol);  
        $this->assign("seccol", $seccol);
        

        $suntree = $db->getsublist($xmap, $colmodel['id'], false); //子栏目树        
        $sontree = genTree9($suntree);
        $this->assign('sontree' . $sontree); //子栏目树  

        $this->bcolums = $colmodel; //当前栏目默认为第一个子栏目      
        $this->subcollist = $suntree;

        $idlst = array_keys($suntree);
        $idlst[] = $this->bcolums['id']; //当前栏目及目栏目 id 列表
        $this->subcolid = $idlst;
  
        $this->assign('bcolums', $this->bcolums); //当前栏目  
        
        $viewmod = $colmodel['viewmod'];
        $groupmark = $colmodel['groupmark'];
        $pagemod = $colmodel['viewpath'];
        $ct = count($colpath);
        if (empty($viewmod)) {           
            for ($i = $ct; $i > 0; $i--) {
                $x = $i - 1;
                if (!empty($colpath[$x]['viewmod'])) {
                    $viewmod = $colpath[$x]['viewmod'];
                }
            }
        }
        if (empty($groupmark)) {           
            for ($i = $ct; $i > 0; $i--) {
                $x = $i - 1;
                if (!empty($colpath[$x]['groupmark'])) {
                    $groupmark = $colpath[$x]['groupmark'];
                }
            }
        }

        $this->assign('groupmark', $groupmark); //当前导航标记
        $this->assign("pagemod",$pagemod);


        $model['poster'] = trim($model['poster']);
        $model['checker'] = trim($model['checker']);
        $model['pic'] = trim($model['pic']);
        $model['hits'] = showhits($model);

        $dbc = new \Common\Umodel\CusCollectModel();
        if ($this->userId) {
            $map = array(
                'userid' => $this->userId, //用户ID
                'collectid' => $id['id'],
                'collecttable' => 'News', //收集对像表
                'collecttype' => CollectType_Like, // 点赞
                'sid_id' => $this->bsid_id, //所在站点
            );

            $cmodel = $dbc->iscollecting($map);
            if ($cmodel) {
                $this->assign('islike', 1);
            }
        }
        $model['flowers'] = showflowers($model);

        $this->assign('model', $model);
        $this->assign('pre', $model['pre']);
        $this->assign('next', $model['next']);

        $atitle[] = $model['page_title'];
        $keyword[] = $model['keyword'];
        $desc[] = $model['summary'];
        $this->makemeta($atitle, $keyword, $desc);
        $this->display("{$viewmod}detail");
    }

}
