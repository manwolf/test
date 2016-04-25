<?php
/**
 * 
 * 功能：移动框架服务器上传文件功能
 * 作者： 孙广兢
 * 日期：2015年8月29日
 * 修改：2015年9月21日 重构文件上传信息
 */
		require_once ("{$_SERVER['DOCUMENT_ROOT']}/srvapi/framework/config.php");
		header ( "Content-Type: text/html; charset=UTF-8" );
		header ( "Access-Control-Allow-Origin:*" );
		
		// 输入过滤信息
		$type = $_REQUEST ['type']; // 类型，是student、teacher
		$name = $type; // 名字，是student、teacher
		$platform = ( integer ) $_REQUEST ['platform']; // 应用平台
		$version = $_REQUEST ['version']; // 应用平台
		if (! preg_match ( '/^(student|teacher)$/', $type ) ) {
			getMsg (1,"请输入文件类型: student、teacher ",false,0);			
		}
		if (! preg_match ( '/^[1-2]$/', $platform )) {
			getMsg (1,"请输入应用平台:   安卓：1、苹果： 2)",false,0);
		}
		if (! preg_match ( '/^([1-9]?[0-9])+(\.[1-9]?[0-9]){2}$/', $version )) {
			getMsg (1,"请输入正确的版本号，如1.0.0 ",false,0);
		}
		
		// 如果定义TestVersion，则运行测试环境
		if (defined ( 'TestVersion' )) { // 测试环境
			$domain_name = "http://testapi.e-teacher.cn";
		} else {
			$domain_name = "http://app.e-teacher.cn";
		}
		
		// 判断文件上传是否发生意外错误
		if ($_FILES == NULL or $_FILES ["file"] ["error"] > 0) {
			getMsg (1,"上传文件出错 ",$_FILES,0);			
		}
		
		// 判断文件大于是否满足要求0～50M，
		if ($_FILES ["file"] ["size"] > 52428800 or $_FILES ["file"] ["size"] <= 0) {
			getMsg (1,"上传文件的大小应为: 0～50M! ",$_FILES,0);			
		}
		
		// 正则匹配文件类型zip 、apk、ipa
		$file_type = $_FILES ["file"] ["type"];
		$pattern = '/^application\/(zip|x-zip-compressed|vnd.android.package-archive|octet-stream)$/';
		if (! preg_match ( $pattern, $file_type )) {
			getMsg (1,"上传文件的类型应为: zip、apk、ipa ! ",$_FILES,0);			
		}
		
		if($platform == 1){// 安卓
			$platformString = "Android";
		}else{// 苹果
			$platformString = "IOS";
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
		$position = '/framework/' . $platformString . '/' . $name . '/' . $lastposition . '/'; // 用于库存备份
		$new_position = '/framework/new/'; // 用于真实使用，每个文件只存最新版本，旧版删除
		                                   // 学生端：容器名字：studentIOS.ipa studentAndroid.apk; 小应用的名字：studentIOS.zip studentAndroid.zip
		                                   // 教师端：容器名字：teacherIOS.ipa teacherAndroid.apk; 小应用的名字：teacherIOS.zip teacherAndroid.zip
		                                   // 最新文件名
		$backup_name =  $name.$platformString."V".$version.".".$file_extension;
		$new_name = $name . $platformString . '.' . $file_extension;
		
		$localUrlTemp = '/home/wwwroot/default' . $position .  $backup_name; // 文件即将上传到服务器的备份位置
		$localUrl = '/home/wwwroot/default' . $new_position . $new_name; // 文件即将上传到服务器的最终位置
		$url_backup = $position . $backup_name; // 文件上传后公网可访问的备份的url地址
		$url = $new_position . $new_name; // 文件上传后公网可访问的最新的url地址
		
		$file = $_FILES ['file'] ['tmp_name'];
		if (file_exists ( $file )) {
			$file_MD5 = md5_file ( $file, false ); // 32位md5加密值
		} else {
			getMsg (1,"上传文件时发生错误 ! ",$_FILES,0);			
		}
		move_uploaded_file ( $_FILES ['file'] ['tmp_name'], $localUrlTemp );
		$result = copy ( $localUrlTemp, $localUrl );
		if ($result) {
			$arr = array (
					"name" => $_FILES,
					"url" => $url,
					"url_backup" => $url_backup,
					"file_MD5" => $file_MD5 
			);
			getMsg (1,' 文件上传成功',$arr,0);			
		} else {
			getMsg (1,' 文件上传失败',$_FILES,0);
			exit ();
		}
	
	/**
	 * JS编码输出
	 */
	function getMsg($code,$msg,$data,$pages) {
		$th = array(
				'code' => $code,
				'msg' => $msg,
				'data' => $data,
				'pages' => $pages,				
		);		
		echo json_encode ( $th, JSON_UNESCAPED_UNICODE );
		exit ();
	}


?>