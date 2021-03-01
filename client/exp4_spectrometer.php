<?php
	//客户端接口

	if(!isset($_GET['client']))exit;
	//防止恶意调用

	require "../admin/config/database.php";
	$db = new mysqli(HOSTNAME, HOSTUSER, HOSTPWD, HOSTDB);
	$db->query('set names utf8');		//防止查询数据库乱码

	//若课堂还未开放或者已经终止，则不允许连接（其实只是为了第一步connect，但是这一步并不是很重要
	$result = $db->query("SELECT `status` FROM `physics_status` WHERE `name`='spectrometer'");
	$result = $result->fetch_assoc();

	if( $result['status']==0){
		$arr = array(
			'status' => '0',
			'msg' => '课堂未开放或者已终止'
		);
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) );
    	exit; 
	}

	//若课堂课堂已结束或已经完成全部实验（除connect和check_info外其它所有会提交group_num的步骤）
	if(isset($_GET['group_num']) && $_GET['action']!='check_info'){
		$group_num = $_GET['group_num'];
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_spectrometer` WHERE `group_num`='{$group_num}'");
		$ifend = $ifend->fetch_assoc();
		$ifend = $ifend['ifend'];
		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
			foreach ( $arr as $key => $value ) {  		//防止中文乱码
	        	$arr[$key] = urlencode ( $value );  
	    	}  
	    	echo urldecode ( json_encode ($arr) );
	    	exit; 
    	}
	}

//具体的接口
	if( isset($_GET['action']) && $_GET['action']=='connect'){
	//测试连接
		$arr = array(
			'status' => '1',
			'msg' => '连接成功'
		);
	}else if( isset($_GET['action']) && $_GET['action']=='login'){
	//接收学生信息	
		$group_num = $_GET['group_num'];
		$stu_num =  $_GET['stu_num'];
		$stu_name = $_GET['stu_name'];
		// var_dump($group_num);  var_dump($stu_num); var_dump($stu_name);
	
		//查询该学号，用于判断是否已经登陆系统
		$obj = $db->query("SELECT `stu_num` FROM `physics_course_spectrometer` WHERE `stu_num`={$stu_num} OR `group_num`='$group_num'");
		$data = $obj->fetch_assoc();
		
		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
		}else if($data['stu_num']!=null){
			$arr = array( 
				'status'=>'0',
				'msg'=>'请勿重复登录！'
			);
		}else{
			//可以登录
			date_default_timezone_set('Asia/Shanghai');
			$cur_time = getdate();
			$hours = $cur_time['hours'];
			$minutes = $cur_time['minutes'];
			$compare = $hours*60 + $minutes;	
			// var_dump($compare);

			//上课开始十五分钟后为迟到	,扣5分		
			$start_time = $db->query("SELECT `start_time` FROM `physics_status` WHERE `name`='potentioneter'");
			$start_time = $start_time->fetch_assoc();
			// var_dump($start_time);
			$start_time = intval($start_time['start_time']);
			 // var_dump($start_time);
			if( $compare - $start_time > 15){
				$data = $db->query("SELECT `grade` FROM `physics_course_spectrometer` WHERE `group_num`={$group_num}");
				$data = $data->fetch_assoc();
				$grade = intval($data['grade']) - 5;		
				//var_dump($grade);
				$reuslt = $db->query("UPDATE `physics_course_spectrometer` SET `grade`=$grade WHERE `group_num`={$group_num}");
			}
			//登录，更新数据库
			$sql = "UPDATE `physics_course_spectrometer` SET `stu_num`='{$stu_num}', `stu_name`='{$stu_name}' WHERE `group_num`='{$group_num}'";
			$result = $db->query($sql);
			if(!$result) {
				$arr = array( 
					'status'=>'0',
					'msg'=>'登录失败'
				);
			}else{
				$arr = array( 
					'status'=>'1',
					'msg'=>'登录成功'
				);
			}
		}


	}else if( isset($_GET['action']) && $_GET['action']=='submit'){
	//接收上传的实验数据
		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT * FROM `physics_course_spectrometer` WHERE `group_num`={$group_num}");
		$result = $result->fetch_assoc();

		$E_yellow_inside = sprintf("%.2f",abs( ($_GET['lambda_yellow_inside']-$result['lambda_2'])/$result['lambda_2']*100))."%";
		$E_yellow_outside = sprintf("%.2f",abs( ($_GET['lambda_yellow_outside']-$result['lambda_3'])/$result['lambda_3']*100))."%";
		$D_color_theoretical = sprintf("%.3f",($result['yellow_outside_angle'] - $result['yellow_inside_angle'])/( $result['lambda_3'] - $result['lambda_2']));
		$E_d = sprintf("%.1f",abs(($result['constant']-$_GET['d'])/$result['constant']*100))."%";

		$result = $db -> query("UPDATE `physics_course_spectrometer` SET `evaluation`=1,`status_1`=2,`green_1`='{$_GET['green_1']}',`green_2`='{$_GET['green_2']}',`green_3`='{$_GET['green_3']}',`green_4`='{$_GET['green_4']}',`green_angle`='{$_GET['green_angle']}',`yellow_inside_1`='{$_GET['yellow_inside_1']}',`yellow_inside_2`='{$_GET['yellow_inside_2']}',`yellow_inside_3`='{$_GET['yellow_inside_3']}',`yellow_inside_4`='{$_GET['yellow_inside_4']}',`yellow_inside_angle`='{$_GET['yellow_inside_angle']}',`yellow_outside_1`='{$_GET['yellow_outside_1']}',`yellow_outside_2`='{$_GET['yellow_outside_2']}',`yellow_outside_3`='{$_GET['yellow_outside_3']}',`yellow_outside_4`='{$_GET['yellow_outside_4']}',`yellow_outside_angle`='{$_GET['yellow_outside_angle']}',`d`='{$_GET['d']}',`lambda_yellow_inside`='{$_GET['lambda_yellow_inside']}',`E_yellow_inside`='$E_yellow_inside',`E_yellow_outside`='$E_yellow_outside',`lambda_yellow_outside`='{$_GET['lambda_yellow_outside']}',`D_color`='{$_GET['D_color']}',`D_color_theoretical`='$D_color_theoretical',`E_d`='$E_d'  WHERE `group_num`={$group_num}");
		if( !$result){
			$arr = array(
				'status' => '0',
				'msg' => '提交失败'
			);
		}else{
			$arr = array(
				'status' => '1',
				'msg' => '提交成功'
			);
		}

	}else if( isset($_GET['action']) && $_GET['action']=='check_info'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT `status_1` FROM `physics_course_spectrometer` WHERE `group_num`={$group_num}");
		$result = $result->fetch_assoc();
		if($result['status_1']=='1'){
			$arr = array(
				'status' => '1',
				'msg' => '审核通过'
			);
		}else if($result['status_1']=='2'){
			$arr = array(
				'status' => '0',
				'msg' => '审核中'
			);
		}else{
			$arr = array(
				'status' => '0',
				'msg' => '未通过'
			);
		}

	}else if( isset($_GET['action']) && $_GET['action']=='help'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("UPDATE `physics_course_spectrometer` SET `seek_help`=1 WHERE `group_num`={$group_num}");
		if($result){
			$arr = array(
				'status' => '1',
				'msg' => '提交成功'
			);

		}else{
			$arr = array(
				'status' => '0',
				'msg' => '提交失败'
			);
		}	
	}else if( isset($_GET['action']) && $_GET['action']=='image'){
		//image uplpoad 2018/2/7
		// var_dump($_FILES);
		$filename = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$result = move_uploaded_file($tmp_name , "../admin/upload/spectrometer/".$filename);
		if($result){
			$arr = array(
				'status' => '1',
				'msg' => '图片上传成功'
			);

		}else{
			$arr = array(
				'status' => '0',
				'msg' => '图片上传失败'
			);
		}	
	}

	foreach ( $arr as $key => $value ) {  		//防止中文乱码
    	$arr[$key] = urlencode ( $value );  
	}  
	echo urldecode ( json_encode ($arr) ); 
