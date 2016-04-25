<?php
header ( 'Content-type:text/html;charset=utf-8' );
 include_once '../../utf8/func/common.php';
include_once '../../utf8/func/SDKConfig.php';
include_once '../../utf8/func/secureUtil.php';
include_once '../../utf8/func/httpClient.php';
include_once '../../utf8/func/log.class.php';

/**
 *	查询交易
 */

/**
 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
 */


// 初始化日志
$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
$log->LogInfo ( "===========处理后台请求开始============" );

$params = array(
		'version' => '5.0.0',		//版本号
		'encoding' => 'utf-8',		//编码方式
		'certId' => getSignCertId (),	//证书ID	
		'signMethod' => '01',		//签名方法
		'txnType' => '00',		//交易类型	
		'txnSubType' => '00',		//交易子类
		'bizType' => '000000',		//业务类型
		'accessType' => '0',		//接入类型
		'channelType' => '07',		//渠道类型
		'orderId' => $_REQUEST['out_trade_no'],	//请修改被查询的交易的订单号
		'merId' => '898110282990734',	//商户代码，请修改为自己的商户号
		'txnTime' => $_REQUEST['out_trade_no'],	//请修改被查询的交易的订单发送时间
	);
// 签名
sign ( $params );
// 发送信息到后台
$result = sendHttpRequest ( $params, SDK_SINGLE_QUERY_URL );
$result = str_replace("=", "\":\"", $result);
$result = str_replace("&", "\",\"", $result);
$result = "{\"".substr($result, 0,strlen($result)-6)."\"}";
$result = json_decode($result);
echo "respCode{$result->respCode}";
?>

