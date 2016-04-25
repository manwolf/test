<?php
include_once 'base/crudCtr.php';
/**
 * 功能：教师请假及课程安排
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class JS_classStatistics extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_info';
	}
	// 教师上课统计 ---教师端现在主页
	function teacherIndex() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$date = ($newrow ['date']); // 接受前端传来时间
		date_default_timezone_set ( 'PRC' ); // 设置时区
		$time = time (); // 获取当前时间
		$nowTime = date ( "Y-m-d", $time ); // 获取服务器当前年月日
		                                    
		// 判断时间同步
		if (date ( 'Ymd', strtotime ( $nowTime ) ) == date ( 'Ymd', strtotime ( $date ) ) && ! $date == null && ! $tid == null) {
			// if(1==1)
			// {
			$beginDate = date ( 'Y-m-01', strtotime ( date ( "Y-m-d" ) ) ); // 获取当月第一天
			$endDateOne = date ( 'Y-m-d', strtotime ( "$beginDate +1 month -1 day" ) ); // 获取本月最后1天
			// 判断老师所有剩余课时 家长未确认
			$querySql = 'select count(user_confirm)*2 as information  
					from  class_list c ,order_list o, teacher_info t
		    	      where t.tid=o.teacher_tid and o.tid=c.order_tid and user_confirm=0 and t.tid=' . $tid;
			$model = spClass ( $this->tablename );
			$resultno = $model->findSql ( $querySql );
			// 判断统计不超过本月
			if (date ( 'Ymd', strtotime ( $date ) ) >= date ( 'Ymd', strtotime ( $beginDate ) ) && date ( 'Ymd', strtotime ( $date ) ) <= date ( 'Ymd', strtotime ( $endDateOne ) )) {
				// $querySql='select count(user_confirm) as classNum,count()';
				// 当月老师已完成课时总数
				$querySql = 'select count(user_confirm)*2 as information  
		    			from  class_list c ,order_list o, teacher_info t
		    	        where t.tid=o.teacher_tid and o.tid=c.order_tid 
		    			and user_confirm=1 and c.class_start_date>="'.$beginDate.'" and c.class_start_date<="'.$endDateOne.'" and t.tid=' . $tid;
				$model = spClass ( $this->tablename );
				$resultyes = $model->findSql ( $querySql );
				// $yes_class_num=$resultyes['0']['yes_class_num'];
				// 统计教师所有学生数
				$querySql = 'select count(distinct user_tid) as information from order_list  where  teacher_tid=' . $tid;
				$model = spClass ( $this->tablename );
				$resultuser = $model->findSql ( $querySql );
				// $user_num= $resultuser['0']['user_num'];
				$result = array_merge ( $resultno, $resultyes, $resultuser );
				if ($result) {
					
					$msg->ResponseMsg ( 0, 'sucsess', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, 'fail', $result, 0, $prefixJS );
				}
			} else {
				
				$msg->ResponseMsg ( 1, '日期不能超过这个月！', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '日期和系统日期不同步！', $result, 0, $prefixJS );
		}
		
		return true;
	}
	// 教师调休 教师当月总课数达到70，其他无视
	// 返回申请状态0、1 (家长端对应显示0、1)每时间断只能调整一次，需提前24小时申请
	function teacherPaidLeave() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$teacher_nowdate = ($newrow ['teacher_nowdate']); // 接受前端传来当前日期
		$teacher_date = $newrow ['teacher_date']; // 获得教师需要调休的日期
		$teacher_time = $newrow ['teacher_time']; // 获得教师需要调休的时间
		                                          // $teacher_time=($newrow['time']); //接受前端传来时间
		date_default_timezone_set ( 'PRC' ); // 设置时区
		$time = time (); // 获取当前时间
		$nowTime = date ( "Y-m-d", $time ); // 获取服务器当前年月日
		                                    // echo $nowTime;
		                                    // echo $teacher_date;
		if (date ( 'Ymd', strtotime ( $nowTime ) ) == date ( 'Ymd', strtotime ( $teacher_nowdate ) ) && ! $teacher_nowdate == null && ! $tid == null) {
			
			$time = time (); // 获取当前时间
			$now = date ( "H:i:s", $time ); // 获取服务器当前时间
			                                // echo $time.'===';
			$now = strtotime ( $now ); // 当前时间戳
			                           // $teacher_time=$teacher_time.':00'; //前段没有秒需要配上
			$teacher_time = strtotime ( $teacher_time ); // 需调休的时间戳
			                                             // 教师需提前24小时申请
			if ((date ( 'Ymd', strtotime ( $teacher_date ) ) - date ( 'Ymd', strtotime ( $nowTime ) ) >= 1 && $teacher_time >= $now && date ( 'Ymd', strtotime ( $teacher_date ) ) - date ( 'Ymd', strtotime ( $nowTime ) ) < 2) || date ( 'Ymd', strtotime ( $teacher_date ) ) - date ( 'Ymd', strtotime ( $nowTime ) ) >= 2) {
				
				// 新增和后状态改为1 申请中
				$teacher_date = $newrow ['teacher_date']; // 获得教师需要调休的日期
				$teacher_time = $newrow ['teacher_time']; // 获得教师需要调休的时间
				$time = time (); // 获取当前时间
				$nowTime = date ( "Y-m-d H:i:s", $time ); // 获取服务器当前年月日
				$updateSql = 'update teacher_schedule set teacher_entry_state=1,teacher_apply_date="' . $nowTime . '" where schedule_date="' . $teacher_date . '" and schedule_time="' . $teacher_time . '" and teacher_tid=' . $tid;
				$model = spClass ( $this->tablename );
				$upresult = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '申请需提前24小时', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '日期和系统日期不同步', $result, 0, $prefixJS );
		}
		return true;
	}
	// 教师课程安排
	// 接受当前时间，查询前、现在、下周的所有课程信（按时间段查询） 学生姓名以及在读年级会显示在课程日历中
	function teacherCurriculum() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$date = $newrow ['date']; // 当前时间
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'not tid', flase, 0, $prefixJS );
		} else {
			
			$nowdate = strtotime ( $date ); // 获取当前时间戳
			$beginweek = strtotime ( '-' . (10) . 'day', $nowdate ); // 获取10天前的时间戳
			$beginweek = date ( 'Y-m-d', $beginweek ); // 获取10天前的日期
			                                           // echo $beginweek.'--';
			$afterweek = strtotime ( '+' . (10) . 'day', $nowdate ); // 获取10天后的时间戳
			$afterweek = date ( 'Y-m-d', $afterweek ); // 获取10天后的日期
			                                           
			//请 不要修改代码
			$querySql = 'select o.tid as order_tid,s.teacher_entry_state,s.teacher_rest_state,s.time_busy,s.schedule_date,s.schedule_time,
                         u.user_name,o.class_content,s.tid,c.tid as class_tid
                         from  teacher_info t
                         left join  teacher_schedule s on t.tid=s.teacher_tid
                         left join  class_list c  on  s.class_tid=c.tid
                         left join  order_list o  on  c.order_tid=o.tid
                         left join    user_info u on o.user_tid=u.tid
 
                         where  s.schedule_date>="' . $beginweek . '"  and schedule_date <="' . $afterweek . '"  and
                         t.tid=' . $tid . ' group by s.schedule_date,s.schedule_time';
			// echo $querySql;
			// exit;
			
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				$teacher_class = $newrow ['teacher_class']; //教师本月已上课时数
				$beginDate = date ( 'Y-m-01', strtotime ( date ( "Y-m-d" ) ) ); // 获取当月第一天
				                                                               
				$endDateOne = date ( 'Y-m-d', strtotime ( "$beginDate +1 month -1 day" ) ); // 获取本月最后1天
				                                                                            
				if ($teacher_class >= 70 && 0 == time_busy) {//当教师本月已完成课时超过70    且时间在当前到本月之间   的所有状态为闲时的记录
					// 这里需要判断教师忙闲
					$tid = $newrow ['tid'];
					$updateSql = 'update teacher_schedule set teacher_rest_state=1 where  schedule_date>="' . $date . '" and schedule_date<="' . $endDateOne . '" and teacher_tid=' . $tid;
					
					$model = spClass ( $this->tablename );
					$resultup = $model->runSql ( $updateSql );
				}
				$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, 'not find', $result, 0, $prefixJS );
			}
		}
		
		return true;
	}
	
	
	// 教师查看家长评价结果
	function userEvaluationResults() {
		// echo aaa;
		// 课程记录页面返回家长评价结果
		// 查询数据 返回12345 + 评论 状态 0/1 未评价/已评价
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'not null', $result, 0, $prefixJS );
			exit;
		} else {
			$querySql = 'select * from user_evaluation  where class_tid=' . $tid;
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '教师没有上传教案', $result, 0, $prefixJS );
			}
		}
		
		return true;
	}
	// 教师个人信息 现在只有评价
	function teacherPersonalInformation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'not find tid', $result, 0, $prefixJS );
		} else {
			$querySql = 'select t.teacher_name,avg(e.teacherOntime) as teacherOntimeSum,avg(e.lessonPlanReady) as lessonPlanReadySum,avg(e.classroomInteraction) as classroomInteractionSum,count(e.class_tid) as PNum
					from teacher_info t,order_list o,class_list c, user_evaluation e 
					where o.teacher_tid=t.tid and c.order_tid=o.tid and e.class_tid=c.tid and t.tid=' . $tid;
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			} else {
				
				$msg->ResponseMsg ( 1, '还没有学生对您评价！', $result, 0, $prefixJS );
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