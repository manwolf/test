<?php
header("Content-type:text/html; charset=utf-8");
include_once 'base/responseMsg.php';
require_once 'vendor/autoload.php';
include_once 'vendor/jpush.php';
use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class jpushtool
{
	public $app_key; 
	
	public $master_secret;
	
    public $posturl = "https://api.jpush.cn/v3/push";
	
    public function __construct() {
    	if(defined('TestVersion')){
    		#测试环境
    		$this->app_key = 'c4417af7228ffd075d36488a';
	
			$this->master_secret = '287444fc71aa9abeb14f39fc';
    	}else{
    		#正式环境
    		$this->app_key = 'e26265978a361cc40a3f7943';

   			$this->master_secret = '563f808d4fadab9371f72807';
    	}
    }
    
    public function push($msg)
    {
        $msgobj = new responseMsg();
        $br = '<br/>';
        $client = new JPushClient($this->app_key, $this->master_secret);
        $result = $client->push()
            ->setPlatform(M\all)
            ->setAudience(M\all)
            ->setNotification(M\notification($msg))
            ->send();
        if($result->sendno && $result->msg_id){
        	return array(
        			'response' => true,
        			'sendno' => $result->sendno,
        			'msg_id' => $result->msg_id
        	);
        }else{
        	return array(
        			'response' => false,
        	);
        }
    }        
    public function report($msg_ids)
    {
        $responsemsg = new responseMsg();
        $br = '<br/>';
        $client = new JPushClient($this->app_key, $this->master_secret);
        $result = $client->report($msg_ids);
        foreach ($result->received_list as $received) {
            echo '---------' . $br;
            echo 'msg_id : ' . $received->msg_id . $br;
            echo 'android_received : ' . $received->android_received . $br;
            echo 'ios_apns_sent : ' . $received->ios_apns_sent . $br;
        }
    }

    public function pushtoalias($alias, $msg, $title, $url = 'https://api.jpush.cn/v3/push')
    {       	
    	$jpush = new jpush($this->master_secret, $this->app_key);
        $result = $jpush->pushMessage($title, $msg, json_encode(new aliasClient($alias)), '1'); // 1 表示是生产环境
        $msgobj = new responseMsg();
//         $msgobj->ResponseMsg(0, 'success', array(
//         		'sendno' => $result['sendno'],
//         		'msg_id' => $result['msg_id']
//         ), 0, $prefixJS);
        if($result->sendno && $result->msg_id){
        	return array(
        			'response' => true,
        			'sendno' => $result->sendno,
        			'msg_id' => $result->msg_id
        	);
        }else{
        	return array(
        			'response' => false,
        	);
        }
    }
    
    // 获取当前应用的所有标签列表
    public function getTags()
    {
        $msgobj = new responseMsg();
        $client = new JPushClient($this->app_key, $this->master_secret);
        $result = $client->getTags();
        $payload = $result->body;
        $msgobj->ResponseMsg(0, 'success', array(
            'tags' => $payload['tags']
        ), 0, $prefixJS);
    }
    // 查询某个用户是否在tag下
    public function isDeviceInTag($TAG, $REGISTRATION_ID)
    {
        $msgobj = new responseMsg();
        $client = new JPushClient($this->app_key, $this->master_secret);
        $result = $client->isDeviceInTag($REGISTRATION_ID, $TAG);
        $payload = $result->body;
        $msgobj->ResponseMsg(0, 'success', array(
            'result' => $payload['result']
        ), 0, $prefixJS);
    }
    // 获取当前用户的所有属性，包含tags, alias。
    public function getDeviceTagAlias($REGISTRATION_ID)
    {
        $msgobj = new responseMsg();
        $client = new JPushClient($this->app_key, $this->master_secret);
        $result = $client->getDeviceTagAlias($REGISTRATION_ID);
        $payload = $result->body;
        $msgobj->ResponseMsg(0, 'success', array(
            'alias' => $payload['alias'],
            'tags' => $payload['tags']
        ), 0, $prefixJS);
    }
}

class aliasClient
{

    public function aliasClient($aliasName)
    {
        $this->alias[] = $aliasName;
    }

    public $alias = array();
}

/**

* 使用方法
$appkeys='';

$masterSecret='';

$jpush = new jpush($masterSecret,$appkeys);

$title   =  '标题';

$message =  '消息内容';

$message_type = 0;

$receiver = 'all';//接收者

$extras = array();

$jpush->pushMessage($title,$message,$receiver,$message_type,$extras);

$jpush->request_post($url,'{"platform":"all","audience":"all","notification":{"alert":"小陈我们今晚去打老虎额！!"}}',array("Authorization: Basic {$base64_auth_string}","Content-Type: application/json")); 

*/
