
<?php
	if (!defined('templates')) exit;
	//防止恶意调用
?>
	<script type="text/javascript" src="./templates/default/js/function.js"></script>



<div class="body">

	<div class="container index">

		<div class="course">
			<div class="index_card_container">
				<a href="javascript:void(0)" onclick="startCourse(this,1)" name="oscillograph" class="course_card">
					<div class="course_card_top color_red">
						<i class="fa fa-credit-card"></i>
						<span>示波器与李萨如图形</span>
					</div>
					<div class="course_card_content">
						<h3 class="course_card_name">
							实验内容：
						</h3>
						<p>1）学习使用示波器观察电信号波形和李萨如图形<br>
						   2）学习使用示波器测量电信号的幅度和频率的方法
						</p>
					</div>
					<div class="course_card_bottom">
						<!-- <div class="course_card_info">712人在学</div> -->
						<div class="course-card-price"><?php echo_course_status(1);?></div>
					</div>
				</a>
				<div class="course_card_img course_img_1 "></div>	
			</div>

			<div class="index_card_container">
				<a href="javascript:void(0)" onclick="startCourse(this,2)" name="potentioneter" class="course_card">
					<div class="course_card_top color_purple">
						<i class="fa fa-credit-card"></i>
						<span>电位差计的原理及应用</span>
					</div>
					<div class="course_card_content">
						<h3 class="course_card_name">
							实验内容：
						</h3>
						<p>1）了解电位差计的工作原理<br>
						   2）学习使用电位差计测量电源电动势
						</p>

						
					</div>
					<div class="course_card_bottom">
						<!-- <div class="course_card_info">712人在学</div> -->
						<div class="course-card-price"><?php echo_course_status(2);?></div>
					</div>
				</a>
				<div class="course_card_img course_img_2 "></div>	
			</div>


			<div class="index_card_container">
				<a href="javascript:void(0)" onclick="startCourse(this,3)" name="moment_inertia" class="course_card">
					<div class="course_card_top color_orange">
						<i class="fa fa-credit-card"></i>
						<span>用气垫摆测量转动惯量</span>
					</div>
					<div class="course_card_content">
						<h3 class="course_card_name">
							实验内容：
						</h3>
						<p> 1）掌握转动惯量的概念和测量原理<br>
							2）掌握游标卡尺的使用方法<br>
							3）验证转动惯量平行轴定理<br>
						</p>
					</div>
					<div class="course_card_bottom">
						<!-- <div class="course_card_info">712人在学</div> -->
						<div class="course-card-price"><?php echo_course_status(3);?></div>
					</div>
				</a>
				<div class="course_card_img course_img_3 "></div>	
			</div>

			<div class="index_card_container">
				<a href="javascript:void(0)" onclick="startCourse(this,4)" name="spectrometer" class="course_card">
					<div class="course_card_top color_blue">
						<i class="fa fa-credit-card"></i>
						<span>分光计的使用及光栅衍射</span>
					</div>
					<div class="course_card_content">
						<h3 class="course_card_name">
								实验内容：
						</h3>
						<p>1）学习分光计的结构和调节方法
						   2）利用分光计测量光栅常数，光波长和角色散率
						</p>
					</div>
					<div class="course_card_bottom">
						<!-- <div class="course_card_info">712人在学</div> -->
						<div class="course-card-price"><?php echo_course_status(4);?></div>
					</div>
				</a>
				<div class="course_card_img course_img_4 "></div>	
			</div>
			
			<div class="index_card_container">
				<a href="javascript:void(0)" onclick="startCourse(this,5)" name="newton" class="course_card">
					<div class="course_card_top color_red">
						<i class="fa fa-credit-card"></i>
						<span>光的干涉--牛顿环</span>
					</div>
					<div class="course_card_content">
						<h3 class="course_card_name">
								实验内容：
						</h3>
						<p>1）观察光的等厚干涉现象<br>
           				   2）学习最小二乘法，逐差法处理数据
						</p>
					</div>
					<div class="course_card_bottom">
						<!-- <div class="course_card_info">712人在学</div> -->
						<div class="course-card-price"><?php echo_course_status(5);?></div>
					</div>
				</a>
				<div class="course_card_img course_img_5 "></div>	
			</div>
			
			<div class="index_card_container">
				<a href="javascript:void(0)" onclick="startCourse(this,6)" name="thermal_conductivity" class="course_card">
					<div class="course_card_top color_purple">
						<i class="fa fa-credit-card"></i>
						<span>稳态法测量物体的导热系数</span>
					</div>
					<div class="course_card_content">
						<h3 class="course_card_name">
								实验内容：
						</h3>
						<p>1）了解热传导的物理过程<br>
						   2）用稳态法测定材料的导热系数
     					</p>
					</div>
					<div class="course_card_bottom">
						<!-- <div class="course_card_info">712人在学</div> -->
						<div class="course-card-price"><?php echo_course_status(6);?></div>
					</div>
				</a>
				<div class="course_card_img course_img_1 "></div>	
			</div>

		</div>

	</div><!--end container-->
	
	<div class="body-bg"></div>
<div><!--end tag 'body'-->

<div class="popup-bg" id="popup-bg">
	<div class="mask">
		<button class="mask-close" onclick="close_popup()"><span aria-hidden="true">×</span></button>
		<p class="t">将开启新的课堂</p>
		<div class="course_info">
			<p><span class="mask-title">实验名称</span><span class="mask-content" id="experiment_name"></span></p>
			<p><span class="mask-title">教&emsp;&emsp;师</span><span class="mask-content"><?php echo($_SESSION["user_name"]);?></span></p>
			<p><span class="mask-title">上课时间</span><span class="mask-content"><?php echo(ymd_date());?>&emsp;<?php echo(course_time());?></span></p>
			<form class="mask-form" action="" method="POST">
				<div class="class_number">
					<span class="class-icon"><i class="fa fa-drivers-license-o"></i></span>
					<input type="text" class="class-input" placeholder="班级（如有的话）" id="classNum" name="classNum">
				</div>
				<button type="submit" class="mask-submit" id="submit_flag" onclick="if_cur_course()">确认开始</button>
			</form>
		</div>
	</div>
</div>