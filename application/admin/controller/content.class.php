<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/7/10 0010
 * Time: 16:11
 */

defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);
yzm_base::load_sys_class('form','',0);

class content extends common{
    public function init(){
        include $this->admin_tpl('content_list');
    }

}