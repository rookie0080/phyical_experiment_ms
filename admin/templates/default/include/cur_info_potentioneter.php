<?php
	if(!defined('templates')) exit();
	//防止恶意调用

?>

<script type="text/javascript" src="./templates/default/js/function.js"></script>

<div class="container" name="potentioneter">
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
	                <th width="90px">定标</th>
	                <th>测量电动势</th>
	                <th>求助</th>
	                <th>未通过</th>
	                <th width="120px">成绩</th>
	                <th width="100px">原始数据</th>
	            </tr>
	        </thead>
	        <tbody>
	   			<?php init_info_potentioneter();?>
	        </tbody>
	    </table>
    </div>
</div>

<!-- 实验数据详情弹窗-->
<div class="popup-bg" id="popup-bg">
	 <div class="popup-detail">
	 <button class="detail-close" onclick="close_popup()"><span aria-hidden="true">×</span></button>

        <div class="stu_info">
            <span>组号：<span id="group_num"></span></span>
            <span>姓名：<span id="stu_name"></span></span>
            <span>学号：<span id="stu_num"></span></span>
        </div>

		<div class="table_titile">一、定标</div>

        <table class="tb-content">
            <thead>
	            <tr>
	                <th>U'<sub>AB</sub>(V)</th>
	                <th>U'<sub>0</sub>(V/m<sup>-1</sup>)</th>
	                <th>l's(m)</th>
	                <th>R<sub>AB</sub>(&Omega;)</th>
	                <th>ls(m)</th>
	                <th>U<sub>0</sub>(V/m<sup>-1</sup>)</th>
	                <th>U<sub>AB</sub>(V)</th>
	                <th>E(V)</th>
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

        <div class="popup_button">
            <button type="button" class="button_1" onclick="pass(1,2)">通过</button>
            <button type="button" class="button_1" onclick="fail(1,2)" style="margin-right: 35px">不通过</button>
        </div>

		<div class="table_titile">二、测量电动势</div>

        <table class="tb-content" style='width: 425px; height: 70px; margin-left: 35px'>
             <tr>
	                <th>次数n</th>
	                <th>1</th>
	                <th>2</th>
	                <th>3</th>
	                <th>4</th>
	                <th>5</th>
	                <th>6</th>
	                <th width="23%">平均值lx(m)</th>
	        </tr>
      
           
            <tr>
                <th width="20%">lx(m)</th>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

        </table>
	
		 
		<div class="popup_button" style="text-align: left; padding-left: 35px">
            <button type="button" class="button_2" onclick="pass(2,2)" >通过</button>
            <button type="button" class="button_2" onclick="fail(2,2)" style="margin-right: 123px" >不通过</button>
        </div>

        <div class="potentioneter-stu-result">
        	<p>待测电动势理论值: <span id="Exs"></span>V</p>
        	<p>测量结果: E<sub>x</sub>=<span id='measure_E'></span>V</p>
        	<p>相对误差: E=<span id="error_E"></span></p>
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

<div class="body1">
	<div class="body-bg-1"></div>
</div><!--end body1-->


<script type="text/javascript">window.onload=infoAjax(2);</script>