<?php
include_once "tools/tokenGen.php";
include_once 'TeacherIntroduction.php';
// include_once "tools/defSqlInject.php";
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
class JYTeacherInfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_info';
	}
	/**
	 * 功能：查询老师，修改老师，更新职位状态，更新老师等级
	 * 作者： 李坡
	 * 日期：2015年9月1日
	 */
	// 查询教师信息
	public function queryAllTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$begin_date = $newrow ['begin_date'];//前端开始传来时间
			$end_date = $newrow ['end_date'];//前端结束传来时间
			$page = $newrow ['page'];
			$begindate = date ( 'Y-m-01', strtotime ( date ( "Y-m-d" ) ) ); // 获取月初时间
			$enddate = date ( 'Y-m-d', strtotime ( "$begindate +1 month -1 day" ) ); // 获取月底时间
			if ($page == '' || $page == null || $page <= 0) {
				$page = 1;
			}
			unset ( $newrow ['page'] );
			unset ( $newrow ['user_tid'] );
			if($result[0]['city']=='全国'){//通过判断城市是否为全国选择不同sql语句
			$querySql='select d.tid, d.teacher_num,d.teacher_name,d.teacher_city,d.post_status,d.teachers_level,d.teacher_hiredate ,d.teacher_dimission,d.evaluation,
				if(a.count,a.count,0)as count,if(b.count1,b.count1,0)as count1,if(c.count2,c.count2,0)as count2
				from
				(select t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,t.teacher_dimission,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
						from teacher_info t left join teacher_detail_info d on d.teacher_tid=t.tid
						left join teacher_artificial_evaluation a on a.teacher_tid=t.tid ) d
						left join
						(select count(distinct user_tid)as count,teacher_tid from order_list where  pay_done=1 group by teacher_tid) a on  d.tid=a.teacher_tid
						left join
						(select o.teacher_tid,count( c.user_confirm)*2 as count1 from class_list c ,order_list o where o.tid=c.order_tid and c.user_confirm=1 and class_start_date>="'.$begindate.'"'.' and class_start_date<="'.$enddate.'"'.' group by o.teacher_tid) b on a.teacher_tid=b.teacher_tid
						left join
						(select o.teacher_tid,count( c.user_confirm)*2 as count2 from class_list c ,order_list o where o.tid=c.order_tid and c.user_confirm=0 and class_start_date>="'.$begindate.'"'.' and class_start_date<="'.$enddate.'"'.' group by o.teacher_tid) c on b.teacher_tid=c.teacher_tid';
			}else{
			$querySql='select d.tid, d.teacher_num,d.teacher_name,d.teacher_city,d.post_status,d.teachers_level,d.teacher_hiredate ,d.teacher_dimission,d.evaluation,
				if(a.count,a.count,0)as count,if(b.count1,b.count1,0)as count1,if(c.count2,c.count2,0)as count2
				from
				(select t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.post_status,t.teachers_level,t.teacher_hiredate ,t.teacher_dimission,(a.teacher_ontime_evaluation+a.teacher_appearance_evaluation+a.teacher_lesson_evaluation)/3  as evaluation
						from teacher_info t  left join teacher_detail_info d on d.teacher_tid=t.tid
						left join teacher_artificial_evaluation a on a.teacher_tid=t.tid where t.teacher_city="'.$result[0]['city'].'"'.') d
						left join
						(select count(distinct user_tid)as count,teacher_tid from order_list where  pay_done=1 group by teacher_tid) a on  d.tid=a.teacher_tid
						left join
						(select o.teacher_tid,count( c.user_confirm)*2 as count1 from class_list c ,order_list o where o.tid=c.order_tid and c.user_confirm=1 and class_start_date>="'.$begindate.'"'.' and class_start_date<="'.$enddate.'"'.' group by o.teacher_tid) b on a.teacher_tid=b.teacher_tid
						left join
						(select o.teacher_tid,count( c.user_confirm)*2 as count2 from class_list c ,order_list o where o.tid=c.order_tid and c.user_confirm=0 and class_start_date>="'.$begindate.'"'.' and class_start_date<="'.$enddate.'"'.' group by o.teacher_tid) c on b.teacher_tid=c.teacher_tid';
			}
			if (!$newrow ){ // 如果查询内容为空 教研员的城市为全国，则查询全国老师
				$querySql;
			}
			if($newrow['teacher_name']!=null){//如果输入有名字按名字查询
				$querySql.=' where d.teacher_name="'.$newrow['teacher_name'].'"';
			}
			if($newrow['teacher_num']!=null){//如果输入有老师编号，按老师编号查询
				$querySql.=' where d.teacher_num="'.$newrow['teacher_num'].'"';
			}
			if($newrow['time_type']!=null){//时间类型不为空则按类型查
				if($newrow['time_type']==0){//如果time传0 则为入职时间
				$querySql.=' where d.teacher_hiredate>="'.$begin_date.'"'.' and d.teacher_hiredate<="'.$end_date.'"' ;
				}
				if($newrow['time_type']==1){//如果time_type传1则为离职时间
					$querySql.=' where d.teacher_dimission>="'.$begin_date.'"'.' and d.teacher_dimission<="'.$end_date.'"' ;
				}
			}
			if($newrow['post_status']!=null){//按职位状态0为实习1为在职2为离职
				$querySql.=' where d.post_status="'.$newrow['post_status'].'"' ;
			}
			if($newrow['teachers_level']!=null){//按老师级别查询0为初级1为中级2为高级3为特级
				$querySql.=' where d.teachers_level="'.$newrow['teachers_level'].'"' ;
			}
			$model=spClass('teacher_info');
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page'];
			$result=$model->findSql($querySql);
			$results = $verify->record ();
			if($result){
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
			}else{
				$msg->ResponseMsg (1, '查符合条件的老师', false,1, $prefixJS );
			}
			}else {
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
		if ($result) {
			unset ( $newrow ['user_tid'] );
			$city = $result [0] ['city'];
			$teacher_tid = $newrow ['teacher_tid'];
			$querySql='select teacher_city from teacher_info where tid="'.$teacher_tid.'"';
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			if($result[0]['teacher_city']==$city || $city==全国 ){
			$querySql = "select distinct t.tid, t.teacher_num,t.teacher_name,t.teacher_city,t.teacher_sex,t.teacher_district,t.teacher_town,t.student_grade_max,t.student_grade_min,t.teacher_seniority,t.telephone,t.address,t.graduated_from,t.graduation_date,t.teacher_major,  t.post_status,t.teachers_level,t.teacher_hiredate ,d.teacher_information_1,d.teacher_information_2,d.teacher_information_3,d.teacher_idea
						from teacher_info t, teacher_detail_info d
						where d.teacher_tid=t.tid and  t.tid='".$teacher_tid."'";
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			$verify = new checkCtr ();
			$results = $verify->record ();
			$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', false, 0, $prefixJS );
			}
		} else {
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
		if ($result) {
			$city = $result [0] ['city'];
			$teacher_num = $newrow ['teacher_num'];
			$level = $newrow ['level'];
			$querySql = 'select teacher_city from teacher_info where teacher_num="' . $teacher_num . '"';
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			if ($result [0] ['teacher_city'] == $city || $city == 全国) {
				$conditions = array (
						'teacher_num' => $teacher_num 
				); // 条件查询 字段teacher_num=$teacher_num
				$model = spClass ( 'teacher_info' );
				$result = $model->updateField ( $conditions, 'teachers_level', $level );
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', $result, 0, $prefixJS );
			}
		} else {
			
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
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
		if ($result) {
			$city = $result [0] ['city'];
			$teacher_num = $newrow ['teacher_num'];
			$state = $newrow ['state']; // 老师职位状态 0为实习 1为在职 2为离职
			$date = date ( 'Y-m-d H:i:s', time () ); // 获取当前时间
			$querySql = 'select teacher_city from teacher_info where teacher_num="' . $teacher_num . '"';
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			if ($result [0] ['teacher_city'] == $city || $city == 全国) {
				$conditions = array (
						'teacher_num' => $teacher_num 
				);
				$model = spClass ( 'teacher_info' );
				$result = $model->updateField ( $conditions, 'post_status', $state );
				if ($result) {
					$addSql = "insert teacher_info set dimission_time='".$date."'"; // 老师离职添加老师离职时间
					$model = spClass ( 'teacher_info' );
					$result = $model->runSql ( $addSql );
					$results = $verify->record ();
				}else{
					$msg->ResponseMsg ( 1, '修改失败', false, 0, $prefixJS );
				}
				$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
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
		$verify = new checkCtr ();
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
			$msg->ResponseMsg ( 0, '对不起，您没有权限！', 1, 0, $prefixJS );
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
		if ($result) {
			$city = $result [0] ['city'];
			if (defined ( 'TestVersion' )) { // 如果为测试环境$url地址如下
				$url = "http://testapi.e-teacher.cn/testfile/Teacher_image/$telephone.png";
			} else {
				$url = "http://image.e-teacher.cn/teacher/$telephone.png";
			}
			$newrow ['teacher_image'] = $url;
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '填写信息为空！', null, 0, $prefixJS );
			} else {
				if ($city == $newrow['teacher_city'] || $city==全国 ) { // 教研城市跟老师城市不匹配不能添加
					if ($newrow ['student_grade_max'] < $newrow ['student_grade_min']) { // 最高年级小于最低年级不能添加
					$msg->ResponseMsg ( 1, '最高年级不能小于最低年级！', 1, 0, $prefixJS );
					return;
				}
				if ($newrow ['telephone'] == null) {
					$msg->ResponseMsg ( 1, '手机号不能为空！', 1, 0, $prefixJS );
					return;
				}
				
				$conditions = array (
						'teacher_num' => $newrow ['teacher_num'] 
				); // 判断是否有一样的手机号一样则不能重复添加
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
							$verify = new checkCtr ();
							$results = $verify->record ();
						}
					}
				}
				$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
			}else{
				$msg->ResponseMsg ( 1, '对不起，你没有权限添加非本城市的老师！', 1, 0, $prefixJS );
				return;
				}
		}
		}else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
		}
	}
	// 老师修改密码
	function update() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		unset($newrow['user_tid']);
		$tid = $newrow ['tid'];
		$querySql = "select tid,teacher_city ,login_pwd,from teacher_info where tid=" . $tid;
		$model = spClass ( 'teacher_info' );
		$result1 = $model->findSql ( $querySql );
		if ($result1 [0] ['tid'] == null) {
			$msg->ResponseMsg ( 1, '此老师不存在', 1, 0, $prefixJS );
			return ;
		}
		if($pwd==$result[0]['login_pwd']){
			$msg->ResponseMsg ( 1, '密码与旧密码一致！', 1, 0, $prefixJS );
			return ;	
		}
			$update = "update teacher_info set login_pwd=" . $pwd . "where tid='" . $tid . "'";
			$model = spClass ( 'teacher_info' );
			$result = $model->runSql ( $updateSql );
			$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
		}else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', $result, 0, $prefixJS );
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
		if ($result) {
			unset ( $newrow ['user_tid'] );
			$city = $result [0] ['city'];
			$tid = $newrow ['tid'];
			$telephone = $newrow ['telephone'];
			$querySql = "select tid,teacher_city from teacher_info where tid=" . $tid;
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			if ($result [0] ['teacher_city'] == $city || $city == 全国) {
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
					$verify = new checkCtr ();
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, '填写的信息是空的！', false, 0, $prefixJS );
				}
				
				// return true;
			} else {
				$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', 1, 0, $prefixJS );
			}
		} else {
			
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
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
		if ($result) {
			unset($newrow['user_tid']);
			$city = $result [0] ['city'];
			$tid = $newrow ['tid'];
			$querySql = "select tid,telephone,teacher_city from teacher_info where tid=" . $tid;
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql ( $querySql );
			$telephone = $result [0] ['telephone'];
			if ($result [0] ['teacher_city'] == $city || $city == 全国) {
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
					$verify = new checkCtr ();
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
				}
				// else
				// {
				// $msg->ResponseMsg ( 1, '填写的信息是空的！', false, 0, $prefixJS );
				// }
				
				// return true;
			} else {
				$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', 1, 0, $prefixJS );
			}
		} else {
			
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
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
		$condition = array(
				'telephone'=> $telephone,
				'login_pwd'=> $login_pwd,				
		);
		$model = spClass ( 'teacher_info' );
		$result = $model->find($condition);		
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
		$verify = new checkCtr ();
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
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
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
