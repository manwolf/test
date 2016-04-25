<?php
include_once 'base/crudCtr.php';
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
		switch ($time) {
			case 1 :
				// 获取当天起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
				break;
			case 2 :
				// 获取本周的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1 - 7, date ( "Y" ) ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ) - date ( "w" ) + 7 - 7, date ( "Y" ) ) );
				break;
			case 3 :
				// 获取本月的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( 'm' ), 1, date ( 'Y' ) ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( 'm' ), date ( 't' ), date ( 'Y' ) ) );
				break;
			default :
				// 全部
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, 1, 1, 1970 ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
				break;
		}
		
		$querySql = "SELECT 
									s.tid, s.sc_city , s.sc_name,	s.sc_telephone, s.post_status, 	s.sc_num, s.sc_hiredate,
									COUNT(u.fast_count = 1) AS registerAmount, 
									COUNT(u.tid) AS fastOrderAmount, 
									COUNT(o.order_type = 0) AS freeTryOrderAmount, 
									COUNT(o.order_type = 1) AS payOrderAmount 
								FROM 
									sc_user_info s
									LEFT JOIN	user_info u ON s.sc_num = u.sc_num
									LEFT JOIN	order_list o  ON o.user_tid = u.tid
								WHERE 
									s.tid > 0
				";
		
		if ($sc_name) {
			$querySql .= ' AND s.sc_name = "' . $sc_name . '"';
		}
		if ($sc_tid) {
			$querySql .= ' AND s.tid = "' . $sc_tid . '"';
		}
		if ($sc_num) {
			$querySql .= ' AND s.sc_num= "' . $sc_num . '"';
		}
		
		if ($state != NULL) {
			$querySql .= ' AND post_status= ' . $state ;
		}
		$querySql .= ' AND sc_hiredate >= "' . $start . '" AND sc_hiredate <= "' . $end . '"';
		$querySql .= ' GROUP BY s.tid ';		
		$model = spClass ( 'sc_user_info' );		
		//$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->findSql ( $querySql );
		//$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		//$pager = $model->spPager ()->getPager ();
		//$total_page = $pager ['total_page'];
		if ($result) {
			$ifexport = $newrow ['ifexport'];
			$filetype = $newrow ['filetype'];
			if ($ifexport === null) {
				$ifexport = 0;
			}
			if ($ifexport == 1) {
				$title = "序号\t市场人员ID\t城市\t姓名\t手机号\t岗位状态\t编号\t入职时间\t注册量\t快速约课量\t免费试课量\t付费下单量\t\n";
				$filename = "eTeacher市场人员基本信息";
				$this->exportDataToFile ( $title,$result, $filename,$filetype);
				exit ();
			} else {
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				exit ();
			}
		} else {
			$msg->ResponseMsg ( 0, "查询结果为空", $result, 0, $prefixJS );
			exit ();
		}
		return;
	}
	
	/**
	 * 更改市场专员在职状态
	 */
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
		$state =  $newrow ['state'];
		$time = ( integer ) $newrow ['time'];
		$telephone = $newrow ['telephone'];
		if (empty($sc_tid ) ) {
			$msg->ResponseMsg ( 1, '市场人员的ID不能为空！', false, 0, $prefixJS );
			exit ();
		}
		$condition .= ' tid  =  "'.$sc_tid.'"';		
		$model = spClass ( 'sc_user_info' );		
		$result = @$model->find( $condition );
		if(!$result){
			$msg->ResponseMsg ( 0, "市场人员不存在", fasle, 0, $prefixJS );
			exit ();
		}else{
			$sc_name = $result['sc_name'];
		}
		switch ($time) {
			case 1 :
				// 获取当天起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ), date ( "Y" ) ) );
				break;
			case 2 :
				// 获取本周的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1 - 7, date ( "Y" ) ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( "m" ), date ( "d" ) - date ( "w" ) + 7 - 7, date ( "Y" ) ) );
				break;
			case 3 :
				// 获取本月的起始时间和结束时间
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( 'm' ), 1, date ( 'Y' ) ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 23, 59, 59, date ( 'm' ), date ( 't' ), date ( 'Y' ) ) );
				break;
			default :
				// 全部
				$start = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, 1, 1, 1970 ) );
				$end = date ( "Y-m-d H:i:s", mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) ) );
				break;
		}
		
		$querySql = 'SELECT 
					o.user_tid, u.user_name, u.telephone,u.create_time,
					o.tid, o.class_content,c.class_hour,	o.order_money					 
				FROM					
					user_info u 
					LEFT JOIN order_list o  	   ON  o.user_tid = u.tid 
					LEFT JOIN class_discount c   ON  o.class_discount_tid = c.tid
				WHERE 
					 u.sc_tid= "' . $sc_tid . '" ';
		
		if ($telephone) {
			$querySql .= ' AND u.telephone = "' . $telephone . '"';
		}
		$querySql .= ' AND u.create_time >= "' . $start . '" AND u.create_time <= "' . $end . '"';
		
		switch ($state) {
			case 1 :
				// state是1 代表注册
				$querySql .= ' and o.tid > 0 ';
				break;
			case 2 :
				// state是2的时候是快速约课
				$querySql .= ' and u.fast_count > 0';
				break;
			case 3 :
				// state是3 代表免费试课
				$querySql .= ' and  o.order_type= 0 and  o.pay_done=1';
				break;
			case 4 :
				// state是4 代表付费课程
				$querySql .= ' and o.order_type=1 and o.pay_done=1';
				break;
		}
		$querySql .= ' GROUP BY o.user_tid ORDER BY o.tid   ';	
		//echo $querySql;
		$model = spClass ( 'order_list' );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->findSql ( $querySql );
		//$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		//$pager = @$model->spPager ()->getPager ();
		//$total_page = $pager ['total_page'];
		if ($result) {
			$ifexport = $newrow ['ifexport'];
			$filetype = $newrow ['filetype'];
			if ($ifexport === null) {
				$ifexport = 0;
			}
			if ($ifexport == 1) {
				$title = "序号\t学生ID\t学生姓名\t学生手机号\t注册时间\t订单号\t年级\t购买课时\t订单金额\t\n";
				$filename = "eTeacher市场人员".$sc_name."的地推结果基本信息";
				$this->exportDataToFile ( $title,$result, $filename,$filetype);
				exit ();
			} else {
				$msg->ResponseMsg ( 0, "查询成功", $result, $total_page, $prefixJS );
				exit ();
			}
		} else {
			$msg->ResponseMsg ( 0, "查询结果为空", $result, 0, $prefixJS );
			exit ();
		}		
		return;
	}
	
	/**
	 * 导出数据到文件
	 * @param string $title 标题
	 * @param string $result 数据 （二维数组）
	 * @param string $filename 文件名
	 * @param string $filetype 文件扩展名
	 */
	public function exportDataToFile($title='\n',$result='', $filename = 'eTeacher导出信息' , $filetype = 'xls') {
		switch ($filetype) {
			case "doc" : // word文档文件
				header ( "Content-type:application/vnd.ms-word" );
				header ( "Content-Disposition:attachment;filename= ".$filename.".doc" );
				break;
			case "txt" : // txt记事本文件
				header ( "Content-type:text/plain" );
				header ( "Content-Disposition:attachment;filename= ".$filename.".txt" );
				break;
			default : // 默认excel表格文件
				header ( "Content-type:application/vnd.ms-excel" );
				header ( "Content-Disposition:attachment;filename= ".$filename.".xls" );
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