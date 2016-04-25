<?php
include_once 'payListCtr.php';
include_once 'base/crudCtr.php';
/**
 * 功能：根据免费订单日期自动生成课程记录 内部调用
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class automatic extends crudCtr {
	public function __construct() {
		$this->tablename = 'class_list';
	}
	// 自动生成classlist 暂为1节课
	function addOrderList($order_tid, $user_tid, $order_date) {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		date_default_timezone_set ( 'PRC' ); // 设置时区
		                                  // echo '---'.$order_tid,$user_tid,$order_date;
		
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'fail', null, 0, $prefixJS );
		} else {
			// echo $user_tid;
			//判断学生城市
			$querySql = 'select user_city from user_info where tid=' . $user_tid;
			$model = spClass ( 'user_info' );
			
			$resul_use = $model->findSql ( $querySql );
			// echo $resul_use[0]['user_city'];
			// exit;
			// 上海地区免费规则
			if ($resul_use [0] ['user_city'] == '上海') {
				// echo 'aaaa';
				for($i = 1; $i <= 1; $i ++) {
					
					$a_time = strtotime ( $order_date ); // 获取当前日期的时间戳
					                                  // $b_time=date('Y-m-d',$a_time);
					$b_time = strtotime ( '+' . (1) . 'week', $a_time ); // 获取一周后时间戳
					$order_date = date ( 'Y-m-d', $b_time ); // 获取当前时间
					$time = strtotime ( $order_date ); // 再次获取当前时间戳
					$aaa = idate ( 'm', $time ); // 获取当前月份
					 // $b_time = strtotime('+1 Month',$a_time); //获取一个月后的时间戳
					 // $b = date('Y-m-d',$b_time); //获得一个月后的日期
					if ($i == 1) {// 第一次循环的时候因为是从七天后开始的 所以减去七天

						$a = strtotime ( $order_date );
						$b = strtotime ( '+' . (- 1) . 'week', $a );
						$order_date = date ( 'Y-m-d', $b );
						// echo $order_date;
						// exit;
					}
					// 给课程表添加一节课
					$addSql = 'insert into class_list ( order_tid,class_start_date,class_start_time,class_no)
			  	   select tid,"' . $order_date . '",order_time,' . $i . ' from order_list b where b.tid=' . $order_tid;
					// echo $addSql;
					// exit;
					
					$model = spClass ( $this->tablename );
					// echo $addSql;
					$result = $model->runSql ( $addSql );
				
					// 更新免费试课的订单状态为已排课
					$querySql = ' update order_list set order_state= 3 where tid = ' . $order_tid;
					$model = spClass ( $this->tablename );
					$result = $model->runSql ( $querySql );
				}
			}
			// 重庆地区免费规则
			// 暂定和上海一样
			if ($resul_use [0] ['user_city'] == '重庆') {
				
				for($i = 1; $i <= 1; $i ++) {
					
					$a_time = strtotime ( $order_date ); // 获取当前日期的时间戳
					                                  // $b_time=date('Y-m-d',$a_time);
					$b_time = strtotime ( '+' . (1) . 'week', $a_time ); // 获取一周后时间戳
					$order_date = date ( 'Y-m-d', $b_time ); // 获取当前时间
					$time = strtotime ( $order_date ); // 再次获取当前时间戳
					$aaa = idate ( 'm', $time ); // 获取当前月份
					
					if ($i == 1) {// 第一次循环的时候因为是从七天后开始的 所以减去七天
						$a = strtotime ( $order_date );
						$b = strtotime ( '+' . (- 1) . 'week', $a );
						$order_date = date ( 'Y-m-d', $b );
						// echo $order_date;
						// exit;
					}
					// 给课程表添加一节课
					$addSql = 'insert into class_list ( order_tid,class_start_date,class_start_time,class_no)
			  	   select tid,"' . $order_date . '",order_time,' . $i . ' from order_list b where b.tid=' . $order_tid;
					// echo $addSql;
					// exit;
					
					$model = spClass ( $this->tablename );
					// echo $addSql;
					$result = $model->runSql ( $addSql );
					
					// 更新免费试课的订单状态为已排课
					$querySql = ' update order_list set order_state= 3 where tid =  ' . $order_tid;
					$model = spClass ( $this->tablename );
					$result = $model->runSql ( $querySql );
				}
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


