<?php
namespace Admin\Behavior;
use Think\Behavior;

class SetthemeBehavior extends \Think\Behavior {
    public function run(&$param) {
        set_theme();
    }
}
