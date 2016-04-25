<?php
/**
 * 
 * 功能：移动框架服务器上传文件功能
 * 作者： 孙广兢
 * 日期：2015年8月29日
 */
header ( "Content-Type: text/html; charset=UTF-8" );
header ( "Access-Control-Allow-Origin:*" );
// 如果服务器有TestVersion.key，则运行测试环境
$file_path = "/home/wwwroot/default/upLoadFileTemp/TestVersion.key";
if (file_exists ( $file_path )) {
	define ( 'TestVersion', 'This is a test version.' );
}
if (defined ( 'TestVersion' )) { // 测试环境
	$domain_name = "http://testapi.e-teacher.cn";
} else {
	$domain_name = "http://app.e-teacher.cn";
}
$callback = $_POST ['callback']; // JS_Callback
                                 // 判断文件上传是否发生意外错误
if ($_FILES ["file"] ["error"] > 0) {
	echo $callback . "(" . json_encode ( array (
			"code" => 1,
			"msg" => ' 上传文件出错' . $_FILES ["file"] ["error"],
			"data" => null,
			"pages" => 0 
	) ) . ")";
	exit ();
	break;
}

// 判断文件大于是否满足要求0～50M，
if ($_FILES ["file"] ["size"] > 52428800 or $_FILES ["file"] ["size"] <= 0) {
	echo $callback . "(" . json_encode ( array (
			"code" => 1,
			"msg" => ' 上传文件的大小应为: 0～50M!',
			"data" => null,
			"pages" => 0 
	) ) . ")";
	exit ();
	break;
}
// 判断文件类型 zip 、apk
$typeMatchArray = array (
		"application/x-zip-compressed",
		"application/zip", // 小应用zip文件
		"application/vnd.android.package-archive", // 安卓apk容器
		"application/octet-stream" 
); // 苹果ipa容器
   
// 获取文件后缀类型
$file_type = $_FILES ["file"] ["type"];
if (! in_array ( $file_type, $typeMatchArray )) {
	echo $callback . "(" . json_encode ( array (
			"code" => 1,
			"msg" => ' 上传文件的类型应为: zip、apk、ipa! ',
			"data" => null,
			"pages" => 0 
	) ) . ")";
	exit ();
	break;
}
// 获取容器或小应用的类型
if ($_POST) {
	$type = $_POST ['type']; // 类型，是student、teacher、OA
	$platform = $_POST ['platform']; // 应用平台
	
	switch ($platform) {
		case 1 : // 安卓
			$platformString = "Android";
			break;
		case 2 : // 苹果
			$platformString = "IOS";
			break;
	}
}
$uploadname = $_FILES ["file"] ["name"];
$file_extension = pathinfo ( $uploadname, PATHINFO_EXTENSION ); // 获取文件扩展名
switch ($file_extension) {
	case "zip" : // 上传的是小应用
		$lastposition = 'base';
		break;
	default : // 上传的是容器
		$lastposition = 'eteacher';
		break;
}
// 形式： /framework/Android/student/base/
$position = '/framework/' . $platformString . '/' . $type . '/' . $lastposition . '/';
$localUrlTemp = "/home/wwwroot/default/upLoadFileTemp/" . $uploadname; // 文件即将上传到服务器的临时位置
$localUrl = "/home/wwwroot/default" . $position . $uploadname; // 文件即将上传到服务器的最终位置
$url = $domain_name . $position . $uploadname; // 文件上传后公网可访问的包含域名的url地址
 
move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $localUrlTemp );
$result = rename ( $localUrlTemp, $localUrl );
if ($result) {
	$arr = array (
			"name" => $type,
			"file_extension" => $file_extension,
			"position" => $position,
			"url" => $url 
	);
	echo $callback . "(" . json_encode ( array (
			"code" => 0,
			"msg" => ' 文件' . $uploadname . '上传成功',
			"data" => $arr,
			"pages" => 0 
	) ) . ")";
} else {
	echo $callback . "(" . json_encode ( array (
			"code" => 1,
			"msg" => ' 文件上传失败',
			"data" => null,
			"pages" => 0 
	) ) . ")";
	exit ();
}

?>