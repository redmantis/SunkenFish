<?php
namespace Admin\Controller;
use Think\Model;

/**
 * 权限验证
 */
class CommonController extends BaseController
{
 
    /**
     * 修改属性
     * @param type $tb  表单名称
     * @param type $idname 索引字段名
     * @param type $id  索引字段值
     * @param type $filedname 属性字段名
     * @param type $filedval  属性段值
     */
    public function changeattr($tb = '', $idname = 'id', $id, $filedname, $filedval) {
        $rs = testenable_writing();
        if ($rs['status'] !== 0) {
              $this->ajaxReturn($rs);
              exit;            
        }
        
        $id = intval($id);
        $filedval = intval($filedval);
        $filedval = $filedval == 0 ? 1 : 0;
        $curnettext = $filedval ? '✓' : '✗';

        $db = M($tb);
        $data[$filedname] = $filedval;
        
        if ($tb == 'admin') {
            if (C('AUTH_CONFIG.AUTH_SUPERMAN') == $id) {
                $msg['status'] = 0;
                $msg['msg'] = "超级管理员不可禁用";
                $this->ajaxReturn($msg);
                die;
            }
        }

        if ($db->where(array($idname => $id))->save($data)) {
            $data['status'] = 0;
            $data['curentval'] = $filedval;
            $data['curenttext'] = $curnettext;
            $data['msg'] = 'DataModifySuc';
        } else {
            $data['status'] = 20000002;
            $data['msg'] = 'DataModifyFailed';
        }   
        $this->ajaxReturn($data, 'JSON');
    }
    
    /**
     * 属性轮换
     * @param type $tb  表单名称
     * @param type $idname 索引字段名
     * @param type $id  索引字段值
     * @param type $filedname 属性字段名
     * @param type $filedval  属性段值
     * @param type $format  属性列表
     */
    public function rotationattr($tb = '', $idname = 'id', $id, $filedname, $filedval, $format) {
        $rs = testenable_writing();
        if ($rs['status'] !== 0) {
              $this->ajaxReturn($rs);
              exit;            
        }  
        $id = intval($id);
        $rs = getAttrsElementList($format);
        $array = array_values($rs);
        $count = count($rs) - 1;        
        $key=0;
        foreach ($array as $k => $v) {
            if ($v['tagvalue'] == $filedval) {
                if ($k < $count) {
                    $key = $k + 1;                   
                }
                $removeclass=$v['tagclass'];
                break;
            }
        }
        $filedval = $array[$key]['tagvalue'];
        $curnettext = clearpre($array[$key]['title']);

        $db = M($tb);
        $data[$filedname] = $filedval;
        if ($db->where(array($idname => $id))->save($data)) {
            $data['status'] = 0;
            $data['curentval'] = $filedval;
            $data['curenttext'] = $curnettext;
            $data['removeclass'] = $removeclass;
            $data['curent'] = $array[$key];
            $data['msg'] = 'DataModifySuc';
        } else {
            $data['status'] = 20000002;        
            $data['msg'] = 'DataModifyFailed';
        }
        $this->ajaxReturn($data);
    }
    
    
    public function prompt(){     
        $this->display('Commonf/prompt');
    }

    /**
     * 保存自动刷新状态
     */
    public function jumpandflash() {
        $flash = I("flash");
        cookie('jumpandflash', $flash);
    }
   
}
