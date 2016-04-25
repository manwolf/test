<?php

include_once 'base/crudCtr.php';
/**
 * 功能：上传文件相关接口，该接口只支持android家长端
 * 作者： 陈鸿润
 * 创建日期：2015年8月27日
 * 最新修改：2015年8月31日
 */
class uploadCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'pay_list';
	}
	# 子类对add实现空操作
	public function add(){
		exit();
	}
	# 子类对update实现空操作
	public function update(){
		exit();
	}
	/**
	 * 上传文件
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function uploadForTeacher(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		switch ($newrow['type'])
		{
			case 0:						# 0为上传教案 1为上传教师头像
				if($_FILES["image"]["size"]<= 0){ # 未上传文件
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Not upload file.',"data"=>null,"pages"=>0) ).")";
					break;
				}
				if($_FILES["image"]["size"]>= 8388608){ # 文件大于4M，结束传输
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'File too large.',"data"=>null,"pages"=>0) ).")";
					break;
				}
				if( !preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $_FILES["image"]["type"]) ){
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Type errors.',"data"=>null,"pages"=>0) ).")";
					break;
				}							
				if(defined('TestVersion')){
					//测试环境
					$uploads_dir = '/home/chenmengfan/Teacher_grammer/';
					header("Content-Type: text/html; charset=UTF-8");
					$uploadname = $_FILES["image"]["name"];
					$uploadtitle = $_POST["title"];
					move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir.$uploadname);
					copy("/home/chenmengfan/Teacher_grammer/".$uploadname, "/home/wwwroot/default/testfile/Teacher_grammer/".$uploadname);
					$url = "http://testapi.e-teacher.cn/testfile/Teacher_grammer/".$uploadname;
				}else{
					//正式环境
					$uploads_dir = '/home/imageftp/class/';
					header("Content-Type: text/html; charset=UTF-8");
					$uploadname = $_FILES["image"]["name"];
					$uploadtitle = $_POST["title"];
					move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir.$uploadname);
					copy("/home/imageftp/class/".$uploadname, "/home/wwwroot/default/class/".$uploadname);
					$url = "http://image.e-teacher.cn/class/".$uploadname;
				}
				if( !substr($uploadname, 0, stripos($uploadname, '.')) ){
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'class_tid null.',"data"=>null,"pages"=>0) ).")";
					break;
				}
				$con = mysql_connect(mysql_address, mysql_account, mysql_password);
				if (! $con) {
					die ( 'Could not connect: ' . mysql_error () );
				}
				mysql_select_db ( "eteacher", $con );
				$Str = "update class_list set teacher_grammar = '".$url."' "." where tid=".substr($uploadname, 0, stripos($uploadname, '.'));
				mysql_query ( $Str );
				mysql_close ( $con );
				
				$msg->ResponseMsg ( 0, 'success', $url, 0, $prefixJS );
				
				break;
			case 1:
				if($_FILES["image"]["size"]<= 0){ # 未上传文件
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Not upload file.',"data"=>null,"pages"=>0) ).")";
					break;
				}
				if($_FILES["image"]["size"]>= 8388608){ # 文件大于4M，结束传输
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'File too large.',"data"=>null,"pages"=>0) ).")";
					break;
				}
				if( !preg_match("/^image\/(gif|jpe?g|png|bmp)$/", $_FILES["image"]["type"]) ){
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'Type errors.',"data"=>null,"pages"=>0) ).")";
					break;
				}				
				if(defined('TestVersion')){
					//测试环境
					$uploads_dir = '/home/chenmengfan/Teacher_image/';
					header("Content-Type: text/html; charset=UTF-8");
					$uploadname = $_FILES["image"]["name"];
					$uploadtitle = $_POST["title"];
					move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir.$uploadname);
					copy("/home/chenmengfan/Teacher_image/".$uploadname, "/home/wwwroot/default/testfile/Teacher_image/".$uploadname);
					$url = "http://testapi.e-teacher.cn/testfile/Teacher_image/".$uploadname;
				}else{//正式环境
					$uploads_dir = '/home/imageftp/teacher/';
					header("Content-Type: text/html; charset=UTF-8");
					$uploadname = $_FILES["image"]["name"];
					$uploadtitle = $_POST["title"];
					move_uploaded_file($_FILES['image']['tmp_name'], $uploads_dir.$uploadname);
					copy("/home/imageftp/teacher/".$uploadname, "/home/wwwroot/default/teacher/".$uploadname);
					$url = "http://image.e-teacher.cn/teacher/".$uploadname;
				}
				if( !substr($uploadname, 0, stripos($uploadname, '.')) ){
					echo $_REQUEST['callback']."(".json_encode( array("code"=>1,"msg"=>'class_tid null.',"data"=>null,"pages"=>0) ).")";
					break;
				}
				$con = mysql_connect(mysql_address, mysql_account, mysql_password);
				if (! $con) {
					die ( 'Could not connect: ' . mysql_error () );
				}
				mysql_select_db ( "eteacher", $con );
				$Str = "update teacher_info set teacher_image = '".$url."' "." where telephone=".substr($uploadname, 0, stripos($uploadname, '.'));
				mysql_query ( $Str );
				mysql_close ( $con );
				
				$msg->ResponseMsg ( 0, 'success', $url, 0, $prefixJS );
				
				break;
		}
	}	
}