<?php
// define('TestVersion', 'This is a test version.');

# 定义全局常量（mysql连接地址、账号、密码）
define("mysql_address","localhost");
define("mysql_account","root");
define("mysql_password","sicai");

header("Content-type:application/json; charset=utf-8;Access-Control-Allow-Origin:*");

header("Access-Control-Allow-Methods", "POST,OPTIONS,GET");

date_default_timezone_set("PRC");
//include_once 'authentication.php';
include_once 'controller/base/responseMsg.php';
define("APP_PATH", dirname(__FILE__));
define("SP_PATH", dirname(__FILE__) . '/SpeedPHP'); 

define("DOMAIN","http://localhost/ews/srvapi/framework/");//设置绝对路径前缀常量
define("MD5STR", "Si@e#Cai%Teacher^");//设置md5加密后缀字符串
/*if(!ipauth())
{
    $msg = new responseMsg();     
    $msg->ResponseMsg(1, ' your host is not in whitelist', array(), 0, '');
    return ;
}*/  
$spConfig = array(
    "db" => array
    ( // 数据库设置
        'host' => mysql_address, // 数据库地址，一般都可以是localhost
        'login' => mysql_account, // 数据库用户名
        'password' => mysql_password, // 数据库密码
        'database' => 'eteacher'
    ),    
    'view' => array
    (
        'auto_display'=>true,
        'enabled' => TRUE, // 开启视图
        'config' =>array(
            'template_dir' => APP_PATH.'/tpl', // 模板目录
            'compile_dir' => APP_PATH.'/tmp', // 编译目录
            'cache_dir' => APP_PATH.'/tmp', // 缓存目录
            'left_delimiter' => '<{',  // smarty左限定符
            'right_delimiter' => '}>', // smarty右限定符,
        	),  
//     	'launch' => array( 
// 		 'router_prefilter' => array( 
// // 			array('spAcl','mincheck') // 开启有限的权限控制
// 			array('spAcl','maxcheck') // 开启强制的权限控制
// 		 )
//      ),
    		
        'engine_name' => 'Smarty',
        'engine_path' => SP_PATH.'/Drivers/Smarty/Smarty.class.php',
    ),
	'include_path'=>array(	APP_PATH.'/plug',),
); // 数据库的库名称
		
require (SP_PATH . "/SpeedPHP.php");
spRun();


