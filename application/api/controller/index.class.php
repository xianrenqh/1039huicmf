<?php
/**
 * 系统API接口类
 * Created by PhpStorm.
 * User: 小灰灰
 * Date:  2019/05/01 8:44
 * Other:
 */
yzm_base::load_sys_class('page','',0);
class index{
    
    
    /**
     * 验证码图像
     */
    public function code(){
        session_start();
        $code = yzm_base::load_sys_class('code');
        if(isset($_GET['width']) && intval($_GET['width'])) $code->width = intval($_GET['width']);
        if(isset($_GET['height']) && intval($_GET['height'])) $code->height = intval($_GET['height']);
        if(isset($_GET['code_len']) && intval($_GET['code_len'])) $code->code_len = intval($_GET['code_len']);
        if(isset($_GET['font_size']) && intval($_GET['font_size'])) $code->font_size = intval($_GET['font_size']);
        if($code->width > 500 || $code->width < 10)  $code->width = 100;
        if($code->height > 300 || $code->height < 10)  $code->height = 35;
        if($code->code_len > 8 || $code->code_len < 2)  $code->code_len = 4;
        $code->show_code();
        $_SESSION['code'] = $code->get_code();
    }
    
    /*
 * 点击验证码
*/
    public function code_captcha(){
        session_start();
        $clicaptcha = yzm_base::load_sys_class('code_clicaptcha');
        if($_POST['do'] == 'check'){
            $res= $clicaptcha->check($_POST['info'], false) ? 1 : 0;
            return_json(['res'=>$res]);
        }else{
            $clicaptcha->creat();
        }
    }
    
}