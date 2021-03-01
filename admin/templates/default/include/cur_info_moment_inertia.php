<?php

	if(!defined('templates')) exit();
	//防止恶意调用

?>

<script type="text/javascript" src="./templates/default/js/function.js"></script>




<div class="container" name="moment_inertia">
	<div class="left_item">
		<div class="widget evaluating">
			<?php  init_evaluating();?>
		</div>
		
		<div class="widget help">
			<?php init_help();?>
		</div>
	</div>
  
	<div class="info_table">
	    <table class="cur_data" id="cur_data">
	        <thead >
	            <tr>
	                <th>组号</th>
	                <th>学号</th>
	                <th>姓名</th>
	                <th>测量转动惯量</th>
	                <th>求助</th>
	                <th>未通过</th>
	                <th width="120px">成绩</th>
	                <th width="100px">原始数据</th>
	            </tr>
	        </thead>
	        <tbody>
	   			<?php init_info_moment_inertia();?>
	        </tbody>
	    </table>
    </div>
</div>

<div class="body1">
	<div class="body-bg-1"></div>
</div><!--end body1-->

<div class="popup-bg" id="popup-bg" >
		 <div class="popup-detail" style="height: 670px; width: 770px">
		 <button class="detail-close" onclick="close_popup()" style="margin-top: -25px"><span aria-hidden="true">×</span></button>

	        <div class="stu_info" style="margin-top: 0px">
	            <span>组号：<span id="group_num"></span></span>
	            <span>姓名：<span id="stu_name"></span></span>
	            <span>学号：<span id="stu_num"></span></span>
	        </div>

			<div class="table_titile" style="margin-top: 10px">数据表一、</div>

	        <table class="tb-content" >

		            <tr>
		                <td>物体</td>
		                <td width="100px">摆轮</td>
		                <td>摆轮+圆环</td>
		                <td width="120px">摆轮+飞机模型</td>
		                <td width="120px">摆轮+两圆柱</td>
	            	</tr>

	            	 <tr>
		                <td style='position: relative;'>
		                	<div class="moment_inertia_time">时间t</div>
		                <!-- 	<div class="moment_inertia_slash"></div> -->
		                 	<canvas id="moment_inertia_slash" width="120px" height="47px"></canvas>
		                	<div class="moment_inertia_order">次数</div>
		                </td>

		                <script type="text/javascript">
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

	        <div class="table_titile" style="margin-top: 20px">数据表二、</div>

	        <table class="tb-content">
	        	<tr>
					<td colspan="2" width="200px">次序</td>
					<td width="60px">1</td>
					<td width="60px">2</td>
					<td width="60px">3</td>
					<td width="60px">4</td>
					<td width="60px">5</td>
					<td width="60px">6</td>
					<td width="60px">平均</td>
	        	</tr>

	        	<tr>
	        		<td rowspan="2">圆柱</td>
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
	        		<td colspan="4"></td>
	        		<td colspan="4"></td>
	        	</tr>


	        	<tr>
	        		<td rowspan="2">圆环</td>
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
	        		<td rowspan="2">质量</td>
	        		<td>圆环m1/g</td>
	        		<td colspan="7"></td>
	        	</tr>

	        	<tr>
	        		<td>圆环m1/g</td>
	        		<td colspan="7"></td>
	        	</tr>



	        </table>


	        <div class="popup_button" style="margin-top: 15px">
	            <button type="button" class="button_1" onclick="pass(1,2)">通过</button>
	            <button type="button" class="button_1" onclick="fail(1,2)" style="margin-right: 35px">不通过</button>
	        </div>
	    </div>
	</div>

	<!-- 老师评分弹窗 -->
<div class="popup-bg-mark" id="popup-bg-mark">
	<div class="popup-detail-mark">
		<button class="detail-close" onclick="close_popup_mark()"><span aria-hidden="true">×</span></button>
		<p class="t">成绩评定</p>			
		<div>
			<p class="mark_stu_info">
				<span class="mark-title">组号</span><span class="mark-content" id="mark_group_num"></span>
				<span class="mark-title">姓名</span><span class="mark-content" id="mark_name"></span>
				<span class="mark-title">机评结果</span><span class="mark-content" id="grade_by_machine"></span>
			</p>

			<p style="margin-top: 45px"><span>备注</span></p>
			<textarea class="remark" rows="6" cols="42" placeholder="在这里备注..." id="remark"></textarea>
			<div style="margin-top: 15px; padding: 0">
				<p class="modify">
					<span class="class-icon"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
					<input type="text" class="class-input" placeholder="修改分数" id="grade_modified">
				</p>
			</div>

			<button type="submit" class="mark-submit" onclick="mark_modify()">提交</button>
		</div>

	</div>
</div> 


	<script type="text/javascript">window.onload=infoAjax(1);</script>