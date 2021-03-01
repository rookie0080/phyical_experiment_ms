<?php
	//客户端接口

	if(!isset($_GET['client']))exit;
	//防止恶意调用

	require "../admin/config/database.php";
	$db = new mysqli(HOSTNAME, HOSTUSER, HOSTPWD, HOSTDB);
	$db->query('set names utf8');		//防止查询数据库乱码

	//若课堂还未开放或者已经终止，则不允许连接（其实只是为了第一步connect，但是这一步并不是很重要
	$result = $db->query("SELECT `status` FROM `physics_status` WHERE `name`='newton'");
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
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_newton` WHERE `group_num`='{$group_num}'");
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
		
		//查询该学号，用于判断是否已经登陆系统
		$obj = $db->query("SELECT `stu_num` FROM `physics_course_newton` WHERE `stu_num`={$stu_num} OR `group_num`='$group_num'");
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
				$data = $db->query("SELECT `grade` FROM `physics_course_newton` WHERE `group_num`={$group_num}");
				$data = $data->fetch_assoc();
				$grade = intval($data['grade']) - 5;		
				//var_dump($grade);
				$reuslt = $db->query("UPDATE `physics_course_newton` SET `grade`=$grade WHERE `group_num`={$group_num}");
			}
			//登录，更新数据库
			$sql = "UPDATE `physics_course_newton` SET `stu_num`='{$stu_num}', `stu_name`='{$stu_name}' WHERE `group_num`='{$group_num}'";
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
		$result = $db -> query("SELECT * FROM `physics_course_newton` WHERE `group_num`={$group_num}");
		$result = $result->fetch_assoc();
                if ($result['radius'] == '0') {
			$arr = array(
				'status' => '0',
				'msg' => '提交失败，实验设定值不可为0！'
			);
		}else{

			//var_dump($_GET['radius_commit']);
			//var_dump($result['radius']);
			$E_R = sprintf("%.2f",abs($_GET['radius_commit']-$result['radius'])/$result['radius']*100)."%";
		

			$result = $db -> query("UPDATE `physics_course_newton` SET `evaluation`=1,`status_1`=2,`radius_commit`='{$_GET['radius_commit']}',`E_R`='$E_R',`L6`='{$_GET['L6']}',`L7`='{$_GET['L7']}',`L8`='{$_GET['L8']}',`L9`='{$_GET['L9']}',`L10`='{$_GET['L10']}',`L11`='{$_GET['L11']}',`L12`='{$_GET['L12']}',`L13`='{$_GET['L13']}',`L14`='{$_GET['L14']}',`L15`='{$_GET['L15']}',`R6`='{$_GET['R6']}',`R7`='{$_GET['R7']}',`R8`='{$_GET['R8']}',`R9`='{$_GET['R9']}',`R10`='{$_GET['R10']}',`R11`='{$_GET['R11']}',`R12`='{$_GET['R12']}',`R13`='{$_GET['R13']}',`R14`='{$_GET['R14']}',`R15`='{$_GET['R15']}',`d6`='{$_GET['d6']}',`d7`='{$_GET['d7']}',`d8`='{$_GET['d8']}',`d9`='{$_GET['d9']}',`d10`='{$_GET['d10']}',`d11`='{$_GET['d11']}',`d12`='{$_GET['d12']}',`d13`='{$_GET['d13']}',`d14`='{$_GET['d14']}',`d15`='{$_GET['d15']}',`q6`='{$_GET['q6']}',`q7`='{$_GET['q7']}',`q8`='{$_GET['q8']}',`q9`='{$_GET['q9']}',`q10`='{$_GET['q10']}',`q11`='{$_GET['q11']}',`q12`='{$_GET['q12']}',`q13`='{$_GET['q13']}',`q14`='{$_GET['q14']}',`q15`='{$_GET['q15']}'  WHERE `group_num`={$group_num}");
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
		}
	}else if( isset($_GET['action']) && $_GET['action']=='check_info'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT `status_1` FROM `physics_course_newton` WHERE `group_num`={$group_num}");
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
		$result = $db -> query("UPDATE `physics_course_newton` SET `seek_help`=1 WHERE `group_num`={$group_num}");
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
		//var_dump($_FILES);
		$filename = $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name'];
		$result = move_uploaded_file($tmp_name , "../admin/upload/newton/".$filename);
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
