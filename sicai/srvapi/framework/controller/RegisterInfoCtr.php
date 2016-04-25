<?php
include_once 'base/crudCtr.php';
class RegisterInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'register_info';
		
	}
	#增加注册信息
	function addRegister($tid)
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
	$model = spClass ( $this->tablename );
	$addSql = 'insert  register_info set ';
	foreach ( $newrow as $k => $v )
	{
	   $addSql = $addSql . $k . '="' . $v . '",';
	}
	$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
	
// 	  echo $addSql;
// 		 exit;
	
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
}