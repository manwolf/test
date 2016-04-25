<?php
include_once 'base/crudCtr.php';
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
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$potential_tel = $newrow ['potential_phone'];
		// 验证token
		if ($token == null) {
			$msg->ResponseMsg ( 1, ' 令牌不能为空！ ', false, 0, $prefixKF );
			exit ();
		} // 查询客服专员的token
		$model = spClass ( 'kf_user_info' );
		$result1 = $model->findBy ( 'kf_token', $token );
		// 查询客服主管的token
		$model = spClass ( 'kf_admin_info' );
		$result2 = $model->findBy ( 'kf_admin_token', $token );
		if (! $newrow) { // token不为空
			$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
		} else {
			if ($token == $result1 ['kf_token'] or $token == $result2 ['kf_admin_token']) {
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
							
							$addSql = $addSql . $k . '="' . $v . '",';
						}
						
						$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
						
						$model = spClass ( $this->tablename );
						$result = $model->runSql ( $addSql );
						
						if ($result <= 0) {
							return;
						}
						
						$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
					}
				}
			}
		}
	}
	// 修改潜在客户状态，"0"为已转化；“1”为未转化 默认值为未转化
	function state() {
		// echo aaa;
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		// 验证token
		if ($token == null) {
			$msg->ResponseMsg ( 1, ' 令牌不能为空！ ', false, 0, $prefixKF );
			exit ();
		} // 验证客服专员token
		$model = spClass ( 'kf_user_info' );
		$result1 = $model->findBy ( 'kf_token', $token );
		// 验证客服主管token
		$model = spClass ( 'kf_admin_info' );
		$result2 = $model->findBy ( 'kf_admin_token', $token );
		
		if (! ($token == $result1 ['kf_token']) && ! ($token == $result2 ['kf_admin_token'])) {
			$msg->ResponseMsg ( 1, '验证失败', null, 0, $prefixJS );
			exit ();
		}
		
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
		} else {
			// $newrow ['sc_token'] = $this->produceToken ();
			// $token=$newrow ['token'];
			
			$updateSql = 'update kf_potential_info set potential_change = 1 where tid = ' . $tid;
			
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $updateSql );
			
			// $updateSql='update kf_potential_info set ';
			// //$tid = $newrow ['tid'];
			// //$newrow [1] = 1;
			// $updateSql='select potential_change from kf_potential_info where tid='.$tid;
			// $model = spClass ( $this->tablename);
			// $result = $model->findSql ( $querySql );
			
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		}
		return true;
	}
	
	// 查询潜在客户信息
	function queryAllPotential() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$potential_number = $newrow ['potential_number'];
		unset ( $newrow ['page'] );
		$startdate = $newrow ['start_date'];
		$enddate = $newrow ['end_date'];
		$kf_tid = $newrow ['kf_tid'];
		// $potential_register=$newrow['potential_register'];
		// unset ( $newrow ['start_date']);
		// unset ( $newrow ['end_date']);
		/*
		 * $querySql="select kf_city from kf_user_info where tid=".$kf_tid;
		 * $model = spClass ( 'kf_user_info' );
		 * $result=$model->findSql($querySql);
		 */
		
		unset ( $newrow ['kf_tid'] );
		// token验证
		if ($token == null) {
			$msg->ResponseMsg ( 1, ' 令牌不能为空！ ', false, 0, $prefixKF );
			exit ();
		}
		$model = spClass ( 'kf_user_info' );
		$result1 = $model->findBy ( 'kf_token', $token );
		$model = spClass ( 'kf_admin_info' );
		$result2 = $model->findBy ( 'kf_admin_token', $token );
		
		if ($token == $result1 ['kf_token'] or $token == $result2 ['kf_admin_token']) {
			
			if ($token == $result1 ['kf_token']) {
				$kf_city = $result1 ['kf_city'];
			} else {
				$kf_city = $result2 ['kf_admin_city'];
			}
			// 根据潜在客户的姓名查询信息
			$querySql = 'select potential_name from kf_potential_info ';
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			$potential_name = $newrow ['potential_name'];
			// # 判断潜在客户是否已注册
			// $querySql = "select telephone from user_info where telephone=\"{$result ['potential_phone']}\"";
			// $model = spClass ( $this->tablename );
			// $result = $model->findSql ( $querySql );
			if (! $newrow) {
				// 根据城市查询信息
				$querySql = 'select * from kf_potential_info where potential_city="' . $kf_city . '"';
				// echo $querySql;
				// exit;
				$result = $model->findSql ( $querySql );
				$result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$msg->ResponseMsg ( 0, 'success', $result, $total_page, $prefixJS );
			} else {
				if ($newrow ['start_date'] && $newrow ['end_date']) {
					// 根据日期查询信息，该城市某段时间内的所有信息
					$querySql = "select * from  kf_potential_info  where  potential_city='" . $kf_city . "'" . "and potential_date >='" . $startdate . "'" . "and potential_date <='" . $enddate . "'";
					// echo $querySql;
					// exit;
					$model = spClass ( $this->tablename );
					$result = $model->findSql ( $querySql );
					$result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					$msg->ResponseMsg ( 0, 'success', $result, $total_page, $prefixJS );
					return true;
				}
				// 根据潜在客户的联系方式查询出不同的结果
				if (! $newrow && $potential_phone == $result ['potential_phone']) {
					$querySql = 'select * from kf_potential_info';
					
					// echo $querySql;
					// exit;
					$result = $model->findSql ( $querySql );
					$result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					$msg->ResponseMsg ( 0, 'success', $result, $total_page, $prefixJS );
				} 

				elseif ($potential_phone == $result ['potential_phone']) {
					$querySql = 'select * from kf_potential_info where ';
					foreach ( $newrow as $k => $v ) {
						if ($newrow ['potential_number'] >= 5) {
							$querySql = $querySql . $k . '>="' . $v . '" and ';
							continue;
						}
						$querySql = $querySql . $k . '="' . $v . '" and ';
					}
					$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
					if ($result = $model->findSql ( $querySql )) {
						$page = $newrow ['page'] ? $newrow ['page'] : 1;
						$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
						$pager = $model->spPager ()->getPager ();
						$total_page = $pager ['total_page'];
						$msg->ResponseMsg ( 0, 'success', $result, $total_page, $prefixJS );
					} else {
						$msg->ResponseMsg ( 1, '查无该条件信息，请重新查询。', $result, 0, $prefixJS );
					}
				}
				return true;
			}
		}
	}
}