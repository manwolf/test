<?php
include_once '../../utf8/func/common.php';
include_once '../../utf8/func/secureUtil.php';
# 如果服务器有TestVersion.key，则运行测试环境
$file_path="/home/chenhr/TestVersion.key";
if(file_exists($file_path)){
	define('TestVersion', 'This is a test version.');
}else{

}
# 根据out_trade_no，找出order_tid
$con = mysql_connect("localhost","root","sicai");
if (!$con)
{
	die('Could not connect: ' . mysql_error());
}
mysql_select_db("eteacher", $con);
# 根据out_trade_no取出order_tid, pay_done, invitation_code
$result = mysql_query("SELECT order_tid, pay_done, invitation_code FROM pay_list where out_trade_no=\"{$_REQUEST['orderId']}\"");
$pay_list_array = mysql_fetch_array($result);
# 根据order_tid取出order_list表中的order_type，order_money和user_tid
$result = mysql_query("SELECT order_type, order_money, user_tid FROM order_list where tid='".$pay_list_array['order_tid']."'");
$order_list_array = mysql_fetch_array($result);
if(2 == $order_list_array['order_type'] && 0 == $pay_list_array['pay_done']){
	# 修改钱包余额
	$sqlStr = "update wallet_list set balance = balance+".$order_list_array['order_money']." where tid>0 and user_tid=".$order_list_array['user_tid'];
	if( !mysql_query ( $sqlStr )){
		echo "({\"code\":1,\"msg\":\"修改钱包价格失败！\",\"data\":[{}],\"pages\":0})";
	}
}
# 判断邀请码是否不存在或已失效！
if($pay_list_array['invitation_code'] && 0 == $pay_list_array['pay_done']){
	# 修改使用邀请码后的订单价格
	# 根据订单号找出用户所在的城市
	$Str = "select u.user_city from order_list o, user_info u where o.user_tid = u.tid
	and o.tid =\"{$pay_list_array['order_tid']}\"";
	$user_city_array = mysql_query ( $Str );
	$user_city_array = mysql_fetch_array($user_city_array);
	$user_city = $user_city_array['user_city'];
	# 查询折扣率
	$Str = "SELECT i.invitation_discount
	FROM invitation_info i, invitation_use_list u
	WHERE invitation_valid != 1 and u.invitation_info_tid = i.tid
	and invitation_start_date <= now() and invitation_end_date >= now()
	and u.invitation_code = \"{$pay_list_array['invitation_code']}\"
	and invitation_city = \"{$user_city}\";";
	$invitation_discount_array = mysql_query ( $Str );
	$invitation_discount_array = mysql_fetch_array($invitation_discount_array);
	$invitation_discount = $invitation_discount_array['invitation_discount'];
	# 根据折扣率，计算出最终价格
	$price = $order_list_array['order_money'] * $invitation_discount;
	# 修改订单价格
	$updateStr = "update order_list set order_money = {$price} where tid= {$pay_list_array['order_tid']}";
	mysql_query ( $updateStr );
	# 判断邀请码是否有效，并加入邀请码记录
	$Sql = 'SELECT a.invitation_name,a.invitation_discount
					FROM invitation_info a
					right join invitation_use_list b on b.invitation_info_tid = a.tid
					WHERE a.invitation_valid != 1
				and a.invitation_start_date <= now() and a.invitation_end_date >= now()
				and b.invitation_used_times < a.invitation_times
				and b.invitation_code = "'.$pay_list_array['invitation_code'].'"
				and a.invitation_city = "'.$user_city.'"';
	if ( !mysql_query ($Sql) ){
		echo "({\"code\":1,\"msg\":\"邀请码不存在或已失效！\",\"data\":[{}],\"pages\":0})";
	}
	# 将邀请码写入订单表中
	$Sql ='update order_list set invitation_code = "'.$pay_list_array['invitation_code'].'"
					 where tid = "'.$pay_list_array['order_tid'].'"';
	mysql_query ($Sql);
	# 将该邀请码的已使用次数加1
	$Sql = 'UPDATE invitation_use_list SET invitation_used_times = invitation_used_times+ 1
						WHERE  invitation_code = "' . $pay_list_array['invitation_code'] . '"';
	mysql_query ($Sql);
}
# 完成支付
$sqlStr = "update pay_list set pay_done = 1 where tid >0 && out_trade_no=\"{$_REQUEST['orderId']}\"";
mysql_query ( $sqlStr );
mysql_close ( $con );

?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>响应页面</title>

<style type="text/css">
body table tr td {
	font-size: 14px;
	word-wrap: break-word;
	word-break: break-all;
	empty-cells: show;
}
</style>
</head>
<body>
	<table width="800px" border="1" align="center">
		<tr>
			<th colspan="2" align="center">响应结果</th>
		</tr>
	
			<?php
			foreach ( $_POST as $key => $val ) {
				?>
			<tr>
			<td width='30%'><?php echo isset($mpi_arr[$key]) ?$mpi_arr[$key] : $key ;?></td>
			<td><?php echo $val ;?></td>
		</tr>
			<?php }?>
			<tr>
			<td width='30%'>验证签名</td>
			<td><?php			
			if (isset ( $_POST ['signature'] )) {
				
				echo verify ( $_POST ) ? '验签成功' : '验签失败';
				$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
			} else {
				echo '签名为空';
			}
			?></td>
		</tr>
	</table>
</body>
</html>
