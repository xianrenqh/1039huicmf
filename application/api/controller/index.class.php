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
    
    /**
     *获取bing背景图
     */
    public function getbing_bgpic(){
        $idx = $_GET['idx'];
        $api = "http://cn.bing.com/HPImageArchive.aspx?format=js&idx=$idx&n=1";
        $data = self::object2array(json_decode(self::get_url($api)));
        $pic_url = $data['images'][0]->{'url'}; //获取数据里的图片地址
        if($pic_url){
            $images_url  ="https://cn.bing.com/".$pic_url;      //如果图片地址存在，则输出图片地址
        }else{
            $images_url="https://s1.ax1x.com/2018/12/10/FGbI81.jpg";     //否则输入一个自定义图
        }
        header("Location: $images_url");    //header跳转
        
    }
    private  function get_url($url)
    {
        $ch = curl_init();
        $header[] = "";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
    private function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
    
}