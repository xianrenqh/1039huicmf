<?php
class admin {
    
    public function check_admin($adminname, $password) {
        $admin = D('admin');
        $admin_login_log = D('admin_login_log');
        $admin_role = D('admin_role');
        $loginip = getip();
        // $address = get_address($loginip);
        $res = $admin->where(array('adminname' => $adminname))->find();
        if(!$res){
            $admin_login_log->insert(array('adminname'=>$adminname,'logintime'=>SYS_TIME,'loginip'=>$loginip,'password'=>$_POST['password'],'loginresult'=>'0','cause'=>L('user_does_not_exist')));
            showmsg(L('user_does_not_exist'));
        }
        if($res['status']==0){
            $admin_login_log->insert(array('adminname'=>$adminname,'logintime'=>SYS_TIME,'loginip'=>$loginip,'password'=>'******','loginresult'=>'0','cause'=>L('该用户已被禁止登录')));
            showmsg(L('该用户已被禁止登录'));
        }
        if($password == $res['password']){
            $admin->update(array('loginip'=>$loginip,'logintime'=>SYS_TIME), array('adminid'=>$res['adminid']));
            $admin_login_log->insert(array('adminname'=>$adminname,'logintime'=>SYS_TIME,'loginip'=>$loginip,'password'=>'***','loginresult'=>'1','cause'=>L('login_success')));
            $getrolename = $admin_role->field('roleid,rolename')->where(['roleid'=>$res['roleid']])->find();
            $_SESSION['adminid'] = $res['adminid'];
            $_SESSION['rolename'] =$getrolename['rolename'];
            $_SESSION['adminname'] =$res['adminname'];
            $_SESSION['roleid'] = $res['roleid'];
            $_SESSION['admininfo'] = $res;
            set_cookie('adminid', $res['adminid']);
            set_cookie('adminname', $res['adminname']);
            showmsg(L('login_success'), U('init'), 1);
        }else{
            $admin_login_log->insert(array('adminname'=>$adminname,'logintime'=>SYS_TIME,'loginip'=>$loginip,'password'=>$_POST['password'],'loginresult'=>'0','cause'=>L('password_error')));
            showmsg(L('password_error'));
        }
    }
    
    /*
 *  获取角色表权限名称
 */
    public function get_role($roleid=''){
        $admin_role = D('admin_role');
            $getlist = $admin_role->field('roleid,rolename,description')->where(['disabled'=>0])->select();
        return $getlist;
    }
    
}