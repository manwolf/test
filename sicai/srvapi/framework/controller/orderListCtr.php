<?php
include_once 'userInfoCtr.php';
include_once 'automatic.php';
/**
 * 功能：学生预约生成/查询订单信息
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class orderListCtr extends userInfoCtr {
	public function __construct() {
		$this->tablename = 'order_list';
	}
	// 当调用checkAddParams时验证$token
	public function checkAddParams($row, $token, $prefixJS) {
		$tid = $row ['user_tid'];
		$newrow = array (
				'tid' => $tid 
		);
		if (! parent::checkAddParams ( $newrow, $token, $prefixJS )) {
			return false;
		}
		return true;
	}
	
	// 添加订单
	function add() {
		// echo('crudCtr->add');
		// echo '1'."\n";
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$class_discount_tid = $newrow ['class_discount_tid']; // 折扣ID
		$order_type = $newrow ['order_type']; // 订单状态 0免费 1付费 2添加充值钱包订单
		$user_tid = $newrow ['user_tid'];
		$pay_done = $newrow ['pay_done'];
		$order_date = $newrow ['order_date'];
		$order_time = $newrow ['order_time'];
		$teacher_tid = $newrow ['teacher_tid'];
		// 判断用户所选时间老师状态是否为闲
		
		if (2 == $order_type) {
			$model = spClass ( $this->tablename );
			$result = $model->create ( $newrow );
			if ($result >= 0) {
				$result = $model->findAll ( array (
						$model->pk => $result 
				) );
				$msg->ResponseMsg ( 0, "添加充值钱包订单成功", $result, 1, $prefixJS );
				exit ();
			} else {
				$msg->ResponseMsg ( 0, "添加充值钱包订单失败", $result, 1, $prefixJS );
				exit ();
			}
		}
		
		// echo $class_way;
		// echo $class_count;
		// exit;
		
		date_default_timezone_set ( 'PRC' ); // 设置时区
		                                     // 验证请求参数
		if (! $this->checkAddParams ( $newrow, $token, $prefixJS )) {
			// echo('crudCtr->add->step 1');
			$msg->ResponseMsg ( 1, "token error1", null, 1, $prefixJS );
			return;
		}
		// 查询学生免费使用记录
		$query = 'select user_free_num from  user_info where tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$resultqu = $model->findSql ( $query );
		// if(0==$resultqu[0]['user_free_num'])
		// 判断是否是免费订单 0免费 1付费
		if (0 == $order_type) {
			if ($user_tid == '' || $teacher_tid == '' || $order_date == '' || $order_time == '') {
				$msg->ResponseMsg ( 1, "请检查教师id,学生id,以及是否选择上课时间！", null, 1, $prefixJS );
				exit ();
			}
			$querySql = 'select t.post_status,s.time_busy 
        			from teacher_info t,teacher_schedule s where s.schedule_date="' . $order_date . '" and s.schedule_time="' . $order_time . '" and  t.tid=' . $teacher_tid;
			// 判断教师是否在职 post_status为1时在职 其他状态不能预约
			// $querySql = 'select post_status from teacher_info where tid=' . $teacher_tid;
			$model = spClass ( $this->tablename );
			$resultQu = $model->findSql ( $querySql );
			if ($resultQu [0] ['post_status'] != 1) {
				$msg->ResponseMsg ( 1, "您好！该教师不是在职状态，无法预约", null, 1, $prefixJS );
				exit ();
			}
			$resultQuery='select time_busy 
        			from teacher_schedule  where schedule_date="' . $order_date . '" and schedule_time="' . $order_time.'" and teacher_tid='.$teacher_tid;
			if ($resultQuery [0] ['time_busy'] != 0) {
				$msg->ResponseMsg ( 1, "您好！该时间已被预约，请重新选择日期", null, 1, $prefixJS );
				exit ();
			}
			
			// 判断用户以免费使用次数 为0时可以继续下单
			if (0 == $resultqu [0] ['user_free_num']) {
				$time = time (); // 获取当前时间
				$nowdate = date ( "Y-m-d", $time ); // 获取服务器当前年月日
				
				$nowtime = date ( "H:i:s", $time ); // 获取服务器当前时间
				                                    // echo $nowtime.'===';
				$nowtime = strtotime ( $nowtime ); // 当前时间戳
				$order_time = strtotime ( $order_time ); // 订单时间戳
				                                         // 免费试课必须提前24小时
				if ((date ( 'Ymd', strtotime ( $order_date ) ) - date ( 'Ymd', strtotime ( $nowdate ) ) >= 1 && $order_time >= $nowtime && date ( 'Ymd', strtotime ( $order_date ) ) - date ( 'Ymd', strtotime ( $nowdate ) ) < 2) || date ( 'Ymd', strtotime ( $order_date ) ) - date ( 'Ymd', strtotime ( $nowdate ) ) >= 2) {
					if($class_discount_tid ==''){
						$msg->ResponseMsg ( 1, "没有获取到免费价格及折扣信息！", $result, 1, $prefixJS );
						exit ();
					}
					// 查询所选普通课程 在当前城市的折扣和课时数及免费价格
					$querySql = 'select class_hour,class_disc,class_free_price from class_discount where tid=' . $class_discount_tid;
					$model = spClass ( $this->tablename );
					$resultsquery = $model->findSql ( $querySql );
					// 计算普通课程的原价
					$order_original_price = ($resultsquery [0] ['class_free_price']) * ($resultsquery [0] ['class_hour']);
					$newrow ['order_original_price'] = $order_original_price;
					// 计算普通课程的实际价格
					$order_money = ($resultsquery [0] ['class_free_price']) * ($resultsquery [0] ['class_hour']) * ($resultsquery [0] ['class_disc']);
					$newrow ['order_money'] = $order_money;
					
					
					$model = spClass ( $this->tablename );					
					// 返回表的主键
					$result = $model->create ( $newrow );
					
					// return;
					// echo('crudCtr->add->step 2');
					if ($result <= 0) {
						$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
						return;
					}
					$result = $model->findAll ( array (
							$model->pk => $result 
					) );
					if (! $this->checkAddParams ( $newrow, $token, $prefixJS )) {
						// echo('crudCtr->add->step 1');
						return;
					}
					if (count ( $result ) > 0) 

					{
						$order_tid = $result [0] ['tid'];
						// 调用自动排课接口$automatic 传入参数
						$automatic = new automatic ();
						$automatic->addOrderList ( $order_tid, $user_tid, $order_date );
						
						$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
					}
				} else {
					$msg->ResponseMsg ( 1, '您所选的时间必须在24小时以后', $result, 1, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '您的免费试课次数已用完！', $result, 1, $prefixJS );
			}
		}  // 付费订单
else {
			$class_types = $newrow ['class_types']; // class_types:课程类型 0为普通课程 1为精品课程 默认为0 必传
			                                        // 普通课程
			
			if (0 == $class_types) {
				// 判断教师是否在职 post_status为1时在职 其他状态不能预约
				$querySql = 'select post_status from teacher_info where tid=' . $teacher_tid;
				$model = spClass ( $this->tablename );
				$resultQuery = $model->findSql ( $querySql );
				if ($resultQuery [0] ['post_status'] != 1) {
					$msg->ResponseMsg ( 1, "您好！该教师不是在职状态，无法预约", null, 1, $prefixJS );
					exit ();
				}
				if ($user_tid == '' || $teacher_tid == '') {
					$msg->ResponseMsg ( 1, "请传入教师和学生的id", null, 1, $prefixJS );
					exit ();
				}
				// 验证学生$token
				if (! $this->checkAddParams ( $newrow, $token, $prefixJS )) {
					// echo('crudCtr->add->step 1');
					$msg->ResponseMsg ( 1, "token error2", null, 1, $prefixJS );
					return;
				}
				//更新用户信息user_info年级
				if($newrow ['class_grade'] !=''){
					$updateSql='update user_info set  user_grade='.$newrow ['class_grade'].' where tid='.$user_tid;
					$model = spClass ( $this->tablename );
					$resultup = $model->runSql ( $updateSql );
				}
				// $class_grade=$newrow['class_grade']; //家长所选年级
				// 根据学生id查询 所在城市的年级单价
				$querySql = 'select c.class_price from class_price c,user_info u where c.class_city=u.user_city and u.tid=' . $user_tid . ' and c.class_grade=' . $newrow ['class_grade'];
				$model = spClass ( $this->tablename );
				$resultqu = $model->findSql ( $querySql );
				// 查询所选普通课程 在当前城市的折扣和课时数
				$querySql = 'select class_hour,class_disc from class_discount where tid=' . $class_discount_tid;
				$model = spClass ( $this->tablename );
				$resultsquery = $model->findSql ( $querySql );
				// 计算普通课程的原价
				$order_original_price = ($resultqu [0] ['class_price']) * ($resultsquery [0] ['class_hour']);
				$newrow ['order_original_price'] = $order_original_price;
				// 计算普通课程的实际价格
				$order_money = ($resultqu [0] ['class_price']) * ($resultsquery [0] ['class_hour']) * ($resultsquery [0] ['class_disc']);
				$newrow ['order_money'] = $order_money;
				
				$model = spClass ( $this->tablename );
				// 新增
				$result = $model->create ( $newrow );
				
				// return;
				// echo('crudCtr->add->step 2');
				if ($result <= 0) {
					$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
					return;
				}
				
				$result = $model->findAll ( array (
						$model->pk => $result 
				) );
				
				if (count ( $result ) > 0) {
					
					$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
				}
			} // 精品课程
              elseif (1 == $class_types) {
				// 验证参数
				if (! $this->checkAddParams ( $newrow, $token, $prefixJS )) {
					// echo('crudCtr->add->step 1');
					$msg->ResponseMsg ( 1, "token error2", null, 1, $prefixJS );
					return;
				}
				
				$high_quality_courses_tid = $newrow ['high_quality_courses_tid']; // 精品课程的id
				if ($high_quality_courses_tid == '') {
					$msg->ResponseMsg ( 1, '缺少精品课程id!', array (), 1, $prefixJS );
					exit ();
				}
				// 查询精品课程的课时数和单价
				$querySql = 'select class_hour,high_quality_price from high_quality_courses where tid=' . $high_quality_courses_tid;
				$model = spClass ( $this->tablename );
				$result = $model->findSql ( $querySql );
				// 计算精品课程的原价
				$order_original_price = ($result [0] ['class_hour']) * ($result [0] ['high_quality_price']);
				$newrow ['order_original_price'] = $order_original_price;
				// 计算精品课程的实际价格
				$order_money = ($result [0] ['class_hour']) * ($result [0] ['high_quality_price']);
				$newrow ['order_money'] = $order_money;
				// 添加一条预约订单
				$model = spClass ( $this->tablename );
				$result = $model->create ( $newrow );
				if ($result <= 0) {
					$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
					return;
				}
				
				$result = $model->findAll ( array (
						$model->pk => $result 
				) );
				
				if (count ( $result ) > 0) {
					
					$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
				}
			}
		}
	}
	
	// 查询订单
	function queryOrder() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// $newrow [1] = 1;
		$user_tid = $newrow ['user_tid'];
		//由于时间关系  暂时先将普通和精品分开查询
		// 查询所有非拼客订单 o.order_type !=2  普通
		$querySql = 'select o.*, t.teacher_name, t.teacher_image, c.class_count*2 as class_count, c.class_disc,u.spelling_lesson_number,
				     u.participants_number,s.class_count*2 as spell_class_count
                     from order_list o left join teacher_info t on o.teacher_tid=t.tid left join class_discount c
				     on o.class_discount_tid=c.tid left join user_spelling_lesson u on u.tid=o.user_spelling_lesson_tid
				     left join  spelling_lesson_discount s on s.city=t.teacher_city
 				     
				     where o.order_state != "2" and  o.order_type !=2  and o.order_type !=3  and o.class_types=0 and o.user_tid=' . $user_tid . ' group by tid order by create_time ';
		
		            $model = spClass ( $this->tablename );
		            $resultqu1 = $model->findSql ( $querySql );
		// $msg->ResponseMsg ( 0, 'success', $resultqu, 0, $prefixJS );
		// exit;
		
		// 查询拼客所有已支付的订单 区分拼课与其他订单order_type为3时为拼课  普通
		$querySql = 'select o.*, t.teacher_name, t.teacher_image, c.class_count*2 as class_count, c.class_disc,u.spelling_lesson_number,
				    u.participants_number,s.class_count*2 as spell_class_count
                    from order_list o 
				     left join teacher_info t on o.teacher_tid=t.tid 
				     left join class_discount c on o.class_discount_tid=c.tid 
				     left join user_spelling_lesson u on u.tid=o.user_spelling_lesson_tid
				     left join  spelling_lesson_discount s on s.city=t.teacher_city
 				     where o.order_state != "2" and  o.order_type=3  and  o.order_type !=2 and o.pay_done=1 and o.class_types=0 and o.user_tid=' . $user_tid . ' group by tid order by create_time ';
		             $model = spClass ( $this->tablename );
		             $resultqu2 = $model->findSql ( $querySql );
		// 查询所有非拼客订单 o.order_type !=2   精品
		$querySql = 'select o.*, u.spelling_lesson_number,u.participants_number,h.class_hour as class_count ,h.high_quality_image,h.high_quality_name,h.courses_type
				    from  order_list o left join high_quality_courses h on o.high_quality_courses_tid=h.tid
				    left join user_info us on h.city=us.user_city 
				    left join user_spelling_lesson u on u.tid=o.user_spelling_lesson_tid
				    where o.order_state != "2" and  o.order_type !=2  and o.order_type !=3  and o.class_types=1 and o.user_tid=' . $user_tid . ' group by tid order by create_time ';
		            $model = spClass ( $this->tablename );
		            $resultquery1 = $model->findSql ( $querySql );
		// // 查询拼客所有已支付的订单 区分拼课与其他订单order_type为3时为拼课   精品
		$querySql = 'select o.*, u.spelling_lesson_number,u.participants_number,h.class_hour as class_count,h.high_quality_image,h.high_quality_name,h.courses_type
				    from  order_list o left join high_quality_courses h on o.high_quality_courses_tid=h.tid
				    left join user_info us on h.city=us.user_city
				    left join user_spelling_lesson u on u.tid=o.user_spelling_lesson_tid
				    where o.order_state != "2" and  o.order_type=3  and  o.order_type !=2 and o.pay_done=1  and o.class_types=1 and o.user_tid=' . $user_tid . ' group by tid order by create_time ';
		            $model = spClass ( $this->tablename );
		            $resultquery2 = $model->findSql ( $querySql );
		// 将两个json数组合并输出
		$result = array_merge ( $resultqu1, $resultqu2,$resultquery1,$resultquery2);//,$resultquery3,$resultquery4);
		
		if ($result) {
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 0, '没有发现您的订单！', $result, 0, $prefixJS );
		}
		// $demo=$model->dumpSql();
	}
	// 禁止以下action实例化基类
	function query() {
		return false;
	}
	function delete() {
		return false;
	}
	function update() {
		return false;
	}
}