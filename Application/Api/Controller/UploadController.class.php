<?php

namespace Api\Controller;

use Think\Controller;

class UploadController extends BaseController {
    public $sid_id;
    public $sid_dir;
    public $upload;
    public $rootpath;
    public function _initialize() {
        parent::_initialize();
        
        $rs = testenable_writing();
        if ($rs['status'] !== 0) {
            $rs['msg'] = showTagbyMark($rs['msg']);
            $this->ajaxReturn($rs);
            die;
        }

        $userinfo = $this->curuser;    
       
        if ($userinfo['sid_id']) {
            $db = new \Common\Model\SubsidiaryModel();
            $model = $db->getmodebyid($userinfo['sid_id']);
            $this->sid_dir = $model['sid_dir'];
        } else {            
            $data['status'] = 20000007;
            $data['code']=-1;
            $data['msg'] = showTagbyMark('SafetyFailed');            
            $this->ajaxReturn($data, "JSON");
            die;
        }
        
        $this->upinfo['userid'] = $this->curuser['userid'];
        $this->upinfo['usertype'] = $this->curuser['usertype'];
        $this->upinfo['sid_id'] = $this->curuser['sid_id'];

        $upload = new \Think\Upload(); // 实例化上传类
        $upload->savePath = $this->sid_dir . '/'; // 设置附件上传（子）目录
        $upload->autoSub = true; 
        
        switch (strtolower(ACTION_NAME)) {// 设置附件上传根目录      
            case "upload":
                $upload->rootPath = './' . C('IMAGEROOTPATH') . '/';
                $this->rootpath= C('IMAGEROOTPATH');
                break;
            case "uploadvideo":
                $upload->rootPath = './' . C('VIDEOROOTPATH') . '/';
                $this->rootpath= C('VIDEOROOTPATH');
                break;         
            case "uploadfile":
                $upload->rootPath = './' . C('FILEROOTPATH') . '/';
                $this->rootpath= C('FILEROOTPATH');
                break;
        }
        $tp=I('post.type');
        if($tp==UploadFileModSys){
             $upload->rootPath = './' . C('SYSIMAGESEVURL') . '/';  
             $this->rootpath= C('SYSIMAGESEVURL');
        }
        
        $this->upload = $upload;
    }

    /*上传图片*/
    public function upload() {

        $upload = $this->upload; // 实例化上传类
        $upload->maxSize = 1 * 1024 * 1024 * 1024; // 设置附件上传大小
        $upload->exts = C('UPLOADTYPE.pic'); // 设置附件上传类型
        // 上传文件 
        $infolist = $upload->upload();

        $info = $infolist['file'];
        if (!$info) {// 上传错误提示错误信息      
            $data['status'] = 30000001;
            $data['code']=-1;
            $data['msg'] = $upload->getError();
            echo json_encode($data);
            die();
        }

        $db = new \Common\Model\UpFileModel();
     
        $this->upinfo['filetype'] = 1;
        $this->upinfo['filesize'] = intval($info['size'] / 1024);
        $this->upinfo['filepath'] =  $this->rootpath . $info['savepath'] . $info['savename'];
        $this->upinfo['filename'] = $info['name'];
        $this->upinfo['updmd']=CONTROLLER_NAME.'/'.ACTION_NAME;
        $db->addnew( $this->upinfo);

        $data['status'] = 0;
        $data['name'] = $info['name'];
        $data['size'] = intval($info['size'] / 1024);
        $data['pic'] = $info['savepath'] . $info['savename'];
        $data['abspic'] =  $this->rootpath . $info['savepath'] . $info['savename'];  
        
        /**
         * 兼容layedit上传要求
         */
        $data['code']=0;
        $data['data']=array('src'=>$data['abspic'],'title'=> $data['name']);

        $this->ajaxReturn($data);
    }

    /* 上传视频 */
/*
    public function uploadvideo() {
        $upload = $this->upload; // 实例化上传类
        $upload->maxSize = 1 * 1024 * 1024 * 1024; // 设置附件上传大小
        $upload->exts = C('UPLOADTYPE.video'); //array('wmv','avi','mp4'); // 设置附件上传类型
        // 上传文件 
        $infolist = $upload->upload();
        $info = $infolist['myvideo'];
        if (!$info) {// 上传错误提示错误信息      
            $data['status'] = -1;
            $data['msg'] = $upload->getError();
            echo json_encode($data);
            die();
        }

        $data['status'] = 0;
        $data['name'] = $info['name'];
        $data['size'] = intval($info['size'] / 1024);
        $data['pic'] = $info['savepath'] . $info['savename'];
        $data['abspic'] = C('VIDEOSEVURL') . $info['savepath'] . $info['savename'];
        echo json_encode($data);
        die();
    }

  

    public function uploadfile() {
        $upload = $this->upload; // 实例化上传类
        $upload->maxSize = 1 * 1024 * 1024 * 1024; // 设置附件上传大小
        $upload->exts = C('UPLOADTYPE.file'); //array('rar', 'txt','doc','docx','pdf','xls','xlsx','zip','exe','crd','ceb'); // 设置附件上传类型
        // 上传文件 
        $infolist = $upload->upload();
        $info = $infolist['file'];
        if (!$info) {// 上传错误提示错误信息      
            $data['status'] = -1;
            $data['msg'] = $upload->getError();
            echo json_encode($data);
            die();
        }

        $data['status'] = 0;
        $data['name'] = $info['name'];
        $data['size'] = intval($info['size'] / 1024);
        $data['pic'] = $info['savepath'] . $info['savename'];
        $data['abspic'] = C('FILESEVURL') . $info['savepath'] . $info['savename'];

        echo json_encode($data);
        die();
    }
 * 
 */

    /**
     * 删除上传文件
     */
    public function del() {
        $token =  C('TOKEN_ON');
        C('TOKEN_ON', true);
        $db = new \Common\Model\UpFileModel();
        $userid = 0;
        if ($this->upinfo['usertype'] == 2) {
            $userid = $this->upinfo['m_id'];
        }

        $f = I('post.filepath');
        $file = '.' . $f;
        $data['status'] = 0;
        $data['msg'] = showTagbyMark('SafetyFailed'); //'文件不存在或权限不足';     
        $db->del($f, $userid);
        if (unlink($file)) {
            $db = new \Common\Zfmodel\ZfPhpotodeatilModel();//如果是相册图片，删除相册记录
            $db->delimg($f);
            $data['msg'] = showTagbyMark('FileDelOk'); //'文件删除成功';
        } else {
            $data['msg'] = showTagbyMark('FileDelFailed');
        }
        
        C('TOKEN_ON', $token);
        $this->ajaxReturn($data);
    }
    
    /**
     * 裁剪图片
     */
    public function cutimg() {
        $data = I('post.');
        $image = new \Think\Image();
        $image->open("." . $data['filepath']);
        $rs = $image->crop($data['width'], $data['height'], $data['x'], $data['y'])->save("." . $data['filepath'], NULL, 100);

        $rsd['status'] = 0;
        $rsd['rs'] = $rs;
        $this->ajaxReturn($rsd);
    }
}
