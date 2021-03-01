<?php
	session_start();
	//作为防止恶意调用公共文件的标记
	define('exp',true);

	require dirname(__FILE__).'/function/common.php';


	if( isset($_GET['action']) && $_GET['action']=='login'){
		$result = check_login($_POST['user_name'],$_POST['password']);
		if( !empty($result)){
			//验证通过
			$_SESSION['level'] = $result['level'];
			$_SESSION['user_name'] = $result['name'];
			$_SESSION['uid'] = $result['uid'];
			_location(null,'./index.php');

		}else{
			_alert_back('用户名或密码错误，忘记密码可联系管理员');
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>物理实验室管理--登录</title>
	<link rel='stylesheet' type='text/css' href='./templates/default/css/style.css'>
	<link rel='stylesheet' href='./templates/default/css/font-awesome-4.7.0/css/font-awesome.min.css'>
	<style type="text/css">
		html{height: 100%;}
		body{
			display: block;
			position: relative;
			padding: 0;
			margin: auto;
			height: 100%;
			/*background-image: radial-gradient(ellipse closest-side, #ffffff, #d4e2af);*/
            /* background:  linear-gradient(180deg, rgb(255, 255, 255) 0%,rgba(5, 56, 5, 0.5) 100%);*/
            /*opacity: 1;*/
			background:url('./templates/default/image/bg.jpg');				
			background-size: cover;
			background-repeat: no-repeat;
			overflow: hidden;
		}
	
	</style>
</head>
<body>
		<div class="login-bg">
			<div class="login-bg-1"></div>
		</div>
		
		<div class='login'>
			<!-- <strong class='login-title'>物理实验管理系统</strong> -->
			<div class="message input-color">物理实验管理系统</div>
			<div class="dark_banner"></div>
			<form action='./login.php?action=login' method='POST' class="login-form">
				<div class="form-icon"><i class="fa fa-user"></i><input type='text' name='user_name' placeholder="用户名" class="login-input"><br></div>
				<div class="form-icon"><i class="fa fa-lock"></i><input type='password' name='password' placeholder="密码" class="login-input"><br></div>
				<input type='submit' value='立即登录' class="login-submit input-color">
			</form>
		</div>

</body>
</html>