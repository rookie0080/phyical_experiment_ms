<?php
	//客户端接口
	
	if(!isset($_GET['client']))exit;
	//防止恶意调用

	require "../admin/config/database.php";
	$db = new mysqli(HOSTNAME, HOSTUSER, HOSTPWD, HOSTDB);
	$db->query('set names utf8');		//防止查询数据库乱码

	$result = $db->query("SELECT `status` FROM `physics_status` WHERE `name`='oscillograph'");
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

	if( isset($_GET['action']) && $_GET['action']=='connect'){
	//测试连接
		$arr = array(
			'status' => '1',
			'msg' => '连接成功'
		);
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 

	}else if( isset($_GET['action']) && $_GET['action']=='login'){
	//接收学生信息	
		$group_num = $_GET['group_num'];
		$stu_num =  $_GET['stu_num'];
		$stu_name = $_GET['stu_name'];

		//迟到-5，迟到界定为从上课时间开始到上课开始十五分钟内
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_oscillograph` WHERE `group_num`='{$group_num}'");
		$ifend = $ifend->fetch_assoc();
		$ifend = $ifend['ifend'];
		//查询该学号，用于判断是否已经登陆系统
		$obj = $db->query("SELECT `stu_num` FROM `physics_course_oscillograph` WHERE `stu_num`={$stu_num}");
		$data = $obj->fetch_assoc();
		
		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
		}else if($data){
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
			$start_time = $db->query("SELECT `start_time` FROM `physics_status` WHERE `name`='oscillograph'");
			$start_time = $start_time->fetch_assoc();
			// var_dump($start_time);
			$start_time = intval($start_time['start_time']);
			 // var_dump($start_time);
			if( $compare - $start_time > 15){
				$data = $db->query("SELECT `grade` FROM `physics_course_oscillograph` WHERE `group_num`={$group_num}");
				$data = $data->fetch_assoc();
				$grade = intval($data['grade']) - 5;		
				//var_dump($grade);
				$reuslt = $db->query("UPDATE `physics_course_oscillograph` SET `grade`=$grade WHERE `group_num`={$group_num}");
			}
			//登录，更新数据库
			$sql = "UPDATE `physics_course_oscillograph` SET `stu_num`='{$stu_num}', `stu_name`='{$stu_name}' WHERE `group_num`='{$group_num}'";	
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
		
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 

	}else if( isset($_GET['action']) && $_GET['action']=='require_data'){	
	//根据组号返回请求的实验设备预设值
		$group_num = $_GET['group_num'];
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_oscillograph` WHERE `group_num`='{$group_num}'");
		$ifend = $ifend->fetch_assoc();
		$ifend = $ifend['ifend'];

		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
			// echo'here';
		}else{
			//获取老师输入的实验预期值
			$group_num = $_GET['group_num'];
			$result = $db->query("SELECT `v_std` , `f_std` FROM `physics_course_oscillograph` WHERE `group_num`={$group_num}" );
			if( !$result){
				$arr = array(
					'status' => 0,
					'msg' => "未查询到数据"	
				);
			}else{
				$result = $result -> fetch_assoc();
				$arr = array(	
					'status' => 1,					
					'v_std' => $result['v_std'],
					'f_std' => $result['f_std']
				);
			}
		}
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 

	}else if( isset($_GET['action']) && $_GET['action']=='submit_1'){
	//接收上传的实验数据
		$group_num = $_GET['group_num'];
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_oscillograph` WHERE `group_num`='{$group_num}'");
		$ifend = $ifend->fetch_assoc();
		$ifend = $ifend['ifend'];
		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
		}else{
			//可以提交数据
			$group_num = $_GET['group_num'];
			$result = $db -> query("SELECT `v_std`,`f_std` FROM `physics_course_oscillograph` WHERE `group_num`={$group_num}");		//获取老师输入的实验预期值
			$data = $result -> fetch_assoc();
			//var_dump($data);
			$E_v = abs(($_GET['v'] - $data['v_std'])) / $data['v_std'];	//计算实验误差
			$E_f = abs(($_GET['f'] - $data['f_std'])) / $data['f_std'];

			$E_v = (string)($E_v*100).'%';	//数据库中必须为字符串形式
			$E_f = (string)($E_f*100).'%';
			$v_up = $_GET['v'];
			$f_up = $_GET['f'];
			$V_DIV = $_GET['V_DIV'];
			$Dy = $_GET['Dy'];
			$TIME_DIV = $_GET['TIME_DIV'];
			$n = $_GET['n'];
			$Dx = $_GET['Dx'];
			$T = $_GET['T'];

			$result = $db -> query("UPDATE `physics_course_oscillograph` SET `evaluation`=1,`status_1`=2,`v_up`='$v_up',`f_up`='$f_up',`E_v`='$E_v',`E_f`='$E_f',`V_DIV`='$V_DIV',`Dy`='$Dy',`TIME_DIV`='$TIME_DIV',`n`='$n',`Dx`='$Dx',`T`='$T'  WHERE `group_num`={$group_num}");
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
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 
	}else if( isset($_GET['action']) && $_GET['action']=='submit_2'){
		$group_num = $_GET['group_num'];
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_oscillograph` WHERE `group_num`='{$group_num}'");
		$ifend = $ifend->fetch_assoc();
		$ifend = $ifend['ifend'];
		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
		}else{
			//可以提交数据
			$group_num = $_GET['group_num'];

			$Nx1 = $_GET['Nx1'];
			$Nx2 = $_GET['Nx2'];
			$Nx3 = $_GET['Nx3'];
			$Nx4 = $_GET['Nx4'];
			$Ny1 = $_GET['Ny1'];
			$Ny2 = $_GET['Ny2'];
			$Ny3 = $_GET['Ny3'];
			$Ny4 = $_GET['Ny4'];
			$fy1 = $_GET['fy1'];
			$fy2 = $_GET['fy2'];
			$fy3 = $_GET['fy3'];
			$fy4 = $_GET['fy4'];

			$result = $db -> query("UPDATE `physics_course_oscillograph` SET `evaluation`=1,`status_2`=2,`Nx1`='$Nx1',`Nx2`='$Nx2',`Nx3`='$Nx3',`Nx4`='$Nx4',`Ny1`='$Ny1',`Ny2`='$Ny2',`Ny3`='$Ny3',`Ny4`='$Ny4',`fy1`='$fy1',`fy2`='$fy2',`fy3`='$fy3',`fy4`='$fy4'  WHERE `group_num`={$group_num}");
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
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 

	}else if( isset($_GET['action']) && $_GET['action']=='check_info_1'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT `status_1` FROM `physics_course_oscillograph` WHERE `group_num`={$group_num}");
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
		
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 

	}else if( isset($_GET['action']) && $_GET['action']=='check_info_2'){

		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT `status_2` FROM `physics_course_oscillograph` WHERE `group_num`={$group_num}");
		$result = $result->fetch_assoc();
		if($result['status_2']=='1'){
			$arr = array(
				'status' => '1',
				'msg' => '审核通过'
			);	
		}else if($result['status_2']=='2'){
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
		
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
    	echo urldecode ( json_encode ($arr) ); 
	}else if( isset($_GET['action']) && $_GET['action']=='help'){
		$group_num = $_GET['group_num'];
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_oscillograph` WHERE `group_num`='{$group_num}'");
		$ifend = $ifend->fetch_assoc();
		$ifend = $ifend['ifend'];
		if($ifend == 1){
			//ifend为0表示课堂未开始或者已经结束，或者已经结算成绩
			$arr = array( 
				'status'=>'0',
				'msg'=>'课堂已结束或已经完成全部实验！'
			);
		}else{
			$result = $db -> query("UPDATE `physics_course_oscillograph` SET `seek_help`=1 WHERE `group_num`={$group_num}");
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
		}
		foreach ( $arr as $key => $value ) {  		//防止中文乱码
        	$arr[$key] = urlencode ( $value );  
    	}  
		echo urldecode ( json_encode ($arr) ); 
	}
