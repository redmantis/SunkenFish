<?php
namespace Admin\Controller;
use Common\Gmodel;

/*
 * 最后修改时间：2017/11/2
 * 最后修改人：默鱼
 * 区域管理
 */
class SubregionController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $this->assign('tabelname', 'LbsRegion');
        $plist = array('del', 'update', 'add');
        $gbcode = I('gbcode', '');
        $parm = array('gbcode' => $gbcode);
        $this->getuserpower($plist,$parm);
        $this->assign('uploadfilemod', '');
    }

    /**
     * 文章列表
     * @param type $key
     */
    public function index() {
        $regioncode = I('gbcode');
        $key = I('key');     
        $db = new Gmodel\LbsSubregionModel();
        if (IS_POST) {
            $map = getselectmap($this->manageid, $this->manages_id);
            $regioncode = trim($regioncode, '00');
            if (!empty($regioncode)) {
                $map['areacode'] = array('like', "{$regioncode}%");
            }

            if ($key !== "")
                $map['areaname'] = array('like', "%$key%");

            $count = $db->where($map)->count(); // 查询满足要求的总记录数
            $Page = new \Extend\Page($count, $this->pagesize); // 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show(); // 分页显示输出
            $rs = $db->where($map)->scope('orderby')->limit($Page->firstRow . ',' . $Page->listRows)->select();

            $newrs = showTranceDatabymap($db->ShowMap, $rs, $this->userPower);
            $drs = array('list' => $newrs, 'totalSize' => $count, 'map' => $map);
            $this->ajaxReturn($drs);
            die;
        }

        $dataset = C('SUBREGIONSFILTER');
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
        $db = new Gmodel\LbsSubregionModel();
        $model = $db->getmodel(array('id'=>$id));
        if (!$model['status']) {
            $msg['returnCode'] = $model['status'];
            $msg['returnMessage'] = $model['msg'];
            $this->ajaxReturn($msg);
            die;
        }
        $dataset = C("LBSSUBREGION");
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
            $this->display("Commonf/tablecard");
        } else {
            $data = getpost(0);
            $rs = $db->update($data);
            $rs=  trancemessage($rs);            
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 更改发布状态
     * @param type $id
     * @param type $ispost
     */
    function del($id) {
        $id = intval($id);
        $db = new Gmodel\LbsSubregionModel();      
        $rs = $db->del($id);
        $rs = trancemessage($rs);        
        $this->ajaxReturn($rs);
    }

    /**
     * 添加新闻
     * @param type $colid
     */
    public function add() {
        $dataset = C('LBSSUBREGION');
        $this->assign('tablecard', $dataset['table_card']);
        if (!IS_POST) {
            $gbcode = I('gbcode', '');
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case 'gbcode':
                        $dl[] = show_list($v, $key, false, $gbcode);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display("Commonf/tablecard");
        } else {
            $db = new Gmodel\LbsSubregionModel();
            $d = getpost();
            $rs = $db->addnew($d);
            $rs = trancemessage($rs);
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
            $db = new Gmodel\LbsSubregionModel();
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