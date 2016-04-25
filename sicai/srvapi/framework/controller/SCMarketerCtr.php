<?php
include_once "tools/tokenGen.php";
//include_once 'TeacherIntroduction.php';
//include_once "tools/defSqlInject.php";
include_once 'base/crudCtr.php';
class SCMarketerCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'oa_user_info';
	}

	//同城市的市场专员查询同城市的注册量
	public function queryRegister()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$sc_city=$newrow['sc_city'];
		//$data=$newrow['data'];
		date_default_timezone_set('PRC');
		$beginDate = $newrow ['begin_date'];
		$endDate = $newrow ['end_date'];
					
		if(! $newrow)
		{
			$msg->ResponseMsg ( 1, '日期和时间不能为空！', $result, 0, $prefixJS );
			
		}
		
		else 
		{
			if($newrow['sc_city'] && !$newrow['begin_date'] && !$newrow['end_date']){
				$querySql = 'select count(*) from sc_user_info s,user_info u
				where s.sc_city = u.user_city AND s.sc_city ="'.$sc_city.'"';
				$model = spClass ( $this->tablename );
				$result = $model->findSql ( $querySql );
				$msg->ResponseMsg ( 0, '成功',$result, 0, $prefixJS );
				return true;
			}
		}
		
		if(! $newrow)
		{
			$msg->ResponseMsg ( 1, '日期和时间不能为空！', $result, 0, $prefixJS );
		}
		else
		{
			if(!$newrow['sc_city'] && $newrow['begin_date'] && $newrow['end_date']){
				$querySql = "select count(*) from user_info u ,sc_user_info s where  create_time >='".$beginDate."' && create_time <= '".$endDate."'";
				// 					echo $querySql;
				// 					exit;
				$model = spClass($this->tablename);
				$result = $model->findSql($querySql);
				$msg->ResponseMsg(0, '成功', $result, 0, $prefixJS);
				return true;
			}
				
		}
		
		if(! $newrow)
		{
			$msg->ResponseMsg ( 1, '日期和时间不能为空！', $result, 0, $prefixJS );
		}
		else 
		{
			$querySql = "select count(*) from user_info u ,sc_user_info s where  u.create_time >='".$beginDate."' && u.create_time <= '".$endDate."'"."AND s.sc_city ='".$sc_city."'";
// 					echo $querySql;
// 					exit;
			$model = spClass($this->tablename);
			$result = $model->findSql($querySql);
			$msg->ResponseMsg(0, '成功', $result, 0, $prefixJS);
			return true;
		}
		 
}			
		
	//修改市场专员的信息
	function updateMarketer()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// $updateSql='update teacher_info set ';
		$sc_token=$newrow['sc_token'];
 		$tid = $newrow ['tid'];
		unset($newrow ['tid']);
		//$newrow [1] = 1;
		$querySql="select tid,sc_token from sc_user_info where tid='".$tid."'";
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		//判断条件
		if (  $result['sc_token'] == $sc_token && $newrow)
		{
			$updateSql = 'update sc_user_info set ';
			foreach ( $newrow as $k => $v )
			{
				if ($k == 'tid')
				{
					continue;
				}
				$updateSql = $updateSql . $k . '="' . $v . '",';
// 				echo $updateSql;
// 				exit;
			}
			$updateSql = substr ( $updateSql, 0, strlen ( $updateSql ) - 1 );
			$model = spClass ( $this->tablename );
			$tidsql = ' where tid=' . $tid;
			$updateSql = $updateSql . $tidsql;
			$result = $model->runSql ( $updateSql );
			$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixJS );
		}
		else
		{
			$msg->ResponseMsg ( 1, '权限不足', false, 0, $prefixJS );
		}
	
		return true;
	}
	#查询市场专员
	 function queryMarketer()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$querySql='select sc_token from sc_admin_info ';
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		$sc_token=$newrow['sc_token'];
			
		if (! $newrow && $sc_token==$result['sc_token'])
		{
			$querySql = 'select tid,sc_name,sc_name_en,sc_sex,sc_age,sc_city,sc_district,sc_town,sc_telephone,sc_image from sc_user_info' ;
			// 				echo $querySql;
			// 				exit;
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixJS );
		}
		// 			echo 11;
		// 			exit;
		elseif($sc_token==$result['sc_token'])
		{
			$querySql = 'select * from sc_user_info where ';
			foreach ( $newrow as $k => $v )
			{
				$querySql = $querySql . $k . '="' . $v . '" and ';
			}
			$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
			// 			 echo $querySql;
			// exit;
	
	
			if ($result = $model->findSql ( $querySql ))
			{
				$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixJS );
			}
			else
			{
				$msg->ResponseMsg ( 1, '此教师不存在', $result, 0, $prefixJS );
			}
		}
	
		return true;
	}

	
	#老师第一次登陆修改密码
	function updatePwdFirst (){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid=$newrow['tid'];
	 	$pwd=$newrow['pwd'];
	 	if($pwd==null){
	 		$msg->ResponseMsg ( 1, '请输入密码！', false, 0, $prefixJS );
	 		
	 	}else{
	 		
	 		$updateSql ="update sc_user_info set login_state=1 , sc_pwd=".$pwd." where tid=".$tid;
	 		$model = spClass ( 'sc_user_info' );
	 		$result = $model->runSql ( $updateSql );
	 		$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixJS );
	 	}
	}
	// 产生token
	function produceToken($len = 8)
	{
		$tokenTxt = $this->randomkeys ( $len );
		//echo $tokenTxt;
	
		// 这里的$tokenTxt不是token，是token的源字符串，系统默认自动生成一个8位的随机数做为token的源。
		$token = tokenGen::encrypt ( $tokenTxt );
		//echo '$token='.$token."<br>";
		# start $token里会出现特殊符号，下面将特殊符号替换为数字或字母
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		//$token = strtr($token, "+", "A");
		//$token = '@$^&#$%&$#%**$@#$%^&*()==';
		for($i; $i<strlen($token); $i++)
		{
			$ord_token = ord($token{$i});
			if(!( ($ord_token>=48 && $ord_token<=57) || ($ord_token>=65 && $ord_token<=90) || ($ord_token>=97 && $ord_token<=122) || ($ord_token==61) )){
				//echo '$token1='.$token{$i}."<br>";
				//echo '-----'.ord($token{$i})."<br>";
				$token{$i} = $pattern {mt_rand ( 0, 35 )};
				//echo '$token2='.$token{$i}."<br>";
			}
		}
		# end
		//echo '$token转化后='.$token."<br>";
		$model = spClass ( "sc_info" );
		$sum = $model->findCount ( array (
				'sc_token' => $token
		) );
		//echo '$sum='.$sum."<br>";
		//exit;
		if ($sum > 0)
		{
			return strtr($this->produceToken ( $len ), "+", "A");
		} else
			return $token;
	}
	
	// 产生定长的随机数
	function randomkeys($length)
	{
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++)
		{
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
	// 登录
	function testToken()
	{
		$token = "VToCa1p+VzJcfQA+CmYDPw==";
		echo strtr($token, "+", "A");
		//$msg->ResponseMsg ( 1, "请获取验证码", $result, 1, $prefixJS );
	
	}
	
	// 	//生成MD5密文
	// 	function cipher($login_pwd)
	// 	{
	// 		//echo $login_pwd.'----';
	// 		//得到数据的密文
	// 		$login_pwd=md5($login_pwd);         // echo $login_pwd.'---';
	// 		//再把密文字符串的字符顺序调转
	// 		$login_pwd = strrev($login_pwd);    // echo $login_pwd.'---';
	// 		//再进行一次MD5运算并返回
	// 		$login_pwd=md5($login_pwd);         // echo $login_pwd.'---';
	// 		//将返回后的字符串循环十次
	// 		$times=11;
	// 		for ($i = 0; $i < $times; $i++) {
	// 			$login_pwd = md5($login_pwd);
	// 		}
	// 		//echo $login_pwd.'---';
	// 		//再把密文字符串的字符顺序调转
	// 		$login_pwd = strrev($login_pwd);      //echo $login_pwd.'---';
	// 		//最后再进行一次MD5运算并返回
	// 		$login_pwd=md5($login_pwd);
	// 		//         echo $login_pwd;
	// 		//         exit;
	// 		//echo $login_pwd.'----';
	// 		return $login_pwd;
	//  	}
	}
	