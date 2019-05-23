<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date:  2019/05/05 10:15
 * Other: 系统配置
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);
yzm_base::load_sys_class('page','',0);

class system_manage extends common {
    
    /**
     * 配置信息
     */
    public function init() {
        $data = get_config();
        include $this->admin_tpl('system_set');
    }
    
    
    /**
     * 保存配置信息
     */
    public function save(){
        if(isset($_POST['dosubmit'])){
            delcache('configs');
            unset($_POST['dosubmit']);
            unset($_POST['mail_to']);
            $Config = D('config');
            foreach($_POST as $key => $value){
                if($key!='site_code'){
                    $value = htmlspecialchars($value);
                }
                $Config->update(['value'=>$value],['name'=>$key]);
            }
            return_json(['message'=>"保存成功",'icon'=>2]);
        }
    }
    
    
}