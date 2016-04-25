<?php
include_once 'base/crudCtr.php';
/**
 * 功能：学生确认订单及评价
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class usersCourseRecord extends crudCtr {
	public function __construct() {
		$this->tablename = 'user_evaluation';
	}
	// 家长确认后的所有订单
	function userConfirmedCourse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if (! $newrow) {
			
			$msg->ResponseMsg ( 1, '没有获取到学生id号', $result, 0, $prefixJS );
			exit;
		} else {
			// 查询所有家长确认过后的课程记录
			$querySql = 'select c.user_evaluation_state,u.tid as user_tid,c.tid as class_tid,o.tid as order_tid,
		          		t.teacher_name,u.user_name,c.class_start_date,c.class_start_time,o.class_content,
		          		c.teacher_confirm,c.user_confirm,c.teacher_grammar,e.user_evaluation,e.teacherOntime,e.lessonPlanReady,e.classroomInteraction
		          		 from user_info u left join order_list o on o.user_tid=u.tid left join 
		          		teacher_info t on o.teacher_tid=t.tid left join class_list c on c.order_tid=o.tid 
		          		left join user_evaluation e on e.class_tid=c.tid where c.user_confirm=1 and u.tid=' . $tid;
			
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, '您暂时没有课程记录！', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	// 家长未确认的所有订单
	function userUnrecognizedCourse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if (! $newrow) {
			
			$msg->ResponseMsg ( 1, '没有获取到学生id号', $result, 0, $prefixJS );
			exit;
		} else {
			// 获取所有家长未确认的课程记录
			$querySql = 'select c.user_evaluation_state,u.tid as user_tid,c.tid as class_tid,o.tid as order_tid,t.teacher_name,u.user_name,c.class_start_date,c.class_start_time,o.class_content,c.teacher_confirm,c.user_confirm,c.teacher_grammar
					   from teacher_info t,user_info u,order_list o,class_list c
					   where t.tid=o.teacher_tid and u.tid=o.user_tid and o.tid=c.order_tid  and c.user_confirm=0 and u.tid=' . $tid;
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				// echo $class_start_time;
				// exit;
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, '您暂时没有课程记录！', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	
	// 家长确认 user_confirm 返回1
	function updateClassInfoCtr() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// $updateSql='update teacher_info set ';
		
		$tid = $newrow ['tid'];
		if ( $tid == '') {
			$msg->ResponseMsg ( 1, '您必须提供一个主键,tid', $result, 0, $prefixJS );
			exit;
		}
			// 家长确认后修改课程状态
			$updateSql = 'update class_list set user_confirm=1 where tid=' . $tid;
			
			$model = spClass ( $this->tablename );
			
			$result = $model->runSql ( $updateSql );
			
			if($result){
				
				//根据课程编号查询学生ID
				$querySql='select o.user_tid from order_list o, class_list c  where  c.order_tid=o.tid and c.tid='.$tid;
				$model = spClass ( $this->tablename );
				$resultqu = $model->findSql ( $querySql );
				//若没有查询到该课程相关信息
				if(!$resultqu[0]['user_tid']){
					$msg->ResponseMsg ( 1, '没有发现该课程！', $result, 0, $prefixJS );
					exit;
				}
				//给学生添加200积分
				$updateSql = 'update user_info set user_exchange_points=user_exchange_points+200 where tid=' . $resultqu[0]['user_tid'];
				$model = spClass ( $this->tablename );
				$resultup = $model->runSql ( $updateSql );
// 				echo $updateSql;
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			}else{
				$msg->ResponseMsg ( 1, '确认失败！', $result, 0, $prefixJS );
			}
			
		
		
		return true;
	}
	// 家长对老师评价 每次确认后。。。 修改class_list表
	function addUsersEvaluation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if ( $tid =='') {
			$msg->ResponseMsg ( 1, '没有获取取到课程号', null, 0, $prefixJS );
			exit;
		} else {
			// 记录家长评价
			
			$addSql = 'insert user_evaluation set ';
			foreach ( $newrow as $k => $v ) {
				// 将传过来的tid转为class_tid
				if ($k == 'tid') {
					$k = 'class_tid';
					// continue;
				}
				$addSql = $addSql . $k . '="' . $v . '",';
			}
			$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
			// echo $addSql;
			// exit;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $addSql );
			// $querySql='';
			if ($result <= 0) {
				return;
			} else {
				// 修改评价状态
				$tid = $newrow ['tid'];
				$updateSql = 'update class_list set user_evaluation_state=1 where tid=' . $tid;
				
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				// $msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	
	// 统计家长剩余未上课的所有课时数
	function userClassSurplusNum() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if ($tid == '' && $tid==null) {
			
			$msg->ResponseMsg ( 1, '没有发现该学生id！', $result, 0, $prefixJS );
			exit;
		}
		// 统计家长剩余未上课的所有课时数
		$querySql = 'select count(c.user_confirm)*2  as userClassSurplusNum
					   from user_info u,order_list o,class_list c
					   where  u.tid=o.user_tid and o.tid=c.order_tid and c.user_confirm=0 and u.tid=' . $tid;
		
		// echo $querySql;
		// exit;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if ($result = $model->findSql ( $querySql )) {
			// echo $class_start_time;
			// exit;
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '您暂时没有任何课程！', $result, 0, $prefixJS );
		}
		return true;
	}

//     //统计家长剩余未上课的所有课时数
//     function userClassSurplusNum()
//     {
//     	$msg = new responseMsg ();
//     	$capturs = $this->captureParams ();
//     	$prefixJS = $capturs ['callback'];
//     	$token = $capturs ['token'];
//     	$newrow = $capturs ['newrow'];
//     	$tid = $newrow['tid'];
//     	if(!$newrow)
//     	{
    	
//     		$msg->ResponseMsg ( 1, '没有发现该学生！', $result, 0, $prefixJS );
//     		exit;
//     	}
//     	//统计家长剩余未上课的所有课时数
//     	$querySql='select count(c.user_confirm)*2  as userClassSurplusNum
// 					   from user_info u,order_list o,class_list c
// 					   where  u.tid=o.user_tid and o.tid=c.order_tid and
//     			       c.user_confirm=0 and u.tid='.$tid;
    		
//     	// 				echo $querySql;
//     	// 				exit;
//     	$model = spClass ( $this->tablename );
//     	$result = $model->findSql ($querySql);
//     	if($result = $model->findSql ($querySql))
//     	{
//     		// 				echo $class_start_time;
//     		// 				exit;
//     		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
//     	}
//     	else
//     	{
//     		$msg->ResponseMsg ( 1, '您暂时没有任何课程！', $result, 0, $prefixJS );
//     	}
//     	return true;
//     }

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