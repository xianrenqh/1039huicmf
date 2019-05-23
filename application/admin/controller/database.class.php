<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date:  2019/05/05 11:33
 * Other: 数据备份操作
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);
yzm_base::load_sys_class('databack', '', 0);

class database extends common{
    
    public $config;
    public function __construct() {
        parent::__construct();
        $this->config = array(
            'path'     => YZMPHP_PATH.'cache'.DIRECTORY_SEPARATOR.'databack'.DIRECTORY_SEPARATOR, //备份文件目录
            'part'     => 2097152, //2MB
            'compress' => 1,    //是否压缩
            'level'    => 4,	//压缩水平
        );
    }
    
    /**
     * 数据库列表
     */
    public function init() {
        $admin = D('admin');
        $data = $admin->fetch_all($admin->query('SHOW TABLE STATUS'));
        for($i=0;$i<count($data);$i++){
            $data[$i]['Data_length']=sizecount($data[$i]['Data_length']);
        }
        if(isset($_GET['do'])){
            $return = ["code"=>0,'msg'=>'获取成功','count'=>count($data),'data'=>$data];
            return_json($return);
        }else{
            include $this->admin_tpl('database_list');
        }
    }
    
    
    
    /**
     * 优化表
     */
    public function public_optimize() {
        $tables = isset($_POST['tables']) ? $_POST['tables'] : trim($_GET['tables']);
        if(is_array($tables)){
            if($tables[0]!=''){
                $tables=implode(',',$tables);
                D('admin')->query("OPTIMIZE TABLE $tables");
                $return = ['message'=>'操作成功！','icon'=>1];
            }else{
                $return =['message'=>'请指定要优化的表！','icon'=>2];
            }
        }else{
            D('admin')->query("OPTIMIZE TABLE $tables");
            $return = ['message'=>'操作成功！','icon'=>1];
        }
        return_json($return);
    }
    
    /**
     * 修复表
     */
    public function public_repair() {
        $tables = isset($_POST['tables']) ? $_POST['tables'] : trim($_GET['tables']);
        if(is_array($tables)){
            if($tables[0]!=''){
                $tables=implode(',',$tables);
                D('admin')->query("REPAIR TABLE $tables");
                $return = ['message'=>'操作成功！','icon'=>1];
            }else{
                $return =['message'=>'请指定要修复的表！','icon'=>2];
            }
        }else{
            D('admin')->query("REPAIR TABLE $tables");
            $return = ['message'=>'操作成功！','icon'=>1];
        }
        return_json($return);
    }
    
    /**
     * 表结构
     */
    public function public_datatable_structure() {
        $table = isset($_GET['table']) ? trim($_GET['table']) : '';
        if(!$table) showmsg('请选择表!');
        $admin = D('admin');
        $data = $admin->fetch_array($admin->query('SHOW CREATE TABLE '.$table));
        include $this->admin_tpl('datatable_structure');
    }
    
    /**
     * 数据库备份列表
     */
    public function databack_list() {
        $data = array();
        $list = glob($this->config['path'].'*');
        foreach($list as $name){
            
            if(preg_match('/^[a-z]{6}-\d{8}-\d{6}-\d+\.sql(?:\.gz)?$/', basename($name))){
                $info['filesize'] = sizecount(filesize($name));
                $info['filename'] = basename($name);
                $name = sscanf($info['filename'], '%6s-%4s%2s%2s-%2s%2s%2s-%d');
                $info['backtime'] = $name[1].'-'.$name[2].'-'.$name[3].' '.$name[4].':'.$name[5].':'.$name[6];
                $info['random'] = $name[0];
                $info['part'] = $name[7];
                $info['time'] = strtotime($info['backtime']);
                $data[] = $info;
            }
        }
        array_multisort(array_column($data,'time'),SORT_DESC,$data);
        
        include $this->admin_tpl('databack_list');
    }
    
    /**
     * 数据库导出备份(此方法名不能更改export_list，因为在记录系统日志情况下会报错:1062)
     */
    public function export_list(){
        if (isset($_POST['dosubmit'])) {
            $tables = isset($_POST['tables']) ? $_POST['tables'] : '';
            if(!$tables) return_json(['message'=>'请指定要备份的表！']);
    
            //备份目录不存在，先创建目录
            if(!is_dir($this->config['path'])){
                @mkdir($this->config['path'], 0755, true);
                @file_put_contents($this->config['path'].'index.html', '');
            }
    
            //检查是否有正在执行的任务
            $lock = $this->config['path'].'backup.lock';
            if(is_file($lock)){
                return_json(['message'=>'检测到有一个备份任务正在执行，请稍后再试！','status'=>201]);
                //showmsg('检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                $len = @file_put_contents($lock, SYS_TIME); //创建锁文件
            }
            if(!$len)
                return_json(['message'=>$this->config['path'].'目录不存在或不可写，请检查！']);
    
            $_SESSION['backup_config'] = $this->config;
    
            //生成备份文件信息
            $file = array(
                'name' => random(6, 'abcdefghigklmzopqrstuvwxyz').'-'.date('Ymd-His'),
                'part' => 1,
            );
            $_SESSION['backup_file'] = $file;
            //缓存要备份的表
            $_SESSION['backup_tables'] = $tables;
    
            //创建备份文件
            $database = new databack($file, $this->config);
            if(false !== $database->create()){
                $tab = array('id' => 0, 'start' => 0);
                return_json(['message'=>'初始化成功！','href'=>U('export_list', $tab),'start'=>1]);
                //showmsg('初始化成功！', U('export_list', $tab), 1);
            } else {
                return_json(['message'=>'初始化失败，备份文件创建失败！']);
            }
        }elseif (isset($_GET['id']) && isset($_GET['start'])) {
    
            $tables = $_SESSION['backup_tables'];
            $id = intval($_GET['id']);
            $start = intval($_GET['start']);
            $database = new databack($_SESSION['backup_file'], $_SESSION['backup_config']);
            $start  = $database->backup($tables[$id], $start);
            if(false === $start){  //出错
                return_json(['message'=>'备份出错']);
            } elseif (0 === $start) { //下一表
                if(isset($tables[++$id])){
                    $tab = array('id' => $id, 'start' => 0);
                    showmsg('表'.$tables[$id].'备份完成！', U('export_list', $tab), 0.1);
                } else {   //备份完成，清空缓存
                    @unlink($_SESSION['backup_config']['path'].'backup.lock');
                    unset($_SESSION['backup_tables'],$_SESSION['backup_file'],$_SESSION['backup_config']);
                    showmsg('备份全部完成！', U('databack_list'), 2);
                }
            } else {
                $tab  = array('id' => $id, 'start' => $start[0]);
                $rate = floor(100 * ($start[0] / $start[1]));
                showmsg('表'.$tables[$id].'正在备份...('.$rate.'%)', U('export_list', $tab), 0.1);
            }
    
        } else {
            showmsg('参数错误！');
        }
    }
    
    /**
     * 备份文件下载
     */
    public function databack_down() {
        if(!isset($_GET['random']) || !isset($_GET['time']) || !isset($_GET['part'])) showmsg('请指定要下载的文件!');
        
        $filename = $_GET['random'].'-'.date('Ymd-His', intval($_GET['time'])).'-'.intval($_GET['part']).'.sql';
        if($this->config['compress']) $filename .= '.gz';
        
        if(!is_file($this->config['path'].$filename)) showmsg($filename.'文件不存在!', 'stop');
        file_down($this->config['path'].$filename);
    }
    
    /**
     * 备份文件删除
     */
    public function databack_del() {
        if(!isset($_GET['random']) || !isset($_GET['time'])) showmsg('请指定要删除的文件!');
        
        $filename  = $_GET['random'].'-'.date('Ymd-His', intval($_GET['time'])) . '-*.sql*';
        $path  = $this->config['path'].$filename;
        array_map('unlink', glob($path));
        if(count(glob($path))){
            showmsg('备份文件删除失败，请检查权限！');
        } else {
            showmsg('备份文件删除成功！','',1);
        }
    }
    
    
    /**
     * 数据库导入
     */
    public function import() {
        if(isset($_GET['time'])) {
            $filename  = $_GET['random'].'-'.date('Ymd-His', intval($_GET['time'])) . '-*.sql*';
            $path  = $this->config['path'].$filename;
            $files = glob($path);
            $list  = array();
            foreach($files as $name){
                $basename = basename($name);
                $match    = sscanf($basename, '%6s-%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^[a-z]{6}-\d{8}-\d{6}-\d+\.sql.gz$/', $basename);
                $list[$match[7]] = array($match[7], $name, $gz);
            }
            
            ksort($list);
            
            //检测文件正确性
            $last = end($list);
            if(count($list) === $last[0]){
                //缓存备份列表
                $_SESSION['backup_list'] = $list;
                showmsg('初始化成功！', U('import', array('part' => 1, 'start' => 0)), 1);
            } else {
                showmsg('备份文件可能已经损坏，请检查！');
            }
        } elseif(isset($_GET['part']) && isset($_GET['start'])) {
            $part = intval($_GET['part']);
            $start = intval($_GET['start']);
            $list  = $_SESSION['backup_list'];
            if(!isset($list) || !is_array($list)) showmsg('非法操作！');
            
            $databack = new databack($list[$part], array('path' => $this->config['path'],'compress' => $list[$part][2]));
            
            $start = $databack->import($start);
            
            if(false === $start){
                showmsg('还原数据出错！');
            } elseif(0 === $start) { //下一卷
                if(isset($list[++$part])){
                    $data = array('part' => $part, 'start' => 0);
                    showmsg('正在还原：卷'.$part.'...', U('import', $data), 1);
                } else {
                    unset($_SESSION['backup_list']);
                    showmsg('还原完成！', U('databack_list'), 2);
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if($start[1]){
                    $rate = floor(100 * ($start[0] / $start[1]));
                    showmsg('正在还原：卷'.$part.'...('.$rate.'%)', U('import', $data), 1);
                } else {
                    $data['gz'] = 1;
                    showmsg('正在还原：卷'.$part.'...', U('import', $data), 1);
                }
            }
            
        } else {
            showmsg('参数错误！');
        }
    }
    
}