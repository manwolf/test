<?php
include_once 'userInfoCtr.php';
include_once 'base/encrypt.php';
/**
 * 功能：邀请码相关信息，包括客户端的邀请码验证、使用记录，市场人员操作
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class SCInvitationCtr extends userInfoCtr {
	
	/**
	 * 客户端根据城市及邀请码，从服务器返回验证信息，包括是否有效、折扣率
	 */
	function askInvitation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		// $token = $capturs ['token'];//目前暂不验证身份
		$newrow = $capturs ['newrow'];
		$invitation_code = $newrow ['invitation_code'];
		$city = $newrow ['city'];
		if ($invitation_code == null or $city == null) {
			$msg->ResponseMsg ( 1, ' 邀请码不能为空 ! ', false, 0, $callback );
			exit ();
		}
		$querySql = 'SELECT a.invitation_name,a.invitation_discount 
					FROM invitation_info a  
					RIGHT JOIN invitation_use_list b ON b.invitation_info_tid = a.tid
					WHERE  a.invitation_valid != 1   
				 AND a.invitation_start_date <= now() AND a.invitation_end_date >= now()
				 AND b.invitation_used_times < a.invitation_times
				 AND b.invitation_code = "' . $invitation_code . '"						
				 AND a.invitation_city = "' . $city . '"';
		$model = spClass ( 'invitation_use_list' );
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 邀请码无效 ! ', false, 0, $callback );
			exit ();
		} else {
			$msg->ResponseMsg ( 0, ' 邀请码有效 ', $result, 0, $callback );
		}
	}
	
	/**
	 * 市场主管或市场专员查询邀请码信息
	 * 
	 * @return boolean
	 */
	function queryInvitationinfo() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证身份
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "sc_token", $token, "sc_user_info" ); // 市场专员
		$result2 = $verify->VerifyAuth ( "sc_token", $token, "sc_admin_info" ); // 市场主管
		if ($result1) {
			$result = $result1;
			$city = $result ['sc_city'];
		} else {
			$result = $result2;
			$city = $result ['sc_city'];
		}
		if (! $city) {
			$msg->ResponseMsg ( 1, '该市场人员无城市属性，不能继续查询！', false, 0, $callback );
			exit ();
		}
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！', false, 0, $callback );
			exit ();
		}
		$model = spClass ( 'order_list' );
		$querySql = 'SELECT a.tid,a.invitation_name,a.invitation_amount,a.invitation_discount,
				CONCAT(a.invitation_start_date," ~ ",a.invitation_end_date) AS invitation_valid_date,
				a.invitation_city,	a.invitation_times,	a.create_time,c.sc_name,							
				CASE a.invitation_valid 							
					WHEN "1" THEN  "已失效"  
					ELSE 	"使失效"					
					END AS invitation_valid,
				COUNT(b.invitation_used_times) AS  invitation_used_count
				FROM invitation_info  a  
				LEFT JOIN sc_admin_info c ON a.sc_admin_tid = c.tid
				LEFT JOIN  invitation_use_list b ON a.tid = b.invitation_info_tid AND b.invitation_used_times > 0
				WHERE   a.tid >0 ';
		if ($newrow ['state'] != null) {
			switch ($newrow ['state']) {
				case 0 : // 未开始
					$querySql .= "  AND invitation_start_date >= now() ";
					break;
				case 1 : // 已开始
					$querySql .= "  AND invitation_start_date <= now()  AND invitation_end_date >= now() ";
					break;
				case 2 : // 已结束
					$querySql .= "  AND invitation_end_date <= now() ";
					break;
				default : // 全部
					break;
			}
		}
		if ($newrow ['invitation_name'] != null)
			$querySql .= "  AND a.invitation_name 	LIKE '%" . $newrow ['invitation_name'] . "%' ";
		if ($city != "全国") {
			$querySql .= "  AND a.invitation_city 	='" . $city . "'";
		}
		$querySql .= ' GROUP BY a.tid  ORDER BY a.create_time DESC ';
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '成功', $result, $total_page, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 查无此邀请码信息，请调整搜索条件！ ', $result, $total_page, $callback );
			exit ();
		}
		return true;
	}
	
	/**
	 * 市场主管或市场专员查询某一组邀请码的详细使用列表
	 */
	function askInvitationUseList() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$state = $newrow ['state'];
		$invitation_info_tid = ( integer ) $newrow ['invitation_info_tid'];
		$invitation_code = $newrow ['invitation_code'];
		// 验证身份
		$verify = new encrypt ();
		$result1 = $verify->VerifyAuth ( "sc_token", $token, "sc_user_info" ); // 市场专员
		$result2 = $verify->VerifyAuth ( "sc_token", $token, "sc_admin_info" ); // 市场主管
		if ($result1) {
			$result = $result1;
			$city = $result ['sc_city'];
		} else {
			$result = $result2;
			$city = $result ['sc_city'];
		}
		if (! $result) {
			$msg->ResponseMsg ( 1, '令牌验证错误！ ', false, 0, $callback );
			exit ();
		}
		if (! $city) {
			$msg->ResponseMsg ( 1, '该市场人员无城市属性，不能继续查询！', false, 0, $callback );
			exit ();
		}
		if ($invitation_info_tid == null) {
			$msg->ResponseMsg ( 1, '邀请码的类别不能为空！ ', false, 0, $callback );
			exit ();
		}
		$model = spClass ( $this->tablename );
		$querySql = 'SELECT 
				a.tid, a.invitation_code,b.tid AS order_tid, b.create_time , d.class_hour,
				a.invitation_used_times,
				e.user_name ,e.user_area
				FROM invitation_use_list a 
				LEFT JOIN invitation_info f ON a.invitation_info_tid = f.tid
					LEFT JOIN order_list b ON a.invitation_code = b.invitation_code 
		     AND b.pay_done = 1
					LEFT JOIN class_discount d ON d.tid = b.class_discount_tid 
					LEFT JOIN user_info	e ON b.user_tid = e.tid 
				WHERE  a.invitation_info_tid = "' . $invitation_info_tid . '"';
		if ($city != "全国") {
			$querySql .= '  AND  f.invitation_city = "' . $city . '"';
		}
		if ($state != null) {
			// $state == 1 or $state == 0
			if ($state == 0) { // 未使用
				$querySql .= '  AND a.invitation_used_times = 0 ';
			} elseif ($state == 1) { // 已使用
				$querySql .= '  AND a.invitation_used_times > 0 ';
			}
		}
		if ($invitation_code) {
			$querySql .= '  AND a.invitation_code = "' . $invitation_code . '"';
		}
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '成功', $result, $total_page, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 邀请码不存在！', false, $total_page, $callback );
			exit ();
		}
		return true;
	}
	
	/**
	 * 市场主管使某一组邀请码失效
	 */
	function invitationInvalid() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = ( integer ) $newrow ['tid'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "sc_token", $token, "sc_admin_info" ); // 市场主管
		if (! $result) {
			$msg->ResponseMsg ( 1, '该市场人员无权限继续操作！', false, 0, $callback );
			exit ();
		}
		$city = $result ['sc_city'];
		if (! $city) {
			$msg->ResponseMsg ( 1, '该市场人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		if ($tid <= 0) {
			$msg->ResponseMsg ( 1, ' 输入的邀请码类别tid有误！', false, 0, $callback );
			exit ();
		}
		$model = spClass ( $this->tablename );
		$querySql .= ' UPDATE invitation_info SET invitation_valid= 1 WHERE  tid = ' . $tid;
		if ($city != "全国") {
			$querySql .= '  AND  invitation_city = "' . $city . '"';
		}
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			$msg->ResponseMsg ( 0, '成功', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 更改邀请码失效状态失败！ ', false, 0, $callback );
			exit ();
		}
		return true;
	}
	
	/**
	 * 市场主管创建邀请码
	 */
	function createInvitation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证身份
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "sc_token", $token, "sc_admin_info" ); // 市场主管
		if (! $result) {
			$msg->ResponseMsg ( 1, '该市场人员无权限进行此操作！', false, 0, $callback );
			exit ();
		}
		$city = $result ['sc_city'];
		$sc_tid = $result ['tid'];
		if (! $city) {
			$msg->ResponseMsg ( 1, '该市场人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		// 过滤输入信息
		$invitation_code_prefix = $newrow ['invitation_code_prefix']; // 有序邀请码的前缀
		$invitation_amount = ( integer ) $newrow ['invitation_amount']; // 保证邀请码的数量为整数
		$invitation_discount = ( float ) $newrow ['invitation_discount']; // 保证邀请码的折扣为双精度数
		$invitation_times = ( integer ) $newrow ['invitation_times']; // 保证邀请码的使用次数为整数
		$invitation_start_date = date ( 'Y-m-d', strtotime ( $newrow ['invitation_start_date'] ) ); // 保证邀请码的起始日期是YYYY-mm-dd的格式
		$invitation_end_date = date ( 'Y-m-d', strtotime ( $newrow ['invitation_end_date'] ) ); // 保证邀请码的结束日期是YYYY-mm-dd的格式
		                                                                                // 判断输入信息的有效性
		if ($newrow ['invitation_name'] == null or $invitation_amount <= 0 or $invitation_amount >= 10000 or $invitation_discount < 0 or $invitation_times <= 0 or $invitation_end_date <= "1970-01-01" or $invitation_end_date <= "1970-01-01") {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		// 非全国市场主管，才有资格创建任何城市的邀请码；
		// 普通市场主管只能创建本城市的邀请码，因此城市属性是与市场主管身份保持一致，而与输入的城市信息无关
		if ($city == "全国") {
			if ($newrow ['invitation_city'] == null) {
				$msg->ResponseMsg ( 1, '新建邀请码的城市不能为空！', false, 0, $callback );
				exit ();
			} else {
				$city = $newrow ['invitation_city'];
			}
		}
		$model = spClass ( 'invitation_info' ); // 初始化invitation_info模型类
		$result = $model->findBy ( 'invitation_name', $newrow ['invitation_name'] );
		if ($result) {
			$msg->ResponseMsg ( 1, '该邀请码名称已经存在！', false, 0, $callback );
			exit ();
		}
		if ($invitation_code_prefix) {
			$model = spClass ( 'invitation_info' ); // 初始化invitation_info模型类
			$result = $model->findBy ( 'invitation_code_prefix', $newrow ['invitation_code_prefix'] );
			if ($result) {
				$msg->ResponseMsg ( 1, '该有序邀请码的前缀已经存在！', false, 0, $callback );
				exit ();
			}
		}
		$new = array ( // 新增一组邀请码的数组
				'invitation_name' => $newrow ['invitation_name'],
				'invitation_amount' => $invitation_amount,
				'invitation_discount' => $invitation_discount,
				'invitation_times' => $invitation_times,
				'invitation_start_date' => $invitation_start_date . " 00:00:00",
				'invitation_end_date' => $invitation_end_date . " 23:59:59",
				'invitation_city' => $city,
				'invitation_code_prefix' => $invitation_code_prefix ,
				'sc_admin_tid' => $sc_tid
		);
		$model = spClass ( 'invitation_info' ); // 初始化invitation_info模型类
		$result = $model->create ( $new ); // 进行新增操作
		if ($result) {
			$tid = $result;
		} else {
			$msg->ResponseMsg ( 1, ' 客户信息添加失败！ ', false, 0, $callback );
			exit ();
		}
		// 客户信息添加成功后进行添加订单操作
		$sql = 'INSERT INTO invitation_use_list(invitation_code, invitation_info_tid)	 VALUES';
		if ($newrow ['invitation_amount'] > 0) {
			$numlength = strlen ( ( string ) $newrow ['invitation_amount'] );
			switch ($numlength) { // 目的是保证所有数字的长度一致
				case 2 : // 总数量为2位数时，小于10的数加前导符号“0”
					$prefix1 = "0";
					break;
				case 3 : // 总数量为3位数时，小于10的数加前导符号“00”，小于100的数加前导符号“0”，
					$prefix1 = "00";
					$prefix2 = "0";
					break;
				case 4 : // 总数量为4位数时，小于10的数加前导符号“000”，小于100的数加前导符号“00”，小于1000的数加前导符号“0”，
					$prefix1 = "000";
					$prefix2 = "00";
					$prefix3 = "0";
					break;
			}
			$chars1 = 'abcdfghijklmnopqrstuvwxyz'; // characters to build the password from
			$chars2 = '0123456789abcdfghijklmnopqrstuvwxyz'; // characters to build the password from
			for($i = 1; $i <= $newrow ['invitation_amount']; $i ++) {
				if ($invitation_code_prefix != null) {
					// 邀请码前缀不为空，则产生有序邀请码
					if ($i < 10) { // 前9张邀请码
						$numstr = $prefix1 . $i;
					} elseif ($i < 100) {// 第10到第99张邀请码
						$numstr = $prefix2 . $i;
					} elseif ($i < 1000) {// 第100到第999张邀请码
						$numstr = $prefix3 . $i;
					} else {// 第1000到第9999张邀请码
						$numstr = $i;
					}
					$invitation_code = $invitation_code_prefix . $numstr;
				} else {
					// 邀请码前缀为空，则产生无序邀请码
					do { // 先生产一个新的邀请码，如果发现已经有重复的邀请码存在，则更换一个新的
						$invitation_code = $this->randStr ( $chars1, 1 ) . $this->randStr ( $chars2, 2 ) . rand ( 0, 9 ) . $this->randStr ( $chars2, 1 );
						$model = spClass ( 'invitation_use_list' );
						$result = @$model->findBy ( 'invitation_code', $invitation_code );
					} while ( $result );
				}
				$sql .= '("' . $invitation_code . '",' . $tid . '),';
			}
			$sql = substr ( $sql, 0, strlen ( $sql ) - 1 );
		}
		$gb = spClass ( 'invitation_use_list' ); // 初始化invitation_use_list模型类
		$result = $gb->runSql ( $sql ); // 进行新增邀请码操作
		if (! $result) { // 如果自动生成邀请码失败，则撤销刚才在invitation_info表中创建的邀请码类别
			$gb = spClass ( 'invitation_info' );
			$gb->deleteByPk ( $tid );
			$msg->ResponseMsg ( 1, '自动生成邀请码列表失败！ ', false, 0, $callback );
			exit ();
		} else {
			$msg->ResponseMsg ( 0, ' 创建邀请码成功！ ', $result, 0, $callback );
		}
	} // 结束
	
	/**
	 * 记录邀请码的使用信息
	 */
	function recordInvitationUse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$order_tid = $newrow ['order_tid'];
		$invitation_code = $newrow ['invitation_code'];
		if ($order_tid == null or $invitation_code == null) {
			$msg->ResponseMsg ( 1, '邀请码及订单号不能为空！ ', false, 0, $callback );
			exit ();
		}
		// 判断订单是否支付完成
		$model = spClass ( "order_list" );
		$querySql = 'SELECT	 a.tid	,b.teacher_city
				FROM order_list a LEFT JOIN teacher_info b ON a.teacher_tid = b.tid
				WHERE  a.pay_done = 1  AND a.tid = "' . $order_tid . '"';
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 订单未支付成功，邀请码使用失败！', false, 0, $callback );
			exit ();
		}
		$city = $result ['0'] ['teacher_city'];
		// 判断邀请码是否失效
		$querySql = 'SELECT a.invitation_name,a.invitation_discount , b.invitation_used_times
				FROM invitation_info a  
				RIGHT JOIN invitation_use_list b ON b.invitation_info_tid = a.tid
				WHERE  a.invitation_valid != 1   
			 AND a.invitation_start_date <= now()  AND a.invitation_end_date >= now()
			 AND b.invitation_used_times < a.invitation_times
			 AND b.invitation_code = "' . $invitation_code . '"						
			 AND a.invitation_city = "' . $city . '"';
		$model = spClass ( 'invitation_use_list' );
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 邀请码无效 ! ', false, 0, $callback );
			exit ();
		}
		$invitation_used_times = $result ['0'] ['invitation_used_times'];
		// 将邀请码写入订单表中
		$model = spClass ( "order_list" );
		$querySql = 'UPDATE	 order_list SET invitation_code = "' . $invitation_code . '"
				 WHERE  tid = "' . $order_tid . '"';
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows < 1) {
			$msg->ResponseMsg ( 1, ' 邀请码使用失败！', false, 0, $callback );
			exit ();
		}
		// 将该邀请码的已使用次数加1
		$model = spClass ( "invitation_use_list" );
		$querySql = 'UPDATE invitation_use_list SET invitation_used_times = 1 + "' . $invitation_used_times . '"
					WHERE  invitation_code = "' . $invitation_code . '"';
		$result = @$model->runSql ( $querySql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows < 1) {
			$msg->ResponseMsg ( 1, ' 邀请码使用失败！', false, 0, $callback );
			exit ();
		} else {
			$msg->ResponseMsg ( 0, ' 邀请码使用完成！', $result, 0, $callback );
		}
		return true;
	}
	
	/**
	 * 市场主管导出本城市的邀请码到文件
	 */
	function exportFile() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$state = $newrow ['state'];
		$tid = $newrow ['tid'];
		$filetype = $newrow ['filetype'];
		$verify = new encrypt ();
		$result = $verify->VerifyAuth ( "sc_token", $token, "sc_admin_info" ); // 市场主管
		$city = $result ['sc_city'];
		if (! $city) {
			$msg->ResponseMsg ( 1, '该市场人员无城市属性，不能继续操作！', false, 0, $callback );
			exit ();
		}
		if ($tid == null) {
			$msg->ResponseMsg ( 1, ' 邀请码的tid不能为空！', false, 0, $callback );
			exit ();
		}
		$model = spClass ( "invitation_use_list" );
		$querySql = 'SELECT b.invitation_city,b.invitation_name,		 		
		 		CASE b.invitation_valid 							
							WHEN "1" THEN  "失效"  
							ELSE 	"有效"					
							END AS invitation_valid,
		 		invitation_amount,invitation_discount,b.invitation_start_date,invitation_end_date,
		 		invitation_times,a.invitation_code,a.invitation_used_times
		 		FROM invitation_use_list a
		 		LEFT JOIN invitation_info  b ON a.invitation_info_tid = b.tid
		 		WHERE  b.tid = "' . $tid . '"';
		if ($city != "全国") {
			$querySql .= '  AND  b.invitation_city = "' . $city . '"';
		}
		if ($state != null) {
			// $state == 1 or $state == 0
			if ($state == 0) {
				$querySql .= '  AND a.invitation_used_times = 0 ';
			} elseif ($state == 1) {
				$querySql .= '  AND a.invitation_used_times > 0 ';
			}
		}
		if ($invitation_code) {
			$querySql .= '  AND a.invitation_code = "' . $invitation_code . '"';
		}
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 1, ' 数据为空，不能导出!', false, 0, $callback );
			exit ();
		}
		switch ($filetype) {
			case "doc" : // word文档文件
				header ( "Content-type:application/vnd.ms-word" );
				header ( "Content-Disposition:attachment;filename= eTeacher邀请码.doc" );
				break;
			case "txt" : // txt记事本文件
				header ( "Content-type:text/plain" );
				header ( "Content-Disposition:attachment;filename= eTeacher邀请码.txt" );
				break;
			default : // 默认excel表格文件
				header ( "Content-type:application/vnd.ms-excel" );
				header ( "Content-Disposition:attachment;filename= eTeacher邀请码.xls" );
				break;
		}
		header ( "charset=UTF-8" );
		echo "序号\t城市\t邀请码名称\t邀请码是否有效\t邀请码数量\t邀请码折扣\t" . "邀请码开始日期\t邀请码结束日期\t邀请码有效次数\t邀请码\t邀请码的已使用次数\t\n";
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
	}
	
	/**
	 * 产生一个指定长度的随机字符串,并返回给用户
	 * 
	 * @param string $chars        	
	 * @param number $len
	 *        	产生字符串的位数
	 */
	private function randStr($chars = 'abcdfghijklmnopqrstuvwxyz', $len = 4) {
		mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
		$password = '';
		while ( strlen ( $password ) < $len )
			$password .= substr ( $chars, (mt_rand () % strlen ( $chars )), 1 );
		return $password;
	}

/**
 * END
 */
}


