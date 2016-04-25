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
		$url = "http://api.map.baidu.com/geosearch/v3/nearby?ak=%s&geotable_id=%s&location=%s&radius=%s&sortby=%s&sn=%s";
		
		//get请求uri前缀
		$uri = substr($url, (strpos($url,'.com/')+4), (strpos($url,'?')-strpos($url,'com/')-3) );
		
		//构造请求串数组
		$querystring_arrays = array (
				'ak' => $ak,
				'geotable_id' => '119192',
				'location' => "{$_REQUEST['longitude']},{$_REQUEST['latitude']}", //  121.516511,31.152098
				'radius' => '45000',
				'sortby' => 'distance:1'
		);
}else{
		#正式环境
		//API控制台申请得到的ak（此处ak值仅供验证参考使用）
		$ak = 'Wby2PCndDEAmYsWwoQnN1Bar';
		
		//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
		$sk = '1BI6fOnQv5IWU9KGqtU3NUBcEPtK1GbN';
		
		//以Geocoding服务为例，地理编码的请求url，参数待填
		$url = "http://api.map.baidu.com/geosearch/v3/nearby?ak=%s&geotable_id=%s&location=%s&radius=%s&sortby=%s&sn=%s";
		
		//get请求uri前缀
		$uri = substr($url, (strpos($url,'.com/')+4), (strpos($url,'?')-strpos($url,'com/')-3) );
		
		//构造请求串数组
		$querystring_arrays = array (
				'ak' => $ak,
				'geotable_id' => '118310',
				'location' => "{$_REQUEST['longitude']},{$_REQUEST['latitude']}", //121.516511,31.152098
				'radius' => '45000',
				'sortby' => 'distance:1'
		);
			
}

function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET')
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
$target = sprintf($url, $ak, $querystring_arrays['geotable_id'], urlencode($querystring_arrays['location']), $querystring_arrays['radius'], urlencode($querystring_arrays['sortby']),  $sn);
 
echo file_get_contents($target);
?>
