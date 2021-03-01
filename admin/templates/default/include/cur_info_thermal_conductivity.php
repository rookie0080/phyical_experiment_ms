<?php
	if(!defined('templates')) exit();
	//防止恶意调用

?>

<script type="text/javascript" src="./templates/default/js/function.js"></script>




	<div class="container" name="thermal_conductivity">
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
		                <th>温度示值</th>
		                <th>尺寸和质量</th>
		                <th>求助</th>
		                <th>未通过</th>
		                <th width="120px">成绩</th>
		                <th width="100px">原始数据</th>
		            </tr>
		        </thead>
		        <tbody>
		   			<?php init_info_thermal_conductivity();?>
		        </tbody>
		    </table>
	    </div>
	</div>

	<div class="popup-bg" id="popup-bg">
		 <div class="popup-detail" style='padding-top: 15px; height: 550px'>
		 <button class="detail-close" onclick="close_popup()"><span aria-hidden="true">×</span></button>

	        <div class="stu_info">
	            <span>组号：<span id="group_num"></span></span>
	            <span>姓名：<span id="stu_name"></span></span>
	            <span>学号：<span id="stu_num"></span></span>
	        </div>

			<div class="table_titile" style="margin-top: 20px">一、铜盘在T2附近自然冷却时的温度示值</div>

	        <table class="tb-content">
	           
		            <tr>
		                <td colspan="1" width="150px">稳态时的温度示值</td>
					    <td colspan="5" style='text-align: left; padding-left: 50px'>高温T1=<span id="T_1"></span></td>
					    <td colspan="5" style='text-align: left; padding-left: 50px'>低温T2= <span id="T_2"></span></td>
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
	           		<p style="position: absolute; margin-left: 35px; margin-top: 10px; color: rgba(21, 20, 20, 0.67)">温度变化率：<span id="change_rate"></span> &nbsp;&#8451;/s</p>      

	        <div class="popup_button">
	            <button type="button" class="button_1" onclick="pass(1,2)">通过</button>
	            <button type="button" class="button_1" onclick="fail(1,2)" style="margin-right: 35px">不通过</button>
	        </div>

 			<div class="table_titile" style="margin-top: 0px">二、几何尺寸和质量的测量</div>

	        <table class="tb-content" style='width:600px;'>
	            <tr>
	                <td colspan="2">次序</td>
	                <td width="50px">1</td>
	                <td width="50px">2</td>
	                <td width="50px">3</td>
	                <td width="50px">4</td>
	                <td width="50px">5</td>
	                <td width="50px">6</td>
	                <td width="50px">平均</td>
	            </tr>
	            <tr>
	                <td rowspan="2">样品盘B</td>
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
	                <td colspan="7"></td>
	            </tr>

	             <tr>
	                <td rowspan="3">散热铜盘C</td>
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
	                <td colspan="7"></td>
	            </tr>

	             <tr>
	                <td>质量m/g</td>
	                <td colspan="7"></td>
	            </tr>
				
	        </table>
			 
			<div class="popup_button" style="margin-right: 27px">
	            <button type="button" class="button_2" onclick="pass(2,2)" >通过</button>
	            <button type="button" class="button_2" onclick="fail(2,2)">不通过</button>
	        </div>

	    </div>
	</div>
	
<div class="body1">
	<div class="body-bg-1"></div>
</div><!--end body1-->

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


<div class="body1">
	<div class="body-bg-1"></div>
</div><!--end body1-->


<script type="text/javascript">window.onload=infoAjax(2);</script>