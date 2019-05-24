<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date:  2019/05/01 15:57
 * Other:
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);
class admin_manage extends  common {
    
    /**
     * 管理员管理列表
     */
    public function init(){
        $DB_admin =D('admin');
        $roleid = isset($_GET['roleid']) ? intval($_GET['roleid']) : 0;
        $total=$DB_admin->total();
        if(isset($_POST['do']) ){
            $page =$_POST['page'];
            $limit = $_POST['limit'];
            $first = ($page - 1) * $limit;
            $getkey = $_POST['key'];
            $where='1=1';
                $adminname=$getkey['adminname']?$getkey['adminname']:"";
                $roles=$getkey['roles']?$getkey['roles']:"";
                $status=$getkey['isuse']?$getkey['isuse']:"";
       
            if($adminname!=''){
                $where.=" and adminname like '%$adminname%' ";
            }
            if($roles!=''){
                $where.=" and roleid=$roles ";
            }
            if($status!=''){
                $where.=" and status =$status ";
            }
            $list = $DB_admin->where($where)->limit("$first,$limit")->order('adminid asc')->select();
            for($i=0;$i<count($list);$i++){
                unset($list[$i]['password']);
                $list[$i]['rolename']=self::get_role($list[$i]['roleid'])['rolename'];
                $list[$i]['addtime'] = date("Y-m-d H:i:s",$list[$i]['addtime']);
                $list[$i]['logintime'] =$list[$i]['logintime']==0?'':date("Y-m-d H:i:s",$list[$i]['logintime']);
            }
            $total = $DB_admin->where($where)->total();
            $data['code']=0;
            $data['msg']='';
            $data['count']=$total;
            $data['data']=$list;
            return_json($data);
        }else{
            $role_list = self::get_role();
            include $this->admin_tpl('admin_list');
        }
    }
    
    /**
     * 编辑管理员
     */
    public function edit(){
        $DB_admin =D('admin');
        $admin_role = D('admin_role');
        $role_list = (self::get_role());
        if(isset($_POST['dosubmit'])){
            unset($_POST["adminname"]);
            if($_POST['password']){
                if($_POST['password']!=$_POST['password2']){
                    return_json(['status'=>0,'message'=>'您输入的两次密码不一样，请重新输入']);
                }
                $_POST['password'] = password($_POST['password']);
            }else{
                unset($_POST['password']);
            }
            $r = $admin_role->field('rolename')->where(array('roleid' => $_POST['roleid']))->find();
            //$_POST['rolename'] = $r['rolename'];
            if($_POST['adminid']==1&&$_POST['status']==0){
                return_json(['message'=>'id为1的系统管理员无法被“禁用”！']);exit;
            }
            $update = $DB_admin->update($_POST, array('adminid' => $_POST['adminid']));
            if($update){
                return_json(array('status'=>1,'message'=>L('operation_success')));
            }else{
                return_json(['message'=>'你没做任何修改!!']);
            }
           
        }else{
            $adminid=$_GET['adminid'];
            $data = $DB_admin->where(['adminid'=>$adminid])->find();
            include $this->admin_tpl('admin_edit');
        }
    }
    
    /**
     * 添加管理员
     */
    public function add(){
        $DB_admin =D('admin');
        if(isset($_POST['dosubmit'])){
            if(!check_token($_POST['token'])){
                return_json(array('status'=>0,'message'=>L('lose_parameters')));
            }
            $res=$DB_admin->where(['adminname'=>$_POST['adminname']])->find();
            if($res){
                $return=['status'=>0,'message'=>'登录名已存在'];
            }else{
                unset($_POST['dosubmit']);
                $_POST['password']=password($_POST['password']);
                $_POST['rolename']=$role_list =self::get_role( $_POST['roleid'])['rolename'];
                $_POST['addtime']=time();
                $DB_admin->insert($_POST,true);
                $return = ['status'=>1,'message'=>'添加成功'];
            }
            return_json($return);
        }else{
            $role_list = (self::get_role());
            include $this->admin_tpl('admin_add');
        }
    }
    
    /**
     * 编辑用户信息
     */
    public function public_edit_info()
    {
        $adminid = $_SESSION['adminid'];
        if(isset($_POST['dosubmit'])) {
            if(D('admin')->update(['email'=>$_POST['email']],['adminid'=>$adminid])){
                showmsg(L('operation_success'));
            }else{
                showmsg(L('data_not_modified'));
            }
        }else{
            $data = D('admin')->where(array('adminid'=>$adminid))->find();
            $data['rolename']=self::get_role($data['roleid'])['rolename'];
            include $this->admin_tpl('public_edit_info');
        }
    }
    
    /*
     * 修改密码
     */
    public function public_edit_pwd(){
        $adminid = $_SESSION['adminid'];
        if(isset($_POST['dosubmit'])) {
            if(!$_POST['password']) showmsg(L('data_not_modified'));
            $admin = D('admin');
            if(!$admin->where(array('adminid' => $adminid,'password' => password($_POST['old_password'])))->find()) showmsg('旧密码不正确！');
            if($admin->update(array('password' => password($_POST['password'])), array('adminid' => $adminid))){
                showmsg(L('operation_success'));
            }else{
                showmsg(L('data_not_modified'));
            }
        }else{
            $data = D('admin')->where(array('adminid'=>$adminid))->find();
            include $this->admin_tpl('public_edit_pwd');
        }
    }
    
    /*
     *  获取角色表权限名称
     */
    public function get_role($roleid=''){
        $admin_role = D('admin_role');
        if($roleid){
            $getlist=$admin_role->field('rolename')->where(['roleid'=>$roleid])->find();
        }else{
            $getlist = $admin_role->field('roleid,rolename,description')->where(['disabled'=>0])->select();
        }
        return $getlist;
    }
    
    
}