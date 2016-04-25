<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
class KFfightoffCtr extends crudCtr {
	/**
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			$page = $newrow ['page'];
			if ($page == '' || $page == null || $page <= 0) {
				$page = 1;
			}
			unset ( $newrow ['page'] );
			unset ( $newrow ['user_tid'] );
			unset ( $newrow ['token'] );
			$date = date ( 'Y-m-d' ); // 获取当前时间
			if ($city == 全国) {//全国则查询所有
				$querySql = 'select distinct * from(select distinct
					u.pay_done,u.high_quality_courses_tid,o.class_way,u.teaching_grade ,u.teache_class,s.class_hour,(t.tid)as teacher_tid,u.user_name ,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,(o.tid)as o_tid,o.spelling_state
					from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o, spelling_lesson_discount s
					where   u.teacher_tid=t.tid and s.city=i.user_city and s.spell_class_num=u.participants_number  and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0  order by o.tid desc)
                    w
                    left join 
                    (select c.tid,c.high_quality_name 
                    from  high_quality_courses c,user_spelling_lesson u  
                    where c.tid=u.high_quality_courses_tid) b on b.tid=w.high_quality_courses_tid
                     
                    left join
                    (select distinct order_tid
                    from class_list c ,order_list o
                    where o.tid=c.order_tid) a
                    on  a.order_tid=w.o_tid  ';
			} else {
				$querySql = 'select distinct * from(select distinct
					u.pay_done,u.high_quality_courses_tid,o.class_way,u.teaching_grade ,u.teache_class,s.class_hour,(t.tid)as teacher_tid,u.user_name ,u.user_phone,u.takertime,u.user_venue,u.teaching_week,u.spelling_lesson_number,u.participants_number,u.teacher_time,u.user_message,t.teacher_name,(o.tid)as o_tid,o.spelling_state
					from  user_spelling_lesson u ,teacher_info t,user_info i,order_list o, spelling_lesson_discount s
					where   u.teacher_tid=t.tid and s.city=i.user_city and s.spell_class_num=u.participants_number  and o.user_spelling_lesson_tid=u.tid and o.spelling_lesson_type = 0 and i.user_city="' . $city . '"' . ' order by o.tid desc)
                    w
                    left join 
                    (select c.tid,c.high_quality_name 
                    from  high_quality_courses c,user_spelling_lesson u  
                    where c.tid=u.high_quality_courses_tid) b on b.tid=w.high_quality_courses_tid
                     
                    left join
                    (select distinct order_tid
                    from class_list c ,order_list o
                    where o.tid=c.order_tid) a
                    on  a.order_tid=w.o_tid  ';
			}
			if (! $newrow) { // 如果为空查询全部
				$querySql;
			}
			if ($newrow) { // 如果不为空 则按条件查询
				if ($newrow ['fightoffstate'] != null) {
					$querySql .= ' where w.spelling_state="' . $newrow ['fightoffstate'] . '"';
				}
				if ($newrow ['teaching_grade'] != null && $newrow ['teache_class'] != null) { // 前端传年级 0-12 代表学前到高三
					$querySql .= ' where w.teaching_grade ="' . $newrow ['teaching_grade'] . '"' . ' and w.teache_class="' . $newrow ['teache_class'] . '"';
				}
				if ($newrow ['classtime']) { // 上课时间 一天内传1 3天内传3 一周内传7 一个月内传30 一个季度内传90
					$begindate = date ( 'Y-m-d', strtotime ( '-' . $newrow ['classtime'] . ' day' ) );
					$querySql .= ' and w.takertime>="' . $begindate . '"' . ' and w.takertime="' . $date . '"';
				}
				if ($newrow ['fightoffnum']) { // 参与人数 有几个人传几
					$querySql .= ' where w.participants_number="' . $newrow ['fightoffnum'] . '"';
				}
				if ($newrow ['user_name']) {//按名字查询
					$querySql .= 'where w.user_name="' . $newrow ['user_name'] . '"';
				}
				if ($newrow ['usertype'] != null) { // 前端传来是否有一对一课程，0为有 1为没有
					if ($newrow ['usertype'] == 0) {
						$querySql .= ' where  w.o_tid in (select distinct  c.order_tid from class_list c ,order_list o where o.tid=c.order_tid)';
					}
					if ($newrow ['usertype'] == 1) {
						$querySql .= ' where w.o_tid not in (select distinct  c.order_tid from class_list c ,order_list o where o.tid=c.order_tid)';
					}
				}
				if ($newrow ['class_way'] != null) { // 前端传来上课方式 0为老师上门 1为在线授课
					if ($newrow ['class_way'] == 0) {
						$querySql .= ' where w.class_way !=3 ';
					}
					if ($newrow ['class_way'] == 1) {
						$querySql .= ' where w.class_way=3 ';
					}
				}
				if ($newrow ['course_choice'] != null) { // 前端传来课程选择 0为传统，1为短期
					if ($newrow ['course_choice'] == 0) {
						$querySql .= 'where w.high_quality_courses_tid is null';
					}
					if ($newrow ['course_choice'] == 1) {
						$querySql .= 'where w.high_quality_courses_tid>0';
					}
				}
				if ($newrow ['teacher_choice'] != null) {
					if ($newrow ['teacher_choice'] == 0) { // 前端传来分配老师0为已分配1为未分配
						$querySql .= 'where w.teacher_name is not null';
					}
					if ($newrow ['teacher_choice'] == 1) {
						$querySql .= 'where w.teacher_name is null';
					}
				}
			}
			$model = spClass ( 'teacher_info' );
			$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page']; // 获取总页数
			$results = $verify->record ();
			if($result){
			$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, "查无此相关信息", true, 1, $prefixJS );
				
		}
		}else {
			$msg->ResponseMsg ( 1, "对不起您没有权限！", false, 1, $prefixJS );
		}
	}
	//分配老师
	function  assignTeacher(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city=$result[0]['city'];
			//查询老师城市属性
			$quetySql='select teacher_city from teacher_info where tid="'.$newrow['teacher_tid'].'"';
			$model=spClass('teacher_info');
			$result=$model->findSql($quetySql);
			$teacher_tid=$result[0]['tid'];
		if($city==全国 ||$city==$result[0]['teacher_city']){//操作者的城市属性和老师的城市属性匹配 不一致则不能操作
			$conditions = array (
						'teacher_tid' => $newrow['teacher_tid']
				); // 条件查询 字段teacher_num=$teacher_num
				$model = spClass ( 'user_spelling_lesson' );
				$result = $model->updateField ( $conditions, 'teachers_tid', $teacher_tid );
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, "查询成功", $result, 1, $prefixJS );
				
			}else{
			$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师！', 1, 0, $prefixJS );
		}
		}else{
		
			$msg->ResponseMsg ( 1, '对不起，您没权限！', 1, 0, $prefixJS );
		}
	}
	//查询全部老师
	function queryTeacher(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city=$result[0]['city'];
			if($city==全国){//如果是全国权限则提示选择城市
				$msg->ResponseMsg ( 1, '您是全国权限，请选择你想操作的城市！', 1, 0, $prefixJS );		
			return ;
			}
			if($newrow['city']!=null ){//如果是全国权限但没选择城市 则执行下面语句
				$quetySql='select tid,teacher_city, teacher_name from teacher_info where teacher_city="'.$newrow['city'].'"';
			}
			if($city!=全国){//如果传来不是全国权限 则执行下面语句
				$quetySql='select tid,teacher_city, teacher_name from teacher_info where teacher_city="'.$city.'"';
			}
				$model=spClass('teacher_info');
				$result=$model->findSql($quetySql);
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, "查询成功", $result, 1, $prefixJS );
			}
		else{
			$msg->ResponseMsg ( 1, '对不起，您没权限！', 1, 0, $prefixJS );
				
		}
	}
	
	// 查看拼课详情
	function queryFightOffDetails() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$order_tid = $newrow ['order_tid'];
			$spelling_state = $newrow ['spelling_state'];
			//查询拼课id 拼课人城市属性
			$querySql = "select o.user_spelling_lesson_tid,u.user_city from order_list o,user_info u where u.tid=o.user_tid and  o.tid=" . $order_tid;
			$model = spClass ( 'order_list' );
			$result = $model->findSql ( $querySql );
			$tid = $result [0] ['user_spelling_lesson_tid'];
			if ($result) {
				// 身份验证通过查询所要查询的内容
				$querySql = 'select * from(select distinct o.tid,u.user_name,o.order_phone,o.create_time,o.pay_done,o.order_remark,o.spelling_lesson_type
						from user_info u,order_list o,user_spelling_lesson s
						where o.user_tid=u.tid  and   o.user_spelling_lesson_tid=s.tid  and spelling_state="' . $spelling_state . '"' . ' and  s.tid="' . $tid . '"' . ') w 
                        left join 
                        (select distinct c.order_tid ,count(k.tid)as count
	                    from class_list c ,order_list o  ,kf_return_info k
	                    where o.tid=c.order_tid  and k.return_phone=o.order_phone  group by c.order_tid  ) a 
                        on  a.order_tid=w.tid';
// 				echo $querySql;
				$model = spClass ( 'order_list' );
				$result = $model->findSql ( $querySql );
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
			}else{
				$msg->ResponseMsg ( 1, '暂无此订单的详情', false, 0, $prefixJS );
			}
			
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$order_tid = $newrow ['order_tid']; // 订单tid
			//根据订单id查询发起者信息
			$querySql = "select distinct u.user_name,t.teacher_name,u.takertime,u.user_venue,u.teaching_week,u.teacher_time,u.teache_class,u.participants_number,u.course_package
							from teacher_info t,user_info i,user_spelling_lesson u ,order_list o
							where u.user_tid=i.tid and u.teacher_tid=t.tid and o.user_spelling_lesson_tid=u.tid and i.tid=o.user_tid  and  o.tid=" . $order_tid;
			$model = spClass ( 'order_list' );
			$result = $model->findSql ( $querySql );
			$verify = new checkCtr ();
			$results = $verify->record ();
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			$order_tid = $newrow ['order_tid']; // 订单tid
			/*
			 * 1.根据订单id查询是发起人还是参与者
			 * 2.根据订单id查询发起者的tid,取消订单的时候可以修改发起者信息中的参与人数
			 */
			$querySql = "select o.spelling_lesson_type ,o.spelling_state,o.user_spelling_lesson_tid,u.user_city ,s.pay_done from order_list o,user_info u,user_spelling_lesson s where o.user_tid=u.tid and s.tid=o.user_spelling_lesson_tid and o.tid=" . $order_tid;
			$model = spClass ( 'order_list' );
			$result = $model->findSql ( $querySql );
			if ($city == $result [0] ['user_city'] || $city == 全国) {
				$type = $result [0] ['spelling_lesson_type'];
				$state = $result [0] ['spelling_state'];
				$tid = $result [0] ['user_spelling_lesson_tid'];
				$pay_done = $result [0] ['pay_done'];
				if ($pay_done == 1) {
					$msg->ResponseMsg ( 1, '对不起，有人已经付款，无法取消拼课', $result, 0, $prefixJS );
					return;
				}
				// 判断此tid是否被取消成功 如果取消成功则不能在取消
				if (0 == $state || 4 == $state) {
					$msg->ResponseMsg ( 1, '您已经取消过此人，请勿重复取消', $result, 0, $prefixJS );
					return;
				}
				/*
				 * 判断是发起者还是参与者，如果是发起者type=0 如果是参与者type=1
				 * 这里判断是参与者还是发起者是为了区分取消订单的去向
				 * 发起者订单取消之后会在取消拼课那里找到，
				 * 参与者订单取消之后待定
				 */
				if ($type == 1) { // 参与者 去向订单将spelling_state改为4
					$updateSql = "update order_list set spelling_state=4 where tid=" . $order_tid;
					$model = spClass ( 'order_list' );
					$result = $model->runSql ( $updateSql );
					// 订单取消后参与者人数会减一 理论上最多能取消三个参与者不需要判断participants_number<=1的情况，但不排除网络问题没取消掉的参与者，因此上面判断了这个tid是否取消过
					$updateSql = "update user_spelling_lesson set participants_number=participants_number -1 where tid=" . $tid;
					$model = spClass ( 'order_list' );
					$result = $model->runSql ( $updateSql );
					$msg->ResponseMsg ( 0, '取消成功', $result, 0, $prefixJS );
					return;
				}
				if ($type == 0) {
					$updateSql = "update order_list set spelling_state=0 where tid>0 and user_spelling_lesson_tid=" . $tid;
					$model = spClass ( 'order_list' );
					$result = $model->runSql ( $updateSql );
					$verify = new checkCtr ();
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, '取消成功', $result, 0, $prefixJS );
					return;
				}
			} else {
				$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的学生', false, 0, $prefixJS );
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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			$order_tid = $newrow ['order_tid'];
			$querySql = " select o.spelling_lesson_type ,o.spelling_state,o.user_spelling_lesson_tid,u.user_city ,s.pay_done from order_list o,user_info u,user_spelling_lesson s where o.user_tid=u.tid and s.tid=o.user_spelling_lesson_tid and from order_list o,user_info u where o.user_tid=u.tid and o.tid=" . $order_tid;
			$model = spClass ( 'order_list' );
			$result = $model->findSql ( $querySql );
			if ($city == $result [0] ['user_city'] || $city == 全国) {
				
				/*
				 * 转入规则：
				 * 正在拼课->正在上课->拼课完成
				 * 只有发起人才能转换状态，发起人状态转换参与者跟着发起人走 但参与者的时间状态不改变
				 * 由1->2->3
				 * 此状态不可逆 只能正向转换
				 */
				// 查询拼课状态和拼课人类型（发起者0 参与者1）
				if ($result [0] ['pay_done'] == 0) {
					$msg->ResponseMsg ( 1, '对不起，有人没付款，暂不能转入', $result, 0, $prefixJS );
					return;
				}
				$tid = $result [0] ['user_spelling_lesson_tid'];
				if ($result [0] ['spelling_lesson_type'] == 0) {
					if ($result [0] ['spelling_state'] == 1) { // 发起人如果拼课状态为1 则由1->2过渡
						$updateSql = "update order_list set spelling_state=2 where tid>0 and user_spelling_lesson_tid='" . $tid . "'";
						$model = spClass ( 'order_list' );
						$result = $model->runSql ( $updateSql );
						$msg->ResponseMsg ( 0, '转入成功', $result, 0, $prefixJS );
						return;
					}
					if ($result [0] ['spelling_state'] == 2) { // 发起人如果拼课状态为2 则由2->3过渡
						$updateSql = "update order_list set spelling_state=3 where tid>0 and user_spelling_lesson_tid='" . $tid . "'";
						$model = spClass ( 'order_list' );
						$result = $model->runSql ( $updateSql );
						$verify = new checkCtr ();
						$results = $verify->record ();
						$msg->ResponseMsg ( 0, '转入成功', $result, 0, $prefixJS );
						return;
					}
				} else {
					$msg->ResponseMsg ( 1, '转入失败，只有拼课发起人才能转入', false, 0, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的订单', false, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
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
	 * $verify = new checkCtr();
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

