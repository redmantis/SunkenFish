<?php

namespace Home\Controller;

use Think\Controller;
use Common\Book;

class BooksController extends BaseController {

    public $subcollist; //子栏目列表
    public $subcolid; //子栏目ID列表   
    public $titlelist = array();
    public $model;

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {

        $catid = I('catid', 0, intval);
        $map = ['cat_id' => $catid];

        $catename = getkeyname($catid, "novelcate");
        $this->assign("catename", $catename);
        $this->assign("catid",$catid);

        $map['comment_count'] = 1;

        $bk = new Book\BkBookModel();
        $bl = $bk->get_list($map, 14);
        $this->assign("toplist", $bl['list']);

        $map['sortstr'] = 20;
        $bl = $bk->get_list($map, 14);
        $this->assign("newestlist", $bl['list']);

        $skip = get_firstrow($this->pgsize);
        $rs = $bk->get_list($map, $skip, $this->pgsize);
        $count = $rs['totalSize'];

        $Page = new \Extend\Page($count, $this->pgsize);
        $show = $Page->showarray();
        $this->assign('dlist', $rs['list']);
        $this->assign('pagestr', $show);
        $this->titlelist[] = $catename;
        $this->makemeta($this->titlelist, [$catename], [$catename]);
        $this->display();
    }

    public function detail() {
        $bk = new Book\BkBookModel();
        $bkid = I('bkid', 0, intval);
        $model = $bk->getmodelbyid($bkid);

        $catename = getkeyname($model['cat_id'], "novelcate");
        $this->assign("catename", $catename);

        $this->titlelist[] = $model['title'];
        $this->makemeta($this->titlelist, [$model['title']], [$model['info']]);
        $this->assign('articledb', $model);

        $fpgsize = 99;
        $art = new Book\BkArticleModel();
        $map['bookid'] = $bkid;
        $rs = $art->get_list($map,0,18);
        $this->assign('newchaperlist', $rs['list']);
        
        $map['sortstr'] = 2;
        $skip = get_firstrow($fpgsize);
       
        $rs = $art->get_list($map, $skip, $fpgsize);
        $count = $rs['totalSize'];

        $Page = new \Extend\Page($count,$fpgsize);
        $show = $Page->showarray();
        $this->assign('chapterdb', $rs['list']);
        $this->assign('pagestr', $show);

        $this->display();
    }

    public function articl() {
        $atid = I("atid", 0, intval);
        $atdb = new Book\BkArticleModel();
        $atmodel = $atdb->getmodelbyid($atid);       
        $atmodel['content']= htmlSummary(read_content($atmodel['bookid'], $atid),1000)."…… <br />本站仅演示网站功能，完整章节，请前往正版网站阅读。<br /><br />";
        $this->assign('article', $atmodel);
     
        $prnex=$atdb->getprenext($atid, ['bookid'=>$atmodel['bookid']]);
        
        $this->assign('prenex',$prnex);
        
        $bk = new Book\BkBookModel();       
        $model = $bk->getmodelbyid($atmodel['bookid']);
        $this->titlelist[] = $model['title'];
        $this->titlelist[] = $atmodel['title'];
        $this->makemeta($this->titlelist, [$model['title'],$atmodel['title']], [$model['info']]);
        $this->assign('articledb', $model);
        
        

        $this->display();
    }

    public function search() {
        $tag = I('tag', '', checkinput);
        $key = I('key', '', checkinput);
        $pagesize = $this->bconfig['front_list_pagesize'];
        $data['sid_id'] = $this->bsid_id;
        $data['size'] = $pagesize;
        if ($tag) {
            $data['tag'] = $tag;
            $tagname = getkeyname($tag, 'Goodstag');
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
