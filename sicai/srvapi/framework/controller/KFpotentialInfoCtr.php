<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
class KFpotentialInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'kf_potential_info';
	}
	/**
	 * 功能：回访记录基本的增加和查询
	 * 作者：陈梦帆
	 * 日期： 2015-08-31
	 */
	// 新增潜在客户信息
	function addpotential($tid) {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			// $city=$result[0]['city'];
			$potential_tel = $newrow ['potential_phone'];
			
			if (! $newrow) { // 添加信息不为空
				$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
			} else {
				// 判断潜在客户是否已注册，“0”为未注册，“1”为已注册
				$query = 'select count(telephone) as num from user_info where telephone=' . $potential_tel;
				$model = spClass ( $this->tablename );
				$result = $model->findSql ( $query );
				if ($result [0] ['num'] >= 1) { // 在user_info表里查到该手机号则表示该用户已注册，添加潜在客户信息将potential_register字段判定为“1”
					$addSql = 'insert kf_potential_info set potential_register = 1,';
					
					foreach ( $newrow as $k => $v ) {
						if ($k == 'user_tid') {
							continue;
						}
						$addSql = $addSql . $k . '="' . $v . '",';
					}
					
					$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
					
					$model = spClass ( $this->tablename );
					$result = $model->runSql ( $addSql );
					
					if ($result <= 0) {
						return;
					}
					
					$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
				} else { // 如该手机号在user_info表中不存在，则直接添加潜在客户信息
					$addSql = 'insert kf_potential_info set ';
					
					foreach ( $newrow as $k => $v ) {
						if ($k == 'user_tid') {
							continue;
						}
						$addSql = $addSql . $k . '="' . $v . '",';
					}
					
					$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
					
					$model = spClass ( $this->tablename );
					$result = $model->runSql ( $addSql );
					
					if ($result <= 0) {
						return;
					}
					$verify = new checkCtr ();
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
				}
			}
		} else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限', $result, 0, $prefixJS );
		}
	}
	
	// 修改潜在客户状态，"0"为已转化；“1”为未转化 默认值为未转化
	function state() {
		// echo aaa;
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			// $city=$result[0]['city'];
			$tid = $newrow ['tid'];
			if (! $newrow) {
				$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
			} else {
				// $newrow ['sc_token'] = $this->produceToken ();
				// $token=$newrow ['token'];
				
				$updateSql = 'update kf_potential_info set potential_change = 1 where tid = ' . $tid;
				
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			}
			return true;
		} else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限', 1, 0, $prefixJS );
		}
	}
	// 查询潜在客户信息
	function queryAllPotential() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		$potential_name = $newrow ['potential_name'];
		$potential_number = $newrow ['potential_number'];
		$potential_city = $newrow ['potential_city'];
		$potential_phone = $newrow ['potential_phone'];
		$potential_change = $newrow ['potential_change'];
		$startdate = $newrow ['start_date'];
		$enddate = $newrow ['end_date'];
		$page = $newrow ['page'];
		unset ( $newrow ['page'] );
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			
			$city = $result [0] ['city'];
		}
		else{
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 1, $prefixJS );
			exit;
		}
			if ($user_tid == null || $token == null) {
				$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 1, $prefixJS );
				exit;
			} 
			if (! $newrow && $city == '全国'){
				$querySql = 'select * from kf_potential_info where 1=1';
				$model = spClass ( $this->tablename );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$verify = new checkCtr ();
				$results = $verify->record ();
				if ($result) {
					$msg->ResponseMsg ( 0, '查询成功！', $result, $total_page, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, '查询失败，此用户不存在！', $result, 0, $prefixJS );
				}
			}
				else{
			
				$querySql = 'select * from kf_potential_info where 1=1 and potential_city="'.$city.'"';
			
			
				
				if ($potential_name != '') {
					// 根据潜在客户的姓名查询信息
					$querySql = $querySql . ' and potential_name="' . $potential_name . '"';
				}
// 				if ($potential_city != '' && $potential_city != null) {
// 					// 根据城市查询信息
// 					$querySql .= ' and  potential_city = "' . $potential_city . '"';
// 				}
				if ($newrow ['start_date'] && $newrow ['end_date']) {
					// 根据日期查询信息，该城市某段时间内的所有信息
					$querySql .= ' and potential_date >= "' . $startdate . '"  
									and potential_date <= "' . $enddate . '"';
				}
				if ($potential_phone != '') {
					// 根据潜在客户的联系方式查询出不同的结果
					$querySql = $querySql . " and potential_phone='{$potential_phone}'";
				}
				if ($potential_change != '') {
					// 根据潜在客户的转化状态查询出不同的结果
					$querySql = $querySql . " and potential_change='{$potential_change}'";
				}
				if ($potential_number != '' && $potential_number< 5) {
					// 根据潜在客户的回访次数查询出不同的结果
					$querySql = $querySql . " and potential_number='{$potential_number}'";
				}
				// 查询回访次数大于等于5次的信息
				if ($potential_number != '' && $newrow ['potential_number'] >= 5) {
					$querySql .= ' and  potential_number >= ' . $potential_number;
				}
				$model = spClass ( $this->tablename );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$verify = new checkCtr ();
				$results = $verify->record ();
				if ($result) {
					$msg->ResponseMsg ( 0, '查询成功！', $result, $total_page, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, '查询失败，此用户不存在！', $result, 0, $prefixJS );
				}
			
				}
	}
}
			