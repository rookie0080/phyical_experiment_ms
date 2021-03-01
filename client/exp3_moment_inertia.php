<?php
	//客户端接口

	if(!isset($_GET['client']))exit;
	//防止恶意调用

	require "../admin/config/database.php";
	$db = new mysqli(HOSTNAME, HOSTUSER, HOSTPWD, HOSTDB);
	$db->query('set names utf8');		//防止查询数据库乱码

	//若课堂还未开放或者已经终止，则不允许连接（其实只是为了第一步connect，但是这一步并不是很重要
	$result = $db->query("SELECT `status` FROM `physics_status` WHERE `name`='moment_inertia'");
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
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_moment_inertia` WHERE `group_num`='{$group_num}'");
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
		// var_dump($group_num); var_dump($stu_num); var_dump($stu_name);
	
		//查询该学号，用于判断是否已经登陆系统
		$obj = $db->query("SELECT `stu_num` FROM `physics_course_moment_inertia` WHERE `stu_num`={$stu_num} OR `group_num`='$group_num'");
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
				$data = $db->query("SELECT `grade` FROM `physics_course_moment_inertia` WHERE `group_num`={$group_num}");
				$data = $data->fetch_assoc();
				$grade = intval($data['grade']) - 5;		
				//var_dump($grade);
				$reuslt = $db->query("UPDATE `physics_course_moment_inertia` SET `grade`=$grade WHERE `group_num`={$group_num}");
			}
			//登录，更新数据库
			$sql = "UPDATE `physics_course_moment_inertia` SET `stu_num`='{$stu_num}', `stu_name`='{$stu_name}' WHERE `group_num`='{$group_num}'";
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
		
		$t0_ave = sprintf("%3.f",($_GET['t0_1']+$_GET['t0_2']+$_GET['t0_3']+$_GET['t0_4']+$_GET['t0_5']+$_GET['t0_6'])/6);
		$t1_ave = sprintf("%3.f",($_GET['t1_1']+$_GET['t1_2']+$_GET['t1_3']+$_GET['t1_4']+$_GET['t1_5']+$_GET['t1_6'])/6);
		$t2_ave = sprintf("%3.f",($_GET['t2_1']+$_GET['t2_2']+$_GET['t2_3']+$_GET['t2_4']+$_GET['t2_5']+$_GET['t2_6'])/6);
		$t3_ave = sprintf("%3.f",($_GET['t3_1']+$_GET['t3_2']+$_GET['t3_3']+$_GET['t3_4']+$_GET['t3_5']+$_GET['t3_6'])/6);
		
		$d_ave = sprintf("%3.f",($_GET['d1']+$_GET['d2']+$_GET['d3']+$_GET['d4']+$_GET['d5']+$_GET['d6'])/6 );
		$dn_ave = sprintf("%3.f", ($_GET['dn_1']+$_GET['dn_2']+$_GET['dn_3']+$_GET['dn_4']+$_GET['dn_5']+$_GET['dn_6'])/6 );
		$dw_ave = sprintf("%3.f",($_GET['dw_1']+$_GET['dw_2']+$_GET['dw_3']+$_GET['dw_4']+$_GET['dw_5']+$_GET['dw_6'])/6 );
		$x_theoretical = sprintf("%f",$d_ave/2.0+6);

		$result = $db -> query("UPDATE `physics_course_moment_inertia` SET `evaluation`=1,`status_1`=2,`t0_1`='{$_GET['t0_1']}',`t0_2`='{$_GET['t0_2']}',`t0_3`='{$_GET['t0_3']}',`t0_4`='{$_GET['t0_4']}',`t0_5`='{$_GET['t0_5']}',`t0_6`='{$_GET['t0_6']}',`t0_ave`='{$t0_ave}',`t1_1`='{$_GET['t1_1']}',`t1_2`='{$_GET['t1_2']}',`t1_3`='{$_GET['t1_3']}',`t1_4`='{$_GET['t1_4']}',`t1_5`='{$_GET['t1_5']}',`t1_6`='{$_GET['t1_6']}',`t1_ave`='{$t1_ave}',`t2_1`='{$_GET['t2_1']}',`t2_2`='{$_GET['t2_2']}',`t2_3`='{$_GET['t2_3']}',`t2_4`='{$_GET['t2_4']}',`t2_5`='{$_GET['t2_5']}',`t2_6`='{$_GET['t2_6']}',`t2_ave`='{$t2_ave}',`t3_1`='{$_GET['t3_1']}',`t3_2`='{$_GET['t3_2']}',`t3_3`='{$_GET['t3_3']}',`t3_4`='{$_GET['t3_4']}',`t3_5`='{$_GET['t3_5']}',`t3_6`='{$_GET['t3_6']}',`t3_ave`='{$t3_ave}',`d1`='{$_GET['d1']}',`d2`='{$_GET['d2']}',`d3`='{$_GET['d3']}',`d4`='{$_GET['d4']}',`d5`='{$_GET['d5']}',`d6`='{$_GET['d6']}',`d_ave`='{$d_ave}',`x`='{$_GET['x']}',`x_theoretical`='$x_theoretical',`dn_1`='{$_GET['dn_1']}',`dn_2`='{$_GET['dn_2']}',`dn_3`='{$_GET['dn_3']}',`dn_4`='{$_GET['dn_4']}',`dn_5`='{$_GET['dn_5']}',`dn_6`='{$_GET['dn_6']}',`dn_ave`='{$dn_ave}',`dw_1`='{$_GET['dw_1']}',`dw_2`='{$_GET['dw_2']}',`dw_3`='{$_GET['dw_3']}',`dw_4`='{$_GET['dw_4']}',`dw_5`='{$_GET['dw_5']}',`dw_6`='{$_GET['dw_6']}',`dw_ave`='{$dw_ave}',`m1`='{$_GET['m1']}',`m2`='{$_GET['m2']}' WHERE `group_num`={$group_num}");
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
		$result = $db -> query("SELECT `status_1` FROM `physics_course_moment_inertia` WHERE `group_num`={$group_num}");
		$result = $result->fetch_assoc();
		if($result['status_1']==1){
			$arr = array(
				'status' => '1',
				'msg' => '审核通过'
			);
		}else if($result['status_1']==2){
			$arr = array(
				'status' => '0',
				'msg' => '审核中'
			);
		}else{
			$arr = array(
				'status' => 0,
				'msg' => '未通过'
			);
		}
	}else if( isset($_GET['action']) && $_GET['action']=='help'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("UPDATE `physics_course_moment_inertia` SET `seek_help`=1 WHERE `group_num`={$group_num}");
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
