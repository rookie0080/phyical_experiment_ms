<?php
	//客户端接口
	//电位差计
	
	if(!isset($_GET['client']))exit;
	//防止恶意调用

	require "../admin/config/database.php";
	$db = new mysqli(HOSTNAME, HOSTUSER, HOSTPWD, HOSTDB);
	$db->query('set names utf8');		//防止查询数据库乱码

	//若课堂还未开放或者已经终止，则不允许连接（其实只是为了第一步connect，但是这一步并不是很重要）
	$result = $db->query("SELECT `status` FROM `physics_status` WHERE `name`='potentioneter'");
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
	if(isset($_GET['group_num']) && $_GET['action']!='check_info_1' && $_GET['action']!='check_info_2'){
		$group_num = $_GET['group_num'];
		$ifend = $db->query("SELECT `ifend` FROM `physics_course_potentioneter` WHERE `group_num`='{$group_num}'");
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
		$obj = $db->query("SELECT `stu_num` FROM `physics_course_potentioneter` WHERE `stu_num`={$stu_num}");
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
			$start_time = $db->query("SELECT `start_time` FROM `physics_status` WHERE `name`='potentioneter'");
			$start_time = $start_time->fetch_assoc();
			// var_dump($start_time);
			$start_time = intval($start_time['start_time']);
			 // var_dump($start_time);
			if( $compare - $start_time > 15){
				$data = $db->query("SELECT `grade` FROM `physics_course_potentioneter` WHERE `group_num`={$group_num}");
				$data = $data->fetch_assoc();
				$grade = intval($data['grade']) - 5;		
				//var_dump($grade);
				$reuslt = $db->query("UPDATE `physics_course_potentioneter` SET `grade`=$grade WHERE `group_num`={$group_num}");
			}
			//登录，更新数据库
			$sql = "UPDATE `physics_course_potentioneter` SET `stu_num`='{$stu_num}', `stu_name`='{$stu_name}' WHERE `group_num`='{$group_num}'";
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
	}else if( isset($_GET['action']) && $_GET['action']=='require_data'){	
	//根据组号返回请求的实验设备预设值

		//获取老师输入的实验预期值
		$group_num = $_GET['group_num'];
		$result = $db->query("SELECT `Exs` FROM `physics_course_potentioneter` WHERE `group_num`={$group_num}" );
		if( !$result){
			$arr = array(
				'status' => 0,
				'msg' => "未查询到数据"	
			);
		}else{
			$result = $result -> fetch_assoc();
			$arr = array(	
				'status' => 1,					
				'Exs' => $result['Exs'],
			);
		}
	}else if( isset($_GET['action']) && $_GET['action']=='submit_1'){
	//接收上传的实验数据
		$group_num = $_GET['group_num'];

				//第一次提交
		$U_ab = $_GET['U_ab'];
		$U_0 = $_GET['U_0'];
		$I_s = $_GET['I_s'];
		$Rab = $_GET['Rab'];
		$Is = $_GET['Is'];
		$U0 = $_GET['U0'];
		$Uab = $_GET['Uab'];
		$E = $_GET['E'];
		

		$result = $db->query("UPDATE `physics_course_potentioneter` SET `evaluation`=1,`status_1`=2,`U_ab`='$U_ab',`U_0`='$U_0',`I_s`='$I_s',`Rab`='$Rab',`Is`='$Is',`U0`='$U0',`Uab`='$Uab',`E`='$E' WHERE `group_num`={$group_num}");

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
	}else if( isset($_GET['action']) && $_GET['action']=='submit_2'){
		$group_num = $_GET['group_num'];
		//第二次提交
		$Lx1 = $_GET['Lx1'];
		$Lx2 = $_GET['Lx2'];
		$Lx3 = $_GET['Lx3'];
		$Lx4 = $_GET['Lx4'];
		$Lx5 = $_GET['Lx5'];
		$Lx6 = $_GET['Lx6'];
		$Lx_ave = $_GET['Lx_ave'];
		
		$result = $db -> query("SELECT `Exs` FROM `physics_course_potentioneter` WHERE `group_num`={$group_num}");		//获取老师输入的实验预期值
		$data = $result -> fetch_assoc();
		$Ex = $_GET['Ex'];
		$E_e = abs(($_GET['Ex']-$data['Exs'])) / $data['Exs'];		//计算实验误差
		$E_e = ($E_e*100);	//数据库中必须为字符串形式
		$E_e = sprintf("%.2f",$E_e).'%';
		//var_dump($E_e);


		$result = $db->query("UPDATE `physics_course_potentioneter` SET `evaluation`=1,`status_2`=2,`Lx1`='$Lx1',`Lx2`='$Lx2',`Lx3`='$Lx3',`Lx4`='$Lx4',`Lx5`='$Lx5',`Lx6`='$Lx6',`Lx_ave`='$Lx_ave',`Ex`='$Ex',`E_e`='$E_e' WHERE `group_num`={$group_num}");

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
	}else if( isset($_GET['action']) && $_GET['action']=='check_info_1'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT `status_1` FROM `physics_course_potentioneter` WHERE `group_num`={$group_num}");
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
	}else if( isset($_GET['action']) && $_GET['action']=='check_info_2'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("SELECT `status_2` FROM `physics_course_potentioneter` WHERE `group_num`={$group_num}");
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
		
	}else if( isset($_GET['action']) && $_GET['action']=='help'){
		$group_num = $_GET['group_num'];
		$result = $db -> query("UPDATE `physics_course_potentioneter` SET `seek_help`=1 WHERE `group_num`={$group_num}");
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
