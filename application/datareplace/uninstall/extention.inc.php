<?php
defined('IN_YZMPHP') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
$menu->delete(array('m'=>'datareplace', 'c'=>'index'));
