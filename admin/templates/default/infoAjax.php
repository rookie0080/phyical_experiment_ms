<?php 
	//js请求的目标php文件
	//通过action关键字确定此文件应该调用哪一个函数

	header("Content-Type:text/plain");
	if(!isset($_GET['action'])) exit();
	//防止恶意调用

	define('exp',true);
	//调用后台函数文件的标记

	require dirname(__FILE__).'/../../function/common.php';

	if( isset($_GET['exp_name'])) 
		$exp_name = $_GET['exp_name'];
	//调用与action对应的函数
	if( $_GET['action']=='detail_data'){
		if(isset($_GET['group_num'])) {
			$result = detail_data($exp_name,$_GET['group_num']);
			$result = $result->fetch_assoc();
			// var_dump($result);
			echo json_encode($result);
		}
	}else if( $_GET['action']=='pass'){

		$result =  pass($exp_name,$_GET['option'],$_GET['group_num']);
		$arr = array(
			'status'=>$result
			);
		echo json_encode($arr); 

	}else if( $_GET['action']=='fail'){
		
		$result = fail($exp_name,$_GET['option'],$_GET['group_num']);
		$arr = array(
			'status'=>$result
			);
		echo json_encode($arr);

	}else if( $_GET['action']=='solve_help'){		
		solve_help($exp_name,$_GET['group_num']);

	}else if( $_GET['action']=='course_status'){		
		course_status($_GET['course_id'],$_GET['user_id']);

	}else if( $_GET['action']=='if_cur_course'){
		$result = if_cur_course($_GET['user_id']);
		$arr = array(
			'status' => $result,
			);
		echo json_encode($arr);
	}else if( $_GET['action']=='start_course'){
		//var_dump($_GET['user_id']);
		
		echo $_GET['classNum'];
		$result = start_course($_GET['user_id'],$_GET['course_name'],$_GET['classNum']);
		echo($result);
	}else if( $_GET['action']=='close_course'){
		$close = close_course($_GET['user_id'],$_GET['course_name']);
		$save = save_data($_GET['course_name'],$_GET['user_id']);
		// echo("close:".$close);
		// echo("\nsave:".$save);

		if ($close && $save) 
			$result = 1;
		else $result = 0;
		echo $result;
	}else if( $_GET['action']=='change_pwd'){
		$result = change_pwd($_GET['user_id'],$_GET['old_pwd'],$_GET['new_pwd']);
		echo $result;
	}else if( $_GET['action']=='cur_course_name'){
		$result = cur_course_name_close($_GET['user_id']);
		//echo 'here';
		echo $result;
	}else if( $_GET['action']=='show_detail_via_stu_num'){
		$result = show_detail_via_stu_num($_GET['stu_num'],$_GET['exp_name']);
		echo $result;
	}else if( $_GET['action']=='show_detail_via_date'){
		$result = show_detail_via_date($_GET['exp_time'], $_GET['exp_name']);
		echo $result;
	}else if( $_GET['action']=='admin_change_pwd'){
		$result = admin_change_pwd($_GET['new_pwd'], $_GET['number']);
		echo $result;
	}else if( $_GET['action']=='admin_delete_user'){
		$result = admin_delete_user($_GET['number']);
		echo $result;
	}else if( $_GET['action']=='add_user'){
		$result = add_user($_GET['number'],$_GET['user_name'],$_GET['pwd']);
		echo $result;
	}else if( $_GET['action']=="mark_submit"){
		$result = remark_submit($_GET['exp_name'],$_GET['group_num'],$_GET['remark'],$_GET['grade_modified']);
		echo $result;
	}else if( $_GET['action']=="change_parameter"){
		if( $_GET['exp_name']=="oscillograph"){
			$result = change_parameter_oscillograph($_GET['group_num'], $_GET['v_std'], $_GET['f_std']);
		}else if( $_GET['exp_name']=='potentionter'){
			$result = change_parameter_potentioneter($_GET['group_num'], $_GET['Exs']);
		}
		echo $result;
	}else if( $_GET['action']=='modified_course_status'){
		$result = modified_course_status($_GET['exp_name'],$_GET['user_id']);
		echo $result;
	}else if( $_GET['action']=='modified_course_status_newton'){
		$result = modified_course_status_newton($_GET['user_id'],$_GET['para']);
		echo $result;
	}else if( $_GET['action']=='modified_course_status_spectrometer'){
		$result = modified_course_status_spectrometer($_GET['user_id'],$_GET['para']);
		echo $result;
	}else if( $_GET['action']=='cur_user'){
		$result = cur_user($_GET['course_id']);
		echo $result;
	}else if( $_GET['action']=='data_detail'){
		$result = data_detail($_GET['exp_name'],$_GET['stu_num']);
		echo json_encode($result);

	}
?>


