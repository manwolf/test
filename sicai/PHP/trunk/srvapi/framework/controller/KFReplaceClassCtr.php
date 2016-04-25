<?php
include_once 'userInfoCtr.php';
include_once 'base/encrypt.php';
/**
 * 功能：客服主管、客服专员操作处理学生、教师的调课申请
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class KFReplaceClassCtr extends userInfoCtr {
	
	/**
	 * 客服查询学生换课申请
	 *
	 * @return boolean
	 */
	function queryApply() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证客服身份
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" ); // 客服专员
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" ); // 客服主管
		if ($result1) {
			$result = $result1;
			$city = $result ['kf_city'];
		} else {
			$result = $result2;
			$city = $result ['kf_admin_city'];
		}
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！ ', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		$model = spClass ( 'user_classes' );
		$querySql = 'SELECT
						a.tid,"学生" AS applyer_type,b.user_name, b.user_grade AS class_content ,
						2*COUNT(ALL e.tid ) AS remainingHour ,
						a.create_time AS apply_time,                         
						CASE a.user_classes 
							WHEN "0" THEN "次月换课"
							WHEN "1" THEN "加课"
							WHEN "2" THEN "临时调课"							
							END AS user_classes,';
		if ($newrow ['service_reply_state'] == 1) { // 当为临时调课时，假显示为已处理
			$querySql .= ' IF(a.user_classes = 2,"已处理", CASE a.service_reply_state 
					WHEN "0" THEN "处理中"
					WHEN "1" THEN "已处理"							
					END) 
				 AS service_reply_state,';
		} else {
			$querySql .= ' CASE a.service_reply_state
					WHEN "0" THEN "处理中"
					WHEN "1" THEN "已处理"
					END
				 AS service_reply_state,';
		}
		$querySql .= ' a.user_classes_need, a.service_reply 						
		FROM 
			user_classes a  LEFT JOIN user_info b ON a.user_tid = b.tid  
			LEFT JOIN order_list c  ON c.user_tid = a.user_tid
		  LEFT JOIN class_list e 	ON e.order_tid  = c.tid		AND e.user_confirm 	= 0  
		WHERE 1 = 1 ';
		if ($city != "全国") {
			$querySql .= ' AND b.user_city = "' . $city . '" ';
		}
		if ($newrow ['user_classes'] != null)
			$querySql .= 'AND a.user_classes 	= "' . $newrow ['user_classes'] . '"';
		if ($newrow ['service_reply_state'] != null)
			$querySql .= 'AND a.service_reply_state 	= "' . $newrow ['service_reply_state'] . '"';
		if ($newrow ['user_name'] != null)
			$querySql .= 'AND b.user_name 	like "%' . $newrow ['user_name'] . '%"';
		if ($newrow ['date'] != null) {
			switch ($newrow ['date']) {
				case 1 : // 当天
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 3 : // 三天内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 2, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 7 : // 一周内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 6, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 8 : // 一周以上
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 6, date ( "Y" ) ) );
					break;
				default : // 全部
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
			}
		} else {
			$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
			$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
		}
		if ($newrow ['date'] != null)
			$querySql .= ' AND  a.create_time >= "' . $start_time . '"  AND  a.create_time <= "' . $end_time . '"';
		
		$querySql .= ' GROUP BY a.tid  ORDER BY a.create_time DESC ';
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = @$model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '成功', $result, $total_page, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, ' 查无此申请，请修改搜索条件！ ', $result, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 客服处理学生次月换课申请、加课申请
	 *
	 * @return boolean
	 */
	function updateApplyState() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证客服身份
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );//客服专员
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );//客服主管
		if ($result1) {
			$result = $result1;
			$city = $result ['kf_city'];
		} else {
			$result = $result2;
			$city = $result ['kf_admin_city'];
		}
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！ ', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		// 过滤输入条件
		$tid = ( integer ) $newrow ['tid'];
		if ($tid <= 0) {
			$msg->ResponseMsg ( 1, ' 学生申请tid 不能为空！ ', false, 0, $prefixKF );
			exit ();
		}
		// 判断是否具备执行资格
		$model = spClass ( $this->tablename );
		$querySql = ' SELECT a.tid,a.user_classes,a.user_tid,b.user_add_class,b.user_nextmonth_state
				FROM user_classes a  LEFT JOIN user_info b ON a.user_tid = b.tid
				WHERE a.tid = "' . $tid . '" ';
		if ($city != "全国") {
			$querySql .= ' AND b.user_city = "' . $city . '" ';
		}
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 该申请不存在！', false, 0, $prefixKF );
			exit ();
		}
		$model = spClass ( 'user_info' );
		switch ($result ['0'] ['user_calsses']) {
			case 1 : // 加课，修改用户可加课状态
				$querySql = ' UPDATE user_info	SET user_add_class = 0 
				WHERE tid =  "' . $result ['0'] ['user_tid'] . '"';
				$result = @$model->runSql ( $querySql );
				break;
			case 0 : // 次月调课，修改用户次月调课申请状态为已处理
				$querySql = ' UPDATE user_info	SET user_nextmonth_state = 2
				WHERE tid =  "' . $result ['0'] ['user_tid'] . '"';
				$result = @$model->runSql ( $querySql );
				break;
		}
		$model = spClass ( 'user_classes' );
		$querySql = ' UPDATE user_classes SET service_reply_state= 1 ';
		if ($newrow ['service_reply'] != null) {
			$querySql .= ' ,  service_reply =  " ' . $newrow ['service_reply'] . '"';
		}
		$querySql .= ' WHERE tid =  "' . $tid . '"';
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, ' 学生申请调课批准失败！ ', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 客服查询教师调休申请
	 *
	 * @return boolean
	 */
	function queryTeacherApply() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证客服身份
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );//客服专员
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );//客服主管
		if ($result1) {
			$result = $result1;
			$city = $result ['kf_city'];
		} else {
			$result = $result2;
			$city = $result ['kf_admin_city'];
		}
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！ ', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		$model = spClass ( 'teacher_schedule' );
		$querySql = 'SELECT	a.tid, b.teacher_name ,a.teacher_apply_date,
						CONCAT(a.schedule_date, " ",a.schedule_time) AS schedule_date,						 
						CASE a.teacher_entry_state 
							WHEN "0" THEN "未申请"
							WHEN "1" THEN "申请中"
							WHEN "2" THEN "已批准"
							END AS teacher_entry_state						
					FROM 
						teacher_schedule a LEFT JOIN teacher_info b ON  a.teacher_tid =b.tid 
					WHERE 
						a.teacher_entry_state != 0	 ';
		if ($city != "全国") {
			$querySql .= ' AND b.teacher_city = "' . $city . '" ';
		}
		if ($newrow ['teacher_entry_state'] != null)
			$querySql .= 'AND a.teacher_entry_state 	= "' . $newrow ['teacher_entry_state'] . '"';
		if ($newrow ['teacher_name'] != null)
			$querySql .= 'AND b.teacher_name 	= "' . $newrow ['teacher_name'] . '"';
		if ($newrow ['date'] != null) {
			switch ($newrow ['date']) {
				case 1 : // 当天
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 3 : // 三天内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 2, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 7 : // 一周内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 6, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 8 : // 一周以上
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 6, date ( "Y" ) ) );
					break;
				default : // 全部
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
			}
		} else {
			$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
			$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
		}
		if ($newrow ['date'] != null)
			$querySql .= ' AND  a.teacher_apply_date >= "' . $start_time . '"  AND  a.teacher_apply_date <= "' . $end_time . '"';
		$querySql .= ' GROUP BY a.tid  ORDER BY a.teacher_apply_date DESC';
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '成功', $result, $total_page, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, ' 查无此申请，请修改搜索条件！ ', $result, $total_page, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 客服更改教师调休申请状态
	 *
	 * @return boolean
	 */
	function updateTeacherApplyState() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证客服身份
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );//客服专员
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );//客服主管
		if ($result1) {
			$result = $result1;
			$city = $result ['kf_city'];
		} else {
			$result = $result2;
			$city = $result ['kf_admin_city'];
		}
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！ ', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		// 过滤输入条件
		$tid = ( integer ) $newrow ['tid']; // 保证输入的tid为整数
		if ($tid <= 0) {
			$msg->ResponseMsg ( 1, ' 教师tid不能为空！ ', false, 0, $prefixKF );
			exit ();
		}
		$model = spClass ( $this->tablename );
		$querySql = ' SELECT a.tid FROM teacher_schedule a LEFT JOIN teacher_info b ON  a.teacher_tid =b.tid 
				WHERE a.tid = "' . $tid . '" ';
		if ($city != "全国") {
			$querySql .= ' AND b.teacher_city = "' . $city . '" ';
		}
		$result = $model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 该申请不存在！', false, 0, $prefixKF );
			exit ();
		}
		$model = spClass ( 'teacher_schedule' );
		$result = @$model->findBy ( 'tid', $tid );
		if ($result ['time_busy'] == 1) {
			$msg->ResponseMsg ( 1, ' 该教师的这个时间段已经被占用，因此该申请不能被批准！ ', false, 0, $prefixKF );
			exit ();
		}
		$model = spClass ( 'teacher_schedule' );
		$querySql = ' UPDATE teacher_schedule SET teacher_entry_state = 2 ,time_busy = 1 ' . ' WHERE tid =  "' . $tid . '"';
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, '成功', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, ' 批准申请失败！ ', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}

/**
 * END
 */
}

