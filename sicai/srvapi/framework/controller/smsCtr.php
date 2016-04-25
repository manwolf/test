<?php

include_once 'base/captureparam.php';
include_once 'base/HttpClient.class.php';
header ( "Content-type:text/html; charset=utf-8; Access-Control-Allow-Origin:*" );
//response.setHeader("Access-Control-Allow-Origin", "*");
// 
class smsCtr extends captureparam 
{
	
	private function sendSms($telephone, $content) 
	{
		
		$result = $this->sendSmsMain ( "http://sdk.entinfo.cn:8061/webservice.asmx/mdsmssend", $telephone, $content );
		
		$matchresult = preg_match ( '#成功#', $result, $matches, PREG_OFFSET_CAPTURE );
		
		
	}
	private function sendSmsMain($url, $telephone, $content) 
	{
		$obj = new HttpClient ( '' );
		$result = $obj->quickPost ( $url, array (
				'sn' => 'DXX-WSS-11Y-06110',
				'pwd' => strtoupper(md5('DXX-WSS-11Y-06110'.'557865')),
				'mobile' => $telephone,
				'content' => $content,
				'ext'=>'',
				'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
				'msgfmt'=>'',
				'rrid'=>''
		) );
		return $result;
	}
	
	// 发送手机短信
	public function sendSMSAction() 
	{
		dump($newrow ['telephone'],$newrow ['content'] );
		
		$msg = new responseMsg ();
		
		$capturs = $this->captureParams ();
		$newrow = $capturs ['newrow'];
		$prefixJS = $capturs ['callback'];
		$tokenTxt = $capturs ['token'];
		$newrow = array_change_key_case ( $newrow, CASE_LOWER );
		
		
		if (($newrow ['telephone'] != '') && ($newrow ['content'] != '')) 
		{
			
			$content = iconv ( 'utf-8', 'gb2312', $newrow ['content'] . "[eTeacher]" );
			$this->sendSms ( $newrow ['telephone'], $content );
		}
		$list = array ();
		$list [] = array ();
		$msg->ResponseMsg ( 0, '短信发送完毕', $list, 0, $prefixJS );
	}
	// 计算验证记录主键值
	private function getVerifyCode($id) {
		$id = $id + 200000 - 666 + rand ( 1, 100000 );
		
		return $id;
	}
	
	// 注册时发手机验证码
	public function getRegVerifyidAction() 
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$newrow = $capturs ['newrow'];
		$prefixJS = $capturs ['callback'];
		$tokenTxt = $capturs ['token'];
		$newrow = array_change_key_case ( $newrow, CASE_LOWER );
		
		$rst = spClass ( "user_info" )->findAll ( array (
				'telephone' => $newrow ['telephone'] 
		) );
		
		// 数据库插入一条记录
		$model = spClass ( 'verify_sms' );
		
		// 清除同一个手机之前注册的验证码
		$telephone_record = $model->findAll ( array (
				'telephone' => $newrow ['telephone'] 
		) );
		if (count ( $telephone_record ) > 0) 
		{
			$model->delete ( array (
					'telephone' => $newrow ['telephone'] 
			) );
		}
		
		$sql = "SELECT max(tid) as tid FROM verify_sms";
		$result = $model->findSql ( $sql ); // 执行查找
		                                    // 生成验证码，写入数据库
		$verifynewrow = array ();
		$verifynewrow ['tid'] = $result [0] ['tid'] + 1;
		$verifynewrow ['verify_code'] = $this->getVerifyCode ( $verifynewrow ['tid'] );
		$verifynewrow ['telephone'] = $newrow ['telephone'];
		$model->create ( $verifynewrow );
		
		// 发送验证短信
		//$content = iconv ( 'utf-8', 'gb2312', "亲爱的用户，您好!您的验证码是：" . $verifynewrow ['verify_code'] . "  为了您的账户安全，请勿将验证码转发他人！[eTeacher]" );
		//$content = iconv ( "gb2312", "UTF-8//IGNORE" ,"亲爱的用户，您好!您的验证码是：" . $verifynewrow ['verify_code'] . "  为了您的账户安全，请勿将验证码转发他人！[eTeacher]" );
		//iconv( "gb2312", "UTF-8//IGNORE" ,'您好测试短信[XXX公司]')
		$content = "亲爱的用户，您好!您的验证码是：" . $verifynewrow ['verify_code'] . "  为了您的账户安全，请勿将验证码转发他人！[eTeacher]" ;
		$this->sendSms ( $newrow ['telephone'], $content ); // 给手机发送验证码短信
		                                                        
		// 返回验证码对应id
		$returnnewrow = array ();
		$returnnewrow ['tid'] = sprintf ( "%d", $verifynewrow ['tid'] );
		
		$list = array ();
		$list [] = $returnnewrow;
		$msg->ResponseMsg ( 0, '注册验证码发送完毕', $list, 0, $prefixJS );
	}
	
	// 检查手机收到的验证码是否正确
	public function verifySMSAction() 
	{
		$capturs = $this->captureParams ();
		$newrow = $capturs ['newrow'];
		$prefixJS = $capturs ['callback'];
		$tokenTxt = $capturs ['token'];
		$newrow = array_change_key_case ( $newrow, CASE_LOWER );
		$msg = new responseMsg ();
		
		//为应对苹果应用商店审核，将小陈的手机号在审核期间设置为始终可登录
		$telephone =  $newrow[telephone];
		if ("15000034157" == $telephone)
		{
			$model = spClass ( 'verify_sms' );
			$querySQL = 'select * from verify_sms where telephone = "15000034157"';
			$result = $user_info->findSql ( $querySQL );
		
			//$this->changeToken ( $condition, $result );
			$msg->ResponseMsg ( 0, '验证通过', $result, 0, $prefixJS );
		}
		//
		
		// 验证ID对应的验证码是否正确
		$model = spClass ( 'verify_sms' );
		$findnewrow = array ();
		$findnewrow ['tid'] = sprintf ( "%d", $newrow ['tid'] );
		$findnewrow ['verify_code'] = $newrow ['verify_code'] ;
		//dump($findnewrow);
		
		$result = $model->findAll ( $findnewrow ); // 执行查找
		if (count ( $result ) > 0) 
		{
			$msg->ResponseMsg ( 0, '验证通过', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '验证码不存在', array (), 0, $prefixJS );
		}
	}
	function query() {
		return false;
	}
	function delete() {
		return false;
	}
	function update() {
		return false;
	}
	function add() {
		return false;
	}
}
