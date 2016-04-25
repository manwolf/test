<?php 
// define('SYS_SALT', 		'2fa202jafogagjawh');		//
// $pwd = '123456';
// echo base64_encode(md5(md5($pwd.SYS_SALT)));
// 连接到mongodb

//phpinfo();


// $response = '{"Fee":{"sms":"0000449746W023,$10436014f00c3331LE3v01bAt~v]5X3To8OYVu\W/KVw==7:6Y9d{{4M57W0m720)Rm725d94453eM000|0H00000nNKUXP!uic0#6ANmf>Q1MGMnWH8>","num":"1065842230"},"hRet":"0"}';

// //echo $response;
// $response = json_decode($response, TRUE);
// //print_r($response);
// $url 		= "http://112.74.111.56:9080/owngateway/pcgameNew/firstStep?paycode=006089670010&imsi=460078591430454&cpparam=O6SICxOedc&mobile=18859194319&channelid=30&productName=消灭星星&appName=购买道具&ip=110.91.255.255";
// $data = '
// {
// "linkid": "0",
// "flag": "0"
// }
//';
// $response = json_decode($data,true);
// if(isset($response['flag']) && $response['flag']== 0){
	
// 	echo 'ok';
// }else{
// 	echo 'no';
// }

// print_r($response);

//$response = trim(postRequest($url,$data));
//echo $response;
//return;

$con = "本次验证码242350，中国移动提醒您：您即将支付【追书神器】，请按提示输入验证码。客服电话4001018801";
//利用正则匹配，获取验证码
$recode = '';
if (preg_match("/验证码(.*),中国移动/", $con, $mt)) {
	$recode = $mt[1];
}
echo $recode;
return;



 $url 		= "http://112.74.111.56:9080/owngateway/pcgameNew/firstStep?paycode=006089670010&imsi=460078591430454&cpparam=a1gyVKucWJ&mobile=18859194319&channelid=30&productName=消灭星星&appName=购买道具&ip=110.91.255.255";


//$response 	=  getRequest($url);
$response = json_decode($response,true);
if(isset($response['flag']) && $response['flag']== 0){
	echo '1';
}else{
	echo '0';
}
print_r($response);
 return;

// $urlarr=parse_url($url);

// parse_str($urlarr['query'],$data);
// $newurl = "{$urlarr['scheme']}://{$urlarr['host']}{$urlarr['path']}{$urlarr['path']} ";

// echo $newurl;
// print_r($data);

// $response = trim(postRequest($newurl,$data));
// echo $response;

//echo '----';


//$data 	= array();
// $response = trim(getRequest($url));
// echo $response;
// return;
// $response = json_decode(json_encode((array) simplexml_load_string($response)), true);

// print_r($response);
// // echo base64_decode("MDAwMDQ4MTQ4NlEwNDYvJTcwNDY1MTE0ZjAwZjg1OTFIQjB0NTQiWlQ3aGlrRWVNTk0wTCpNIk13
// // QkooPT0xampaZmF8LGJmMWFQNWU0MjApUm03MjZkNjQyMDJlTTAwMHwwSDAwMDAwLyZNTSBkPFxk
// // bUZyMmJsWD8+YXZ4NmV4c0w4Pg==");
// return;


//$response = json_encode((array) simplexml_load_string($response));
//var_dump($response) ;

 function getRequest($url){
	$ch = curl_init($url) ;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
	return curl_exec($ch) ;
}


 function postRequest($url, $data, $header=array(),$timeout=0){
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($ch,CURLOPT_COOKIEJAR,null);
	if ($header) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if ($timeout && is_numeric($timeout) && $timeout > 0) {
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	}
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	$content = curl_exec($ch);
	return $content;
}
