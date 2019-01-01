<?php
namespace Common\Model;
use Think\Model;

/**
 * Description of AdminlogModel
 *管理员登录日志
 * @author redmantis <默鱼 at feiyufly001@hotmail.com>
 */
class AdminLogModel  extends Model {

    protected $fields = array(
        'log_id',
        'addtime',
        "addtime" => 'optime',
        'm_id',
        'log_info',
        'log_ip',
        'sid_id',
        'logtype',
    );
    
    protected $_auto = array(
        array('status', 1),
        array('statusstr', '成功'),
        array('log_ip', 'get_client_ip', 3, 'function'),
        array('log_info', 'serialize', 3, 'function'),
        array('addtime', 'time', 1, 'function'),
    );

    /**
     * 生成一条日志
     * @param type $model
     * @param type $info
         * $info['status']          //数字状态
         * $info['statusstr']       //文本状态
         * $info['op']              //数字操作
         * $info['opstr']           //文本操作
         * $info['opcontent']       //操作内容
         * $info['objectid']        //操作对象ID
         * $info['objecttable']     //操作对象表
     */
    public function addlog($model, $info) {
        $model['logtype'] = $info['op'];
        $model['addtime'] = time();
        $model['log_info'] = serialize($info);
        $model['log_ip'] = get_client_ip();
        $this->add($model);
    }
    
    public function addnew($model) {
        C('TOKEN_ON', false);
        if (!$this->create($model)) {
        }
        $this->add();
    }

    /**
     * 读取操作日志
     * @param array $map
     * @param type $getcount
     * @return type
     */
    public function getlist($map, $getcount = 0) {
       
        unset($map['start']);
        unset($map['end']);
        if ($getcount) {
            return $this->where($map)->count();
        } else {
            $page = $map['page'];
            $size = $map['size'];
            unset($map['page']);
            unset($map['size']);
            $rs = $this->field(true)->where($map)->scope('orderby')->limit($page, $size)->select();
            return $rs;
        }
    }
    
        protected $_scope = array(
        // 命名范围 orderby
        'orderby' => array(
            'order' => 'log_id desc',
        ),
    );

}
