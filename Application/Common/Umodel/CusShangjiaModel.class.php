<?php

/*
 * 商户实名认证资料
 * 字符：UTF-8
 * @author  RDM:默鱼
 * @Email   feiyufly001@hotmail.com
 * @Creat   2017-12-12 14:56:07
 * @Modify  2017-12-12 14:56:07
 * @CopyRight:  2017 by RDM
 */

namespace Common\Umodel;
use Think\Model;

class CusShangjiaModel extends Model {

    protected $_validate = array(
        array('company', 'require', '请填写企业名称！', '', '', 3),
        array('license', 'require', '请填写营业执照号码！', '', '', 3),
        array('licensepic', 'require', '请上传营业执照照片！', '', '', 3),
        array('thumb', 'require', '请上传形象照片！', '', '', 3),
        array('truename', 'require', '请填写联系人！', '', ''),
        array('mobile', 'require', '请填写联系电话！', '', ''),
        array('email', 'require', '请填写联系邮箱！', '', ''),
        array('idcard', 'require', '请填写身份证编码！', '', '', 4),
        array('gender', array(1, 2, 0), '请指下性别', 3, 'in',4),
        array('type', array(1, 2, 3), '实名类型错误', 3, 'in'),
    );
    protected $patchValidate = true; //批量验证数据  
    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'regtime desc',
        ),
    );

    /**
     * 读取列表
     * @param type $map
     * @param type $skip
     * @param type $pagesize
     * @return type
     */
    public function getlist($map, $skip = 0, $pagesize = 0) {
        unset($map['size']);
        $sortstr = "";
        if (isset($map['sortstr'])) {
            $sortstr = $map['sortstr'];
            unset($map['sortstr']);
        }
        $count = 0;
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        if ($pagesize > 0) {
            $count = $this->where($filedarray)->bind($valuearray)->count();
            $rs = $this->where($filedarray)->bind($valuearray)->scope('orderby' . $sortstr)->limit($skip, $pagesize)->select();
        } else {
            $rs = $this->where($filedarray)->bind($valuearray)->scope('orderby' . $sortstr)->select();
        }
        $drs = array('list' => $rs, 'totalSize' => $count, 'status' => 1);
        return $drs;
    }
    
    /**
     * 取实名信息
     * @param type $userid 用户ID
     * @param type $type   用户类型
     */
    public function getmodel($userid, $type) {
        $rs = array('status' => 1);
        $model = $this->where(array('userid' => ":userid"))->bind(":userid", $userid)->find();    
        if (!$model) {
            $model = array('userid' => $userid);
            $pre = "";
            switch ($type) {
                case 1:
                    $pre = "GR";
                    break;
                case 2:
                    $pre = "QY";
                    break;
                case 3:
                    $pre = "ZJ";
                    break;
                default :
                    $rs['status'] = 0;
                    break;
            }
            $model['biz'] = creatOrderNo($pre);
            $model['type'] = $type;
            $model['regtime']=  time();
//            if ($rs['status'] == 1) {
//                $s = $this->add($model);
//            }
        }
        $rs['data']=$model;
        return $rs;
    }
    
    /**
     * 更新实名信息
     * @param type $model
     * @return type
     */
    public function update($model) {
        C('TOKEN_ON', false);
        $crcakmode = 3;
        if ($model['shangjia_type'] == 1) {
            $crcakmode = 4;
        }
        $rs = array('status' => 0, 'msg' => 'DataModifyFailed');
        if (!$this->create($model, $crcakmode)) {
            $rs['status'] = 0;
            $rs['data'] = $model;
            $rs['msg'] = $this->getError();
        } else {
            $model['status']=1;    
            if (isset($model['modid'])) {
                $model['type'] = $model['modid'];
            }
            $m = $this->where(array('userid' => ":userid"))->bind(":userid", $model['userid'])->find();
            if ($m) {             
                $s = $this->where(array('userid' => ":userid"))->bind(":userid", $model['userid'])->save($model);
                if (isset($model['modid'])) {
                    $udb = new CusCustomerModel();
                    $data = array('id' => $model['userid'], 'modid' => $model['modid']);
                    $udb->chg_user_mod($data);
                }
            } else {
                $udb = new CusCustomerModel();
                $data = array('id' => $model['userid'], 'modid' => $model['modid']);
                $udb->chg_user_mod($data);
                $s = $this->add($model);
            }
            if ($s) {
                $rs['status'] = $s;
                $rs['msg'] = "DataModifySuc";
                $pram = get_langue_parm();
                $url = U("/Ucent/Join/step1", $pram);
                $rs['location'] = $url;
            } else {
                $rs['msg'] = "DataModifyFailed";
                $rs['data']=$model;
            }
        }
        return $rs;
    }

}
