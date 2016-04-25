<?php
include_once 'base/crudCtr.php';
include_once 'base/encrypt.php';
/**
 * 客服专员操作，包括登陆
 * 功能：
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class KFUserCtr extends crudCtr{
	/**
	 * 客服专员登录
	 */
	function kfUserLand(){		
		$msg 		= new responseMsg ();
		$capturs 	= $this->captureParams ();
		$prefixJS 	= $capturs ['callback'];			
		$newrow 	= $capturs ['newrow'];
		$kf_telephone 	= $newrow ['kf_telephone'];
		$kf_pwd 	= $newrow ['kf_pwd'];
		//客服专员登陆
		$verify = new encrypt();
		$result = $verify->login('kf_telephone',$kf_telephone,'kf_pwd',$kf_pwd,'kf_user_info');
		$verify->loginAutoRecord ($result['0']['tid'],$result['0']['kf_token']);
		if($result){
			if($result['login_state']==1){//1表示已经登陆过
				$msg->ResponseMsg ( 0, '登录成功', $result, 0, $prefixJS );						
			}else{
				$msg->ResponseMsg ( 0, '第一次登陆请修改密码！', $result, 0, $prefixJS );						
			}
		}else{
			$msg->ResponseMsg ( 1, '账号和密码不对，登陆失败！', false, 0, $prefixJS );
		}
	}
					
	/**
	 * 客服第一次登陆时，要求修改密码
	 * 作者：李坡
	 */
	function updatePwdFirst (){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];		
	 	$pwd=$newrow['pwd'];
	 	$kf_token=$newrow['token'];
	 	$verify = new encrypt();
		$result = $verify->VerifyAuth("kf_token",$token,"kf_user_info");
	 	$tid=$result['tid'];
	 	if($result){
	 	if($pwd==null){
	 		$msg->ResponseMsg ( 1, '请输入密码！', false, 0, $prefixJS );	 		
	 	}else{	 		
	 		$updateSql ='UPDATE kf_user_info SET login_state=1, kf_pwd="'.$pwd.'"' .' WHERE tid='.$tid;
			$model = spClass ( 'kf_user_info' );
	 		$result = $model->runSql ( $updateSql );
			$msg->ResponseMsg ( 0, 'success', 0, 0, $prefixJS );
	 	}
	}else{
		$msg->ResponseMsg ( 0, '身份验证失败', 1, 0, $prefixJS );		
	}
	}

}
	