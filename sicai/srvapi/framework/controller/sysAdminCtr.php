<?php
		include_once "tools/tokenGen.php";
		include_once 'base/crudCtr.php';
		class sysAdminCtr extends crudCtr{
		public function __construct(){
				$this->tablename = 'oa_user_info';
	}
	#增加city
	public function addCity($tid)
	{
			
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		if(!$newrow)
		{
			$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
		}
	
		else
		{	
			$addSql = "insert area_list set ";

			foreach ( $newrow as $k => $v )
			{
				$addSql = $addSql . $k . '="' . $v . '",';
			}
			$addSql = substr ( $addSql, 0, strlen ( $addSql ) -1 );
	
// 			 				echo $addSql;
// 			 				exit;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $addSql );
// 			 echo $result['0']['tid'];
// 			 exit;
			//$tid=$result;
			if ($result <= 0)
			{
				return;
			}
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		}
	
		return true;
	}
	
	
	#修改手机号
		function updateTel()
		{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$telephone=$newrow['telephone'];
		$type=$newrow['type'];
		if($type=='JY'){
			$updateSql="update jy_user_info set where jy_telephone= ".$telephone;
			echo $updateSql;
			exit;
		}
		}
	//添加教研员
		
		 function addJy($tid)
	 {
	$msg = new responseMsg ();
	$capturs = $this->captureParams ();
	$prefixJS = $capturs ['callback'];
	$token = $capturs ['token'];
	$newrow = $capturs ['newrow'];
	$jy_city=$newrow['jy_city'];
	$jy_telephone=$newrow['jy_telephone'];
	$jy_pwd=$newrow['jy_pwd'];
// 	echo 22;
// 	exit;
	if( $jy_telephone==null ||$jy_pwd==null)
	{
	$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
	}
	
	else
	{
		
	$newrow ['jy_token'] = $this->produceToken (); //自动生成token
    //$login_pwd=$newrow ['login_pwd'];
    $model = spClass ( jy_user_info );
	$addSql = 'insert  jy_user_info set ';
// 	echo $addSql;
// 	exit;
	foreach ( $newrow as $k => $v )
	{
	   $addSql = $addSql . $k . '="' . $v . '",';
	}
	   $addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
	

	$result = $model->runSql ( $addSql );
	$querySql='';
	//echo $model->dumpSql();
	$tid=$result;
	if ($result <= 0)
	{
	return;
	}
	else 
	{
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
	}
	}
	
	return true;
	}
	// 产生token
	private function produceToken($len = 8)
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
		$model = spClass ( "jy_user_info" );
		$sum = $model->findCount ( array (
				'jy_token' => $token
		) );
		//echo '$sum='.$sum."<br>";
		//exit;
		if ($sum > 0)
		{
			return strtr($this->produceToken ( $len ), "+", "A");
		} else
			return $token;
		// 		echo $token;
		// 		exit;
	
	}
	
	// 产生定长的随机数
	private function randomkeys($length)
	{
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++)
		{
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
	}
		