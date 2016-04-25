<script type="text/javascript" src="local.venus.com/Venus/js/common/jquery.tools.min.js"></script>



$(document).ready(function() {	
	displaySearch();
	searchAdData();
});

$('#type').change(function(){	
	displaySearch();
	searchAdData();
});

function searchAdData(){
	//showPop('查询中...');
	var beginDate 	= $('#beginDate').val();
	var endDate 		= $('#endDate').val();
	var type 		= $('#type').val();
	var campaign 	= $('#campaign').val();
	var apk 			= $('#apk').val();
	var app 			= $('#app').val();
	
	var orderBy 	  	= '';
	var orderType 	= '';
	var data = '&timeStamp=' + new Date().getTime() +'&search=1' + '&beginDate=' + beginDate + '&endDate=' + endDate 
	+ '&type=' + type +  '&campaign=' + campaign +  '&apk=' + apk +  '&app=' + app + '&orderBy=' + orderBy + '&orderType=' + orderType;
	$.ajax({ 
		type: 		'GET', 
		url: 		'/index.php/admin/AdData', 
		dataType: 	'json', 
		data: 		data, 
		success: 	function(content){
			//hidePop();
			console.info(content);
			updateAdData(type,content,$('#dataBody'));
			console.info
			highlight(campaign,$('.campaignName'));
			highlight(apk,$('.apkName'));
			highlight(app,$('.appName'));
			
			//show('detailInfoDiv');
		}
	});
}

function updateAdData(type,data,obj){
	obj.empty();		
	var str = "<tr>";	
	str += "<th class='tdCenter'  style='width:5%' class='tdLeft'>序号</th>";
	
	str += "<th class='tdLeft' " + isDisplay('2',type) + " style=' width:15%'>广告活动</th>";
	str += "<th class='tdCenter'" + isDisplay('3',type) + " style='width:10%'>包ID</th>";
	str += "<th class='tdLeft'" + isDisplay('4',type) + " style='width:15%'>渠道</th>";
	str += "<th class='tdLeft'" + isDisplay('5',type) + " style='width:15%'>日期</th>";
	str += "<th class='tdLeft'" + isDisplay('6',type) + " style='width:15%'>月份</th>";
	
	str += "<th class='tdCenter' style='width:10%'>展示量</th>";
	str += "<th class='tdCenter' style='width:10%'>点击量</th>";
	str += "<th class='tdCenter' style='width:10%'>下载量</th>";
	str += "<th class='tdCenter' style='width:10%'>安装量</th>";
	str += "<th class='tdCenter' style='width:10%'>激活量</th>";
	
	str += "<th class='tdLeft'" + isDisplay('7',type) + " style='width:10%'>活动明细</th>";
	str += "<th class='tdLeft'" + isDisplay('8',type) + " style='width:10%'>包明细</th>";
	str += "<th class='tdLeft'" + isDisplay('9',type) + " style='width:10%'>每日明细</th>";
	str += "<th class='tdLeft'" + isDisplay('10',type) + " style='width:10%'>渠道明细</th>";
	str += "</tr>";
	
	var strData 				= "";
	var strDataAll 			= "";
	var 	totalPVAll 			= 0;
	var totalClickAll	 	= 0;
	var totalDownloadedAll 	= 0;
	var totalInstallAll 		= 0;
	var totalActiveAll	 	= 0;
	//遍历data，将数据构造成html字符串，追加到dataBody中，展示出来
	$.each(data, function(ind, item){
		totalPVAll 			+= parseInt(item.totalPV);
		totalClickAll 		+= parseInt(item.totalClick);
		totalDownloadedAll	+= parseInt(item.totalDownloaded);
		totalInstallAll		+= parseInt(item.totalInstall);
		totalActiveAll		+= parseInt(item.totalActive);
		
		strData += "<tr>";
		strData += "<td>" + (ind + 1) + "</td>";
		
		strData += "<td class='campaignName'" + isDisplay('2',type) + ">"+ item.campaignName +"</td>";
		strData += "<td class='apkName' " + isDisplay('3',type) + ">"+ item.apk +"</td>";
		strData += "<td class='appName' " + isDisplay('4',type) + ">"+ item.appName +"</td>";
		strData += "<td" + isDisplay('5',type) + ">"+ item.day +"</td>";
		strData += "<td" + isDisplay('6',type) + ">"+ item.month +"</td>";
		
		strData += "<td>"+ isZero(item.totalPV) +"</td>";
		strData += "<td>"+ isZero(item.totalClick) +"</td>";
		strData += "<td>"+ isZero(item.totalDownloaded) +"</td>";
		strData += "<td>"+ isZero(item.totalInstall) +"</td>";
		strData += "<td>"+ isZero(item.totalActive) +"</td>";
		
		strData += "<td" + isDisplay('7',type) + "><span  class='xyjButtonSmall qq' onclick='searchAdDataDetail(1,"+ item.campaign + "," + item.app + "," + item.day + "," + item.month  + ")'>活动明细</span></td>";
		strData += "<td" + isDisplay('8',type) + "><span  class='xyjButtonSmall qq' onclick='searchAdDataDetail(2,"+ item.campaign + "," + item.app + "," + item.day + "," + item.month  + ")'>包明细</span></td>";
		strData += "<td" + isDisplay('9',type) + "><span  class='xyjButtonSmall qq' onclick='searchAdDataDetail(3,"+ item.campaign + "," + item.app + "," + item.day + "," + item.month  + ")'>每日明细</span></td>";
		strData += "<td" + isDisplay('10',type) + "><span  class='xyjButtonSmall qq' onclick='searchAdDataDetail(4,"+ item.campaign + "," + item.app + "," + item.day + "," + item.month  + ")'>渠道明细</span></td>";		

		strData += "</tr>";
	});
	
	strDataAll += "<tr>";
	strDataAll += "<td style='color:orange;font-size:15px;' >总计</td>";
	
	strDataAll += "<td class='campaignName'" + isDisplay('2',type) + ">-</td>";
	strDataAll += "<td class='apkName' " + isDisplay('3',type) + ">-</td>";
	strDataAll += "<td class='appName' " + isDisplay('4',type) + ">-</td>";
	strDataAll += "<td " + isDisplay('5',type) + ">-</td>";
	strDataAll += "<td " + isDisplay('6',type) + ">-</td>";
	
	strDataAll += "<td style='color:orange;font-size:15px;'>"+ isZero(totalPVAll) +"</td>";
	strDataAll += "<td style='color:orange;font-size:15px;'>"+ isZero(totalClickAll) +"</td>";
	strDataAll += "<td style='color:orange;font-size:15px;'>"+ isZero(totalDownloadedAll) +"</td>";
	strDataAll += "<td style='color:orange;font-size:15px;'>"+ isZero(totalInstallAll) +"</td>";
	strDataAll += "<td style='color:orange;font-size:15px;'>"+ isZero(totalActiveAll) +"</td>";
	
	strDataAll += "<td " + isDisplay('7',type) + ">-</td>";
	strDataAll += "<td " + isDisplay('8',type) + ">-</td>";
	strDataAll += "<td " + isDisplay('9',type) + ">-</td>";
	strDataAll += "<td " + isDisplay('10',type) + ">-</td>";		

	strDataAll += "</tr>";	
	str = str + strDataAll +strData;
	obj.append(str);
}


function searchAdDataDetail(detailType,campaignID,appID,day,month){
	//showPop('查询中...');
	console.info('searchAdDataDetail');return;
	var beginDate 	= $('#beginDate').val();
	var endDate 		= $('#endDate').val();	
	var campaign 	= $('#campaign').val();
	var apk 			= $('#apk').val();
	var app 			= $('#app').val();
	var type 		= $('#type').val();
	
	var orderBy 	  	= '';
	var orderType 	= '';
	var data = '&timeStamp=' + new Date().getTime() +'&search=1' + '&beginDate=' + beginDate + '&endDate=' + endDate 
	+ '&type=' + type +  '&campaign=' + campaign +  '&apk=' + apk +  '&app=' + app + '&orderBy=' + orderBy + '&orderType=' + orderType
	+ '&campaignID=' + campaignID + '&appID='+ appID ;
	
	$.ajax({ 
		type: 		'GET', 
		url: 		'/index.php/admin/AdData', 
		dataType: 	'json', 
		data: 		data, 
		success: 	function(content){
			//hidePop();
			console.info(content);
			updateAdData(type,content,$('#dataBody'));
			console.info
			highlight(campaign,$('.campaignName'));
			highlight(apk,$('.apkName'));
			highlight(app,$('.appName'));
			
			//show('detailInfoDiv');
		}
	});
	
}




//判断该列是否显示
function isDisplay(col,type){
	var rel = " ";
	switch(type){
	case '1':{
		if('3' == col || '4' == col || '5' == col || '6' == col || '7' == col || '9' == col ){
			rel=  " style='display:none' ";
		}			
		break;
		}
	case '2':{
		if( '4' == col || '5' == col || '6' == col || '7' == col || '8' == col || '10' == col){
			rel=  " style='display:none' ";
		}
		break;
		}
	case '3':{
		if( '2' == col || '3' == col || '5' == col || '6' == col || '10' == col ){
			rel=  " style='display:none' ";
		}
		break;
		}
	case '4':{
		if( '2' == col || '3' == col || '4' == col || '6' == col || '8' == col ||  '9' == col){
			rel=  " style='display:none' ";
		}
		break;
		}
	case '5':{
		if( '2' == col || '3' == col || '4' == col || '5' == col || '8' == col ||  '9' == col){
			rel=  " style='display:none' ";
		}
		break;
		}	
	}
	return rel;
}


function isZero(num){
	var rel = num ;
	if('0' == num){
		rel = "<span class='gray'>"+ num +"</span>"
	}
	return rel;
}

function displaySearch(){
	var type 		= $('#type').val();
	switch(type){
	case '1':{
		$('#campaign').fadeIn();
		$('#apk').fadeOut();
		$('#app').fadeOut();
		break;
		}
	case '2':{
		$('#campaign').fadeIn();
		$('#apk').fadeIn();
		$('#app').fadeOut();
		break;
		}
	case '3':{
		$('#campaign').fadeOut();
		$('#apk').fadeOut();
		$('#app').fadeIn();
		break;
		}
	case '4':{
		$('#campaign').fadeOut();
		$('#apk').fadeOut();
		$('#app').fadeOut();
		break;
		}
	case '5':{
		$('#campaign').fadeOut();
		$('#apk').fadeOut();
		$('#app').fadeOut();
		break;
		}
	}
}

