<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';

class AlertCtr extends crudCtr {
	
	//有订单进来提示
	public function alert() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
// 		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$querySql='select  count(tid)as count from order_list  ';
		$model = spClass ( 'order_list' );
		$result = $model->findSql ( $querySql);
		
		$msg->ResponseMsg ( 0, '', $result, 0, $prefixJS );
		}
	
	
//页面跳转承上启下过渡作用
	function pageJumps(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// 		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$msg->ResponseMsg ( 0, 'ture', true, 0, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
				
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
}
	
	