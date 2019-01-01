<?php

/*
 * 采集小说信息
 */

/**
 * 采集小说信息
 * @param type $url
 * @param type $remote  采集远程封面图片
 * @param type $base
 * @return int
 */
function getbookinfo($url, $remote = 1, $base = "https://www.37zw.net") {
    import("Org.Util.simple_html_dom");
    $html = new \simple_html_dom();
    $result = get_html_byurl($base . $url);
    $html->load($result);

    $model = array();
    $thumb = $html->find('#fmimg img', 0)->src;
    if ($remote) {
        $sp = new \Org\Util\Spider();
        $rs = $sp->downloadImage($base . $thumb);
        $model['thumb'] = $rs['path'];
    }
    $model['remotethumb'] = $base . $thumb;

    $info = $html->find("#info p", 0)->plaintext;
    $info = explode('：', $info);
    if (empty($info[1])) {
        $model['author'] = "";
    } else {
        $model['author'] = $info[1];
    }

    $intro = $html->find("#intro p", 0)->plaintext;
    $model['info'] = $intro;

    $con_top = $html->find(".con_top", 0)->plaintext;
    $con_top = explode(' ', $con_top);

    switch (trim($con_top[4])) {
        case "玄幻小说":
            $cat_id = 1;
            break;
        case "修真小说":
            $cat_id = 2;
            break;
        case "都市小说":
            $cat_id = 3;
            break;
        case "穿越小说":
            $cat_id = 4;
            break;
        case "网游小说":
            $cat_id = 5;
            break;
        case "科幻小说":
            $cat_id = 6;
            break;
        default :
            $cat_id = 0;
            break;
    }
    $model['cat_id'] = $cat_id;
    $html->clear();
    return $model;
}

/**
 * 读取章节内容
 * @param type $url
 * @return type
 */
function getchapter($url) {
    import("Org.Util.simple_html_dom");
    $html = new \simple_html_dom();
    $result = get_html_byurl($url);
    $html->load($result);
    $model = array();
    $model['title'] = $html->find('.bookname h1', 0)->plaintext;
    $content = $html->find("#content", 0)->outertext;   //outertext |innertext
    $content = preg_replace('/[&nbsp;]{4}/', '', $content);
    $model['content'] = $content;
    $html->clear();
    return $model;
}

/**
 * 读取章节列表
 */
function getchapterlist($url, $pid = '', $pindex = 0) {
    import("Org.Util.simple_html_dom");
    $html = new \simple_html_dom();
    $result = get_html_byurl($url);
    $html->load($result);
    $list0 = $html->find('#list', 0)->children(0);
    $x = $list0->children();
    $list = [];
    $cstart = false;
    foreach ($x as $k => $v) {
        $tag = $v->tag;
        $title = $v->plaintext;
        $cpurl = "";
        if ($tag === 'dd') {
            $link = $v->children(0);
            $cpurl = $link->href;
            $cpurl = trim($cpurl);
            $ischapter = 0;
        } else {
            $ischapter = 1;
        }

        if ($pindex == 0) {
            $cstart = true;
        } elseif ($pindex == $k) {
            $cstart = true;
            continue;
        }

        if ($cstart) {
            $model = ['url' => $url . $cpurl, 'pid' => $cpurl, 'pindex' => $k, "title" => $title, 'ischapter' => $ischapter];
            $list[] = $model;
        }
    }
    $html->clear();
    return $list;
}

/**
 * 获取网页资料
 * @param type $url
 * @return type
 */
function get_html_byurl($url) {
    do {
        $sp = new \Extend\Snoopy();
        $sp->fetch($url);
        $result = $sp->results;
        if (empty($result)) {
            $i++;
            echo $i . "\r\n";
        } else {
            return $result;
        }
    } while ($i < 3);
}

/**
 * 
 * @param type $sp 对应线程序号
 * @param type $div 分割线程数
 */
function allcap($sp = 0, $div = 5, $baseurl = "https://www.37zw.net") {
    ignore_user_abort();
    // 取消脚本运行时间的超时上限
    set_time_limit(0);

    $db = new \Common\Book\BkBookModel();
    $ac = new \Common\Book\BkArticleModel();
    $list = $db->getlist(['comment_count' => 0]);
    $qcos = new \Extend\Qcloudcos(null);

    foreach ($list as $k => $v) {

        if ($v['id'] % $div === $sp) {
            $str = "开始采集《{$v['title']}》章节{$v['id']}……\r\n";
            outputstr($str, $sp);
            $db->where(['id' => $v['id']])->setField('posttime', time());
            $cplist = getchapterlist($baseurl . $v['url'], $v['pid'], $v['pindex']);
            $bkpath = getgrouppath($v['id']);
            $path = "/mydata/book/cap{$bkpath}/{$v['id']}";
            $fu = new \Org\Util\FileUtil($path);

            $fu->createDir($path);
            file_put_contents("{$path}/index.html", "<title>{$v['title']}</title>" . PHP_EOL, FILE_APPEND);

            foreach ($cplist as $key => $cpv) {
                if ($cpv['ischapter']) {
                    $lang['title'] = $cpv['title'];
                    $lang['content'] = "";
                } else {
                    $lang = getchapter($cpv['url']);
                }
                $content = $lang['content'];
                $lang['content'] = "";

                unset($v['title']);
                if (empty($lang['title'])) {
                    $lang['title'] = $cpv['url'];
                }
                $base['bookid'] = $v["id"];
                $base['url'] = $cpv['url'];
                $base['hits'] = 0;
                $base['	tags'] = "";
                $base['ischapter'] = $cpv['ischapter'];
                $model = ['base' => $base, 'cn' => $lang];
                $ars = $ac->addnew($model);

                file_put_contents("{$path}/{$ars['id']}.html", $content);
                if ($cpv['ischapter']) {
                    file_put_contents("{$path}/index.html", "<dt><a href='{$ars['id']}.html'>{$cpv['title']}</a></dt>" . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents("{$path}/index.html", "<dd><a href='{$ars['id']}.html'>{$lang['title']}</a></dd>" . PHP_EOL, FILE_APPEND);
                }

//                $qcos->putObject("{$path}/{$ars['id']}.html", $content);

                $data = ['pid' => $cpv['pid'], 'pindex' => $cpv['pindex'], 'updatetime' => time()];
                $db->where(['id' => $v['id']])->save($data);
                file_put_contents("{$path}/pindex.txt", $cpv['pindex']);
                outputstr("{$cpv['pid']}-{$cpv['pindex']}-{$cpv['title']}-{$cpv['ischapter']}-{$v['id']}\r\n", $sp);
            }
            $db->where(['id' => $v['id']])->setField("comment_count", 1);
            outputstr("采集结束\n", $sp);
        } else {
            continue;
        }
    }
}

/**
 * 
 * @param type $sp 对应线程序号
 * @param type $div 分割线程数
 */
function siglcap($sp = 5, $baseurl = "https://www.37zw.net") {
    ignore_user_abort();
    // 取消脚本运行时间的超时上限
    set_time_limit(0);

    $str = "开始采集《》章节……\r\n";
    outputstr($str, $sp);

    $cplist = getchapterlist($baseurl . "/3/3628/", '', 0);

    $path = "/mydata/book/cap";
    $fu = new \Org\Util\FileUtil($path);
    $fu->createDir($path);
      file_put_contents("{$path}/nt.txt", '' . PHP_EOL);
    foreach ($cplist as $key => $cpv) {
        if ($cpv['ischapter']) {
            $lang['title'] = $cpv['title'];
            $lang['content'] = "";
        } else {
            $lang = getchapter($cpv['url']);
        }
        $content = $lang['content'];
        $lang['content'] = "";
        if ($cpv['ischapter']) {
            file_put_contents("{$path}/nt.txt", $cpv['title'] . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents("{$path}/nt.txt", $lang['title'] . PHP_EOL, FILE_APPEND);
        }
         file_put_contents("{$path}/nt.txt", PHP_EOL, FILE_APPEND);
        write_content($content, "{$path}/nt.txt");

        outputstr("{$key}-{$cpv['title']}\r\n", $sp);
    }
    outputstr("采集结束\n", $sp);
}

/**
 * 
 * @param type $sp 对应线程序号
 * @param type $div 分割线程数
 */
function bqcap($sp = 5, $baseurl = "http://www.biquge.com.tw") {
    ignore_user_abort();
    // 取消脚本运行时间的超时上限
    set_time_limit(0);

    $str = "开始采集《》章节……\r\n";
    outputstr($str, $sp);

    $cplist = getchapterlist($baseurl . "/5_5872/", '', 0);

    $path = "/mydata/book/cap";
    $fu = new \Org\Util\FileUtil($path);
    $fu->createDir($path);
      file_put_contents("{$path}/nt.txt", '' . PHP_EOL);
    foreach ($cplist as $key => $cpv) {
        if ($cpv['ischapter']) {
            $lang['title'] = $cpv['title'];
            $lang['content'] = "";
        } else {
            $lang = getchapter($cpv['url']);
        }
        $content = $lang['content'];
        $content = preg_replace('/xiǎo/', '小', $content);
        $content = preg_replace('/diǎn/', '点', $content);
        $content = preg_replace('/dǐng/', '顶', $content);
        
        $lang['content'] = "";
        if ($cpv['ischapter']) {
            file_put_contents("{$path}/nt.txt", $cpv['title'] . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents("{$path}/nt.txt", $lang['title'] . PHP_EOL, FILE_APPEND);
        }
         file_put_contents("{$path}/nt.txt", PHP_EOL, FILE_APPEND);
        write_content($content, "{$path}/nt.txt");

        outputstr("{$key}-{$cpv['title']}\r\n", $sp);
    }
    outputstr("采集结束\n", $sp);
}

/**
 * 
 * @param type $sp 对应线程序号
 * @param type $div 分割线程数
 */
function testallcap($sp = 0, $div = 4, $baseurl = "https://www.37zw.net") {
    $db = new \Common\Book\BkBookModel();
    $ac = new \Common\Book\BkArticleModel();
    $list = $db->getlist(['id' => 1]);

    foreach ($list as $k => $v) {

        $str = "开始采集《{$v['title']}》章节{$v['id']}……\r\n";
        outputstr($str);
        $cplist = getchapterlist($baseurl . $v['url'], '', 0);
        foreach ($cplist as $key => $cpv) {
            if ($cpv['ischapter']) {
                $lang['title'] = $cpv['title'];
            } else {
                // $lang = getchapter($cpv['url']);
            }
            unset($v['title']);
            if (empty($lang['title'])) {
                $lang['title'] = $cpv['url'];
            }
            $base['bookid'] = $v["id"];
            $base['url'] = $cpv['url'];
            $base['ischapter'] = $cpv['ischapter'];
            $model = ['base' => $base, 'cn' => $lang];

            outputstr("{$cpv['pid']}-{$cpv['pindex']}-{$cpv['title']}-{$cpv['ischapter']}-{$v['id']}\r\n");
        }
        outputstr("采集结束\n");
    }
}

/**
 * 控制台输出中文
 * @param type $str
 */
function outputstr($str, $p) {
    if (!file_exists("out{$p}.txt")) {
        if (PHP_OS == 'WINNT') {
            echo iconv('UTF-8', 'GB2312', $str);
        } else {
            echo $str;
        }
    }
}

/**
 * 读取文章内容
 * @param type $bookid
 * @param type $id
 * @return type
 */
function read_content($bookid, $id) {
    $bkpath = getgrouppath($bookid);
    $file_path = "/mydata/book/cap{$bkpath}/$bookid/{$id}.html";
//    $file_path = "/mydata/book/{$bookid}/{$id}.html";
    $str = "";
    if (file_exists($file_path)) {
        $str = file_get_contents($file_path); //将整个文件内容读入到一个字符串中    
    }

    if (preg_match('/[(www.37zw.com)|(www.37zw.net)]/u', $str) > 0) {
        $str = preg_replace('/([^br])+$/', '</div>', $str);
    }
    $str = str_replace('<div id="content">', "", $str);
    $str = str_replace('</div>', "", $str);
    $arr = explode("<br />", $str);
    $str = "";
    foreach ($arr as $v) {
        $s = trim($v);
        if (!empty($s)) {
            $str .= "<p>{$s}</p>";
        }
    }
    return $str;
}

/**
 * 读取文章内容
 * @param type $bookid
 * @param type $id
 * @return type
 */
function write_content($str, $path) {
    if (preg_match('/[(www.37zw.com)|(www.37zw.net)]/u', $str) > 0) {
        $str = preg_replace('/([^br])+$/', '</div>', $str);
    }
    $str = str_replace('<div id="content">', "", $str);
    $str = str_replace('</div>', "", $str);
    $arr = explode("<br />", $str);

    foreach ($arr as $v) {
        $s = trim($v);
        if (!empty($s)) {
            $s = str_replace('<br', "", $s);
            file_put_contents($path, $s . PHP_EOL, FILE_APPEND);
        }
    }
}

function getgrouppath($bookid) {
    $i = floor($bookid / 1000);
    if ($i < 1) {
        return "";
    } else {
        return $i;
    }
}
