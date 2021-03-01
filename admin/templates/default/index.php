<?php
	define('exp',true);
	//防止函数库被误调用

	require_once "./function/common.php";
	if( !isset($_SESSION['user_name'])) _location("请先登录","../../login.php");

	define('templates',true);
	define('index',true);
	//防止模板文件被恶意调用
	
	include dirname(__FILE__).'/include/header.php';
	include dirname(__FILE__).'/include/body.php';
	include dirname(__FILE__).'/include/footer.php';
?>