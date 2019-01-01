<?php
namespace Admin\Controller;

use Common\Book;

class BooksController extends BaseController {
    public $istopic;
    public $userPower;//用户具备的操作权限
    public $basetable="bk_article";
    public function _initialize() {
        parent::_initialize();

        $suffix = I("suffix", '');
        if ($suffix == '1') {
            $suffix = "";
        }
        $this->basetable = $this->basetable . $suffix;

        /* 组装用户权限 */
        $plist = array('del', 'update', 'add', 'batchoperate');
        $this->getuserpower($plist);
    }

    public function index($key = '') {
        $db = new Book\BkArticleModel(['basetable'=> $this->basetable]);    
        if (IS_POST) {
            $isshow = I('isshow', -1, intval);
            if ($key !== "") {
                $map['title'] = array('like', "%$key%");      
            } 
            $count=$db->getcount($map);
            $Page = new \Extend\Page($count, $this->pagesize);
            $show = $Page->show();
            $rs = $db->getlist($map, $Page->firstRow, $Page->listRows);
            $newrs = showTranceDatabymap($db->ShowMap, $rs, $this->userPower,"Common\Book\BkArticleModel");
            $drs = array('list' => $newrs, 'totalSize' => $count);
            $this->ajaxReturn($drs);
            die;
        }
        $this->assign('showmap', $db->ShowMap);

        $map = getselectmap($this->manageid, $this->manages_id);
        //查询参数面板输出
//        $dataset = C('NEWSRFILTER');        
        $dataset['suffix']=$db->getAllPartition();
        foreach ($dataset as $key => $v) {
            switch ($key) {             
                default:
                    $dx[] = show_list($v, $key, false, I($key));
                    break;
            }
        }  
        $this->assign('searchdata', $dx);
        
        
        $this->display('Commonf/index');
    }

    public function add() {
        $viewmodpannel="NOTEPANEL";      
        $dataset = C($viewmodpannel);
        $this->assign('basecard', $dataset['table_card']);
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {
                $value = '';
                switch ($key) {                    
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }     
            $this->assign('setdata', $dl);
            $this->display("Commonf/lang_edit");
        } 
        else {          
            $data = getLangPost(1); 
            $db = new Book\BkArticleModel();   
            $rs = $db->addnew($data);           
            $this->ajaxReturn($rs);
        }
    }

    public function update() {
        $map = I('get.');
        $db = new Book\BkArticleModel();
        $model = $db->getmodel($map);
        $viewmodpannel = "NOTEPANEL";
        $dataset = C($viewmodpannel);
        $this->assign('basecard', $dataset['table_card']);

        if (!IS_POST) {
            $base = $model['base'];
            foreach ($dataset as $key => $v) {
                $value = $model['base'][$key];
                switch ($key) {
                    default:
                        $dl[] = show_list($v, $key, false, $base[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('id', $base['id']);
            $this->assign('lanmodel', $model);
            $this->display("Commonf/lang_edit");
        } else {
            $data = getLangPost(1);
            $db = new Book\BkArticleModel();
            $rs = $db->updater($data);                       
            $this->ajaxReturn($rs);
        }
    }
    
    public function del() {
        $map = I('get.');
        $db = new Book\BkArticleModel();
        $rs = $db->del($map);
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
            $db = new Book\BkArticleModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }
    }

}