<?php
namespace Common\Model;
use Think\Model;

class AuthGroupAccessModel extends Model {

    protected $fields = array('m_id',
        'group_id',
    );
    
    public function setAcess($mid, $ids) {
        $map = array('m_id' => $mid);
        $rs = $this->where($map)->delete();
        $dataList = array();
        $ids = trim($ids, ',');
        $pst = explode(',', $ids);
        foreach ($pst as $key => $v) {
            $dataList[] = array('m_id' => $mid, 'group_id' => $v);
        }
        $rs += $this->addAll($dataList);
        if ($rs) {
            return array('status' => 0, 'msg' => 'DataModifySuc');
        } else {
            return array('status' => 20000002, 'msg' => 'DataModifyFailed');
        }
    }
}
