<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
/**
 * 功能：市场主管管理市场专员及统计地推结果
 * 作者： 孙广兢
 * 创建日期：2015年9月10日
 */
class SCDataCtr extends crudCtr {
	
	/**
	 * 按条件查询或导出市场专员的基本信息
	 */
	function queryScUser() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$sc_city = $newrow ['sc_city'];
		$sc_name = $newrow ['name'];
		$sc_tid = $newrow ['tid'];
		$state = $newrow ['state'];
		$time = ( integer ) $newrow ['time'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if (! $result) {
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $prefixJS );
			exit ();
		}
		$city = $result ['0'] ['city'];
		switch ($time) {
			case 1 :
				// 获取当天起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
				break;
			case 2 :
				// 获取本周的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1, date ( "Y" ) ) );
				break;
			case 3 :
				// 获取本月的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( 'm' ), 1, date ( 'Y' ) ) );
				break;
			default :
				// 全部
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, 1, 1, 1970 ) );
				break;
		}
		$end = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
		$querySql = '
				SELECT 
	ss.tid, 	ss.city, 	ss.user_name, 	ss.telephone,	
				CASE ss.post_status
				WHEN "0" THEN  "在职"
				WHEN "1" THEN  "离职"
				END AS post_status , 	ss.num, 	 
	IFNULL(aa.registerAmount,0) AS registerAmount, 	IFNULL(bb.fastOrderAmount,0) AS fastOrderAmount, 
	IFNULL(cc.freeTryOrderAmount,0) AS freeTryOrderAmount, 
	IFNULL(dd.payOrderAmount,0) AS payOrderAmount
from 
	(
		SELECT 
			s.tid, s.city,	s.user_name,	s.telephone,	s.post_status,s.num 
		from 
			oa_user_info s 
			left join user_roles_info ur on s.tid = ur.oa_user_info_tid 
			left join roles_info r on r.tid = ur.roles_info_tid 
		WHERE 
			r.roles_name = "市场专员" ';
		if ($city != "全国" and $city != NULL) {
			$querySql .= ' AND s.city = "' . $city . '"';
		}
		if ($sc_name) {
			$querySql .= ' AND s.user_name LIKE "%' . $sc_name . '%"';
		}
		if ($sc_tid) {
			$querySql .= ' AND s.tid = "' . $sc_tid . '"';
		}
		if ($sc_num) {
			$querySql .= ' AND s.num= "' . $sc_num . '"';
		}
		
		if ($state != NULL) {
			$querySql .= ' AND s.post_status= "' . $state . '"';
		}
		$querySql .= ' 
		group by s.tid 
		order by s.tid DESC
	) ss 
	LEFT JOIN (
			SELECT u.sc_tid, count( DISTINCT u.tid ) as registerAmount 
			FROM user_info u 
			where u.sc_tid > 0 
			AND u.create_time >= "' . $start . '" AND u.create_time <= "' . $end . '"
			group by u.sc_tid order by u.sc_tid DESC
			) aa
			ON  ss.tid = aa.sc_tid
	LEFT JOIN ( SELECT k.potential_date,			 	
			u.sc_tid, 
			count(
				DISTINCT u.tid
			) as fastOrderAmount			
		FROM 	user_info 	u 	  LEFT JOIN  order_list o  on u.tid = o.user_tid,
					kf_potential_info k 					
   WHERE  k.user_tid = u.tid AND u.sc_tid > 0 and o.pay_done != 1	 
					AND k.potential_date >= "' . $start . '" AND k.potential_date <= "' . $end . '"
	 GROUP BY 	u.sc_tid 	ORDER BY 	u.sc_tid DESC 
			) bb
			ON  ss.tid = bb.sc_tid	
	 
	LEFT JOIN (
		SELECT 
			u.sc_tid, 
			count(DISTINCT o.user_tid) AS freeTryOrderAmount 
		FROM 	user_info u 
			left join order_list o on u.tid = o.user_tid 
		where 	u.sc_tid > 0 	and u.fast_count = 0	and o.tid > 0 	and o.order_type = 0 and o.pay_done = 1
			AND o.create_time >= "' . $start . '" AND o.create_time <= "' . $end . '"
		group by 	u.sc_tid 	order by 		u.sc_tid DESC
	) cc on ss.tid = cc.sc_tid 
					
	LEFT JOIN (
		SELECT 
			u.sc_tid, 	count(DISTINCT o.user_tid) AS payOrderAmount 
		FROM 
			user_info u 	left join order_list o on u.tid = o.user_tid 
		where 		u.sc_tid > 0 	and u.fast_count = 0	and o.tid > 0 		and o.order_type = 1 and o.pay_done = 1
					AND o.create_time >= "' . $start . '" AND o.create_time <= "' . $end . '"
		group by 	u.sc_tid 	order by 		u.sc_tid DESC
	) dd on ss.tid = dd.sc_tid
				';
		$querySql .= ' ORDER BY ss.num DESC';
		$model = spClass ( 'oa_user_info' );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		// $result = @$model->findSql ( $querySql );
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = @$model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$ifexport = $newrow ['ifexport'];
			$filetype = $newrow ['filetype'];
			if ($ifexport === null) {
				$ifexport = 0;
			}
			if ($ifexport == 1) {
				$title = "序号\t市场人员ID\t城市\t姓名\t手机号\t岗位状态\t编号\t注册量\t快速约课量\t免费试课量\t付费下单量\t\n";
				$filename = "eTeacher市场人员基本信息";
				$this->exportDataToFile ( $title, $result, $filename, $filetype );
				exit ();
			} else {
				@$verify->record (); // 记录该操作的访问者信息
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				exit ();
			}
		} else {
			$msg->ResponseMsg ( 1, "查询结果为空", $result, 0, $prefixJS );
			exit ();
		}
		return;
	}
	
	/**
	 * 更改市场专员在职状态
	 */
	function updateSCUserstate() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$num = $newrow ['num'];
		$post_status = ( integer ) $newrow ['state'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if (! $result) {
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $prefixJS );
			exit ();
		}
		$city = $result ['0'] ['city'];
		
		$model = spClass ( 'oa_user_info' );
		$updateSql = ' UPDATE oa_user_info SET  post_status = "' . $post_status . '" WHERE num = "' . $num . '" ';
		if ($city != "全国" and $city != NULL) {
			$updateSql .= ' AND city = "' . $city . '"';
		}
		// echo $updateSql;
		$result = $model->runSql ( $updateSql );
		$affectedRows = @$model->affectedRows ();
		// echo $affectedRows;
		// exit();
		if ($affectedRows) {
			@$verify->record (); // 记录该操作的访问者信息
			$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
			return true;
		} else {
			$msg->ResponseMsg ( 1, '修改失败', 0, 0, $prefixJS );
			return false;
		}
	}
	
	/**
	 * 按条件查询或导出某一个市场专员的详细地推结果
	 */
	function querySingleScUser() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$sc_tid = $newrow ['tid'];
		$state = $newrow ['state'];
		$time = ( integer ) $newrow ['time'];
		$telephone = $newrow ['telephone'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if (! $result) {
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $prefixJS );
			exit ();
		}
		$city = $result ['0'] ['city'];
		if (empty ( $sc_tid )) {
			$msg->ResponseMsg ( 1, '市场人员的ID不能为空！', false, 0, $prefixJS );
			exit ();
		}
		$condition = ' tid  =  "' . $sc_tid . '"';
		$model = spClass ( 'oa_user_info' );
		$result = @$model->find ( $condition );
		if (! $result) {
			$msg->ResponseMsg ( 0, "市场人员不存在", fasle, 0, $prefixJS );
			exit ();
		}
		if ($city != "全国") {
			if ($result ['city'] != $city) {
				$msg->ResponseMsg ( 1, '您无权操作其他城市的市场人员！', false, 0, $prefixJS );
				exit ();
			}
		}
		$sc_name = $result ['name'];
		
		switch ($time) {
			case 1 :
				// 获取当天起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
				break;
			case 2 :
				// 获取本周的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1, date ( "Y" ) ) );
				break;
			case 3 :
				// 获取本月的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( 'm' ), 1, date ( 'Y' ) ) );
				break;
			default :
				// 全部
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, 1, 1, 1970 ) );
				break;
		}
		$end = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
		$querySql = '
				select 
	bb.user_tid, 	aa.user_name, 	aa.telephone, 	aa.create_time, 
	bb.tid, 	bb.class_content, 	bb.class_hour, 	bb.order_money 
from 
	(
		SELECT 
			u.tid,			u.user_name, 			u.telephone,			u.create_time ,u.fast_count
		FROM 
			user_info u 
		where 
			u.sc_tid= "' . $sc_tid . '"';
		if ($telephone) {
			$querySql .= ' AND u.telephone LIKE "%' . $telephone . '%"';
		}
		$querySql .= '	 group by 	u.tid 		order by u.tid DESC
	) aa 
	left join (
		SELECT 
			o.user_tid,			o.tid,			o.class_content, o.create_time,
			IF(o.high_quality_courses_tid, i.class_hour,d.class_hour	) AS class_hour, 
			o.order_money ,o.order_type,o.pay_done
		FROM 
			order_list o 
			LEFT JOIN class_discount d ON o.class_discount_tid = d.tid 
			LEFT JOIN high_quality_courses i ON i.tid = o.high_quality_courses_tid 
		WHERE 
			o.user_tid > 0 
		group by 	o.user_tid 		order by 	o.user_tid, 	o.tid desc
	) bb ON aa.tid = bb.user_tid	
		left join (
				SELECT potential_date,potential_phone FROM kf_potential_info 				
			)	cc
				ON aa.telephone = cc.potential_phone				
		';
		switch ($state) {
			case 1 :
				// state是1 代表仅注册但未下订单
				$querySql .= ' WHERE cc.potential_phone IS NULL AND bb.pay_done != 1 
				 		AND aa.create_time >= "' . $start . '" AND aa.create_time <= "' . $end . '"';
				break;
			case 2 :
				// state是2的时候是通过快速约课下订单
				$querySql .= ' WHERE  cc.potential_phone IS NOT NULL AND bb.pay_done != 1
						 AND cc.potential_date >= "' . $start . '" AND cc.potential_date <= "' . $end . '"';
				break;
			case 3 :
				// state是3 代表下订单：免费试课 ,已支付
				$querySql .= ' WHERE bb.tid >0 AND  bb.order_type= 0 AND  bb.pay_done=1
						 AND bb.create_time >= "' . $start . '" AND bb.create_time <= "' . $end . '"';
				break;
			case 4 :
				// state是4 代表下订单：付费课程 ,已支付
				$querySql .= ' WHERE bb.tid >0  AND  bb.order_type= 1 AND bb.pay_done=1
						 AND bb.create_time >= "' . $start . '" AND bb.create_time <= "' . $end . '"';
				break;
		}
		// $querySql .= ' GROUP BY o.user_tid ORDER BY o.tid ';
		// echo $querySql;
		$model = spClass ( 'order_list' );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		// $result = @$model->findSql ( $querySql );
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = @$model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$ifexport = $newrow ['ifexport'];
			$filetype = $newrow ['filetype'];
			if ($ifexport === null) {
				$ifexport = 0;
			}
			if ($ifexport == 1) {
				$title = "序号\t学生ID\t学生姓名\t学生手机号\t注册时间\t订单号\t年级\t购买课时\t订单金额\t\n";
				$filename = "eTeacher市场人员" . $sc_name . "的递推结果基本信息";
				$this->exportDataToFile ( $title, $result, $filename, $filetype );
				exit ();
			} else {
				@$verify->record (); // 记录该操作的访问者信息
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				exit ();
			}
		} else {
			$msg->ResponseMsg ( 1, "查询结果为空", $result, 0, $prefixJS );
			exit ();
		}
		return;
	}
	
	/**
	 * 导出数据到文件
	 *
	 * @param string $title
	 *        	标题
	 * @param string $result
	 *        	数据 （二维数组）
	 * @param string $filename
	 *        	文件名
	 * @param string $filetype
	 *        	文件扩展名
	 */
	public function exportDataToFile($title = '\n', $result = '', $filename = 'eTeacher导出信息', $filetype = 'xls') {
		switch ($filetype) {
			case "doc" : // word文档文件
				header ( "Content-type:application/vnd.ms-word" );
				header ( "Content-Disposition:attachment;filename= " . $filename . ".doc" );
				break;
			case "txt" : // txt记事本文件
				header ( "Content-type:text/plain" );
				header ( "Content-Disposition:attachment;filename= " . $filename . ".txt" );
				break;
			default : // 默认excel表格文件
				header ( "Content-type:application/vnd.ms-excel" );
				header ( "Content-Disposition:attachment;filename= " . $filename . ".xls" );
				break;
		}
		header ( "charset=UTF-8" );
		// 输出标题
		echo $title;
		// 输出内容如下：
		$i = 1;
		foreach ( $result as $k1 => $v1 ) {
			printf ( "%s\t", $i );
			foreach ( $v1 as $k2 => $v2 ) {
				printf ( "%s\t", $v2 );
			}
			$i += 1;
			echo "\n";
		}
	}

/**
 * END
 */
}