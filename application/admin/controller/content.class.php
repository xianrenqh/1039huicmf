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
        $DB_content =D('content');
        $total=$DB_content->total();
        if(isset($_POST['do']) ){
            $page =$_POST['page'];
            $limit = $_POST['limit'];
            $first = ($page - 1) * $limit;
            $where='1=1';
            $list = $DB_content->where($where)->limit("$first,$limit")->order('id desc')->select();
            $total = $DB_content->where($where)->total();
            for($i=0;$i<count($list);$i++){
                $list[$i]['inputtime']=date("Y-m-d H:i:s",$list[$i]['inputtime']);
            }
            $data['code']=0;
            $data['msg']='';
            $data['count']=$total;
            $data['data']=$list;
            return_json($data);
        }else{
            include $this->admin_tpl('content_list');
        }
    }
    
    //添加文章内容
    public function add()
    {
        $DB_content =D('content');
        if(isset($_POST['dosubmit'])){
            if(!check_token($_POST['token'])){
                return_json(array('status'=>0,'message'=>L('lose_parameters')));
            }
            unset($_POST['dosubmit']);
            $_POST['title']=$_POST['title'];
            $_POST['catid']=$_POST['catid'];
            $_POST['keywords']=$_POST['keywords'];
            $_POST['description']=$_POST['description'];
            $_POST['content']=$_POST['content'];
            $_POST['status']=$_POST['status'];
            $_POST['inputtime']=time();
            $DB_content->insert($_POST,true);
            $return = ['status'=>1,'message'=>'添加成功'];
            return_json($return);
        }else{
            include $this->admin_tpl('content_add');
        }
    }
    
    //编辑文章内容
    public function edit()
    {
        $DB_content =D('content');
        $id = $_GET['id'];
        if(isset($_POST['dosubmit'])){
            $update = $DB_content->update($_POST,['id'=>$_POST['id']]);
            if($update){
                return_json(array('status'=>1,'message'=>L('operation_success')));
            }else{
                return_json(['message'=>'你没做任何修改!!']);
            }
        }else{
            $data = $DB_content->where(['id'=>$id])->find();
            include $this->admin_tpl('contelt_edit');
        }
    }
    
    //删除单条文章内容
    public function del_one()
    {
        $DB_content =D('content');
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if($DB_content->delete(['id'=>$id])){
            showmsg(L('operation_success'));
        }else{
            showmsg(L('operation_failure'));
        }
    }
    
}