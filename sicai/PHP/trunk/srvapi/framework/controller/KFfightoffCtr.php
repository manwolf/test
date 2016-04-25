<?php
include_once 'base/crudCtr.php';
include_once 'base/encrypt.php';
class KFfightoffCtr extends crudCtr {
	/**
	 *
	 * 功能：查询拼课信息，查询拼课详情，查询发起人信息，取消拼课，拼课转入
	 * 作者： 李坡
	 * 日期：2015年9月1日
	 */
	// 查询拼课信息，根据不同的条件查询
	function queryFightOff() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$page = $newrow ['page'];
		if ($page == '' || $page == null || $page <= 0) {
			$page = 1;
		}
		unset ( $newrow ['page'] );
		$date = date ( 'Y-m-d' );//获取当前时间
		$kf_token = $newrow ['token'];
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if ($result1) {
			$result = $result1;
			$city = $result1 ['kf_city'];
		} else {
			$result = $result2;
			$city = $result2 ['kf_admin_city'];
		}
		
		if ($result) {
			if (! $newrow) { // 如果为空查询全部
				$querySql = "select * from(select distinct 
					c.class_hour,o.teacher_tid,u.user_name ,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
					from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o, class_discount c
					where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 order by o.tid desc)
                    w
                    left join 
                    (select distinct order_tid 
                    from class_list c ,order_list o  
                    where o.tid=c.order_tid) a 
                    on  a.order_tid=w.tid ";
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();
					$total_page = $pager ['total_page'];//获取总页数
					$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					return;
				}
			if ($newrow) { // 如果不为空 则按条件查询
				
				if ($newrow ['fightoffstate'] != null) {//拼课状态 0为取消拼课 1为正在拼课 2为正在上课3为拼课完成
					
					$querySql = 'select * from(select distinct 
					c.class_hour,o.teacher_tid,u.user_name ,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
					from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
					where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and o.spelling_state='.$newrow ['fightoffstate'] .' order by o.tid desc)
                    w
                    left join 
                    (select distinct order_tid 
                    from class_list c ,order_list o  
                    where o.tid=c.order_tid) a 
                    on  a.order_tid=w.tid';
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();//获取分页信息
					$total_page = $pager ['total_page'];//获取总页数
					$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					return;
				}
				if ($newrow ['classes'] != null) {//前端传年级 0-12 代表学前到高三    
					$querySql = 'select * from(select distinct 
					c.class_hour,o.teacher_tid,u.user_name ,o.class_content,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
				    from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
					where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0  and o.class_content ="'.$newrow ['classes'].'"'  .'order by o.tid desc)
                    w
                    left join 
                    (select distinct order_tid 
                    from class_list c ,order_list o  
                    where o.tid=c.order_tid) a 
                    on  a.order_tid=w.tid' ;
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();//获取分页信息
					$total_page = $pager ['total_page'];//获取总页数
					$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					return;
				}
				if ($newrow ['classtime']) {//上课时间 一天内传1 3天内传3 一周内传7 一个月内传30  一个季度内传90
					$begindate = date ( 'Y-m-d', strtotime ( '-' . $newrow ['classtime'] . ' day' ) );
					// echo $begindate;
					// exit;
					// $enddate=date('Y-m-d',strtotime('+'. $classtime. 'day'));
					$querySql = 'select * from(select distinct 
					c.class_hour,o.teacher_tid,u.user_name ,o.class_content,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
				    from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
					where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and  o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and u.takertime>="' . $begindate . '"' . ' and u.takertime="' . $date.'"' .' order by o.tid desc)
                    w
                    left join 
                    (select distinct order_tid 
                    from class_list c ,order_list o  
                    where o.tid=c.order_tid) a 
                    on  a.order_tid=w.tid' ;
					
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();//获取分页信息
					$total_page = $pager ['total_page'];//获取总页数
					$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					return;
				}
				if ($newrow ['fightoffnum']) {//参与人数 有几个人传几
					$querySql = "select * from(select distinct 
					c.class_hour,o.teacher_tid,u.user_name ,o.class_content,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
				    from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
					where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and u.participants_number=" . $newrow ['fightoffnum'] . " order by o.tid desc)
                    w
                    left join 
                    (select distinct order_tid 
                    from class_list c ,order_list o  
                    where o.tid=c.order_tid) a 
                    on  a.order_tid=w.tid" 
					;
					$model = spClass ( 'teacher_info' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();//获取分页信息
					$total_page = $pager ['total_page'];//获取总页数
					$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					return;
				}
				if ($newrow ['user_name']) {
					$querySql = 'select * from(select distinct 
					c.class_hour,o.teacher_tid,u.user_name ,o.class_content,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
				    from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
					where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and u.user_name="' . $newrow ['user_name'] . '"' .'order by o.tid desc)
                    w
                    left join 
                    (select distinct order_tid 
                    from class_list c ,order_list o  
                    where o.tid=c.order_tid) a 
                    on  a.order_tid=w.tid '
					;
					$model = spClass ( 'order_list' );
					$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
					$pager = $model->spPager ()->getPager ();//获取分页信息
					$total_page = $pager ['total_page'];//获取总页数
					$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					return;
				}
				if ($newrow ['usertype']!=null) { // 前端传来是否有一对一课程，0为有 1为没有
					if ($newrow ['usertype'] == 0) {
						$querySql = "  select * from(select distinct 
						c.class_hour,o.teacher_tid,u.user_name ,o.class_content,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
					    from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
						where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and o.tid in (select distinct  c.order_tid from class_list c ,order_list o where o.tid=c.order_tid) order by o.tid desc )
	                    w
	                    left join 
	                    (select distinct order_tid 
	                    from class_list c ,order_list o  
	                    where o.tid=c.order_tid) a 
	                    on  a.order_tid=w.tid ";
						$model = spClass ( 'order_list' );
						$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
						$pager = $model->spPager ()->getPager ();//获取分页信息
						$total_page = $pager ['total_page'];//获取总页数
						$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					}
					if ($newrow ['usertype'] == 1) {
						$querySql = " select * from(select distinct 
						c.class_hour,o.teacher_tid,u.user_name ,o.class_content,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,o.tid,o.spelling_state
					    from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o,class_discount c
						where  u.teacher_tid=t.tid and u.user_tid=i.tid and o.class_discount_tid=c.tid and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and o.tid not in (select distinct  c.order_tid from class_list c ,order_list o where o.tid=c.order_tid) order by o.tid desc)
	                    w
	                    left join 
	                    (select distinct order_tid 
	                    from class_list c ,order_list o  
	                    where o.tid=c.order_tid) a 
	                    on  a.order_tid=w.tid ";
						$model = spClass ( 'order_list' );
						$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
						$pager = $model->spPager ()->getPager ();//获取分页信息
						$total_page = $pager ['total_page'];//获取总页数
						$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
					}
					return;
				}
			
		} else {
			
			$msg->ResponseMsg ( 1, "对不起您没有权限！", false, 1, $prefixJS );
		}
	}
	}
	// 查看拼课详情
	function queryFightOffDetails() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$order_tid = $newrow ['order_tid'];
		$kf_token = $newrow ['token'];
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if ($result1) {
			$result = $result1;
			$city = $result1 ['kf_city'];
		} else {
			$result = $result2;
			$city = $result2 ['kf_admin_city'];
		}
		
		$querySql="select user_spelling_lesson_tid from order_list where tid=".$order_tid;
		$model = spClass ( 'order_list' );
		$result = $model->findSql ( $querySql );
		$tid=$result[0]['user_spelling_lesson_tid'];
		
		if ($result) {
			#身份验证通过查询所要查询的内容
			$querySql = 'select * from(select distinct o.tid,u.user_name,o.order_phone,o.create_time,o.pay_done,o.order_remark,o.spelling_lesson_type
						from user_info u,order_list o,user_spelling_lesson s
						where o.user_tid=u.tid  and   o.user_spelling_lesson_tid=s.tid and s.tid="'.$tid.'"'  .') w 
                        left join 
                        (select distinct c.order_tid ,count(k.tid)as count
	                    from class_list c ,order_list o  ,kf_return_info k
	                    where o.tid=c.order_tid  and k.return_phone=o.order_phone  group by c.order_tid  ) a 
                        on  a.order_tid=w.tid' ;
				$model = spClass ( 'order_list' );
				$result = $model->findSql ( $querySql );
				$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '对不起，您没权限！', 1, 0, $prefixJS );
		}
	}
	// 发起人信息
	function queryInitiatorDetails() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$order_tid = $newrow ['order_tid'];//订单tid
		$kf_token = $newrow ['token'];
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if ($result1) {
			$result = $result1;
			$city = $result1 ['kf_city'];
		} else {
			$result = $result2;
			$city = $result2 ['kf_admin_city'];
		}
		
		if ($result) {
			#验证通过查询内容
			$querySql = "select distinct u.user_name,t.teacher_name,u.takertime,u.user_venue,u.teaching_week,u.teacher_time,u.teache_class,u.participants_number,u.course_package
							from teacher_info t,user_info i,user_spelling_lesson u ,order_list o
							where u.user_tid=i.tid and u.teacher_tid=t.tid and o.user_spelling_lesson_tid=u.tid and i.tid=o.user_tid  and  o.tid=" . $order_tid;
			$model = spClass ( 'order_list' );
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限', false, 0, $prefixJS );
		}
	}
	// 取消拼课
	function cancelFightOff() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$order_tid = $newrow ['order_tid'];//订单tid
		$kf_token = $newrow ['token'];
		#根据订单id查询是发起人还是参与者
		$querySql = "select spelling_lesson_type ,spelling_state from order_list where tid=" . $order_tid;
		$model = spClass ( 'order_list' );
		$result = $model->findSql ( $querySql );
		$type = $result [0] ['spelling_lesson_type'];
		$state=$result[0]['spelling_state'];
		#根据订单id查询发起者的tid,取消订单的时候可以修改发起者信息中的参与人数
		$querySql = "select user_spelling_lesson_tid from order_list where tid=" . $order_tid;
		$model = spClass ( 'order_list' );
		$result = $model->findSql ( $querySql );
		$tid = $result [0] ['user_spelling_lesson_tid'];
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if ($result1) {
			$result = $result1;
			$city = $result1 ['kf_city'];
		} else {
			$result = $result2;
			$city = $result2 ['kf_admin_city'];
		}
		if ($result) {//判断此tid是否被取消成功 如果取消成功则不能在取消
			if($result[0]['spelling_state'] !=0 || $result[0]['spelling_state'] !=4 ){
				$msg->ResponseMsg ( 1, '您已经取消过此人，请勿重复取消', $result, 0, $prefixJS );
				return ;
			}
			/*判断是发起者还是参与者，如果是发起者type=0 如果是参与者type=1 
			这里判断是参与者还是发起者是为了区分取消订单的去向
			发起者订单取消之后会在取消拼课那里找到，
			参与者订单取消之后待定*/			
			if ($type == 1) {//参与者 去向订单将spelling_state改为4
				$updateSql = "update order_list set spelling_state=4 where tid=" . $order_tid;
				$model = spClass ( 'order_list' );
				$result = $model->runSql ( $updateSql );
				#订单取消后参与者人数会减一 理论上最多能取消三个参与者不需要判断participants_number<=1的情况，但不排除网络问题没取消掉的参与者，因此上面判断了这个tid是否取消过
				$updateSql = "update user_spelling_lesson set participants_number=participants_number -1 where tid=" . $tid;
				$model = spClass ( 'order_list' );
				$result = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, '取消成功', $result, 0, $prefixJS );
				return;
			}
			if ($type == 0) {
				$updateSql = "update order_list set spelling_state=0 ,where tid=" . $order_tid;
				$model = spClass ( 'order_list' );
				$result = $model->runSql ( $updateSql );
				$msg->ResponseMsg ( 0, '取消成功', $result, 0, $prefixJS );
				return ;
			}
		} else {
			$msg->ResponseMsg ( 1, '对不起，身份验证失败', false, 0, $prefixJS );
		}
	}
	// 拼课订单转入
	function conversion() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$order_tid = $newrow ['order_tid'];
		$kf_token = $newrow ['token'];
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "kf_token", $token, "kf_user_info" );
		$result2 = $verify->VerifyAuth ( "kf_admin_token", $token, "kf_admin_info" );
		if ($result1) {
			$result = $result1;
			$city = $result1 ['kf_city'];
		} else {
			$result = $result2;
			$city = $result2 ['kf_admin_city'];
		}
		
		if ($result) {
			/*转入规则：
			 *正在拼课->正在上课->拼课完成 
			 *只有发起人才能转换状态，发起人状态转换参与者跟着发起人走 但参与者的时间状态不改变
			 *由1->2->3
			 *此状态不可逆 只能正向转换
			 */
			#查询拼课状态和拼课人类型（发起者0 参与者1）
			$querySql = "select spelling_lesson_type,spelling_state  from order_list where tid=" . $order_tid;
			$model = spClass ( 'order_list' );
			$result = $model->findSql ( $querySql );
			if ($result [0] ['spelling_lesson_type'] == 0) {
				if ($result [0] ['spelling_state'] == 1) {//发起人如果拼课状态为1  则由1->2过渡
					$updateSql = "update order_list set spelling_state=2 where tid=" . $order_tid;
					$model = spClass ( 'order_list' );
					$result = $model->runSql ( $updateSql );
					$msg->ResponseMsg ( 0, '转入成功', $result, 0, $prefixJS );
					return;
				}
				if ($result [0] ['spelling_state'] == 2 ) {//发起人如果拼课状态为2  则由2->3过渡
					$updateSql = "update order_list set spelling_state=3 where tid=" . $order_tid;
					$model = spClass ( 'order_list' );
					$result = $model->runSql ( $updateSql );
					$msg->ResponseMsg ( 0, '转入成功', $result, 0, $prefixJS );
					return;
				}
			} else {
				$msg->ResponseMsg ( 1, '转入失败，只有拼课发起人才能转入', false, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '对不起，身份验证失败', false, 0, $prefixJS );
		}
	}
	/*
	 * #查询正在一对一的课程详情
	 * function queryCourseDetails()
	 * {
	 * $msg = new responseMsg ();
	 * $capturs = $this->captureParams ();
	 * $prefixJS = $capturs ['callback'];
	 * $token = $capturs ['token'];
	 * $newrow = $capturs ['newrow'];
	 * $order_tid=$newrow['order_tid'];
	 * $kf_token=$newrow['token'];
	 * $verify = new encrypt();
	 * $result1 = $verify->VerifyAuth("kf_token",$token,"kf_user_info");
	 * $result2 = $verify->VerifyAuth("kf_admin_token",$token,"kf_admin_info");
	 * if($result1){
	 * $result = $result1;
	 * $city = $result1['kf_city'];
	 * }else{
	 * $result = $result2;
	 * $city = $result2['kf_admin_city'];
	 * }
	 *
	 * if($result){
	 * $querySql="select c.tid from class_list c ,order_list o where o.tid=c.order_tid and o.tid=".$order_tid;
	 *
	 * }
	 * }
	 */
}

