<?php
		include_once "tools/tokenGen.php";
		include_once 'base/crudCtr.php';
		include_once 'base/checkCtr.php';
		
		class SCManageCtr extends crudCtr{
		public function __construct(){
				$this->tablename = 'oa_user_info';
	}
	  
		
	#查询全国市场专员
	public function queryAllMarketer()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$kf_token=$newrow['token'];
		$verify = new checkCtr();
		$result = $verify->VerifyAuth("kf_token",$token,"kf_user_info");
		if($tid==null){
			$msg->ResponseMsg ( 0, 'tid不能为空', 1, 0, $prefixJS );
			return ;	
		}
		$sc_token=$newrow['sc_token'];
// 		获取市场管理员的token
		$querySql='select sc_token from sc_admin_info where tid='.$tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($sc_token==null || $sc_token != $result[0]['sc_token']){
			$msg->ResponseMsg ( 1, '您没有权限', 1, 0, $prefixJS );
				return ;
		}
			unset($newrow['sc_token']);
		 
		if (! $newrow && $sc_token==$result[0]['sc_token'])
		{
			$querySql = 'select tid,sc_name,sc_name_en,sc_sex,sc_age,sc_city,sc_district,sc_town,sc_telephone,sc_image from sc_user_info' ;
			// 				echo $querySql;
			// 				exit;
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
		}
		// 			echo 11;
		// 			exit;
		elseif($sc_token==$result[0]['sc_token'])
		{
			$querySql = 'select tid,sc_name,sc_name_en,sc_sex,sc_age,sc_city,sc_district,sc_town,sc_telephone,sc_image  from sc_user_info where ';
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
				$msg->ResponseMsg ( 1, '此教师不存在，请调整搜索条件', $result, 0, $prefixJS );
			}
		}
	
		return true;
	}
	#删除市场专员
	function deleteMarketer()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$newrow [1] = 1;
		
		if($tid==null)
		{
			$msg->ResponseMsg ( 1, 'not find the object', $result, 0, $prefixJS );
		}
		else
		{
			$querySql='select sc_token from sc_user_info where tid='.$tid;
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			$sc_token=$newrow['sc_token'];
			if ( $sc_token==$result['$sc_token'])
			{
				$delSql = 'delete from sc_user_info where tid=' . $tid;
				// echo $delSql;
				// exit;
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $delSql );
				$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixJS );
					
			}
			else
			{
					
				$msg->ResponseMsg ( 1, '权限不足，不能继续操作！', $result, 0, $prefixJS );
					
			}
		}
		return true;
	}

	#查询全国注册量
	
	public function queryAllRegister()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$city=$newrow['city'];
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
			if($newrow['city'] && !$newrow['begin_date'] && !$newrow['end_date']){
				$querySql = 'select count(*) from  user_info where user_city="'.$city.'"' ;
// 				echo $querySql;
// 				exit;
				$model = spClass ( $this->tablename );
				$result = $model->findSql ( $querySql );
				$msg->ResponseMsg ( 0, '成功',$result, 0, $prefixJS );
				return true;
			}
		}
	
		if(! $newrow)
		{
			$msg->ResponseMsg ( 1, '日期和时间不能为空', $result, 0, $prefixJS );
		}
		else
		{
			if(!$newrow['city'] && $newrow['begin_date'] && $newrow['end_date']){
				$querySql = "select count(*) from  user_info  where  create_time >='".$beginDate."' && create_time <= '".$endDate."'";
				// 					echo $querySql;
				// 					exit;
				$model = spClass($this->tablename);
				$result = $model->findSql($querySql);
				$msg->ResponseMsg(0, 'sucess', $result, 0, $prefixJS);
				return true;
			}
	
		}
	
		if(! $newrow)
		{
			$msg->ResponseMsg ( 1, '日期和时间不能为空', $result, 0, $prefixJS );
		}
		else
		{
			$querySql = "select count(*) from user_info  where  create_time >='".$beginDate."' && create_time <= '".$endDate."'"."AND user_city ='".$city."'";
			// 					echo $querySql;
			// 					exit;
			$model = spClass($this->tablename);
			$result = $model->findSql($querySql);
			$msg->ResponseMsg(0, '成功', $result, 0, $prefixJS);
			return true;
		}
			
	}
	//添加市场专员
		
	  function addMarketer($tid)
	 {
	$msg = new responseMsg ();
	$capturs = $this->captureParams ();
	$prefixJS = $capturs ['callback'];
	$token = $capturs ['token'];
	$newrow = $capturs ['newrow'];
	$sc_city=$newrow['sc_city'];
	$sc_telephone=$newrow['sc_telephone'];
// 	$sc_pwd=$newrow['sc_pwd'];
	$sc_num=$newrow['sc_num'];
// 	echo 22;
// 	exit;
	if($sc_city==null || $sc_telephone==null  ||$sc_num==null)
	{
	$msg->ResponseMsg ( 1, '失败', null, 0, $prefixJS );
	}
	
	else
	{
		
	$newrow ['sc_token'] = $this->produceToken (); //自动生成token
    $model = spClass ( oa_user_info );
	$addSql = 'insert  oa_user_info set ';
	
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
		$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixJS );
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
		$model = spClass ( "sc_user_info" );
		$sum = $model->findCount ( array (
				'sc_token' => $token
		) );
// 		echo '$sum='.$sum."<br>";
// 		exit;
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
		