<?php
/**
 * extention.func.php   用户自定义函数库
 *
 * User: 小灰灰
 * Date:  2019/05/01 8:25
 * Other:
 */

/**
 * 返回json数组
 * @param $arr
 * @return string
 */
function return_json($arr = []){
    header('Content-Type:application/json; charset=utf-8');
    die(json_encode($arr));
}

/**
 * 获取系统配置信息
 * @param $key 键值，可为空，为空获取整个数组
 * @return array|string
 */
function get_config($key = ''){
    if(!$configs = getcache('configs')){
        $data = D('config')->where(array('status'=>1))->select();
        $configs = array();
        foreach($data as $val){
            $configs[$val['name']] = $val['value'];
        }
        setcache('configs', $configs);
    }
    if(!$key){
        return $configs;
    }else{
        return array_key_exists($key, $configs) ? $configs[$key] : '';
    }
}


function password($pass) {
    return md5(substr(md5(trim($pass)), 3, 26));
}


//删除数组中的键值
//array_flip之后unset,这种方法有一个弊端，就是反转后由于有两个键值都为qq，有一个数据将会丢失，所以在使用时请谨慎）
function delByValue($arr, $value){
    $tempArr = array_flip($arr);
    unset($tempArr[$value]);
    return array_flip($tempArr);
}

//删除数组中的某个元素
function delKey($arr,$value){
    $key = array_search($value, $arr);
    if ($key !== false)
        array_splice($arr, $key, 1);
    return $arr;
}



/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $v) {
        if ($v['parentid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 子元素计数器
 * @param array $array
 * @param int   $pid
 * @return array
 */
function array_children_count($array, $pid)
{
    $counter = [];
    foreach ($array as $item) {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }
    return $counter;
}
/**
 * 构建层级（树状）数组
 * @param array  $array          要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid_name       父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid_name = 'parentid', $child_key_name = 'children')
{
    $counter = array_children_count($array, $pid_name);
    if (!isset($counter[0]) || $counter[0] == 0) {
        return $array;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0) {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0) {
            array_push($array, $temp);
        } else {
            if ($temp[$pid_name] == 0) {
                $tree[] = $temp;
            } else {
                $array = array_child_append($array, $temp[$pid_name], $temp, $child_key_name);
            }
        }
        $counter = array_children_count($array, $pid_name);
    }
    return $tree;
}
/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name)
{
    foreach ($parent as &$item) {
        if ($item['id'] == $pid) {
            if (!isset($item[$child_key_name])) {
                $item[$child_key_name] = [];
            }
            
            $item[$child_key_name][] = $child;
        }
    }
    return $parent;
}