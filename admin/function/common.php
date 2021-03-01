<?php
	//公共函数

	//防止恶意调用
	if(!defined('exp')){
		exit('Acess denied!');
	}
	
	include_once dirname(__FILE__).'/database.class.php';
	
	function _alert($msg){
		echo "<script>alert('$msg');</script>";
	}

	function _alert_back($msg){
		echo"<script>alert('$msg');history.back();</script>";
	}

	function _location($info,$url){
		if(!empty($info)){
			echo "<script>alert('$info');location.href='$url';</script>";
			exit();
		}else{
			header('Location:'.$url);	
		}
	}


	function check_login($user_name, $password){
		$obj = new database;
		$result = $obj->check_login($user_name, $password);
		return $result;
 	}

	/*计算课程所属的时间段，因学校而异，最早提交半小时开启课堂*/
 	function course_time(){
 		date_default_timezone_set('Asia/Shanghai');
		$cur_time = getdate();
		$hours = $cur_time['hours'];
		$minutes = $cur_time['minutes'];
		//$hours = 9; $minutes = 44;		//测试使用
		$compare = $hours*60 + $minutes;				
		if( $compare>=450 && $compare<=584){
			$time = '8:00--9:45';
		}else if( $compare>=585 && $compare<=719){
			$time = '10:15--12:00';
		}else if( $compare>=810 && $compare<=944){
			$time = '14:00--15:45';
		}else if( $compare>=945 && $compare<=1079){
			$time = '16:15--18:00';
		}else if( $compare>=1110 && $compare<=1229){
			$time = '18:45--20:30';
		}
	
		if(isset($time)) return ($time);
		else return "wrong time!";		
 	}

 	function ymd_date(){
 		date_default_timezone_set('Asia/Shanghai');
 		return date('Y/m/d');
 	}


 	function detail_data($exp_name,$group_num){
 		$obj = new database($exp_name);
		$result = $obj->detail_data($group_num);
		return $result;
 	}


	function pass($exp_name,$option,$group_num){
 		$obj = new database($exp_name);
 		$result = $obj->pass($option,$group_num);
 		return $result;
 	}

 	function fail($exp_name,$option,$group_num){
 		$obj = new database($exp_name);
 		$result = $obj->fail($option,$group_num);
 		return $result;
 	}

 	function solve_help($exp_name,$group_num){
 		$obj = new database($exp_name);
 		$obj->solve_help($group_num);
 	}

 	function course_status($course_id,$user_id){
 		$obj = new database();
 		$obj->course_status_start($course_id,$user_id);
 	}

 	function if_cur_course($user_id){
 		//查询当前教师是否有正在进行的课程
 		$obj = new database();
 		$result = $obj->if_cur_course($user_id);
 		//var_dump($result);
 		return $result;
 	}

 	function cur_course_name($user_id){
 		$obj = new database();
 		$result = $obj->cur_course_name($user_id);
 		return $result;
 	}

 	function start_course($user_id, $course_name, $classNum){
 		$obj = new database();
 		$result = $obj->start_course($user_id,$course_name,$classNum);
 		return $result;
 	}

	function close_course($user_id,$course_name){
		$obj = new database();
 		$result = $obj->close_course($user_id,$course_name);
 		return $result;
	}

	function save_data($course_name, $user_id){
		$obj = new database();
		$time = ymd_date().' '.course_time();
		$result = $obj->save_data($course_name,$time,$user_id);
		return $result;
	}

	function change_pwd($user_id,$old_pwd,$new_pwd){
		$obj = new database();
 		$result = $obj->change_pwd($user_id,$old_pwd,$new_pwd);
 		return $result;
	}

	function cur_course_name_close($user_id){	
		$obj = new database();
 		$result = $obj->cur_course_name_close($user_id);
 		$result = $result->fetch_assoc();
 		$name = $result['name']; 
 		//echo("\n$name:".$name);
 		return $name;
	}

 	function search_info_num($stu_num){
 		$obj = new database();
 		$result = $obj->search_info_num($stu_num);
 		$out = "";
 		if($result){	

 				$flag = 1;
	 			while($data = $result->fetch_assoc()){
	 				if($flag==1){
	 					
				 		$name = $data['stu_name'];
				 		$out .= "<div class='search-stu-info'>";
				 		$out .= "学号：<span class='flag_stu_num'>".$stu_num."</span>";
				 		$out .= "姓名：".$name."\n";
				 		$out .= "</div>";
				 		$flag = 0;
	 				}
	 				$out .= "<tr name='".$data['order']."'>";	
		 				$out .= "<td>".$data['time']."</td>";
		 				//$out .= "<td>".$data['stu_name']."</td>"; 
		 				$out .= "<td class='exp_name' name='".$data['exp_name_en']."'>".$data['exp_name_ch']."</td>";
		 				$out .= "<td>".$data['help_times']."</td>";
		 				$out .= "<td>".$data['fail_times']."</td>";
		 				$out .= "<td>".$data['grade']."</td>";
		 				$out .= "<td><button onclick='detail_via_stu_num(this)' class='button-detail'>查看</button></td>";
	 				$out .= "</tr>";
	 			}
	 		
	 	}
 		return $out;

 	}

 	function search_info_date($date,$user_name){
 		$out = "";
 		$obj = new database();
 		$result = $obj->search_info_date($date,$user_name);

 		if($date==''){
 			$date1 = '全部显示';
 			if($result){
	 			$flag = 1;		
	 			while($data = $result->fetch_assoc()){
	 				if($flag == 1){
	 					$out .= "<div class='search-stu-info' style='padding-left: 310px'>";
	 					$out .= "查询日期：&nbsp;&nbsp;&nbsp;".$date1."</div>";
	 					$flag += 1;
	 				}
	 				$out .= "<tr name='".$data['order']."'>";	
	 					$time = $data['time'];
	 					$out .= "<td class='exp_time' name='".$data['time']."'>".$time."</td>";
		 				$out .= "<td class='exp_name' name='".$data['exp_name_en']."'>".$data['exp_name']."</td>";
		 				$out .= "<td>".$data['teacher']."</td>";
		 				$out .= "<td><button onclick='detail_via_date(this)' class='button-detail'>查看</button></td>";
	 				$out .= "</tr>";
	 			}
	 	
	 	}
 		}else{
 			$date1 = $date;
 			if($result){
	 			$flag = 1;		
	 			while($data = $result->fetch_assoc()){
	 				if($flag == 1){
	 					$out .= "<div class='search-stu-info' style='padding-left: 310px'>";
	 					$out .= "查询日期：&nbsp;&nbsp;&nbsp;".$date1."</div>";
	 					$flag += 1;
	 				}
	 				$out .= "<tr name='".$data['order']."'>";	
	 					$time = substr($data['time'],11,12);
	 					$out .= "<td class='exp_time' name='".$data['time']."'>".$time."</td>";
		 				$out .= "<td class='exp_name' name='".$data['exp_name_en']."'>".$data['exp_name']."</td>";
		 				$out .= "<td>".$data['teacher']."</td>";
		 				$out .= "<td><button onclick='detail_via_date(this)' class='button-detail'>查看</button></td>";
	 				$out .= "</tr>";
	 			}
	 	
	 	}
 		}
 		
 		return $out;
 	}

 	function init_info_oscillograph(){
 		//非公共函数
 		$out = "";
 		for( $i = 1; $i<=40; $i++){		
			$out .="
				<tr>
					<td>".$i."</td>
					<td style='width:90px'></td>
					<td style='width:90px'></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}
		echo $out;
 	}

 	function init_info_potentioneter(){
 		$out = "";
 		for( $i = 1; $i<=40; $i++){		
			$out .="
				<tr>
					<td>".$i."</td>
					<td style='width:90px'></td>
					<td style='width:90px'></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}
		echo $out;
 	}

 	function init_info_thermal_conductivity(){
 		//非公共函数
 		$out = "";
 		for( $i = 1; $i<=40; $i++){		
			$out .="
				<tr>
					<td>".$i."</td>
					<td style='width:90px'></td>
					<td style='width:90px'></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}
		echo $out;
 	}

 	function init_info_newton(){
 		//非公共函数
 		$out = "";
 		for( $i = 1; $i<=40; $i++){		
			$out .="
				<tr>
					<td>".$i."</td>
					<td style='width:90px'></td>
					<td style='width:90px'></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}
		echo $out;
 	}
 
 	function init_info_moment_inertia(){
		//非公共函数
 		$out = "";
 		for( $i = 1; $i<=40; $i++){		
			$out .="
				<tr>
					<td>".$i."</td>
					<td style='width:90px'></td>
					<td style='width:90px'></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}
		echo $out;
 	}

 	function init_info_spectrometer(){
		//非公共函数
 		$out = "";
 		for( $i = 1; $i<=40; $i++){		
			$out .="
				<tr>
					<td>".$i."</td>
					<td style='width:90px'></td>
					<td style='width:90px'></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
		}
		echo $out;
 	}

 	function init_help(){
 		//初始化求助栏
 		$out = "";
 		$out .= "
			<h4 class='title'>有<span class='count_help'>0</span>条求助信息:</h4>
 			<ul class='group_list' name='0'>
 			</ul>";
		echo $out;
 		
 	}


 	function init_evaluating(){
 		//初始化待评测栏
 		$out = "";
 		$out .= "
 			<h4 class='title'>有<span class='count_evaluating'>0</span>条待评测（按提交顺序）:</h4>
 			<ul class='group_list' name='0'></ul>";
		echo $out;
 	}

 	function show_detail_via_stu_num($stu_num,$exp_name){
 		$obj = new database($exp_name);
 		$result = $obj->show_detail_via_stu_num($stu_num);
 		$out = "";

 		switch($exp_name){
 			case 'oscillograph':
	 			$result = $result->fetch_assoc();
	 			$out .= "
					<div class='popup-bg' id='popup-bg' style='display: block'>
						<div class='popup-detail' >
						<button class='detail-close' onclick='search_close_popup()'><span aria-hidden='true'>×</span></button>

						<div class='stu_info' style='margin-top: 0px'>
					        <span>姓名：<span id='stu_name'>".$result['stu_name']."</span></span>
					        <span>学号：<span id='stu_num'>".$result['stu_num']."</span></span>
					    </div>

				 		 <div class='table_titile' style='margin-top: 80px; margin-bottom: 35px'>一、测量正弦波</div>
				         <table class='tb-content'>
				            <thead>
					            <tr>
					                <th>Vp-p（标准）</th>
					                <th>f（标准）</th>
					                <th>V/DIV</th>
					                <th>Dy</th>
					                <th>V'p-p</th>
					                <th>Ev</th>
					                <th>TIME/DIV</th>
					                <th>n</th>
					                <th>Dx</th>
					                <th>T'</th>
					                <th>f'</th>
					                <th>Ef</th>
					            </tr>
				            </thead>
				            <tbody>
					            <tr>
					                <td>".$result['v_std']."</td>
					                <td>".$result['f_std']."</td>
					                <td>".$result['V_DIV']."</td>
					                <td>".$result['Dy']."</td>
					                <td>".$result['v_up']."</td>
					                <td>".$result['E_v']."</td>
					                <td>".$result['TIME_DIV']."</td>
					                <td>".$result['n']."</td>
					                <td>".$result['Dx']."</td>
					                <td>".$result['T']."</td>
					                <td>".$result['f_up']."</td>
					                <td>".$result['E_f']."</td>
				            </tr>
				            </tbody>
				        </table>
	
						<div class='table_titile'>二、李萨如图形</div>
				        
				        <table class='tb-content' style='width:400px'>
				            <tr>
				                <th>Nx</th>
				                <td>".$result['Nx1']."</td>
				                <td>".$result['Nx2']."</td>
				                <td>".$result['Nx3']."</td>
				                <td>".$result['Nx4']."</td>
				            </tr>
				            <tr>
				                <th>Ny</th>
				                <td>".$result['Ny1']."</td>
				                <td>".$result['Ny2']."</td>
				                <td>".$result['Ny3']."</td>
				                <td>".$result['Ny4']."</td>
				            </tr>
				            <tr>
				                <th>fy（hz）</th>
				                <td>".$result['fy1']."</td>
				                <td>".$result['fy2']."</td>
				                <td>".$result['fy3']."</td>
				                <td>".$result['fy4']."</td>
				            </tr>
				        </table>		 

				    </div>
				</div>";
				break;
			case 'potentioneter':
	 			$result = $result->fetch_assoc();
	 			$out .= "
					<div class='popup-bg' id='popup-bg' style='display: block'>
						<div class='popup-detail' >
						<button class='detail-close' onclick='search_close_popup()'><span aria-hidden='true'>×</span></button>

						<div class='stu_info' style='margin-top: 0px'>
					        <span>姓名：<span id='stu_name'>".$result['stu_name']."</span></span>
					        <span>学号：<span id='stu_num'>".$result['stu_num']."</span></span>
					    </div>
				 		<div class='table_titile' style='margin-top: 80px; margin-bottom: 35px'>一、定标</div>
				        <table class='tb-content'>
							<thead>
					            <tr>
					                <th>U_ab</th>
					                <th>U_0</th>
					                <th>I_s</th>
					                <th>Rab</th>
					                <th>Is</th>
					                <th>U0</th>
					                <th>Uab</th>
					                <th>E(误差)</th>
					            </tr>
				            </thead>
				            <tbody>
					            <tr>
					                <td>".$result['U_ab']."</td>
					                <td>".$result['U_0']."</td>
					                <td>".$result['I_s']."</td>
					                <td>".$result['Rab']."</td>
					                <td>".$result['Is']."</td>
					                <td>".$result['U0']."</td>
					                <td>".$result['Uab']."</td>
					                <td>".$result['E_e']."</td>	
				            	</tr>
				            </tbody>
				        </table>
	
						<div class='table_titile'>二、测量电动势</div>
				        
				        <table class='tb-content' style='width:400px'>
				            <tr>
				                <th>次数n</th>
				                <th>1</th>
				                <th>2</th>
				                <th>3</th>
				                <th>4</th>
				                <th>5</th>
				                <th>6</th>
				                <th>平均值lx</th>
					        </tr>
							<tr>
								<td></td>
				                <td>".$result['Lx1']."</td>
				                <td>".$result['Lx2']."</td>
				                <td>".$result['Lx3']."</td>
				                <td>".$result['Lx4']."</td>
				                <td>".$result['Lx5']."</td>
				                <td>".$result['Lx6']."</td>
				                <td>".$result['Lx_ave']."</td>
				            </tr>
				        </table>		 

				    </div>
				</div>";
				break;
			case 'moment_inertia':
	 			$result = $result->fetch_assoc();
	 			$out .= "
					<div class='popup-bg' id='popup-bg' style='display: block'>
						 <div class='popup-detail' style='height: 670px; width: 770px'>
						 <button class='detail-close' onclick='close_popup()' style='margin-top: -25px'><span aria-hidden='true'>×</span></button>

					        <div class='stu_info' style='margin-top: 0px'>
					            <span>姓名：<span id='stu_name'>".$result['stu_name']."</span></span>
					            <span>学号：<span id='stu_num'>".$result['stu_num']."</span></span>
					        </div>

							<div class='table_titile' style='margin-top: 10px'>数据表一、</div>

					        <table class='tb-content' >

						            <tr>
						                <td>物体</td>
						                <td width='100px'>摆轮</td>
						                <td>摆轮+圆环</td>
						                <td width='120px'>摆轮+飞机模型</td>
						                <td width='120px'>摆轮+两圆柱</td>
					            	</tr>

					            	 <tr>
						                <td style='position: relative;'>
						                	<div class='moment_inertia_time'>时间t</div>
						                <!-- 	<div class='moment_inertia_slash'></div> -->
						                 	<canvas id='moment_inertia_slash' width='120px' height='47px'></canvas>
						                	<div class='moment_inertia_order'>次数</div>
						                </td>

						                <script type='text/javascript'>
						                	//绘制斜线
						                	var canvas = document.getElementById('moment_inertia_slash');
				        					var context = canvas.getContext('2d');//二维
						                	context.lineWidth = 0.2; // 线条宽度
				       						context.strokeStyle = 'rgb(0, 0, 0)'; // 线条颜色
				        					context.lineCap = 'round'; // 线头形状
				       						context.moveTo(0, 0); // 起点
				       						context.lineTo(120, 47); // 终点
									        // 绘制
									        context.stroke();
						                </script>

						                <td>t<sub>0</sub></td>
						                <td>t<sub>1</sub></td>
						                <td>t<sub>2</sub></td>
						                <td>t<sub>3</sub></td>
					            	</tr>

					            	<tr>
					            		<td>1</td>
					            		<td>".$result['t0_1']."</td>
					            		<td>".$result['t1_1']."</td>
					            		<td>".$result['t2_1']."</td>
					            		<td>".$result['t3_1']."</td>
					            	</tr>

					            	<tr>
					            		<td>2</td>
					            		<td>".$result['t0_2']."</td>
					            		<td>".$result['t1_2']."</td>
					            		<td>".$result['t2_2']."</td>
					            		<td>".$result['t3_2']."</td>
					            	</tr>

					            	<tr>
					            		<td>3</td>
					            		<td>".$result['t0_3']."</td>
					            		<td>".$result['t1_3']."</td>
					            		<td>".$result['t2_3']."</td>
					            		<td>".$result['t3_3']."</td>
					            	</tr>

					            	<tr>
					            		<td>4</td>
					            		<td>".$result['t0_4']."</td>
					            		<td>".$result['t1_4']."</td>
					            		<td>".$result['t2_4']."</td>
					            		<td>".$result['t3_4']."</td>
					            	</tr>

					            	<tr>
					            		<td>5</td>
					            		<td>".$result['t0_5']."</td>
					            		<td>".$result['t1_5']."</td>
					            		<td>".$result['t2_5']."</td>
					            		<td>".$result['t3_5']."</td>
					            	</tr>

					            	<tr>
					            		<td>6</td>
					            		<td>".$result['t0_6']."</td>
					            		<td>".$result['t1_6']."</td>
					            		<td>".$result['t2_6']."</td>
					            		<td>".$result['t3_6']."</td>
					            	</tr>

					            	<tr>
					            		<td>t（平均）</td>
					            		<td>".$result['t0_ave']."</td>
					            		<td>".$result['t1_ave']."</td>
					            		<td>".$result['t2_ave']."</td>
					            		<td>".$result['t3_ave']."</td>
					            	</tr>

					        </table>

					        <div class='table_titile' style='margin-top: 20px'>数据表二、</div>

					        <table class='tb-content'>
					        	<tr>
									<td colspan='2' width='200px'>次序</td>
									<td width='60px'>1</td>
									<td width='60px'>2</td>
									<td width='60px'>3</td>
									<td width='60px'>4</td>
									<td width='60px'>5</td>
									<td width='60px'>6</td>
									<td width='60px'>平均</td>
					        	</tr>

					        	<tr>
					        		<td rowspan='2'>圆柱</td>
					        		<td>直径d/cm</td>
					        		<td>".$result['d1']."</td>
					        		<td>".$result['d2']."</td>
					        		<td>".$result['d3']."</td>
					        		<td>".$result['d4']."</td>
					        		<td>".$result['d5']."</td>
					        		<td>".$result['d6']."</td>
					        		<td>".$result['d_ave']."</td>
					        	</tr>

					        	<tr>
					        		<td>距离x/cm</td>
					        		<td colspan='4'>".$result['x']."</td>
					        		<td colspan='4'>".$result['x_theoretical']."</td>
					        	</tr>


					        	<tr>
					        		<td rowspan='2'>圆环</td>
					        		<td>d<sub>内</sub>/cm</td>
					        		<td>".$result['dn_1']."</td>
					        		<td>".$result['dn_2']."</td>
					        		<td>".$result['dn_3']."</td>
					        		<td>".$result['dn_4']."</td>
					        		<td>".$result['dn_5']."</td>
					        		<td>".$result['dn_6']."</td>
					        		<td>".$result['dn_ave']."</td>
					        	</tr>

					        	<tr>
					        		<td>d<sub>外</sub>/cm</td>
					        		<td>".$result['dw_1']."</td>
					        		<td>".$result['dw_2']."</td>
					        		<td>".$result['dw_3']."</td>
					        		<td>".$result['dw_4']."</td>
					        		<td>".$result['dw_5']."</td>
					        		<td>".$result['dw_6']."</td>
					        		<td>".$result['dw_ave']."</td>
					        	</tr>

					        	<tr>
					        		<td rowspan='2'>质量</td>
					        		<td>圆环m1/g</td>
					        		<td colspan='7'>".$result['m1']."</td>
					        	</tr>

					        	<tr>
					        		<td>圆环m1/g</td>
					        		<td colspan='7'>".$result['m2']."</td>
					        	</tr>

					        </table>

					    </div>
					</div>
					";
				break;
			case 'spectrometer':
				$result = $result->fetch_assoc();
				$out .= "
					<div class='popup-bg' id='popup-bg' style='display: block'>
						<div class='popup-detail' style='height: 550px; width: 1100px' >
							<button class='detail-close' onclick='search_close_popup()'><span aria-hidden='true'>×</span></button>
							
							<div class='detail_content'>
								<div class='stu_info' style='margin-top: 20px; margin-left: 30px'>
							        <span>姓名：<span id='stu_name'>".$result['stu_name']."</span></span>
							        <span>学号：<span id='stu_num'>".$result['stu_num']."</span></span>
							    </div>
					
							    <div class='table_titile' style='margin-top: 40px'>一、光栅衍射</div>

						        <table class='tb-content' >
						           	<tr>
						           		<td rowspan='2'></td>
						           		<td colspan='2'>k = 1</td>
						           		<td colspan='2'>k = -1</td>
						           		<td>衍射角</td>
						           	</tr>

									<tr>
										<td>&#934;<sub>1</sub></td>
										<td>&#934;<sub>2</sub></td>
										<td>&#934;<sub>10</sub></td>
										<td>&#934;<sub>20</sub></td>
										<td>&#966;</td>
									</tr>	

									<tr>
										<td>绿光</td>
										<td>".$result['green_1']."</td>
										<td>".$result['green_2']."</td>
										<td>".$result['green_3']."</td>
										<td>".$result['green_4']."</td>
										<td>".$result['green_angle']."</td>
									<tr>

									<tr>
										<td>黄光(内)</td>
										<td>".$result['yellow_inside_1']."</td>
										<td>".$result['yellow_inside_2']."</td>
										<td>".$result['yellow_inside_3']."</td>
										<td>".$result['yellow_inside_4']."</td>
										<td>".$result['yellow_inside_angle']."</td>
									</tr>

									<tr>
										<td>黄光(外)</td>
										<td>".$result['yellow_outside_1']."</td>
										<td>".$result['yellow_outside_2']."</td>
										<td>".$result['yellow_outside_3']."</td>
										<td>".$result['yellow_outside_4']."</td>
										<td>".$result['yellow_outside_angle']."</td>
									</tr>	          
						        </table>

								        <p class='spectrometer_p'>
								        	<span>d = <span id='d'>".$result['d']."</span>nm</span>
								        	<span>d理论值 = <span id='d_theoretical'>".$result['constant']."</span>nm</span>
								        	<span style='margin-right: 0'>相对误差 E<sub>d</sub> = <span id='E_d'>".$result['E_d']."</span></span> 
								        </p>

								       	<p class='spectrometer_p'>   
								        	<span>&#955;<sub>黄内</sub> = <span id='lambda_yellow_inside'>".$result['lambda_yellow_inside']."</span>nm</span>
								        	<span>E<sub>黄内</sub> = <span id='E_lambda_yellow_inside'>".$result['E_yellow_inside']."</span></span>
								        </p>

								        <p class='spectrometer_p'>   
								        	<span>&#955;<sub>黄外</sub> = <span id='lambda_yellow_outside'>".$result['lambda_yellow_outside']."</span>nm</span>
								        	<span>E<sub>黄外</sub> = <span id='E_lambda_yellow_outside'>".$result['E_yellow_outside']."</span></span>
								        </p>
								        
								       	<p class='spectrometer_p'>
								        	<span>色速率 D = <span id='D'>".$result['D_color']."</span></span>
								        	<span>D理论值 = <span id='D_theoretical'>".$result['D_color_theoretical']."</span></span>
								       	</p>

							</div>
								
							<div class='detail_picture_spectrometer' style='background-image: url(./upload/spectrometer/".$result['stu_num'].".jpg)'></div>
										
						</div>
					</div>

				";
				break;
			case 'newton':
				$result = $result->fetch_assoc();
				// var_dump($result);
				$out .= "
					<div class='popup-bg' id='popup-bg'  style='display: block'>
						<div class='popup-detail' style='height: 550px; width: 1100px'>
							<button class='detail-close' onclick='search_close_popup()''><span aria-hidden='true'>×</span></button>
							<div class='detail_content'>
		        				<div class='stu_info' style='margin-top: 20px; margin-left: 30px'>
						            <span>姓名：<span id='stu_name'>".$result['stu_name']."</span></span>
							        <span>学号：<span id='stu_num'>".$result['stu_num']."</span></span>
		        				</div>

								<div class='table_titile' style='margin-top: 20px'>一、测量曲率半径</div>

						        <table class='tb-content' >
						            <tr>
						                <td rowspan='2'>环序/m</td>
						                <td colspan='2'>显微镜读数/mm</td>
						                <td rowspan='2'>环的直径d<sub>m</sub>/mm</td>
						                <td rowspan='2'>(d<sub>m</sub>)<sup>2</sup>/mm<sup>2</sup></td>
					            	</tr>

					            	<tr>
					            		<td>左方</td>
					            		<td>右方</td>
					            	</tr>

					            	<tr>
					            		<td>6</td>
					            		<td>".$result['L6']."</td>
					            		<td>".$result['R6']."</td>
					            		<td>".$result['d6']."</td>
					            		<td>".$result['q6']."</td>
					            	</tr>

					            	<tr>
					            		<td>7</td>
					            		<td>".$result['L7']."</td>
					            		<td>".$result['R7']."</td>
					            		<td>".$result['d7']."</td>
					            		<td>".$result['q7']."</td>
					            	</tr>

					            	<tr>
					            		<td>8</td>
					            		<td>".$result['L8']."</td>
					            		<td>".$result['R8']."</td>
					            		<td>".$result['d8']."</td>
					            		<td>".$result['q8']."</td>
					            	</tr>

					            	<tr>
					            		<td>9</td>
					            		<td>".$result['L9']."</td>
					            		<td>".$result['R9']."</td>
					            		<td>".$result['d9']."</td>
					            		<td>".$result['q9']."</td>
					            	</tr>

					            	<tr>
					            		<td>10</td>
					            		<td>".$result['L10']."</td>
					            		<td>".$result['R10']."</td>
					            		<td>".$result['d10']."</td>
					            		<td>".$result['q10']."</td>
					            	</tr>

					            	<tr>
					            		<td>11</td>
					            		<td>".$result['L11']."</td>
					            		<td>".$result['R11']."</td>
					            		<td>".$result['d11']."</td>
					            		<td>".$result['q11']."</td>
					            	</tr>

					            	<tr>
					            		<td>12</td>
					            		<td>".$result['L12']."</td>
					            		<td>".$result['R12']."</td>
					            		<td>".$result['d12']."</td>
					            		<td>".$result['q12']."</td>
					            	</tr>

					            	<tr>
					            		<td>13</td>
					            		<td>".$result['L13']."</td>
					            		<td>".$result['R13']."</td>
					            		<td>".$result['d13']."</td>
					            		<td>".$result['q13']."</td>
					            	</tr>

					            	<tr>
					            		<td>14</td>
					            		<td>".$result['L14']."</td>
					            		<td>".$result['R14']."</td>
					            		<td>".$result['d14']."</td>
					            		<td>".$result['q14']."</td>
					            	</tr>

					            	<tr>
					            		<td>15</td>
					            		<td>".$result['L15']."</td>
					            		<td>".$result['R15']."</td>
					            		<td>".$result['d15']."</td>
					            		<td>".$result['q15']."</td>
					            	</tr>
					            	
					       		</table>

						        <div class='newton_result'>
						        	<span>逐差法计算结果： R = <span id='R_commit'>".$result['radius_commit']."</span></span>
						        	<span>理论值 R'= <span id='R_set'>".$result['radius']."</span></span>
						        	<span style='margin-right: 0'>相对误差 E = <span id='E_R'>".$result['E_R']."</span></span>    
						        		
						       	</div>

						    </div>

					        <div class='detail_picture' style='background-image: url(./upload/newton/".$result['stu_num'].".jpg)'></div>
								
					    </div>
					</div>
				";
				break;
			case 'thermal_conductivity':
				$result = $result->fetch_assoc();
				$out .= "
					<div class='popup-bg' id='popup-bg' style='display: block'>
		 				<div class='popup-detail' style='padding-top: 15px; height: 550px'>
						    <button class='detail-close' onclick='search_close_popup()''><span aria-hidden='true'>×</span></button>

					        <div class='stu_info'>
					            <span>姓名：<span id='stu_name'>".$result['stu_name']."</span></span>
							    <span>学号：<span id='stu_num'>".$result['stu_num']."</span></span>
					        </div>

							<div class='table_titile' style='margin-top: 20px'>一、铜盘在T2附近自然冷却时的温度示值</div>

	        				<table class='tb-content'>
	           
				            <tr>
				                <td colspan='1' width='150px'>稳态时的温度示值</td>
							    <td colspan='5' style='text-align: left; padding-left: 50px'>高温T1=<span id='T_1'>".$result['T_1']."</span></td>
							    <td colspan='5' style='text-align: left; padding-left: 50px'>低温T2= <span id='T_2'>".$result['T_2']."</span></td>
				            </tr>
			           
				            <tr>
				                <td>次序</td>
				                <td>1</td>
				                <td>2</td>
				                <td>3</td>
				                <td>4</td>
				                <td>5</td>
				                <td>6</td>
				                <td>7</td>
				                <td>8</td>
				                <td>9</td>
				                <td>10</td>
		           			</tr>

			           		<tr>
				                <td>时间t/s</td>
				                <td>".$result['t1']."</td>
				                <td>".$result['t2']."</td>
				                <td>".$result['t3']."</td>
				                <td>".$result['t4']."</td>
				                <td>".$result['t5']."</td>
				                <td>".$result['t6']."</td>
				                <td>".$result['t7']."</td>
				                <td>".$result['t8']."</td>
				                <td>".$result['t9']."</td>
				                <td>".$result['t10']."</td>
			           		</tr>
								
							<tr>
				                <td>温度示值T/C</td>
				                <td>".$result['te1']."</td>
				                <td>".$result['te2']."</td>
				                <td>".$result['te3']."</td>
				                <td>".$result['te4']."</td>
				                <td>".$result['te5']."</td>
				                <td>".$result['te6']."</td>
				                <td>".$result['te7']."</td>
				                <td>".$result['te8']."</td>
				                <td>".$result['te9']."</td>
				                <td>".$result['te10']."</td>
			           		</tr>

			      		</table>
			           		<p style='position: absolute; margin-left: 35px; margin-top: 10px; color: rgba(21, 20, 20, 0.67)'>温度变化率：<span id='change_rate'>".$result['change_rate']."</span> &nbsp;&#8451;/s</p>      


		 				<div class='table_titile' style='margin-top: 40px'>二、几何尺寸和质量的测量</div>

			      	    <table class='tb-content' style='width:600px;'>
				            <tr>
				                <td colspan='2'>次序</td>
				                <td width='50px'>1</td>
				                <td width='50px'>2</td>
				                <td width='50px'>3</td>
				                <td width='50px'>4</td>
				                <td width='50px'>5</td>
				                <td width='50px'>6</td>
				                <td width='50px'>平均</td>
				            </tr>
				            <tr>
				                <td rowspan='2'>样品盘B</td>
				                <td>厚度h<sub>B</sub>/cm</td>
				                <td>".$result['hb1']."</td>
				                <td>".$result['hb2']."</td>
				                <td>".$result['hb3']."</td>
				                <td>".$result['hb4']."</td>
				                <td>".$result['hb5']."</td>
				                <td>".$result['hb6']."</td>
				                <td>".$result['hb_ave']."</td>
				            </tr>

				            <tr>
				                <td>直径d<sub>B</sub>/cm</td>
				                <td colspan='7'>".$result['db']."</td>
				            </tr>

				             <tr>
				                <td rowspan='3'>散热铜盘C</td>
				                <td>厚度h<sub>C</sub>/cm</td>
				                <td>".$result['hc1']."</td>
				                <td>".$result['hc2']."</td>
				                <td>".$result['hc3']."</td>
				                <td>".$result['hc4']."</td>
				                <td>".$result['hc5']."</td>
				                <td>".$result['hc6']."</td>
				                <td>".$result['hc_ave']."</td>
				            </tr>

				             <tr>
				                <td>直径d<sub>C</sub>/cm</td>
				                <td colspan='7'>".$result['dc']."</td>
				            </tr>

				             <tr>
				                <td>质量m/g</td>
				                <td colspan='7'>".$result['m']."</td>
				            </tr>
							
				        </table>

				    </div>
				</div>
				";
			default: 
				break;
 		}
 		return $out;
 	}


 	function show_detail_via_date($exp_time,$exp_name){
 		$obj = new database($exp_name);
 		$result = $obj->show_detail_via_date($exp_time);
 		$out = "";
 		//var_dump($result);

 		switch($exp_name){
 			case 'oscillograph':
 				$exp_name_ch = '示波器与李萨如图形';
 				break;
 			case 'potentioneter':
 				$exp_name_ch = '电位差计';
 				break;
 			case 'moment_inertia':
 				$exp_name_ch = '用气垫摆测量转动惯量';
 				break;
 			case 'spectrometer':
 				$exp_name_ch = '分光计的使用和光栅衍射';
 				break;
 			case 'newton':
 				$exp_name_ch = '光的干涉--牛顿环';
 				break;
 			case 'thermal_conductivity':
 				$exp_name_ch = '稳态法测量物体的导热系数';
 				break;
 		}

 		$out .= "
					<div class='search-popup-bg' id='search-popup-bg' style='display: block; '>
						 <div class='search-detail-top'></div>
						 <div class='search-popup-detail'>
							 <a class='search-detail-close' onclick='close_popup_result()'><span aria-hidden='true' style='margin-right: 125px'><i class='fa fa-reply' style='margin-right: 20px'></i>返回</span></a>

							 <div class='detail_head_info'>
								<span>时间：<span id='detail_time'></span></span>
								<span id='detail_exp_name' name='".$exp_name."'>实验名称：".$exp_name_ch."</span>
							 </div>
							
							<table class='search_detail'>
								<thead>
								<tr> 
									<th width='150px'>学号</th>
									<th width='150px'>姓名</th>
									<th width='100px'>分数</th>
									<th width='100px'>求助次数</th>
									<th width='100px'>失败次数</th>
									<th width='120px'>原始数据</th>
								</tr>
								</thead>";
				$out .= "<tbody>";
	 			while( $data = $result->fetch_assoc()){
	 				//echo ($data['stu_num'].' '.$exp_time.' '.$exp_name);
	 				$data = $obj->return_from_exp_data($data['stu_num'],$exp_time);
	 				$data = $data->fetch_assoc();
	 			
	 				if($data == null) continue;		//这句话很重要
	 				
	 				$out .= "
	 					<tr>
		 					<td>".$data['stu_num']."</td>
							<td>".$data['stu_name']."</td>
							<td>".$data['grade']."</td>
							<td>".$data['help_times']."</td>
							<td>".$data['fail_times']."</td>
							<td><buttons class='button-detail' onclick='data_detail(this)'>详情</button></td>
		                </tr>";
	 			}
				   $out .= " 
				   	   </tbody> 
				       </table>
				    </div>
				</div>";

				if($exp_name=='oscillograph'){
					$out .="
					<div class='popup-bg' id='popup-bg'>
			 			<div class='popup-detail'>
				 			<button class='detail-close' onclick='close_popup()'><span aria-hidden='true'>×</span></button>

					        <div class='stu_info'>
					            <span>姓名：<span id='stu_info_stu_name'></span></span>
					            <span>学号：<span id='stu_info_stu_num'></span></span>
					        </div>

							<div class='table_titile'>一、测量正弦波</div>

					        <table class='tb-content'>
					            <thead>
						            <tr>
						                <th width='10%'>Vp-p标(V)</th>
						                <th>f标(Hz)</th>
						                <th>V/DIV<br>(V)</th>
						                <th>Dy</th>
						                <th width='13%'>V'p-p<br>(V)</th>
						                <th>Vp-p误差</th>
						                <th>TIME<br>/DIV</th>
						                <th>n</th>
						                <th>Dx</th>
						                <th>T'(ms)</th>
						                <th>f'(Hz)</th>
						                <th width='10%'>f误差</th>
						            </tr>
					            </thead>
					            <tbody>
						            <tr>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
					            </tr>
					            </tbody>
					        </table>

				 			<div class='table_titile'>二、李萨如图形</div>

					        <table class='tb-content' style='width:350px'>
					            <tr>
					                <th width='100px'>Nx</th>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					            </tr>
					            <tr>
					                <th>Ny</th>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					            </tr>
					            <tr>
					                <th>fy（hz）</th>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					            </tr>
					        </table>
						</div>
					</div>
					";
				}else if( $exp_name=='potentioneter'){
					$out .= "
						<div class='popup-bg' id='popup-bg'>
							 <div class='popup-detail'>
						 	<button class='detail-close' onclick='close_popup()'><span aria-hidden='true'>×</span></button>

					        <div class='stu_info'>
					            <span>姓名：<span id='stu_info_stu_name'></span></span>
					            <span>学号：<span id='stu_info_stu_num'></span></span>
					        </div>

							<div class='table_titile'>一、定标</div>

					        <table class='tb-content'>
					            <thead>
						            <tr>
						                <th>U'<sub>AB</sub>(V)</th>
						                <th>U'<sub>0</sub>(V/m<sup>-1</sup>)</th>
						                <th>l's(m)</th>
						                <th>R<sub>AB</sub>(&Omega;)</th>
						                <th>ls(m)</th>
						                <th>U<sub>0</sub>(V/m<sup>-1</sup>)</th>
						                <th>U<sub>AB</sub>(V)</th>
						                <th>电源电压E</th>
						            </tr>
					            </thead>
					            <tbody>
						            <tr>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
						                <td></td>
					           		</tr>
					            </tbody>
					        </table>

							<div class='table_titile'>二、测量电动势</div>

					        <table class='tb-content' style='width: 425px; height: 70px; margin-left: 35px'>
					             <tr>
						                <th>次数n</th>
						                <th>1</th>
						                <th>2</th>
						                <th>3</th>
						                <th>4</th>
						                <th>5</th>
						                <th>6</th>
						                <th width='23%'>平均值lx(m)</th>
						        </tr>
					      
					           
					            <tr>
					                <th width='20%'>lx(m)</th>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					            </tr>

					        </table>
						
					        <div class='potentioneter-stu-result' style='margin-top: -76px'>
					        	<p>待测电动势理论值: <span id='Exs'></span>V</p>
					        	<p>测量结果: E<sub>x</sub>=<span id='measure_E'></span>V</p>
					        	<p>相对误差: E=<span id='error_E'></span></p>
					        </div>

					    </div>
					</div>
					";
				}else if( $exp_name=='thermal_conductivity'){
					$out .= "
					<div class='popup-bg' id='popup-bg'>
						<div class='popup-detail' style='padding-top: 15px; height: 550px'>
						<button class='detail-close' onclick='close_popup()'><span aria-hidden='true'>×</span></button>

				        <div class='stu_info'>
				            <span>姓名：<span id='stu_info_stu_name'></span></span>
				            <span>学号：<span id='stu_info_stu_num'></span></span>
				        </div>

						<div class='table_titile' style='margin-top: 20px'>一、铜盘在T2附近自然冷却时的温度示值</div>

				        <table class='tb-content'>
				           
					            <tr>
					                <td colspan='1' width='150px'>稳态时的温度示值</td>
								    <td colspan='5' style='text-align: left; padding-left: 50px'>高温T1=<span id='T_1'></span></td>
								    <td colspan='5' style='text-align: left; padding-left: 50px'>低温T2= <span id='T_2'></span></td>
					            </tr>
				           
					            <tr>
					                <td>次序</td>
					                <td>1</td>
					                <td>2</td>
					                <td>3</td>
					                <td>4</td>
					                <td>5</td>
					                <td>6</td>
					                <td>7</td>
					                <td>8</td>
					                <td>9</td>
					                <td>10</td>
				           		</tr>

				           		<tr>
					                <td>时间t/s</td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
				           		</tr>
									
								<tr>
					                <td>温度示值T/C</td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
					                <td></td>
				           		</tr>

				        </table>
				           		<p style='position: absolute; margin-left: 35px; margin-top: 10px; color: rgba(21, 20, 20, 0.67)'>温度变化率：<span id='change_rate'></span> &nbsp;&#8451;/s</p>      

			 			<div class='table_titile' style='margin-top: 40px'>二、几何尺寸和质量的测量</div>

				        <table class='tb-content' style='width:600px;'>
				            <tr>
				                <td colspan='2'>次序</td>
				                <td width='50px'>1</td>
				                <td width='50px'>2</td>
				                <td width='50px'>3</td>
				                <td width='50px'>4</td>
				                <td width='50px'>5</td>
				                <td width='50px'>6</td>
				                <td width='50px'>平均</td>
				            </tr>
				            <tr>
				                <td rowspan='2'>样品盘B</td>
				                <td>厚度h<sub>B</sub>/cm</td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				            </tr>

				            <tr>
				                <td>直径d<sub>B</sub>/cm</td>
				                <td colspan='7'></td>
				            </tr>

				             <tr>
				                <td rowspan='3'>散热铜盘C</td>
				                <td>厚度h<sub>C</sub>/cm</td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				                <td></td>
				            </tr>

				             <tr>
				                <td>直径d<sub>C</sub>/cm</td>
				                <td colspan='7'></td>
				            </tr>

				             <tr>
				                <td>质量m/g</td>
				                <td colspan='7'></td>
				            </tr>
							
				        </table>

				    </div>
				</div>
	
					";
				}else if( $exp_name=='newton'){
					$out .= "
						<div class='popup-bg' id='popup-bg' >
							<div class='popup-detail' style='height: 550px; width: 1100px'>
								<button class='detail-close' onclick='close_popup()'><span aria-hidden='true'>×</span></button>
								<div class='detail_content'>
							        <div class='stu_info' style='margin-top: 20px; margin-left: 30px'>
							            <span>姓名：<span id='stu_info_stu_name'></span></span>
							            <span>学号：<span id='stu_info_stu_num'></span></span>
							        </div>

									<div class='table_titile' style='margin-top: 20px'>一、测量曲率半径</div>

							        <table class='tb-content' >
							           
							           
								            <tr>
								                <td rowspan='2'>环序/m</td>
								                <td colspan='2'>显微镜读数/mm</td>
								                <td rowspan='2'>环的直径d<sub>m</sub>/mm</td>
								                <td rowspan='2'>(d<sub>m</sub>)<sup>2</sup>/mm<sup>2</sup></td>
							            	</tr>

							            	<tr>
							            		<td>左方</td>
							            		<td>右方</td>
							            	</tr>

							            	<tr>
							            		<td>6</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>7</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>8</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>9</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>10</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>11</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>12</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>13</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>14</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>

							            	<tr>
							            		<td>15</td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            		<td></td>
							            	</tr>
							            	
							        </table>

							        <div class='newton_result'>
							        	<span>逐差法计算结果： R = <span id='R_commit'></span></span>
							        	<span>理论值 R'= <span id='R_set'></span></span>
							        	<span style='margin-right: 0'>相对误差 E = <span id='E_R'></span></span>    
							        		
							       	</div>
							    </div>

						        <div class='detail_picture'></div>
									
						    </div>
						</div>

					";
				}else if( $exp_name=='moment_inertia'){
					$out .= "
						<div class='popup-bg' id='popup-bg' >
							<div class='popup-detail' style='height: 670px; width: 770px'>
							<button class='detail-close' onclick='close_popup()'' style='margin-top: -25px'><span aria-hidden='true'>×</span></button>

						        <div class='stu_info' style='margin-top: 0px'>
						            <span>姓名：<span id='stu_info_stu_name'></span></span>
						            <span>学号：<span id='stu_info_stu_num'></span></span>
						        </div>

								<div class='table_titile' style='margin-top: 10px'>数据表一、</div>

						        <table class='tb-content' >

							            <tr>
							                <td>物体</td>
							                <td width='100px'>摆轮</td>
							                <td>摆轮+圆环</td>
							                <td width='120px'>摆轮+飞机模型</td>
							                <td width='120px'>摆轮+两圆柱</td>
						            	</tr>

						            	 <tr>
							                <td style='position: relative;'>
							                	<div class='moment_inertia_time'>时间t</div>
							                <!-- 	<div class='moment_inertia_slash'></div> -->
							                 	<canvas id='moment_inertia_slash' width='120px' height='47px'></canvas>
							                	<div class='moment_inertia_order'>次数</div>
							                </td>

							                <script type='text/javascript'>
							                	//绘制斜线
							                	var canvas = document.getElementById('moment_inertia_slash');
					        					var context = canvas.getContext('2d');//二维
							                	context.lineWidth = 0.2; // 线条宽度
					       						context.strokeStyle = 'rgb(0, 0, 0)'; // 线条颜色
					        					context.lineCap = 'round'; // 线头形状
					       						context.moveTo(0, 0); // 起点
					       						context.lineTo(120, 47); // 终点
										        // 绘制
										        context.stroke();
							                </script>

							                <td>t<sub>0</sub></td>
							                <td>t<sub>1</sub></td>
							                <td>t<sub>2</sub></td>
							                <td>t<sub>3</sub></td>
						            	</tr>

						            	<tr>
						            		<td>1</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						            	<tr>
						            		<td>2</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						            	<tr>
						            		<td>3</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						            	<tr>
						            		<td>4</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						            	<tr>
						            		<td>5</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						            	<tr>
						            		<td>6</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						            	<tr>
						            		<td>t（平均）</td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            		<td></td>
						            	</tr>

						        </table>

						        <div class='table_titile' style='margin-top: 20px'>数据表二、</div>

						        <table class='tb-content'>
						        	<tr>
										<td colspan='2' width='200px'>次序</td>
										<td width='60px'>1</td>
										<td width='60px'>2</td>
										<td width='60px'>3</td>
										<td width='60px'>4</td>
										<td width='60px'>5</td>
										<td width='60px'>6</td>
										<td width='60px'>平均</td>
						        	</tr>

						        	<tr>
						        		<td rowspan='2'>圆柱</td>
						        		<td>直径d/cm</td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        	</tr>

						        	<tr>
						        		<td>距离x/cm</td>
						        		<td colspan='4'></td>
						        		<td colspan='4'></td>
						        	</tr>


						        	<tr>
						        		<td rowspan='2'>圆环</td>
						        		<td>d<sub>内</sub>/cm</td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        	</tr>

						        	<tr>
						        		<td>d<sub>外</sub>/cm</td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        		<td></td>
						        	</tr>

						        	<tr>
						        		<td rowspan='2'>质量</td>
						        		<td>圆环m1/g</td>
						        		<td colspan='7'></td>
						        	</tr>

						        	<tr>
						        		<td>圆环m1/g</td>
						        		<td colspan='7'></td>
						        	</tr>



						        </table>

						    </div>
						</div>

					";
				}else if( $exp_name=='spectrometer'){
					$out .= "
						<div class='popup-bg' id='popup-bg' >
							<div class='popup-detail' style='height: 550px; width: 1100px'>
								<button class='detail-close' onclick='close_popup()''><span aria-hidden='true'>×</span></button>
								<div class='detail_content'>
							        <div class='stu_info' style='margin-top: 20px; margin-left: 30px'>
							            <span>姓名：<span id='stu_info_stu_name'></span></span>
							            <span>学号：<span id='stu_info_stu_num'></span></span>
							        </div>

									<div class='table_titile' style='margin-top: 40px'>一、光栅衍射</div>

							        <table class='tb-content' >
							           	<tr>
							           		<td rowspan='2'></td>
							           		<td colspan='2'>k = 1</td>
							           		<td colspan='2'>k = -1</td>
							           		<td>衍射角</td>
							           	</tr>

										<tr>
											<td>&#934;<sub>1</sub></td>
											<td>&#934;<sub>2</sub></td>
											<td>&#934;<sub>10</sub></td>
											<td>&#934;<sub>20</sub></td>
											<td>&#966;</td>
										</tr>	

										<tr>
											<td>绿光</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										<tr>

										<tr>
											<td>黄光(内)</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>

										<tr>
											<td>黄光(外)</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>	          
							        </table>

							        <p class='spectrometer_p'>
							        	<span>d = <span id='d'></span>nm</span>
							        	<span>d理论值 = <span id='d_theoretical'></span>nm</span>
							        	<span style='margin-right: 0'>相对误差 E<sub>d</sub> = <span id='E_d'></span></span> 
							        </p>

							       	<p class='spectrometer_p'>   
							        	<span>&#955;<sub>黄内</sub> = <span id='lambda_yellow_inside'></span>nm</span>
							        	<span>E<sub>黄内</sub> = <span id='E_lambda_yellow_inside'></span></span>
							        </p>

							        <p class='spectrometer_p'>   
							        	<span>&#955;<sub>黄外</sub> = <span id='lambda_yellow_outside'></span>nm</span>
							        	<span>E<sub>黄外</sub> = <span id='E_lambda_yellow_outside'></span></span>
							        </p>
							        
							       	<p class='spectrometer_p'>
							        	<span>色速率 D = <span id='D'></span></span>
							        	<span>D理论值 = <span id='D_theoretical'></span></span>
							       	</p>

							    </div>

						        <div class='detail_picture_spectrometer'></div>
									
						    </div>
						</div>

					";
				}
  		return $out;
 	}

 	function user_manage(){
 		$obj = new database();
 		$result = $obj->user_manage();
 		$out = "";
 		while( $data = $result->fetch_assoc()){
 			if($data['level']=='2') $level = '普通用户';
 			else if($data['level']=='1') $level = '管理员';
	 		$out .= "
				<tr>
					<td>".$data['number']."</td>
					<td id='user_name'>".$data['name']."</td>
					<td>".$data['pwd']."</td>
					<td>".$level."</td>";
					
			if($data['level']=='1'){
				 $out .= "
			    	<td>
						<a href='javascript:void(0)'  onclick='add_user()' style='margin-left: 56px'>添加用户</a>				
					</td>";
			}else if($data['level']=='2'){
			    $out .= "
			    	<td>
						<a href='javascript:void(0)'  onclick='admin_delete_user(this)'>删除</a>
						&nbsp;&nbsp;&nbsp;
						<a href='javascript:void(0)' onclick='admin_change_pwd(this)'>修改密码</a>
					</td>";
			}
			$out .= "</tr>";
		}
		return $out;
 	}

 	function admin_change_pwd($new_pwd, $number){
 		$obj = new database();
 		$result = $obj->admin_change_pwd($new_pwd, $number);
 		return $result;
 	}

 	function admin_delete_user($number){
 		$obj = new database();
 		$result = $obj->admin_delete_user($number);
 		return $result;
 	}

 	function add_user($number, $user_name, $pwd){
 		$obj = new database();
 		$result = $obj->add_user($number, $user_name, $pwd);
 		return $result;
 	}

 	function remark_submit($exp_name, $group_num, $remark, $grade_modified){
 		$obj = new database($exp_name);
 		$result = $obj->remark_submit($group_num, $remark, $grade_modified);
 		return $result;
 	}

 	function set_parameter($exp_name){
 		$obj = new database($exp_name);
 		$result = $obj->set_parameter($exp_name);
 		$out = "";
 		switch($exp_name){
 			case 'oscillograph':
		 		while( $data = $result->fetch_assoc()){
		 			$out .= "
		 				<tr>
		 					<td>".$data['group_num']."</td>
		 					<td>".$data['v_std']."</td>
		 					<td>".$data['f_std']."</td>
		 					<td><button onclick='change_parameter(this)' class='button-detail'>修改</button></td>
		 				</tr>";
		 		}
		 		return $out;
		 		break;
		 	case 'potentioneter':
		 		while( $data = $result->fetch_assoc()){
		 			$out .= "
		 				<tr>
		 					<td>".$data['group_num']."</td>
		 					<td>".$data['Exs']."</td>
		 					<td><button onclick='change_parameter(this)' class='button-detail'>修改</button></td>
		 				</tr>";
		 		}
		 		return $out;
		 		break;
		 	case 'newton':
		 		if(($result = has_para_newton()) != null){
		 			for( $i = 1; $i<=40; $i++){
		 				$data = $result->fetch_assoc();
		 				$out .= "
							<tr>
								<td>".$i."</td>
								<td style='position: relative'><input type='text' class='newton-input' value='".$data['radius']."'></td>
							</tr>
			 			";
	 				}
		 		}else{
		 			for( $i = 1; $i<=40; $i++){
			 			$out .= "
							<tr>
								<td>".$i."</td>
								<td style='position: relative'><input type='text' placeholder='点击设置参数' class='newton-input'></td>
							</tr>
			 			";
		 			}
		 		}
		 		return $out;
		 		break;
		 	case 'spectrometer':
		 		if(($result = has_para_spectrometer()) != null){
		 			for( $i = 1; $i<=40; $i++){
		 				$data = $result->fetch_assoc();
		 				$out .= "
							<tr>
								<td>".$i."</td>
								<td style='position: relative'><input type='text' class='newton-input' value='".$data['constant']."'></td>
							</tr>
			 			";
	 				}
		 		}else{
		 			for( $i = 1; $i<=40; $i++){
			 			$out .= "
							<tr>
								<td>".$i."</td>
								<td style='position: relative'><input type='text' placeholder='点击设置参数' class='newton-input'></td>
							</tr>
			 			";
		 			}
		 		}
		 		return $out;
		 		break;
	 	}
 	}

 	function has_para_newton(){
 		$obj = new database();
 		return $obj->has_para_newton();
 	}

 	function has_para_spectrometer(){
 		$obj = new database();
 		return $obj->has_para_spectrometer();
 	}

 	function query_parameter($exp_name){
 		$obj = new database($exp_name);
 		$out = "";
	 	switch($exp_name){
	 		case 'oscillograph':
 				$result = $obj->query_parameter_oscillograph($exp_name);
		 		while( $data = $result->fetch_assoc()){
		 			$out .= "
		 				<tr>
		 					<td>".$data['group_num']."</td>
		 					<td>".$data['v_std']."</td>
		 					<td>".$data['f_std']."</td>
		 					<td><button onclick='change_parameter(this)' class='button-detail'>修改</button></td>
		 				</tr>";
		 		}
		 		break;
		 	case 'potentioneter':
		 		$result = $obj->query_parameter_potentioneter($exp_name);
		 		while( $data = $result->fetch_assoc()){
		 			$out .= "
		 				<tr>
		 					<td>".$data['group_num']."</td>
		 					<td>".$data['Exs']."</td>
		 					<td><button onclick='change_parameter(this)' class='button-detail'>修改</button></td>
		 				</tr>";
		 		}
		 		break;
		 	default: break;

	 	}
	 	return $out;
 	}

 	function change_parameter_oscillograph($group_num, $v_std, $f_std){
 		$obj = new database();
 		$result = $obj->change_parameter_oscillograph($group_num, $v_std, $f_std);
 		return $result;
 	}

 	function change_parameter_potentioneter($group_num, $E_std){
 		$obj = new database();
 		$result = $obj->change_parameter_potentioneter($group_num, $E_std);
 		return $result;
 	}

 	function modified_course_status($exp_name, $user_id){
 		$obj = new database();
 		return  ($obj->modified_course_status($exp_name, $user_id));
 	}

 	function modified_course_status_newton($user_id, $para){
 		$obj = new database();
 		return  $obj->modified_course_status_newton($user_id, $para);
 	}

 	function modified_course_status_spectrometer($user_id, $para){
 		$obj = new database();
 		return  $obj->modified_course_status_spectrometer($user_id, $para);
 	}

 	function sepctrometer_lambda(){
 		$out = "";
 		if( ($result = has_para_lambda()) != null){
 			$result = $result->fetch_assoc();
 			$out .= "
 				<div class='sepctrometer_lambda'>
					<span>&#955;<sub>绿</sub> = <input id='lambda_1' value='".$result['lambda_1']."'>nm</span>
					<span>&#955;<sub>黄内</sub> = <input id='lambda_2' value='".$result['lambda_2']."'>nm</span>
					<span>&#955;<sub>黄外</sub> = <input id='lambda_3' value='".$result['lambda_3']."'>nm</span>
 				</div>
 			";
 		}else{
 			$out .= "
 				<div class='sepctrometer_lambda'>
					<span>&#955;<sub>绿</sub> = <input id='lambda_1' placeholder='点击输入'></span>
					<span>&#955;<sub>黄内</sub> = <input id='lambda_2' placeholder='点击输入'></span>
					<span>&#955;<sub>黄外</sub> = <input id='lambda_3' placeholder='点击输入'></span>
 				</div>
 			";
 		}
 		return $out;
 	}

 	function has_para_lambda(){
 		$obj = new database();
 		return  $obj->has_para_lambda();
 	}

 	function echo_course_status($num){
 		$obj = new database();
 		$result = $obj->echo_course_status($num);
 		if($result == null)
 			echo "<span style='color: #404f47'>未开课</span>";
 		else echo $result."正在上课";
 	}

 	function cur_user($course_id){
 		$obj = new database();
 		$result = $obj->echo_course_status($course_id);
 		if($result == null)
 			return 0;
 		else 
 			return $result;
 	}

 	function data_detail($exp_name,$stu_num){
 		$obj = new database();
 		$result = $obj->data_detail($exp_name,$stu_num);
 		return $result;
 	}

 	function end_when_time_out($exp_name){
 		$obj = new database();
 		$end_time = $obj->query_end_time($exp_name);

 		date_default_timezone_set('Asia/Shanghai');
 		$interval = 1;
 		do{
			$cur_time = getdate();
			$hours = $cur_time['hours'];
			$minutes = $cur_time['minutes'];
			
			$compare = $hours*60 + $minutes;				
			if( $compare > $end_time ){	
				end_when_time_out($exp_name);
			}
			sleep($interval);
		}while(true);
 	}



?>



