<?php
include_once 'automatic.php';
include_once 'userInfoCtr.php';
/**
 * 功能：支付环境所使用的相关接口，包括免费试课支付、付费支付、钱包充值
 * 作者： 陈鸿润
 * 创建日期：2015年8月27日
 * 修改日期：2015年9月1日
 */
class payListCtr extends userInfoCtr {
	public function __construct() {
		$this->tablename = 'pay_list';
	}
	// 子类对update实现空操作
	public function update() {
		exit ();
	}
	// 子类对query实现空操作
	public function query() {
		exit ();
	}
	/**
	 * 免费试课支付接口
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日 
	 */
	public function add() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 查询订单对应用户的tid和user_free_num（ 免费试课次数）
		$order_tid = $newrow ['order_tid'];
		$query = 'select u.tid,u.user_free_num from  order_list o,user_info u where o.user_tid=u.tid and o.tid=' . $order_tid;
		$model = spClass ( $this->tablename );
		$resultquery = @$model->findSql ( $query );
		// 验证请求参数 和 token
		if (! $this->verifyUserToken($newrow["token"], $resultquery[0]["tid"]) ) {
			$msg->ResponseMsg ( 1, $this->tokenFailedtipString, null, 1, $prefixJS );
			return;
		}
		// 为0时可以免费支付，为1时可以免费支付（每个用户只能免费支付一次）
		if (0 == $resultquery [0] ['user_free_num']) {			
			// 根据请求参数，对pay_list进行insert操作
			$model = spClass ( $this->tablename );
			$result = @$model->create ( $newrow );
			if ($result <= 0) { // insert失败
				return;
			}
			$result = @$model->findAll ( array ( // 根据insert后，新生成tid，查询pay_list表的所有信息
					$model->pk => $result 
			) );
			// 判断对pay_list查询是否成功
			if (count ( $result ) > 0) {
				// 根据订单获取学生ID 和 用户免费试课次数
				$query = 'select u.tid,u.user_free_num from  order_list o,user_info u where o.user_tid=u.tid and o.tid=' . $order_tid;
				$model = spClass ( $this->tablename );
				$resultqu = @$model->findSql ( $query );
				// 当支付成功后，用户免费状态改变（ 实际操作：user_free_num+1 ）
				$tid = $resultqu [0] ['tid'];
				$update = 'update user_info set user_free_num=user_free_num+1 where tid=' . $tid;
				$model = spClass ( $this->tablename );
				$upresult = @$model->runSql ( $update );
				$msg->ResponseMsg ( 0, '免费订单支付已成功！', $result, 1, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '免费订单您只能支付一次！', $result, 1, $prefixJS );
		}
	}
	/**
	 * insert支付清单
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */ 
	public function addPayList() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证token
		$model = spClass ( 'user_info' );
		$Sql = "select tid from user_info where token='" . $token . "' and tid ='" . $newrow ['user_tid'] . "'";
		$token_result = $model->findSql ( $Sql );
		if (! $token_result [0] ['tid']) {
			echo $prefixJS . "({\"code\":1,\"msg\":\"Token error.\",\"data\":[{}],\"pages\":0})";
			return false;
		}
		// 如果是钱包充值，则往order_list（订单表）插入一条钱包订单（order_type为2）
		if (1 == $newrow ['pay_method']) {
			$model = spClass ( 'order_list' );
			$conditions = array (
					'user_tid' => $newrow ['user_tid'],
					'order_type' => '2',
					'order_money' => $newrow ['money'] 
			);
			$order_list_tid = $model->create ( $conditions );
			$newrow ['order_tid'] = $order_list_tid;
		}
		// 如果pay_type等于3，则为银联支付，该接口返回tn（银联流水号）和 out_trade_no（商户自定义订单号）
		if (3 == $newrow ['pay_type']) {
			// 根据订单取出支付价格
			$queryMoney = "select order_money from order_list where tid={$newrow['order_tid']}";
			$order_money_array = spClass ( 'order_list' )->findSql ( $queryMoney );
			$order_money = $order_money_array [0] ['order_money'];
			// 如果使用了邀请码，则修改支付价格；如果没有使用，则以原价对银联支付，发送请求
			if ($newrow ['invitation_code']) {
				// 根据user_tid，找到user所在城市
				// 然后调用类SCInvitationCtr中的askInvitation接口，查询折扣率
				// 最后用订单价格 乘以 折扣率，计算出发起请求的价格
				$queryMoney = "select user_city from user_info where tid={$newrow['user_tid']}";
				$user_city_array = spClass ( 'user_info' )->findSql ( $queryMoney );
				$user_city = $user_city_array [0] ['user_city'];
				$url = "http://{$_SERVER['HTTP_HOST']}/srvapi/framework/index.php?c=SCInvitationCtr&a=askInvitation&invitation_code={$newrow['invitation_code']}&city={$user_city}";
				$invitation_discount_array = json_decode ( substr ( file_get_contents ( $url ), 1, strlen ( file_get_contents ( $url ) ) - 2 ) );
				$order_money = $order_money * $invitation_discount_array->data [0]->invitation_discount;
			}
			// 将价格单位转化为分
			$real_order_money = ( int ) ($order_money * 100);
			if (defined ( 'TestVersion' )) { // 测试环境 将测试环境的请求金额设置为1分
				$real_order_money = 1;
			} else { // 正式环境
			}
			// 调用银联请求接口
			$url = "http://{$_SERVER['HTTP_HOST']}/srvapi/framework/controller/tools/upacp_sdk_php/demo/utf8/Form_6_2_AppConsume.php?txnAmt={$real_order_money}";
			$response = file_get_contents ( $url );
			// 处理返回数据
			$response = json_encode ( array (
					'tn' => $response 
			) );
			$response = str_replace ( "\ufeff", "", $response );
			$response = str_replace ( "{\"tn\":\"", "", $response );
			$response = substr ( $response, 0, strlen ( $response ) - 6 );
			$explode_by_tn = explode ( 'tn', $response );
			$explode_by_orderId = explode ( 'orderId', $explode_by_tn [1] );
			// 给需要赋值的变量赋值
			$tn = $explode_by_orderId [0];
			$orderId = $explode_by_orderId [1];
			$result = array (
					'tn' => $tn,
					'out_trade_no' => $orderId 
			);
			$newrow ['trade_no'] = $tn;
			$newrow ['out_trade_no'] = $orderId;
		}
		// 添加支付清单
		$model = spClass ( $this->tablename );
		$addSql = 'insert ' . $this->tablename . ' set ';
		unset ( $newrow ['user_tid'] );
		unset ( $newrow ['money'] );
		foreach ( $newrow as $k => $v ) {
			$addSql = $addSql . $k . '="' . $v . '",';
		}
		$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
		$result = $model->runSql ( $addSql );
		// 如果为银联支付，则返回tn（银联流水号） 和 out_trade_no(商户自定义订单号）
		if (3 == $newrow ['pay_type']) {
			// $msg->ResponseMsg ( 0, 'success', array('tn'=>$tn, 'out_trade_no'=>$orderId), 0, $prefixJS );
			echo $prefixJS . "({\"code\":0,\"msg\":\"Success\",\"data\":[{\"tn\":\"{$tn}\",\"out_trade_no\":\"{$orderId}\"}],\"pages\":0})";
		} else {
			echo $prefixJS . "({\"code\":0,\"msg\":\"Success\",\"data\":[{}],\"pages\":0})";
		}
	}
	/**
	 * 完成支付（修改订单状态,并执行相应操作）
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function updatePayList() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		// 验证token
		$model = spClass ( 'user_info' );
		$Sql = "select tid from user_info where token='" . $token . "' and tid ='" . $newrow ['user_tid'] . "'";
		$token_result = $model->findSql ( $Sql );
		if (! $token_result [0] ['tid']) {
			echo $prefixJS . "({\"code\":1,\"msg\":\"Token error.\",\"data\":[{}],\"pages\":0})";
			return false;
		}
		// 判断是否支付成功
		// 判断pay_type类型
		$model = spClass ( 'pay_list' );
		$Sql = "select pay_type from pay_list where out_trade_no='" . $newrow ['out_trade_no'] . "'";
		$pay_list_array = $model->findSql ( $Sql );
		// 当pay_type为null时，不会进入微信支付
		if (null == $pay_list_array [0] [pay_type]) {
			$pay_list_array [0] [pay_type] = 10000;
		}
		// 对支付状态进行主动查询
		// 0为 微信支付 1为支付宝支付 2为钱包支付 3为银联支付
		switch ($pay_list_array [0] [pay_type]) {
			case 0 : // 微信支付
				if (defined ( 'TestVersion' )) {
					// 测试环境
					$url = "http://testapi.e-teacher.cn/srvapi/framework/controller/tools/WxpayAPI_php_v3/example/orderquery.php?out_trade_no=" . $newrow ['out_trade_no'];
				} else { // 正式环境
					$url = "http://api.e-teacher.cn/srvapi/framework/controller/tools/WxpayAPI_php_v3/example/orderquery.php?out_trade_no=" . $newrow ['out_trade_no'];
				}
				$file_contents = file_get_contents ( $url );
				$start_num = strpos ( $file_contents, "trade_state" );
				$str = substr ( $file_contents, $start_num + 21, 7 );
				if (! ('SUCCESS' == $str)) {
					echo $prefixJS . "({\"code\":1,\"msg\":\"wxPay verification error.\",\"data\":[{}],\"pages\":0})";
					exit ();
				}
				break;
			case 1 : // 支付宝支付
				break;
			case 2 : // 钱包支付
			         // 加入版本机制后，把walletCtr中的payByWallet改到这里实现
				break;
			case 3 : // 银联支付
				if (defined ( 'TestVersion' )) {
					// 测试环境
					$url = "http://testapi.e-teacher.cn/srvapi/framework/controller/tools/upacp_sdk_php/demo/utf8/Form_6_5_Query.php?out_trade_no=" . $newrow ['out_trade_no'];
				} else { // 正式环境
					$url = "http://api.e-teacher.cn/srvapi/framework/controller/tools/upacp_sdk_php/demo/utf8/Form_6_5_Query.php?out_trade_no=" . $newrow ['out_trade_no'];
				}
				$file_contents = file_get_contents ( $url );
				$file_contents_by_respCode = explode ( 'respCode', $file_contents );
				if (! (00 == $file_contents_by_respCode [1])) {
					echo $prefixJS . "({\"code\":1,\"msg\":\"unionPay verification error.\",\"data\":[{}],\"pages\":0})";
					exit ();
				}
				break;
			default : // 其他
				echo $prefixJS . "({\"code\":0,\"msg\":\"Does not support this payment.\",\"data\":[{}],\"pages\":0})";
				exit ();
				break;
		}
		// update支付清单
		// 查询pay_method,order_tid,pay_done,invitation_code
		$model = spClass ( 'pay_list' );
		$Sql = "select pay_method,order_tid,pay_done,invitation_code from pay_list where out_trade_no='" . $newrow ['out_trade_no'] . "'";
		$pay_list_array = $model->findSql ( $Sql );
		// 判断钱包是否充值
		if (1 == $pay_list_array [0] ['pay_method'] && 0 == $pay_list_array [0] ['pay_done']) {
			// 根据order_tid找出充值金额
			$model = spClass ( 'order_list' );
			$Sql = "select order_money from order_list where tid=" . $pay_list_array [0] ['order_tid'];
			$order_money_array = $model->findSql ( $Sql );
			// 修改钱包余额
			$updateSql = "update wallet_list set balance = balance+" . $order_money_array [0] ['order_money'] . " where tid>0 and user_tid=" . $newrow ['user_tid'];
			$model = spClass ( 'wallet_list' );
			$model->runSql ( $updateSql );
		}
		// 判断pay_list中，是否使用邀请码,若使用了邀请码，则修改订单金额，并插入记录
		if ($pay_list_array [0] ['invitation_code'] && 0 == $pay_list_array ['pay_done']) {
			// 修改订单金额
			// 根据订单号找出用户所在的城市,并找出价格
			$queryOrderStr = "select u.user_city, o.order_money from order_list o, user_info u where o.user_tid = u.tid
				and o.tid =\"{$pay_list_array[0]['order_tid']}\"";
			$model = spClass ( 'order_list' );
			$order_list_array = $model->findSql ( $queryOrderStr );
			// 查询折扣率
			$queryInvitationDiscountStr = "SELECT i.invitation_discount
				FROM invitation_info i, invitation_use_list u
				WHERE invitation_valid != 1 and u.invitation_info_tid = i.tid
				and invitation_start_date <= now() and invitation_end_date >= now()
				and u.invitation_code = \"{$pay_list_array[0]['invitation_code']}\"
				and invitation_city = \"{$order_list_array[0]['user_city']}\";";
			$model = spClass ( 'invitation_info' );
			$invitation_discount_array = $model->findSql ( $queryInvitationDiscountStr );
			// 根据折扣率，计算出最终价格
			$price = $order_list_array [0] ['order_money'] * $invitation_discount_array [0] ['invitation_discount'];
			// 修改订单价格
			$updateStr = "update order_list set order_money = {$price} where tid= {$pay_list_array[0]['order_tid']}";
			$model = spClass ( 'order_list' );
			if (! $model->runSql ( $updateStr )) {
				echo $prefixJS . "({\"code\":1,\"msg\":\"订单价格修改失败.\",\"data\":[{}],\"pages\":0})";
			}
			// 调用邀请码插入记录接口
			$url = "http://{$_SERVER['HTTP_HOST']}/srvapi/framework/index.php?c=SCInvitationCtr&a=recordInvitationUse&invitation_code=" . $pay_list_array [0] ['invitation_code'] . "&order_tid=" . $pay_list_array [0] ['order_tid'] . "&callback=" . $prefixJS;
			file_get_contents ( $url );
		}
		// 修改pay_done = 1
		$updateSql = "update pay_list set pay_done= '1' where out_trade_no=\"{$newrow['out_trade_no']}\" and tid > 0";
		$model = spClass ( 'pay_list' );
		// 如果该订单已支付，则update失败，返回失败信息
		if (! $model->runSql ( $updateSql )) {
			echo $prefixJS . "({\"code\":1,\"msg\":\"该订单已支付.\",\"data\":[{}],\"pages\":0})";
		}
		// 判断该学生支付订单是否是拼课订单
		$querySql = 'select user_spelling_lesson_tid from order_list where order_type=3 and user_tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if ($result [0] ['user_spelling_lesson_tid'] > 0) { // 若有拼课订单		                                                   
			// 查询拼课信息中的支付人数和参与者的人数
			$querySql = 'select count(o.pay_done) as pay_number,u.participants_number from user_spelling_lesson u,order_list o where u.tid=o.user_spelling_lesson_tid and o.pay_done=1 and  u.tid=' . $result [0] ['user_spelling_lesson_tid'];
			$model = spClass ( $this->tablename );
			$resultqs = $model->findSql ( $querySql );
			if ($result [0] ['pay_number'] == $result [0] ['participants_number']) { // 支付人数与参与人数相同的话 则拼课生效
			                                                                        
				// 当所有人支付完成后拼课信息改为已支付
				$updateSql = 'update user_spelling_lesson set pay_done=1 where tid=' . $result [0] ['user_spelling_lesson_tid'];
				$model = spClass ( $this->tablename );
				$resultus = $model->runSql ( $updateSql );
				// 每人只能参与一个拼客 当支付之后 又可以发起或参与新的拼客
				// $update='update user_info set spelling_class_state=0 where tid='.$user_tid;
				// $model = spClass ( $this->tablename );
				// $resultup = $model->runSql ( $update );
			}
		}		
		// 当免费试课支付成功自动生成课程记录
		// 查询order_list tid
		$model = spClass ( 'pay_list' );
		$Sql = "select pay_method,order_tid from pay_list where out_trade_no='" . $newrow ['out_trade_no'] . "'";
		$pay_list_array = $model->findSql ( $Sql );
		// $tid = $result['0']['tid'];
		$tid = $pay_list_array [0] ['order_tid'];
		$querySql = 'select * from order_list where tid=' . $tid;
		$model = spClass ( 'order_list' );
		$resultdis = $model->findSql ( $querySql );
		if (0 == $resultdis [0] ['order_type'] && 1 == $resultdis [0] ['pay_done']) {
			$order_tid = $resultdis ['0'] ['tid'];
			$user_tid = $resultdis ['0'] ['user_tid'];
			$order_date = $resultdis ['0'] ['order_date'];
			$automatic = new automatic ();
			$automatic->addOrderList ( $order_tid, $user_tid, $order_date );
		}
		echo $prefixJS . "({\"code\":0,\"msg\":\"verification success.\",\"data\":[{}],\"pages\":0})";
	}
	/**
	 * 客服查询微信是否支付成功，和广兢商量，加到广兢的接口中
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function queryOrderForKF() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$file_contents = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		if (defined ( 'TestVersion' )) {
			// 测试环境
			$url = "http://testapi.e-teacher.cn/srvapi/framework/controller/tools/WxpayAPI_php_v3/example/orderquery.php?out_trade_no=" . $newrow ['out_trade_no'];
		} else { // 正式环境
			$url = "http://api.e-teacher.cn/srvapi/framework/controller/tools/WxpayAPI_php_v3/example/orderquery.php?out_trade_no=" . $newrow ['out_trade_no'];
		}
		// $url = "http://testapi.e-teacher.cn/srvapi/framework/controller/tools/WxpayAPI_php_v3/example/orderquery.php?out_trade_no=2ea279ca696946aceb4337fb1ba9b23a";
		$file_contents = file_get_contents ( $url );
		$start_num = strpos ( $file_contents, "</head>" );
		$file_contents = substr ( $file_contents, $start_num + 7, strlen ( $file_contents ) );
		$file_contents = preg_replace ( "'<table[^>]*?>'", "", $file_contents );
		$file_contents = preg_replace ( "'<br[^>]*?>'", "", $file_contents );
		$file_contents = preg_replace ( "'<font[^>]*?>'", '","', $file_contents );
		$file_contents = preg_replace ( "'</font[^>]*?>'", '"', $file_contents );
		$file_contents = str_replace ( ' : ', ':"', $file_contents );
		$start_num = strpos ( $file_contents, "," );
		$file_contents = substr ( $file_contents, $start_num + 1, strlen ( $file_contents ) );
		$file_contents = str_replace ( ' ', '', $file_contents );
		$file_contents = '{' . $file_contents . '"}';
		$file_contents = json_decode ( $file_contents );
		$msg->ResponseMsg ( 0, 'success', true, $file_contents, $prefixJS );
	}
	/**
	 * 手机网页支付接口（微信支付、支付宝支付、银联支付）
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function payForH5() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		// 根据订单取出支付价格
		$queryMoney = "select user_tid,order_money from order_list where tid={$newrow['order_tid']}";
		$order_array = spClass ( 'order_list' )->findSql ( $queryMoney );
		// 验证token
		$model = spClass ( 'user_info' );
		$Sql = "select tid from user_info where token='" . $newrow['token'] . "' and tid ='" . $order_array [0] ['user_tid'] . "'";
		$token_result = $model->findSql ( $Sql );
		if (! $token_result [0] ['tid']) {
			echo $prefixJS . "({\"code\":1,\"msg\":\"Token error.\",\"data\":[{}],\"pages\":0})";
			return false;
		}
		// 取出支付价格		
		$order_money = $order_array [0] ['order_money'];

		// 如果使用了邀请码，则修改支付价格；如果没有使用，则以原价对银联支付，发送请求
		if ($newrow ['invitation_code']) {
			// 根据user_tid，找到user所在城市
			// 然后调用类SCInvitationCtr中的askInvitation接口，查询折扣率
			// 最后用订单价格 乘以 折扣率，计算出发起请求的价格
			$queryMoney = "select u.user_city from order_list o, user_info u where o.user_tid = u.tid 
					and o.tid =\"{$newrow['order_tid']}\"";
			$user_city_array = spClass ( 'user_info' )->findSql ( $queryMoney );
			$user_city = $user_city_array [0] ['user_city'];
			$url = "http://{$_SERVER['HTTP_HOST']}/srvapi/framework/index.php?c=SCInvitationCtr&a=askInvitation&invitation_code={$newrow['invitation_code']}&city={$user_city}";
			$invitation_discount_array = json_decode ( substr ( file_get_contents ( $url ), 1, strlen ( file_get_contents ( $url ) ) - 2 ) );
			$order_money = $order_money * $invitation_discount_array->data [0]->invitation_discount;
		}
		// 将价格单位转化为分
		$real_order_money = ( int ) ($order_money * 100);
		if (defined ( 'TestVersion' )) { // 测试环境 将测试环境的请求金额设置为1分
			$real_order_money = 1;
		} else { // 正式环境
		}
		unset($newrow["money"]);	
		$timeStamp = time ();
		$newrow['out_trade_no'] = $timeStamp;
		$result = spClass("pay_list")->create($newrow);
	
		switch ($newrow ['pay_type']) {
			case 0 : // 微信支付
				break;
			case 1 : // 支付宝支付
				break;
			case 3 : // 银联支付
				if (defined ( 'TestVersion' )) {
					Header ( "Location: http://testapi.e-teacher.cn/srvapi/framework/controller/tools/upacp_sdk_php/demo/utf8/Form_6_2_FrontConsume.php?txnAmt={$real_order_money}&orderId={$timeStamp}" );
				} else {
					Header ( "Location: http://api.e-teacher.cn/srvapi/framework/controller/tools/upacp_sdk_php/demo/utf8/Form_6_2_FrontConsume.php?txnAmt={$real_order_money}&orderId={$timeStamp}" );
				}
				$msg = new responseMsg ();
				$msg->ResponseMsg ( 0, '支付成功！', true, 0, $prefixJS );
				break;
		}
	}
}