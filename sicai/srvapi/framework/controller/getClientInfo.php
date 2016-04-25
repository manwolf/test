<?php
include_once 'base/crudCtr.php';
/**
 * 目前，该接口已废弃。
 * 功能：获取客户端信息
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class getClientInfo extends crudCtr {
	
	/**
	 * 获取客户端城市信息以及经纬度
	 */
	public function getClientCity(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) $ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) $ip = getenv("REMOTE_ADDR");
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = "unknown";
		//echo $ip;
		if($ip == "unknown"){			
			$msg->ResponseMsg ( 1, '未找到IP地址！', false, 0, $callback );
			exit();
		}
		//$ip = '116.231.193.252';//本地测试用ip
		//API控制台申请得到的ak
		//$ak = 'LnNqzmsWdsXev0AaWwvr3Gr0';
		$ak = 'AiX9s5vGm7R4QKgk5aDvbooV';
		//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看
		$sk = 'ohjFUvZzy2UUTE9KX2WOPyql11GUZ6MG';					
		$url = "http://api.map.baidu.com/location/ip?&ip=%s&output=%s&ak=%s&sn=%s&coor=bd09ll";
		//coor不出现时，默认为百度墨卡托坐标；coor=bd09ll时，返回为百度经纬度坐标
		$output = 'json';
		//get请求uri前缀		
		$uri = substr($url, (strpos($url,'.com/')+4), (strpos($url,'?')-strpos($url,'com/')-3) );
		//echo "{$uri} /n";		
		//构造请求串数组
		$querystring_arrays = array (	
				'ip' => $ip,
				'ak' => $ak,				
				'output' => $output,
		);		
		//调用sn计算函数，默认get请求
		$sn = $this->caculateAKSN($ak, $sk, $uri, $querystring_arrays);
		//输出计算得到的sn	
		//echo "sn: $sn<br>";
		//请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
		$target = sprintf($url, $ip,$output, $ak, $sn);				
		//输出完整请求的url（仅供参考验证，故不能正常访问服务）
		//echo "url: $target<br>";
		//$target = $url;
		$result_string = file_get_contents($target);
		$result_string = str_replace("\u5e02", "", $result_string);
		
		//echo $result_string;
		$result = json_decode($result_string,TRUE);
		//print_r($result) ;
		$new = array(
				'city' => $result['content']['address_detail']['city'],
				'x'	=> $result['content']['point']['x'],
				'y' => $result['content']['point']['y']				
		);		
		if($new){			
			$msg->ResponseMsg ( 0, '查询客户端成功!', $new, 0, $callback );
		}else{			
			$msg->ResponseMsg ( 1, '查询客户端失败，返回默认值：上海!', "上海", 0, $callback );
		}
		return true;		
	}
	
/** 
* @brief 计算SN签名算法 
* @param string $ak access key 
* @param string $sk secret key 
* @param string $url url值，例如: /geosearch/nearby 不能带hostname和querstring，也不能带？ 
* @param array  $querystring_arrays 参数数组，key=>value形式。在计算签名后不能重新排序，也不能添加或者删除数据元素 
* @param string $method 只能为'POST'或者'GET' 
*/  
public function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET'){  
    if ($method === 'POST'){  
        ksort($querystring_arrays);  
    }  
    $querystring = http_build_query($querystring_arrays);  
    return md5(urlencode($url.'?'.$querystring.$sk));  
} 
}
?>
