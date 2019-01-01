<?php
namespace Admin\Controller;
use Think\Controller;
use Common\Model;
use Common\ViewModel;

class SubsidiaryController extends BaseController{
    
    public function _initialize() {
        parent::_initialize();
        $this->assign('tabelname', 'Subsidiary');
        $this->getuserpower();
    }

    public function index() {
        $db = new Model\SubsidiaryModel();
        if (IS_POST) {
            $key = I('key');
            if ($key !== "") {
                $map['sid_name'] = array('like', "%$key%");
            }
            $count = $db->getcount($map);
            $Page = new \Extend\Page($count, $this->pagesize);
            $show = $Page->show();
            $rs = $db->getlist($map, $Page->firstRow, $Page->listRows);

            $newrs = showTranceDatabymap($db->ShowMap, $rs, $this->userPower, get_class($db));
            $drs = array('list' => $newrs, 'totalSize' => $count);
            $this->ajaxReturn($drs);
        }
        $this->assign('showmap', $db->ShowMap);
        $this->display('Commonf/index');
    }

    public function add() {
        //默认显示添加表单
        if (!IS_POST) {
            $dataset = C('SUBSIDIARYPANLE');
            foreach ($dataset as $key => $v) {
                switch ($key) {
                    case 'theme':
                        $map['id'] = $id;
                        $dl[] = show_list($v, $key, false, '', $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, '');
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->display("Commonf/commonf");
        } else {
            //如果用户提交数据
            $db = new Model\SubsidiaryModel();
            $data = getpost();
            $rs = $db->addnew($data);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 更新站点信息
     * @param  [type] $id [单页ID]
     * @return [type]     [description]
     */
    public function update($sid_id) {
        $id = I('get.sid_id', 0, intval);
        $db = new Model\SubsidiaryModel();  
        if (!IS_POST) {
            $model = $db->getmodel($id);
            $dataset = C('SUBSIDIARYPANLE');
            foreach ($dataset as $key => $v) {
                $value = $model[$key];
                switch ($key) {
                    case 'theme':
                        $map['id'] = $id;
                        $dl[] = show_list($v, $key, false, $value, $map);
                        break;
                    default:
                        $dl[] = show_list($v, $key, false, $value);
                        break;
                }
            }
            $this->assign('setdata', $dl);
            $this->assign('model', $model);
            $this->display("Commonf/commonf");
        } else {
            $data = getpost();
            $data['sid_id'] = $sid_id;
            $rs = $db->update($data);
            $this->ajaxReturn($rs);
        }
    }

    /**
     * 禁用/开启站点
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function del($sid_id) {
        $id = I('get.sid_id', 0, intval);
        $db = new Model\SubsidiaryModel();
        $rs = $db->sdel(['sid_id' => $id]);
        $this->ajaxReturn($rs);
    }

}
