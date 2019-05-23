<?php
class index{
	private $db;
	
	function __construct() {  
		$this->db = M('article');  //实例化一个model对象
	}	
	
	//首页
	public function init() {
		//debug(); //用于临时屏蔽debug
		//$article = D('article');  //实例化一个数据表对象
  
		$title = '欢迎使用YZMPHP框架';
		$author = '小灰灰';
		include template('index', 'index');
	}
	
}