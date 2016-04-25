<?php
include_once 'base/crudCtr.php';
/**
 * 功能：根据城市 获取各城市课程单价
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class cityUnitPriceCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'class_price';
	}
	//录入城市年级信息   获取城市自动分为12年级  暂未使用
	 function addCity()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$class_city=$newrow['class_city'];
		if(!$newrow['class_city']){
			return false;
		}
		//判断该城市是否已存在
		$querySql = 'select count(class_city) as num from class_price where class_city="'.$class_city.'"';
        $model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
// 		print_r($result['0']['num']);
// 		exit;
		if($result['0']['num']>0)
		{
			$msg->ResponseMsg ( 1, '这座城市已存在！', $result, 0, $prefixJS );
		}
		else
		{
			//若无此城市则存入（12年级）
			for($i=1;$i<=12;$i++)
			{
				
			$addSql = 'insert  class_price set class_city="'.$class_city.'",'.'class_grade='.$i;
			
			
			//  	 echo $addSql;
			// 		  exit;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $addSql );
			$querySql='';
			}
			// echo $model->dumpSql();
			//$tid=$result;
			if ($result <= 0)
			{
				return;
			}
			
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			}	
		
		return true;
		
	}
	    //按城市查询当地年级划分和年级单价
	    function queryCity() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$class_city=$newrow['class_city'];
				
		if (!$newrow) {
			
			$msg->ResponseMsg ( 1, 'Unable to get the city', $result, 0, $prefixJS );
			exit;
		} 
		else
		{	//order by 进攻 desc
		   // $querySql = 'select * from class_price where class_city="'.$class_city.'"';
		    $querySql = 'select * from class_price  where class_city="'.$class_city.'" ';
		   
            $model = spClass ( $this->tablename );
			if($result = $model->findSql ( $querySql ))
			{
				$msg->ResponseMsg ( 0, 'Success', $result, 0, $prefixJS );
				
			}
			else
			{
				$msg->ResponseMsg ( 0, 'not found', $result, 0, $prefixJS );
				
			}
			
		}
		return true;
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
