<?php
header ( "Content-Type:application/json;charset=utf-8" );
include_once 'responseMsg.php';
include_once 'index.php';
class captureparam extends spController {
	function captureParams() 
	{		
		$tablename = '';
		$arr = $this->spArgs ();   
		
		$newrow = array ();
		$action = '';
		
		$prefixJS = '';
		
		$token = ''; // 用于解析token参数。但并非所有接口都需要token参数
		
		$pagenum = '';
		$pagesize = '';
		# 函数mysql_real_escape_string() 需要先连接到数据库才能使用，故先连接数据库
		$conn = @mysql_connect('localhost','root','sicai',0) or die(mysql_error());
		foreach ( $arr as $k => $v ) {
			switch ($k) {
				case 'c' :
					continue;
				
				case 'a' :
					$action = $v;
					continue;
				
				case 'callback' :
					$prefixJS = $v;
					break;
				
				case 'token' :
					$token = $v;
					break;
				
				case 'tablename' :
					$tablename = $v;
					break;
					
				case 'ETTimeStamp' :
						continue;
						
				default :
					
					if (('' != $v)) {
						# 运用mysql_real_escape_string()函数，过滤到Sql注入的常用字符（在常用字符前加反斜杠'\'）
						$newrow[$k] = mysql_real_escape_string($v);
					}
			}
		}
		# 及时的关闭对数据库的连接
		mysql_close ( $conn );
		return array (
				"callback" => $prefixJS,
				"token" => $token,
				"newrow" => $newrow,
				'tablename' => $tablename,
				'pagenum' => $pagenum,
				'pagesize' => $pagesize,
				//'testrow' =>  $testrow
		);
	}
	function captureUpdateParams() {
		$arr = $this->spArgs ();
		$newrow = array ();
		$prefixJS = '';
		$tablename = '';
		$token = '';
		# 函数mysql_real_escape_string() 需要先连接到数据库才能使用，故先连接数据库
		$conn = @mysql_connect('localhost','root','sicai',0) or die(mysql_error());
		foreach ( $arr as $k => $v ) {
			switch ($k) {
				case 'c' :
					continue;
				
				case 'a' :
					continue;
				
				case 'callback' :
					$prefixJS = $v;
					break;
				
				case 'token' :
					$token = $v;
					break;
				
				case 'tablename' :
					$tablename = $v;
					
					break;
				
				case 'tid' :
					$condition = array (
							'tid' => $v 
					);
					break;
					
				case 'ETTimeStamp' :
					continue;
					
				default :
					
					if (('' != $v)) {
						# 运用mysql_real_escape_string()函数，过滤到Sql注入的常用字符（在常用字符前加反斜杠'\'）
						$newrow[$k] = mysql_real_escape_string($v);
					}
			}
		}
		# 及时的关闭对数据库的连接
		mysql_close ( $conn );
		return array (
				"callback" => $prefixJS,
				"token" => $token,
				"newrow" => $newrow,
				'tablename' => $tablename,
				'condition' => $condition 
		);
	}
}

