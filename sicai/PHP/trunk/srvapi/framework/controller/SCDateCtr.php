<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
class SCDateCtr extends crudCtr {
	/**
	 * 功能：对市场专员数据管理
	 * 作者： 李坡
	 * 创建日期：2015年9月6日
	 */
	// 按条件查询市场专员業績
	function queryScDate() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		if ($page == '' || $page == null || $page <= 0) {
			$page = 1;
		}
		unset ( $newrow ['page'] );
		$sc_city = $newrow ['sc_city'];
		// 获取当天起始时间和结束时间
		$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
		$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
		// 获取本周的起始时间和结束时间
		$startweek = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1 - 7, date ( "Y" ) ) );
		$endweek = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ) - date ( "w" ) + 7 - 7, date ( "Y" ) ) );
		// 获取本月的起始时间和结束时间
		$beginThismonth = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( 'm' ), 1, date ( 'Y' ) ) );
		$endThismonth = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( 'm' ), date ( 't' ), date ( 'Y' ) ) );
		if (! $newrow) {
			$querySql = "select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num";
			$model = spClass ( 'sc_user_info' );
			$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page']; // 获取总页数
			$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
			return;
		}
		if ($newrow) {
			if ($newrow ['name']) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
				and s.sc_name="' . $newrow ['name'] . '"';
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['time'] == 1) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
			and sc_hiredate>="' . $start . '"' . ' and sc_hiredate="' . $end . '"';
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['time'] == 2) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
			and sc_hiredate>="' . $startweek . '"' . ' and sc_hiredate="' . $endweek . '"';
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['time'] == 3) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
			and sc_hiredate>="' . $beginThismonth . '"' . ' and sc_hiredate="' . $endThismonth . '"';
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] != null) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
				and post_status=' . $newrow ['state'];
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] != null && $newrow ['time'] == 1) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
			 	and sc_hiredate>="' . $start . '"' . ' and sc_hiredate="' . $end . '"' . ' and post_status=' . $newrow ['state'];
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] != null && $newrow ['time'] == 2) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
			    and sc_hiredate>="' . $startweek . '"' . ' and sc_hiredate="' . $endweek . '"' . ' and post_status=' . $newrow ['state'];
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] != null && $newrow ['time'] == 3) {
				$querySql = 'select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num
			 	and sc_hiredate>="' . $beginThismonth . '"' . ' and sc_hiredate="' . $endThismonth . '"' . ' and post_status=' . $newrow ['state'];
				$model = spClass ( 'sc_user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
		}
	}
	// 更改市场专员在職狀態
	function updateScLevel() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$sc_num = $newrow ['sc_num'];
		$state = $newrow ['state'];
		// $jy_token = $newrow ['token'];
		if ($newrow) {
			$conditions = array (
					'sc_num' => $sc_num 
			); // 条件查询 字段teacher_num=$teacher_num
			$model = spClass ( 'sc_user_info' );
			$result = $model->updateField ( $conditions, 'post_status', $state );
			$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '所修改的信息不能为空', 0, 0, $prefixJS );
		}
	}
	// 按條件查詢註冊的人
	function queryIndex() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		if ($page == '' || $page == null || $page <= 0) {
			$page = 1;
		}
		unset ( $newrow ['page'] );
		$sc_num = $newrow ['sc_num'];
		unset ( $newrow ['sc_num'] );
		// 获取当天起始时间和结束时间
		$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
		$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
		// 获取本周的起始时间和结束时间
		$startweek = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1 - 7, date ( "Y" ) ) );
		$endweek = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ) - date ( "w" ) + 7 - 7, date ( "Y" ) ) );
		// 获取本月的起始时间和结束时间
		$beginThismonth = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( 'm' ), 1, date ( 'Y' ) ) );
		$endThismonth = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( 'm' ), date ( 't' ), date ( 'Y' ) ) );
		$querySql = 'select distinct  u.telephone,u.create_time,u.user_name,o.tid,o.class_content,o.order_money,c.class_hour 
					   from order_list o ,class_discount c,user_info u where o.user_tid=u.tid and o.class_discount_tid=c.tid 
					    and  u.sc_num="' . $sc_num . '"';
		
		if (! $newrow) {
			$querySql;
			$model = spClass ( 'user_info' );
			$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page']; // 获取总页数
			$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
			return;
		}
		if ($newrow ['telephone']) {
			$querySql .= ' and u.telephone="' . $newrow ['telephone'] . '"';
			$model = spClass ( 'user_info' );
			$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page']; // 获取总页数
			$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
			return;
		}
		
		if ($newrow ['time'] != null && $newrow ['state'] == null) {
			if ($newrow ['time'] == 1) {
				$querySql .= ' and u.create_time>="' . $start . '"' . ' and u.create_time<="' . $end . '"';
				
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['time'] == 2) {
				$querySql .= ' and u.create_time>="' . $startweek . '"' . ' and u.create_time<="' . $endweek . '"';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['time'] == 3) {
				$querySql .= ' and u.create_time>="' . $beginThismonth . '"' . ' and u.create_time<="' . $endThismonth . '"';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
		}
		if ($newrow ['time'] == null && $newrow ['state'] != null) {
			if ($newrow ['state'] = 2) { // state是2的时候是快速约课
				$querySql .= ' and u.fast_count>0';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 3) { // state是3 代表免费试课
				$querySql .= ' and  o.order_type=0 and  o.pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 4) { // state是4 代表付费课程
				$querySql .= ' and o.order_type=1 and o.pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 1) { // state是1 代表注册
				$querySql .= 'select distinct  u.telephone,u.create_time,u.user_name,o.tid,o.class_content,o.order_money,c.class_hour
			        from user_info u left join order_list o on o.user_tid=u.tid and o.tid=null
			        left join class_discount c on  c.tid=o.class_discount_tid ';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
		}
		// if($newrow['time']==null && $newrow['state']!=null){
		// if($newrow['state']!=null ){
		// $querySql.= ' and post_status='.$newrow['state'];
		// $model=spClass('user_info');
		// $result=$model->findSql($querySql);
		// $msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
		// }
		// }
		
		if ($newrow ['time'] && $newrow ['state']) { // 如果选择有时间和状态
			if ($newrow ['time'] == 1 && $newrow ['state'] = 2) { // time是1的时候是今天 state是2的时候是快速约课
				$querySql .= ' and u.create_time>="' . $start . '"' . ' and u.create_time<="' . $end . '"' . ' and u.fast_count>0';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 2 && $newrow ['time'] == 2) { // time是2的时候是本周
				$querySql .= ' and u.create_time>="' . $startweek . '"' . ' and u.create_time<="' . $endweek . '"' . ' and u.fast_count>0';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 2 && $newrow ['time'] == 3) { // time是3的时候是本月
				$querySql .= ' and u.create_time>="' . $beginThismonth . '"' . ' and u.create_time<="' . $endThismonth . '"' . ' and u.fast_count>0';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 3 && $newrow ['time'] == 1) { // state是3 代表免费试课
				$querySql .= ' and u.create_time>="' . $start . '"' . ' and u.create_time<="' . $end . '"' . 'and o.order_type=0 and pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 3 && $newrow ['time'] == 2) { // state是3 代表免费试课
				$querySql .= ' and u.create_time>="' . $startweek . '"' . ' and u.create_time<="' . $endweek . '"' . ' and o.order_type=0 and pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				
				return;
			}
			if ($newrow ['state'] == 3 && $newrow ['time'] == 3) { // state是3 代表免费试课
				$querySql .= ' and u.create_time>="' . $beginThismonth . '"' . ' and u.create_time<="' . $endThismonth . '"' . ' and o.order_type=0 and pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 4 && $newrow ['time'] == 1) { // state是4 代表付费课程
				$querySql .= ' and u.create_time>="' . $start . '"' . ' and u.create_time<="' . $end . '"' . 'and o.order_type=1 and pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 4 && $newrow ['time'] == 2) { // state是4 代表付费课程
				$querySql .= ' and u.create_time>="' . $startweek . '"' . ' and u.create_time<="' . $endweek . '"' . ' and o.order_type=1 and pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 4 && $newrow ['time'] == 3) { // state是4 代表付费课程
				$querySql .= ' and u.create_time>="' . $beginThismonth . '"' . ' and u.create_time<="' . $endThismonth . '"' . ' and o.order_type=1 and pay_done=1';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 1 && $newrow ['time'] == 1) { // state是1 代表注册
				$querySql .= 'select distinct  u.telephone,u.create_time,u.user_name,o.tid,o.class_content,o.order_money,c.class_hour
			        from user_info u left join order_list o on o.user_tid=u.tid and o.tid=null
			        left join class_discount c on  c.tid=o.class_discount_tid  and u.create_time>="' . $start . '"' . ' and u.create_time<="' . $end . '"';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 1 && $newrow ['time'] == 2) { // state是1 代表注册
				$querySql .= 'select distinct  u.telephone,u.create_time,u.user_name,o.tid,o.class_content,o.order_money,c.class_hour
			        from user_info u left join order_list o on o.user_tid=u.tid and o.tid=null
			        left join class_discount c on  c.tid=o.class_discount_tid  and u.create_time>="' . $startweek . '"' . ' and u.create_time<="' . $endweek . '"';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
			if ($newrow ['state'] == 1 && $newrow ['time'] == 3) { // state是1 代表注册
				$querySql = 'select distinct  u.telephone,u.create_time,u.user_name,o.tid,o.class_content,o.order_money,c.class_hour
			        from user_info u left join order_list o on o.user_tid=u.tid and o.tid=null
			        left join class_discount c on  c.tid=o.class_discount_tid  and u.create_time>="' . $beginThismonth . '"' . ' and u.create_time<="' . $endThismonth . '"';
				$model = spClass ( 'user_info' );
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page']; // 获取总页数
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				return;
			}
		}
	}
	function exportFile() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			$conenttype = $newrow ['conenttype'];
			$filetype = $newrow ['filetype'];
			
			if ($city == null) {
				$msg->ResponseMsg ( 1, '该市场人员无城市属性，不能继续操作！', false, 0, $callback );
				exit ();
			}
			if ($conenttype == 1) {//如果$conenttupe==1则表示查询专员的邀请人
				$querySql = 'select distinct  u.telephone,u.create_time,u.user_name,o.tid,o.class_content,o.order_money,c.class_hour
					   from order_list o ,class_discount c,user_info u where o.user_tid=u.tid and o.class_discount_tid=c.tid
					    and  u.sc_num="' . $sc_num . '"';
			}
			if ($conenttype == 0) {//如果$conenttupe==0则表示查询专员
				$querySql = "select s.sc_name,s.post_status,s.sc_num,COUNT(u.fast_count=1)as count,count(u.tid)as count1,count(o.order_type=0)as count2,count(o.order_type=1)as count3 from sc_user_info s, user_info u,order_list o where o.user_tid=u.tid and  s.sc_num=u.sc_num";
			}
			
			if ($city != "全国") {
				$querySql .= '  AND  s.sc_city = "' . $result [0] ['city'] . '"';
			}
			if ($city == 全国) {
				$querySql;
			}
			$model = spClass ( "order_list" );
			$result = @$model->findSql ( $querySql );
			if (! $result) {
				$msg->ResponseMsg ( 1, ' 数据为空，不能导出!', false, 0, $callback );
				exit ();
			}
			switch ($filetype) {
				case "doc" : // word文档文件
					header ( "Content-type:application/vnd.ms-word" );
					header ( "Content-Disposition:attachment;filename= eTeacher市场信息.doc" );
					break;
				case "txt" : // txt记事本文件
					header ( "Content-type:text/plain" );
					header ( "Content-Disposition:attachment;filename= eTeacher市场信息.txt" );
					break;
				default : // 默认excel表格文件
					header ( "Content-type:application/vnd.ms-excel" );
					header ( "Content-Disposition:attachment;filename= eTeacher市场信息.xls" );
					break;
			}
			if($conenttype == 0){
			header ( "charset=UTF-8" );
			echo "序号\t城市\市场专员详情\t" . "姓名\t编号\t入职时间\t岗位状态\t注册量\t快速约课量\t免费试课量\t付费下单量\t\n";
			}
			if($conenttype == 1){
				echo "序号\t城市\市场专员业绩详情\t" . "注册手机号\t注册时间\t姓名\t年级\t购买课时\t订单金额\t\n";
				
			}
			// 输出内容如下：
			$i = 1;
			foreach ( $result as $k1 => $v1 ) {
				echo $i . "\t";
				foreach ( $v1 as $k2 => $v2 ) {
					echo $v2 . " \t";
				}
				$i += 1;
				echo "\n";
			}
		}else{
			$msg->ResponseMsg ( 1, ' 身份验证失败!', false, 0, $callback );
		}
	}
}




	