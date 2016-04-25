<?php
include_once 'base/crudCtr.php';
include_once 'base/encrypt.php';
include_once 'tools/tokenGen.php';
/**
 * 功能：客服主管的操作管理，包括登陆、添加客服专员、删除客服专员、查看客服专员信息
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class KFManageCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'kf_admin_info';
	}
	
	/**
	 * 客服主管登录
	 * 
	 * @return boolean
	 */
	function kfAdminLand() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$kf_admin_telephone = $newrow ['kf_admin_telephone'];
		$kf_admin_pwd = $newrow ['kf_admin_pwd'];
		$verify = new encrypt ();
		$result = $verify->login ( 'kf_admin_telephone', $kf_admin_telephone, 'kf_admin_pwd', $kf_admin_pwd, 'kf_admin_info' );
		if ($result) {
			$msg->ResponseMsg ( 0, '登陆成功', $result, 0, $prefixKF );
			return true;
		} else {
			$msg->ResponseMsg ( 1, '账号和密码不对，登陆失败！', false, 0, $prefixKF );
		}
	}
	
	/**
	 * 查询客服专员基本信息
	 */
	public function queryKfUser() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$token = $newrow ['kf_admin_token'];
		// 获取客服管理员_token ->根据客服管理员登陆对应唯一
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" ); // 验证客服主管身份
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		$querySql = 'SELECT tid , kf_name, kf_name_en, kf_age , kf_city  kf_district ,  kf_town,  
				kf_sex,  kf_image,   kf_telephone ,kf_admin_tid 
				FROM kf_user_info 
				WHERE kf_admin_tid 	= "' . $result ['tid'] . '"';
		$model = spClass ( 'kf_user_info' );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		
		if ($result) {
			$msg->ResponseMsg ( 0, '成功', $result, $total_page, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, '该客服主管所管辖区域内暂无客服专员！', false, $total_page, $prefixKF );
			exit ();
		}
		return true;
	}
	/**
	 * 删除属于该客服主管的客服专员
	 */
	function deleteKfUser() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$token = $newrow ['kf_admin_token'];
		// 获取客服管理员_token ->根据客服管理员登陆对应唯一
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		if ($newrow ['tid'] == null) {
			$msg->ResponseMsg ( 1, ' tid不能为空 ', false, 0, $prefixKF );
			exit ();
		}
		$Sql = 'DELETE FROM kf_user_info WHERE tid = "' . $newrow ['tid'] . '" AND kf_admin_tid = "' . $result ['tid'] . '"';
		$model = spClass ( 'kf_user_info' );
		$result = $model->runSql ( $Sql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, '删除客服专员成功', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, '删除客服专员失败！ ', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 添加专属于该客服主管的客服专员
	 * 
	 * @return boolean
	 */
	function addKfUser() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$token = $newrow ['kf_admin_token'];
		$kf_name = $newrow ['kf_name'];
		$kf_pwd = $newrow ['kf_pwd'];
		$kf_telephone = $newrow ['kf_telephone'];
		// 获取客服管理员_token ->根据客服管理员登陆对应唯一
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		
		// 只有全国客服主管才能可以选择城市添加客服专员；普通客服主管只能添加本城市的客服专员
		if ($city == "全国") {
			if ($newrow ['kf_city'] == null) {
				$msg->ResponseMsg ( 1, '新增客服人员的城市不能为空！', false, 0, $callback );
				exit ();
			} else {
				$city = $newrow ['kf_city'];
			}
		} else {
			$city = $result ['kf_admin_city'];
			if ($city == null) {
				$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
				exit ();
			}
		}
		
		$kf_admin_tid = $result ['tid'];
		if ($kf_name == null or $kf_pwd == null or $kf_telephone == null) {
			$msg->ResponseMsg ( 1, ' 客服手机号（登陆账号）、密码、姓名均不能为空 ', false, 0, $prefixKF );
			exit ();
		}
		// 判断客服专员的姓名与电话号码是否重复
		$condition = ' SELECT tid FROM kf_user_info WHERE kf_telephone = "' . $kf_telephone . '" OR 
				kf_name = "' . $kf_name . '" ';
		$model = spClass ( 'kf_user_info' );
		$result = @$model->findsql ( $condition );
		if ($result) {
			$msg->ResponseMsg ( 1, ' 该客服专员姓名与手机号已经被注册过！', false, 0, $prefixKF );
			exit ();
		}
		$new ['kf_name'] = $kf_name;
		$new ['kf_pwd'] = $kf_pwd;
		$new ['kf_city'] = $city;
		$new ['kf_telephone'] = $kf_telephone;
		$new ['kf_admin_tid'] = $kf_admin_tid;
		$new ['kf_token'] = $this->produceToken (); // 自动生成token
		$model = spClass ( 'kf_user_info' );
		$result = @$model->create ( $new ); // 进行新增操作
		if ($result) {
			$msg->ResponseMsg ( 0, '添加客服专员成功', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, '添加客服专员失败！ ', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 产生token
	 * 
	 * @param number $len        	
	 * @return string
	 */
	private function produceToken($len = 8) {
		$tokenTxt = $this->randomkeys ( $len );
		// 这里的$tokenTxt不是token，是token的源字符串，系统默认自动生成一个8位的随机数做为token的源。
		$token = tokenGen::encrypt ( $tokenTxt );
		// start $token里会出现特殊符号，下面将特殊符号替换为数字或字母
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i; $i < strlen ( $token ); $i ++) {
			$ord_token = ord ( $token {$i} );
			if (! (($ord_token >= 48 && $ord_token <= 57) || ($ord_token >= 65 && $ord_token <= 90) || ($ord_token >= 97 && $ord_token <= 122) || ($ord_token == 61))) {
				$token {$i} = $pattern {mt_rand ( 0, 35 )};
			}
		}
		$model = spClass ( "kf_user_info" );
		$sum = $model->findCount ( array (
				'kf_token' => $token 
		) );
		if ($sum > 0) {
			return strtr ( $this->produceToken ( $len ), "+", "A" );
		} else {
			return $token;
		}
	}
	
	/**
	 * 产生定长的随机数
	 * 
	 * @param string $length        	
	 */
	private function randomkeys($length) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}

/**
 * END
 */
}
		