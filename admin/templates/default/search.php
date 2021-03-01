<?php 
	
	define('templates',true);
	//调用前端组件的标记
	define('exp',true);
	//调用后台函数文件的标记

	require dirname(__FILE__).'/../../function/database.class.php';
	require dirname(__FILE__).'/../../function/common.php';


	if(!isset($_SESSION['user_name'])){
		_location('请先登录','../../login.php');
	}




	include dirname(__FILE__).'/include/header.php';
	
?>
	<script type="text/javascript" src="./templates/default/js/function.js"></script>

	<div class="container">
	
		<?php 
		if( !isset($_POST['stu_num']) && !isset($_POST['date'])){
			echo "历史数据查询 &nbsp;><br><br>";
			if( $_GET['search']=='stu_num'){
				$out = "
					<div>
						<form action='./index.php?search=stu_num' method='POST'>
							<p>
								按学号：<input type='text' name='stu_num' class='search-input'>
								<button type='submit' class='search-button'>查询</button>
							</p>
						</form>
					</div>";
			}else if($_GET['search']=='date'){
				$out = "
						<div>
							<form action='./index.php?search=date' method='POST'>
								<p>
									按日期：
									<input type='date' name='date'>
									<button type='submit' class='search-button'>查询</button>
								</p>
							<form>
						</div>";
			}
			echo $out;
		}else{
			
			
			if(isset($_POST['stu_num'])){
				echo "查询结果（按学号）><br><br><br>";
				$out = "
				<table class='table-search-result'>
					<tr>
						<th>日期</th>
						<th>实验名称</th>
						<th>求助次数</th>
						<th>未通过次数</th>
						<th>成绩</th>
						<th></th>
					<tr>";
				echo $out;
				echo search_info_num($_POST['stu_num']);
				echo "</table>";
			}else if(isset($_POST['date'])){
				echo "查询结果（按日期）><br><br><br>";
				$out = "
				<table class='table-search-result'>
					<tr>
						<th>时间</th>
						<th>实验名称</th>
						<th>任课教师</th>
						<th></th>
					<tr>";
				echo $out;
				echo search_info_date($_POST['date'],$_SESSION['user_name']);
				echo "</table>";
			}

		}
		?>

	
	</div>

<?php
	include dirname(__FILE__).'/include/footer.php';

?>
