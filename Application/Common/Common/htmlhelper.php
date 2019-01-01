<?php
/**
 * Description of htmlcontroller
 * HTML控件助手
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */



/**
 * 多图上传
    'des' => '相册',
    'tableindex' => 'photo' . $k,
    'model' => 'mimage',
    'readonly' => 2,        //只读模式 0，2
    'inputtype'=>'hidden',  //是否显示输入框
    'value' => '' 
 * 
 */

/**
 * 单图上传
    'des' => '相册',
    'tableindex' => 'photo' . $k,
    'model' => 'mimage',
    'readonly' => 2,        //只读模式 0，1，2
    'inputtype'=>'hidden',  //是否显示输入框
    'value' => '' 
 * 
 */


class htmlhelper {
    /**
     * 生成html控件
     * @param type $model   字段配置
     * @param type $name    字段名
     * @param type $ishidden   是否隐藏
     * @param type $cuentval   字段值
     * @param type $map        辅助条件（上下文）
     * @param type $cssclass   传入样式表 
     * @return type
     */ 
    static function make_htmlcontorl($model, $name, $ishidden = false, $cuentval = '', $map = '', $cssclass = '') {
        
        $DefaultClass=C('DefaultClass');
        $ext="";
        if($name==='validate_rule'){//忽略验证规则
           return array('title' => '', 'content' => '', 'lineclass' => 'hidden');
        }
        
        $titlecls = isset($model['titlecls']) ? $model['titlecls'] : $DefaultClass["titlecls"];

        //读取标题
        
        if ($model['tableindex'] === 'langue' && !isset($model['titletag'])) {
            $t = explode('__', $name);
            $titletag = $t[1];
        } else {
            $titletag = isset($model['titletag']) ? $model['titletag'] : $name;
        }
        $parentkey = isset($model['parentkey']) ? $model['parentkey'] : 'CommTag';
        $tag = getTagbyMark($titletag, $parentkey, "tagvalue");  
     
        if ($tag) {
            $langTitle = $tag['title']; // 标签  showTagbyMark($titletag, $model['des'], $parentkey, "tagvalue");
            $titledesc = $tag['description']; //提示  showTagbyMark($titletag, '', $parentkey, "tagvalue", 'description');
            $verify = $tag['verify']; //表单验证规则 showTagbyMark($titletag, '', $parentkey, "tagvalue", 'verify');         
            $verifymsg = $tag['verifymsg']; //表单验证消息 showTagbyMark($titletag, '', $parentkey, "tagvalue", 'verifymsg');
            
            $verify = isset($model['verify']) ? $model['verify'] : $verify;
            $verifymsg = isset($model['verifymsg']) ? $model['verifymsg'] : $verifymsg;
            $msg="";
            if (is_array($verifymsg)) {           
                foreach ($verifymsg as $k => $v) {
                    $msg .= "lay-verify-msg-{$k}=\"{$v}\" ";
                }
            } else {
                $msg = "lay-verify-msg=\"{$verifymsg}\"";
            }           
        } else {
            $langTitle = empty($langTitle) ? $model['des'] : $langTitle;
            $langTitle = empty($langTitle) ? $titletag : $langTitle;
        }

        $verifystring = ""; //验证规则
        if (!empty($verify)) {
            $verifystring = "{$msg} lay-vertype=\"tips\" lay-verify=\"{$verify}\"";
        }


        if ($ishidden) {
            $str = "<input type=\"hidden\" name=\"{$name}\" {$verifystring}  id=\"{$name}\" value=\"{$cuentval}\"/>";
            return array('title' => $model['des'], 'content' => $str, 'lineclass' => 'hidden');
        }
        
        $lineclass = '';
        $cuentval = $cuentval !== '' ? $cuentval : $model['value'];

        if (!empty($model['readonly']) && $cuentval !== '')
            $readonly = 'readonly';

        switch ($model['model']) {
            case 'dropdown':
                $str = self::mk_list($model, $name, $cuentval, $map, $cssclass);
                $model['readonly']=2;
                $str2 = self::mk_list($model, $name, $cuentval, $map, $cssclass);
                break;
            case "linkage":
                 $str = self::mk_linkage($model, $name, $cuentval, $map, $cssclass);
                break;
            case "phpotolist":
                $str = self::mk_imglist($model, $name, $cuentval, $map, $cssclass);
                break;
            case "photo":
                $str = self::mk_photo($model, $name, $cuentval, $map, $cssclass);                
                break;
            case 'baidumap'://百度地图
                $str = <<<EOT
                        <div id="allmap" style="zoom:1;position:relative;width: 100%; height:400px; overflow: hidden;">	
                        <div id="map" style="height:100%;-webkit-transition: all 0.5s ease-in-out;transition: all 0.5s ease-in-out;"></div>
                    </div>
EOT;
                break;
             case 'baidumapaddress'://百度地图
                $str = <<<EOT
                      <div class="layui-input-inline">
                      <input type="text" name="{$name}" id="{$name}" $readonly  $style  class="trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]}" value="{$cuentval}" />
                      </div><div class="layui-input-inline">
                      <input type="checkbox" lay-skin="switch" lay-filter="lock{$name}" id="lock{$name}" atr='false' lay-text="解锁|锁定">
                        </div>
EOT;
                break;            
            case 'tree'://生成树型下拉菜单
                $str = self::mk_tree($model, $name, $cuentval, $map, $cssclass);
                break;  
            case 'rdlist':
                $str = self::mk_rdlist($model, $name, $cuentval, $map, $cssclass); 
                break;
            case 'cklist':
                $str = self::mk_cklist($model, $name, $cuentval, $map, $cssclass);
                break;
            case 'attrcklist':
                $str = self::mk_attrcklist($model, $name, $cuentval);
                break;
            case 'star':
                $str = self::mk_start($model, $name, $cuentval);            
                break;
            case 'edit':
                $str = self::mk_edit($model, $name, $cuentval);
                $lineclass = "layui-form-text";
                break;
            case 'image':
                $x=C('UPLOADTYPE.pic');
                $ext = '支持格式：'.  implode(', ',$x);
                $str = self::mk_uploadpic($model, $name, $cuentval,$cssclass);
                $modelx=$model;
                $modelx['readonly']=2;
                $str2 = self::mk_uploadpic($modelx, $name, $cuentval,$cssclass);
                break;
            case 'mimage':
                $x=C('UPLOADTYPE.pic');
                $ext = '支持格式：'.  implode(', ',$x);
                $str = self::mk_muploadpic($model, $name, $cuentval,$cssclass);
                break;
            case 'video':
                 $x=C('UPLOADTYPE.video');
                $ext = '支持格式：'.  implode(', ',$x);
                $str = self::mk_uploadvideo($model, $name, $cuentval);
                break;
            case 'ftpvideo'://  ftp上传视频
                $x=C('UPLOADTYPE.video');
                $ext = '支持格式：'.  implode(', ',$x);
                $str = self::mk_ftpvideo($model, $name, $cuentval);
                break;
            case 'file':
                $x=C('UPLOADTYPE.file');
                $ext = '支持格式：'.  implode(', ',$x);
                //$ext = '支持格式：rar, txt,doc,docx,pdf,xls,xlsx,zip,exe,ceb,crd';
                $str = self::mk_uploadfile($model, $name, $cuentval,$cssclass);
                break;
            case 'input':
                switch ($model['readonly']) {
                    case "1"://带隐藏域的只读
                        $s = htmlspecialchars_decode($cuentval);
                        $str = "<input type=\"hidden\" name=\"{$name}\" id=\"{$name}\" value=\"{$cuentval}\" __VERIFYSTRING__ />  <p class=\"form-control-static\">{$s}</p> ";
                        break;
                    case '2'://不带隐藏域的只读
                        $str = "<p class=\"form-control-static\">{$cuentval}</p> ";
                        break;
                    case '3'://
                        $str = "<input type=\"text\" name=\"{$name}\" id=\"{$name}\" readonly  $style  class=\"trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]}\" value=\"{$cuentval}\" placeholder=\"{$model['placeholder']}\" __VERIFYSTRING__ />";
                        break;
                    default :
                        $str = "<input type=\"text\" name=\"{$name}\" __VERIFYSTRING__ id=\"{$name}\" $readonly  $style  class=\"trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]}\" value=\"{$cuentval}\" placeholder=\"{$model['placeholder']}\" />";
                        break;
                }
                $str2 = "<p class=\"form-control-static\">{$cuentval}</p> ";
                break;
            case "config":
                $tt = getkeyname($cuentval, $model['source']['tagmark']);
                $tt = str_replace("|--", '', $tt);
                $str = "<p class=\"form-control-static\">{$tt}</p> ";
                $str2 = "<p class=\"form-control-static\">{$tt}</p> ";
                break;
            case 'datepicker':
                $width = isset($model['width']) ? $model['width'] : 400;
                $height = isset($model['height']) ? $model['height'] : 20;
                
                $dateattr = "";
                if (isset($model['dateformat'])) {
                    $dateformat = $model['dateformat'];
                    $datetype = isset($dateformat['type']) ? $dateformat['type'] : "datetime";
                    $format = isset($dateformat['format']) ? $dateformat['format'] : "yyyy-MM-dd HH:mm:ss";
                    $trancemat =isset($dateformat['trancemat']) ? $dateformat['trancemat'] : "Y-m-d H:i:s";
                    
                    $dateattr = " data-type=\"{$datetype}\" data-format=\"{$format}\" ";
                    if(isset($dateformat['max'])){
                        $dateattr .=" data-max=\"{$dateformat['max']}\" ";
                    } 
                    if(isset($dateformat['min'])){
                        $dateattr .=" data-min=\"{$dateformat['min']}\" ";
                    } 
                }
               if(empty($cuentval)){
                   $cuentval=$model['value'];
               }
                if (is_number($cuentval)) {
                    if ($cuentval > 0) {
                        $cuentval = date($trancemat, $cuentval);
                    } else {
                        $cuentval = "";
                    }
                }
                switch ($model['readonly']) {
                    case "1"://带隐藏域的只读
                        $s = htmlspecialchars_decode($cuentval);
                        $str = "<input type=\"hidden\" name=\"{$name}\" id=\"{$name}\" value=\"{$cuentval}\" />  <p class=\"form-control-static\">{$s}</p> ";
                        break;
                    case '2'://不带隐藏域的只读
                        $str = "<p class=\"form-control-static\">{$cuentval}</p> ";
                        break;                   
                    default :
                        $str = "<input type=\"text\" name=\"{$name}\" __VERIFYSTRING__ id=\"{$name}\" $readonly  $style  class=\"trimblank {$cssclass} {$model['cls']} datepicker\" value=\"{$cuentval}\" placeholder=\"{$model['placeholder']}\" {$dateattr} />";
                        break;
                }
                $str2 = "<p class=\"form-control-static\">{$cuentval}</p> ";                
                break;
            case "autocomplete"://自动完成            
                if (is_array($cuentval)) {
                    $value = $cuentval;
                } else {
                    $value['authid'] = $cuentval;
                    $value['author'] = $cuentval;
                }                               
                $str = "<input type=\"hidden\" name=\"{$name}val\" id=\"{$model['handdleid']}val\" value=\"{$value["authid"]}\" />
                        <input type=\"text\" name=\"{$name}\" id=\"{$model['handdleid']}text\" $style  class=\"trimblank {$cssclass} {$model['cls'] {$DefaultClass["autocomplete"]}}\" value=\"{$value['author']}\" />";
          
                break;
            case 'password':
                $width = isset($model['width']) ? $model['width'] : 400;
                $height = isset($model['height']) ? $model['height'] : 20;
                $str = "<input type='password' name='{$name}' id='{$name}' __VERIFYSTRING__  $style  class='trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]}' />";
                break;
            case 'switch':
                if ($cuentval) {
                    $cuentvaled = 'checked';
                }
                $str = "<input type=\"checkbox\" value='1' {$cuentvaled} name=\"{$name}\" lay-skin=\"switch\" lay-filter=\"switchTest\" lay-text=\"ON|OFF\">";
                break;
            case 'text':
                $row = isset($model['height']) ? $model['height'] : 6;
                $cols = isset($model['width']) ? $model['width'] : 100;
//                $cuentval = htmlspecialchars($cuentval);
                $cssclass = "layui-textarea";
                $lineclass = "layui-form-text";
                $str .="<textarea name=\"{$name}\" id=\"cnt{$name}\" __VERIFYSTRING__ $readonly rows=\"{$row}\"cols=\"{$cols}\" style=\"resize: both;\" class=\"trimblank {$cssclass} {$model['cls']}\" >{$cuentval}</textarea>";
                break;
            case 'layedit':
                $height = isset($model['height']) ? $model['height'] : 100;
                $str .= "<textarea name=\"{$name}\" id=\"cnt{$name}\" data-height=\"{$height}\"  style=\"display: none;\" class=\"trimblank moyu-editor\" lay-verify=\"moyueditor\" >{$cuentval}</textarea>";
                break;
            default :
                $lineclass = 'hidden';
                $str = "<input type=\"hidden\" name=\"{$name}\" __VERIFYSTRING__ id=\"{$name}\"  value=\"{$cuentval}\"/>";
                break;
        }
        if ($str == '') {
            $lineclass = 'hidden';
            $str = "<input type=\"hidden\" name=\"{$name}\" __VERIFYSTRING__ value=\"{$cuentval}\"/>";
        }
        $str2 = str_replace("__TITLETEXT__", $langTitle, $str2);
        $str = str_replace("__VERIFYSTRING__", $verifystring, $str);

        $result = array('title' => $langTitle, 'content' => $str, 'content2' => $str2, 'lineclass' => $lineclass, 'titledesc' => $titledesc . $ext, 'titlecls' => $titlecls, 'model' => $model, 'key' => $name, 'cmodel' => $model['model']);

//        if ($titletag == "title") {
//            var_dump($result);
//        }

        if (isset($model['tableindex'])) {
            $result['tableindex'] = $model['tableindex'];
        }
        
        $width = isset($model['width']) ? $model['width'] : 100;//多容器宽度
        $result['width'] = $width;
        if($model['model']=='hidden'){//外容器隐藏
             $result['style'] = "display:none;";
        }
        return $result;
    }
    
    /**
     * 生成联动下拉列表
     * @param type $model       html模型
     * @param type $name        控件name属性
     * @param type $ishidden    生成隐藏域
     * @param type $cuentvaled    控件value
     * @param type $map         查询条件 
     * @param type $cssclass    控件样式
     * @return string                       
     */
    static function mk_linkage($model, $name, $cuentval = 0, $map = '', $cssclass = '') {    
        $inputtype = empty($model['inputtype']) ? 'text' : $model['inputtype'];        
        $valarray = explode('/', $cuentval);
        $set = $model['relation'];
        $relationset=$model['relation-set'];  
        $hidvalue = $cuentval;
        if ($relationset['valuemodel'] == 2) {
            $c = count($valarray) - 1;
            $index = $c > 0 ? $c : 0;
            $hidvalue = $valarray[$index];
        }
        $str = "<div class=\"layui-input-inline\"><input type=\"{$inputtype}\" autocomplete=\"off\" name=\"{$name}\" class=\"trimblank layui-input\" id=\"{$name}\" value=\"{$hidvalue}\" __VERIFYSTRING__/></div>";        
        
        $db = self::getdb($model, $map);
        foreach ($set as $k => $kage) {
            
            $verify = $kage['verify'];
            $vmsg = "lay-verify-msg=\"{$kage['verifymsg']}\"";

            $verifystring = ""; //验证规则
            if (!empty($verify)) {
                $verifystring = "{$vmsg} lay-vertype=\"msg\" lay-verify=\"{$verify}\"";
            }
    
            $str .= "<div class=\"layui-input-inline linkage-{$name}\">";
            $datastr = "";
            if(isset($kage['extmap'])){
                $extmap=$kage['extmap'];
                unset($kage['extmap']);
            }
            $kage = array_merge($kage, $relationset);
            foreach ($kage as $kk => $vv) {
                if ($kk == 'id') {
                    $datastr .= "{$kk}=\"{$vv}\"";
                } else {
                    $datastr .= "datax-{$kk}=\"{$vv}\"";
                }
            }
            foreach ($extmap as $ks => $vs) {//附加参数
                $datastr .= "data-{$ks}=\"{$vs}\"";
            }

            $datastr .= "datax-ctrindex=\"{$k}\"";

            $str .= "<select {$datastr}  class=\"{$cssclass}  {$model['cls']}\" datax-linkagename=\"{$name}\" lay-filter=\"linkage\" {$model["search"]} {$model["ignore"]} {$verifystring} >";
            foreach ($db as $key => $v) {
                if ($valarray[$k] === $v['idvalue']) {
                    $str .= "<option selected value=\"{$v['idvalue']}\">{$v['idname']}</option>";
                } else {
                    $str .= "<option value=\"{$v['idvalue']}\">{$v['idname']}</option>";
                }
            }
            $str .= "</select></div>";
            if (isset($kage['action']) && $valarray[$k]) {
                $submap = array($kage['pramname'] => $valarray[$k]);
                if ($extmap) {
                    $submap = array_merge($extmap, $submap);
                }
                $ajaxstr = common_data_readapi($kage['action'], $submap);
                $db = $ajaxstr['list'];
            } else {
                $db = NULL;
            }
        }
        return $str;
    }
    /**
     * 生成下拉列表
     * @param type $model       html模型
     * @param type $name        控件name属性
     * @param type $ishidden    生成隐藏域
     * @param type $cuentvaled    控件value
     * @param type $map         查询条件 
     * @param type $cssclass    控件样式
     * @return string                       
     */
    static function mk_list($model, $name, $cuentval = 0, $map = '', $cssclass = '') {
        $str = '<select ' .$model["ignore"] . ' name="' . $name . '" lay-filter="' . $name . '" id="' . $name . '" class="' . $cssclass . ' ' . $model['cls'] . '" ' .$model["search"] . ' __VERIFYSTRING__ >';
        $db = self::getdb($model, $map);
        if (count($db) == 0)
            return '';
        foreach ($db as $key => $v) {
            if ($cuentval == $v['idvalue']) {
                $cuentvaled = 'selected';
                $strname = $v['idname'];
            } else
                $cuentvaled = '';
            $str .="<option {$cuentvaled} value=\"{$v['idvalue']}\">{$v['idname']}</option>";
        }
        $str .='</select>';
        switch ($model['readonly']) {
            case "1"://带隐藏域的只读         
                $str = "<input type=\"hidden\" name=\"{$name}\" id=\"{$name}\" value=\"{$cuentval}\" />  <p class=\"form-control-static\">{$strname}</p> ";
                break;
            case '2'://不带隐藏域的只读
                $str = "<p class=\"form-control-static\">{$strname}</p> ";
                break;
        }

        return $str;
    }
    
    static public function mk_imglist2($model, $name, $cuentval = 0, $map = '', $cssclass = '') {
        $str = "<input type=\"hidden\" name=\"{$name}\" id=\"{$name}\" value=\"{$cuentval}\" /><ul id=\"{$name}photolist\" class=\"photolist\">";
        $db = self::getdb($model, $map);
        foreach ($db as $key => $v) {
            if ($cuentval == $v['idvalue']) {
                $cuentvaled = 'select';
                $strname = $v['idname'];
            } else
                $cuentvaled = '';
            $url = creat_url_lan($model['update'], array('id' => $v['idvalue']));
            $newimg = creatThumbImages($v, 100, 100);
            $str .="<li class=\"{$cuentvaled} photolistoption\" data-id=\"{$name}\" data-value=\"{$v['idvalue']}\" style=\"background-image: url({$newimg});\">
            <span class='title'>{$v['idname']}</span>
            <span class='update diag_modal'  data-title=\"{$model['windowtitle']}\" data-url=\"{$url}\">查看</span>
            </li>";
        }
        $url = creat_url_lan($model['addnew'],$model['creapara']);
        $str .="<li class=\"addphotolist diag_modal\" data-title=\"{$model['windowtitle']}\" data-url=\"{$url}\" ><span>添加</span></li></ul>";     

        return $str;
    }
    
    static public function mk_imglist($model, $name, $cuentval = 0, $map = '', $cssclass = '') {
        
        $inputtype = empty($model['inputtype']) ? 'text' : $model['inputtype'];
        $str = "<input type=\"{$inputtype}\" name=\"{$name}\" id=\"{$name}\" value=\"{$cuentval}\" /><ul id=\"{$name}photolist\" class=\"photolist\">";
        $db = self::getdb($model, $map);
        foreach ($db as $key => $v) {
            if ($cuentval == $v['idvalue']) {
                $cuentvaled = 'select';
                $strname = $v['idname'];
            } else
                $cuentvaled = '';
            $url = creat_url_lan($model['update'], array('id' => $v['idvalue']));
            $newimg = creatThumbImages($v, 100, 100);
            $str .="<li class=\"{$cuentvaled} photolistoption\" data-id=\"{$name}\" data-value=\"{$v['idvalue']}\" style=\"background-image: url({$newimg});\">
            <span class='title'>{$v['idname']}</span>
            <span class='update diag_modal'  data-title=\"{$model['windowtitle']}\" data-url=\"{$url}\">查看</span>
            </li>";
        }
        $url = creat_url_lan($model['addnew'],$model['creapara']);
        $str .="<li class=\"addphotolist diag_modal\" data-title=\"{$model['windowtitle']}\" data-url=\"{$url}\" ><span>添加</span></li></ul>";     

        return $str;
    }
    
    /**
     * 
     * @param type $model
     * @param type $name
     * @param type $cuentval
     * @param type $map
     * @param type $cssclass
     * @return type
     */
    static public function mk_photo($model, $name, $cuentval = 0, $map = '', $cssclass = '') {        
        $columns = getAttrsElementList($model['phototype']);
        $card=array();
        $listarry = array();
        $newvalue=array();
        if (!empty($cuentval)) {
            $newvalue = json_decode($cuentval, true);
            $listarry = $newvalue['list'];
        }
        foreach ($columns as $v) {
            $card[] = array('tagvalue' => $v['tagvalue'], 'title' => $v['title']);
            if (!isset($listarry[$v['tagvalue']])) {
                $listarry[$v['tagvalue']] = array();
            }
        }
        $newvalue['card'] = $card;
        if (!isset($newvalue['list'])) {
            $newvalue['list'] = $listarry;
        }
        $newjson = json_encode($newvalue);         
        $imgurl = get_photo_firstimg($newjson);
        $imgstr = "<img class=\"layui-upload-photopanle \"  data-handle=\"cnt{$name}\" data-title=\"{$model['windowtitle']}\" id=\"demofup{$name}\" src=\"{$imgurl}\" >";
        $str = "<textarea name=\"{$name}\" id=\"cnt{$name}\"  readonly rows=\"{$row}\"cols=\"{$cols}\" style=\"resize: both; display:none\" class=\"trimblank \" >{$newjson}</textarea>";
        $str .=$imgstr;

        return $str;
    }

    
    /**
     * 生成树形列表
     * @param type $model       html模型
     * @param type $name        控件name属性
     * @param type $ishidden    生成隐藏域
     * @param type $cuentvaled    控件value
     * @param type $map         查询条件 
     * @param type $cssclass    控件样式
     * @return string                       
     */
    static function mk_tree($model, $name, $cuentval = 0, $map = '', $cssclass = '') {

        $id = $map['id'];
        unset($map['id']);
        $dbsource = $model['source'];
        if (!isset($dbsource['dbmodel'])) {
            return "dbmodel 必须配置";
        }

        $str = "<select name=\"{$name}\" id=\"{$name}\" lay-filter=\"{$name}\" class=\"{$cssclass}  {$name} {$model['cls']}\"  lay-filter=\"{$model['cls']}\" {$model["search"]} {$model["ignore"]}  __VERIFYSTRING__>";
        if (isset($model['listtopname'])) {
            if ($cuentval == $model['listtopid']) {
                $cuentvaled = 'selected';
            }
            $str .= "<option {$cuentvaled} value='{$model['listtopid']}' >{$model['listtopname']}</option>";
        }
        if (isset($map['parentid'])) {
            $pid = $map['parentid'];
            unset($map['parentid']);
        } else {                     

            if (isset($dbsource['pmap'])) {
                @(eval('$db=new ' . "{$dbsource['dbmodel']}();"));
                $pid = $db->where($dbsource['pmap'])->getField('id');
            }
            if(!$pid){
                  $pid = 0;  
            }
        }

        $db = self::getdb($model, $map);             
        $dl = array();
        formatTreeList($db, $pid, $dl, '', '', 0);

        $aidlist = array();
        if ($model['mod'] == 'NotSelf') {
            $aidlist[] = $id;
            if ($id > 0) {
                $ds = array();
                formatTreeList($dl, $id, $ds, '', '', 0);
                foreach ($ds as $k => $v) {
                    $aidlist[] = $k;
                }
            }
        }

        foreach ($dl as $key => $v) {
            $disabled = "";
            if ($cuentval == $v[$dbsource['idfiled']]) {
                $cuentvaled = 'selected';
            } else {
                $cuentvaled = '';
            }
            switch ($model['mod']) {
                case "NotSelf":
                    if (in_array($v['id'], $aidlist)) {
                        $disabled = "disabled";
                    }
                    $str .= "<option {$cuentvaled} value=\"{$v[$dbsource['idfiled']]}\" {$disabled}  path='{$v['xpath']}'>{$v[$dbsource['valuefiled']]}</option>";
                    break;
                case "FinalNode":
                    if ($v['haschild']) {
                        $disabled = "disabled";
                    }
                    $str .= "<option {$cuentvaled} value=\"{$v[$dbsource['idfiled']]}\" {$disabled} path='{$v['xpath']}'>{$v[$dbsource['valuefiled']]}</option>";
                    break;
                default :
                    $str .= "<option {$cuentvaled} value=\"{$v[$dbsource['idfiled']]}\" path='{$v['xpath']}'>{$v[$dbsource['valuefiled']]}</option>";
                    break;
            }
        }
        $str .= '</select>';
        return $str;
    }

    /**
     * 对数组的数据源过滤参数进行处理
     * @param type $filter
     * @return type
     */
    static function filter_process($filter){
        if(empty($filter)){
            return null;
        }
        if(!is_array($filter)){
            $filter=  trim($filter,',');
            $filter=  explode(',', $filter);
        }
        return $filter;
    }

    /**
     * 从配置文件中读取数据源
     * @param type $model
     */
    static function getdb($model, $map = '') {

        $dbsource = $model['source'];
        
        if(isset($dbsource['extmap'])){
            $extmap=$dbsource['extmap'];     
            if(empty($map)){
               $map=$extmap;                      
            }else{
                $map= array_merge($map,$extmap);
            }
        }
        
        $dblist = array();
        if (isset($model['listtopname']))
            $dblist[] = array('idvalue' => $model['listtopid'], 'idname' => $model['listtopname']);

        if (is_array($dbsource)) {
            if (isset($dbsource['sourcetyp'])) {
                switch ($dbsource['sourcetyp']) {
                    case 'map':
                        foreach ($map as $key => $v) {
                            $idvalue = isset($v['idvalue']) ? $v['idvalue'] : $v[$dbsource['idfiled']];
                            $idname = isset($v['idname']) ? $v['idname'] : $v[$dbsource['valuefiled']];
                            $dblist[] = array('idvalue' => $idvalue, 'idname' => $idname);
                        }
                        break;
                    case 'attrmap'://属性
                        $rs = getAttrsElementList($dbsource['tagmark']);
                        foreach ($rs as $key => $v) {
                            $dblist[] = array('idvalue' => $v[$dbsource['idfiled']], 'idname' => $v[$dbsource['valuefiled']]);
                        }
                        break;
                    case 'map2':
                        foreach ($map as $v) {
                            $dblist[] = array('idvalue' => $v, 'idname' => $v);
                        }
                        break;
                    case 'number'://连续数
                        $min = $dbsource['min'] ? $dbsource['min'] : 0;
                        $max = $dbsource['max'] ? $dbsource['max'] : 10;
                        for ($i = $min; $i < $max; $i++) {
                             $dblist[] = array('idvalue' => $i, 'idname' => $i);
                        }                      
                        break;
                    case 'hash'://静态散列值
                        $hash = $dbsource['hash'];
                        foreach ($hash as $key => $val) {
                            $dblist[] = array('idvalue' => $key, 'idname' => $val);
                        }
                        break;
                    case 'dbmodel'://调用模型中的数据读取方法
                        @(eval('$db=new ' . "{$dbsource['dbmodel']}();"));
                        $rs = $db->getlist($map);
                        if (isset($rs['list'])) {
                            $dblist = $rs['list'];
                        } else {
                            $dblist = $rs;
                        }
                        break;
                    default :
                        foreach ($map as $key => $v) {
                            $dblist[] = array('idvalue' => $v, 'idname' => $key);
                        }
                        break;
                }
            } else {//从数据库读取
                $filed = $dbsource['idfiled'] . ',' . $dbsource['valuefiled'];
                if (isset($dbsource['extfiled'])) {
                    $filed .=$dbsource['extfiled'];
                }
                $order = empty($dbsource['orderstr']) ? 'sortid desc' : $dbsource['orderstr'];
                switch ($dbsource['table']) {
                    case "admin":
                        $order = '';
                        $filed .= ',m_name';
                        break;
                    default :
                        break;
                }

                $db = M($dbsource['table'])->where($map)->field($field)->order($order)->cache()->select();
                foreach ($db as $key => $v) {
                    switch ($dbsource['table']) {
                        case "admin":
                            $dblist[] = array('idvalue' => $v[$dbsource['idfiled']], 'idname' => $v[$dbsource['valuefiled']]);
                            break;
                        default :
                            //$dblist[] = array('idvalue' => $v[$dbsource['idfiled']], 'idname' => $v[$dbsource['valuefiled']]);
                            $v['idvalue'] = $v[$dbsource['idfiled']];
                            $v['idname'] = $v[$dbsource['valuefiled']];
                            $dblist[] = $v; 
                            break;
                    }
                }
            }
        } else {
        
            foreach (C($dbsource) as $key => $v) {
                if (is_array($v)) {
                    if ($v['value'] != '-1')
                        $dblist[] = array('idvalue' => $v['value'], 'idname' => $v['name']);
                } else {
                    $dblist[] = array('idvalue' => $v, 'idname' => $key);
                }
            }                
        }
        //数据源过滤
        if (isset($dbsource['filter'])) {
            $filter = self::filter_process($dbsource['filter']);           
            $newdblist = array();
            if (!empty($filter)) {
                foreach ($dblist as $k => $v) {
                    if (in_array($v['idvalue'], $filter)) {
                        $newdblist[] = $v;                       
                    }
                }               
                $dblist = $newdblist;
            }
        }
        return $dblist;
    }

    //生成 radio 列表
    static function mk_rdlist($model, $name, $pid) {
        $db = self::getdb($model, $map);
        if ($pid == '')
            $pid = $model['value'];
        $str = "<div id=\"{$name}\" class=\"layui-radio-panel\"  __VERIFYSTRING__ >";
        $strname = '';
        foreach ($db as $key => $v) {
            if ($pid == $v['idvalue']) {
                $checked = 'checked';
                $strname = $v['idname'];    
            } else
                $checked = '';

            $str.=" <input type=\"radio\" id=\"{$name}{$v['idvalue']}\" name=\"{$name}\" value=\"{$v['idvalue']}\" lay-filter=\"{$name}\" title=\"{$v['idname']}\" {$checked}>";
        }
        $str.="</div>";
        
        switch ($model['readonly']) {
            case "1"://带隐藏域的只读         
                $str = "<input type=\"hidden\" name=\"{$name}\" id=\"{$name}\" value=\"{$pid}\" />  <p class=\"form-control-static\">{$strname}</p> ";
                break;
            case '2'://不带隐藏域的只读
                $str = "<p class=\"form-control-static\">{$strname}</p> ";
                break;
        }
        $titlecls = isset($model['titlecls']) ? $model['titlecls'] : $DefaultClass["rdlist"];
        return $str;
    }

    //生成 checklist 列表
    static function mk_cklist($model, $name, $pid, $map = '', $cssclass = '') {
        if (!empty($model['readonly']) && $cuentval !== '')
            $readonly = 'disabled';

        $db = self::getdb($model, $map);
        if ($pid == '')
            $pid = $model['value'];
         $str = "<div id=\"{$name}\"  class=\"layui-checkbox-panel\" __VERIFYSTRING__ >";
        foreach ($db as $key => $v) {
            $checked = '';
            if (crackin($v['idvalue'], $pid))
                $checked = 'checked';
            $str .=" <input type=\"checkbox\" $checked name=\"{$name}[{$v['idvalue']}]\" value='{$v['idvalue']}' title=\"{$v['idname']}\" $readonly  />";
            // $str .=" <input type=\"checkbox\" {$checked} name=\"{$v['idvalue']}\" lay-skin=\"primary\" value='1' title=\"{$v['idname']} \" $readonly />"; 
        }
        $str.="</div>";
        return $str;
    }

    //生成 属性多选列表
    static function mk_attrcklist($model, $name, $pid) {
        $db = self::getdb($model, $map);
        $str = "";
        foreach ($db as $key => $v) {
            if ($pid[$v['idvalue']] == 1)
                $checked = 'checked';
            else
                $checked = '';
             $str .=" <input type=\"checkbox\" {$checked} name=\"{$v['idvalue']}\" lay-skin=\"primary\" value='1' title=\"{$v['idname']} \" $readonly />";   
         }
        return $str;
    }
    
    //星级评分
    static function mk_start($model, $name, $pid) {
        $db = self::getdb($model, $map);
        if ($pid == '')
            $pid = $model['value'];
        
        $str = "<div class=\"br-wrapper br-theme-css-stars\"><div class=\"br-widget\" id=\"star-{$name}\">";
        $str .= "<input type=\"hidden\" name=\"{$name}\" __VERIFYSTRING__  id=\"{$name}\" value=\"{$pid}\"  />";  
        foreach ($db as $key => $v) {
            $checked = '';
            if ($pid > $v['idvalue']) {
                $checked = 'br-selected';          
            }elseif ($pid==$v['idvalue']) {
                $checked="br-selected br-current";
            } 
            
            $shandle = "startchecked";
            if ($model['readonly'] == 1 && $pid > 0) {
                $shandle = "";
            }

            $str.=" <a href=\"#\" data-rating-value=\"{$v['idvalue']}\" data-rating-text=\"{$v['idname']}\" data-id=\"{$name}\" class=\"{$checked} {$shandle}\"><span></span></a>";
        }
        $str.="</div></div>";
        
        if ($model['readonly'] == 2 && $pid > 0) {
            $str = $pid;
        }
        return $str;
    }

    //生成编辑器
    static function mk_edit($model, $name, $pid) {
        $cnt = $pid;
        $sc = " <script id='post-$name' name='$name' type='text/plain'>$cnt</script>";
        
       $sc .= "  <script>editorlist +='{$name},';</script>";
        return $sc;
    }
    
    static function mk_uploadpic($model, $name, $pid, $cssclass = "") {
        $haystack = C('UPLOADTYPE.pic');
        $exts = implode($haystack, "|");
        return self::mk_uploadcut($exts, 'img', $model, $name, $pid, $cssclass);
    }

    static function mk_uploadfile($model, $name, $pid, $cssclass) {
        $haystack = C('UPLOADTYPE.file');
        $exts = implode($haystack, "|");
        $ftype = "file";
        $DefaultClass = C('DefaultClass');
        $pid = self::crcakext($pid);
 
        if (trim($pid) != '') {
            $imgurl = $pid;
              $imgstr=<<<EOT
       <p id="demoTextfup$name"><a id="demofup$name" href="{$imgurl}">{$pid}</a>
  <a class="delimg layui-btn layui-btn-sm" imgval="{$imgurl}">删除</a></p>
EOT;
        }
        $sc = <<<EOT
  <input type='text'  class="trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]} layui-input-inline" id='valfup$name' name='$name' value="{$pid}"  style="width:80%"/>
  <button type="button" exts="{$exts}"  class="layui-btn upload{$ftype}" id="fup$name">上传文件</button>
  <div class="layui-upload-list">
        {$imgstr}
  </div>
EOT;
        return $sc;
    }

    static function mk_upload($exts, $ftype, $model, $name, $pid, $cssclass = "") {
        $DefaultClass = C('DefaultClass');
        $pid = self::crcakext($pid);
        $inputtype = empty($model['inputtype']) ? 'text' : $model['inputtype'];
        switch ($model['readonly']) {
            case 2:
                 if (trim($pid) != '') {
                    $imgurl = $pid;
                    $imgstr = <<<EOT
                   <p id="demoTextfup$name"><img class="layui-upload-img" id="demofup$name" src="$imgurl"></p>
EOT;
                }
                $sc = <<<EOT
  <div class="layui-upload-list layerphoto">          
 {$imgstr}
  </div>
EOT;
                break;
            case 1:
                if (trim($pid) != '') {
                    $imgurl = $pid;
                    $imgstr = <<<EOT
                   <p id="demoTextfup$name">  <img class="layui-upload-img" id="demofup$name" src="$imgurl" ></p>
EOT;
                }
                $sc = <<<EOT
  <input type='hidden'  class="trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]} layui-input-inline" id='valfup$name' name='$name' value="{$pid}"  style="width:80%"/>
  <div class="layui-upload-list">          
 {$imgstr}
  </div>
EOT;
                break;
            default :
                if (trim($pid) != '') {
                    $imgurl = $pid;
                    $imgstr = <<<EOT
                   <p id="demoTextfup$name">  <img class="layui-upload-img" id="demofup$name" src="$imgurl" >
  <a class="delimg layui-btn layui-btn-sm" handid='valfup$name' imgval="{$imgurl}"><i class="layui-icon">&#xe640;</i></a></p>
EOT;
                }
                $sc = <<<EOT
  <div class="layui-input-inline">                        
  <input type='{$inputtype}'  class="trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]} layui-input-inline" id='valfup$name' name='$name' value="{$pid}"  style="width:80%"/>
  </div>
  <div class="layui-input-inline">
  <button type="button" exts="{$exts}"  class="layui-btn upload{$ftype}" id="fup$name">上传图片</button>
  </div>
  <div class="layui-upload-list">          
 {$imgstr}
  </div>
EOT;
                break;
        }

        return $sc;
    }
    
    /**
     * 上传图片
     * @param type $model
     * @param type $name
     * @param type $select
     * @param type $map
     * @param type $cssclass
     * @return type
     */
    static public function mk_uploadcut($exts, $ftype, $model, $name, $pid, $cssclass = "") {       
        if (trim($pid) != '') {
            $imgurl = $pid;
        } else {
            $imgurl = "/Public/images/select.png";
        }
        if (empty($pid)) {
            $disp = "display:none;";
        }
        $imgurl = empty($imgurl) ? "/Public/images/select.png" : $imgurl;
        $imgstr = "<img class=\"layui-upload-img select-img \"  data-handle=\"{$name}\" data-title=\"{$model['windowtitle']}\" id=\"demofup{$name}\" src=\"{$imgurl}\" >";
        $str = "<input type=\"hidden\" name=\"{$name}\" id=\"valfup{$name}\" value=\"{$pid}\" />"
                . $imgstr
                . "<div class='img-tools' style='{$disp}' id=\"tools-{$name}\"><a id=\"{$name}-select\" class='select-img' data-handle='{$name}' >编辑</a> <a  class='del-image' data-handle='{$name}' >删除</a></div>";
        return $str;
    }

    /**
     * 多图上传
     * @param type $model
     * @param type $name
     * @param type $pid
     * @return type
     */
    static function mk_muploadpic($model, $name, $pid, $cssclass = "") {
        $haystack = C('UPLOADTYPE.pic');
        $exts = implode($haystack, "|");
        $DefaultClass = C('DefaultClass');
        $imglist = explode(',', $pid);
        $imgstr = '';
        $inputtype = empty($model['inputtype']) ? 'text' : $model['inputtype'];
        switch ($model['readonly']) {
            case 2:
                foreach ($imglist as $v) {
                    if (!empty($v)) {                        
                       $newimg = creatThumbImages($v, 100, 100);
                        $imgstr .= <<<EOT
                    <div class="brick small" style="background-image:url({$newimg});">                    
                        </div>
EOT;
                    }
                }

                $sc = <<<EOT
  <div class="layui-upload-list gridly">          
 {$imgstr}
  </div>
EOT;
                break;

            default :
                foreach ($imglist as $v) {
                    if (!empty($v)) {
                         $newimg = creatThumbImages($v, 300, 100);                  
                        $imgstr .= <<<EOT
                    <div class="brick small" style="background-image:url({$newimg});">                    
                        <div class="delimg" handid='valfup$name' imgval="{$v}">
                            <i class="layui-icon">&#x1006;</i></div></div>
EOT;
                    }
                }

                $sc = <<<EOT
  <input type='{$inputtype}' data-autopost="{$model['autopost']}"  class="trimblank {$cssclass} {$model['cls']} {$DefaultClass["input"]} layui-input-inline" id='valfup$name' name='$name' value="{$pid}"  style="width:80%"/>
            <img exts="{$exts}"  class="mimgupload muploadimg" id="fup$name" src="/Public/images/select.png" alt="上传图片" >
  <div class="layui-upload-list gridly">          
 {$imgstr}
  </div>
EOT;
                break;
        }

        return $sc;
    }

    /**
     * 上传视频
     * @param type $model
     * @param type $name
     * @param type $select
     * @param type $map
     * @param type $cssclass
     * @return type
     */
    static public function mk_video($model, $name, $value = '', $map = '', $cssclass = '') {
        $imgurl = $value[$name . "pic"];
        if(empty($value[$name])){
            $disp="display:none;";
        }
        $imgurl = empty($imgurl) ? "/Public/images/select.png" : $imgurl;
        $imgstr = "<img class=\"layui-upload-videopanle \"  data-handle=\"{$name}\" data-title=\"{$model['windowtitle']}\" id=\"demofup{$name}\" src=\"{$imgurl}\" >";
        $str = "<input type=\"hidden\" name=\"{$name}\" id=\"{$name}\" value=\"{$value[$name]}\" />"
                . "<input type=\"hidden\" name=\"{$name}pic\" id=\"valfup{$name}\" value=\"{$value[$name . 'pic']}\" />"
                . $imgstr
                . "<div class='video-tools' style='{$disp}' id=\"tools-{$name}\"><a id=\"{$name}pic-select\" class='select-videopic' data-handle='{$name}' >编辑封面</a> <a id=\"{$name}pic-del\" class='del-video' data-handle='{$name}' >清除视频</a></div>";
        return $str;
    }
     
    static function crcakext($filename,$filetype='pic'){
        $a=  explode('.',$filename);
        if(count($a)>1){
            $ext=  strtolower($a[1]);
            if(!empty($ext)){
                $haystack=C('UPLOADTYPE.'.$filetype);
                if(in_array($ext, $haystack)){
                    return $filename;
                }
            }
        }
        return '';
    }
}
