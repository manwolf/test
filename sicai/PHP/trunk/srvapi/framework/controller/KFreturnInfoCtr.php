<?php
include_once 'base/crudCtr.php';
include_once 'base/encrypt.php';
class KFreturnInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'kf_return_info';
	}
	/**
	 * 功能：回访记录基本的增加和查询
	 * 作者：陈梦帆
	 * 日期： 2015-08-31
	 */
	// 添加回访记录
	function addreturn($tid) {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
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
				$city = $result1 ['kf_city'];
			} else {
				$city = $result2 ['kf_admin_city'];
			}//添加信息不为空
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '添加失败', null, 0, $prefixJS );
			} else {
				// $newrow ['sc_token'] = $this->produceToken ();
				// $token=$newrow ['token'];
				// 插入表中信息，添加城市
				$addSql = 'insert kf_return_info set city = "' . $city . '" , ';
				
				foreach ( $newrow as $k => $v ) {
					if ($k == "tid") {
						continue;
					}
					
					// if($k == "city"){
					// $addSql = $addSql . $k . '="' . $city . '",';
					// }else{
					$addSql = $addSql . $k . '="' . $v . '",';
				}
				
				$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $addSql );
				
				if ($result <= 0) {
					return;
				}
				
				$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
			}
			return true;
		} else {//token验证失败
			$msg->ResponseMsg ( 1, "验证失败", $result, 0, $prefixJS );
			return;
		}
	}
	/**
	 * 查询回访记录
	 */
	function queryAllreturn() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// token验证
		if ($token == null) { // token不能为空
			$msg->ResponseMsg ( 1, ' 令牌不能为空！ ', false, 0, $prefixKF );
			exit ();
		}
		// 查询客服专员信息表（kf_user_info）中是否有该token
		$model = spClass ( 'kf_user_info' );
		$result_user = $model->findBy ( 'kf_token', $token );
		// 查询客服管理员表（kf_admin_info）中是否有该token
		$model = spClass ( 'kf_admin_info' );
		$result_admin = $model->findBy ( 'kf_admin_token', $token );
		// 如果两个表中都没有该token，则返回 token验证失败，并停止函数运行
		if (! ($result_user ['kf_token']) && ! ($result_admin ['kf_admin_token'])) {
			$msg->ResponseMsg ( 1, "token验证失败", $result, 0, $prefixJS );
			return;
		}
		// 如果token存在于客服专员表中，则将$result_user['kf_city']赋值给$kf_city
		// 如果token存在于客服管理员表中，则将$result_user['kf_admin_city']赋值给$kf_city
		if ($token == $result_user ['kf_token']) {
			$kf_city = $result_user ['kf_city'];
		} else {
			$kf_city = $result_admin ['kf_admin_city'];
		}
		// 左联查询 回访记录表和订单表 （kf_return_info k Left Join order_list o）
		$querySql = "select * from kf_return_info 
						where city=\"{$kf_city}\"";
		// 去掉$newrow数组中kf_tid的键和值
		unset ( $newrow ['kf_tid'] );
		unset ( $newrow ['page'] );
		// 如果$newrow数组中有值，则在$querySql加入条件限制
		foreach ( $newrow as $k => $v ) {
			$querySql .= " and {$k} = \"{$v}\"";
		}
		// 如果查询成功，则进行分页，并返回数据 | 如果没有查询到数据，则返回没有此记录
		$result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql );
		if ($result) { // $result = $model->findSql ( $querySql )
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page'];
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '没有此记录', $result, 0, $prefixJS );
		}
		return true;
	}
	// 回访记录接口值
	function queryreturn() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		if ($token == null) {//token不为空
			$msg->ResponseMsg ( 1, ' 令牌不能为空！ ', false, 0, $prefixKF );
			exit ();
		}//验证客服专员token
		$model = spClass ( 'kf_user_info' );
		$result1 = $model->findBy ( 'kf_token', $token );
		//验证客服主管token
		$model = spClass ( 'kf_admin_info' );
		$result2 = $model->findBy ( 'kf_admin_token', $token );
		//token存在则根据被回访人的姓名和联系方式查询回访次数
		if ($token == $result1 ['kf_token'] or $token == $result2 ['kf_admin_token']) {
			
			$querysql = 'select count(tid) from kf_return_info 
			where return_name = "' . $newrow ['return_name'] . '"
			  and return_phone = "' . $newrow ['return_phone'] . '"';
			
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querysql )) {
				$msg->ResponseMsg ( 0, '查找成功', $result, 0, $prefixJS );
			} else {//查无此人
				$msg->ResponseMsg ( 1, '没找到此记录', $result, 0, $prefixJS );
			}
		} else {//验证token不存在
			$msg->ResponseMsg ( 1, "验证失败", $result, 0, $prefixJS );
			return;
		}
	}
	// 通过回访次数查询回访记录
	function queryReturnByVisitTimes() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		// print_r($capturs);
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		//验证token
		if ($token == null) {//token不为空
			$msg->ResponseMsg ( 1, ' 令牌不能为空！ ', false, 0, $prefixKF );
			exit ();
		} // 查询客服专员信息表（kf_user_info）中是否有该token
		$model = spClass ( 'kf_user_info' );
		$result1 = $model->findBy ( 'kf_token', $token );
		// 查询客服管理员表（kf_admin_info）中是否有该token
		$model = spClass ( 'kf_admin_info' );
		$result2 = $model->findBy ( 'kf_admin_token', $token );
		
		if ($token == $result1 ['kf_token'] or $token == $result2 ['kf_admin_token']) {
			// 查询回访记录表（kf_return_info）中所有联系方式
			$Sql = 'select distinct return_phone from kf_return_info';
			$model = spClass ( 'kf_return_info' );
			$return_phone_array = $model->findSql ( $Sql );
			foreach ( $return_phone_array as $k => $v ) {
				//查询出回访次数
				$Sql = "select count(tid) as count from kf_return_info where return_phone ='" . $v ['return_phone'] . "'";
				$model = spClass ( 'kf_return_info' );
				$count_array = $model->findSql ( $Sql );
				//查询回访次数小于等于4次的回访记录
				if (($newrow ['return_count'] == $count_array [0] ['count']) && ($newrow ['return_count'] <= 4)) {
					$Sql = "select * from kf_return_info where return_phone =\"{$v ['return_phone']}\"";
					$model = spClass ( 'kf_return_info' );
					$user_array = $model->findSql ( $Sql );
					foreach ( $user_array as $k => $v ) {
						$user_array [$k] ['return_number'] = $count_array [0] ['count'];
					}
					foreach ( $user_array as $k => $v ) {
						$result [] = $user_array [$k];
					}
				}  //查询回访次数大于等于5次的回访记录
				if (($count_array [0] ['count'] >= 5) && ($newrow ['return_count'] >= 5)) {
					$Sql = "select * from kf_return_info where return_phone =\"{$v ['return_phone']}\"";
					$model = spClass ( 'kf_return_info' );
					$user_array = $model->findSql ( $Sql );
					foreach ( $user_array as $k => $v ) {
						$user_array [$k] ['return_number'] = $count_array [0] ['count'];
					}
					foreach ( $user_array as $k => $v ) {
						$result [] = $user_array [$k];
					}
				}
			}
			# 求出总页数
			$total_page = (int)( count($result)/10 )+1;
			# 根据tid升序排序
			for($i = 0; $i < count ( $result ) - 1; $i ++) {
				for($j = 0; $j < count ( $result ) - 1 - $i; $j ++) {
					if ( ($result[$j]['tid']) >= ($result[$j + 1]['tid']) ) {
						$translation = $result [$j];
						$result [$j] = $result [$j + 1];
						$result [$j + 1] = $translation;
					}
				}
			}
			# 根据页码进行分页
			for( $i= ($newrow['page']-1)*10 ; $i < ($newrow['page'])*10 ; $i++){
				if(!$result[$i]){
					continue;
				}
				$result_paging[] = $result[$i];
			}
			# 将最终结果复制给$result
			$result = $result_paging;
			$msg->ResponseMsg ( 0, '查找成功', $result, $total_page, $prefixJS );
		} else {  // 如果两个表中都没有该token，则返回 token验证失败，并停止函数运行
			$msg->ResponseMsg ( 1, "验证失败", $result, 0, $prefixJS );
			return;
		}
	}
}