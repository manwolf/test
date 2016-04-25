<?php
include_once 'base/crudCtr.php';
/**
 * 功能：学生日历及调课功能
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class userCourseCalendar extends crudCtr {
	public function __construct() {
		$this->tablename = 'user_classes';
	}
	// 学生查看当月课程个(家长课程日历页面)
	function queryCourse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		// $newrow [1] = 1;
		if (! $newrow) {
			
			$msg->ResponseMsg ( 1, 'Please enter the user_tid', $result, 0, $prefixJS );
			exit;
		} else { 
			date_default_timezone_set ( 'PRC' ); // 设置时区
			$time = time (); // 获取当前时间
			$nowTime = date ( "Y-m-d", $time ); // 获取服务器当前年月日
			$beginDate = date ( 'Y-m-01', strtotime ( date ( "Y-m-d" ) ) ); // 获取当月第一天
			// echo $beginDate.'---';
			// $a_time = strtotime($order_date); //获取当前日期的时间戳
			$endDateOne = date ( 'Y-m-d', strtotime ( "$beginDate +1 month -1 day" ) ); // 获取本月最后1天
			// 获取本月课程记录
			$querySql = 'select c.user_classes_state,a.user_classes,c.tid as class_tid,t.tid as teacher_tid,u.tid as user_tid,o.tid as order_tid,c.class_start_date,c.class_start_time,t.teacher_name,o.class_content
		    from teacher_info  t  left join  order_list o  on  t.tid=o.teacher_tid  left join  user_info u
		    on o.user_tid=u.tid  left join class_list c on c.order_tid=o.tid  left join user_classes a
		    on a.user_tid=u.tid  where class_start_date>="' . $beginDate . '" and class_start_date<="' . $endDateOne . '" and u.tid=' . $tid . ' group by c.tid';
			
			$model = spClass ( $this->tablename );
			
			if ($result = $model->findSql ( $querySql )) {
				// $querySql='';
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, 'not find', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	// 家长次月课程调整
	function userMakeAdjustments() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		
		$date = ($newrow ['date']); // 接受前端传来时间
		date_default_timezone_set ( 'PRC' ); // 设置时区
		$time = time (); // 获取当前时间
		$nowTime = date ( "Y-m-d", $time ); // 获取服务器当前年月日
		 // 判断前段客户时间与服务器时间是否相同
		if (date ( 'Ymd', strtotime ( $nowTime ) ) == date ( 'Ymd', strtotime ( $date ) ) && ! $date == null) {
			$querySql = 'select user_nextmonth_state from user_info where tid=' . $user_tid;
			// echo $querySql;
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			//user_nextmonth_state为0时表示该学生当月没有进行过次月调课
			if ($result ['0'] ['user_nextmonth_state'] == 0) {
				// echo 'aaa';
				// exit;
				$beginDate = date ( 'Y-m-01', strtotime ( date ( "Y-m-d" ) ) ); // 获取当月第一天
			    //获取当前日期的时间戳
				$endDateOne = date ( 'Y-m-d', strtotime ( "$beginDate +1 month -1 day" ) ); // 获取本月最后1天
				// echo $endDateOne.'----';
				$endDate = date ( 'Y-m-d', strtotime ( "$beginDate +1 month -5 day" ) ); // 获取本月倒数第5天
				// echo $endDate;
				// 判断客户端日期是否在当月的最后五天
				if (date ( 'Ymd', strtotime ( $date ) ) <= date ( 'Ymd', strtotime ( $endDateOne ) ) && date ( 'Ymd', strtotime ( $date ) ) >= date ( 'Ymd', strtotime ( $endDate ) )) 

				{
					
					// 将家长下月调课需求存入数据库
					$addSql = 'insert  user_classes set ';
					foreach ( $newrow as $k => $v ) {
						if ($k == 'tid' || $k == 'date') { //不存tid和date
							continue;
						}
						$addSql = $addSql . $k . '="' . $v . '",';
					}
					$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
					
					$model = spClass ( $this->tablename );
					$result = $model->runSql ( $addSql );
					if ($result <= 0) {
						return;
					} else {
						// 修改家长下月调课状态
						$updateSql = 'update user_info set user_nextmonth_state=1 where tid=' . $user_tid;
						$model = spClass ( $this->tablename );
						$upresult = $model->runSql ( $updateSql );
						
						$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
					}
				} else {
					$msg->ResponseMsg ( 1, '您只有在本月最后五天才能进行次月调课！', $result, 0, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '您每月只能使用一次调课或者换课功能！', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '您当前日期与系统日期不同步！', $result, 0, $prefixJS );
		}
		
		return true;
	}
	
	// 家长提出加课 无限制 随时可以增加 任何东西
	function userAddClass() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		if($user_tid =='')
		{
			$msg->ResponseMsg ( 1, '请传入学生id', $result, 0, $prefixJS );
			exit;
		}
		
		// $tid = $newrow['tid'];
		// 添加学生加课记录
		$addSql = 'insert  user_classes  set ';
		foreach ( $newrow as $k => $v ) {
			
			$addSql = $addSql . $k . '="' . $v . '",';
		}
		$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
		$model = spClass ( $this->tablename );
		$result = $model->runSql ( $addSql );
		if ($result <= 0) {
			
			return;
		} else {
			// 修改加课状态
			
			$updateSql = 'update user_info set user_add_class=1 where tid=' . $user_tid;
			$model = spClass ( $this->tablename );
			$upresult = $model->runSql ( $updateSql );
			
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		}
		
		return true;
	}
	
	// 家长提出本月换课
	function userChangeClass() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$class_date = $newrow ['class_date']; // 原上课日期
		$class_time = $newrow ['class_time']; // 原上课时间
		$hope_date = $newrow ['hope_date']; // 想要换课的日期
		$hope_time = $newrow ['hope_time']; // 想要换课的时间
		$user_tid = $newrow ['user_tid'];
		$class_tid = $newrow ['class_tid'];
		$user_classes = $newrow ['user_classes'];
		$date = ($newrow ['date']); // 接受前端传来时间		                    
		date_default_timezone_set ( 'PRC' ); // 设置时区
		$time = time (); // 获取当前时间
		$nowTime = date ( "Y-m-d", $time ); // 获取服务器当前年月日
		// 判断前段客户时间与服务器时间是否相同
		if (date ( 'Ymd', strtotime ( $nowTime ) ) == date ( 'Ymd', strtotime ( $date ) ) && ! $date == null) {
			// 查询user_classes=2（临时换课） 时次月调课的状态 若不为0则返回 换课失败（次月换课和临时换课每月只有一种能生效）
			$querySql = 'select user_change_state from user_info where tid=' . $user_tid;
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			if ($result ['0'] ['user_change_state'] == 0) {
				$beginDate = date ( 'Y-m-01', strtotime ( date ( "Y-m-d" ) ) ); // 获取当月第一天
				                                                     // echo $beginDate.'===';
				$endDateOne = date ( 'Y-m-d', strtotime ( "$beginDate +1 month -1 day" ) ); // 获取本月最后1天
				 // echo $endDateOne.'===';
				// 判断原上课日期必须在当前时间之后 本月之前
				if (date ( 'Ymd', strtotime ( $date ) ) <= date ( 'Ymd', strtotime ( $class_date ) ) && date ( 'Ymd', strtotime ( $class_date ) ) <= date ( 'Ymd', strtotime ( $endDateOne ) )) {
					
					$time = time (); // 获取当前时间
					$now = date ( "H:i:s", $time ); // 获取服务器当前时间
					// echo $now.'===';
					$now = strtotime ( $now );
					// echo $now.'===';
					$class_time = strtotime ( $class_time ); // 原上课时间戳
				    // 临时换课需求必须提前24小时 少一秒都不行
					if ((date ( 'Ymd', strtotime ( $class_date ) ) - date ( 'Ymd', strtotime ( $date ) ) >= 1 && $class_time >= $now && date ( 'Ymd', strtotime ( $class_date ) ) - date ( 'Ymd', strtotime ( $date ) ) < 2) || date ( 'Ymd', strtotime ( $class_date ) ) - date ( 'Ymd', strtotime ( $date ) ) >= 2) {
						// echo 'ererer';
						// exit;
						$a_time = strtotime ( $class_date ); // 获取原上课日期的时间戳
						$b_time = strtotime ( '-1 week', $a_time ); // 获取原上课日期七天前的时间戳
						$begindate = date ( 'Y-m-d', $b_time ); // 获取原上课日期七天前的日期
						                                   // echo $begindate.'===';
						$c_time = strtotime ( '+1 week', $a_time ); // 获取原上课日期七天后的时间戳
						$afterweektime = date ( 'Y-m-d', $c_time ); // 获得原上课日期七天后的日期
						if (date ( 'Ymd', strtotime ( $hope_date ) ) >= date ( 'Ymd', strtotime ( $begindate ) ) 
						   && date ( 'Ymd', strtotime ( $hope_date ) ) <= date ( 'Ymd', strtotime ( $afterweektime ) ) && // 想要换课的日期范围只能在原上课日期的前后七天内
                           date ( 'Ymd', strtotime ( $date ) ) < date ( 'Ymd', strtotime ( $hope_date ) )) {
							// //如果是拼客课程 则必须是发起人才能发起拼客
							// if()
							// {
							
							// }
							// 换课后修改老师状态
							$updateSql = 'update teacher_schedule set time_busy=0,class_tid=null where class_tid=' . $class_tid;
							$model = spClass ( $this->tablename );
							$result = $model->runSql ( $updateSql );
							// 查询原上课时间的订单号
							$qurySql = 'select order_tid from class_list where tid=' . $class_tid;
							$model = spClass ( $this->tablename );
							$resultquery = $model->findSql ( $qurySql );
							$order_tid = $resultquery [0] ['order_tid'];
							// echo $order_tid;
							// 将新的上课时间新增入class_list表
							$addSql = 'insert  class_list  set order_tid=' . $order_tid . ',class_start_date="' . $hope_date . '",class_start_time="' . $hope_time . '"';
							$model = spClass ( $this->tablename );
							$resultup = $model->runSql ( $addSql );
							// 删除原上课时间
							$deleteSql = 'delete from class_list where tid=' . $class_tid;
							$model = spClass ( $this->tablename );
							$resultup = $model->runSql ( $deleteSql );
							
							// echo $order_tid;
							// exit;
							
							
							// 记录换课时间 客服查看
							$addSql = 'insert  user_classes  set user_classes=' . $user_classes . ',class_date="' . $class_date . '",class_time="' . $class_time . '"
 			 		                		,hope_date="' . $hope_date . '",hope_time="' . $hope_time . '",class_tid=' . $class_tid . ',user_tid=' . $user_tid;
							$model = spClass ( $this->tablename );
							$result = $model->runSql ( $addSql );
							// //
							// 修改学生表字段
							$updateSql = 'update user_info set user_change_state=1 where tid=' . $user_tid;
							$model = spClass ( $this->tablename );
							$result = $model->runSql ( $updateSql );
							
							$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
							// }
						} else {
							$msg->ResponseMsg ( 1, '您只能在您所选日期的前后七天进行选择！', null, 0, $prefixJS );
						}
					} else {
						$msg->ResponseMsg ( 1, '您必须提前24小时进行调课操作！', null, 0, $prefixJS );
					}
				} else {
					$msg->ResponseMsg ( 1, '请选择合适的时间', null, 0, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '您好！次月调课和换课每月仅能进行一次！', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '您当前时间和系统时间不同步！', $result, 0, $prefixJS );
		}
		
		return true;
	}
	
	// 查询原上课时间前七天后七天 教师上课时间 及繁忙状态
	function teachersStateTime() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$teacher_tid = $newrow ['teacher_tid'];
		$startDate = $newrow ['startDate'];
		$endDate = $newrow ['endDate'];
		if (! $newrow) {
			
			$msg->ResponseMsg ( 1, '没有获取到日期和时间', $result, 0, $prefixJS );
			exit;
		} else 

		{
			// 查询原上课时间前七天后七天 教师上课时间 及繁忙状态
			$querySql = 'select tid,teacher_tid,schedule_date,schedule_time,time_busy 
					from teacher_schedule where schedule_date>="' . $startDate . '" and schedule_date<="' . $endDate . '" and ' . 'teacher_tid=' . $teacher_tid;
			// echo $querySql;
			// exit;
			$model = spClass ( $this->tablename );
			
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, 'not find', $result, 0, $prefixJS );
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