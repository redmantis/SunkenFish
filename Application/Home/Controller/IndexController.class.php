<?php

/*
 * 字符：UTF-8
 * 邮件: feiyufly001@hotmail.com
 * 创建：redmantis <默鱼 at feiyufly001@hotmail.com> 2016-8-4 20:39:17
 * 最终：Rdm
 */

namespace Home\Controller;

use Think\Log;
use Think\Controller;
use Common\Model;
use Common\Flmodel;
use Common\Zfmodel;

class IndexController extends BaseController {

    public $wechat;
    public $weixin;
    public $wechatauth;

     public function _empty() {

        $pagemod = ACTION_NAME;
        $pagemod = strtolower($pagemod);
        if (checksafepath($pagemod))
            $this->redirect('Common/error');

        $subinfo = $this->bsubsid;
        $db = new \Common\News\NewsSingleModel();

        $colmodel = $db->getmodelbypath(['viewpath' => $pagemod, 'sid_id' => $this->bsid_id]); //取得栏目的详细资料 

        if (empty($colmodel)) {
            die("页面不存在");
        }
        $this->assign('model', $colmodel);

        $viewmod = empty($colmodel['viewmod']) ? $colmodel['viewpath'] : $colmodel['viewmod']; //显示模板

        $colpath = $db->getParentPath($colmodel['id']);

        /* 面包屑 */
        $mbx = [];

        $groupmark = $pagemod;
        foreach ($colpath as $v) {
            $title = clearpre($v['title']);
            array_unshift($this->titlelist, $title);
            $mbx[] = [
                'viewpath' => $v['viewpath'],
                'title' => $title,
                'shorttitle' => $v['shorttitle'],
            ];
//             array_unshift($mbx, $mx);
            if (!empty(trim($v['groupmark']))) {
                $groupmark = $v['groupmark'];
            }
        }

        $this->assign('mbx', $mbx);
        $this->assign('pagemod', $pagemod);
        $this->assign('groupmark', $groupmark); //当前导航标记

        $pmap = array('sid_id' => $this->bsid_id);
        $pmap['groupmark'] = $groupmark;
        $pmap['parentid'] = 0;
        $leftmenu = creatMenu($this->bsid_id, 1, $pmap);
        $this->assign('leftmenu', $leftmenu[0]);

        $xmap = array('sid_id' => $this->bsid_id);
        $xmap['istopic'] = $colmodel['istopic'];
        $xmap['isshow'] = 1;
//        $xmap['shownav'] = array('in', '1,2');

        reset($colpath);
        $rootcol = current($colpath);

        $coltree = $db->getTree2($xmap, ['id' => $rootcol['id']], 1);

        $this->assign('roottree', $coltree); //全栏目树       

        $sontree = $db->getTree2($xmap, ['id' => $colmodel['id']]);
        $this->assign('sontree', $sontree); //子栏目树           


        if (count($sontree) > 0) {//当前栏目为根栏目且包含子栏目时
            $this->bcolums = current($sontree); //当前栏目默认为第一个子栏目
        } else {
            $this->bcolums = $colmodel; //当前栏目默认为第一个子栏目
        }

        $this->subcollist = $db->getTree2($xmap, ['id' => $this->bcolums['id']]);

        $this->subcolid = $db->getsublistid($xmap, $this->bcolums['id']);

        if ($this->bcolums['parentid']) {
            $parentcol = $db->getmodelbyid($this->bcolums['parentid']);
        }

        $this->assign('rootcol', $rootcol);
        $this->assign('parentcol', $parentcol); //父栏目
        $this->assign('bcolums', $this->bcolums); //当前栏目        
        $this->assign('parentpath', $colpath); //当前栏目路径
        $this->assign('coltree', $coltree); //全栏目树

        $this->makemeta([clearpre($this->bcolums['title'])], $this->titlelist, array($this->bcolums['summary']));
        $this->display($viewmod);
    }

    public function index() {
        $data['sid_id'] = $this->bsid_id;
        $data['position_id'] = 1;
        $rs = common_data_readapi('get_adlist', $data);
        $this->assign('adlb', $rs['list']);
        
        $data['sid_id']=  $this->bsid_id;
        $data['size'] = 3;
        $data['ishot'] = 1;
        $data['skip']=0;
        $data['collist']='351';
        $lb = $rs = common_data_readapi('get_newslist', $data);
        $this->assign('hotlist', $lb['list']);
        
        $data['collist']='363';
        unset($data['collist']);
        $data['size'] =10;
        $lb = $rs = common_data_readapi('get_newslist', $data);
        $this->assign('vclist', $lb['list']);

        $dbn = new \Common\News\NewsSingleModel();      
        $aboutus = $dbn->getmodelbypath(['viewpath'=>'aboutus','sid_id'=> $this->bsid_id]);
        $this->assign('aboutus', $aboutus);
        $this->makemeta(); //关键字
        
        $bk = new \Common\Book\BkBookModel();
        $bl = $bk->get_list(['comment_count' => 1], 14);
        $this->assign("bl",$bl['list']);        
        $this->assign("reclist",$bl['list']);
        
        $bl = $bk->get_list(['sortstr' => 10], 14);
        $this->assign("updatelist",$bl['list']);
        
        $bl = $bk->get_list(['sortstr' => 20], 14);
        $this->assign("newestlist",$bl['list']);
        $this->display();
    }
  
 
    /**
     * 设置皮肤
     * @param type $theme
     */
    public function settheme($theme) {
        cookie(C('DEFAULT_THEME_NAME'), $theme);
        $ref = I('server.HTTP_REFERER');
        $this->success('皮肤切换完成', $ref);
    }

    /**
     * 语音转换
     * @param type $text
     * @param string $path
     */
    function aip($text, $path, $type, $id) {

        $app_id = $this->bconfig['aip_app_id'];
        $api_key = $this->bconfig['aip_api_key'];
        $secret_key = $this->bconfig['aip_secret_key'];
        $val = $this->bconfig['aip_vol']; //语速
        $per = $this->bconfig['aip_per']; //发音人
        $pit = $this->bconfig['aip_pit']; //语调
        $spd = $this->bconfig['aip_spd']; //语速

        $basepath = C("AIPSAVEPATH." . $type);
        $savepath = "{$basepath}{$id}/{$path}-{$per}-{$pit}-{$val}-{$spd}.mp3";
        if (file_exists("./" . $savepath)) {
            $this->ajaxReturn(array('status' => 1, 'path' => $savepath));
        } else {
            $aipSpeech = new \Org\Util\AipSpeech($app_id, $api_key, $secret_key);
            $result = $aipSpeech->synthesis($text, 'zh', 1, array(
                'vol' => $this->bconfig['aip_vol'],
                'per' => $this->bconfig['aip_per'],
                'pit' => $this->bconfig['aip_pit'],
                'spd' => $this->bconfig['aip_spd'],
            ));

            $fu = new \Org\Util\FileUtil();
            $fu->createDir("{$basepath}{$id}");
            // 识别正确返回语音二进制 错误则返回json 参照下面错误码
            if (!is_array($result)) {
                file_put_contents("./" . $savepath, $result);
                $this->ajaxReturn(array('status' => 1, 'path' => $savepath));
            } else {
                echo $result;
            }
        }
    }

    public function about() {
        $dbn = new \Common\News\NewsSingleModel();
        $model = $dbn->getmodelbypath(['viewpath' => 'about', 'sid_id' => $this->bsid_id]);
        $this->assign('model', $model);

        $atitle[] = $model['title'];
        $keyword[] = $model['keyword'];
        $this->makemeta($atitle, $keyword, $desc);
        $this->assign('groupmark', "about");
        $this->display();
    }

    public function contact() {
        $this->assign('groupmark', "contact");
        $this->display();
    }

}
