<?php
	
	
	

	define('templates',true);
	//调用前端组件的标记
	define('exp',true);
	//调用后台函数文件的标记

	require dirname(__FILE__).'/../../function/database.class.php';
	require dirname(__FILE__).'/../../function/common.php';
 
	if( !if_cur_course($_SESSION['uid'])) exit;
	//防止通过url恶意调用
	
	if(!isset($_SESSION['user_name'])){
		_location('请先登录','../../login.php');
	}

	$exp_name = $_GET['exp_name'];
	include dirname(__FILE__).'/include/header.php';
	include dirname(__FILE__)."/include/cur_info_{$exp_name}.php";
	include dirname(__FILE__).'/include/footer.php';
	
?>