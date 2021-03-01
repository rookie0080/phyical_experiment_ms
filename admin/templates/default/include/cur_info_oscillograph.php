<?php
	if(!defined('templates')) exit();
	//防止恶意调用

?>

<script type="text/javascript" src="./templates/default/js/function.js"></script>




	<div class="container" name="oscillograph">
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
		                <th width="40px">组号</th>
		                <th>学号</th>
		                <th>姓名</th>
		                <th width="100px">测量正弦波</th>
		                <th width="100px">李萨如图像</th>
		                <th width="65px">求助</th>
		                <th width="65px">未通过</th>
		                <th width="120px">成绩</th>
	                	<th width="100px">原始数据</th>
		            </tr>
		        </thead>
		        <tbody>
		   			<?php init_info_oscillograph();?>
		        </tbody>
		    </table>
	    </div>
	</div>

	<div class="popup-bg" id="popup-bg">
		 <div class="popup-detail">
		 <button class="detail-close" onclick="close_popup()"><span aria-hidden="true">×</span></button>

	        <div class="stu_info">
	            <span>组号：<span id="group_num"></span></span>
	            <span>姓名：<span id="stu_name"></span></span>
	            <span>学号：<span id="stu_num"></span></span>
	        </div>

			<div class="table_titile">一、测量正弦波</div>

	        <table class="tb-content">
	            <thead>
		            <tr>
		                <th width="10%">Vp-p标(V)</th>
		                <th>f标(Hz)</th>
		                <th>V/DIV<br>(V)</th>
		                <th>Dy</th>
		                <th width="13%">V'p-p<br>(V)</th>
		                <th>Vp-p误差</th>
		                <th>TIME<br>/DIV</th>
		                <th>n</th>
		                <th>Dx</th>
		                <th>T'(ms)</th>
		                <th>f'(Hz)</th>
		                <th width="10%">f误差</th>
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

	        <div class="popup_button">
	            <button type="button" class="button_1" onclick="pass(1,2)">通过</button>
	            <button type="button" class="button_1" onclick="fail(1,2)" style="margin-right: 35px">不通过</button>
	        </div>

 			<div class="table_titile">二、李萨如图形</div>

	        <table class="tb-content" style='width:350px'>
	            <tr>
	                <th width="100px">Nx</th>
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
			 
			<div class="popup_button">
	            <button type="button" class="button_2" onclick="pass(2,2)" >通过</button>
	            <button type="button" class="button_2" onclick="fail(2,2)" style="margin-right: 135px">不通过</button>
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


<script type="text/javascript">window.onload=infoAjax(2);</script>

 <?php //end_when_time_out('oscillograph'); ?>
