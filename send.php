<?php
header("Content-Type:text/html;charset=utf-8");
$apikey = "06b96681b629d606cb8af2b0339fbb41"; //修改为您的apikey(https://www.yunpian.com)登陆官网后获取
$mobile = urlencode("13761382964"); //请用自己的手机号代替  //18116979508   15300588773   13761382964
$text="上海，2月26日周五，晴，6-14° 。";

	$ch = curl_init();
	/* 设置验证方式 */
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
	
	/* 设置返回结果为流 */
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	/* 设置超时时间*/
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
	/* 设置通信方式 */
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 发送模板短信
// 需要对value进行编码
$data=array('tpl_id'=>'1248305','tpl_value'=>urlencode('#content#').'='.urlencode($text),'apikey'=>$apikey,'mobile'=>$mobile);

$json_data = tpl_send($ch,$data);

echo $json_data;



curl_close($ch);

/***************************************************************************************/
//获得账户信息
function get_user($ch,$apikey){
	curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/user/get.json');
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('apikey' => $apikey)));
	return curl_exec($ch);
}
/**
 * 发送短信
 * @param unknown $ch
 * @param unknown $data
 * @return mixed
 */
function send($ch,$data){
	curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/send.json');
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	return curl_exec($ch);
}
/**
 * 发送模板短信
 * @param unknown $ch
 * @param unknown $data
 */
function tpl_send($ch,$data){
	curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/tpl_send.json');
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	return curl_exec($ch);
}
/**
 * 发送音频验证
 * @param unknown $ch
 * @param unknown $data
 */
function voice_send($ch,$data){
	curl_setopt ($ch, CURLOPT_URL, 'http://voice.yunpian.com/v1/voice/send.json');
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	return curl_exec($ch);
}

?>