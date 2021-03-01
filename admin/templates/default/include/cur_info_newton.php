<?php
	if(!defined('templates')) exit();
	//防止恶意调用

?>

<script type="text/javascript" src="./templates/default/js/function.js"></script>




<div class="container" name="newton">
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
	                <th>测量曲率半径</th>
	                <th>求助</th>
	                <th>未通过</th>
	                <th width="120px">成绩</th>
	                <th width="100px">原始数据</th>
	            </tr>
	        </thead>
	        <tbody>
	   			<?php init_info_newton();?>
	        </tbody>
	    </table>
    </div>
</div>

<div class="body1">
	<div class="body-bg-1"></div>
</div><!--end body1-->

<div class="popup-bg" id="popup-bg" >
		 <div class="popup-detail" style="height: 550px; width: 1100px">
		 <button class="detail-close" onclick="close_popup()"><span aria-hidden="true">×</span></button>
			<div class="detail_content">
		        <div class="stu_info" style="margin-top: 20px; margin-left: 30px">
		            <span>组号：<span id="group_num"></span></span>
		            <span>姓名：<span id="stu_name"></span></span>
		            <span>学号：<span id="stu_num"></span></span>
		        </div>

				<div class="table_titile" style="margin-top: 20px">一、测量曲率半径</div>

		        <table class="tb-content" >
		           
		           
			            <tr>
			                <td rowspan="2">环序/m</td>
			                <td colspan="2">显微镜读数/mm</td>
			                <td rowspan="2">环的直径d<sub>m</sub>/mm</td>
			                <td rowspan="2">(d<sub>m</sub>)<sup>2</sup>/mm<sup>2</sup></td>
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

		        <div class="newton_result">
		        	<span>逐差法计算结果： R = <span id="R_commit"></span></span>
		        	<span>理论值 R'= <span id="R_set"></span></span>
		        	<span style="margin-right: 0">相对误差 E = <span id="E_R"></span></span>    
		        		
		       	</div>

		        <div class="popup_button" style="margin-top: 20px; float: right; margin-right: -135px">
		            <button type="button" class="button_1" onclick="pass(1,2)">通过</button>
		            <button type="button" class="button_1" onclick="fail(1,2)" style="margin-right: 35px">不通过</button>
		        </div>
		    </div>

	        <div class="detail_picture"></div>
				
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
