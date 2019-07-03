<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/6/8 0008
 * Time: 9:13
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);

class index extends common{
    public function init(){
        $admin = D('admin');
        if(isset($_GET['action']) && $_GET['action']=="getfields"){
            $exptable = $_GET['exptable'];
            $tableName = $admin->fetch_all($admin->query(" select COLUMN_NAME from information_schema.COLUMNS where table_name = '$exptable' and TABLE_SCHEMA='".C('db_name')."' "));
            $tableNames='';
            foreach($tableName as $v){
                $tableNames .='<a href=\'javascript:pf("'.$v['COLUMN_NAME'].'")\'><u>'.$v['COLUMN_NAME'].'</u></a>' ;
            }
            return_json(['names'=>$tableNames]);
        }else{
            $exptable_list = $admin->fetch_all($admin->query('SHOW TABLE STATUS'));
            if($exptable_list==''){
                echo "<font>找不到你所指定的数据库！ </font><br>";
            }
            include $this->admin_tpl('datareplace');
        }
    }
    
    //执行替换sql
    public function dosql(){
        if (empty($_SESSION['code']) || strtolower($_POST['code']) != $_SESSION['code']) {
            $_SESSION['code'] = '';
            return_json(['status'=>0,'message'=>'安全码不正确！','icon'=>2]);
        }else{
            $condition = empty($condition) ? '' : " WHERE $condition ";
            $rpfield= $_POST['rpfield'];
            $rpstring = $_POST['rpstring'];
            $tostring = $_POST['tostring'];
            $exptable = $_POST['exptable'];
            $admin = D('admin');
            $rs1 = $admin->query(" UPDATE `$exptable` SET $rpfield=REPLACE($rpfield,'$rpstring','$tostring') ");
            $rs2 = $admin->fetch_all($rs1);
            $rs3= $admin->fetch_all($admin->query("OPTIMIZE TABLE `$exptable`"));
            if($rs2!=''){
                return_json(['status'=>200,'message'=>'成功完成数据替换！','icon'=>1]);
                exit();
            }else{
                return_json(['status'=>201,'message'=>'数据替换失败！','icon'=>2]);
                exit();
            }
        }
    }
    
    
}