<?php 

	define('templates',true);
	//调用前端组件的标记
	define('exp',true);
	//调用后台函数文件的标记

	require dirname(__FILE__).'/../../function/database.class.php';
	require dirname(__FILE__).'/../../function/common.php';

	if( !isset($_SESSION['level']) || $_SESSION['level']!='1'){
		_location('您不具有管理员权限！','./index.php');
	}


	include dirname(__FILE__).'/include/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>用户管理</title>
</head>
<body>
	
	<script type="text/javascript" src="./templates/default/js/function.js"></script>

	<div class="container">
		系统用户管理 &nbsp;><br><br>
		<div>
			<table class='table-search-result table-user-manage'>
				<thead>
					<tr>
						<th>登录账号</th>
						<th>用户名</th>
						<th>密 码</th>
						<th>权 限</th>
						<th>操 作</th>
					</tr>
				</thead>
				<tbody>	
					<?php echo user_manage();?>
				</tbody>
		 	</table>
		</div>
	</div>

	<div class="popup-bg-adduser" id="popup-bg-adduser">
		<div class="mask">
			<button class="mask-close" onclick="close_popup_adduser()"><span aria-hidden="true">×</span></button>
			<p class="t">添加用户</p>			
		
			<form class="mask-form">
				<div class="new_user_info">
					<div><span class="title">账 号：</span><input type="text" class="class-input" id="number" ></div>
					<div><span class="title">用户名：</span><input type="text" class="class-input" id="added_user_name"></div>
					<div><span class="title">密 码：</span><input type="text" class="class-input" id="pwd"></div>
				</div>
				<button type="submit" class="mask-submit" onclick="add_user_submit()">提交</button>
			</form>
		
		</div>
	</div><!--end change_pwd -->
</body>
</html>