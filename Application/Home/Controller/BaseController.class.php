<?php
namespace Home\Controller;
use Think\Controller;
use Common\Model;
use Common\ViewModel;

/**
 * 如果某个控制器必须用户登录才可以访问  
 * 请继承该控制器
 */
class BaseController extends Controller {  

//    public function _empty() {
//        $this->redirect('Common/error');
//    }

    public $bsubsid;                             //当前站点信息
    /**
     *当前站点ID
     * @var type 
     */
    public $bsid_id;                            //当前站点ID
    public $basemap;                            //数据查询的基础条件
    public $bconfig;                            //网站基本配置
    public $templatepage;                       //当前显示模板
    public $bcolums;                            //当前栏目
    public $bparentcolums;                      //上缓栏目资料  
    public $iswexin;                            //微信浏览器标识
    public $userId;                             //用户ID
    public $userInfo;                           //用户基本资料  
    public $languelist;
    public $curentctiy;                         //当前城市
    public $pgsize;

    protected function _initialize() { 
        set_csp_header();       
        $siturl = $_SERVER['HTTP_HOST'];
//        $protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";        
//        if (count(explode('.',$siturl))==2) {
//            header('Location: ' . $protocol . 'www.' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//            exit;
//        } 
        $subsib = new Model\SubsidiaryModel();
        $subinfo = $subsib->getmodebysite($siturl); //根据域名取得站点信息         
        if (!$subinfo) {           
            $this->redirect('Common/nosite');
        }  
        
        $curentlangue = I(C('LANGUE_TAG'), '');
        $this->assign('curent_langue',$curentlangue);
       
        $this->bsubsid = $subinfo;
        $this->bsid_id = $subinfo['sid_id'];
   

        $this->basemap['sid_id'] = $this->bsid_id;
        $this->assign('csidid', $this->bsid_id);


        /* 取得当前站点基本配置 */       
        $baseconfig = getConfig('', $this->bsid_id, 'baseinfo');
        $this->bconfig = $baseconfig;
        $this->pgsize = $baseconfig['front_list_pagesize'];

        $sysconfig=  getConfig();
        $this->assign('sysconfig', $sysconfig);

        $languetable = getAttrsElementList("Langue");
        $languelist = array();
        foreach ($languetable as $k => $v) {
            $languelist[$v['tagvalue']] = $v;
        }

        $langdata = getLangueInfo();
        $this->assign('languelist', $languelist);
        $this->languelist = $languelist;
        $this->assign('curentlang', $languelist[$langdata['curent_lang']]);


        
        $this->assign('bconfig', $this->bconfig);
        $this->assign('site_title',  $this->bconfig['web_title']);
        $this->assign('site_keyword',  $this->bconfig['web_keyword']);
        $this->assign('site_description',  $this->bconfig['web_description']);
        $this->assign('target_blank',$baseconfig['target_blank']);//从新页面打开
        
        /*主页导航*/
        $pcnav=  creatMenu($this->bsid_id, 1);
        
        $this->assign('pc_site_nav', $pcnav);
      
        
        //检查传入参数的合法性  
        $pagemod = I('mod', 'index'); 
        if(checksafepath($pagemod)) $this->redirect('Common/error');
        $this->assign('pagemod',$pagemod);  
        
        //检查是否微信打开
        $this->iswexin = isWeixin();
        $this->assign("isWexin", $this->iswexin); 
        
        //设置当前导航标记
        $ctrlname=  strtolower(CONTROLLER_NAME);
        $this->assign('groupmark',$ctrlname);
        
        //获取当前语言参数
        $pram = get_langue_parm();
        $this->assign('searchurl',  U('product/index',$pram));
        
        //获取用户登录信息
        $this->userInfo = getuserinfo();
        if ($this->userInfo) {
            $this->userId = $this->userInfo['id'];
            $this->assign("userInfo", $this->userInfo);
        }
        
        $this->assign('np', ''); //用于配合页面查询参数生成  不限项
        $this->assign('xp', 1); //用于配合页面查询参数生成   勾选项    

        $this->creattemplate(CONTROLLER_NAME, ACTION_NAME, $colmodel);
    }
    
    /**
     * 创建模板
     * @param type $contrl
     * @param type $atcion
     * @param type $colmodel  当前栏目
     * @param type $parcolmodel  上级栏目
     */
    public function creattemplate($contrl, $atcion, $colmodel) {
        if(is_null($colmodel)){
            return;
        }       
        $template = gettpl($contrl, $atcion, $colmodel);
     
        switch ($template['isdefault']) {
            case '-1':
                //访问不存在的控制器，转到错误页面
                //$this->redirect('index/index');
                //$this->redirect('Common/error');
                break;
            case '1';
                $this->templatepage = $template['default'];
                //默认模板，不进行处理 
                break;
            default :
                $this->templatepage = $template['default'];
                break;
        }
    }

    /**
     * 生成meta内容
     * @param array $title
     * @param array $keyword
     * @param array $des
     */
    public function makemeta($title = array(), $keyword = array(), $des = array()) {
        $cityname = show_region_name($this->curentctiy, 'city');
        $title[] = $cityname . $this->bconfig['web_title']; //showTagbyMark("site_title", $this->bconfig['web_title'], "site_attr");
        $keyword[] = $this->bconfig['web_keyword']; //showTagbyMark("siet_keywords", $this->bconfig['web_keyword'], "site_attr"); //$this->bconfig['web_keyword'];
        $des[] = $this->bconfig['web_description']; // showTagbyMark("site_description", $this->bconfig['web_description'], "site_attr"); //$this->bconfig['web_description'];
        $this->assign('site_title', implode('-', $title));
        $this->assign('site_keyword', implode(',', $keyword));
        $this->assign('site_description', implode(',', $des));
    }

    public function nodate(){
        $this->error('数据不存在或已经被清除');
    }
    
    public function showmessage($msg,$content="",$url=""){
        $this->assign('msg',$msg);
        $this->assign('url',$url);
        $this->assign('content',$content);
        $this->display('./Public/tpl/showmsg.html');
        die;
    }
}
