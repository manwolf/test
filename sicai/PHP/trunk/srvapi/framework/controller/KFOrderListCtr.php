<?php
include_once 'userInfoCtr.php';
include_once 'base/encrypt.php';
/**
 * 功能：客服主管、客服专员对已有客户的订单进行增删改查操作
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class KFOrderListCtr extends userInfoCtr {
	
	/**
	 * 客服查询订单
	 *
	 * @return boolean
	 */
	function queryOrder() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证身份
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
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		if($city == "全国"){
			$city = $newrow['user_city'];
		}
		// 执行查询操作
		$model = spClass ( 'order_list' );
		//查询已有客户数据：只负责查询一对一订单
		$querySql = 'SELECT	a.tid,a.teacher_tid,
						CONCAT(a.order_date," ", a.order_time) AS class_time, 
						a.pay_done,a.order_state,a.create_time,a.class_content,												
						CASE a.class_way 
							WHEN "0" THEN "老师上门"
							WHEN "1" THEN "附近咖啡馆"
							WHEN "2" THEN "双方约定"	
							WHEN "3" THEN "在线授课"	
							END AS class_way,						
						a.order_address, a.order_phone,
		 		b.user_name,b.user_city,b.user_area,
				IF(a.high_quality_courses_tid,"未分配",c.teacher_name) AS teacher_name,	
				IF(a.high_quality_courses_tid,"短期课程",d.class_content) AS class_count,
				IF(a.high_quality_courses_tid,i.high_quality_name,"传统课程") AS high_quality_name,				
				d.class_hour,
		 		2*COUNT(DISTINCT e.tid ) AS remainingHour ,
         COUNT(DISTINCT f.tid) AS return_count,
				CASE a.invitation_code
						WHEN "" THEN "未使用"
						ELSE h.invitation_discount
				END AS invitation
		 	  FROM   order_list   a 
				LEFT JOIN class_discount d	 ON  a.class_discount_tid= d.tid	
				LEFT JOIN teacher_info c  ON a.teacher_tid 			= c.tid	
				LEFT JOIN user_info    b  ON a.user_tid 				= b.tid
				LEFT JOIN kf_return_info f 	ON f.return_phone = b.telephone							
				LEFT JOIN class_list e 		ON e.order_tid  = a.tid   AND e.user_confirm =0 
				LEFT JOIN invitation_use_list g ON a.invitation_code = g.invitation_code
				LEFT JOIN invitation_info  h ON g.invitation_info_tid = h.tid		
				LEFT JOIN high_quality_courses i ON i.tid = a.high_quality_courses_tid
				WHERE a.tid > 0 AND user_spelling_lesson_tid IS NULL 	'	;
		
		// 过滤输入条件
		//订单课程的城市
		if ($city != null and $city != '全国')	
			$querySql .= ' AND IF(a.high_quality_courses_tid , i.city = "' . $city . '", d.class_city= "' . $city . '" )';
		//用户所在地区
		if ($newrow ['user_area'] != null)
			$querySql .= ' AND b.user_area 	= "' . $newrow ['user_area'] . '"';
		//支付是否完成，0：未支付；1：已支付
		if ($newrow ['pay_done'] != null)
			$querySql .= ' AND a.pay_done 	= "' . $newrow ['pay_done'] . '"';
		//订单流转状态：0：已完成，1：未排课，2：已取消，3：已排课
		if ($newrow ['order_state'] != null)
			$querySql .= ' AND a.order_state 	= "' . $newrow ['order_state'] . '"';
		//订单的年级信息： 0：学前、1：小学1年级、2：小学2年级。。。13：全部
		if ($newrow ['class_content'] != null and $newrow ['class_content'] != 13) {
			if ($newrow ['class_content'] >= 3) {
				$querySql .= ' AND a.class_content	LIKE CONCAT("%",' . $newrow ['class_content'] . ',"%")';
			} elseif(0 === $newrow ['class_content'] ){
				$querySql .= ' AND a.class_content NOT LIKE "%年级" ';
			}else {
				$querySql .= ' AND a.class_content LIKE CONCAT("%小学",' . $newrow ['class_content'] . ',"%")';
			}
		}
		//上课方式：0：老师上门，1：附近咖啡馆，2：第三方约定地点，3：在线授课
		if ($newrow ['class_way'] != null)
			$querySql .= ' AND a.class_way 	= "' . $newrow ['class_way'] . '"';
		//用户姓名
		if ($newrow ['user_name'] != null)
			$querySql .= ' AND b.user_name 	LIKE "%' . $newrow ['user_name'] . '%" ';
		//教师姓名
		if ($newrow ['teacher_name'] != null)
			$querySql .= ' AND c.teacher_name 	LIKE "%' . $newrow ['teacher_name'] . '%"';
		//是否分配教师  1：已分配  2：未分配（或者为空）
		if ($newrow ['having_teacher'] != null){
			switch($newrow ['having_teacher']){
				case 1:
					$querySql .= ' AND a.teacher_tid > 0';
					break;
				case 2:
					$querySql .= ' AND a.teacher_tid 	<= 0';
					break;
		}
		}			
		//课程选择：1：免费试课；2：购买套餐；3：短期课程		
		switch($newrow ['class_count']){
			case 1:
				$querySql .= " AND d.class_content 	LIKE '%免费试课%' ";
				break;
			case 2:
				$querySql .= " AND d.class_content 	NOT LIKE '%免费试课%' ";
				break;
			case 3:
				$querySql .= " AND a.class_types 	 = 1 ";
				break;
		}			
		//订单联系电话
		if ($newrow ['telephone'] != null)
			$querySql .= ' AND a.order_phone		= "' . $newrow ['telephone'] . '"';
		//订单号tid
		if ($newrow ['tid'] != null)
			$querySql .= ' AND a.tid 					= "' . $newrow ['tid'] . '"';		
		//下单时间： 1：今天，7：一周内，30：本月，90：本季度
		if ($newrow ['date'] != null) {
			switch ($newrow ['date']) {
				case 1 : // 当天
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 7 : // 一周内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - 6, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 30 : // 一个月内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), 1, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				case 90 : // 一个季度内
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ) - 2, 1, date ( "Y" ) ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
				default : // 全部
					$start_time = date ( "Y-m-d ", mktime ( 0, 0, 0, 1, 1, 1970 ) );
					$end_time = date ( "Y-m-d ", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
					break;
			}
			$querySql .= ' AND a.create_time 	>="' . $start_time . '"	AND a.create_time 	<="' . $end_time . '"';
		} 
		//按照订单号分组
		$querySql .= ' GROUP BY a.tid ';
		//剩余课时： 0：0，1：1-10，2：11-20，3：21-30，4：31-40	，5：41-50，6：51以上
		if ($newrow ['ramainingHour'] != null) {
			switch ($newrow ['ramainingHour']) {
				case 0 : // 剩余0课时
					$ramainingHourMin = 0;
					$ramainingHourMax = 0;
					break;
				case 1 : // 剩余1-10课时
					$ramainingHourMin = 1;
					$ramainingHourMax = 10;
					break;
				case 2 : // 剩余11-20课时
					$ramainingHourMin = 11;
					$ramainingHourMax = 20;
					break;
				case 3 : // 剩余21-30课时
					$ramainingHourMin = 21;
					$ramainingHourMax = 30;
					break;
				case 4 : // 剩余31-40课时
					$ramainingHourMin = 31;
					$ramainingHourMax = 40;
					break;
				case 5 : // 剩余41-50课时
					$ramainingHourMin = 41;
					$ramainingHourMax = 50;
					break;
				case 6 : // 剩余50课时以上
					$ramainingHourMin = 51;
					$ramainingHourMax = 10000;
					break;
				default : // 全部
					$ramainingHourMin = 0;
					$ramainingHourMax = 10000;
					break;
			}
		} else {
			$ramainingHourMin = 0;
			$ramainingHourMax = 10000;
		}
		$querySql .= ' HAVING  d.class_hour-COUNT(DISTINCT e.tid )  >= ' . $ramainingHourMin . " AND d.class_hour-COUNT(DISTINCT e.tid )  <= " . $ramainingHourMax;
		//回放次数： 0：0,1：1,2：2，3：3,4：4,5：大于5
		if ($newrow ['return_count'] != null) {
			if ($newrow ['return_count'] <= 4) {
				$querySql .= " AND COUNT(DISTINCT f.tid) = " . $newrow ['return_count'];
			} else {
				$querySql .= " AND COUNT(DISTINCT f.tid) >= " . $newrow ['return_count'];
			}
		}
		//按照下单时间排序
		$querySql .= ' ORDER BY a.create_time DESC ';
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		
			
		if ($result) {
			$ifexport = $newrow['ifexport'];
			$filetype = $newrow['filetype'];
			if($ifexport === null){
				$ifexport = 0;
			}
			if($ifexport == 1){
				$this->exportOrderList($result,$filetype);
				exit ();
			}else{
			$msg->ResponseMsg ( 0, '查询订单成功！', $result, $total_page, $prefixKF );
			exit ();
			}
		} else {
			$msg->ResponseMsg ( 1, ' 查无此订单，请调整搜索条件！ ', $result, 0, $prefixKF );
			exit ();
		}
		
		return true;
	}
	
	/**
	 * 客服主管或客服专员针对已支付订单进行转入操作
	 * 已支付（未排课）-> 已排课
	 * 已支付（已排课）-> 已完成
	 *
	 * @return boolean
	 */
	function switchOrder() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
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
		// 判断限制执行条件
		$model = spClass ( 'order_list' );
		$querySql = ' SELECT a.* FROM order_list a 
				LEFT JOIN teacher_info b  ON a.teacher_tid = b.tid  
				WHERE a.tid = "' . $tid . '" AND a.pay_done = 1 AND a.order_state != 2 AND a.order_state != 0';
		if ($city != "全国") {
			$querySql .= ' AND b.teacher_city = "' . $city . '" ';
		}
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 该订单无法进行转入操作！', false, 0, $prefixKF );
			exit ();
		}
		$order_state_old = $result ['0'] ['order_state'];
		if ($order_state_old == 1) {
			$order_state = 3;
			$switch_msg = ' 转入已排课';
		} elseif ($order_state_old == 3) {
			$order_state = 0;
			$switch_msg = ' 转入已完成';
		} else {
			$msg->ResponseMsg ( 1, '该订单不能进行转入操作！', false, 0, $prefixKF );
			exit ();
		}
		$querySql = ' UPDATE order_list SET order_state= "' . $order_state . '"  
				WHERE tid =  "' . $tid . '"';
		$model = spClass ( 'order_list' );
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, $switch_msg . '操作成功！', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, $switch_msg . '操作失败！', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 客服主管取消订单
	 * 任何状态 -> 已取消
	 *
	 * @return boolean
	 */
	function cancelOrder() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" ); // 客服主管
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误，权限不足，只有客服主管才能取消订单！ ', false, 0, $prefixKF );
			exit ();
		}
		$city = $result ['kf_admin_city'];
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		$querySql = ' SELECT a.* FROM order_list a 
				LEFT JOIN teacher_info b  ON a.teacher_tid = b.tid  
				WHERE a.tid = "' . $tid . '"';
		if ($city != "全国") {
			$querySql .= ' AND b.teacher_city = "' . $city . '" ';
		}
		$model = spClass ( 'order_list' );
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 该订单不存在！', false, 0, $prefixKF );
			exit ();
		}
		$querySql = ' UPDATE order_list SET order_state= 2  WHERE tid =  "' . $tid . '"';
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, '取消订单成功', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, ' 取消订单失败！', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 客服更改订单状态异常状态
	 *
	 * @return boolean
	 */
	function updateNormalState() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
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
		// 过滤输入条件
		$normal_state = ( integer ) $newrow ['normal_state'];
		$tid = ( integer ) $newrow ['tid'];
		// 判断操作条件
		$model = spClass ( 'order_list' );
		$querySql = ' SELECT a.* 
				FROM order_list a LEFT JOIN teacher_info b  ON a.teacher_tid = b.tid  
				WHERE a.tid = "' . $tid . '" AND b.teacher_city = "' . $city . '"';
		$result = $model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 该订单不能进行标记或取消异常操作！', false, 0, $prefixKF );
			exit ();
		}
		$querySql = ' UPDATE order_list SET normal_state= "' . $normal_state . '"  WHERE tid =  "' . $tid . '"';
		$result = $model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, '标记成功', $result, 0, $prefixKF );
		} else {
			$msg->ResponseMsg ( 1, ' 标记订单异常状态失败！ ', false, 0, $prefixKF );
			exit ();
		}
		return true;
	}
	
	/**
	 * 客服手动添加订单
	 */
	function addOrder() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" ); // 客服主管
		if (! $result) {
			$msg->ResponseMsg ( 1, '权限不足，只有客服主管才能增加订单！ ', false, 0, $prefixKF );
			exit ();
		}
		$city = $result ['kf_admin_city'];
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $prefixKF );
			exit ();
		}
		// 纠正输入的值为undefined未定义的参数
		foreach ( $newrow as $key => $value ) {
			if ($value == "undefined") {
				$newrow [$key] = "";
			}
		}
		// 判断输入信息的有效性
		if ($newrow ['user_name'] == null or $newrow ['user_area'] == null or $newrow ['teacher_name'] == null or $newrow ['class_content'] == null or $newrow ['class_way'] === null or $newrow ['order_address'] == null or $newrow ['order_phone'] == null or $newrow ['order_money'] === null or $newrow ['class_count'] == null or $newrow ['pay_type'] === null) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $prefixKF );
			exit ();
		}
		// 过滤输入条件
		$class_way = ( integer ) $newrow ['class_way']; // 保证上课方式为数字
		$order_money = ( float ) $newrow ['order_money']; // 保证订单金额是双精度数字
		$pay_type = ( integer ) $newrow ['pay_type']; // 保证支付类型为数字
		$order_date = date ( 'Y-m-d', strtotime ( $newrow ['order_date'] ) ); // 保证免费试课的订单上课日期是YYYY-mm-dd的格式
		$order_time = $newrow ['order_time']; // 免费试课的上课时间
		$user_name = $newrow ['user_name']; // 学生姓名：字符串
		$user_area = $newrow ['user_area']; // 学生的地区：字符串
		$teacher_name = $newrow ['teacher_name']; // 教师姓名：数据中已有的教师姓名
		$class_content = ( integer ) $newrow ['class_content']; // 学生当前年级：数字0、1、2
		$order_address = $newrow ['order_address']; // 订单联系地址：字符串
		$order_phone = $newrow ['order_phone']; // 订单联系手机号：11位数字
		$class_count = $newrow ['class_count']; // 课程选择：字符串
		                                        // 只有全国客服主管才能选择不同的城市来添加订单；普通客服主管只能添加本城市的订单
		if ($city == '全国') {
			if ($newrow ['user_city'] == null) {
				$msg->ResponseMsg ( 1, '订单的城市信息不能为空！', false, 0, $prefixKF );
				exit ();
			} else {
				$city = $newrow ['user_city'];
			}
		} // 判断教师姓名与城市、年级信息是否匹配
		$conditions = " teacher_name  = '" . $teacher_name . "' AND post_status = 1 AND teacher_city  = '" . $city . "' AND student_grade_min <= " . $class_content . " AND student_grade_max >= " . $class_content;
		$gb = spClass ( 'teacher_info' ); // 初始化teacher_info模型类
		$result = $gb->find ( $conditions ); // 查找
		if (! $result) {
			$msg->ResponseMsg ( 1, '该教师与城市、教授年级范围不匹配，或者非在职！', false, 0, $prefixKF );
			exit ();
		} else {
			$teacher_tid = $result ['tid'];
		}
		
		// 判断课程与城市信息是否匹配
		$sql = 'SELECT * FROM class_discount WHERE class_city= "' . $city . '" 
				AND	class_content = "' . $class_count . '"';
		$gb = spClass ( 'class_discount' ); // 初始化class_discount模型类
		$result = $gb->findSql ( $sql ); // 查找
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 该课程与城市信息不匹配！', false, 0, $prefixKF );
			exit ();
		} else {
			$class_discount_tid = $result ['0'] ['tid'];
		}
		// 只有免费试课的订单才会直接填写上课日期和时间
		if ($class_count == '免费试课') {
			$order_type = 0; // 订单类型：免费
			if ($order_date <= "1970-01-01" or $order_time == null) {
				$msg->ResponseMsg ( 1, '请输入免费试课订单的上课日期与时间！', false, 0, $prefixKF );
				exit ();
			}
			//如果免费试课，则判断该教师的这段时间是否为忙
			$findSql = ' SELECT tid
				FROM teacher_schedule
				WHERE time_busy = 0 AND schedule_date = "' . $order_date . '"
						AND schedule_time = "' . $order_time . '" AND  teacher_tid =' . $teacher_tid;
			$result = $gb->findSql( $findSql ); // 查找
			if (!$result OR $order_date <= date ( "Y-m-d",time () ) ) {
				$msg->ResponseMsg ( 1, '该教师的这段时间不能被预订，请选择24小时以后的其他时间！', false, 0, $prefixKF );
				exit ();
			}
			
		} else {
			$order_date = "";
			$order_time = "";
			$order_type = 1; // 订单类型：付费
		}
		// 根据姓名与手机号判断该用户是否已经注册过
		$gb = spClass ( 'user_info' ); // 初始化user_info模型类
		$conditions = array ( // PHP的数组
				'user_name' => $user_name,
				'telephone' => $order_phone 
		);
		$result = $gb->find ( $conditions ); // 查找
		if ($result) {			
			$user_msg = '已有客户';
			$user_tid = $result ['tid'];	
			//如果免费试课，判断用户以免费使用次数 为0时可以继续下单
			$query = 'SELECT user_free_num 
					FROM  user_info 
					WHERE user_free_num = 0 AND tid =' . $user_tid;
			$gb = spClass ( 'user_info' ); // 初始化user_info模型类
			$user_free_num_result = $gb->findSql ( $query );
			if (!$user_free_num_result ) {
				$msg->ResponseMsg ( 1, '该学生已经试用过免费课程，每个用户只能使用1次免费课程！', false, 0, $prefixKF );
				exit ();
			}			
			
			$row = array ( // PHP的数组					
					'user_grade' => $class_content,
					'user_city' => $city,
					'user_area' => $user_area,					
			);			
			$conditions = array('tid'=> $user_tid);
			@$gb->update($conditions, $row); // 进行更新操作			
		} else {
			$user_msg = '新增客户';			
			// 未注册用户信息表
			$new = array ( // PHP的数组
					'user_name' => $user_name,
					'user_grade' => $class_content,
					'user_city' => $city,
					'user_area' => $user_area,
					'telephone' => $order_phone 
			);
			$user_tid = @$gb->create ( $new ); // 进行新增操作
		}
		if (! $user_tid) {
			$msg->ResponseMsg ( 1, ' 客户信息添加失败！ ', false, 0, $prefixKF );
			exit ();
		}		
		// 客户信息添加成功后进行添加订单操作
		$new = array ( // PHP的数组
				'user_tid' => $user_tid,
				'order_date' => $order_date,
				'order_time' => $order_time,
				'teacher_tid' => $teacher_tid,
				'order_money' => $order_money,
				'order_state' => '1',
				'order_type' => $order_type,
				'order_address' => $order_address,
				'order_phone' => $order_phone,
				'class_way' => $class_way,
				'class_content' => $class_count,
				'class_discount_tid' => $class_discount_tid 
		);
		$gb = spClass ( 'order_list' ); // 初始化order_list模型类
		$order_tid = @$gb->create ( $new ); // 进行新增订单操作 ，返回新增订单的tid
		if (! $order_tid) {
			$msg->ResponseMsg ( 1, $user_msg . ' 新增订单失败！ ', false, 0, $prefixKF );
			exit ();
		}
		// 订单信息添加成功后进行添加支付操作
		$new = array ( // PHP的数组
				'order_tid' => $order_tid,
				'pay_type' => $pay_type,
				'pay_done' => '1' 
		);
		$gb = spClass ( 'pay_list' ); // 初始化模型类
		$pay_result = @$gb->create ( $new ); // 进行新增支付记录的操作
		if (! $pay_result) {
			$msg->ResponseMsg ( 1, $user_msg . ' 新增订单支付失败！ ', false, 0, $prefixKF );
			exit ();
		}
		// 免费试课的订单类型需要进行特殊操作
		if (0 == $order_type) {
			$new = array ( // PHP的数组
					'order_tid' => $order_tid,
					'class_start_date' => $order_date,
					'class_start_time' => $order_time,
					'class_no' => '1' 
			);
			$gb = spClass ( 'class_list' ); // 初始化模型类
			$class_list_result = @$gb->create ( $new ); // 进行新增课程的操作
			if (! $class_list_result) {
				$msg->ResponseMsg ( 1, $user_msg . '新增免费试课订单的自动排课失败！ ', false, 0, $prefixKF );
				exit ();
			} else {
				// 更新免费试课的订单状态为已排课
				$querySql = ' UPDATE order_list SET order_state= 3 WHERE tid =  "' . $order_tid . '"';
				$gb = spClass ( 'order_list' ); // 初始化模型类
				$result = @$gb->runSql ( $querySql );
				$affectedRows = @$gb->affectedRows ();
				if ($affectedRows < 1) {
					$msg->ResponseMsg ( 1, $user_msg . '更新免费试课订单的已排课状态失败！ ', false, 0, $prefixKF );
					exit ();
				}
				//更新用户免费试课次数为1
				$querySql = ' UPDATE user_info SET user_free_num = 1 WHERE tid =  "' . $user_tid . '"';
				$gb = spClass ( 'user_info' ); // 初始化模型类
				$result = @$gb->runSql ( $querySql );
				$affectedRows = @$gb->affectedRows ();
				if ($affectedRows < 1) {
					$msg->ResponseMsg ( 1, $user_msg . '更新免费试课订单的使用次数失败！ ', false, 0, $prefixKF );
					exit ();
				}
			}
		}
		$msg->ResponseMsg ( 0, $user_msg . '新增订单支付成功！', $order_tid, 0, $prefixKF );
	}
	
	/**
	 * 客服人员查询本城市的在职教师姓名列表，方便客服添加订单时制作教师姓名下拉菜单
	 */
	public function getTeacherName() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证身份
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
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		if ($city == "全国") {
			$city = $newrow ['user_city'];
		} else {
			/*
			 * if ($city != $newrow ['user_city']) {
			 * $msg->ResponseMsg ( 1, '您无权操作其他城市！', false, 0, $callback );
			 * exit ();
			 * }
			 */
		}
		// 执行查询操作
		// 查询本城市的在职教师
		$querySql = 'SELECT tid,teacher_name FROM teacher_info
				WHERE post_status = 1 AND teacher_city ="' . $city . '"
						ORDER BY teacher_name';
		$gb = spClass ( 'teacher_info' );
		$result = @$gb->findSql ( $querySql ); // 查找
		$msg->ResponseMsg ( 0, '查询成功！', $result, 0, $callback );
	}
	
	/**
	 * 客服人员查询本城市的课程信息列表，方便客服添加订单时制作课程信息的下拉菜单
	 */
	public function getCourse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证身份
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
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		if ($city == "全国") {
			$city = $newrow ['user_city'];
		} else {
			/*
			 * if ($city != $newrow ['user_city']) {
			 * $msg->ResponseMsg ( 1, '您无权操作其他城市！', false, 0, $callback );
			 * exit ();
			 * }
			 */
		}
		// 执行查询操作
		// 查询本城市的课程信息列表
		$querySql = 'SELECT tid,class_content FROM class_discount
				WHERE class_city ="' . $city . '"
						ORDER BY class_content';
		$gb = spClass ( 'class_discount' );
		$result = @$gb->findSql( $querySql ); // 查找
		$msg->ResponseMsg ( 0, '查询成功！', $result, 0, $callback );
	}
	
	/**
	 * 客服人员根据城市和年级查询课程价格
	 */
	public function getCoursePrice() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_grade = $newrow ['class_content']; // 学生年级
		$class_count = $newrow ['class_count']; // 课程信息
		                                        // 验证身份
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
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		if ($city == "全国") {
			$city = $newrow ['user_city'];
		} else {
			/*
			 * if ($city != $newrow ['user_city']) {
			 * $msg->ResponseMsg ( 1, '您无权操作其他城市！', false, 0, $callback );
			 * exit ();
			 * }
			 */
		}
		// 执行查询操作
		// 查询本城市的对应年级的课程总价格
		// 查询本城市的对应年级的课程单价
		$querySql = 'SELECT class_price FROM class_price
				WHERE class_grade ="' . $user_grade . '" AND  class_city ="' . $city . '"
						LIMIT 1';
		$gb = spClass ( 'class_price' );
		$result = @$gb->findSql( $querySql ); // 查找
		if (! $result) {
			$msg->ResponseMsg ( 1, '年级与城市不匹配！', false, 0, $callback );
			exit ();
		} else {
			$class_price = $result ['0'] ['class_price'];
		}
		// 查询本城市的对应课程的打折信息、课程次数
		$querySql = 'SELECT ' . $class_price . ' * class_count * class_disc AS course_totle_price
				FROM class_discount
				WHERE class_content ="' . $class_count . '" AND  class_city ="' . $city . '"
						LIMIT 1';
		$gb = spClass ( 'class_discount' );
		$result = @$gb->findSql( $querySql ); // 查找
		if (! $result) {
			$msg->ResponseMsg ( 1, '课程与城市不匹配！', false, 0, $callback );
			exit ();
		} else {
			$msg->ResponseMsg ( 0, '查询成功！', $result, 0, $callback );
		}
	}
	
	/**
	 * 为订单分配教师
	 */
	public function addOrderTeacher(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$order_tid = $newrow ['order_tid']; // 获取订单的tid
		$teacher_tid = $newrow ['teacher_tid']; // 获取教师的tid
		// 验证身份
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
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $prefixKF );
			exit ();
		}
		if ($city == null) {
			$msg->ResponseMsg ( 1, '该客服人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		
		// 查询订单号是否存在，是否已经被分配教师，是否属于短期课程
		$querySql = 'SELECT a.tid,b.city FROM order_list a 
				LEFT JOIN high_quality_courses b ON a.high_quality_courses_tid = b.tid  
				WHERE a.tid = "' . $order_tid .'" AND (a.teacher_tid <= 0 OR a.teacher_tid IS NULL)
						AND a.high_quality_courses_tid > 0 ';
		$gb = spClass ( 'order_list' );
		$orderResult = @$gb->findSql( $querySql ); // 查找		
		if (!$orderResult) {
			$msg->ResponseMsg ( 1, '此订单不能分配教师！', false, 0, $callback );
			exit ();
		} 
		// 查询教师是否存在，是否在职
		$querySql = 'SELECT teacher_city FROM teacher_info				
				WHERE post_status = 1 AND tid ="' . $teacher_tid .'" ';
		$gb = spClass ( 'teacher_info' );
		$teacherResult = @$gb->findSql( $querySql ); // 查找
		if (!$teacherResult) {
			$msg->ResponseMsg ( 1, '查无此教师！', false, 0, $callback );
			exit ();
		}
		//判断该订单课程与所选教师是否在同一座城市
		if($orderResult['0']['city'] <> $teacherResult['0']['teacher_city']){
			$msg->ResponseMsg ( 1, '该订单课程与所选教师不在同一座城市！', false, 0, $callback );
			exit ();
		}
		//判断客服主管
		if ($city !== "全国") {
			if($city != $orderResult['0']['city']){
				$msg->ResponseMsg ( 1, '您无权操作其他城市的订单！', false, 0, $callback );
				exit ();
			}
		}
		//为该订单分配教师
		$runSql = 'UPDATE order_list SET teacher_tid = "'. $teacher_tid.'"
				WHERE tid ="' . $order_tid .'" ';
		$gb = spClass ( 'order_list' );
		$updateResult = @$gb->runSql( $runSql ); // 查找
		$affectedRows = @$gb->affectedRows ();
		if ($affectedRows < 1) {
			$msg->ResponseMsg ( 1, '分配教师失败！ ', false, 0, $callback );
			exit ();
		}else{
			$msg->ResponseMsg ( 0, '分配教师成功！ ', $updateResult, 0, $callback );
		}
	}
	
	/**
	 * 导出文件
	 * @param string $result 二维数组
	 * @param string $filetype 文件扩展名
	 */
	public  function exportOrderList($result='',$filetype='xls'){
		switch ($filetype) {
			case "doc" : // word文档文件
				header ( "Content-type:application/vnd.ms-word" );
				header ( "Content-Disposition:attachment;filename= eTeacher订单.doc" );
				break;
			case "txt" : // txt记事本文件
				header ( "Content-type:text/plain" );
				header ( "Content-Disposition:attachment;filename= eTeacher订单.txt" );
				break;
			default : // 默认excel表格文件
				header ( "Content-type:application/vnd.ms-excel" );
				header ( "Content-Disposition:attachment;filename= eTeacher订单.xls" );
				break;
		}
		header ( "charset=UTF-8" );
		echo "序号\t订单号\t教师tid\t上课时间\t支付状态\t流转状态\t下单时间\t学生年级\t上课方式\t订单联系地址\t订单联系手机号\t学生姓名\t用户城市\t用户地区\t老师姓名\t课程选择\t" . "课程名称\t课时数\t剩余课时数\t回访次数\t邀请码\t\n";
		// 输出内容如下：
		$i = 1;
		foreach ( $result as $k1 => $v1 ) {
			printf("%s\t",$i) ;
			foreach ( $v1 as $k2 => $v2 ) {
				printf("%s\t",$v2) ;
			}
			$i += 1;
			echo "\n";
		}
	}
/**
 * END
 */
}

