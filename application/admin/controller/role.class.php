<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date:  2019/05/02 8:19
 * Other:
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);
class role extends common {
	
	public function init(){
        $total = D('admin_role')->total();
        $list = D('admin_role')->order('roleid asc')->select();
        if(isset($_POST['do'])){
            $data['code']=0;
            $data['msg']='';
            $data['count']=$total;
            $data['data']=$list;
            return_json($data);
        }else{
            include $this->admin_tpl('role_list');
        }
        
	}
	
	
    //添加角色
	public function add(){
        $admin_role = D('admin_role');
        if(isset($_POST['dosubmit'])) {
            $res=$admin_role->where(['rolename'=>$_POST['rolename']])->find();
            if($res){
                $return=['status'=>0,'message'=>'角色名称已存在'];
            }else{
                $_POST['system'] = 0;
                unset($_POST['dosubmit']);
                $admin_role->insert($_POST, true);
                $return = ['status'=>1,'message'=>'添加成功'];
            }
            return_json($return);
        }else{
            include $this->admin_tpl('role_add');
        }
    }

    //修改角色
    public function edit(){
        $admin_role = D('admin_role');
        if(isset($_POST['dosubmit'])) {
            $update = $admin_role->update($_POST,['roleid'=>$_POST['roleid']]);
            if($update){
                return_json(array('status'=>1,'message'=>L('operation_success')));
            }else{
                return_json(['message'=>'你没有做任何修改']);
            }
        }else{
            $roleid = $_GET['roleid'];
            $data = $admin_role->where(['roleid'=>$roleid])->find();
            include $this->admin_tpl('role_edit');
        }
    }
    
    /**
     * 权限管理
     */
    public function role_priv(){
        $admin_role = D('admin_role');
        if(isset($_POST['dosubmit'])) {
            $admin_role_priv = D('admin_role_priv');
            if (is_array($_POST['menuid']) && count($_POST['menuid']) > 0) {
        
                $admin_role_priv->delete(array('roleid'=>$_POST['roleid']));
                $menuinfo = D('menu')->field('`id`,`m`,`c`,`a`,`data`')->select();

                foreach ($menuinfo as $_v) $menu_info[$_v['id']] = $_v;
                foreach($_POST['menuid'] as $menuid){
                    $info = array();
                    $info = $menu_info[$menuid];
                    if($info['m'] == '') continue;
                    $info['roleid'] = $_POST['roleid'];
                    $info['authid'] = $info['id'];
                    $admin_role_priv->insert($info, false, false);
                }
            } else {
                $admin_role_priv->delete(array('roleid'=>$_POST['roleid']));
            }
    
            delcache('menu_string_'.$_POST['roleid']);
            showmsg(L('operation_success'));
        }else{
            $roleid = isset($_GET['roleid']) ? intval($_GET['roleid']) : 0;
            if($roleid == 1) showmsg('超级管理员权限不允许修改！');
    
            $tree = yzm_base::load_sys_class('tree');
            $tree->icon = array('│ ','├─ ','└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $data = D('menu')->order('listorder ASC,id DESC')->select();
            $priv_data = D('admin_role_priv')->where(array('roleid'=>$roleid))->select();
            
            foreach($data as $k=>$v) {
                $data[$k]['level'] = $this->get_level($v['id'],$data);
                $data[$k]['checked'] = ($this->is_checked($v,$roleid,$priv_data))? ' checked' : '';
            }

            $data=array2level($data);
            $checkedId =array_column($priv_data, 'authid');
            $checkIds=[];
            foreach($checkedId as $k=> $v){
                $checkIds[]= strval($v);
            }
            $returndata = ['code'=>0,'msg'=>'获取成功',
                'data'=>[
                    'list'=>$data,
                    'checkedId'=> $checkIds
                ]
            ];
            if(isset($_GET['do'])){
                return_json($returndata);
            }else{
                include $this->admin_tpl('role_priv');
            }
           
            
            /*$str  = "<tr>
						<td><label>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</label></td>
					</tr>";
            $tree->init($data);
            $menus = $tree->get_tree(0, $str);
            include $this->admin_tpl('role_priv');*/
        }
    }
    
    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    private function get_level($id, $array=array(), $i=0) {
        foreach($array as $n=>$value){
            if($value['id'] == $id){
                if($value['parentid']== '0') return $i;
                $i++;
                return $this->get_level($value['parentid'],$array,$i);
            }
        }
    }
    
    
    /**
     *  检查指定菜单是否有权限
     * @param array $data menu表中数组
     * @param int $roleid 需要检查的角色ID
     */
    private function is_checked($data,$roleid,$priv_data) {
        $priv_arr = array('m','c','a','data');
        if($data['m'] == '') return false;
        foreach($data as $key=>$value){
            if(!in_array($key,$priv_arr)) unset($data[$key]);
        }
        $data['roleid'] = $roleid;
        return in_array($data, $priv_data) ? true : false;
    }

}


