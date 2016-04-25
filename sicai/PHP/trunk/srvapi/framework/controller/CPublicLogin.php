<?php
// include_once "tools/tokenGen.php";
//include_once 'TeacherIntroduction.php';
//include_once "tools/defSqlInject.php";
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
class CPublicLogin extends crudCtr {
	
	
	function landAll() {
	$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		if($newrow){
			$querySql='select tid from oa_user_info where telephone="'.$newrow['telephone'].'"'. ' and pwd="'.$newrow['pwd'].'"';
			$model = spClass ( 'oa_user_info' );
			$result = $model->findSql ( $querySql );
			
			if($result[0]['tid']==null){
				$msg->ResponseMsg ( 1, '账号或密码错误！', false, 0, $prefixJS );
			}else{
				$verify = new checkCtr ();
				$result = $verify->login ();
				
				if ($result [0] ['login_state'] == 0) { // login_state=0 时第一次登录要修改密码
						$msg->ResponseMsg ( 0, '第一次登陆请修改密码！', $result, 0, $prefixJS );
					} else {
						$msg->ResponseMsg ( 0, '登录成功！', $result, 0, $prefixJS );
					}
				}
		}else{
			$msg->ResponseMsg ( 1, '账号密码不能为空！', 1, 0, $prefixJS );
	
		}
	}
	function updatePwdFirst() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
	
		$verify = new checkCtr ();
		$result = $verify->acl ();
	
		if ($result) {
			$pwd = $newrow ['pwd'];
			if ($pwd == null) {
				$msg->ResponseMsg ( 1, '请输入密码!', false, 0, $prefixJS );
			} else {
				$updateSql = "update oa_user_info set login_state=1, pwd='" . $pwd . "' where login_state=0 and tid='" . $newrow['tid'] . "'";
				$model = spClass ( 'oa_user_info' );
				$result = @$model->runSql ( $updateSql );
				$affectedRows = @$model->affectedRows ();
				if ($affectedRows) {
					$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, ' 修改失败！', false, 0, $prefixJS );
					exit ();
				}
			}
		} else {
			$msg->ResponseMsg ( 1, '身份验证失败！', 1, 0, $prefixJS );
		}
	}
	
	
	
	
	//公共登录
	function landMarketer()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$telephone=$newrow['telephone'];
		$pwd=$newrow['pwd'];
		$login_type = $newrow['login_type'];
	#每换一个使用环境需要将HTTP链接换到对应环境
		switch($login_type){
			case 'JY':

				if(defined('TestVersion'))
				{
					$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=JYUserInfoCtr&a=jyland&telephone=".$telephone."&pwd=".$pwd."&login_type=".$login_type."&callback=".$prefixJS;
// 					file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=JYUserInfoCtr&a=jyland&telephone=".$telephone."&pwd=".$pwd."&callback=".$prefixJS;
// 					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			case 'sys':

				if(defined('TestVersion'))
				{
					$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=sysAdminCtr&a=sysland&sys_name=".$telephone."&sys_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}else
				{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=sysAdminCtr&a=sysland&sys_enam=".$telephone."&sys_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			case 'KF':
				if(defined('TestVersion'))
				{
					$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=KFUserCtr&a=kfUserLand&kf_telephone=".$telephone."&kf_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=KFUserCtr&a=kfUserLand&kf_telephone=".$telephone."&kf_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
					echo file_get_contents($url);
				}
				break;
			case 'KFA':

				if(defined('TestVersion'))
				{
					$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=KFManageCtr&a=kfAdminLand&kf_admin_telephone=".$telephone."&kf_admin_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=KFManageCtr&a=kfAdminLand&kf_admin_telephone=".$telephone."&kf_admin_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			case 'SCA':

				if(defined('TestVersion'))
				{
					$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=SCManageCtr&a=scAdminLand&sc_telephone=".$telephone."&sc_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=SCManageCtr&a=scAdminLand&sc_telephone=".$telephone."&sc_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			case 'SC' :

				if(defined('TestVersion'))
				{
					$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=SCMarketerCtr&a=landMarketer&sc_telephone=".$telephone."&sc_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=SCMarketerCtr&a=landMarketer&sc_telephone=".$telephone."&sc_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			case 'LS':

				if(defined('TestVersion'))
				{
				$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=JYTeacherInfoCtr&a=landingTeacher&telephone=".$telephone."&login_pwd=".$pwd."&callback=".$prefixJS;
				file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=KFManageCtr&a=kfAdminLand&kf_admin_telephone=".$telephone."&kf_admin_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			case 'XS':

				if(defined('TestVersion'))
				{
				$url="HTTP://testapi.e-teacher.cn/srvapi/framework/index.php?c=passportCtr&a=loginAction&telephone=".$telephone."&login_pwd=".$pwd."&callback=".$prefixJS;
				file_get_contents($url);
				echo file_get_contents($url);
				}else{
					$url="HTTP://api.e-teacher.cn/srvapi/framework/index.php?c=KFManageCtr&a=kfAdminLand&kf_admin_telephone=".$telephone."&kf_admin_pwd=".$pwd."&callback=".$prefixJS;
					file_get_contents($url);
				echo file_get_contents($url);
				}
				break;
			
			
		}
// 	   return ture;
	}
}