<?php
namespace Admin\Controller;
use Common\Gmodel;

/*
 * 最后修改时间：2017/10/17
 * 最后修改人：鲁承涛
 * 楼宇管理
 */
class RegionController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('tabelname','LbsRegion');
        $plist = array('update','subregion_index');
        $this->getuserpower($plist);
        $this->assign('uploadfilemod','');
    }

    /**
     * 文章列表
     * @param type $key
     */
    public function index() {     
        $regioncode=  I('regioncode');      
        $key=I('key');
        $is_region_hot=I('is_region_hot',-1,  intval);
        $is_hot=I('is_hot',-1,  intval);
        $db=new Gmodel\LbsRegionModel();
        if(IS_POST){
        $map = getselectmap($this->manageid, $this->manages_id);  
        $regioncode=  trim($regioncode,'00');   
        if (!empty($regioncode)) {
                $map['gbcode'] = array('like', "{$regioncode}%");
            }

        if ($key !== "")
                $map['region'] = array('like', "%$key%");
        
         if ($is_region_hot > -1)
                $map['is_region_hot'] = $is_region_hot;
         
          if ($is_hot > -1)
                $map['is_hot'] = $is_hot;

            $count = $db->where($map)->count(); // 查询满足要求的总记录数
            $Page = new \Extend\Page($count, $this->pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show(); // 分页显示输出
            $rs = $db->where($map)->scope('orderby')->limit($Page->firstRow . ',' . $Page->listRows)->select();

            $newrs = showTranceDatabymap($db->ShowMap, $rs, $this->userPower);
            $drs = array('list' => $newrs, 'totalSize' => $count,'map'=>$map);
            $this->ajaxReturn($drs);
            die;
        }    
     
        $dataset = C('REGIONSFILTER');
        foreach ($dataset as $key => $v) {
            switch ($key) {
                case 'province':
                    $map['gbcode'] = array('like', "%0000");
                    $dx[] = show_list($v, $key, false, '', $map);
                    break;
                default:
                    $dx[] = show_list($v, $key, false, I($key));
                    break;
            }
        }
        $this->assign('showmap', $db->ShowMap);
        $this->assign('searchdata', $dx);        
        $this->display('Commonf/index');
    }


/**
     * 更新地区信息
     * @param type $id
     */
public function update($id) {
        checkid($id, true);
        $db = new Gmodel\LbsRegionModel();
        $model = $db->getmodel(array('id'=>$id));
        if (!$model['status']) {
            $msg['returnCode'] = $model['status'];
            $msg['returnMessage'] = $model['msg'];
            $this->ajaxReturn($msg);
            die;
        }
        $dataset = C("LBSREGION");
        $this->assign('tablecard',$dataset['table_card']);
        $model=$model['data'];
        if (!IS_POST) {
            foreach ($dataset as $key => $v) {
                switch ($key) {                   
                    default:
                        $dl[] = show_list($v, $key, false, $model[$key]);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('id', $id);
            $this->assign('baidumap', 2);
            $this->display("Commonf/tablecard");
        } else {
            $data = getpost(0);
            $rs = $db->update($data);
            $rs=  trancemessage($rs);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 删除地区
     * @param type $id
     * @param type $ispost
     */
    function del($id) {
        $id = intval($id);
        $db = new Gmodel\LbsRegionModel();
        $rs = $db->del($id);
        $rs = trancemessage($rs);
        $this->ajaxReturn($msg);
    }

    /**
     * 添加新闻
     * @param type $colid
     */
    public function add() {
        $dataset = C("LBSREGION");
        $this->assign('tablecard',$dataset['table_card']);
        if (!IS_POST) { 
            foreach ($dataset as $key => $v) {
                switch ($key) {               
                    default:
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display("Commonf/tablecard");
        }
       else {
            $db = new Gmodel\LbsRegionModel();
            $d = getpost();
            $rs = $db->addnew($d);
            $rs=  trancemessage($rs);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 批处理文章
     * @param type $id
     */
    public function batchoperate() {
        if (IS_POST) {
            $idlist = I('idlist');
            $error = "";
            if (empty($idlist)) {
                $msg['returnCode'] = 0;
                $msg['returnMessage'] = "没有选择要操作的对像";
                $this->ajaxReturn($msg);
                die;
            }
            $db =  new Gmodel\LbsRegionModel();
            $idl = explode(',', $idlist);
            $count=0;
            foreach ($idl as $id) {
                if (!empty($id)) {
                    $id = intval($id);
                    $rs = $db->sdel($id);
                    if($rs['status']){
                          $count ++;
                    }
                }
            }     
            if ($count) {
                $msg['returnCode'] = 1;
                $msg['returnMessage'] = "成功删除<b>{$count}</b>记录！";
            }
            $this->ajaxReturn($msg);
        }
    }
}