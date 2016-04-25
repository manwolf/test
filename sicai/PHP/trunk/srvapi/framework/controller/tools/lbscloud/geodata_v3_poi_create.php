<?php

# 如果服务器有TestVersion.key，则运行测试环境 DOCUMENT_ROOT
$file_path="{$_SERVER['DOCUMENT_ROOT']}/test_key/TestVersion.key";
if(file_exists($file_path)){
	define('TestVersion', 'This is a test version.');
}else{

}
if(defined('TestVersion')){	
	#测试环境
	//API控制台申请得到的ak（此处ak值仅供验证参考使用）
	$ak = 'LnNqzmsWdsXev0AaWwvr3Gr0';
	
	//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
	$sk = 'ohjFUvZzy2UUTE9KX2WOPyql11GUZ6MG';
	
	//以Geocoding服务为例，地理编码的请求url，参数待填
	//$url = "http://api.map.baidu.com/geocoder/v2/?address=%s&output=%s&ak=%s&sn=%s";
	// $url = "http://api.map.baidu.com/geodata/v3/geotable/list?name=%s&output=%s&ak=%s&sn=%s";
	$url = "http://api.map.baidu.com/geodata/v3/poi/create";
	// $url = "http://localhost/test/getPost.php";
	//get请求uri前缀
	$uri = '/geodata/v3/poi/create';
	
	//构造请求串数组
	$querystring_arrays = array (
			'longitude' => $_REQUEST['longitude'],
			'latitude' => $_REQUEST['latitude'],
			'coord_type' => '3',
			'geotable_id' => '119192',
			'ak' => $ak,
			'spelling_tid' => $_REQUEST['spelling_tid']
	);
	print_r($querystring_arrays);
}else{
	#正式环境
	//API控制台申请得到的ak（此处ak值仅供验证参考使用）
	$ak = 'Wby2PCndDEAmYsWwoQnN1Bar';
	
	//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
	$sk = '1BI6fOnQv5IWU9KGqtU3NUBcEPtK1GbN';
	
	//以Geocoding服务为例，地理编码的请求url，参数待填
	//$url = "http://api.map.baidu.com/geocoder/v2/?address=%s&output=%s&ak=%s&sn=%s";
	// $url = "http://api.map.baidu.com/geodata/v3/geotable/list?name=%s&output=%s&ak=%s&sn=%s";
	$url = "http://api.map.baidu.com/geodata/v3/poi/create";
	// $url = "http://localhost/test/getPost.php";
	//get请求uri前缀
	$uri = '/geodata/v3/poi/create';
	
	//构造请求串数组
	$querystring_arrays = array (
			'longitude' => $_REQUEST['longitude'],
			'latitude' => $_REQUEST['latitude'],
			'coord_type' => '3',
			'geotable_id' => '118310',
			'ak' => $ak,
			'spelling_tid' => $_REQUEST['spelling_tid']
	);
}

function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'POST')
{
	if ($method === 'POST'){
		ksort($querystring_arrays);
	}
	$querystring = http_build_query($querystring_arrays);
	return md5(urlencode($url.'?'.$querystring.$sk));
}
//调用sn计算函数，默认get请求
$sn = caculateAKSN($ak, $sk, $uri, $querystring_arrays);

//请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
// $target = sprintf($url, urlencode($name), $output, $ak, $sn);

//输出完整请求的url（仅供参考验证，故不能正常访问服务）
// echo "url: $target \n";

$querystring_arrays['sn'] = $sn;
$postdata = http_build_query($querystring_arrays);
$opts = array('http' =>
		array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
		)
);
$context = stream_context_create($opts);
$result = file_get_contents($url, false, $context);
echo $result;

?>
