<?php
 include_once 'base/responseMsg.php';
 /**
  * 功能：加密类自定义函数
  * 作者： 孙广兢
  * 日期：2015年8月7日  * 
  */
class encrypt extends  spController {
		
	/**
	 * 加密给定字符串，利用md5、crypt、已知字符串进行混合复杂加密
	 * @param string $str 输入字符串
	 * @return string 返回加密后的字符串
	 */
	public function myMd5($str){		
		if($str){
			$return = md5(crypt($str,substr(MD5STR,1,2)).MD5STR);
		}else{
			$return = " 输入字符串不能为空 ! ";
		}		
		return $return;
	} 
	 
	/**
	 * 根据用户名及密码产生一个AccessToken
	 * @param string $name 用户名
	 * @param string $password 密码
	 * @return string 返回加密后的字符串
	 */
	public function createAccessToken($name,$password){
		$return = myMd5(myMd5($name).MD5STR.myMd5($password));
		return $return;
	}
	
	/**
	 * 手动验证用户tid、token是否存在于表table_name中
	 * @param string $tid_name 用户tid的字段名
	 * @param int $tid_value 用户tid的值
	 * @param string $accessToken_name 用户token的字段名
	 * @param string $accessToken_value 用户token的值
	 * @param string $table_name 用户信息存放的数据表
	 * @return 若验证存在，则返回用户信息，否则输出错误提示
	 */
	public function verifyAuth($accessToken_name='',$accessToken_value='',$table_name='') {
		$msg = new responseMsg ();	
		$conn = @mysql_connect(mysql_address,mysql_account,mysql_password,0) or die(mysql_error());		
		$sql = 'SELECT table_name FROM information_schema.columns WHERE table_name = "'.$table_name.'"';
		$query_result = @mysql_query($sql) or die(" 查找数据表的SQL语句执行失败 ");//执行SQL语句
		if(!$query_result){
			mysql_close ( $conn );
			return false;		
		}else{
			$sql = 'SELECT column_name FROM information_schema.columns WHERE table_name = "'.$table_name.'" AND column_name = "'.$accessToken_name.'"';
			$query_result = @mysql_query($sql) or die(" 查找字段名的SQL语句执行失败 ");//执行SQL语句
			if(!$query_result){
				mysql_close ( $conn );
				return false;		
			}else{
				$sql = 'SELECT * FROM eteacher.'.$table_name.' WHERE '.$accessToken_name.' = "'.$accessToken_value.'" ';
				$gb = spClass($table_name); // 初始化数据表模型类
				$result = @$gb->findSql($sql); // 执行查找				
				if(!$result){
					mysql_close ( $conn );
					return false;					
				}else{
					mysql_close ( $conn );
					return $result['0'];
				}
			}
		}		
	}
	
	/**
	 * 自动验证用户tid、token是否存在于表table_name中
	 * @param string $accessToken_value 用户token的值
	 * @param string $table_name 用户信息存放的数据表
	 * @return 若验证存在，则返回用户信息，否则返回错误
	 */
	public function autoVerifyAuth($accessToken_name='',$table_name='') {
		$tid_name = 'tid';
		if (isset($_COOKIE[md5("user_tid")])){
		$tid_value =  $_COOKIE[md5("user_tid")] ;
		}else{
			mysql_close ( $conn );
				return false;
		}	
		if (isset($_COOKIE[md5("accessToken")])){
			$accessToken_value =  $_COOKIE[md5("accessToken")] ;
		}else{
			mysql_close ( $conn );
				return false;
		}				
		$msg = new responseMsg ();
		$conn = @mysql_connect(mysql_address,mysql_account,mysql_password,0) or die(mysql_error());		
		$sql = 'SELECT table_name FROM information_schema.columns WHERE table_name = "'.$table_name.'"';
		$query_result = @mysql_query($sql) or die(" 查找数据表的SQL语句执行失败 ");//执行SQL语句
		if(!$query_result){
			mysql_close ( $conn );
				return false;
		}else{
			$sql = 'SELECT column_name FROM information_schema.columns WHERE table_name = "'.$table_name.'" AND column_name = "'.$accessToken_name.'"';
			$query_result = @mysql_query($sql) or die(" 查找字段名的SQL语句执行失败 ");//执行SQL语句
			if(!$query_result){
				mysql_close ( $conn );
				return false;
			}else{
				$sql = 'SELECT * FROM eteacher.'.$table_name.' WHERE '.$tid_name.' = "'.$tid_value.'" AND '.$accessToken_name.' = "'.$accessToken_value.'" ';
				$gb = spClass($table_name); // 初始化数据表模型类
				$result = @$gb->findSql($sql); // 执行查找
				if(!$result){
					return false;
					exit();
				}else{
					mysql_close ( $conn );				
					return $result['0'];
				}
			}
		}
	}
	
	/**
	 * 登陆后自动将用户tid，token写入cookie存储变量中，有效期为2小时
	 * @param int $tid
	 * @param string $accessToken
	 */
	 public function loginAutoRecord($tid='',$accessToken='') {
	 	setcookie(md5("user_tid"), $tid);  /* expire in 2 hour */
	 	setcookie(md5("accessToken"), $accessToken);  /* expire in 2hour */	 	
	} 
	
	/**
	 * 退出登陆后，清除用户token的存储信息
	 */
	 public function logout() {
	 	setcookie(md5("user_tid"), "", time()-7200);  /* expire in 2 hour */
	 	setcookie(md5("accessToken"), "", time()-7200);  /* expire in 2hour */	 	
	} 
	
	/**
	 * 用户根据账号及密码、账号存储的数据表进行登陆操作
	 * @param string $login_account_name 登陆账号的字段名
	 * @param string $login_account_value 登陆账号的值
	 * @param string $login_password_name 登陆密码的字段名
	 * @param string $login_password_value 登陆密码的值
	 * @param string $table_name 登陆账号的存储信息
	 * @return 若验证存在，则返回用户信息，否则返回错误
	 */
	public function login($login_account_name='',$login_account_value='',$login_password_name='',$login_password_value='',$table_name=''){
		$msg = new responseMsg ();
		$conn = @mysql_connect(mysql_address,mysql_account,mysql_password,0) or die(mysql_error());		
		$sql = 'SELECT table_name FROM information_schema.columns WHERE table_name = "'.$table_name.'"';
		$query_result = @mysql_query($sql) or die(" 查找数据表的SQL语句执行失败 ");//执行SQL语句
		if(!$query_result){
			mysql_close ( $conn );
			return false;
		}else{
			$sql = 'SELECT column_name FROM information_schema.columns WHERE table_name = "'.$login_account_name.'" AND column_name = "'.$login_password_name.'"';
			$query_result = @mysql_query($sql) or die(" 查找字段名的SQL语句执行失败 ");//执行SQL语句
			if(!$query_result){
				mysql_close ( $conn );
				return false;
			}else{				
				if($login_account_value == null or $login_password_value == null){
					mysql_close ( $conn );
					return false;				
				}				
				$sql = 'SELECT * FROM eteacher.'.$table_name.' WHERE '.$login_account_name.' = "'.$login_account_value.'" AND '.$login_password_name.' = "'.$login_password_value.'" ';
				$gb = spClass($table_name); // 初始化数据表模型类
				$result = @$gb->findSql($sql); // 执行查找
				if(!$result){
					mysql_close ( $conn );
					return false;					
				}else{
					mysql_close ( $conn );
					return $result;
				}
			}
		}
	}
	
	
}
?>