<?php
include_once 'base/crudCtr.php';
class versionInfo extends crudCtr {
	public function __construct() {
		$this->tablename = 'version_info';
	}	
	//未使用
	function queryVersionInfo()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
        
		if(!$newrow)
		{
			$msg->ResponseMsg ( 1, 'the version_number is not find', null, 0, $prefixJS );			
		}
		else
		{
			$querySql = 'select * from version_info where ';
			foreach ( $newrow as $k => $v )
			{
				$querySql = $querySql . $k . '="' . $v . '" and ';
			}
			$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
// 			echo $querySql;
// 			exit;
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql ))
			{
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			}
			else
			{
				$msg->ResponseMsg ( 1, 'the version_number is not find', null, 0, $prefixJS );
			}
		}		
		return true;
	}
}