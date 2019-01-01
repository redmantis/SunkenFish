<?php

namespace Api\Controller;

use Think\Controller;

class IndexController extends BaseController {

    public function index() {
//        $Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
//        $area = $Ip->getlocation('203.34.5.66');
        $f = $_FILES;
        $kx = getallheaders();
        $key = maketoken($kx['Xkey']);
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 5 * 1024 * 1024 * 1024; // 设置附件上传大小 5G
        $upload->exts = null; // 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = I('post.title'); // 设置附件上传（子）目录
        $upload->autoSub = true;
//        $upload->saveName = null;
        $upload->replace = true;
        // 上传文件 
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            echo $upload->getError();
        }
        echo json_encode($info);
    } 

    function uploadimage() {
        echo $this->upld('uploadimage');
    }

    function uploadscrawl() {
        echo $this->upld('uploadscrawl');
    }

    function uploadvideo() {
        echo $this->upld('uploadvideo');
    }

    function uploadfile() {     
        echo $this->upld('uploadfile');
    }

    /**
     * 远程抓取图片
     */
    function catchimage() {
        set_time_limit(0);

        $CONFIG = $this->xconfg;
        /* 上传配置 */
        $config = array(
            "pathFormat" => $CONFIG['catcherPathFormat'],
            "maxSize" => $CONFIG['catcherMaxSize'],
            "allowFiles" => $CONFIG['catcherAllowFiles'],
            "oriName" => "remote.png"
        );
      
        $fieldName = $CONFIG['catcherFieldName'];      
        if ($config['urlPrefix'] == '/')
            $config['urlPrefix'] = '';
        $config['pathFormat'] = str_replace('###', $this->sid_dir, $config['pathFormat']);
     
        /* 抓取远程图片 */
        $list = array();
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {
            $item = new \Org\Net\Uploader($imgUrl, $config, "remote");
//            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, array(
                "state" => $info["state"],
                "url" => $info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source" => htmlspecialchars($imgUrl)
            ));
        }        

        /* 返回抓取数据 */
        echo json_encode(array(
            'state' => count($list) ? 'SUCCESS' : 'ERROR',
            'list' => $list
        ));
    }

    function upld($acton) {
        
        $upinfo=  $this->upinfo;
        $base64 = "upload";
        $CONFIG = $this->xconfg;
        switch ($acton) {
            case 'uploadimage':
                $upinfo['filetype'] = 1;
                $config = array(
                    "pathFormat" => $CONFIG['imagePathFormat'],
                    "maxSize" =>$CONFIG['imageMaxSize'],
                    "allowFiles" => $CONFIG['imageAllowFiles'],
                    "urlPrefix" => C('IMAGESEVRICE') . '/',
                );
                $fieldName = $CONFIG['imageFieldName'];
                break;
            case 'uploadscrawl':
                $upinfo['filetype'] = 1;
                $config = array(
                    "pathFormat" => $CONFIG['scrawlPathFormat'],
                    "maxSize" => $CONFIG['scrawlMaxSize'],
                    "allowFiles" => $CONFIG['scrawlAllowFiles'],
                    "oriName" => "scrawl.png",
                    "urlPrefix" => C('IMAGESEVRICE') . '/',
                );
                $fieldName = $CONFIG['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $upinfo['filetype'] = 2;
                $config = array(
                    "pathFormat" => $CONFIG['videoPathFormat'],
                    "maxSize" => $CONFIG['videoMaxSize'],
                    "allowFiles" => $CONFIG['videoAllowFiles'],
                    "urlPrefix" => C('IMAGESEVRICE') . '/',
                );
                $fieldName = $CONFIG['videoFieldName'];
                break;
            case 'uploadfile':
                $upinfo['filetype'] = 3;
                $config = array(
                    "pathFormat" => $CONFIG['filePathFormat'],
                    "maxSize" => $CONFIG['fileMaxSize'],
                    "allowFiles" => $CONFIG['fileAllowFiles'],
                    "urlPrefix" => C('IMAGESEVRICE') . '/',
                );
                $fieldName = $CONFIG['fileFieldName'];
                break;
            default:
                $upinfo['filetype'] = 4;
                $config = array(
                    "pathFormat" => $CONFIG['filePathFormat'],
                    "maxSize" => $CONFIG['fileMaxSize'],
                    "allowFiles" => $CONFIG['fileAllowFiles'],
                    "urlPrefix" => C('IMAGESEVRICE') . '/',
                );
                $fieldName = $CONFIG['fileFieldName'];
                break;
        }
        
        if($config['urlPrefix']=='/') $config['urlPrefix']='';
        $config['pathFormat'] = str_replace('###', $this->sid_dir, $config['pathFormat']);
        /* 生成上传实例对象并完成上传 */
        $up = new \Org\Net\Uploader($fieldName, $config, $base64);
        
        $upfinfo = $up->getFileInfo();
        $db = new \Common\Model\UpFileModel();
        $upinfo['updmd'] = CONTROLLER_NAME . '/' . ACTION_NAME;
        if ($upfinfo['state'] == 'SUCCESS') {
            $upinfo['filesize'] = intval($upfinfo['size'] / 1024);
            $upinfo['filepath'] = $upfinfo['path'];
            $upinfo['filename'] = $upfinfo['original'];

            $upinfo['userid'] = $this->curuser['userid'];
            $upinfo['usertype'] = $this->curuser['usertype'];
            $upinfo['sid_id'] = $this->curuser['sid_id'];
            $db->addnew($upinfo);
        }



        // new Uploader($fieldName, $config, $base64);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址(带前缀）
         *     "path" => "",           //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */
        /* 返回数据 */
       // return '{"state":"SUCCESS","url":"\/Uploads\/image\/xintong\/201608\/1471409183208441.png","path":"\/Uploads\/image\/xintong\/201608\/1471409183208441.png","title":"1471409183208441.png","original":"about-img02.png","type":".png","size":73285}';
        return json_encode($up->getFileInfo(),JSON_UNESCAPED_SLASHES);
    }
    
    /* 列出图片 */

    function listimage() {
        $CONFIG = $this->xconfg;
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
        $urlPrefix = $CONFIG['imageManagerUrlPrefix'];

        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = I('size', 0, intval);
        $size = $size == 0 ? $listSize : $size;
        $start = I('start', 0, intval);
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "" : "/") . $path;
        $files = $this->getfiles($path, $allowFiles, $urlPrefix);
        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }

        /* 返回数据 */
        $result = json_encode(array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ));

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    function getfiles($path, $allowFiles, $urlPrefix, &$files = array()) {
        if (!is_dir($path))
            return null;
        if (substr($path, strlen($path) - 1) != '/')
            $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $urlPrefix, $files);
                } else {
                    if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                        $files[] = array(
                            'url' => $urlPrefix . substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime' => filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }
}
