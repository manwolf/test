<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			unset ( $newrow ['user_tid'] );
			
			$city = $result [0] ['city']; // 添加信息不为空
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
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
			}
			return true;
		} else { // token验证失败
			$msg->ResponseMsg ( 1, "对不起，您没有权限！", 1, 0, $prefixJS );
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			
			// 左联查询 回访记录表和订单表 （kf_return_info k Left Join order_list o）
			$querySql = "select * from kf_return_info 
						where city=\"{$city}\"";
			
			// 去掉$newrow数组中kf_tid的键和值
			unset ( $newrow ['user_tid'] );
			unset ( $newrow ['token'] );
			unset ( $newrow ['page'] );
			// 如果$newrow数组中有值，则在$querySql加入条件限制
			foreach ( $newrow as $k => $v ) {
				$querySql .= " and {$k} = \"{$v}\"";
			}
			
			// 如果查询成功，则进行分页，并返回数据 | 如果没有查询到数据，则返回没有此记录
			
			$model = spClass ( $this->tablename );
			$result = @$model->spPager ( $this->spArgs ( 'page', 1 ), 10 )->findSql ( $querySql );
			if ($result) {
				// $result = $model->findSql ( $querySql )
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '没有此记录', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, "对不起，您没有权限！", 1, 0, $prefixJS );
		}
	}
	
	// 回访记录接口值
	function queryreturn() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			
			$querysql = 'select count(tid) from kf_return_info 
			where return_name = "' . $newrow ['return_name'] . '"
			  and return_phone = "' . $newrow ['return_phone'] . '"';
			
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querysql )) {
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查找成功', $result, 0, $prefixJS );
			} else { // 查无此人
				$msg->ResponseMsg ( 1, '没找到此记录', $result, 0, $prefixJS );
			}
		} else { // 验证token不存在
			$msg->ResponseMsg ( 1, "对不起，您没有去权限！", 1, 0, $prefixJS );
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			// 查询回访记录表（kf_return_info）中所有联系方式
			$Sql = 'select distinct return_phone from kf_return_info';
			$model = spClass ( 'kf_return_info' );
			$return_phone_array = $model->findSql ( $Sql );
			foreach ( $return_phone_array as $k => $v ) {
				// 查询出回访次数
				$Sql = "select count(tid) as count from kf_return_info where return_phone ='" . $v ['return_phone'] . "'";
				$model = spClass ( 'kf_return_info' );
				$count_array = $model->findSql ( $Sql );
				// 查询回访次数小于等于4次的回访记录
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
				} // 查询回访次数大于等于5次的回访记录
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
			// 求出总页数
			$total_page = ( int ) (count ( $result ) / 10) + 1;
			// 根据tid升序排序
			for($i = 0; $i < count ( $result ) - 1; $i ++) {
				for($j = 0; $j < count ( $result ) - 1 - $i; $j ++) {
					if (($result [$j] ['tid']) >= ($result [$j + 1] ['tid'])) {
						$translation = $result [$j];
						$result [$j] = $result [$j + 1];
						$result [$j + 1] = $translation;
					}
				}
			}
			// 根据页码进行分页
			for($i = ($newrow ['page'] - 1) * 10; $i < ($newrow ['page']) * 10; $i ++) {
				if (! $result [$i]) {
					continue;
				}
				$result_paging [] = $result [$i];
			}
			// 将最终结果复制给$result
			$result = $result_paging;
			$verify = new checkCtr ();
			$result = $verify->record ();
			$msg->ResponseMsg ( 0, '查找成功', $result, $total_page, $prefixJS );
		} else { // 如果两个表中都没有该token，则返回 token验证失败，并停止函数运行
			$msg->ResponseMsg ( 1, "对不起，您没有权限！", 1, 0, $prefixJS );
			return;
		}
	}
}
