<?php
namespace Common\Model;
use Think\Model;

/**
 * 用户上传文件清单
 */
class UpFileModel  extends Model {   

    public function addnew($model) {
        $model['ip'] = get_client_ip();
        $model['addtime'] = time();
        $this->add($model);
    }

    /**
     * 返回列表
     * @param type $map
     * @param type $page
     * @param type $pagesize
     * @return type
     */
    public function getlist($map, $page = 0, $pagesize = 0) {
        $count = 0;
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        if ($pagesize > 0) {
            $count = $this->where($filedarray)->bind($valuearray)->count();
            $rs = $this->field(true)->where($filedarray)->bind($valuearray)->scope('orderby')->page($page, $pagesize)->select();
        } else {
            $rs = $this->where($map)->select();
        }
        $drs = array('list' => $rs, 'totalSize' => $count);
        return $drs;
    }
    
    /**
     * 删除文件
     * @param type $path
     * @param type $userid
     * @return string
     */
    public function del($path, $userid) {
        $map['filepath'] = $path;
        if ($userid > 0) {
            $map['userid'] = $userid;
        }
        $filedarray = array();
        $valuearray = array();
        tracemaptobind($map, $filedarray, $valuearray);
        $rs= $this->where($filedarray)->bind($valuearray)->delete();
        return $rs;
    }

    protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'flashtime desc,id desc',
        ),
    );

}
