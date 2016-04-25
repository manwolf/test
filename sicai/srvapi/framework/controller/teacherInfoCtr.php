<?php
include_once "tools/tokenGen.php";
// include_once 'TeacherIntroduction.php';
include_once "tools/defSqlInject.php";
include_once 'base/crudCtr.php';
/**
 * 功能：获取教师列表及简介图片等信息
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class teacherInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_info';
	}
	
	// 获取教师列表
	public function getTeacherList() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$newrow [1] = 1;
		$model = spClass ( $this->tablename );
		
		$teacher_class_fees = $newrow ['teacher_class_fees'];
		$teacher_area = $newrow ['teacher_area'];
		$student_age_min = $newrow ['student_age_min'];
		$student_age_max = $newrow ['student_age_max'];
		$teacher_city = $newrow ['teacher_city'];
		$teacher_district = $newrow ['teacher_district'];
		$teacher_town = $newrow ['teacher_town'];
		$student_grade_max = $newrow ['student_grade_max'];
		$student_grade_min = $newrow ['student_grade_min'];
		$class_start_time = $newrow ['class_start_time'];
		// 根据年级 城市 区 镇 时间筛选在职教师
		$querySql = "select distinct t.tid, t.teacher_name, t.teacher_name_en,
				 t.teacher_sex, t.teacher_age, t.teacher_area, t.student_age_max,
				 t.student_age_min,  t.teacher_seniority, t.teacher_image, 
				t.student_grade_max, t.student_grade_min, t.teacher_city, 
				t.teacher_district, t.teacher_town, t.telephone,
				      c.class_price as teacher_class_fees, c.class_grade, td.teacher_idea
				from teacher_info t
				inner join teacher_detail_info td on t.tid = td.teacher_tid
 inner join class_price c on t.student_grade_max >= c.class_grade and t.student_grade_min <= c.class_grade
 inner join  teacher_schedule s on  s.teacher_tid=t.tid
 where t.teacher_city = c.class_city  and t.post_status=1"; // 判断城市 和教师是否在职
		                                                           // echo $querySql;
		                                                           // exit;
		
		if ($teacher_area != '') {
			$querySql = $querySql . " and teacher_area='{$teacher_area}'";
		}
		if ($teacher_city != '') {
			$querySql = $querySql . " and teacher_city='{$teacher_city}'";
		}
		if ($teacher_district != '') {
			$querySql = $querySql . " and teacher_district='{$teacher_district}'";
		}
		if ($teacher_town != '') {
			if ($teacher_town != '全区') {
				$querySql = $querySql . " and teacher_town in('{$teacher_town}', '全区')";
			}
		}
		
		if ($student_age_max != '') {
			$querySql = $querySql . " and student_age_max >= '{$student_age_max}'";
		}
		if ($student_age_min != '') {
			$querySql = $querySql . " and student_age_min <= '{$student_age_min}'";
		}
		if ($student_grade_max != '') {
			$querySql = $querySql . " and c.class_grade = '{$student_grade_max}'";
		}
		if ($student_grade_min != '') {
			$querySql = $querySql . " and student_grade_min <= '{$student_grade_min}'";
		}
		
		if ($student_age_min != '') {
			$querySql = $querySql . " and student_age_min <= '{$student_age_max}'";
		}
		if ($class_start_time != '') {
			$schedule_date = $newrow ['schedule_date'];
			$schedule_time = $newrow ['schedule_time'];
			date_default_timezone_set ( 'PRC' ); // 设置时区
			$time = time (); // 获取当前时间
			$nowDate = date ( "Y-m-d", $time ); // 获取服务器当前年月日
			$nowTime = date ( "H:i:s", $time ); // 获取服务器当前时间
			                                    // echo $nowDate,$nowTime;
			$a_date = strtotime ( $nowDate ); // 获取当前日期的时间戳
			$a_time = strtotime ( $nowTime ); // 获取当前时间的时间戳
			                                  // $b_time=date('Y-m-d',$a_time);
			$b_time = strtotime ( '+' . (1) . 'week', $a_date ); // 获取一周后时间戳
			$afterweekdate = date ( 'Y-m-d', $b_time ); // 获取七天后的日期
			
			$hourdate = strtotime ( '+' . (1) . 'day', $a_date ); // 获取一天候后时间戳
			$afterhourdateOne = date ( 'Y-m-d', $hourdate ); // 获取一天后的日期
			$hourdate = strtotime ( '+' . (2) . 'day', $a_date ); // 获取二天候后时间戳
			$afterhourdateTwo = date ( 'Y-m-d', $hourdate ); // 获取二天后的日期
			                                                 // echo $afterhourdateOne,$afterhourdateTwo;
			
			$hourtime = strtotime ( '+' . (24) . 'hour', $a_time ); // 获取24小时后时间戳
			$afterhourtime = date ( 'H:i:s', $hourtime ); // 获取24小时后时间
			                                              // echo $afterhourdate,$afterhourtime;
			                                              // exit;
			                                              // 查询24小时后 七天内 且教师有状态为闲时(time_busy=0)的所有老师信息
			$querySql = $querySql . " and (( s.schedule_date>='" . $afterhourdateTwo . "' and s.schedule_date<='" . $afterweekdate . "'
		    		and time_busy=0 and s.schedule_time='" . $class_start_time . "') or (s.schedule_date>='" . $afterhourdateOne . "' and 
		    		 		s.schedule_date<='" . $afterhourdateTwo . "' and s.schedule_time>='" . $afterhourtime . "' and time_busy=0 and
		    		 				s.schedule_time='" . $class_start_time . "'))";
			// echo $querySql;
			// exit;
		}
		
		$querySql = $querySql . " group by t.tid order by teacher_class_fees asc  "; // asc升序 desc降序
		                                                                             // echo $querySql;
		                                                                             // exit;
		$result = $model->findSql ( $querySql );
		
		$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		
		return true;
	}
	
	// 获取教师列表--简介
	public function getTeacherDetailInfo() {
		// echo 321;
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		// $newrow [1] = 1;
		$model = spClass ( "teacher_detail_info" );
		
		$teacher_tid = $newrow ['teacher_tid'];
		
		$querySql = "select * from teacher_detail_info where 1 = 1";
		if ($teacher_tid != '') {
			$querySql = $querySql . " and teacher_tid='{$teacher_tid}'";
		}
		
		$result = $model->findSql ( $querySql );
		$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		
		return true;
	}
	// 查询教师基本信息
	public function queryTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$model = spClass ( $this->tablename );
		// $result = $model->findSql ( $querySql );
		// echo 111;
		// exit;
		if (! $newrow) {
			$querySql = 'select * from teacher_info';
			
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		} 

		else {
			$querySql = 'select * from teacher_info where ';
			foreach ( $newrow as $k => $v ) {
				$querySql = $querySql . $k . '="' . $v . '" and ';
			}
			$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
			// echo $querySql;
			// exit;
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '没有发现该教师', $result, 0, $prefixJS );
			}
		}
		
		return true;
	}
	// 查看教师头像
	public function queryTeacherImg() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		$Sql = 'select teacher_image from teacher_info 
				where tid =' . $newrow ['tid'];
		
		$model = spClass ( 'teacher_info' );
		$result = $model->findSql ( $Sql );
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
	}
	// 查询每天上课的时间段
	function classTimeEveryDay() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$querySql = 'select * from class_time_everyday where 1=1';
		$model = spClass ( 'class_time_everyday' );
		if ($result = $model->findSql ( $querySql )) {
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '没有找到上课时间', $result, 0, $prefixJS );
		}
		return true;
	}
	// 教师人工评价 教研对教师评价
	function teacherArtificialEvaluation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$teacher_tid = $newrow ['teacher_tid'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'not find tid', $result, 0, $prefixJS );
			// exit;
		} else {
			// 查询teacher_tid是否存在
			$query = 'select count(T.teacher_tid) as count from
					(
					select teacher_tid from teacher_artificial_evaluation 
					where teacher_tid=' . $teacher_tid . ') T';
			$model = spClass ( 'teacher_artificial_evaluation' );
			$result = $model->findSql ( $query );
			if ($result [0] ['count'] > 0) {
				// 获取教研对老师的各项评价 和 平均评价
				// avg(teacher_ontime_evaluation),avg(teacher_appearance_evaluation),avg(teacher_lesson_evaluation)
				// $querySql='select *,(select ) from teacher_artificial_evaluation where teacher_tid='.$teacher_tid;
				$querySql = 'select  *,sum(teacher_ontime_evaluation + teacher_appearance_evaluation + teacher_lesson_evaluation)/3 as total_evaluation from teacher_artificial_evaluation where teacher_tid=' . $teacher_tid;
				$model = spClass ( 'teacher_artificial_evaluation' );
				
				if ($result = $model->findSql ( $querySql )) {
					$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, '查询失败', $result, 0, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '该老师还没有评价！', $result, 0, $prefixJS );
			}
		}
		return true;
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
	function add() {
		return false;
	}
	// 根据不同城市，老师性别，上课年级，所在区，所在街道筛选不同的老师信息（陈梦帆PC端）
	function filterteacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];

		$teacher_city = $newrow ['teacher_city'];
		$teacher_sex = $newrow ['teacher_sex'];
		$student_grade_max = $newrow ['student_grade_max'];
		$student_grade_min = $newrow ['student_grade_min'];
		$teacher_district = $newrow ['teacher_district'];
		$teacher_town = $newrow ['teacher_town'];
		// 根据城市筛选教师
		$querySql = "select distinct t.tid, t.teacher_name,
				  t.teacher_image,t.student_grade_max, t.student_grade_min, 
				 td.teacher_idea from teacher_info t
				inner join teacher_detail_info td on t.tid = td.teacher_tid
                where t.teacher_city = '" . $teacher_city."'"; // 判断城市
		// 根据性别查询老师

		if ($teacher_sex != '') {
			$querySql = $querySql . " and t.teacher_sex = ".$teacher_sex;

		if ($teacher_sex != '') {
			$querySql = $querySql . " and t.teacher_sex = '{$teacher_sex}'";

		}

		if ($student_grade_max != '' and $student_grade_min != '') {
			$querySql = $querySql . " and student_grade_max >= '{$student_grade_max}' 
			                          and student_grade_min <= '{$student_grade_min}'";
		}
		// 根据老师所在区查询
		if ($teacher_district != '') {
			$querySql = $querySql . " and teacher_district = '{$teacher_district}'";
		}
		// 根据老师所在街道查询
		if ($teacher_town != '') {
			if ($teacher_town != '全区') {
				$querySql = $querySql . " and teacher_town in('{$teacher_town}', '全区')";
			}
		}
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
// 		$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
// 		$pager = $model->spPager ()->getPager ();
// 		$total_page = $pager ['total_page'];
//         echo $querySql;
		if ($result) {
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '查询失败', false, $total_page, $prefixJS );
		}
	}
 }
}



	

	
		
	
	
