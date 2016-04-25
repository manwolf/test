<?php
include_once 'base/crudCtr.php';
include_once 'base/captureparam.php';
include_once 'base/HttpClient.class.php';
include_once 'tools/jpushtool.php';
header("Content-type:text/html; charset=utf-8");
/**
 * 功能：极光推送相关接口（服务器将消息推送给所有用户 | 服务器根据tid，将消息推送给指定用户 | 服务器根据tid，将消息推送给多个用户）
 * 作者： 陈鸿润
 * 创建日期：2015年8月27日
 * 修改日期：2015年9月1日
 */
class jPushCtr extends crudCtr{
	public function __construct() {
		$this->tablename = '';
	}
	# 子类对add实现空操作
	public function add(){
		exit();
	}
	# 子类对update实现空操作
	public function update(){
		exit();
	}
	# 子类对query实现空操作
	public function query(){
		exit();
	}
	/**
	 * 服务器将消息推送给所有用户
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	function push(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		# 调用tools/jpushtool.php中，极光推送接口
		$jpushtool = new jpushtool();
		$result = $jpushtool->push($newrow['message']);
		if($result['response']){
			$msg->ResponseMsg ( 0, '推送成功', $result, 0, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '推送不成功', null, 0, $prefixJS );
		}
		
	}
	/**
	 * 服务器根据tid，将消息推送给指定用户
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function pushtoalias(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		# 调用tools/jpushtool.php中，极光推送接口
		$jpushtool = new jpushtool();
		$result = $jpushtool->pushtoalias($newrow['tid'],$newrow['message'],$newrow['title']);
		if($result['response']){
			$msg->ResponseMsg ( 0, '推送成功', $result, 0, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '推送不成功', null, 0, $prefixJS );
		}
	}
	/**
	 * 服务器根据tid，将消息推送给多个用户
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function pushBytid(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		# 去掉前端传来json中的'\'(ascall码等于92)
		for($i = 0, $j = 0; $i+$j < strlen($newrow['tid_array_json']); $i++){
			if(ord($newrow['tid_array_json']{$i+$j}) == 92){
				$j++;
				$change.= $newrow['tid_array_json']{$i+$j};
			}else{
				$change.= $newrow['tid_array_json']{$i+$j};
			}
		}
		# 将$change转化为数组		
		$tid_array = json_decode($change);
		# 调用tools/jpushtool.php中，极光推送接口
		$jpushtool = new jpushtool();
		# 循环调用pushtoalias接口
		foreach($tid_array as $k => $v){
			$response = $jpushtool->pushtoalias($v,$newrow['message'],$newrow['title']);
			if($response['response']){
				$result[] = $response;
			}else{
				$result[] = "第{$k}条信息推送失败";
			}
		}
		$msg->ResponseMsg ( 0, '推送完成', $result, 0, $prefixJS );
	}
}