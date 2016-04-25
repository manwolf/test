<?php 





$u = '{"version":"1.0","order_id":"wz42thgpubfi38irDcx7534E","trande_no":"10708682","pay_amount":"2","pay_result":"10","user_id":"5053029","extends":"fQKCuHlH19","sign":"77c91a7a70af380f1863830852d08670","ss":"pkikR7j8Zb"}';
$arr = json_decode($u,true);
$urlp = '';
foreach ($arr as $k => $v){
	if($v){
		$urlp .= '/'. $k.'/'.$v;
	}
}
//spid
//$url = "http://p.maimob.cn/index.php/Out/index". $urlp;


$url = "http://p.maimob.cn/index.php/Out/CMP" . $urlp;


echo $url;