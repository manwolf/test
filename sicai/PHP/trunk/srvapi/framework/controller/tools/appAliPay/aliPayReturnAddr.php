<?php
if($_REQUEST['alipay_user_trade_search_response']['trade_record']['order_status'] == "TRADE_FINISHED"){
	# 业务逻辑代码
	
	echo "SUCCESS";
}else{
	# 业务逻辑代码
	
	echo "fail";
}