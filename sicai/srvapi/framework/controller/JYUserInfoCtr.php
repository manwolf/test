<?php
include_once "tools/tokenGen.php";
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
class JYUserInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'oa_user_info';
	}
	// 查询全国教研员
	function queryAllJy() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		  	
		if ($result && $result[0]['jy_city']==全国) { // 教研城市为空 为全国教研员
			if (! $newrow) // 如果为空 查询全部
			{
				$querySql = 'select tid,jy_name,jy_name_en,jy_sex,jy_age,jy_seniority,jy_image,jy_city,jy_district,jy_town,jy_telephone from jy_user_info';
				$model = spClass ( 'jy_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$msg->ResponseMsg ( 0, 'success', $result, $total_page, $prefixJS );
			} elseif ($newrow) // 不为空 按条件查询
			{
				$querySql = 'select tid,jy_name,jy_name_en,jy_sex,jy_age,jy_seniority,jy_image,jy_city,jy_district,jy_town,jy_telephone from jy_user_info where ';
				foreach ( $newrow as $k => $v ) {
					$querySql = $querySql . $k . '="' . $v . '" and ';
				}
				$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
				$model = spClass ( 'jy_user_info' );
				if ($result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql )) {
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, '这个教师不存在！', $result, 0, $prefixJS );
				}
			}
		} else {
			$msg->ResponseMsg ( 1, '您没有权限！', 1, 0, $prefixJS );
		}
	}
	
	// 修改教研员信息
	function jyupdate() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$jy_token = $newrow ['token'];
		$verify = new checkCtr ();
		$result = $verify->VerifyAuth ( "jy_token", $token, "jy_user_info" );
		if ($result && $result ['jy_city'] == null) {
			if ($newrow) {
				$updateSql = 'update jy_user_info set';
				foreach ( $newrow as $k => $v ) {
					if ($k == 'tid') {
						continue;
					}
					$updateSql = $updateSql . $k . '="' . $v . '",';
				}
				$updateSql = substr ( $updateSql, 0, strlen ( $updateSql ) - 1 );
				$model = spClass ( $this->tablename );
				$tidsql = ' where tid=' . $tid;
				$updateSql = $updateSql . $tidsql;
				$result = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '填写的信息是空的！', false, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '您没有权限！', $result, 0, $prefixJS );
		}
	}
	// 删除教研员
	function jydelete() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$jy_token = $newrow ['token'];
		$verify = new checkCtr ();
		$result = $verify->VerifyAuth ( "jy_token", $token, "jy_user_info" );
		if ($result && $result ['jy_city'] == null) {
			if ($tid == null) { // 判断是否有这个教研员
				$msg->ResponseMsg ( 1, '没有找到这个教研员', 1, 0, $prefixJS );
			} else {
				$delSql = 'delete from jy_user_info where  tid=' . $tid;
				$model = spClass ( 'jy_user_info' );
				$result = $model->runSql ( $delSql );
				$msg->ResponseMsg ( 0, '删除成功', 0, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '您没有权限！', $result, 0, $prefixJS );
		}
	}
	
	// 教研员登录
	public function jyland() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
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
		
		if ($result){
			if ($result [0] ['login_state'] == 0) { // login_state=0 时第一次登录要修改密码
					$msg->ResponseMsg ( 0, '第一次登陆请修改密码！', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 0, '登录成功！', $result, 0, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '对不起您没有权限！', 1, 0, $prefixJS );
			}
		}
		}else{
			$msg->ResponseMsg ( 1, '账号密码不能为空！', 1, 0, $prefixJS );
				
		} 
		}
	// 教研第一次登陆修改密码
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
		$model = spClass ( "jy_user_info" );
		$sum = $model->findCount ( array (
				'jy_token' => $token 
		) );
		
		if ($sum > 0) {
			return strtr ( $this->produceToken ( $len ), "+", "A" );
		} else
			return $token;
		// echo $token;
		// exit;
	}
	
	// 产生定长的随机数
	private function randomkeys($length) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
}