<?php
	session_start();
	
	
	
	if(!isset($_SESSION['user_name'])){
		exit;
	}	//防止恶意调用，其实不管它也没关系~~
	else{
		define('exp',true);
		//调用函数库的标记

		require './function/common.php';

		session_destroy();
		_location(null,'./login.php');
	}


?>