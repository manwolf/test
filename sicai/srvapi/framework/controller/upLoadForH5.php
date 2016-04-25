<?php
/**
 * 功能：上传文件所使用的对应接口
 * 作者： 陈鸿润
 * 创建日期：2015年8月27日
 * 最新修改：2015年9月9日
 */
require_once("{$_SERVER['DOCUMENT_ROOT']}/srvapi/framework/config.php");
date_default_timezone_set("PRC"); # 设置时区
header("Content-Type: text/html; charset=UTF-8"); # 设置编码UTF-8
header("Access-Control-Allow-Origin:*"); # 允许跨域

# 对上传的类型进行分类 ，0为上传教案 1为上传教师头像 2为上传教师生活照
switch ($_REQUEST['type'])  # 
{	
	case 0: # 上传教案
		if($_FILES["file"]["size"]<= 0){ # 未上传文件
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Not upload file.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if($_FILES["file"]["size"]>= 4194304){ # 文件大于4M，结束传输
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'File too large.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $_FILES["file"]["type"]) ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Type errors.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !$_REQUEST['class_tid'] ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'class_tid null.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if(defined('TestVersion')){
			# 测试环境
			$uploads_dir = '/home/chenmengfan/Teacher_grammer/';
			$uploadname = $_REQUEST['class_tid'].".png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/testfile/Teacher_grammer/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://testapi.e-teacher.cn/testfile/Teacher_grammer/".$uploadname;
		}else{
			# 正式环境
			$uploads_dir = '/home/imageftp/class/';
			$uploadname = $_REQUEST['class_tid'].".png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/class/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://image.e-teacher.cn/class/".$uploadname;
		}
		$con = mysql_connect(mysql_address, mysql_account, mysql_password);
		if (! $con) {
			die ( 'Could not connect: ' . mysql_error () );
		}
		mysql_select_db ( "eteacher", $con );
		$Str = "update class_list set teacher_grammar = '".$url."' "." where tid=".substr($uploadname, 0, stripos($uploadname, '.'));
		mysql_query ( $Str );
		mysql_close ( $con );
		
		echo json_encode( array("code"=>0,"msg"=>'Success',"data"=>array('url'=>$url),"pages"=>0) );
		break;
	case 1: # 上传教师头像
		if($_FILES["file"]["size"]<= 0){ # 未上传文件
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Not upload file.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if($_FILES["file"]["size"]>= 4194304){ # 文件大于4M，结束传输
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'File too large.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $_FILES["file"]["type"]) ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Type errors.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !$_REQUEST['teacher_phone'] ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'teacher_phone null.',"data"=>null,"pages"=>0) ).")";
			break;
		}	
		if(defined('TestVersion')){
			# 测试环境
			$uploads_dir = '/home/chenmengfan/Teacher_image/';
			$uploadname = $_REQUEST['teacher_phone'].".png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/testfile/Teacher_image/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://testapi.e-teacher.cn/testfile/Teacher_image/".$uploadname;
		}else{
			# 正式环境
			$uploads_dir = '/home/imageftp/teacher/';
			$uploadname = $_REQUEST['teacher_phone'].".png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/teacher/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://image.e-teacher.cn/teacher/".$uploadname;
		}
		echo json_encode( array("code"=>0,"msg"=>'Success',"data"=>array('url'=>$url),"pages"=>0) );

	break;		
	case 2: # 上传教师生活照
		if($_FILES["file"]["size"]<= 0){ # 未上传文件
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Not upload file.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if($_FILES["file"]["size"]>= 4194304){ # 文件大于4M，结束传输
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'File too large.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $_FILES["file"]["type"]) ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Type errors.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !$_REQUEST['teacher_phone'] || !$_REQUEST['Teacher_detail_image_no'] ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'teacher_pinyin or Teacher_detail_image_no null.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if(defined('TestVersion')){
			# 测试环境
			$uploads_dir = '/home/chenmengfan/Teacher_detail_image/';
			$uploadname = "{$_REQUEST['teacher_phone']}_{$_REQUEST['Teacher_detail_image_no']}.png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/testfile/Teacher_detail_image/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://testapi.e-teacher.cn/testfile/Teacher_detail_image/".$uploadname;
		}else{
			# 正式环境
			$uploads_dir = '/home/imageftp/teacher_detail_image/';
			$uploadname = "{$_REQUEST['teacher_phone']}_{$_REQUEST['Teacher_detail_image_no']}.png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/teacher_detail_image/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://image.e-teacher.cn/teacher_detail_image/".$uploadname;
		}
		echo json_encode( array("code"=>0,"msg"=>'Success',"data"=>array('url'=>$url),"pages"=>0) );
		break;
	case 3:
		if($_FILES["file"]["size"]<= 0){ # 未上传文件
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Not upload file.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if($_FILES["file"]["size"]>= 8388608){ # 文件大于8M，结束传输
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'File too large.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $_FILES["file"]["type"]) ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Type errors.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if( !$_REQUEST['city'] || !$_REQUEST['image_no'] ){
			echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'teacher_pinyin or Teacher_detail_image_no null.',"data"=>null,"pages"=>0) ).")";
			break;
		}
		if("上海" == $_REQUEST['city']){
			$name = "sh";
			$file = "200000";
		}else if ("重庆" == $_REQUEST['city']){
			$name = "cq";
			$file = "404100";
		}
		if(defined('TestVersion')){
			# 测试环境
			$uploads_dir = '/home/chenmengfan/Shuffling/';
			$uploadname = "{$name}_{$_REQUEST['image_no']}.png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/testfile/Shuffling_figure/{$file}/{$uploadname}");
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://testapi.e-teacher.cn/testfile/Shuffling_figure/{$file}/{$uploadname}";
		}else{
			# 正式环境
			$uploads_dir = '/home/imageftp/shuffling/';
			$uploadname = "{$name}_{$_REQUEST['image_no']}.png";
			$result = move_uploaded_file($_FILES['file']['tmp_name'], $uploads_dir.$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$result = copy($uploads_dir.$uploadname, "/home/wwwroot/default/shuffling/".$uploadname);
			if(!$result){
				echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'上传失败.',"data"=>null,"pages"=>0) ).")";
				exit;
			}
			$url = "http://image.e-teacher.cn/shuffling/".$uploadname;
		}
		echo json_encode( array("code"=>0,"msg"=>'Success',"data"=>array('url'=>$url),"pages"=>0) );
	break;
}