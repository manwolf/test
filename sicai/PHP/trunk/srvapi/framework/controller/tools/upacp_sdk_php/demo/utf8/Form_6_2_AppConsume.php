﻿<?php
header ( 'Content-type:text/html;charset=utf-8' );
include_once '../../utf8/func/common.php';
include_once '../../utf8/func/SDKConfig.php';
include_once '../../utf8/func/secureUtil.php';
include_once '../../utf8/func/httpClient.php';
include_once '../../utf8/func/log.class.php';
/**
 * 消费交易-控件后台订单推送 
 */


/**
 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
 */
// 初始化日志
$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
$log->LogInfo ( "============处理前台请求开始===============" );
// 初始化日志
$params = array(
		'version' => '5.0.0',				//版本号
		'encoding' => 'utf-8',				//编码方式
		'certId' => getSignCertId (),			//证书ID
		'txnType' => '01',				//交易类型	
		'txnSubType' => '01',				//交易子类
		'bizType' => '000201',				//业务类型
		'frontUrl' =>  constant("SDK_FRONT_NOTIFY_URL"),  		//前台通知地址，控件接入的时候不会起作用
		'backUrl' => constant("SDK_BACK_NOTIFY_URL"),		//后台通知地址	
		'signMethod' => '01',		//签名方法
		'channelType' => '08',		//渠道类型，07-PC，08-手机
		'accessType' => '0',		//接入类型
		'merId' => '898110282990734',	//商户代码，请改自己的测试商户号
		'orderId' => date('YmdHis'),	//商户订单号，8-40位数字字母
		'txnTime' => date('YmdHis'),	//订单发送时间
		'txnAmt' => $_REQUEST['txnAmt'],		//交易金额，单位分
		'currencyCode' => '156',	//交易币种
		'orderDesc' => '订单描述',  //订单描述，可不上送，上送时控件中会显示该信息
		'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		);
// 签名
sign ( $params );
// 发送信息到后台
$result = sendHttpRequest ( $params, SDK_App_Request_Url );
$result = str_replace("=", "\":\"", $result);
$result = str_replace("&", "\",\"", $result);
$result = "{\"".substr($result, 0,strlen($result)-6)."\"}";
$result = json_decode($result);
// print_r($params);
// print_r($result);
echo "tn{$result->tn}orderId{$result->orderId}";
?>

