<?php
include_once "tools/tokenGen.php";
include_once 'userInfoCtr.php';
include_once 'base/HttpClient.class.php';
include_once "testQuestionPool.php";

header ( "Content-type:text/html; charset=utf-8" );
/**
 * 功能：用户注册 或 登录
 * 作者： 郑哥
 * 创建日期：2015年8月27日
 * 最新修改：2015年8月31日
 * 修改： 2015年9月7日 孙广兢 新增功能：记录市场人员的编号
 */
class passportCtr extends userInfoCtr {
	/**
	 * 家长注册
	 * 作者：郑哥
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 * 修改： 2015年9月7日 孙广兢 新增功能：记录市场人员的编号
	 */
	public function registerAction() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$newrow = $capturs ['newrow'];
		$prefixJS = $capturs ['callback'];
		$login_pwd = $newrow ['login_pwd'];
		// 得到数据的密文
		$login_pwd = md5 ( $login_pwd ); // echo $login_pwd.'---';
		                            // 再把密文字符串的字符顺序调转
		$login_pwd = strrev ( $login_pwd ); // echo $login_pwd.'---';
		                                 // 最后再进行一次MD5运算并返回
		$login_pwd = md5 ( $login_pwd );
		// echo $login_pwd;
		// exit;
		// 为应对苹果应用商店审核，将小陈的手机号在审核期间设置为始终可登录
		$telephone = $newrow [telephone];
		if ("15000034157" == $telephone) {
			$user_info = spClass ( "user_info" );
			$querySQL = 'select * from user_info where telephone = "15000034157"';
			$result = $user_info->findSql ( $querySQL );
			$msg->ResponseMsg ( 0, '登录成功', $result, 1, $prefixJS );
			return;
		}
		if (! $this->IsUserPwdValid ( $newrow )) {
			return;
		}
		
		// 产生新的token
		$newrow ['token'] = $this->produceToken ();
		$user_info = spClass ( "user_info" );
		// 判断手机号是否已注册，如果已注册，则更新密码字段，如果未注册，则新增记录。
		$result = $user_info->findAll ( array (
				'telephone' => "{$newrow['telephone']}" 
		) );
		if (count ( $result ) > 0) {
			// $msg->ResponseMsg ( 1, '手机号已存在', array (), 1, $prefixJS );
			$modifyarray = array ();
			$modifyarray ['login_pwd'] = $newrow ['login_pwd'];
			$result = $user_info->update ( array (
					'tid' => $result [0] ['tid'] 
			), $modifyarray );
		} else {
			$result = $user_info->create ( $newrow );
		}
		
		$result = $user_info->findAll ( array (
				'telephone' => $newrow ['telephone'],
				'login_pwd' => $newrow ['login_pwd'] 
		));
		
		if (count ( $result ) > 0) {
			//从此处开始的几行代码由孙广兢添加
			//记录用户的市场人员编号，成功返回true，失败返回false；返回结果不必告知客户端
			$userInfo = new userInfoCtr();
			$recordResult = $userInfo ->recordSCIdentifier($result['0']['tid'],$newrow ['sc_num'] );			
			//结束添加

			$msg->ResponseMsg ( 0, '注册成功', $result, 1, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '注册失败', array (), 1, $prefixJS );
		}
	}
	/**
	 * 家长登录
	 * 作者：郑哥
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 * 修改： 2015年9月7日 孙广兢 新增功能：记录市场人员的编号
	 */
	public function loginAction() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$condition = $capturs ['newrow'];
		
		// 为应对苹果应用商店审核，将小陈的手机号在审核期间设置为始终可登录
		$telephone = $condition [telephone];
		if ("15000034157" == $telephone) {
			$user_info = spClass ( "user_info" );
			$querySQL = 'select * from user_info where telephone = "15000034157"';
			$result = $user_info->findSql ( $querySQL );			
			$msg->ResponseMsg ( 0, '登录成功', $result, 1, $prefixJS );
			return;
		}
		// 检查参数合法性
		if (!$this->IsUserPwdValid ( $condition )) {
			$msg->ResponseMsg ( 1, '请检查手机号与验证码', $result, 1, $prefixJS );
			return;
		}


		# 查询用户信息表所有内容，并赋值给$result
		$user_info = spClass ( "user_info" );
		$result = $user_info->findAll ( array (
				'telephone' => $condition['telephone'],
				'login_pwd' => $condition['login_pwd']
		));		
		if (count ( $result ) > 0) {
			//从此处开始的几行代码由孙广兢添加
			//记录用户的市场人员编号，成功返回true，失败返回false；返回结果不必告知客户端
			$userInfo = new userInfoCtr();
			$recordResult = $userInfo ->recordSCIdentifier($result['0']['tid'],$condition['sc_num'] );			
			//调用用户测试   判断用户测试权限 --黄东
			$user_tid = $result [0] ['tid'];
			// 调用自动排课接口$testQuestionPool 传入参数
			$testQuestionPool = new testQuestionPool ();
			$testQuestionPool->testingAuthority($user_tid);
			
			$msg->ResponseMsg ( 0, '登录成功', $result, 1, $prefixJS );
		} else {			
			$msg->ResponseMsg ( 1, "请获取 验证码", $result, 1, $prefixJS );
		}
	}	
	// 成功登录后修改Token
	private function changeToken($condition, &$result) {
		$condition = array_change_key_case ( $condition, CASE_LOWER );
		
		$newtoken = $this->produceToken ();
		$user_info = spClass ( "user_info" );
		$user_info->update ( $condition, array (
				'token' => $newtoken 
		) );
		// 普通用户登录成功后修改token
		$result [0] ['token'] = $newtoken;
	}
	
	// 产生token
	private function produceToken($len = 8) {
		$tokenTxt = $this->randomkeys ( $len );
		// echo $tokenTxt;
		
		// 这里的$tokenTxt不是token，是token的源字符串，系统默认自动生成一个8位的随机数做为token的源。
		$token = tokenGen::encrypt ( $tokenTxt );
		// echo '$token='.$token."<br>";
		// start $token里会出现特殊符号，下面将特殊符号替换为数字或字母
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		// $token = strtr($token, "+", "A");
		// $token = '@$^&#$%&$#%**$@#$%^&*()==';
		for($i; $i < strlen ( $token ); $i ++) {
			$ord_token = ord ( $token {$i} );
			if (! (($ord_token >= 48 && $ord_token <= 57) || ($ord_token >= 65 && $ord_token <= 90) || ($ord_token >= 97 && $ord_token <= 122) || ($ord_token == 61))) {
				// echo '$token1='.$token{$i}."<br>";
				// echo '-----'.ord($token{$i})."<br>";
				$token {$i} = $pattern {mt_rand ( 0, 35 )};
				// echo '$token2='.$token{$i}."<br>";
			}
		}
		// end
		// echo '$token转化后='.$token."<br>";
		$model = spClass ( "user_info" );
		$sum = $model->findCount ( array (
				'token' => $token 
		) );
		// echo '$sum='.$sum."<br>";
		// exit;
		if ($sum > 0) {
			return strtr ( $this->produceToken ( $len ), "+", "A" );
		} else
			return $token;
	}
	
	// 产生定长的随机数
	private function randomkeys($length) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
	// 检查注册或登录参数的合法性
	private function IsUserPwdValid($condition) {
		$msg = new responseMsg ();
		if (empty ( $condition ["login_pwd"] )) {
			$msg->ResponseMsg ( 1, '请输入验证码', array (), 1, $prefixJS );
			return false;
		}
		
		if (empty ( $condition ["telephone"] )) {
			$msg->ResponseMsg ( 1, '请输入手机号', array (), 1, $prefixJS );
			return false;
		}
		
		// 查询验证码和手机号在verify_sms中是否有效
		$verify_sms = spClass ( "verify_sms" );
		$sum = $verify_sms->findCount ( array (
				'telephone' => $condition ["telephone"],
				'verify_code' => $condition ["login_pwd"] 
		) );
		if ($sum > 0) {
			return true;
		} else {
			// $msg->ResponseMsg ( 1, '请检查手机号与验证码', array (), 1, $prefixJS );
			return false;
		}
	}
	
	// 登录
	public function testToken() {
		$token = "VToCa1p+VzJcfQA+CmYDPw==";
		echo strtr ( $token, "+", "A" );
		// $msg->ResponseMsg ( 1, "请获取验证码", $result, 1, $prefixJS );
	}

	//禁止以下action实例化基类
	function query() {
		return false;
	}
	function delete() {
		return false;
	}
	function update() {
		return false;
	}
	function add() {
		return false;
	}
}
	
