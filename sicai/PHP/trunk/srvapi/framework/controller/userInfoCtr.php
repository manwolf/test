<?php
include_once 'base/crudCtr.php';
 /**
 *
 *修改日期：孙广兢 2015年9月7日  新增功能记录市场人员的编号的方法
 */
class userInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'user_info';
	}
	//更新学生信息
	public function checkUpdateParams($row, $token, $prefixJS) 
	{
		$msg = new responseMsg ();
		if (! $this->checkActionParams ( $row, $token )) {
			$msg->ResponseMsg ( 1, '权限不允许', array (), 0, $prefixJS );
			return false;
		}
		return true;
	}
	public function checkAddParams($row, $token, $prefixJS) 
	{
		//echo '$tokenStr='.$token."\n";
		$msg = new responseMsg ();
		if (! $this->checkActionParams ( $row, $token )) {
			$msg->ResponseMsg ( 1, '权限不允许', array (), 0, $prefixJS );
			return false;
		}
		return true;
	}
	protected function checkActionParams($row, $token) 
	{
		$userid = $this->getUsertid ( $row );
		
		if (! $this->verifyUserToken ( $token, $userid )) 
		{
			return false;
		}
		return true;
	}
	private function getUsertid($row) 
	{
		$result = spClass ( 'user_info' )->find ( $row );
		$userid = $result ['tid'];
		return $userid;
	}
	
	// 验证token和id是否一致
	function verifyUserToken($tokenStr, $userid)
	 {
		if (empty ( $tokenStr )) 
		{
			return false;
		}
		//echo '$tokenStr='.$tokenStr."\n";
		$tokenFromServer = $this->getUserTokenByID ( $userid );
		//echo '$userid='.$userid."\n";
		//echo '$tokenFromServer='.$tokenFromServer."\n";
		if ($tokenStr == $tokenFromServer) {
			return true;
		} 
		else 
		{
			return false;
		}
	}
	private function getUserTokenByID($userid) 
	{
		$result = spClass ( 'user_info' )->find ( array (
				'tid' => $userid 
		) );
		return $result ['token'];
	}
	public function setUserImg() 
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$userid = $newrow ['userid'];
		
		if (! $this->is_user_exists ( $userid )) 
		{
			$msg->ResponseMsg ( 1, 'fatal error ,user id not exist ', array (), 0, $prefixJS );
			return;
		}
		
		$user_imageUrl_relative = $this->getUpLoadImg ();
		
		$user_image_url = $this->getImgUrl ( $user_imageUrl_relative );
		
		$model = spClass ( $this->tablename );
		$result = $model->update ( array (
				'tid' => $userid 
		), array (
				'user_image' => $user_imageUrl_relative,
				'user_image_url' => $user_image_url 
		) );
		if ($result) 
		{
			
			$msg->ResponseMsg ( 0, 'success', array (
					'userid' => $userid,
					'user_image' => $user_imageUrl_relative,
					'$user_image_url' => $user_image_url 
			), 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, 'failed', array (), 0, $prefixJS );
		}
	}
	private function is_user_exists($user_tid) 
	{
		$user_info_model = spClass ( $this->tablename );
		$result = $user_info_model->find ( array (
				'tid' => $user_tid 
		) );
		return $result;
	}
	private function getImgUrl($user_imageUrl_relative) 
	{
		$SERVER_ADDR = $_SERVER ["SERVER_ADDR"] == '::1' ? "http://localhost" : "http://" . $_SERVER ["SERVER_ADDR"];
		$user_image_url = $SERVER_ADDR . dirname ( $_SERVER ['PHP_SELF'] ) . '/' . $user_imageUrl_relative;
		return $user_image_url;
	}
	private function getUpLoadImg() 
	{
		$uptypes = array (
				'image/jpg',
				'image/jpeg',
				'image/png',
				'image/pjpeg',
				'image/gif',
				'image/bmp',
				'image/x-png' 
		);
		
		$max_file_size = 2000000; // 上传文件大小限制, 单位BYTE
		$destination_folder = "uploadimg/"; // 上传文件路径
		
		$imgpreviewsize = 1 / 2; // 缩略图比例
		
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') 
		{
			/*
			 * if (!is_uploaded_file($_FILES["upfile"]["tmp_name"]))
			 * //是否存在文件
			 * {
			 * echo "图片不存在!";
			 * exit;
			 * }
			 */
			
			$file = $_FILES ["file"];
			if ($max_file_size < $file ["size"]) 
			// 检查文件大小
			{
				return "";
			}
			
			/*
			 * if(!in_array($file["type"], $uptypes))
			 * //检查文件类型
			 * {
			 * echo "文件类型不符!".$file["type"];
			 * exit;
			 * }
			 */
			
			if (! file_exists ( $destination_folder )) 
			{
				mkdir ( $destination_folder );
			}
			
			$filename = $file ["tmp_name"];
			$image_size = getimagesize ( $filename );
			$pinfo = pathinfo ( $file ["name"] );
			$ftype = $pinfo ['extension'];
			$destination = $destination_folder . time () . "." . $ftype;
			if (file_exists ( $destination ) && $overwrite != true)
			 {
				return "";
			}
			
			if (! move_uploaded_file ( $filename, $destination )) 
			{
				return "";
			}
			
			$pinfo = pathinfo ( $destination );
			$fname = $pinfo ['basename'];
			return $destination;
		}
	}
	/**
	 * 孙广兢
	 * 记录市场人员的编号，如果用户已经有市场人员的编号，则不会重复记录
	 * 判断用户与该市场人员的编号的城市属性是否一致，如果不一致，则不会记录
	 * @param string $user_tid 用户tid
	 * @param string $sc_num 市场人员编号
	 * return boole
	 */
	public function recordSCIdentifier($user_tid = "", $sc_num = "") {			
		if ($sc_num == null or $user_tid == null) {
			return "市场人员的编号为空";//false;
		} else {
			$model = spClass ( 'user_info' );
			//判断该用户是否已经有市场人员编号，如果存在，则退出，返回false；
			$querySql = ' SELECT sc_num,user_city FROM user_info WHERE tid =  "' . $user_tid . '"';
			$userResult = @$model->findSql ( $querySql );
			if ($userResult['0']['sc_num']) {
				return "该用户不能重复记录市场人员编号";//false;
			}
			//判断该市场人员的编号是否有效，如果不存在，则退出，返回false；
			$model = spClass ( 'sc_user_info' );
			$querySql = ' SELECT tid FROM sc_user_info 
					WHERE sc_city = "'.$userResult['0']['user_city'].'" AND sc_num =  "' . $sc_num . '"';
			$scResult = @$model->findSql ( $querySql );
			if (!$scResult) {
				return "市场人员的编号无效";//false;
			}			
			//记录用户的市场人员编号，如果记录失败，则退出，返回false；否则返回true；
			$model = spClass ( 'user_info' );
			$updateSql = ' UPDATE user_info SET sc_num = "'.$sc_num.'" WHERE tid =  "' . $user_tid . '"';
			$result = @$model->runSql ( $updateSql );
			$affectedRows = @$model->affectedRows ();
			if ($affectedRows) {
				return "记录用户的市场人员编号成功";//true
			} else{ 
				return "记录用户的市场人员编号失败";//false;
			}
		}
	}

	//禁止以下action实例化基类
	function query() {
		return false;
	}
	function delete() {
		return false;
	}	
	function add() {
		return false;
	}
}
