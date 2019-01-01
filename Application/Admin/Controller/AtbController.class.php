<?php
namespace Admin\Controller;

use Common\Model;
use Common\Gmodel;
use Common\ViewModel;

/**
 * Description of CountController
 * 站点属性管量
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AtbController extends BaseController {
    
    public function _initialize() {
        parent::_initialize();
//        $this->assign('tabelname', 'Attrs');
        $tagvalue = I("get.tagmark");
        $exparm = array('tagmark' => $tagvalue);
        
        $plist = array('del', 'update', 'add', 'batchoperate');
        $this->getuserpower($plist, $exparm);
        $this->assign('uploadfilemod', UploadFileModSys);
    }

    public function index($key = "") {        
        $db = new Gmodel\AtbModel();
        
        $map['sid_id'] = array('in', "0,{$this->manages_id}");
        
        $dl = $db->getTree($map);
      
        $this->assign('showmap', $db->ShowMap);
        $this->assign('dl', $dl);
        
      
         //查询参数面板输出
        $dataset = C('ATTRSSEARCH');
        foreach ($dataset as $key => $v) {
            switch ($key) {             
                case 'aimcol':       
                    if ($tagvalue) {
                        $v['source']['pmap'] = array('tagmark' => $tagvalue);
                    }
                    $dx[] = show_list($v, $key, false, I($key), $map);
                    break;
                default:
                    $dx[] = show_list($v, $key, false, I($key));
                    break;
            }
        }
        $this->assign('searchdata', $dx);   
        $this->display('Commonf/tree');
    }
 
    public function add($parentid=0) {
        $dataset = C('ATTRSCONFIG');
        $this->assign('basecard', $dataset['table_card']);
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {             
                $value = '';
                switch ($key) {
                    case 'parentid':                     
                        $map['sid_id'] = array('in', "0,{$this->manages_id}");
                        $dl[] = show_list($v, $key, false, $parentid, $map);
                        break;      
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }
      
            $this->assign('setdata', $dl);
            $this->assign('actionurl',U('add'));           
            $this->display("Commonf/lang_edit");
        } else {
            $data = getLangPost(1);
            
            $model=$data['base'];
            if ($model['tagtype'] == 1) {                  
                if ($this->manageid != C('AUTH_CONFIG.AUTH_SUPERMAN')) {
                    $this->error("只有系统管理员可以管理系统属性");
                       return;
                }
            }
             $catchpath = "lancatch/{$this->manages_id}/attrs";
             clearCatch($catchpath);
            $db = new Gmodel\AtbModel();
            $rs = $db->addnew($data);       
            $this->ajaxReturn($rs); 
        }
    }

    public function update($id) {
        $id = intval($id);
        $db = new Gmodel\AtbModel();
        $dataset = C('ATTRSCONFIG');
        $this->assign('basecard', $dataset['table_card']);
        $model = $db->getmodel($id);
        if (!IS_POST) {            
            foreach ($dataset as $key => $v) {                
                $value = $model['base'][$key];            
                switch ($key) {
                    case 'parentid':                       
                        $map['sid_id'] = array('in', "0,{$this->manages_id}");
                        $map['id'] = $id;
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;
                    case 'tagtype':                     
                        $datamap = getAttrsElementList('tagtype');
                        $dl[] = show_list($v, $key, false, $value, $datamap);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('id', $id);     
            $this->assign('lanmodel', $model); 
            $this->assign('actionurl',U('update'));
            $this->display("Commonf/lang_edit");
        } else {
            $data = getLangPost(1);
            $db = new Gmodel\AtbModel();
            $tagtype = $db->where(array('id' => $id))->getField('tagtype');
            if ($tagtype == 1) {
                if ($this->manageid != C('AUTH_CONFIG.AUTH_SUPERMAN')) {
                    $this->error("只有系统管理员可以管理系统属性");
                    return;
                }
            }
            $catchpath = "lancatch/{$this->manages_id}/attrs";
            clearCatch($catchpath);
            $rs = $db->updater($data);            
            $this->ajaxReturn($rs);
        }
    }

    public function del($id) {
        $id = intval($id);
        $db = new Gmodel\AtbModel();
        $map = getselectmap($this->manageid, $this->manages_id);
        $rs = $db->del($map, $id);
        $this->ajaxReturn($rs);
    }

    /**
     * 批量删除
     * @param type $id
     */
    public function batchoperate() {        
          if (IS_POST) {
            $idlist = I('idlist');
            $suffix = I("suffix", 1);
            $map = ['idlist' => $idlist, 'suffix' => $suffix];
            $db = new Gmodel\AtbModel();
            $msg = $db->batchDelete($map);
            $this->ajaxReturn($msg);
        }
    }

}