<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("/home/log/log_notify1.txt".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{			
			# 根据out_trade_no，找出order_tid
			$con = mysql_connect("localhost","root","sicai");
			if (!$con)
			{
				die('Could not connect: ' . mysql_error());
			}
			 
			mysql_select_db("eteacher", $con);
			 
			$pay_list_array = mysql_query("SELECT order_tid,pay_done FROM pay_list where out_trade_no='".$result['out_trade_no']."'");
			$pay_list_array = mysql_fetch_array($pay_list_array);
			mysql_close($con);
			 
			# 根据order_tid取出order_list表中的order_type和order_money
			$con = mysql_connect("localhost","root","sicai");
			if (!$con)
			{
				die('Could not connect: ' . mysql_error());
			}
			
			mysql_select_db("eteacher", $con);
			
			$order_list_array = mysql_query("SELECT order_type, order_money, user_tid FROM order_list where tid='".$pay_list_array['order_tid']."'");
			$order_list_array = mysql_fetch_array($order_list_array);
			mysql_close($con);			
			
			if(2 == $order_list_array['order_type'] && 0 == $pay_list_array['pay_done']){
				# 修改钱包余额
				$con = mysql_connect("localhost","root","sicai");
				if (! $con) {
					die ( 'Could not connect: ' . mysql_error () );
				}
			
				mysql_select_db ( "eteacher", $con );
			
				$sqlStr = "update wallet_list set balance = balance+".$order_list_array['order_money']." where tid>0 and user_tid=".$order_list_array['user_tid'];
				
				mysql_query ( $sqlStr );
				mysql_close ( $con );
			}
			
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			$con = mysql_connect("localhost","root","sicai");
			if (!$con)
			{
				die('Could not connect: ' . mysql_error());
			}
			mysql_select_db("eteacher", $con);
			$Sql = "update pay_list set ".
					"trade_no='".$result['transaction_id']."',buyer_id='".$result['openid'].
					"',pay_done= '1' where out_trade_no='".$result['out_trade_no']."' and tid > 0";
			$r = mysql_query($Sql);
			mysql_close($con);
			
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
