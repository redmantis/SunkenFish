<?php

/*
 * 下载远程文件
 */
namespace Org\Util;

/**
 * 下载远程文件
 * @author RDM
 */
class Spider {

    public function downloadImage($url, $path = 'Uploads/remote/') {
        $config = array(
            "pathFormat" => "/Uploads/remote/{yyyy}-{mm}-{dd}/{time}{rand:6}",
            "maxSize" => 51200000,
            "allowFiles" => array(".png", ".jpg", ".jpeg", ".gif", ".bmp"), /* 上传图片格式显示 */
            "oriName" => "remote.png",
            'urlPrefix' => ''
        );
        $item = new \Org\Net\Uploader($url, $config, "remote");
        $info = $item->getFileInfo();
        return $info;
    }

}
