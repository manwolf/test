<?php
include_once "tools/tokenGen.php";
include_once 'TeacherIntroduction.php';
include_once 'base/encrypt.php';
// include_once "tools/defSqlInject.php";
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';

class JYTeacherInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_info';
	}
	// 查询全国教师信息
	public function queryAllTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		
		if($result){
			
		$begin_date = $newrow ['begin_date'];
// 		echo $begin_date;
// 		exit;
		$end_date = $newrow ['end_date'];
		$page = $newrow ['page'];
		$begindate=date('Y-m-01', strtotime(date("Y-m-d")));
		$enddate= date('Y-m-d', strtotime("$begindate +1 month -1 day"));
	
		if ($page == '' || $page == null || $page <= 0) {
			$page = 1;
		}
		unset ( $newrow ['page'] );
		unset($newrow['user_tid']);
		
		
			if (! $newrow && $result[0]['city'] == 全国) // 如果查询内容为空 教研员的城市为全国，则查询全国老师
{
				$querySql = 'SELECT 
				    t.tid,t.teacher_num, t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,
				    (a.teacher_ontime_evaluation + a.teacher_appearance_evaluation + a.teacher_lesson_evaluation) / 3 AS evaluation,
				    IFNULL(COUNT(distinct o.user_tid), 0) as c_total,
				    sum(case when c.user_confirm = 1 and  c.class_start_date>="'.$begindate.'"' .' and c.class_start_date<="'.$enddate.'"' .' THEN 1 else 0  end)*2 c_1,
					sum(case when c.user_confirm = 0 and  c.class_start_date>="'.$begindate.'"' .' and c.class_start_date<="'.$enddate.'"' .' THEN 1 else 0  end)*2 c_2
				    from teacher_info t
				       left join
				    teacher_detail_info d ON d.teacher_tid = t.tid
				        LEFT JOIN
				    teacher_artificial_evaluation a ON a.teacher_tid = t.tid
				        LEFT JOIN
				    order_list o ON o.teacher_tid = t.tid
				        LEFT JOIN
				    class_list c ON c.order_tid = o.tid 
				GROUP BY t.tid
				ORDER BY t.tid';
				$model = spClass ( 'teacher_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager()->getPager ();
				$total_page = $pager ['total_page'];
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow && $result[0]['city'] == 全国) // 如果查询内容不为空 教研员的城市为空，则按条件查询全国老师
{
				if ($newrow ['begin_date'] && $newrow ['end_date']) {
					$querySql = 'select * from (select distinct
						t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
						from teacher_info t, teacher_detail_info d,teacher_artificial_evaluation a
						where d.teacher_tid=t.tid and  teacher_hiredate>"' . $begin_date . '"' . 'and teacher_hiredate<"' . $end_date . '"' . 'group by t.tid)' . 'w left join
						(select
						teacher_tid,
						count(distinct user_tid) as c_total,
						sum(case when c.user_confirm =1  then 1 else 0 end )*2 c_1,
						sum(case when c.user_confirm =0  then 1 else 0 end )*2 c_2
						from order_list o,class_list c
						where c.order_tid=o.tid
						group by o.teacher_tid) o2
							on  o2.teacher_tid=w.tid where ';
					
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				} else {
					$querySql = 'select * from (select distinct
						t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
						from teacher_info t, teacher_detail_info d,teacher_artificial_evaluation a
						where d.teacher_tid=t.tid group by t.tid )
						w left join
						(select
						teacher_tid,
						count(distinct user_tid) as c_total,
						sum(case when c.user_confirm =1  then 1 else 0 end )*2 c_1,
						sum(case when c.user_confirm =0  then 1 else 0 end )*2 c_2
						from order_list o,class_list c
						where c.order_tid=o.tid
						group by o.teacher_tid) o2
							on  o2.teacher_tid=w.tid where ';
					
					foreach ( $newrow as $k => $v ) {
						$querySql = $querySql . $k . '="' . $v . '" and ';
					}
					
					$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
					// echo $querySql;
					// exit;
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					// echo $total_page;
					// exit;
					$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				}
			}
			if (! $newrow && $result[0]['city'] != 全国) { // 如果查询内容为空 教研员的城市不为空，则查询与教研员桐城市的老师
				
				
				$querySql = 'select * from (select distinct
					t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
					
					from teacher_info t,oa_user_info o, teacher_detail_info d,teacher_artificial_evaluation a
					where d.teacher_tid=t.tid and o.city = t.teacher_city AND o.city ="' . $result[0]['city'] . '"' . 'group by t.tid)' . ' 
					w left join
					(select
					teacher_tid,
					count(distinct user_tid) as c_total,
					sum(case when c.user_confirm =1  then 1 else 0 end )*2 c_1,
					sum(case when c.user_confirm =0  then 1 else 0 end )*2 c_2
					from order_list o,class_list c
					where c.order_tid=o.tid
					group by o.teacher_tid) o2
						on  o2.teacher_tid=w.tid  ';
				
				$model = spClass ( 'teacher_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				return;
			}
			
			if ($newrow && $result[0]['city'] != 全国 ) { // 如果查询内容不为空 教研员的城市不为空，则按条件查询与教研员桐城市的老师
				if ($newrow ['begin_date'] && $newrow ['end_date']) {
					$querySql = 'select * from (select distinct
						t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
						from teacher_info t,oa_user_info o, teacher_detail_info d,teacher_artificial_evaluation a
						where d.teacher_tid=t.tid and o.city = t.teacher_city and o.city="'.$result[0]['city'].'"'.' group by t.tid)   
						w left join
						(select
						teacher_tid,
						count(distinct user_tid) as c_total,
						sum(case when c.user_confirm =1  then 1 else 0 end )*2 c_1,
						sum(case when c.user_confirm =0  then 1 else 0 end )*2 c_2
						from order_list o,class_list c
						where c.order_tid=o.tid
						group by o.teacher_tid) o2
							on  o2.teacher_tid=w.tid where  teacher_hiredate>"' . $begin_date . '"' . 'and teacher_hiredate<"' . $end_date . '"';
					
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
					return;
				}
				if (! $newrow ['begin_date'] && ! $newrow ['end_date']) { // 如果查询开始时间和结束时间不为空 则查询这个时间段里的数据
					$querySql = 'select * from (select distinct
						t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
						from teacher_info t,oa_user_info o, teacher_detail_info d ,teacher_artificial_evaluation a
						where d.teacher_tid=t.tid and o.city = t.teacher_city and o.city="'.$results['city'].'"'.'group by t.tid) 
						w left join
						(select
						teacher_tid,
						count(distinct user_tid) as c_total,
						sum(case when c.user_confirm =1  then 1 else 0 end )*2 c_1,
						sum(case when c.user_confirm =0  then 1 else 0 end )*2 c_2
						from order_list o,class_list c
						where c.order_tid=o.tid
						group by o.teacher_tid) o2
							on  o2.teacher_tid=w.tid where  ';
					foreach ( $newrow as $k => $v ) {
						$querySql = $querySql . $k . '="' . $v . '" and ';
					}
					$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
					// echo $querySql;
					// exit;
					
					$model = spClass ( 'teacher_info' );
					
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];
					$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				}
			}
		} else {
			$msg->ResponseMsg ( 1, '对不起，您没权限！', 1, 0, $prefixJS );
		}
	}
	// 查询按tid查询老师
	function queryTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if($result){
		$city=$result[0]['city'];
		$teacher_tid = $newrow ['teacher_tid'];
		$querySql = "select distinct t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.teacher_sex,t.teacher_district,t.teacher_town,t.student_grade_max,t.student_grade_min,t.teacher_seniority,t.telephone,t.address,t.graduated_from,t.graduation_date,t.teacher_major,  t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,d.teacher_idea
						from teacher_info t, teacher_detail_info d
						where d.teacher_tid=t.tid and t.teacher_city=.$result[0]['city'] and  t.tid=" . $teacher_tid;
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			if($result[0]['teacher_city']=$city ||$city==全国){
			$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', false, 0, $prefixJS );
				
		}
		}
		else {
			$msg->ResponseMsg ( 1, '对不起您没有权限', 1, 0, $prefixJS );
		}
	}
	
	// 更新教师级别
	function updateTeacherLevel() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if($result){
		$city=$result[0]['city'];
		$teacher_num = $newrow ['teacher_num'];
		$level = $newrow ['level'];
		$querySql='select teacher_city from teacher_info where teacher_num='.$teacher_num;
		$model=spClass('teacher_info');
		$result=$model->findSql($querySql);
		if($result[0]['teacher_city']==$city ||$city==全国){
		$conditions = array (
					'teacher_num' => $teacher_num 
			); // 条件查询 字段teacher_num=$teacher_num
			$model = spClass ( 'teacher_info' );
			$result = $model->updateField ( $conditions, 'teachers_level', $level );
			$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', $result, 0, $prefixJS );
				
		}
		}else {
		
			$msg->ResponseMsg ( 1, '对不起您没有权限！', 1, 0, $prefixJS );
		}
	}
	// 更改老师状态
	function updateTeacherState() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if($result){
		$city=$result[0]['city'];
		$teacher_num = $newrow ['teacher_num'];
		$state = $newrow ['state']; // 老师职位状态 0为实习 1为在职 2为离职
		$date = date ( 'Y-m-d H:i:s', time () ); // 获取当前时间
		$querySql='select teacher_city from teacher_info where teacher_num='.$teacher_num;
		$model=spClass('teacher_info');
		$result=$model->findSql($querySql);
		if($result[0]['teacher_city']==$city ||$city==全国){
			$conditions = array (
					'teacher_num' => $teacher_num 
			);
			$model = spClass ( 'teacher_info' );
			$result = $model->updateField ( $conditions, 'post_status', $state );
			if ($teacher_num == 2) {
				$addSql = "insert teacher_info set dimission_time='" . $date . "'"; // 老师离职添加老师离职时间
				$model = spClass ( 'teacher_info' );
				$result = $model->runSql ( $addSql );
			}
			$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '对不起您没有权限！', 1, 0, $prefixJS );
		}
	}
	}
	// 删除教师
	function deleteTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		// 查询老师所在城市
		$querySql = "select tid,teacher_city from teacher_info where tid=" . $tid;
		$model = spClass ( 'teacher_info' );
		$result = $model->findSql ( $querySql );
		
		$jy_token = $newrow ['token'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "jy_token", $token, "jy_user_info" );
		if ($result) {
			if ($tid == null && $result [0] ['tid'] == null) {
				$msg->ResponseMsg ( 1, '没有找到这个教师', 1, 0, $prefixJS );
			} else {
				if ($result1 ['jy_city'] == $result [0] ['teacher_city']) // 教研所在城市跟老师所在城市对比 一致则可以删除，不一不能删除
{
					$delSql = 'delete from teacher_info where  tid=' . $tid;
					$model = spClass ( $this->tablename );
					$result = $model->runSql ( $delSql );
					$msg->ResponseMsg ( 0, '删除成功', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 0, '您无权删除非本城市的老师！', 1, 0, $prefixJS );
				}
			}
		} else {
			$msg->ResponseMsg ( 0, '身份验证失败', 1, 0, $prefixJS );
		}
	}
	// 新增教师信息
	function addTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if($result){
			$city=$result[0]['city'];
			if (defined ( 'TestVersion' )) {// 如果为测试环境$url地址如下
			$url = "http://testapi.e-teacher.cn/testfile/Teacher_image/$telephone.png";
		} else {
			$url = "http://image.e-teacher.cn/teacher/$telephone.png";
		}
		$newrow ['teacher_image'] = $url;
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '填写信息为空！', null, 0, $prefixJS );
			} 
			else {
				if ($city != $newrow ['teacher_city'] || $city==全国) { // 教研城市跟老师城市不匹配不能添加
					$msg->ResponseMsg ( 1, '对不起，你没有权限添加非本城市的老师！', 1, 0, $prefixJS );
					return;
				}
				if ($newrow ['student_grade_max'] < $newrow ['student_grade_min']) { // 最高年级小于最低年级不能添加
					$msg->ResponseMsg ( 1, '最高年级不能小于最低年级！', 1, 0, $prefixJS );
					return;
				}
				if ($newrow ['telephone'] == null) {
					$msg->ResponseMsg ( 1, '手机号不能为空！', 1, 0, $prefixJS );
					return;
				}
				
				$conditions = array ('teacher_num' => $newrow ['teacher_num'] ); // 判断是否有一样的手机号一样则不能重复添加
				$model = spClass ( 'teacher_info' );
				$result = $model->find ( $conditions );
				if ($result ['teacher_num'] != null) {
					$msg->ResponseMsg ( 1, '此老师编号已存在，请勿重复添加！', 1, 0, $prefixJS );
					return;
				}
				$conditions = array (
						'telephone' => $newrow ['telephone'] 
				); // 判断是否有一样的手机号一样则不能重复添加
				$model = spClass ( 'teacher_info' );
				$result = $model->find ( $conditions );
				if ($result ['telephone'] != null) {
					$msg->ResponseMsg ( 1, '此手机号码已存在，请勿重复添加！', 1, 0, $prefixJS );
				} else {
					$teacher_token = $newrow ['teacher_token'];
					$newrow ['teacher_token'] = $this->produceToken (); // 自动生成token
					$model = spClass ( 'teacher_info' );
					$result = $model->create ( $newrow );
					$querySql = '';
					$tid = $result;
					
					if ($result <= 0) {
						return;
					} else { // 基本信息添加成功后，再添加老师简介等
						$addSql = 'insert  teacher_artificial_evaluation set teacher_ontime_evaluation="' . $newrow ['teacher_ontime_evaluation'] . '"' . ', teacher_appearance_evaluation="' . $newrow ['teacher_appearance_evaluation'] . '"' . ',teacher_lesson_evaluation="' . $newrow ['teacher_lesson_evaluation'] . '"' . ', teacher_tid=' . $tid;
						
						// $addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
						// echo $addSql;
						// exit;
						$model = spClass ( 'teacher_artificial_evaluation' );
						if ($result = $model->runSql ( $addSql )) {
							$querySql = "select tid ,telephone from  teacher_info where tid=" . $tid;
							$model = spClass ( 'teacher_info' );
							$result = $model->findSql ( $querySql );
						}
					}
				}
				$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '身份验证失败', 1, 0, $prefixJS );
		}
	}
	// 老师修改密码
	function update() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$teacher_token = $newrow ['teacher_token'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "jy_token", $token, "jy_user_info" );
		$querySql = "select tid,teacher_city from teacher_info where tid=" . $tid;
		$model = spClass ( 'teacher_info' );
		$result1 = $model->findSql ( $querySql );
		if ($result1 [0] ['tid'] == null) {
			$msg->ResponseMsg ( 1, '此老师不存在', 1, 0, $prefixJS );
			return;
		}
		if ($result) {
			$update = "update teacher_info set login_pwd=" . $pwd . "where tid='" . $tid . "'";
			$model = spClass ( 'teacher_info' );
			$result = $model->runSql ( $updateSql );
			$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '对不起，权限不足', $result, 0, $prefixJS );
		}
	}
	
	// 修改老师的信息
	function updateTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if($result){
			$city=$result[0]['city'];
		$tid = $newrow ['tid'];
		$telephone = $newrow ['telephone'];
		$querySql = "select tid,teacher_city from teacher_info where tid=" . $tid;
		$model = spClass ( 'teacher_info' );
		$result = $model->findSql ( $querySql );
		if($result[0]['teacher_city']==$city ||$city==全国){
			if ($result [0] ['tid'] == null) {
			$msg->ResponseMsg ( 1, '此老师不存在', 1, 0, $prefixJS );
			return;
		}
		
			if (defined ( 'TestVersion' )) { // 测试环境地址如下
				$url = "http://testapi.e-teacher.cn/testfile/Teacher_image/$telephone.png";
			} else {
				$url = "http://image.e-teacher.cn/teacher/$telephone.png";
			}
			$teacher_image = $url;
			
			if ($newrow) {
				$updateSql = 'update teacher_info  set  teacher_image=" ' . $teacher_image . '"' . ',';
				foreach ( $newrow as $k => $v ) {
					if ($k == 'tid') {
						continue;
					}
					$updateSql = $updateSql . $k . '="' . $v . '",';
					// echo $updateSql;
					// exit;
				}
				$updateSql = substr ( $updateSql, 0, strlen ( $updateSql ) - 1 );
				// echo $updateSql;
				// exit;
				$model = spClass ( 'teacher_info' );
				$tidsql = ' where tid=' . $tid;
				$updateSql = $updateSql . $tidsql;
				// echo $updateSql;
				// exit;
				$result = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '填写的信息是空的！', false, 0, $prefixJS );
			}
			
			// return true;
		}else{
			$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', 1, 0, $prefixJS );
				
		} 
		}else {
		
			$msg->ResponseMsg ( 1, '身份验证失败', 1, 0, $prefixJS );
		}
	}
	// 修改老师简介生活照
	function updateTeacherDetail() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if($result){
			$city=$result[0]['city'];
		$tid = $newrow ['tid'];
		$querySql = "select tid,telephone,teacher_city from teacher_info where tid=" . $tid;
		$model = spClass ( 'teacher_info' );
		$result = $model->findSql ( $querySql );
		$telephone = $result [0] ['telephone'];
		if($result[0]['teacher_city']==$city || $city==全国){
		 if ($result [0] ['tid'] == null) {
			$msg->ResponseMsg ( 1, '此老师不存在', 1, 0, $prefixJS );
			return;
		}
	
			if (defined ( 'TestVersion' )) { // 如果测试环境生活照地址如下
				$t_image_url_1 = "http://testapi.e-teacher.cn/testfile/Teacher_detail_image/" . $telephone . "_1.png";
				$t_image_url_2 = "http://testapi.e-teacher.cn/testfile/Teacher_detail_image/" . $telephone . "_2.png";
			} else {
				$t_image_url_1 = "http://image.e-teacher.cn/teacher_detail_image/" . $telephone . "_1.png";
				$t_image_url_2 = "http://image.e-teacher.cn/teacher_detail_image/" . $telephone . "_2.png";
			}
			
			// if( $newrow['teacher_information_1']!=null && $newrow['teacher_information_2']!=null)
			{
				$updateSql = 'update teacher_detail_info  set  t_image_url_1="' . $t_image_url_1 . '"' . ' , t_image_url_2="' . $t_image_url_2 . '"' . ',';
				foreach ( $newrow as $k => $v ) {
					if ($k == 'tid') {
						continue;
					}
					$updateSql = $updateSql . $k . '="' . $v . '",';
					// echo $updateSql;
					// exit;
				}
				$updateSql = substr ( $updateSql, 0, strlen ( $updateSql ) - 1 );
				// echo $updateSql;
				// exit;
				$model = spClass ( 'teacher_info' );
				$tidsql = ' where teacher_tid=' . $tid;
				$updateSql = $updateSql . $tidsql;
				// echo $updateSql;
				// exit;
				$result = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
			}
			// else
			// {
			// $msg->ResponseMsg ( 1, '填写的信息是空的！', false, 0, $prefixJS );
			// }
			
			// return true;
		}else{
			$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', 1, 0, $prefixJS );
				
		}
		}else {
		
			$msg->ResponseMsg ( 1, '身份验证失败', 1, 0, $prefixJS );
		}
	}
	// 教师登陆
	function landingTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$telephone = $newrow ['telephone'];
		$login_pwd = $newrow ['login_pwd'];
		// $querySql="select post_status from teacher_info where telephone=".$telephone;
		// $model = spClass ( 'teacher_info' );
		// $result1 = $model->findSql ( $querySql );
		// 用户登录
		$verify = new encrypt ();
		$result = $verify->login ( 'telephone', $telephone, 'login_pwd', $login_pwd, 'teacher_info' );
		$verify->loginAutoRecord ( $result ['0'] ['tid'], $result ['0'] ['teacher_token'] );
		if ($result) {
			if ($result [0] ['post_status'] == 1) {
				if ($result [0] ['login_state'] == 0) {
					$msg->ResponseMsg ( 0, '第一次登陆请修改密码！', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 0, '登录成功', $result, 0, $prefixJS );
					return;
				}
			} else {
				$msg->ResponseMsg ( 1, '您不是在职老师不能登录', false, 0, $prefixJS );
			}
		} else {
			
			$msg->ResponseMsg ( 1, '账号密码错误', false, 0, $prefixJS );
		}
	}
	// 教师第一次登陆修改密码
	function updatePwdFirst() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$pwd = $newrow ['pwd'];
		$teacher_token = $newrow ['token'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "teacher_token", $token, "teacher_info" );
		if ($result) {
			
			if ($pwd == null) {
				$msg->ResponseMsg ( 1, '请输入密码!', false, 0, $prefixJS );
			} else {
				
				$updateSql = "update teacher_info set login_state=1, login_pwd=" . $pwd . " where tid=" . $result ['tid'];
				$model = spClass ( 'teacher_info' );
				$result = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, '修改成功！', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '身份验证失败！', 1, 0, $prefixJS );
		}
	}
	
	// 产生token
	function produceToken($len = 8) {
		$tokenTxt = $this->randomkeys ( $len );
		// echo $tokenTxt;
		
		// 这里的$tokenTxt不是token，是token的源字符串，系统默认自动生成一个8位的随机数做为token的源。
		$token = tokenGen::encrypt ( $tokenTxt );
		// echo '$token='.$token."<br>";
		// start $token里会出现特殊符号，下面将特殊符号替换为数字或字母
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		// $token = strtr($token, "+", "A");
		// $token = '@$^&#$%&$#%**$@#$%^&*()==';
		for($i; $i < strlen ( $token ); $i ++) {
			$ord_token = ord ( $token {$i} );
			if (! (($ord_token >= 48 && $ord_token <= 57) || ($ord_token >= 65 && $ord_token <= 90) || ($ord_token >= 97 && $ord_token <= 122) || ($ord_token == 61))) {
				// echo '$token1='.$token{$i}."<br>";
				// echo '-----'.ord($token{$i})."<br>";
				$token {$i} = $pattern {mt_rand ( 0, 35 )};
				// echo '$token2='.$token{$i}."<br>";
			}
		}
		// end
		// echo '$token转化后='.$token."<br>";
		$model = spClass ( "teacher_info" );
		$sum = $model->findCount ( array (
				'teacher_token' => $token 
		) );
		// echo '$sum='.$sum."<br>";
		// exit;
		if ($sum > 0) {
			return strtr ( $this->produceToken ( $len ), "+", "A" );
		} else
			return $token;
	}
	
	// 产生定长的随机数
	function randomkeys($length) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
	// 登录
	function testToken() {
		$token = "VToCa1p+VzJcfQA+CmYDPw==";
		echo strtr ( $token, "+", "A" );
		// $msg->ResponseMsg ( 1, "请获取验证码", $result, 1, $prefixJS );
	}
	
	// //生成MD5密文
	// function cipher($login_pwd)
	// {
	// //echo $login_pwd.'----';
	// //得到数据的密文
	// $login_pwd=md5($login_pwd); // echo $login_pwd.'---';
	// //再把密文字符串的字符顺序调转
	// $login_pwd = strrev($login_pwd); // echo $login_pwd.'---';
	// //再进行一次MD5运算并返回
	// $login_pwd=md5($login_pwd); // echo $login_pwd.'---';
	// //将返回后的字符串循环十次
	// $times=11;
	// for ($i = 0; $i < $times; $i++) {
	// $login_pwd = md5($login_pwd);
	// }
	// //echo $login_pwd.'---';
	// //再把密文字符串的字符顺序调转
	// $login_pwd = strrev($login_pwd); //echo $login_pwd.'---';
	// //最后再进行一次MD5运算并返回
	// $login_pwd=md5($login_pwd);
	// // echo $login_pwd;
	// // exit;
	// //echo $login_pwd.'----';
	// return $login_pwd;
	// }
}
