/*js函数库*/


//要开启的课程的名称
function createXMLHttpRequest(){
	var obj;
	if(window.ActiveXObject){
		obj = new ActiveXObject("Microsoft.XMLHTTP");
	}else if(window.XMLHttpRequest){
		obj = new XMLHttpRequest();
	}
	return obj;
}

function startCourse(obj,course_id){
	if( !if_course_time()){
		alert("开课时间最早为上课前半小时！");
		return;
	}
	var current_user = document.getElementsByClassName("course-card-price")[course_id-1];

	if( current_user.innerHTML[0] != '<'){
		alert(current_user.innerHTML+"！请联系该老师");
		return;
	}

	var user_id = (document.getElementById("uid")).getAttribute("name");
	var experiment_name = document.getElementById("experiment_name");
	obj1 = (obj.getElementsByTagName("span"))[0];
	experiment_name.innerHTML = obj1.innerHTML; 
	experiment_name.setAttribute("name",obj.getAttribute("name"));
	document.getElementById("popup-bg").style.display = "block";

	experiment_name = obj.getAttribute("name");
	// console.log(experiment_name);
	var form_action_name = document.getElementsByClassName("mask-form");

	//如果有课程还在进行则修改action的url
	var obj = createXMLHttpRequest();
	obj.open("GET","./templates/default/infoAjax.php?action=if_cur_course&user_id="+user_id,true);
	obj.onreadystatechange = function(){
		if( obj.readyState == 4 && obj.status == 200){
			var doc = obj.responseText;
			doc = parseJson(doc);
			console.log(doc);
			if( doc['status']=='1'){
				form_action_name[1].setAttribute("action","./index.php");
				return;
			}else{
				document.getElementById("submit_flag").setAttribute("onclick","if_cur_course("+user_id+");");
				//alert('exp_name: '+experiment_name);
				form_action_name[1].setAttribute("action","./index.php?exp_name="+experiment_name);
			}
		}
	}
	obj.send();


}

function if_course_time(){
	return 1;
}

function cur_user(course_id){
	var xmlobj = createXMLHttpRequest();		
	xmlobj.open("GET","./templates/default/infoAjax.php?action=cur_user&course_id="+course_id,false);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			// alert('here');
			console.log(xmlobj.responseText);
			if( xmlobj.responseText == 0){
				return null;
			}else
				return xmlobj.responseText;
		}
	}
	xmlobj.send();	
}

function if_cur_course(course_id){
	//如果有课程还在进行则必须先关闭该课程
	var user_id = (document.getElementById("uid")).getAttribute("name");
	var experiment_name = (document.getElementById("experiment_name")).getAttribute("name");
	var form_action_name = document.getElementsByClassName("mask-form");
	form_action_name =  form_action_name[1].getAttribute("action");
	// alert('user_id: '+user_id+'\nexp_name: '+experiment_name);
	//console.log(form_action_name);
	if( form_action_name === './index.php'){ 
		alert( "您还有课程未结束！请先结束该课程");
	}else{

		//修改该课程的状态

		var class_num = (document.getElementById("classNum")).value;
		if(class_num == "") class_num = 'null';

		var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=start_course&user_id="+user_id+"&course_name="+experiment_name+"&classNum="+class_num,true);
		obj.send();
		
		form_action_name = document.getElementsByClassName("mask-form");
		form_action_name[1].setAttribute("action","./index.php?exp_name="+experiment_name+"&parameter_setting=true&action=set");
		// alert('exp_name: '+experiment_name+'\nclass_num: '+class_num);
		// alert('here');
	}
}

function modified_course_status(exp_name){
	//修改该课程的状态,为"实时课堂"按钮提供依据
	var user_id = (document.getElementById("uid")).getAttribute("name");

	// alert('here');
	
	var xmlobj = createXMLHttpRequest();		
	xmlobj.open("GET","./templates/default/infoAjax.php?action=modified_course_status&exp_name="+exp_name+"&user_id="+user_id,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			console.log(xmlobj.responseText);
			// alert(xmlobj.responseText);
			window.location.href= './index.php?exp_name='+exp_name;	
		}
	}
	xmlobj.send();
}

function modified_course_status_newton(){
 	//牛顿环实验必须全部手动输入仪器参数

	var user_id = (document.getElementById("uid")).getAttribute("name");  
	var tr = document.getElementsByTagName("tr");
	var para = [];
	for(var i = 1; i<=40; i++){
		data = tr[i].getElementsByTagName("input")[0].value;
		if( !data) {
			alert("请设置好全部参数！");
			break;
		}
	 	para.push(data);
	}

		para = JSON.stringify(para);

	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=modified_course_status_newton&user_id="+user_id+"&para="+para,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			if(xmlobj.responseText == 1){
				window.location.href= './index.php?exp_name=newton';		
			}
		}
	}
	xmlobj.send();	

	console.log(para);
}

function modified_course_status_spectrometer(){
	//分光计使用和光栅衍射实验必须全部手动输入仪器参数

	var user_id = (document.getElementById("uid")).getAttribute("name");  
	var tr = document.getElementsByTagName("tr");
	var para = [];

	var lambda_1 = document.getElementById("lambda_1").value;
	var lambda_2 = document.getElementById("lambda_2").value;
	var lambda_3 = document.getElementById("lambda_3").value;

	if(!lambda_1 || !lambda_2 || !lambda_3){
		alert("请设置好全部参数！");
		return;
	}else{
		para.push(lambda_1);
		para.push(lambda_2);
		para.push(lambda_3);
	}
	// alert('here');
	for(var i = 1; i<=40; i++){
		data = tr[i].getElementsByTagName("input")[0].value;
		if( !data) {
			alert("请设置好全部参数！");
			break;
		}
	 	para.push(data);
	}

		para = JSON.stringify(para);

	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=modified_course_status_spectrometer&user_id="+user_id+"&para="+para,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			if(xmlobj.responseText == 1){
				window.location.href= './index.php?exp_name=spectrometer';		
			}
		}
	}
	xmlobj.send();	

	console.log(para);
}

function close_course(){
	var experiment_name;
	var user_id = (document.getElementById("uid")).getAttribute("name");

	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=cur_course_name&user_id="+user_id,false);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			//alert('here');
			experiment_name = xmlobj.responseText;
			//alert("experiment_name:"+experiment_name);
		}
	}
	xmlobj.send();

	console.log(experiment_name);
	// alert('user_id: '+user_id+'\nexp_name: '+experiment_name);
	if(confirm('请确认已结束成绩评定工作！\n是否结束当前课程并保存实验数据？')){
		var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=close_course&user_id="+user_id+"&course_name="+experiment_name,true);
		obj.onreadystatechange = function(){
			//alert(obj.readyState);
			if( obj.readyState == 4 && obj.status == 200){
				var data = obj.responseText;
				console.log(data);
				if( data == 1){
					alert('提交成功');
					window.location.href = "./index.php";
				}
				else alert('提交失败');		
			}
		}
		obj.send();
	}
}

//关闭课程确认窗口
function close_popup(){
	document.getElementById("popup-bg").style.display="none";
}

function search_close_popup(){
	var node = document.getElementById("popup-bg");
	node.parentNode.removeChild(node);
}

function close_popup_adduser(){
	document.getElementById("popup-bg-adduser").style.display="none";
}

function close_popup_result(){
	var div_rm = (document.getElementById("search-popup-bg")).parentNode;
	var body = document.getElementsByTagName("body")[0];
	body.removeChild(div_rm);
}


//自定义删除所有子节点
function removeAllChild(node)
{
    while(node.hasChildNodes()) //当div下还存在子节点时 循环继续
    {
        node.removeChild(node.firstChild);
    }
}

//实时刷新后台数据
function infoAjax(status_count){

	var exp_name = (document.getElementsByClassName("container"))[0].getAttribute("name");
	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoXML.php?exp=true&exp_name="+exp_name+"&status_count="+status_count,true);

	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
	
			//处理新提交的条目
			var xml_doc = xmlobj.responseXML;
			var node_list = xml_doc.getElementsByTagName("student");
			var tr = document.getElementsByClassName("cur_data")[0];
			tr = tr.getElementsByTagName("tr");
		
			var tag = new Array();			//按照status的个数给数组进行赋值
			tag[0] = 'stunum';
			tag[1] = 'name';
			for( var i = 1; i <= status_count; i++){
				tag[i+1] = 'status'+i;
			}
			tag[i+1] = 'helptimes';
			tag[i+2] = 'failtimes';
			tag[i+3] = 'grade';
			var len_tag = i+2+1+1;
		//alert('here');
		//console.log(xml_doc);
		
				for( var i = 0; i<node_list.length; i++){
					var td = tr[i+1].getElementsByTagName("td");
					//console.log(td);
					if((node_list[i].getElementsByTagName("stunum")[0]).innerHTML != ""){
						//有学生在该组时才显示信息

						for( var j = 1; j<=len_tag; j++){
							td[j].innerHTML = node_list[i].getElementsByTagName(tag[j-1])[0].innerHTML;
							//为grade一栏添加标记
							if(tag[j-1]=='grade'){
								td[j].setAttribute("id","grade_by_machine_index");
							}
						}
					

						//添加"详情"和"评分"按钮
						if(!(td[len_tag+1].hasChildNodes())){
							var button_insert_1 = document.createElement("button");
							if(exp_name == 'newton' || exp_name == 'moment_inertia' || exp_name == 'spectrometer'){
								button_insert_1.setAttribute("onclick","show_detail_table(this,1)");
							}else{
								button_insert_1.setAttribute("onclick","show_detail_table(this,2)");
							}
								button_insert_1.setAttribute("class","button-detail");
								button_insert_1.innerHTML = "详情";
							td[len_tag+1].appendChild(button_insert_1);
						}

						var button_insert_2 = document.createElement("button");
							button_insert_2.setAttribute("onclick","mark(this)");
							button_insert_2.setAttribute("class","button-detail button-modified");
							button_insert_2.innerHTML = "修改";
						td[len_tag].appendChild(button_insert_2);
					}
				}
			

			//处理等待评测的组号
			var evaluating = xml_doc.getElementsByTagName("evaluating");
			var len = evaluating.length;
			var count = document.getElementsByClassName("count_evaluating");
			count[0].innerHTML = len;
			var li_operation = (document.getElementsByClassName("evaluating"))[0].getElementsByClassName("group_list")[0];
			removeAllChild(li_operation);		//删除所有子节点
			var i;
			for(i = 0; i < len; i++){
				var new_a = document.createElement("a");
					new_a.setAttribute("href","javascript:void(0)");
					new_a.innerHTML = evaluating[i].innerHTML;
				var new_li = document.createElement("li");
					new_li.setAttribute("title","详情");
					new_li.setAttribute("onclick","show_detail_widget(this,"+status_count+")");
					new_li.appendChild(new_a);
				var flag1 = (document.getElementsByClassName('group_list'))[0].getAttribute('name');	//从name属性获取标记的值
				//console.log(flag1);
				if(flag1==0 && i > 3){
					new_li.style.display = "none";
				}
				li_operation.appendChild(new_li);
			}

			if(i-1<=3){
				for(var j=i; j<=3; j++){
					var new_li = document.createElement("li");		//创建空的li
					li_operation.appendChild(new_li);
				}
			}

			var new_a = document.createElement("a");
			new_a.setAttribute("href","javascript:void(0)");
			var new_li = document.createElement("li");
			new_li.setAttribute("onclick","eval_spread()");
			if(flag1==1) {
				new_a.innerHTML = "<span class='option_symbol'> << </span>";
				new_li.setAttribute("title","收起");
				new_li.setAttribute("onclick","eval_fold_up()");
			}else{
				new_a.innerHTML = "<span class='option_symbol'> >> </span>";
				new_li.setAttribute("title","展开");
			}
			new_li.appendChild(new_a);
			li_operation.appendChild(new_li);

			//处理求助信息
			var help = xml_doc.getElementsByTagName("help");
			var len_help = help.length;
			var count_help = document.getElementsByClassName("count_help");
			count_help[0].innerHTML = len_help;
			var li_operation_help = document.getElementsByClassName("group_list")[1];
			removeAllChild(li_operation_help);	
			for(i = 0; i < len_help; i++){
			    new_a = document.createElement("a");
				new_a.setAttribute("href","javascript:void(0)");
				new_a.innerHTML = help[i].innerHTML;
				new_li = document.createElement("li");
				new_li.setAttribute("title","受理这条信息");
				new_li.setAttribute("onclick","solve_help(this,"+status_count+")");
				new_li.appendChild(new_a);
			var flag2 = (document.getElementsByClassName("group_list"))[1].getAttribute("name");
				if(flag2==0 && i > 3){
					new_li.style.display = "none";
				}
				li_operation_help.appendChild(new_li);
			}

			if(i-1 <=3){
				for(var j=i; j<=3; j++){
					var new_li = document.createElement("li");		//创建空的li
					li_operation_help.appendChild(new_li);
				}
			}

			var new_a = document.createElement("a");
			new_a.setAttribute("href","javascript:void(0)");
			var new_li = document.createElement("li");
			new_li.setAttribute("onclick","help_spread()");
			if(flag2==1) {
				new_a.innerHTML = "<span class='option_symbol'> << </span>";
				new_li.setAttribute("title","收起");
				new_li.setAttribute("onclick","help_fold_up()");
			}else{
				new_a.innerHTML = "<span class='option_symbol'> >> </span>";
				new_li.setAttribute("title","展开");
			}
			new_li.appendChild(new_a);
			li_operation_help.appendChild(new_li);

		}
	}

	setTimeout("infoAjax("+status_count+");",3000);
	xmlobj.send();
}



//展开列表
function eval_spread(){
	var count = document.getElementsByClassName("count_evaluating")[0].innerHTML;
	
	(document.getElementsByClassName("group_list"))[0].setAttribute("name","1");
	
	if(count<=4){
		var li = (document.getElementsByClassName("group_list"))[0].lastChild;
		li.setAttribute("onclick","eval_fold_up()");
		li.setAttribute('title','收起');
		var a = (li.getElementsByTagName("a"))[0];
		a.innerHTML = "<span class='option_symbol'> << </span>";	
		return;
	}

	var group_list = document.getElementsByClassName("group_list");
	li = group_list[0].getElementsByTagName("li");
	group_list[0].removeChild(li[count]);	//先删除末尾的“展开”符号节点
	if(count >= 5){
		for(var i = 4; i<count; i++){
			li[i].style.display = "block";
		}
	}
	var new_a = document.createElement("a");
	new_a.setAttribute("href","javascript:void(0)");
	var new_li = document.createElement("li");
	new_a.innerHTML = "<span class='option_symbol'> << </span>";
	new_li.setAttribute("title","收起");
	new_li.setAttribute("onclick","eval_fold_up()");
	new_li.appendChild(new_a);
	group_list[0].appendChild(new_li);
}

//收起列表
function eval_fold_up(){
	var count = document.getElementsByClassName("count_evaluating")[0].innerHTML;

	(document.getElementsByClassName("group_list"))[0].setAttribute("name","0");
	
	if(count<=4){
		var li = (document.getElementsByClassName("group_list"))[0].lastChild;
			li.setAttribute("onclick","eval_spread()");
		li.setAttribute('title','展开');
		var a = (li.getElementsByTagName("a"))[0];
		a.innerHTML = "<span class='option_symbol'> >> </span>";
		return;
	}

	var group_list = document.getElementsByClassName("group_list");
	li = group_list[0].getElementsByTagName("li");
	if(count >= 4){
		group_list[0].removeChild(li[count]);	//先删除末尾的“展开”符号节点
		for(var i = 4; i<count; i++){
			li[i].style.display = "none";
		}
	}
	var new_a = document.createElement("a");
	new_a.setAttribute("href","javascript:void(0)");
	var new_li = document.createElement("li");
	new_a.innerHTML = "<span class='option_symbol'> >> </span>";
	new_li.setAttribute("title","展开");
	new_li.setAttribute("onclick","eval_spread()");
	new_li.appendChild(new_a);
	group_list[0].appendChild(new_li);

}
 
function help_spread(){
	var count = document.getElementsByClassName("count_help")[0].innerHTML;
	var group_list = (document.getElementsByClassName("help")[0]).getElementsByClassName("group_list");
	li = group_list[0].getElementsByTagName("li");

	(document.getElementsByClassName("group_list"))[1].setAttribute("name","1");

	if(count<=4){
		var li = (document.getElementsByClassName("group_list"))[1].lastChild;
		li.setAttribute("onclick","help_fold_up()");
		li.setAttribute('title','收起');
		var a = (li.getElementsByTagName("a"))[0];
		a.innerHTML = "<span class='option_symbol'> << </span>";
		return;
	}

	group_list[0].removeChild(li[count]);	//先删除末尾的“展开”符号节点
	if(count >= 4){
		for(var i = 4; i<count; i++){
			li[i].style.display = "block";
		}
	}
	var new_a = document.createElement("a");
	new_a.setAttribute("href","javascript:void(0)");
	var new_li = document.createElement("li");
	new_a.innerHTML = "<span class='option_symbol'> << </span>";
	new_li.setAttribute("title","收起");
	new_li.setAttribute("onclick","help_fold_up()");
	new_li.appendChild(new_a);
	group_list[0].appendChild(new_li);
	
}


function help_fold_up(){
	var count = document.getElementsByClassName("count_help")[0].innerHTML;
	var group_list = document.getElementsByClassName("group_list");
	li = group_list[1].getElementsByTagName("li");

	(document.getElementsByClassName("group_list"))[1].setAttribute("name","0");

	if(count<=4){
		var li = (document.getElementsByClassName("group_list"))[1].lastChild;
		li.setAttribute("onclick","help_spread()");
		li.setAttribute('title','展开');
		var a = (li.getElementsByTagName("a"))[0];
		a.innerHTML = "<span class='option_symbol'> >> </span>";
		return;
	}

	group_list[0].removeChild(li[count]);	//先删除末尾的“展开”符号节点
	if(count >= 5){
		for(var i = 4; i<count; i++){
			li[i].style.display = "none";
		}
	}
	var new_a = document.createElement("a");
	new_a.setAttribute("href","javascript:void(0)");
	var new_li = document.createElement("li");
	new_a.innerHTML = "<span class='option_symbol'> >> </span>";
	new_li.setAttribute("title","展开");
	new_li.setAttribute("onclick","help_spread()");
	new_li.appendChild(new_a);
	group_list[0].appendChild(new_li);
}

//json解释器,适合接近w3c标准的浏览器
function parseJson(str){
 	return JSON.parse(str);
}

//显示学生的详细信息
function show_detail_table(obj,status_count){
	var li = obj.parentNode.parentNode.getElementsByTagName("td");
	var stu_num = li[1].innerHTML;
	if(stu_num == ""){
		alert("该组没有学生！");
		return;
	}else{
		show_detail_option(li,status_count);
	} 
}

function show_detail_widget(obj,status_count){

	var group_num = (obj.getElementsByTagName("a"))[0].innerHTML;
	var li = document.getElementById("cur_data");
	li = li.getElementsByTagName("tbody");
	li = li[0].getElementsByTagName("tr");
	li = li[group_num-1].getElementsByTagName("td");
	show_detail_option(li,status_count);
}

function show_detail_option(li,status_count){
	var exp_name = (document.getElementsByClassName("container")[0]).getAttribute("name");
	var popup_bg = document.getElementById("popup-bg");
	popup_bg.style.display = "block";
	(document.getElementById("group_num")).innerHTML = li[0].innerHTML;
	(document.getElementById("stu_name")).innerHTML = li[2].innerHTML;
	(document.getElementById("stu_num")).innerHTML = li[1].innerHTML;

	var group_num = (document.getElementById("group_num")).innerHTML;
	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=detail_data&group_num="+group_num+"&exp_name="+exp_name,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			var result = xmlobj.responseText;
			console.log(result);
			result = parseJson(result);

			for( var i = 1; i<=status_count; i++){
				//alert(result['status_'+i]);
				//console.log("button_"+i);
				var bt = document.getElementsByClassName("button_"+i);
				
				if( result['status_'+i] != 2){
					//console.log('不可用');
					bt[0].setAttribute("disabled","true");
					bt[0].style.background = "#fff";
					bt[1].setAttribute("disabled","true");
					bt[1].style.background = "#fff";
				}else{
					//console.log('可用');
					bt[0].removeAttribute("disabled");
					bt[0].style.background = "#f0f2f0";
					bt[0].style.cursor="pointer";
					bt[1].removeAttribute("disabled");
					bt[1].style.background = "#f0f2f0";
					bt[1].style.cursor="pointer";
				}
			}

			exp_name = (document.getElementsByClassName("container"))[0].getAttribute("name");
			console.log(exp_name);

			if(exp_name=='oscillograph'){
				show_detail_oscillograph(result);
			}else if( exp_name=='potentioneter'){
				show_detail_potentioneter(result);
			}else if( exp_name=='thermal_conductivity'){
				show_detail_thermal_conductivity(result);
			}else if( exp_name=='newton'){
				show_detail_newton(result);
			}else if( exp_name=='moment_inertia'){
				show_detail_moment_inertia(result);
			}else if( exp_name=='spectrometer'){
				show_detail_spectrometer(result);
			}

		}
	}
	xmlobj.send();

}

function show_detail_oscillograph(result){
	var tb = document.getElementsByClassName("tb-content");

	//将改变过的td颜恢复到初始状态
	td = tb[0].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67";
	}
	td = tb[1].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67";
	}
			
	td = tb[0].getElementsByTagName("td");
	td[0].innerHTML = result['v_std'];
	td[1].innerHTML = result['f_std'];
	td[2].innerHTML = result['V_DIV'];
	td[3].innerHTML = result['Dy'];
	td[4].innerHTML = result['v_up'];
	td[5].innerHTML = result['E_v'];
	td[6].innerHTML = result['TIME_DIV'];
	td[7].innerHTML = result['n'];
	td[8].innerHTML = result['Dx'];
	td[9].innerHTML = result['T'];
	td[10].innerHTML = result['f_up'];
	td[11].innerHTML = result['E_f'];

	//Dx和Dy应该保留一位小数
	var len = result['Dy'].length;
	if( result['Dy'][len-2] != '.') {
		td[3].style.color = "rgb(244, 88, 88)";
	}
	len = result['Dx'].length;
	if( result['Dx'][len-2] != '.') {
		td[8].style.color = "rgb(244, 88, 88)";
	}
	//误差大于10%标记为红色
	// console.log(parseFloat(result['E_v']));
	// console.log(parseFloat(result['E_f']));
	if( parseFloat(result['E_v'])>10){
		td[5].style.color = "rgb(244, 88, 88)";
	}
	if( parseFloat(result['E_f'])>10){
		td[11].style.color = "rgb(244, 88, 88)";
	}


	td = tb[1].getElementsByTagName("td");
	td[0].innerHTML = result['Nx1'];
	td[1].innerHTML = result['Nx2'];
	td[2].innerHTML = result['Nx3'];
	td[3].innerHTML = result['Nx4'];
	td[4].innerHTML = result['Ny1'];
	td[5].innerHTML = result['Ny2'];
	td[6].innerHTML = result['Ny3'];
	td[7].innerHTML = result['Ny4'];
	td[8].innerHTML = result['fy1'];
	td[9].innerHTML = result['fy2'];
	td[10].innerHTML = result['fy3'];
	td[11].innerHTML = result['fy4'];

	//满足fx/fy=Ny/Nx （fx固定为500）
	if( 500/result['fy1'] != result['Ny1']/result['Nx1']) {
		td[0].style.color = "rgb(244, 88, 88)";
		td[4].style.color = "rgb(244, 88, 88)";
		td[8].style.color = "rgb(244, 88, 88)";
	}

	if( 500/result['fy2'] != result['Ny2']/result['Nx2']) {
		td[1].style.color = "rgb(244, 88, 88)";
		td[5].style.color = "rgb(244, 88, 88)";
		td[9].style.color = "rgb(244, 88, 88)";
	}

	if( 500/result['fy3'] != result['Ny3']/result['Nx3']) {
		td[2].style.color = "rgb(244, 88, 88)";
		td[6].style.color = "rgb(244, 88, 88)";
		td[10].style.color = "rgb(244, 88, 88)";
	}

	if( 500/result['fy4'] != result['Ny4']/result['Nx4']) {
		td[3].style.color = "rgb(244, 88, 88)";
		td[7].style.color = "rgb(244, 88, 88)";
		td[11].style.color = "rgb(244, 88, 88)";
	}

}
function show_detail_potentioneter(result){
		var tb = document.getElementsByClassName("tb-content");
		//将改变过的td颜恢复到初始状态
		td = tb[0].getElementsByTagName("td");
		for(var i = 1; i<td.length; i++){
			td[i].style.color = "rgba(21, 20, 20, 0.67";
		}
		td = tb[1].getElementsByTagName("td");
		for(var i = 1; i<td.length; i++){
			td[i].style.color = "rgba(21, 20, 20, 0.67";
		}

		
		td = tb[0].getElementsByTagName("td");
		td[0].innerHTML = result['U_ab'];
		td[1].innerHTML = result['U_0'];
		td[2].innerHTML = result['I_s'];
		td[3].innerHTML = result['Rab'];
		td[4].innerHTML = result['Is'];
		td[5].innerHTML = result['U0'];
		td[6].innerHTML = result['Uab'];
		td[7].innerHTML = result['E'];

		//l's(m)，ls(m)，lx(m)三个长度物理量应该保留四个小数位数
		var len = result['I_s'].length;
		if(result['I_s'][len-5] != '.'){
			td[2].style.color = "rgb(244, 88, 88)";
		}
		len = result['Is'].length;
		if(result['Is'][len-5] != '.'){
			td[4].style.color = "rgb(244, 88, 88)";
		}

		//U0(V/m-1)和UAB(V)保留5位有效数字
		len = result['U0'].length;
		var count = 0;
		for( i = 0; i < len; i++){
			if( result['U0'][i] !='0' && result['U0'][i]!='.'){
				for( var j = i; j<len; j++){
					if(result['U0'][j] != '.') count++;
				}
				console.log(count)
				break;
			}
		}
		if(count!=5){
			td[5].style.color = "rgb(244, 88, 88)";
		}


		len = result['Uab'].length;
		count= 0;
		for( i = 0; i < len; i++){
			if( result['Uab'][i] !='0' && result['Uab'][i]!='.'){
				for( var j = i; j<len; j++){
					if(result['Uab'][j] != '.') count++;
				}
				console.log(count);
				break;
			}
		}
		if(count!=5){
			td[6].style.color = "rgb(244, 88, 88)";
		}



		td = tb[1].getElementsByTagName("td");
		td[0].innerHTML = result['Lx1'];
		td[1].innerHTML = result['Lx2'];
		td[2].innerHTML = result['Lx3'];
		td[3].innerHTML = result['Lx4'];
		td[4].innerHTML = result['Lx5'];
		td[5].innerHTML = result['Lx6'];
		td[6].innerHTML = result['Lx_ave'];
		for(var i = 0; i<td.length; i++){
			len = td[i].innerHTML.length;
			if(td[i].innerHTML[len-5] != '.'){
				td[i].style.color = "rgb(244, 88, 88)";
			}
		}

		document.getElementById("Exs").innerHTML = result['Exs'];
		document.getElementById("measure_E").innerHTML = result['Ex'];
		document.getElementById("error_E").innerHTML = result['E_e'];

		// var measure_E = document.getElementById("measure_E");
		// len = measure_E.innerHTML.length;
		// console.log(len);
		if(measure_E.innerHTML[len-5] != '.'){
			measure_E.style.color = "rgb(244, 88, 88)";
		}


		// var Exs = document.getElementById("Exs");
		// 	Exs = parseFloat(Exs.innerHTML);
		// 	Ex = parseFloat(measure_E.innerHTML);
		// var error_E = Math.abs((Ex-Exs)/Exs);
		// console.log(error_E);
		if(result['E_e'] > 0.1){
			document.getElementById("error_E").style.color = "rgb(244, 88, 88)";
		}
		// document.getElementById("error_E").innerHTML = (error_E*100).toFixed(2)+"%";
		
}

function show_detail_thermal_conductivity(result){
	var tb = document.getElementsByClassName("tb-content");
	document.getElementById("T_1").innerHTML = result['T_1'];
	document.getElementById("T_2").innerHTML = result['T_2'];
	document.getElementById("change_rate").innerHTML = result['change_rate'];

	//将改变过的td颜恢复到初始状态
	td = tb[0].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67)";
	}
	td = tb[1].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67";
	}

	td = tb[0].getElementsByTagName("td");
	td[15].innerHTML = result['t1'];
	td[16].innerHTML = result['t2'];
	td[17].innerHTML = result['t3'];
	td[18].innerHTML = result['t4'];
	td[19].innerHTML = result['t5'];
	td[20].innerHTML = result['t6'];
	td[21].innerHTML = result['t7'];
	td[22].innerHTML = result['t8'];
	td[23].innerHTML = result['t9'];
	td[24].innerHTML = result['t10'];

	td[26].innerHTML = result['te1'];
	td[27].innerHTML = result['te2'];
	td[28].innerHTML = result['te3'];
	td[29].innerHTML = result['te4'];
	td[30].innerHTML = result['te5'];
	td[31].innerHTML = result['te6'];
	td[32].innerHTML = result['te7'];
	td[33].innerHTML = result['te8'];
	td[34].innerHTML = result['te9'];
	td[35].innerHTML = result['te10'];

	//C的数值大小应该在a和b的中间
	if( parseFloat(result['T_2']) >= parseFloat(result['te6']) || parseFloat(result['T_2']) <= parseFloat(result['te5'])){
		document.getElementById("T_2").style.color = "rgb(244, 88, 88)";
		td[30].style.color = "rgb(244, 88, 88)";
		td[31].style.color = "rgb(244, 88, 88)";
	}

	td = tb[1].getElementsByTagName("td");
	td[10].innerHTML = result['hb1'];
	td[11].innerHTML = result['hb2'];
	td[12].innerHTML = result['hb3'];
	td[13].innerHTML = result['hb4'];
	td[14].innerHTML = result['hb5'];
	td[15].innerHTML = result['hb6'];
	td[16].innerHTML = result['hb_ave'];

	td[18].innerHTML = result['db'];

	td[21].innerHTML = result['hc1'];
	td[22].innerHTML = result['hc2'];
	td[23].innerHTML = result['hc3'];
	td[24].innerHTML = result['hc4'];
	td[25].innerHTML = result['hc5'];
	td[26].innerHTML = result['hc6'];
	td[27].innerHTML = result['hc_ave'];

	td[29].innerHTML = result['dc'];

	td[31].innerHTML = result['m'];

	//所有长度的数值应该保留三个小数，且最后一位数字为偶数，
	var len;
	for(var i = 10; i<=16; i++){
		len = td[i].innerHTML.length;
		if( td[i].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
			td[i].style.color = "rgb(244, 88, 88)";
		}
	}
	for(var i = 21; i<=27; i++){
		len = td[i].innerHTML.length;
		if( td[i].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
			td[i].style.color = "rgb(244, 88, 88)";
		}
	}
	len = td[18].innerHTML.length;
	if( td[18].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
		td[18].style.color = "rgb(244, 88, 88)";
	}
	len = td[29].innerHTML.length;
	if( td[29].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
		td[29].style.color = "rgb(244, 88, 88)";
	}

}

function show_detail_newton(result){
	var i, j;
	var index;
	var tb = document.getElementsByClassName("tb-content");
	tr = tb[0].getElementsByTagName("tr");
	for( i = 2; i<=11; i++){
		td = tr[i].getElementsByTagName("td");
		index = td[0].innerHTML; 
		td[1].innerHTML = result['L'+index];
		td[2].innerHTML = result['R'+index];
		td[3].innerHTML = result['d'+index];
		td[4].innerHTML = result['q'+index];
	}

	document.getElementById("R_commit").innerHTML = result['radius_commit'];
	document.getElementById("R_set").innerHTML = result['radius'];
	document.getElementById("E_R").innerHTML = result['E_R'];
	document.getElementsByClassName("detail_picture")[0].style.backgroundImage = "url(./upload/newton/"+result['stu_num']+".jpg)";

}

function show_detail_moment_inertia(result){
	var tb = document.getElementsByClassName("tb-content");

	//将改变过的td颜恢复到初始状态
	td = tb[0].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67)";
	}
	td = tb[1].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67";
	}

	tr = tb[0].getElementsByTagName("tr");
	var i ,len;
	for( i = 2; i<=7; i++){
		td = tr[i].getElementsByTagName("td");
		td[1].innerHTML = result['t0_'+(i-1)];
		td[2].innerHTML = result['t1_'+(i-1)];
		td[3].innerHTML = result['t2_'+(i-1)];
		td[4].innerHTML = result['t3_'+(i-1)];
	}
	td = tr[8].getElementsByTagName("td");
	td[1].innerHTML = result['t0_ave'];
	td[2].innerHTML = result['t1_ave'];
	td[3].innerHTML = result['t2_ave'];
	td[4].innerHTML = result['t3_ave'];

	tr = tb[1].getElementsByTagName("tr");

	td = tr[1].getElementsByTagName("td");
	for(i = 2; i<=7; i++){
		//数据保留三个小数，且最后一位小数必须为偶数
		len = td[i].innerHTML.length;
		if( td[i].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
			td[i].style.color = "rgb(244, 88, 88)";
		}
		td[i].innerHTML = result["d"+(i-1)];
	}
	td[8].innerHTML = result['d_ave'];
	td[8].style.color = "rgb(251, 189, 99)";
	

	td = tr[2].getElementsByTagName("td");
	td[1].innerHTML = result["x"];
	//数据保留三个小数，且最后一位小数必须为偶数
	len = td[1].innerHTML.length;
	if( td[1].innerHTML[len-4] != '.' || td[1].innerHTML[len-1]%2 != 0){
		td[1].style.color = "rgb(244, 88, 88)";
	}
	td[2].innerHTML = result["x_theoretical"];

	td = tr[3].getElementsByTagName("td");
	for(i = 2; i<=7; i++){
		//数据保留三个小数，且最后一位小数必须为偶数
		len = td[i].innerHTML.length;
		if( td[i].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
			td[i].style.color = "rgb(244, 88, 88)";
		}
		td[i].innerHTML = result["dn_"+(i-1)];
	}
	td[8].innerHTML = result['dn_ave'];
	td[8].style.color = "rgb(251, 189, 99)";
	

	td = tr[4].getElementsByTagName("td");
	for(i = 1; i<=6; i++){
		//数据保留三个小数，且最后一位小数必须为偶数
		len = td[i].innerHTML.length;
		if( td[i].innerHTML[len-4] != '.' || td[i].innerHTML[len-1]%2 != 0){
			td[i].style.color = "rgb(244, 88, 88)";
		}
		td[i].innerHTML = result["dw_"+i];
	}
	td[7].innerHTML = result['dw_ave'];
	td[7].style.color = "rgb(251, 189, 99)";
	

	td = tr[5].getElementsByTagName("td");
	td[2].innerHTML = result['m1'];

	td = tr[6].getElementsByTagName("td");
	td[1].innerHTML = result['m2'];
}

function  show_detail_spectrometer(result){
	var tb = document.getElementsByClassName("tb-content");

	//将改变过的td颜恢复到初始状态
	td = tb[0].getElementsByTagName("td");
	for(var i = 1; i<td.length; i++){
		td[i].style.color = "rgba(21, 20, 20, 0.67)";
	}

	document.getElementById("d").innerHTML = result['d'];
	//应该保留4个有效数字，否则显示红色
	var len = result['d'].length;
		var count = 0;
		for( i = 0; i < len; i++){
			if( result['d'][i] !='0' && result['d'][i]!='.'){
				for( var j = i; j<len; j++){
					if(result['d'][j] != '.') count++;
				}
				console.log(count)
				break;
			}
		}
		if(count!=4){
			document.getElementById("d").style.color = "rgb(244, 88, 88)";
		}

	document.getElementById("d").innerHTML = result['d'];
	document.getElementById("d_theoretical").innerHTML = result['constant'];
	document.getElementById("E_d").innerHTML = result['E_d'];
	document.getElementById("lambda_yellow_inside").innerHTML = result['lambda_yellow_inside'];
	document.getElementById("E_lambda_yellow_inside").innerHTML = result['E_yellow_inside'];
	document.getElementById("lambda_yellow_outside").innerHTML = result['lambda_yellow_outside'];
	document.getElementById("E_lambda_yellow_outside").innerHTML = result['E_yellow_outside'];
	document.getElementById("D").innerHTML = result['D_color'];
	document.getElementById("D_theoretical").innerHTML = result['D_color_theoretical'];

	document.getElementsByClassName("detail_picture_spectrometer")[0].style.backgroundImage = "url(./upload/spectrometer/"+result['stu_num']+".jpg)";

	var i, j;
	for( i = 10; i<=13; i++){
		// for( j = 1; j<=4; j++){
			// alert(result['green_'+(i-9)]);
			td[i].innerHTML = result['green_'+(i-9)];
		// }
	}
	td[i].innerHTML = result['green_angle'];

	for( i = 16; i<=19; i++){
		// for( j = 1; j<=4; j++){
			td[i].innerHTML = result['yellow_inside_'+(i-15)];
		// }
	}
	td[i].innerHTML = result['yellow_inside_angle'];

	for( i = 22; i<=25; i++){
		// for( j = 1; j<=4; j++){
			td[i].innerHTML = result['yellow_outside_'+(i-21)];
		// }
	}
	td[i].innerHTML = result['yellow_outside_angle'];



}

function mark(node){
	//评分
	(document.getElementById("popup-bg-mark")).style.display = "block";
	//找到显示分数那一栏
	var grade_by_machine = document.getElementById("grade_by_machine_index").innerHTML;
	var i;
	for(i = 0; i < grade_by_machine.length; i++){
		if(grade_by_machine[i] == '<') break;
	}
	grade_by_machine = grade_by_machine.substring(0,i);
	console.log(grade_by_machine);
	document.getElementById("mark_group_num").innerHTML =  node.parentNode.parentNode.getElementsByTagName("td")[0].innerHTML;
	document.getElementById("mark_name").innerHTML =  node.parentNode.parentNode.getElementsByTagName("td")[2].innerHTML;
	// if( grade_by_machine=='未完成'){
	// 	document.getElementById("grade_by_machine").innerHTML = '0';
	// }else{
		document.getElementById("grade_by_machine").innerHTML = grade_by_machine;
	// }

}

function mark_modify(){
	var remark = document.getElementById("remark").value;
	var grade_modified = document.getElementById("grade_modified").value;
	if( remark == '' && grade_modified == ''){
		alert('未填写任何可提交的信息！');
		return;
	}
	//判断分数是否未数字
	for(var i = 0; i < grade_modified.length; i++){
		if(grade_modified[i]>'9' || grade_modified[i]<'0'){
			alert("输入的分数有误！");
			return;
		}
	}
	var exp_name = (document.getElementsByClassName("container"))[0].getAttribute("name");
	var group_num = document.getElementById("mark_group_num").innerHTML;

	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=mark_submit&group_num="+group_num+"&exp_name="+exp_name
		+"&remark="+remark+"&grade_modified="+grade_modified,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			var result = xmlobj.responseText;
				console.log(result);
			if( result == 1){
				alert("提交成功！");
				document.getElementsByClassName("popup-bg-mark")[0].style.display = "none";
				infoAjax(2);
				
			}else{ 
				alert("提交失败！");
			}
		}
	}
	xmlobj.send();
}

function pass(option,status_count){
	var exp_name = (document.getElementsByClassName("container"))[0].getAttribute("name");
	var group_num = (document.getElementById("group_num")).innerHTML;
//	console.log(group_num);
	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=pass&group_num="+group_num+"&option="+option+"&exp_name="+exp_name,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			var result = xmlobj.responseText;
			result = parseJson(result);
			//console.log(result);
			if( result['status'] == '1'){
				alert("提交成功！");
				infoAjax(status_count);			//提交后刷新一次
				var del = document.getElementById("popup-bg");
				del.style.display = "none";
			}else{ 
				alert("提交失败！");
			}
		}
	}
	xmlobj.send();
}

function fail(option,status_count){
	var exp_name = (document.getElementsByClassName("container"))[0].getAttribute("name");
	var group_num = (document.getElementById("group_num")).innerHTML;
	var xmlobj = createXMLHttpRequest();
	xmlobj.open("GET","./templates/default/infoAjax.php?action=fail&group_num="+group_num+"&option="+option+"&exp_name="+exp_name,true);
	xmlobj.onreadystatechange = function(){
		if( xmlobj.readyState == 4 && xmlobj.status == 200){
			var result = xmlobj.responseText;
			//console.log(result);
			result = parseJson(result);
			if( result['status'] == '1'){
				alert("提交成功！");
				infoAjax(status_count);			//提交后刷新一次
				var del = document.getElementById("popup-bg");
				del.style.display = "none";	
			}else{ 
				alert("提交失败！");
			}
		}
	}
	xmlobj.send();
}

function solve_help(obj,status_count){
	var exp_name = (document.getElementsByClassName("container"))[0].getAttribute("name");
	var group_num = (obj.getElementsByTagName("a"))[0].innerHTML;
	var msg = confirm("受理该请求吗?");
	if(msg){
		var xmlobj = createXMLHttpRequest();
		xmlobj.open("GET","./templates/default/infoAjax.php?action=solve_help&group_num="+group_num+"&exp_name="+exp_name,true);
		xmlobj.onreadystatechange = function(){
			if( xmlobj.readyState == 4 && xmlobj.status == 200){
				infoAjax(status_count);
			}
		}
		xmlobj.send();
	}
}

function change_pwd(){
	document.getElementById("popup-changepwd").style.display="block";
}

function changepwd_submit(){
	var user_id = (document.getElementById("uid")).getAttribute("name");
	var old_pwd = document.getElementById("old_pwd").value;
	var new_pwd = document.getElementById("new_pwd").value;
	var pwd_check = document.getElementById("pwd_check").value;

	if(old_pwd=="" || new_pwd==""  || pwd_check=="") {
		alert("输入不能为空！");
	}else if(new_pwd !== pwd_check){
		alert("两次输入不一致，请重新输入");
	}else{
		var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=change_pwd&user_id="+user_id+"&old_pwd="+old_pwd+"&new_pwd="+new_pwd,true);
		obj.onreadystatechange = function(){
			if( obj.readyState == 4 && obj.status == 200){
			//	alert(obj.responseText);
				if (obj.responseText==1){
					alert("修改成功");
					 close_popup_changepwd();
				}else{
					alert('旧密码错误');
				}
			}
		}
		obj.send();
	}
}

function close_popup_changepwd(){
	document.getElementById("popup-changepwd").style.display="none";
}

function close_popup_mark(){
	document.getElementById("popup-bg-mark").style.display="none";
}

function detail_via_stu_num(this_node){
	var stu_num = document.getElementsByClassName('flag_stu_num')[0];
		stu_num = stu_num.innerHTML;


	var exp_name = (this_node.parentNode.parentNode)
		exp_name = exp_name.getElementsByClassName('exp_name')[0];
		exp_name = exp_name.getAttribute("name");


	var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=show_detail_via_stu_num&stu_num="+stu_num+"&exp_name="+exp_name);
		obj.onreadystatechange = function(){
			if( obj.readyState == 4 && obj.status == 200){
				var detail = document.createElement("div");
				    detail.innerHTML = obj.responseText;
				var body = document.getElementsByTagName("body");
				body[0].appendChild(detail);
				//ocument.write(obj.responseText);
			}
		}
	obj.send();
}

function detail_via_date(this_node){
	var exp_time = (this_node.parentNode.parentNode)
		exp_time = exp_time.getElementsByClassName('exp_time')[0];
		exp_time = exp_time.getAttribute("name");


	var exp_name = (this_node.parentNode.parentNode)
		exp_name = exp_name.getElementsByClassName('exp_name')[0];
		exp_name = exp_name.getAttribute("name");



	var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=show_detail_via_date&exp_time="+exp_time+"&exp_name="+exp_name);
		obj.onreadystatechange = function(){
			if( obj.readyState == 4 && obj.status == 200){
				var detail = document.createElement("div");
				    detail.innerHTML = obj.responseText;
				var body = document.getElementsByTagName("body");
				body[0].appendChild(detail);
				//覆盖父元素滚动条
				(document.getElementsByClassName("search-popup-bg")[0]).style.width = window.screen.width+"px";
				//ocument.write(obj.responseText);

				var time = this_node.parentNode.parentNode.getElementsByTagName("td");
				// console.log(time[0]);
					time = time[0].innerHTML;
				document.getElementById("detail_time").innerHTML = time;
			}
		}
	obj.send();
}

function admin_change_pwd(node){
	var user_name = node.parentNode.parentNode.getElementsByTagName('td')[1];
		user_name = user_name.innerHTML;
	var number = node.parentNode.parentNode.getElementsByTagName('td')[0];
		number = number.innerHTML; 
	var new_pwd = prompt("为用户"+user_name+"设置新密码：");
	if(new_pwd != null ){

		var obj = createXMLHttpRequest();
			obj.open("GET","./templates/default/infoAjax.php?action=admin_change_pwd&new_pwd="+new_pwd+"&number="+number);
			obj.onreadystatechange = function(){
				if( obj.readyState == 4 && obj.status == 200){
					if( obj.responseText == 1){
						alert('修改成功');
						window.location.href = './index.php?user_manage=true';
					}
				}
			}
		obj.send();
	}

}

function admin_delete_user(node){
	var user_name = node.parentNode.parentNode.getElementsByTagName('td')[1];
		user_name = user_name.innerHTML;
	var number = node.parentNode.parentNode.getElementsByTagName('td')[0];
		number = number.innerHTML; 
	if(confirm('确定要从系统中删除用户'+user_name+"吗？")){
		var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=admin_delete_user&number="+number);
		obj.onreadystatechange = function(){
			if( obj.readyState == 4 && obj.status == 200){
				// console.log(obj.responseText);
				if( obj.responseText == 1){
					alert('操作成功');
					window.location.href = './index.php?user_manage=true';
				}
			}
		}
		obj.send();
	}
}

function add_user(){
	document.getElementById('popup-bg-adduser').style.display = "block";
}

function add_user_submit(){
	var number = document.getElementById("number").value;
	var user_name = document.getElementById("added_user_name").value;	
	var pwd = document.getElementById("pwd").value;

	var obj = createXMLHttpRequest();
		obj.open("GET","./templates/default/infoAjax.php?action=add_user&number="+number+"&user_name="+user_name+"&pwd="+pwd);
		obj.onreadystatechange = function(){
			if( obj.readyState == 4 && obj.status == 200){
				 console.log(obj.responseText);
				if( obj.responseText == 1){
					alert('添加成功');
					window.location.href = './index.php?user_manage=true';
				}
			}
		}
	obj.send();

}

function change_parameter(node){
	(document.getElementById("popup-bg-parameter")).style.display = "block";
	var group_num = node.parentNode.parentNode;
		group_num = group_num.getElementsByTagName("td")[0].innerHTML;

	document.getElementById("para_group_num").innerHTML = group_num;

	
}

function change_para_submit_oscillograph(){
	//验证输入的参数格式正确与否
	//...
	//...

	var group_num = document.getElementById("para_group_num").innerHTML;
		
	var f_std = document.getElementById("f_std").value;
	var v_std = document.getElementById("v_std").value;
	console.log(f_std);
	console.log(v_std);
	

	var obj = createXMLHttpRequest();
	obj.open("GET","./templates/default/infoAjax.php?action=change_parameter&exp_name=oscillograph&group_num="+group_num+"&f_std="+f_std+"&v_std="+v_std);
	obj.onreadystatechange = function(){
		if( obj.readyState == 4 && obj.status == 200){
			console.log(obj.responseText);
			if( obj.responseText == 1){
				alert('修改成功');
				var mask = document.getElementsByClassName("mask-form"); 
				window.location.href = "./index.php?exp_name=oscillograph&parameter_setting=true&action=modified";
			}else{
				alert('操作失败');
			}
		}
	}
	obj.send();	
}

function change_para_submit_potentioneter(){
	var group_num = document.getElementById("para_group_num").innerHTML;		
	var Exs = document.getElementById("Exs").value;

	var obj = createXMLHttpRequest();
	obj.open("GET","./templates/default/infoAjax.php?action=change_parameter&exp_name=potentionter&group_num="+group_num+"&Exs="+Exs);
	obj.onreadystatechange = function(){
		if( obj.readyState == 4 && obj.status == 200){
			console.log(obj.responseText);
			if( obj.responseText == 1){
				alert('修改成功');
				var mask = document.getElementsByClassName("mask-form"); 
				window.location.href = "./index.php?exp_name=potentioneter&parameter_setting=true&action=modified";
			}else{
				alert('操作失败');
			}
		}
	}
	obj.send();	
}

function close_popup_para(){
	(document.getElementById("popup-bg-parameter")).style.display = "none";
}

function data_detail(this_node){
	var stu_num = (this_node.parentNode.parentNode.getElementsByTagName("td")[0]).innerHTML;		
	var exp_name = (document.getElementById("detail_exp_name")).getAttribute("name");

	var obj = createXMLHttpRequest();
	obj.open("GET","./templates/default/infoAjax.php?action=data_detail&exp_name="+exp_name+"&stu_num="+stu_num)	;
	obj.onreadystatechange = function(){
		if( obj.readyState == 4 && obj.status == 200){
			console.log(obj.responseText);
			var result = parseJson(obj.responseText);
			document.getElementById("popup-bg").style.display = "block";
			document.getElementById("stu_info_stu_name").innerHTML = (this_node.parentNode.parentNode.getElementsByTagName("td")[1]).innerHTML;	
			document.getElementById("stu_info_stu_num").innerHTML = (this_node.parentNode.parentNode.getElementsByTagName("td")[0]).innerHTML;	

			if(exp_name=='oscillograph'){
				data_detail_oscillograph(result);
			}else if( exp_name=='potentioneter'){
				data_detail_potentioneter(result);
			}else if( exp_name=='thermal_conductivity'){
				data_detail_thermal_conductivity(result);
			}else if( exp_name=='newton'){
				data_detail_newton(result);
			}else if( exp_name=='moment_inertia'){
				data_detail_moment_inertia(result);
			}else if( exp_name=='spectrometer'){
				data_detail_spectrometer(result);
			}
			
		}
	}
	obj.send();	
} 

function data_detail_oscillograph(result){

	var tb = document.getElementsByClassName("tb-content");
			
	td = tb[0].getElementsByTagName("td");
	td[0].innerHTML = result['v_std'];
	td[1].innerHTML = result['f_std'];
	td[2].innerHTML = result['V_DIV'];
	td[3].innerHTML = result['Dy'];
	td[4].innerHTML = result['v_up'];
	td[5].innerHTML = result['E_v'];
	td[6].innerHTML = result['TIME_DIV'];
	td[7].innerHTML = result['n'];
	td[8].innerHTML = result['Dx'];
	td[9].innerHTML = result['T'];
	td[10].innerHTML = result['f_up'];
	td[11].innerHTML = result['E_f'];

	td = tb[1].getElementsByTagName("td");
	td[0].innerHTML = result['Nx1'];
	td[1].innerHTML = result['Nx2'];
	td[2].innerHTML = result['Nx3'];
	td[3].innerHTML = result['Nx4'];
	td[4].innerHTML = result['Ny1'];
	td[5].innerHTML = result['Ny2'];
	td[6].innerHTML = result['Ny3'];
	td[7].innerHTML = result['Ny4'];
	td[8].innerHTML = result['fy1'];
	td[9].innerHTML = result['fy2'];
	td[10].innerHTML = result['fy3'];
	td[11].innerHTML = result['fy4'];
}

function data_detail_potentioneter(result){

	var tb = document.getElementsByClassName("tb-content");
	
	td = tb[0].getElementsByTagName("td");
	td[0].innerHTML = result['U_ab'];
	td[1].innerHTML = result['U_0'];
	td[2].innerHTML = result['I_s'];
	td[3].innerHTML = result['Rab'];
	td[4].innerHTML = result['Is'];
	td[5].innerHTML = result['U0'];
	td[6].innerHTML = result['Uab'];
	td[7].innerHTML = result['E'];

	td = tb[1].getElementsByTagName("td");
	td[0].innerHTML = result['Lx1'];
	td[1].innerHTML = result['Lx2'];
	td[2].innerHTML = result['Lx3'];
	td[3].innerHTML = result['Lx4'];
	td[4].innerHTML = result['Lx5'];
	td[5].innerHTML = result['Lx6'];
	td[6].innerHTML = result['Lx_ave'];

	document.getElementById("Exs").innerHTML = result['Exs'];
	document.getElementById("measure_E").innerHTML = result['Ex'];
	document.getElementById("error_E").innerHTML = result['E_e'];
		
}

function data_detail_moment_inertia(result){
	var tb = document.getElementsByClassName("tb-content");

	tr = tb[0].getElementsByTagName("tr");
	var i ,len;
	for( i = 2; i<=7; i++){
		td = tr[i].getElementsByTagName("td");
		td[1].innerHTML = result['t0_'+(i-1)];
		td[2].innerHTML = result['t1_'+(i-1)];
		td[3].innerHTML = result['t2_'+(i-1)];
		td[4].innerHTML = result['t3_'+(i-1)];
	}
	td = tr[8].getElementsByTagName("td");
	td[1].innerHTML = result['t0_ave'];
	td[2].innerHTML = result['t1_ave'];
	td[3].innerHTML = result['t2_ave'];
	td[4].innerHTML = result['t3_ave'];

	tr = tb[1].getElementsByTagName("tr");

	td = tr[1].getElementsByTagName("td");
	for(i = 2; i<=7; i++){
		td[i].innerHTML = result["d"+(i-1)];
	}
	td[8].innerHTML = result['d_ave'];
	

	td = tr[2].getElementsByTagName("td");
	td[1].innerHTML = result["x"];
	td[2].innerHTML = result["x_theoretical"];

	td = tr[3].getElementsByTagName("td");
	for(i = 2; i<=7; i++){
		td[i].innerHTML = result["dn_"+(i-1)];
	}
	td[8].innerHTML = result['dn_ave'];

	td = tr[4].getElementsByTagName("td");
	for(i = 1; i<=6; i++){
		td[i].innerHTML = result["dw_"+i];
	}
	td[7].innerHTML = result['dw_ave'];

	td = tr[5].getElementsByTagName("td");
	td[2].innerHTML = result['m1'];

	td = tr[6].getElementsByTagName("td");
	td[1].innerHTML = result['m2'];
}

function data_detail_spectrometer(result){
	var tb = document.getElementsByClassName("tb-content");

	var td = tb[0].getElementsByTagName("td");

	document.getElementById("d").innerHTML = result['d'];
	document.getElementById("d_theoretical").innerHTML = result['constant'];
	document.getElementById("E_d").innerHTML = result['E_d'];
	document.getElementById("lambda_yellow_inside").innerHTML = result['lambda_yellow_inside'];
	document.getElementById("E_lambda_yellow_inside").innerHTML = result['E_yellow_inside'];
	document.getElementById("lambda_yellow_outside").innerHTML = result['lambda_yellow_outside'];
	document.getElementById("E_lambda_yellow_outside").innerHTML = result['E_yellow_outside'];
	document.getElementById("D").innerHTML = result['D_color'];
	document.getElementById("D_theoretical").innerHTML = result['D_color_theoretical'];

	document.getElementsByClassName("detail_picture_spectrometer")[0].style.backgroundImage = "url(./upload/spectrometer/"+result['stu_num']+".jpg)";

	var i, j;
	for( i = 10; i<=14; i++){
		for( j = 1; j<=4; j++){
			td[i].innerHTML = result['green_'+j];
		}
	}

	for( i = 16; i<=20; i++){
		for( j = 1; j<=4; j++){
			td[i].innerHTML = result['yellow_inside_'+j];
		}
	}

	for( i = 22; i<=26; i++){
		for( j = 1; j<=4; j++){
			td[i].innerHTML = result['yellow_outside_'+j];
		}
	}

}

function data_detail_newton(result){
	var i, j;
	var index;
	var tb = document.getElementsByClassName("tb-content");
	tr = tb[0].getElementsByTagName("tr");
	for( i = 2; i<=11; i++){
		td = tr[i].getElementsByTagName("td");
		index = td[0].innerHTML; 
		td[1].innerHTML = result['L'+index];
		td[2].innerHTML = result['R'+index];
		td[3].innerHTML = result['d'+index];
		td[4].innerHTML = result['q'+index];
	}

	document.getElementById("R_commit").innerHTML = result['radius_commit'];
	document.getElementById("R_set").innerHTML = result['radius'];
	document.getElementById("E_R").innerHTML = result['E_R'];
	document.getElementsByClassName("detail_picture")[0].style.backgroundImage = "url(./upload/newton/"+result['stu_num']+".jpg)";

}

function data_detail_thermal_conductivity(result){
	var tb = document.getElementsByClassName("tb-content");
	document.getElementById("T_1").innerHTML = result['T_1'];
	document.getElementById("T_2").innerHTML = result['T_2'];
	document.getElementById("change_rate").innerHTML = result['change_rate'];

	td = tb[0].getElementsByTagName("td");
	td[15].innerHTML = result['t1'];
	td[16].innerHTML = result['t2'];
	td[17].innerHTML = result['t3'];
	td[18].innerHTML = result['t4'];
	td[19].innerHTML = result['t5'];
	td[20].innerHTML = result['t6'];
	td[21].innerHTML = result['t7'];
	td[22].innerHTML = result['t8'];
	td[23].innerHTML = result['t9'];
	td[24].innerHTML = result['t10'];

	td[26].innerHTML = result['te1'];
	td[27].innerHTML = result['te2'];
	td[28].innerHTML = result['te3'];
	td[29].innerHTML = result['te4'];
	td[30].innerHTML = result['te5'];
	td[31].innerHTML = result['te6'];
	td[32].innerHTML = result['te7'];
	td[33].innerHTML = result['te8'];
	td[34].innerHTML = result['te9'];
	td[35].innerHTML = result['te10'];

	td = tb[1].getElementsByTagName("td");
	td[10].innerHTML = result['hb1'];
	td[11].innerHTML = result['hb2'];
	td[12].innerHTML = result['hb3'];
	td[13].innerHTML = result['hb4'];
	td[14].innerHTML = result['hb5'];
	td[15].innerHTML = result['hb6'];
	td[16].innerHTML = result['hb_ave'];

	td[18].innerHTML = result['db'];

	td[21].innerHTML = result['hc1'];
	td[22].innerHTML = result['hc2'];
	td[23].innerHTML = result['hc3'];
	td[24].innerHTML = result['hc4'];
	td[25].innerHTML = result['hc5'];
	td[26].innerHTML = result['hc6'];
	td[27].innerHTML = result['hc_ave'];

	td[29].innerHTML = result['dc'];

	td[31].innerHTML = result['m'];

}

