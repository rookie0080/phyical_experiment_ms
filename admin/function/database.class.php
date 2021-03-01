<?php

class database{
	private $db;
	private $tb_name;			//要操作的数据表名
	private $tablePrefix = 'physics';		//表前缀
	private $pwdSalt = 'my first project';		//提高加密水平

	function __construct($name=null){
		require_once dirname(__FILE__).'/../config/database.php';
		$this->tb_name = $name;
		$this->db = new mysqli(HOSTNAME,HOSTUSER,HOSTPWD,HOSTDB);
		$this->db->query('set names utf8');		//防止查询数据库乱码
	}


	function check_login($user_name, $password){
		$password = md5($this->db->real_escape_string($password).$this->pwdSalt);
		$sql = "SELECT `name`,`level`,`uid`  FROM `{$this->tablePrefix}_user` WHERE `number`='{$user_name}' AND `password`='{$password}'";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		if($result){
			return $result;
		}else 
			return false;
	}
	function new_info(){
	//实时更新老师的管理页面，根据字段`ifShow`
		$sql = "SELECT * FROM `{$this->tablePrefix}_course_{$this->tb_name}` ORDER BY `group_num`" ;
		$result = $this->db->query($sql);
		//$this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `ifShow`=1 WHERE `ifShow`=0");
		return $result;
	}

	function evaluating_info(){
	//请求等待评测的数据
		$sql = "SELECT `group_num` FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `evaluation`=1";
		$result = $this->db->query($sql);
		return $result;
	}

	function help_info(){	
	//向后台请求学生求助的信息
		$sql = "SELECT `group_num` FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `seek_help`=1";
		$result = $this->db->query($sql);
		return $result;
	}

	function detail_data($group_num){
		$sql = "SELECT * FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `group_num`={$group_num}";
		$result = $this->db->query($sql);
		return $result;
	}

	function pass($option,$group_num){
		$sql = "UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `status_{$option}`=1,`evaluation`=0 WHERE `group_num`={$group_num}";

		//如果实验全部完成，计算机将自动评分的结果改为可显示，‘已评定’,前提是不超过两个子实验
		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `group_num`={$group_num}");
		$data = $data->fetch_assoc();
		if(isset($data['status_2'])){
			if($option==2){ 
				$this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `grade_status`=1,`ifend`=1 WHERE `group_num`={$group_num}");
			}
		}else if(isset($data['status_1'])){
			if($option==1){ 
				$this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `grade_status`=1,`ifend`=1 WHERE `group_num`={$group_num}");
			}
		}

		$result = $this->db->query($sql);
		if($result) return 1;
		else return 0;
	}

	function fail($option,$group_num){
		// 未通过，每次累计减去2分
		$data = $this->db->query("SELECT `grade` FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `group_num`={$group_num}");
		$grade = $data->fetch_assoc();
		$grade = $grade['grade'] - 2;		
		$this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `grade`='$grade' WHERE `group_num`={$group_num}");
	

		$sql = "SELECT `fail_times` FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `group_num`={$group_num}";
		$fail_times = $this->db->query($sql);
		$fail_times = $fail_times->fetch_assoc();
		$fail_times = (int)($fail_times['fail_times']) + 1;
		$sql = "UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `status_{$option}`=0,`fail_times`={$fail_times},`evaluation`=0 WHERE `group_num`={$group_num}";
		$result = $this->db->query($sql);
		if($result) return 1;
		else return  0;
	}

	function solve_help($group_num){
		//求助，每次累计减去2分
		$data = $this->db->query("SELECT `grade` FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `group_num`={$group_num}");
		$data = $data->fetch_assoc();
		$grade = $data['grade'] - 2;
		echo ($grade);
		$this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `grade`=$grade WHERE `group_num`={$group_num}");	
		//求助次数加1
		$help_times = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_{$this->tb_name}` WHERE `group_num`={$group_num}");
		$help_times = $help_times->fetch_assoc();
		$help_times = $help_times['help_times'];
		$help_times = $help_times + 1;
		$this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `help_times`=$help_times WHERE `group_num`={$group_num}");
		$sql = "UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `seek_help`=0 WHERE `group_num`={$group_num}";

		$result = $this->db->query($sql);
	}

	function if_cur_course($user_id){
		$sql = "SELECT `cur_course` FROM `{$this->tablePrefix}_user` WHERE `uid`={$user_id}";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		if( $result['cur_course']!= null){
			return 1;
		}else return 0;
	}

	function cur_course_name($user_id){
		$sql = "SELECT `name` FROM `{$this->tablePrefix}_status` WHERE `user_id`={$user_id}";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		return $result['name'];
	}

	function start_course($user_id, $course_name, $classNum){
		$sql = "UPDATE `{$this->tablePrefix}_status` SET `status`=1,`user_id`={$user_id},`class_num`={$classNum} WHERE `name`='{$course_name}'";
		$result = $this->db->query($sql);

		//修改课程的起始时间
		date_default_timezone_set('Asia/Shanghai');
		$cur_time = getdate();
		$hours = $cur_time['hours'];
		$minutes = $cur_time['minutes'];
		//$hours = 9; $minutes = 44;		//测试使用
		$compare = $hours*60 + $minutes;	

		if( $compare>=450 && $compare<=584){	//8:00--9:45
			$start_time = '450'; 
			$end_time = '584';   			
		}else if( $compare>=585 && $compare<=719){	//10:15--12:00
			$start_time = '585';
			$end_time = '719';
		}else if( $compare>=810 && $compare<=944){	//14:00--15:45
			$start_time = "810";
			$end_time = "944";
		}else if( $compare>=945 && $compare<=1079){		//16:15--18:00
			$start_time = '945';
			$end_time = '1079';
		}else if( $compare>=1110 && $compare<=1229){	//18:45--20:30
			$start_time = '1110';
			$end_time = '1229';
		}else{
			$start_time = '0';		//若以上时间都不符合，为测试用数据，使用时后台不会出现这样的情况
			$end_time = '0';
		}
		
		$re = $this->db->query("UPDATE `{$this->tablePrefix}_status` SET `start_time`='{$start_time}',`end_time`='{$end_time}' WHERE `name`='{$course_name}'");
		echo '$start_time = '.$start_time;
		echo '$end_time='.$end_time;
		echo '$re = '.$re;

		if( $result) return 1;
	}

	function close_course($user_id, $course_name){
		//结束课堂

		//修改status表
		$sql = "UPDATE `{$this->tablePrefix}_status` SET `status`=0,`user_id`=null,`class_num`=null,`start_time`=null,`end_time`=null WHERE `name`='{$course_name}'";
		$result = $this->db->query($sql);

		//若未完成实验则没有分数
		$this->db->query("UPDATE `{$this->tablePrefix}_course_{$course_name}` SET `grade`=0 WHERE `grade_status`=0");
		//修改user表
		$sql = "UPDATE `{$this->tablePrefix}_user` SET `cur_course`=null WHERE `uid`='{$user_id}'";
		$result1 = $this->db->query($sql);
		if($result && $result1) return 1;
		else 
			return 0;
	}

	function save_data($course_name,$time,$user_id){
		//保存当堂的实验数据，无法做到对不同实验统一函数处理方法
		$result = 0;
		// echo('here');
		// echo("\ncourse_name:".$course_name);
		switch($course_name){
			case 'oscillograph':
				$result = $this->save_data_oscillograph($time, $user_id);
				break;
			case 'potentioneter':
				$result = $this->save_data_potentioneter($time, $user_id);
				break;
			case 'thermal_conductivity':
				$result = $this->save_data_thermal_conductivity($time, $user_id);
				break;
			case 'newton':
				$result = $this->save_data_newton($time, $user_id);
				break;
			case 'moment_inertia':
				$result = $this->save_data_moment_inertia($time, $user_id);
				break;
			case 'spectrometer':
				$result = $this->save_data_spectrometer($time, $user_id);
				break;
			default:
				break;
		}
		return $result;
	}

	function save_data_oscillograph($time, $user_id){
		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_oscillograph`");
		//将实验结果存储到data数据表中
		while($result = $data->fetch_assoc()){
			//var_dump($result['stu_num']);
			if($result['stu_num']==null) continue;
			// var_dump($result['remark']);
			if($result['grade_status']==0) $result['grade'] = 0; 		//若结束课堂时任然未完成实验，计0分存入数据库	
			$sql = "INSERT INTO `{$this->tablePrefix}_data_oscillograph` (
				   `teacher_id`,`exp_name`,`time`,`stu_num`,`stu_name`,`grade`,`help_times`,`fail_times`,`v_std`,`f_std`,`v_up`,`E_v`,`f_up`,`E_f`,
				   `V_DIV`,`Dy`,`TIME_DIV`,`n`,`Dx`,`T`,`Nx1`,`Ny1`,`fy1`,`Nx2`,`Ny2`,`fy2`,`Nx3`,`Ny3`,`fy3`,`Nx4`,`Ny4`,`fy4`,`remark` ) VALUES (
				   '$user_id','示波器与李萨如图形','$time','{$result['stu_num']}','{$result['stu_name']}','{$result['grade']}','{$result['help_times']}',
				   '{$result['fail_times']}','{$result['v_std']}','{$result['f_std']}','{$result['v_up']}','{$result['E_v']}','{$result['f_up']}','{$result['E_f']}',
				   '{$result['V_DIV']}','{$result['Dy']}','{$result['TIME_DIV']}','{$result['n']}','{$result['Dx']}','{$result['T']}','{$result['Nx1']}','{$result['Ny1']}',
				   '{$result['fy1']}','{$result['Nx2']}','{$result['Ny2']}','{$result['fy2']}','{$result['Nx3']}','{$result['Ny3']}','{$result['fy3']}','{$result['Nx4']}',
				   '{$result['Ny4']}','{$result['fy4']}','{$result['remark']}' 
				)"; 
			$re = $this->db->query($sql);
			// echo('here1');
			//var_dump('here');
			if(!$re) return 0;
			//添加学生的data	
			$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_student` (`stu_num`,`stu_name`,`exp_name_ch`,`exp_name_en`,`help_times`,`fail_times`,`grade`,`time`) VALUES ('{$result['stu_num']}','{$result['stu_name']}','示波器与李萨如图形','oscillograph','{$result['help_times']}','{$result['fail_times']}','{$result['grade']}','$time')");
			// echo('here2');
			if(!$re) return 0;

		}

		//将course数据表实验数据清空，仅留下group_num字段值，为下次实验做准备
		$this->db->query("TRUNCATE `physics_course_oscillograph`");
		for($i = 1; $i<=40; $i++){
			$result = $this->db->query("INSERT INTO `physics_course_oscillograph` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `v_std`, `f_std`, `v_up`, `E_v`, `f_up`, `E_f`, `V_DIV`, `Dy`, `TIME_DIV`, `n`, `Dx`, `T`, `status_1`, `Nx1`, `Ny1`, `fy1`, `Nx2`, `Ny2`, `fy2`, `Nx3`, `Ny3`, `fy3`, `Nx4`, `Ny4`, `fy4`, `status_2`, `seek_help`, `remark`, `grade_status`, `ifend`) VALUES ('$i', NULL, NULL, 100, 0, 0, 0, '-1', '-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '无', 0, 0);");
		}

		//找到teacher的名字
		$teacher = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id'");
		$teacher = $teacher->fetch_assoc();
		$teacher = $teacher['name'];
		//插入一条课堂记录
		$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_course` (`exp_name`,`time`,`teacher`,`exp_name_en`)VALUES ('示波器与李萨如图形','$time','$teacher','oscillograph')");
		if(!$re) return 0;

		return 1;
	}

	function save_data_potentioneter($time, $user_id){
		// for($i = 5; $i<=40; $i++){
		// 	$this->db->query("INSERT INTO `physics_course_potentioneter` (`group_num`, `stu_num`, `stu_name`, `help_times`, `fail_times`, `grade`, `seek_help`, `evaluation`, `E`, `U_ab`, `U_0`, `I_s`, `Rab`, `Is`, `U0`, `Uab`, `status_1`, `Lx1`, `Lx2`, `Lx3`, `Lx4`, `Lx5`, `Lx6`, `Lx_ave`, `status_2`, `remark`, `Exs`, `Ex`, `E_e`) VALUES ('$i', '081510117', 'Jack', '0', '0', '95', '0', '0', '2', '2', '3', '34', '42', '42', '6', '74', '74', '4', '4', '6', '7', '5', '6', '3', '2', NULL, NULL, NULL, NULL)");
		// }
		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_potentioneter`");
		while($result = $data->fetch_assoc()){
			//var_dump($result['stu_num']);
			if($result['stu_num']==null) continue;
			if($result['grade_status']==0) $result['grade'] = 0; 		//若结束课堂时任然未完成实验，计0分存入数据库	
			$sql = "INSERT INTO `{$this->tablePrefix}_data_potentioneter` (
			 `teacher_id`,`exp_name`,`time`,`stu_num`,`stu_name`,`grade`,`help_times`,`fail_times`,`E`,`U_ab`,`U_0`,`I_s`,`Rab`,`Is`,`U0`,`Uab`,`Lx1`,`Lx2`,`Lx3`,`Lx4`,`Lx5`,`Lx6`,`Lx_ave`,`Exs`,`Ex`,`E_e`,`remark`
			) VALUES (
				'$user_id','电位差计','$time','{$result['stu_num']}','{$result['stu_name']}','{$result['grade']}','{$result['help_times']}','{$result['fail_times']}','{$result['E']}','{$result['U_ab']}','{$result['U_0']}','{$result['I_s']}','{$result['Rab']}','{$result['Is']}','{$result['U0']}','{$result['Uab']}','{$result['Lx1']}','{$result['Lx2']}','{$result['Lx3']}','{$result['Lx4']}','{$result['Lx5']}','{$result['Lx6']}','{$result['Lx_ave']}','{$result['Exs']}','{$result['Ex']}','{$result['E_e']}','{$result['remark']}'
			)"; 
			$re = $this->db->query($sql);
			 // echo('here');
			if(!$re) return 0;	
			  // echo('here1');
			//添加学生记录
			$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_student` (`stu_num`,`stu_name`,`exp_name_ch`,`exp_name_en`,`help_times`,`fail_times`,`grade`,`time`) VALUES ('{$result['stu_num']}','{$result['stu_name']}','电位差计','potentioneter','{$result['help_times']}','{$result['fail_times']}','{$result['grade']}','$time' )");
			if(!$re) return 0;
			// echo('here2');

		}

		//将course数据表实验数据清空，仅留下group_num字段值，为下次实验做准备
		//保留Exs
		$obj = $this->db->query("SELECT `Exs` FROM `physics_course_potentioneter` WHERE 1=1");
		$this->db->query("TRUNCATE `physics_course_potentioneter`");
		for($i = 1; $i<=40; $i++){
			$data = $obj->fetch_assoc();
			$this->db->query("INSERT INTO `physics_course_potentioneter` (`group_num`, `stu_num`, `stu_name`, `help_times`, `fail_times`, `grade`, `seek_help`, `evaluation`, `E`, `U_ab`, `U_0`, `I_s`, `Rab`, `Is`, `U0`, `Uab`, `status_1`, `Lx1`, `Lx2`, `Lx3`, `Lx4`, `Lx5`, `Lx6`, `Lx_ave`, `status_2`, `remark`, `Exs`, `Ex`, `E_e`, `grade_status`, `ifend`) VALUES ('$i', null, null, '0', '0', '100', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '{$data['Exs']}', NULL, NULL, '0', '0');");
		}

		//找到teacher的名字
		$teacher = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id'");
		$teacher = $teacher->fetch_assoc();
		$teacher = $teacher['name'];
		//插入一条课堂记录
		$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_course` (`exp_name`,`time`,`teacher`,`exp_name_en`)VALUES ('电位差计','$time','$teacher','potentioneter')");
		if(!$re) return 0;

		return 1;
	}

	function save_data_thermal_conductivity($time, $user_id){
		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_thermal_conductivity`");
		while($result = $data->fetch_assoc()){
			//var_dump($result['stu_num']);
			if($result['stu_num']==null) continue;
			$sql = "INSERT INTO `{$this->tablePrefix}_data_thermal_conductivity` (
			 `teacher_id`,`exp_name`,`time`,`stu_num`,`stu_name`,`grade`,`help_times`,`fail_times`,`T_1`,`T_2`,`t1`,`t2`,`t3`,`t4`,`t5`,`t6`,`t7`,`t8`,`t9`,`t10`,`te1`,`te2`,`te3`,`te4`,`te5`,`te6`,`te7`,`te8`,`te9`,`te10`,`change_rate`,`hb1`,`hb2`,`hb3`,`hb4`,`hb5`,`hb6`,`hb_ave`,`db`,`hc1`,`hc2`,`hc3`,`hc4`,`hc5`,`hc6`,`hc_ave`,`dc`,`m`,`remark`
			) VALUES (
				'$user_id','稳态法测量物体的导热系数','$time','{$result['stu_num']}','{$result['stu_name']}','{$result['grade']}','{$result['help_times']}','{$result['fail_times']}','{$result['T_1']}','{$result['T_2']}','{$result['t1']}','{$result['t2']}','{$result['t3']}','{$result['t4']}','{$result['t5']}','{$result['t6']}','{$result['t7']}','{$result['t8']}','{$result['t9']}','{$result['t10']}','{$result['te1']}','{$result['te2']}','{$result['te3']}','{$result['te4']}','{$result['te5']}','{$result['te6']}','{$result['te7']}','{$result['te8']}','{$result['te9']}','{$result['te10']}','{$result['change_rate']}','{$result['hb1']}','{$result['hb2']}','{$result['hb3']}','{$result['hb4']}','{$result['hb5']}','{$result['hb6']}','{$result['hb_ave']}','{$result['db']}','{$result['hc1']}','{$result['hc2']}','{$result['hc3']}','{$result['hc4']}','{$result['hc5']}','{$result['hc6']}','{$result['hc_ave']}','{$result['dc']}','{$result['m']}','{$result['remark']}'
			)"; 
			$re = $this->db->query($sql);
			if(!$re) return 0;	
			// echo('here1');
			$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_student` (`stu_num`,`stu_name`,`exp_name_ch`,`exp_name_en`,`help_times`,`fail_times`,`grade`,`time`) VALUES ('{$result['stu_num']}','{$result['stu_name']}','稳态法测量物体的导热系数','thermal_conductivity','{$result['help_times']}','{$result['fail_times']}','{$result['grade']}','$time' )");
			if(!$re) return 0;
			// echo('here2');

		}
		//将course数据表实验数据清空，仅留下group_num字段值，为下次实验做准备
		$this->db->query("TRUNCATE `physics_course_thermal_conductivity`");
		for($i = 1; $i<=40; $i++){
			$this->db->query("INSERT INTO `physics_course_thermal_conductivity` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `T_1`, `T_2`, `t1`, `t2`, `t3`, `t4`, `t5`, `t6`, `t7`, `t8`, `t9`, `t10`, `te1`, `te2`, `te3`, `te4`, `te5`, `te6`, `te7`, `te8`, `te9`, `te10`, `change_rate`, `hb1`, `hb2`, `hb3`, `hb4`, `hb5`, `hb6`, `hb_ave`, `db`, `status_1`, `hc1`, `hc2`, `hc3`, `hc4`, `hc5`, `hc6`, `hc_ave`, `dc`, `m`, `status_2`, `remark`, `seek_help`, `grade_status`, `ifend`) VALUES ('$i', null, null, '100', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '0', '0', '0')");
		}


		//找到teacher的名字
		$teacher = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id'");
		$teacher = $teacher->fetch_assoc();
		$teacher = $teacher['name'];
		//插入一条课堂记录
		$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_course` (`exp_name`,`time`,`teacher`,`exp_name_en`)VALUES ('稳态法测量物体的导热系数','$time','$teacher','thermal_conductivity')");
		if(!$re) return 0;

		return 1;
	}

	function save_data_newton($time, $user_id){
		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_newton`");
		while($result = $data->fetch_assoc()){
			//var_dump($result['stu_num']);
			if($result['stu_num']==null) continue;
			$sql = "INSERT INTO `{$this->tablePrefix}_data_newton` (
			 `teacher_id`,`exp_name`,`time`,`stu_num`,`stu_name`,`grade`,`help_times`,`fail_times`,`radius`,`radius_commit`,`E_R`,`L6`,`L7`,`L8`,`L9`,`L10`,`L11`,`L12`,`L13`,`L14`,`L15`,`R6`,`R7`,`R8`,`R9`,`R10`,`R11`,`R12`,`R13`,`R14`,`R15`,`d6`,`d7`,`d8`,`d9`,`d10`,`d11`,`d12`,`d13`,`d14`,`d15`,`q6`,`q7`,`q8`,`q9`,`q10`,`q11`,`q12`,`q13`,`q14`,`q15`,`remark`
			) VALUES (
				'$user_id','光的干涉--牛顿环','$time','{$result['stu_num']}','{$result['stu_name']}','{$result['grade']}','{$result['help_times']}','{$result['fail_times']}','{$result['radius']}','{$result['radius_commit']}'	,'{$result['E_R']}','{$result['L6']}','{$result['L7']}','{$result['L8']}','{$result['L9']}','{$result['L10']}','{$result['L11']}','{$result['L12']}','{$result['L13']}','{$result['L14']}','{$result['L15']}','{$result['R6']}','{$result['R7']}','{$result['R8']}','{$result['R9']}','{$result['R10']}','{$result['R11']}','{$result['R12']}','{$result['R13']}','{$result['R14']}','{$result['R15']}','{$result['d6']}','{$result['d7']}','{$result['d8']}','{$result['d9']}','{$result['d10']}','{$result['d11']}','{$result['d12']}','{$result['d13']}','{$result['d14']}','{$result['d15']}','{$result['q6']}','{$result['q7']}','{$result['q8']}','{$result['q9']}','{$result['q10']}','{$result['q11']}','{$result['q12']}','{$result['q13']}','{$result['q14']}','{$result['q15']}','{$result['remark']}'
			)"; 
			$re = $this->db->query($sql);
			if(!$re) return 0;	
			 // echo('here1');
			$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_student` (`stu_num`,`stu_name`,`exp_name_ch`,`exp_name_en`,`help_times`,`fail_times`,`grade`,`time`) VALUES ('{$result['stu_num']}','{$result['stu_name']}','光的干涉--牛顿环','newton','{$result['help_times']}','{$result['fail_times']}','{$result['grade']}','$time' )");
			if(!$re) return 0;
			 // echo('here2');

		}

		//将course数据表实验数据清空，仅留下group_num字段值，为下次实验做准备
		
		$data_save = $this->db->query("SELECT `radius` FROM `physics_course_newton` WHERE 1=1");	//radius要保留！！	
		$this->db->query("TRUNCATE `physics_course_newton`");
		for($i = 1; $i<=40; $i++){
			$data = $data_save->fetch_assoc();
			$radius = $data['radius'];
			$this->db->query("INSERT INTO `physics_course_newton` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `radius`, `radius_commit`, `E_R`, `L6`, `L7`, `L8`, `L9`, `L10`, `L11`, `L12`, `L13`, `L14`, `L15`, `R6`, `R7`, `R8`, `R9`, `R10`, `R11`, `R12`, `R13`, `R14`, `R15`, `d6`, `d7`, `d8`, `d9`, `d10`, `d11`, `d12`, `d13`, `d14`, `d15`, `q6`, `q7`, `q8`, `q9`, `q10`, `q11`, `q12`, `q13`, `q14`, `q15`, `status_1`, `remark`, `seek_help`, `grade_status`, `ifend`) VALUES ('$i', null, null, '100', '0', '0', '0', '$radius', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '0', '0', '0')");
		}

		//找到teacher的名字
		$teacher = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id'");
		$teacher = $teacher->fetch_assoc();
		$teacher = $teacher['name'];
		//插入一条课堂记录
		$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_course` (`exp_name`,`time`,`teacher`,`exp_name_en`)VALUES ('光的干涉--牛顿环','$time','$teacher','newton')");
		if(!$re) return 0;

		return 1;
	}

	function save_data_moment_inertia($time, $user_id){
		// for($i=4; $i<=40; $i++){
		// 	$this->db->query("INSERT INTO `physics_course_moment_inertia` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `t0_1`, `t0_2`, `t0_3`, `t0_4`, `t0_5`, `t0_6`, `t0_ave`, `t1_1`, `t1_2`, `t1_3`, `t1_4`, `t1_5`, `t1_6`, `t1_ave`, `t2_1`, `t2_2`, `t2_3`, `t2_4`, `t2_5`, `t2_6`, `t2_ave`, `t3_1`, `t3_2`, `t3_3`, `t3_4`, `t3_5`, `t3_6`, `t3_ave`, `status_1`, `d1`, `d2`, `d3`, `d4`, `d5`, `d6`, `d_ave`, `x`, `x_theoretical`, `dn_1`, `dn_2`, `dn_3`, `dn_4`, `dn_5`, `dn_6`, `dn_ave`, `dw_1`, `dw_2`, `dw_3`, `dw_4`, `dw_5`, `dw_6`, `dw_ave`, `m1`, `m2`, `remark`) VALUES ('$i', '081510111', 'Wall', '96', '0', '0', '0', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '')");
		// }

		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_moment_inertia`");
		while($result = $data->fetch_assoc()){
			//var_dump($result['stu_num']);
			// echo 'here';
			if($result['stu_num']==null) continue;
			$sql = "INSERT INTO `{$this->tablePrefix}_data_moment_inertia` (
			 `teacher_id`,`exp_name`,`time`,`stu_num`,`stu_name`,`grade`,`help_times`,`fail_times`,`t0_1`,`t0_2`,`t0_3`,`t0_4`,`t0_5`,`t0_6`,`t0_ave`,`t1_1`,`t1_2`,`t1_3`,`t1_4`,`t1_5`,`t1_6`,`t1_ave`,`t2_1`,`t2_2`,`t2_3`,`t2_4`,`t2_5`,`t2_6`,`t2_ave`,`t3_1`,`t3_2`,`t3_3`,`t3_4`,`t3_5`,`t3_6`,`t3_ave`,`d1`,`d2`,`d3`,`d4`,`d5`,`d6`,`d_ave`,`x`,`x_theoretical`,`dn_1`,`dn_2`,`dn_3`,`dn_4`,`dn_5`,`dn_6`,`dn_ave`,`dw_1`,`dw_2`,`dw_3`,`dw_4`,`dw_5`,`dw_6`,`dw_ave`,`m1`,`m2`,`remark`
			) VALUES (
				'$user_id','用气垫摆测量转动惯量','$time','{$result['stu_num']}','{$result['stu_name']}','{$result['grade']}','{$result['help_times']}','{$result['fail_times']}','{$result['t0_1']}','{$result['t0_2']}','{$result['t0_3']}','{$result['t0_4']}','{$result['t0_5']}','{$result['t0_6']}','{$result['t0_ave']}','{$result['t1_1']}','{$result['t1_2']}','{$result['t1_3']}','{$result['t1_4']}','{$result['t1_5']}','{$result['t1_6']}','{$result['t1_ave']}','{$result['t2_1']}','{$result['t2_2']}','{$result['t2_3']}','{$result['t2_4']}','{$result['t2_5']}','{$result['t2_6']}','{$result['t2_ave']}','{$result['t3_1']}','{$result['t3_2']}','{$result['t3_3']}','{$result['t3_4']}','{$result['t3_5']}','{$result['t3_6']}','{$result['t3_ave']}','{$result['d1']}','{$result['d2']}','{$result['d3']}','{$result['d4']}','{$result['d5']}','{$result['d6']}','{$result['d_ave']}','{$result['x']}','{$result['x_theoretical']}','{$result['dn_1']}','{$result['dn_2']}','{$result['dn_3']}','{$result['dn_4']}','{$result['dn_5']}','{$result['dn_6']}','{$result['dn_ave']}','{$result['dw_1']}','{$result['dw_2']}','{$result['dw_3']}','{$result['dw_4']}','{$result['dw_5']}','{$result['dw_6']}','{$result['dw_ave']}','{$result['m1']}','{$result['m2']}','{$result['remark']}'
			)"; 
			$re = $this->db->query($sql);
			if(!$re) return 0;	
			 // echo('here1');
			$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_student` (`stu_num`,`stu_name`,`exp_name_ch`,`exp_name_en`,`help_times`,`fail_times`,`grade`,`time`) VALUES ('{$result['stu_num']}','{$result['stu_name']}','用气垫摆测量转动惯量','moment_inertia','{$result['help_times']}','{$result['fail_times']}','{$result['grade']}','$time' )");
			if(!$re) return 0;
			 // echo('here2');

		}

		//将course数据表实验数据清空，仅留下group_num字段值，为下次实验做准备
		$this->db->query("TRUNCATE `physics_course_moment_inertia`");
		for($i = 1; $i<=40; $i++){
			$this->db->query("INSERT INTO `physics_course_moment_inertia` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `t0_1`, `t0_2`, `t0_3`, `t0_4`, `t0_5`, `t0_6`, `t0_ave`, `t1_1`, `t1_2`, `t1_3`, `t1_4`, `t1_5`, `t1_6`, `t1_ave`, `t2_1`, `t2_2`, `t2_3`, `t2_4`, `t2_5`, `t2_6`, `t2_ave`, `t3_1`, `t3_2`, `t3_3`, `t3_4`, `t3_5`, `t3_6`, `t3_ave`, `status_1`, `d1`, `d2`, `d3`, `d4`, `d5`, `d6`, `d_ave`, `x`, `x_theoretical`, `dn_1`, `dn_2`, `dn_3`, `dn_4`, `dn_5`, `dn_6`, `dn_ave`, `dw_1`, `dw_2`, `dw_3`, `dw_4`, `dw_5`, `dw_6`, `dw_ave`, `m1`, `m2`, `remark`,`seek_help`, `grade_status`, `ifend`) VALUES ('$i',null, null, '100', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '0','0', '0')");
		}
		//找到teacher的名字
		$teacher = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id'");
		$teacher = $teacher->fetch_assoc();
		$teacher = $teacher['name'];
		//插入一条课堂记录
		$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_course` (`exp_name`,`time`,`teacher`,`exp_name_en`)VALUES ('用气垫摆测量转动惯量','$time','$teacher','moment_inertia')");
		if(!$re) return 0;

		return 1;
	}

	function save_data_spectrometer($time, $user_id){

		$data = $this->db->query("SELECT * FROM `{$this->tablePrefix}_course_spectrometer`");
		while($result = $data->fetch_assoc()){
			//var_dump($result['stu_num']);
			// echo 'here0 ';
			if($result['stu_num']==null) continue;
			$sql = "INSERT INTO `{$this->tablePrefix}_data_spectrometer` (
			 `teacher_id`,`exp_name`,`time`,`stu_num`,`stu_name`,`grade`,`help_times`,`fail_times`,`lambda_1`,`lambda_2`,`lambda_3`,`constant`,`green_1`,`green_2`,`green_3`,`green_4`,`green_angle`,`yellow_inside_1`,`yellow_inside_2`,`yellow_inside_3`,`yellow_inside_4`,`yellow_inside_angle`,`yellow_outside_1`,`yellow_outside_2`,`yellow_outside_3`,`yellow_outside_4`,`yellow_outside_angle`,`status_1`,`d`,`E_d`,`lambda_yellow_inside`,`E_yellow_inside`,`lambda_yellow_outside`,`E_yellow_outside`,`D_color`,`D_color_theoretical`,`remark`
			) VALUES (
				'$user_id','分光计的使用及光栅衍射','$time','{$result['stu_num']}','{$result['stu_name']}','{$result['grade']}','{$result['help_times']}','{$result['fail_times']}','{$result['lambda_1']}','{$result['lambda_2']}','{$result['lambda_3']}','{$result['constant']}','{$result['green_1']}','{$result['green_2']}','{$result['green_3']}','{$result['green_4']}','{$result['green_angle']}','{$result['yellow_inside_1']}','{$result['yellow_inside_2']}','{$result['yellow_inside_3']}','{$result['yellow_inside_4']}','{$result['yellow_inside_angle']}','{$result['yellow_outside_1']}','{$result['yellow_outside_2']}','{$result['yellow_outside_3']}','{$result['yellow_outside_4']}','{$result['yellow_outside_angle']}','{$result['status_1']}','{$result['d']}','{$result['E_d']}','{$result['lambda_yellow_inside']}','{$result['E_yellow_inside']}','{$result['lambda_yellow_outside']}','{$result['E_yellow_outside']}','{$result['D_color']}','{$result['D_color_theoretical']}','{$result['remark']}'
			)"; 
			$re = $this->db->query($sql);
			if(!$re) return 0;	
			 // echo('here1 ');
			$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_student` (`stu_num`,`stu_name`,`exp_name_ch`,`exp_name_en`,`help_times`,`fail_times`,`grade`,`time`) VALUES ('{$result['stu_num']}','{$result['stu_name']}','分光计的使用及光栅衍射','spectrometer','{$result['help_times']}','{$result['fail_times']}','{$result['grade']}','$time' )");
			if(!$re) return 0;
			 // echo('here2 ');

		}
		//将course数据表实验数据清空，仅留下group_num字段值，为下次实验做准备
		//留下lambda1,lambda2,lambda3,constant
		$obj = $this->db->query("SELECT `lambda_1`,`lambda_2`,`lambda_3`,`constant` FROM `physics_course_spectrometer`WHERE 1=1");
		$this->db->query("TRUNCATE `physics_course_spectrometer`");
		for($i = 1; $i<=40; $i++){
			$data = $obj->fetch_assoc();
			$this->db->query("INSERT INTO `physics_course_spectrometer` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `lambda_1`, `lambda_2`, `lambda_3`, `constant`, `green_1`, `green_2`, `green_3`, `green_4`, `green_angle`, `yellow_inside_1`, `yellow_inside_2`, `yellow_inside_3`, `yellow_inside_4`, `yellow_inside_angle`, `yellow_outside_1`, `yellow_outside_2`, `yellow_outside_3`, `yellow_outside_4`, `yellow_outside_angle`, `status_1`, `d`, `E_d`, `lambda_yellow_inside`, `E_yellow_inside`, `lambda_yellow_outside`, `E_yellow_outside`, `D_color`, `D_color_theoretical`, `remark`, `seek_help`, `grade_status`, `ifend`) VALUES ('$i', null, null, '100', '0', '0', '0', '{$data['lambda_1']}', '{$data['lambda_2']}', '{$data['lambda_3']}', '{$data['constant']}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '0', '0', '0')");
		}

		//找到teacher的名字
		$teacher = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id'");
		$teacher = $teacher->fetch_assoc();
		$teacher = $teacher['name'];
		//插入一条课堂记录
		$re = $this->db->query("INSERT INTO `{$this->tablePrefix}_historicaldata_course` (`exp_name`,`time`,`teacher`,`exp_name_en`)VALUES ('分光计的使用及光栅衍射','$time','$teacher','spectrometer')");
		if(!$re) return 0;

		return 1;
	}

	function change_pwd($user_id,$old_pwd,$new_pwd){
		$sql = "SELECT `password` FROM `{$this->tablePrefix}_user` WHERE `uid`='$user_id' ";
		$pwd_db = $this->db->query($sql);
		$pwd_db = $pwd_db->fetch_assoc();
		$old_pwd = md5($this->db->real_escape_string($old_pwd).$this->pwdSalt);
		$result = null;
		// echo($old_pwd."\n");
		// echo($pwd_db['password']);
		if( $old_pwd === $pwd_db['password']){
			$new_pwd = md5($this->db->real_escape_string($new_pwd).$this->pwdSalt);
			$sql = "UPDATE `{$this->tablePrefix}_user` SET `password`='$new_pwd'";
			$result =  $this->db->query($sql);
		}
		if( !$result) return 0;
		else 
			return 1;
	}

	function search_info_num($stu_num){
		$sql = "SELECT * FROM `{$this->tablePrefix}_historicaldata_student` WHERE `stu_num`='$stu_num'";
		$result = $this->db->query($sql);
		return $result;
	}

	function search_info_date($date, $user_name){
		// var_dump($date);	
		if($date==''){
			$result = $this->db->query("SELECT * FROM `{$this->tablePrefix}_historicaldata_course` WHERE `teacher`='$user_name'");
		}else{
			$year = substr($date,0,4);
			$month = substr($date,5,2);
			$day = substr($date,8,2);	
			$date_db = $year."/".$month."/".$day;
			$sql = "SELECT * FROM `{$this->tablePrefix}_historicaldata_course` WHERE `time` REGEXP '{$date_db}'";
			$result = $this->db->query($sql);
		}
		return $result;	
	}

	function cur_course_name_close($user_id){
		//echo($user_id);
		$sql = "SELECT `name` FROM `{$this->tablePrefix}_status` WHERE `user_id`='$user_id' ";
		$result = $this->db->query($sql);
		return $result;
	}

	function show_detail_via_stu_num($stu_num){
		$sql = "SELECT * FROM `{$this->tablePrefix}_data_{$this->tb_name}` WHERE `stu_num`={$stu_num}";
		$result = $this->db->query($sql);
		return  $result;
	}

	function show_detail_via_date($exp_time){
		$sql = "SELECT * FROM `{$this->tablePrefix}_historicaldata_student` WHERE `time`='{$exp_time}'";
		$result = $this->db->query($sql);
		return  $result;
	}
	function return_from_exp_data($stu_num, $time){
		$sql = "SELECT * FROM `{$this->tablePrefix}_data_{$this->tb_name}` WHERE `stu_num`='{$stu_num}' AND `time`='{$time}'";
		$result = $this->db->query($sql);
		return  $result;
	}

	function user_manage(){
		$sql = "SELECT * FROM `{$this->tablePrefix}_user`";
		$result = $this->db->query($sql);
		return  $result;
	}

	function admin_change_pwd($new_pwd, $number){
		// echo $new_pwd;
		// echo $number;
		$pwd_hash = md5($this->db->real_escape_string($new_pwd).$this->pwdSalt);
		$sql = "UPDATE `{$this->tablePrefix}_user` SET `password`='$pwd_hash', `pwd`='$new_pwd' WHERE `number`='$number'";
		$result = $this->db->query($sql);
		if($result) return 1;
		else return 0;
	}
	function admin_delete_user($number){
		$sql = "DELETE FROM `{$this->tablePrefix}_user` WHERE `number`='$number'";
		$result = $this->db->query($sql);
		if($result) return 1;
		else return 0;
	}

	function add_user($number, $user_name, $pwd){
		$pwd_hash = md5($this->db->real_escape_string($pwd).$this->pwdSalt);
		$sql = "INSERT INTO `{$this->tablePrefix}_user` (`level`,`number`,`name`,`password`,`pwd`) VALUES ('2','$number','$user_name','$pwd_hash','$pwd')";
		$result = $this->db->query($sql);
		if($result) return 1;
		else return 0;
	}

	function remark_submit($group_num, $remark, $grade_modified){
		// echo($remark);
		// var_dump($grade_modified);
		if($remark != ''){
			// echo"here1";
			$re1 = $this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `remark`='$remark' WHERE `group_num`='$group_num'");
			if(!$re1) return 0;
		}
		if($grade_modified != ''){
			// echo("here2");
			$re2 = $this->db->query("UPDATE `{$this->tablePrefix}_course_{$this->tb_name}` SET `grade`='$grade_modified',`grade_status`=1,`ifend`=1 WHERE `group_num`='$group_num'");
			if(!$re2) return 0;
		}
		
		return 1;
	}

	function set_parameter($exp_name){
		switch($exp_name){
			case 'oscillograph':
				for( $i = 1; $i <= 40; $i++){
					$v_std = rand(1,8)*0.5 + 1;
					$v_std = sprintf("%.1f",$v_std);   //Vp-p在1V到5V之间，间隔0.5V
					$f_std = rand(1,18)*50 + 100;	   //f在100hz到1000hz之间，间隔步进50hz。
					$this->db->query("UPDATE `{$this->tablePrefix}_course_oscillograph` SET `v_std`='$v_std',`f_std`='$f_std' WHERE `group_num`='$i'");
				}
				$result = $this->db->query("SELECT `group_num`,`v_std`,`f_std` FROM `{$this->tablePrefix}_course_{$this->tb_name}` ORDER BY `group_num`");
				break;
			case 'potentioneter':
				for( $i = 1; $i <= 40; $i++){
					$Exs = rand(1,8)*0.5 + 1;
					$Exs = sprintf("%.1f",$Exs);   //Exs范围： 
					$this->db->query("UPDATE `{$this->tablePrefix}_course_oscillograph` SET `Exs`='$Exs' WHERE `group_num`='$i'");
				}
				$result = $this->db->query("SELECT `group_num`,`Exs` FROM `{$this->tablePrefix}_course_{$this->tb_name}` ORDER BY `group_num`");
				break;
			default:
				$result = null; 
				break;
		}
		return $result;
	}

	function query_parameter_oscillograph(){
		$result = $this->db->query("SELECT `group_num`,`v_std`,`f_std` FROM `{$this->tablePrefix}_course_{$this->tb_name}` ORDER BY `group_num`");

		return $result;
	}

	function query_parameter_potentioneter(){
		$result = $this->db->query("SELECT `group_num`,`Exs` FROM `{$this->tablePrefix}_course_{$this->tb_name}` ORDER BY `group_num`");
		return $result;
	}

	function change_parameter_oscillograph($group_num, $v_std, $f_std){
		$result = $this->db->query("UPDATE `{$this->tablePrefix}_course_oscillograph` SET `v_std`='$v_std',`f_std`='$f_std' WHERE `group_num`='$group_num'");
		if( $result) return 1;
		else return 0;
	}

	function change_parameter_potentioneter($group_num, $Exs){
		$result = $this->db->query("UPDATE `{$this->tablePrefix}_course_potentioneter` SET `Exs`='$Exs' WHERE `group_num`='$group_num'");
		if( $result) return 1;
		else return 0;
	}

	function modified_course_status($exp_name, $user_id){
		$sql = "SELECT `course_id` FROM `{$this->tablePrefix}_status` WHERE `name`='{$exp_name}'";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		$sql = "UPDATE `{$this->tablePrefix}_user` SET `cur_course`={$result['course_id']} WHERE `uid`='$user_id'";
		$result = $this->db->query($sql);
		if($result)
			return 1;
		else return 0;
	}

	function modified_course_status_newton($user_id, $para){
		// vardump($para);
		$para = json_decode($para);

		// for($i = 4; $i<=40; $i++){
		// 	$this->db->query("INSERT INTO `physics_course_newton` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `radius`, `L6`, `L7`, `L8`, `L9`, `L10`, `L11`, `L12`, `L13`, `L14`, `L15`, `R6`, `R7`, `R8`, `R9`, `R10`, `R11`, `R12`, `R13`, `R14`, `R15`, `d6`, `d7`, `d8`, `d9`, `d10`, `d11`, `d12`, `d13`, `d14`, `d15`, `q6`, `q7`, `q8`, `q9`, `q10`, `q11`, `q12`, `q13`, `q14`, `q15`, `status_1`, `remark`) VALUES ('$i', '081510311', '1212', '12', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '')");
		// }

		// for($i=4; $i<=40; $i++){
		// 	$this->db->query("INSERT INTO `physics_course_thermal_conductivity` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `T_1`, `T_2`, `t1`, `t2`, `t3`, `t4`, `t5`, `t6`, `t7`, `t8`, `t9`, `t10`, `te1`, `te2`, `te3`, `te4`, `te5`, `te6`, `te7`, `te8`, `te9`, `te10`, `change_rate`, `hb1`, `hb2`, `hb3`, `hb4`, `hb5`, `hb6`, `hb_ave`, `db`, `status_1`, `hc1`, `hc2`, `hc3`, `hc4`, `hc5`, `hc6`, `hc_ave`, `dc`, `m`, `status_2`, `remark`) VALUES ('$i', '081510188', 'Rose', '100', '0', '0', '0', '2', '2', '2', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5', '5', '8', '5', '5', '5', '5', '4', '4', '8', '8', '8', '8', '8', '89', '8', '8', '8', '8', '8', '8', '8', '8', '8', '8', '8', '6', NULL);");
		// }


		for($i = 1; $i<=40; $i++){
			$data = $para[$i-1];
			// echo $data;
			// sleep(1000);
			$result = $this->db->query("UPDATE `physics_course_newton` SET `radius`='$data' WHERE `group_num`='$i'");
			if(!$result) return 0;
		}

		//教师插入上课记录
		$sql = "SELECT `course_id` FROM `{$this->tablePrefix}_status` WHERE `name`='newton'";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		$sql = "UPDATE `{$this->tablePrefix}_user` SET `cur_course`={$result['course_id']} WHERE `uid`='$user_id'";
		$result = $this->db->query($sql);
	    // echo('gaga');
		if(!$result)
			return 0;
		return 1;
	}

	function modified_course_status_spectrometer($user_id, $para){
		// 	for($i=2; $i<=40; $i++){
		// 	$this->db->query("
		// 	INSERT INTO `physics_course_spectrometer` (`group_num`, `stu_num`, `stu_name`, `grade`, `help_times`, `fail_times`, `evaluation`, `lambda_1`, `lambda_2`, `lambda_3`, `constant`, `green_1`, `green_2`, `green_3`, `green_4`, `green_angle`, `yellow_inside_1`, `yellow_inside_2`, `yellow_inside_3`, `yellow_inside_4`, `yellow_inside_angle`, `yellow_outside_1`, `yellow_outside_2`, `yellow_outside_3`, `yellow_outside_4`, `yellow_outside_angle`, `status_1`, `d`, `E_d`, `lambda_yellow_inside`, `E_yellow_inside`, `lambda_yellow_outside`, `E_yellow_outside`, `D_color`, `D_color_theoretical`, `remark`) VALUES ('$i', '081510111', '和其正', '99', '0', '0', '0', '1', '2', '2', '2', '2', '6', '5', '5', '6', '65', '5', '5', '5', '5', '5', '5', '5', '5', '6', '65', '3', '2', '5', '5', '5', '3', '6', '6', '')");
		// }

		$para = json_decode($para);

 		$result = $this->db->query("UPDATE `physics_course_spectrometer` SET `lambda_1`='{$para['0']}', `lambda_2`='{$para['1']}', `lambda_3`='{$para['2']}'");
		if(!$result) return 0;
		for($i = 1; $i<=40; $i++){
			$data = $para[$i-1+3];
			// echo $data;
			// sleep(1000);
			$result = $this->db->query("UPDATE `physics_course_spectrometer` SET `constant`='$data' WHERE `group_num`='$i'");
			if(!$result) return 0;
		}

		//教师插入上课记录
		$sql = "SELECT `course_id` FROM `{$this->tablePrefix}_status` WHERE `name`='spectrometer'";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		$sql = "UPDATE `{$this->tablePrefix}_user` SET `cur_course`={$result['course_id']} WHERE `uid`='$user_id'";
		$result = $this->db->query($sql);
	    // echo('gaga');
		if(!$result)
			return 0;
		return 1;
	}

	function has_para_newton(){
		$result = $this->db->query("SELECT `radius` FROM `{$this->tablePrefix}_course_newton` WHERE `radius`");
		// var_dump($result);
		if( ($result->num_rows) == 0) return null;
		else return $result;
	}

	function has_para_spectrometer(){
	
		$result = $this->db->query("SELECT `constant` FROM `{$this->tablePrefix}_course_spectrometer` WHERE `constant`");
		 // var_dump($result);
		if( ($result->num_rows) == 0) return null;
		else return $result;
	}

	function has_para_lambda(){
		$result = $this->db->query("SELECT `lambda_1`,`lambda_2`,`lambda_3` FROM `{$this->tablePrefix}_course_spectrometer` WHERE `lambda_1`");
		  // var_dump($result);
		if( ($result->num_rows) == 0) return null;
		else return $result;
	}

	function echo_course_status($num){
		$result = $this->db->query("SELECT `name` FROM `{$this->tablePrefix}_user` WHERE `cur_course`='$num'");
		if(!$result) return null;
		$result = $result->fetch_assoc();
		return $result['name'];
	}

	function data_detail($exp_name,$stu_num){
		$result = $this->db->query("SELECT * FROM `{$this->tablePrefix}_data_{$exp_name}` WHERE `stu_num`='$stu_num'");
		if(!$result) return null;
		$result = $result->fetch_assoc();
		return $result;
	}

	function query_end_time($exp_name){
		$end_time = $this->db->query("SELECT `start_time`,`end_time` FROM `{$this->tablePrefix}_status` WHERE `name`='$exp_name'");
		$end_time = $end_time->fetch_assoc();
		$end_time = $end_time['end_time'];
		return $end_time;
	}

	// function end_when_time_out($exp_name, $count){
	// 	//如果一直到下课，学生仍没有提交数据，实验算为未完成，成绩为0
	// 	$this->db->query("UPDATE `{$this->tablePrefix}_course_{$exp_name}` SET `grade`=0, `grade_status`=1 WHERE `status_{$count}`=0");
	// }
}