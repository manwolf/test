$(document).ready(function(){
	setC();
	setUrl();
	function setUrl(){
		$u = $("#loadAPI-Base-URL");
		for(var i=0;i<API_Base_URLS.length;i++)
		{
			$u.append('<option value ="'+ API_Base_URLS[i] +'">'+ API_Base_URLS[i] +'</option>');
		}
	}
	function setC(){
		var $c = $("#c").html('<option value ="'+ null +'">请选择控制器</option>');	
		var $a = $("#a").html('<option value ="'+ null +'">请选择执行器</option>');
		var cmap = {};
		for(var i=0;i<API_Doc.length;i++){
			cmap[API_Doc[i].parameter.c] = API_Doc[i].parameter.c;
		}
		for(var k in cmap){
			$c.append('<option value ="'+ k +'">'+ cmap[k] +'</option>');		
		}

	}
	function setA(c){
		var $a = $("#a").html('<option value ="'+ null +'">请选择执行器</option>');
		var amap = {};
		for(var i=0;i<API_Doc.length;i++){
			if(API_Doc[i].parameter.c == c){
				amap[API_Doc[i].parameter.a] = API_Doc[i].parameter.a;
			}
		}
		for(var k in amap){
			$a.append('<option value ="'+ k +'">'+ amap[k] +'</option>');		
		}
	}
	function setP(c,a){
		for(var i=0;i<API_Doc.length;i++){
			if(API_Doc[i].parameter.c == c && API_Doc[i].parameter.a == a){
				var $explan = $("#explan").html(API_Doc[i].explan);
				var api = API_Doc[i];
				var $pbox = $("#p-box");
				for(var k in API_Doc[i].parameter){
					var $p = $("<input/>").attr("name",k).attr("value",API_Doc[i].parameter[k]);
					var $tr = $("<tr></tr>");
					var $a = $("<a class='pick' tar='p-" + k + "' data-t='d' >PICK</a>");
					$("<td>"+ k +"</td>").appendTo($tr);
					$("<td></td>").append($p).appendTo($tr).attr("id","p-"+k);
					$("<td></td>").append($a).appendTo($tr);
					$pbox.append($tr);
				}
			}
		}
	}

	$(document).on("click",".pick",function(){
		if($(this).data("t")=="d"){
			$(this).data("t","s");
			$td = $("#"+$(this).attr("tar"));
			$td.data("input",$td.html());
			$td.html("Be Del-ed!");
		}else{
			$(this).data("t","d");
			$td = $("#"+$(this).attr("tar"));
			$td.html($td.data("input"));
		}
	});

	$("#c").change(function(){
		$("#explan").html("");
		$("#p-box").html("");
		setA($("#c option:selected").val());
	});

	$("#a").change(function(){
		$("#explan").html("");
		$("#p-box").html("");
		setP($("#c option:selected").val(),$("#a option:selected").val());
	});

	$("#POST-BTN").click(function(){
		$("#form-box form").attr("method","post");
		$("#form-box form").attr("action",$("#loadAPI-Base-URL option:selected").val());
		$("#form-box form").submit();
	});

	$("#GET-BTN").click(function(){
		$("#form-box form").attr("method","get");
		$("#form-box form").attr("action",$("#loadAPI-Base-URL option:selected").val());
		$("#form-box form").submit();
	});

});