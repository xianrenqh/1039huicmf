

<?php
defined('IN_YZMPHP') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu->insert(array('name'=>'数据库内容替换', 'parentid'=>4, 'm'=>'datareplace', 'c'=>'index', 'a'=>'init', 'data'=>'', 'listorder'=>51, 'display'=>'1'));
$menu->insert(array('name'=>'执行替换', 'parentid'=>$parentid, 'm'=>'datareplace', 'c'=>'index', 'a'=>'dosql', 'data'=>'', 'listorder'=>1, 'display'=>'0'));