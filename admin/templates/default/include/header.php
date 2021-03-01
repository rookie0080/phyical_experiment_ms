<?php
	if (!defined('templates')) exit;
	//防止恶意调用
	
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<title>物理实验管理--教师管理页面</title>
	<?php echo "<link rel='stylesheet' type='text/css' href='./templates/default/css/style.css'>";?>
	<?php echo "<link rel='stylesheet' href='./templates/default/css/font-awesome-4.7.0/css/font-awesome.min.css'>";?>

</head>
<body>
	<div class="header">
		<div class="nav">
		<?php
			echo "<ul class='nav_list'";
			if(defined('index')) echo "style='margin-left: 0'";
			echo ">";
		?>
				<li><a class="fa fa-home" href="./index.php">首 页</a></li>
				<li class="trigger_dropdown">
					<a href="javascript:void(0)">课堂管理</a>
					<div class="dropdown">
					<?php
						if( if_cur_course($_SESSION['uid'])){
							echo "<a href='./index.php?exp_name=".cur_course_name($_SESSION['uid'])."'>实时监控</a>";
							echo "<a href='javascript:void(0)' onclick='close_course()'>结束当前课堂</a>";
						}else{
							echo "<p>没有正在进行的课程！</p>";
						}
					?>
					</div>
				</li>
				<li class='trigger_dropdown'>
					<a>历史数据</a>
					<div class="dropdown">
						<a href="./index.php?search=date">按日期</a>
						<a href="./index.php?search=stu_num">按学号</a>
					</div>
				</li>
				<li class='trigger_dropdown'>
					<a>账户管理</a>
					<div class="dropdown">
						<a href="javascript:void(0)" onclick="change_pwd()">修改密码</a>
						<?php 
							if(isset($_SESSION['level']) && $_SESSION['level']=='1'){
								echo "<a href='./index.php?user_manage=true'>系统用户管理</a>";
							}

						?>
						<!-- <a href="javascript:void(0)"></a> -->
					</div>
				</li>
			</ul>
		<?php
			echo "<ul class='nav_option'";
			if(defined('index')) echo"style='margin-right: 0'";
			echo ">";
		?>
				<li><a href="./logout.php">退 出 &nbsp;」</a></li>
				<li><span class="symbol-item">|</span></li>
				<?php echo "<li><a name=".$_SESSION['uid']." id='uid'>「 &nbsp;".$_SESSION['user_name']."</a>";?>
					
				<?php echo"</li>";?>
			</ul>
		</div><!--end nav -->
	</div><!--end header -->

	<div class="popup-bg-changepwd" id="popup-changepwd">
		<div class="mask">
			<button class="mask-close" onclick="close_popup_changepwd()"><span aria-hidden="true">×</span></button>
			<p class="t">修改密码</p>			
		
			<form class="mask-form">
				<div class="class_number">
					<div><span class="title">旧密码：</span><input type="password" class="class-input" id="old_pwd" ></div>
					<div><span class="title">新密码：</span><input type="password" class="class-input" id="new_pwd"></div>
					<div><span class="title">确认密码：</span><input type="password" class="class-input" id=	"pwd_check"></div>
				</div>
				<button type="submit" class="mask-submit" onclick="changepwd_submit()">确认修改</button>
			</form>
		
		</div>
	</div><!--end change_pwd -->
