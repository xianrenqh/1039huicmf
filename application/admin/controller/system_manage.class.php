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
        yzm_base::load_common('function/function.php', 'admin');
        if(isset($_POST['dosubmit'])){
            delcache('configs');
            unset($_POST['dosubmit']);
            unset($_POST['mail_to']);
            $arr = [];
            $Config = D('config');
            foreach($_POST as $key => $value){
                if(in_array($key, array('watermark_enable','watermark_name','watermark_position'))) {
                    $value = safe_replace(trim($value));
                    $arr[$key] = $value;
                } else{
                    if($key!='site_code'){
                        $value = htmlspecialchars($value);
                    }
                }
                $Config->update(['value'=>$value],['name'=>$key]);
            }
            set_config($arr);
            delcache('configs');
            return_json(['message'=>"保存成功",'icon'=>2]);
        }
    }
    
    /*
    * 自定义配置
    */
    public function user_config_list(){
        $config = D('config');
        $total = $config->where(array('type' => 99))->total();
        $page = new page($total, 10);
        $data = $config->where(array('type' => 99))->order('id DESC')->limit($page->limit())->select();
        include $this->admin_tpl('user_config_list');
    }
    
    /*
     *  添加配置
    */
    public function user_config_add(){
        if(isset($_POST['dosubmit'])){
            $config = D('config');
            $res = $config->where(array('name' => $_POST['name']))->find();
            if($res) return_json(array('status'=>0,'message'=>'配置名称已存在！'));
            if(empty($_POST['value']))  return_json(array('status'=>0,'message'=>'配置值不能为空！'));
            
            $_POST['type'] = 99;
            if(in_array($_POST['fieldtype'], array('select','radio'))){
                $_POST['setting'] = array2string(explode('|', rtrim($_POST['setting'], '|')));
            }else{
                $_POST['setting'] = '';
            }
            
            if($config->insert($_POST)){
                delcache('configs');
                return_json(array('status'=>1,'message'=>L('operation_success')));
            }else{
                return_json(array('status'=>0,'message'=>L('data_not_modified')));
            }
        }
        include $this->admin_tpl('user_config_add');
    }
    
    /*
 * 用户自定义配置编辑
 */
    public function user_config_edit() {
        if(isset($_POST['dosubmit'])) {
            $data = array();
            $data['title'] = $_POST['title'];
            $data['value'] = $_POST['value'];
            $data['status'] = $_POST['status'];
            
            if(D('config')->update($data, array('id' => intval($_POST['id'])))){
                delcache('configs');
                return_json(array('status'=>1,'message'=>L('operation_success')));
            }else{
                return_json();
            }
            
        } else {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $data = D('config')->where(array('id' => $id))->find();
            $fieldtype = $data['fieldtype'] ? $data['fieldtype'] : 'textarea';
            include $this->admin_tpl('user_config_edit');
        }
    }
    
    /*
 * 用户自定义配置删除
 */
    public function user_config_del() {
        if($_POST && is_array($_POST['id'])){
            if(D('config')->delete($_POST['id'], true)){
                delcache('configs');
                showmsg(L('operation_success'));
            }else{
                showmsg(L('operation_failure'));
            }
        }
    }
    
    /*删除单条数据*/
    public function user_config_del_one(){
        if($_GET['id'] ){
            if(D('config')->delete(['id'=>$_GET['id']], true)){
                delcache('configs');
                showmsg(L('operation_success'));
            }else{
                showmsg(L('operation_failure'));
            }
        }
    }
    
    
    /*
 * 根据字段类型获取html
 */
    public function public_gethtml($ftype='', $val='', $setting='') {
        debug();
        yzm_base::load_sys_class('form','',0);
        
        $fieldtype = $ftype ? $ftype : (isset($_POST['fieldtype'])&&is_string($_POST['fieldtype']) ? safe_replace($_POST['fieldtype']) : 'textarea');
        
        if($fieldtype == 'textarea'){
            echo '<textarea name="value" class="layui-textarea"  placeholder="例如：214243830">'.$val.'</textarea>';
        }elseif(in_array($fieldtype, array('select', 'radio'))){
            if($val){
                echo form::$fieldtype('value', $val, string2array($setting));
            }else{
                echo '<textarea name="setting" class="layui-textarea"  placeholder="选项用“|”分开，如“男|女|人妖”"></textarea> &nbsp;<input type="text" name="value" class="layui-input" style="width:180px" placeholder="默认值用配置值填写">';
            }
        }elseif($fieldtype == 'image' || $fieldtype == 'attachment'){
            echo '<div class="layui-input-inline" style="width: 70%"><input type="text" name="value" value=""  id="value" autocomplete="off" class="layui-input"></div> &nbsp;<button type="button" class="layui-btn" id="test3"><i class="layui-icon"></i>上传文件</button>';
        }else{
            echo '<textarea name="value" class="layui-textarea"  placeholder="例如：214243830">'.$val.'</textarea>';
        }
    }
    
}