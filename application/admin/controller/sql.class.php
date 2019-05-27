<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date: 2019/5/26 0026
 * Time: 17:21
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);

class sql extends common{
    /**
     * SQL命令行
     */
    public function init() {
        include $this->admin_tpl('sql');
    }
    
    /**
     * 执行SQL命令
     */
    public function do_sql(){
        if(isset($_POST['sqlstr'])){
            if(!C('sql_execute')) {
                return_json(['status'=>201,'message'=>'根据系统配置，不允许在线执行SQL命令！']);
            }else{
                $sqlstr = MAGIC_QUOTES_GPC ? stripslashes($_POST['sqlstr']) : $_POST['sqlstr'];
                $sqlstr = rtrim(trim($sqlstr), ';');
                $sqls = $_POST['action']=='many' ? explode(';', $sqlstr) : array(0 => $sqlstr);
                $admin = D('admin');
       
                foreach($sqls as $sql){
                    $sql = trim($sql);
                    if(empty($sql)) continue;
                    if(stristr($sql, '.php')){
                        return_json(['status'=>201,'message'=>'ERROR : 检测到非法字符 “.php” ！']);
                    }
                    if(stristr($sql, 'outfile')){
                        return_json(['status'=>201,'message'=>'ERROR : 检测到非法字符 “outfile”！']);
                    }
                    if(stristr($sql, 'concat')){
                        return_json(['status'=>201,'message'=>'ERROR : 检测到非法字符 “concat” ！']);
                    }
                    if(preg_match("/^drop(.*)database/i", $sql)){
                        return_json(['status'=>201,'message'=>'ERROR : 不允许删除数据库！']);
                    }
                    $result = $admin->query($sql);
                    if($result){
                        if(!preg_match("/^(?:UPDATE|DELETE|TRUNCATE|ALTER|DROP|FLUSH|INSERT|REPLACE|SET|CREATE)\\s+/i", $sql)){
                            $data = $admin->fetch_all($result);
                            $keys = array_keys($data[0]);
                            $data_key='';
                            foreach($keys as $val){
                                $data_key[]='<th>'.$val.'</th>';
                            }
                            $data_row='';
                            foreach($data as $row){
                                $data_row.= '<tr>';
                                foreach($row as $val){
                                    $data_row.= '<td>'.$val.'</td>';
                                }
                                $data_row.= '</tr>';
                            }
                            return_json(['status'=>200,'message'=>'<span style="color:green">OK : 执行成功！</span>','data'=>['data_key'=>$data_key,'data_row'=>$data_row]]);
                        }
                    }else{
                        return_json(['status'=>201,'message'=>'ERROR : 执行失败！']);
                        break;
                    }
                }
            }

  
        }
    
    }
    
    
}