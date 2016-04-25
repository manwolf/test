<?php
		
		include_once 'base/crudCtr.php';
		class queryCityCtr extends crudCtr{
		public function __construct(){
				$this->tablename = 'area_list';
	}
	#增加城市公共接口
		public function queryCity() 
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		
		
		if (! $newrow ) 
		{
			$querySql = 'select * from area_list';
			
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		} 
		
		else
		{
			$querySql = 'select distinct area_city from area_list where ';
			foreach ( $newrow as $k => $v ) 
			{
				$querySql = $querySql . $k . '="' . $v . '" and ';
		    }
			$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
// 			 echo $querySql;
// 			exit;			
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) 
			{
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			
			} 
			
			else 
				
			{
				$msg->ResponseMsg ( 1, 'the city is not find', $result, 0, $prefixJS );
			}
		}
		
		
	} 
		}