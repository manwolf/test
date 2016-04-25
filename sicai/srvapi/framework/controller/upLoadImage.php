<?php
/**
 * 功能：上传短期课程图片文件
 * 作者：孙广兢
 * 创建日期：2015年9月11日
 * 
 */
require_once("{$_SERVER['DOCUMENT_ROOT']}/srvapi/framework/config.php");
header ( "Content-Type: text/html; charset=UTF-8" );
header ( "Access-Control-Allow-Origin:*" );
// 如果服务器有TestVersion.key，则运行测试环境
$file_path = "{$_SERVER['DOCUMENT_ROOT']}/test_key/TestVersion.key";
if (file_exists ( $file_path )) {
	define ( 'TestVersion', 'This is a test version.' );
}
if (defined ( 'TestVersion' )) { // 测试环境
	$domain_name = "http://testapi.e-teacher.cn";
} else {//正式环境
	$domain_name = "http://image.e-teacher.cn";
}
// 判断文件上传是否发生意外错误
if ($_FILES == NULL or $_FILES ["file"] ["error"] > 0) {
	echo json_encode ( array (
			"code" => 1,
			"msg" => ' 上传文件出错' . $_FILES ["file"] ["error"],
			"data" => $_FILES,
			"pages" => 0
	) );
	exit ();
	break;
}

// 判断文件大于是否满足要求0～2M，
if ($_FILES ["file"] ["size"] > 2097152 or $_FILES ["file"] ["size"] <= 0) {
	echo json_encode ( array (
			"code" => 1,
			"msg" => ' 上传文件的大小应为: 0～2M!',
			"data" => $_FILES,
			"pages" => 0
	) );
	exit ();
	break;
}
// 判断文件类型 gif、jpg、jpeg、png、bmp
$file_type = $_FILES ["file"] ["type"];
if (!preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $file_type) ) {
	echo json_encode ( array (
			"code" => 1,
			"msg" => ' 上传文件的类型应为:gif、jpg、jpeg、png、bmp ! ',
			"data" => $_FILES,
			"pages" => 0
	) );
	exit ();
	break;
}
// 获取容器或小应用的类型
if ($_REQUEST) {
	$class_num = $_REQUEST ['class_num']; //短期课程的编号
	$type= $_REQUEST ['type'];// 类型，1是短期课程列表图片， 2：短期课程详情图片
	switch($type){
		case 1 ://短期课程列表图片
			$typestring = "listPic";
			break;
		case 2 ://短期课程详情图片
			$typestring = "detailPic";
			break;
		default://默认短期课程列表图片
			$typestring = "listPic";
			break;
	}
	$city = $_REQUEST ['city']; // 城市
	switch ($city) {
		case "上海" : 
			$citystring = "Shanghai";
			break;
		case "重庆" : 
			$citystring = "Chongqing";
			break;
		case "北京" :
			$citystring = "Beijing";
			break;
		case "深圳" :
			$citystring = "Shenzhen";
			break;
		default://默认为上海
			$citystring = "Shanghai";
			break;
	}
}
$position = '/shortCourseImage/';
$uploadname = $citystring.$class_num.$typestring.".png";
$localUrlTemp = "/home/wwwroot/default/upLoadImageTemp/" . $uploadname; // 文件即将上传到服务器的临时位置
$localUrl = "/home/wwwroot/default" . $position . $uploadname; // 文件即将上传到服务器的最终位置
$url = $domain_name . $position . $uploadname; // 文件上传后公网可访问的包含域名的url地址
//开始上传文件
move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $localUrlTemp );
$result = copy ( $localUrlTemp, $localUrl );

if ($result) {	
	$con = @mysql_connect(mysql_address,mysql_account,mysql_password,0) 
	or die(mysql_error());	
	mysql_select_db ( "eteacher", $con );
	switch($type){
		case 1 ://短期课程列表图片
			$Str = ' UPDATE high_quality_courses 	SET high_quality_image = "'.$url.'" WHERE tid= "'.$class_num.'" ';
			break;
		case 2 ://短期课程详情图片
			$Str = ' UPDATE class_quality_details 	SET detail_image  = "'.$url.'" WHERE high_quality_courses_tid = "'.$class_num.'" ';
			break;
		default://默认短期课程列表图片
			$Str = ' UPDATE high_quality_courses 	SET high_quality_image = "'.$url.'" WHERE tid= "'.$class_num.'" ';
			break;
	}		
	mysql_query ( $Str );
	mysql_close ( $con );	
	
	$arr = array (
			"name" => $type,			
			"position" => $position. $uploadname,
			"url" => $url
	);
	echo json_encode ( array (
			"code" => 0,
			"msg" => ' 文件' . $uploadname . '上传成功',
			"data" => $arr,
			"pages" => 0
	) );
} else {
	echo json_encode ( array (
			"code" => 1,
			"msg" => ' 文件上传失败',
			"data" => $_FILES,
			"pages" => 0
	) );
	exit ();
}

?>