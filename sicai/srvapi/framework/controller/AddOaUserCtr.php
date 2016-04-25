<?php
include_once 'base/checkCtr.php';
include_once 'tools/tokenGen.php';

class AddOaUserCtr extends crudCtr {
	
	/*
	 * 添加教研员，客服，市场等OA系统用户
	 * 
	 */
	//添加教研员
	function addSC() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city=$result[0]['city'];
			$newrow ['token'] = $this->produceToken (); // 自动生成token
					
		if($newrow){
			$newa['num']=$newrow['num'];
			$newa['user_name']=$newrow['user_name'];
			$newa['telephone']=$newrow['telephone'];
			$newa['token']=$newrow['token'];
			$newa['city']=$newrow['city'];
			if($city==$newa['city'] || $city==全国){
			$querySql='select tid from oa_user_info where telephone="'.$newa['telephone'].'"';
			$model = spClass ( 'oa_user_info' );
			$result = $model->findSql( $querySql);
			if($result[0]['tid']!=null){
				$msg->ResponseMsg ( 1, '此电话号码已存在，不能重复添加', false, 0, $prefixJS );
				return ;
			}
			$model = spClass ( 'oa_user_info' );
			$result = $model->create ( $newa);
			$user_tid=$result;
			if($user_tid>0){//如果表oa_user_info 添加成功则执行下面代码
			//根据专员职位查询专员的tid
			$querySql='select tid from roles_info where roles_name="'.$newrow['roles_name'].'"';
			$model = spClass ( 'roles_info' );
 			$result = $model->findSql( $querySql);
			$roles_tid=$result[0]['tid'];
 			//往user_roles_info用户角色关系表里插入用户tid和角色id
			$newb['oa_user_info_tid']=$user_tid;
			$newb['roles_info_tid']=$roles_tid;
			$model = spClass ( 'user_roles_info' );
			$result = $model->create ( $newb);
			$results= $verify->record ();
			$msg->ResponseMsg ( 0, '添加成功', true, 0, $prefixJS );
			}
		  return ;
		}
	  else{
	  	$msg->ResponseMsg ( 1, '您不能添加非本城市的市场专员',1, 0, $prefixJS );
	 }
		}else{
	  
	  	$msg->ResponseMsg ( 1, '您填写的信息为空',1, 0, $prefixJS );
	   }
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限',1, 0, $prefixJS );
	}
	}
	
	//添加客服专员
function addKF() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city=$result[0]['city'];
			$newrow ['token'] = $this->produceToken (); // 自动生成token
					
		if($newrow){
// 			$newa['num']=$newrow['num'];
			$newa['user_name']=$newrow['user_name'];
			$newa['telephone']=$newrow['telephone'];
			$newa['token']=$newrow['token'];
			$newa['city']=$newrow['city'];
			if($city==$newa['city'] || $city==全国){
			$querySql='select tid from oa_user_info where telephone="'.$newa['telephone'].'"';
			$model = spClass ( 'oa_user_info' );
			$result = $model->findSql( $querySql);
			if($result[0]['tid']!=null){
				$msg->ResponseMsg ( 1, '此电话号码已存在，不能重复添加', false, 0, $prefixJS );
				return ;
			}
			$model = spClass ( 'oa_user_info' );
			$result = $model->create ( $newa);
			$user_tid=$result;
			if($user_tid>0){//如果表oa_user_info 添加成功则执行下面代码
			//往user_roles_info用户角色关系表里插入用户tid和角色id
			$newb['oa_user_info_tid']=$user_tid;
			$newb['roles_info_tid']=$roles_tid;
			$model = spClass ( 'user_roles_info' );
			$result = $model->create ( $newb);
			$results = $verify->record ();
			$msg->ResponseMsg ( 0, '添加成功', true, 0, $prefixJS );
			}
		  return ;
		}
	  else{
	  	$msg->ResponseMsg ( 1, '您不能添加非本城市的客服专员',1, 0, $prefixJS );
	 }
		}else{
	  
	  	$msg->ResponseMsg ( 1, '您填写的信息为空',1, 0, $prefixJS );
	   }
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限',1, 0, $prefixJS );
	}
	}
	
	
	/**
	 * 产生token
	 *
	 * @param number $len
	 * @return string
	 */
	private function produceToken($len = 8) {
		$tokenTxt = $this->randomkeys ( $len );
		// 这里的$tokenTxt不是token，是token的源字符串，系统默认自动生成一个8位的随机数做为token的源。
		$token = tokenGen::encrypt ( $tokenTxt );
		// start $token里会出现特殊符号，下面将特殊符号替换为数字或字母
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i; $i < strlen ( $token ); $i ++) {
			$ord_token = ord ( $token {$i} );
			if (! (($ord_token >= 48 && $ord_token <= 57) || ($ord_token >= 65 && $ord_token <= 90) || ($ord_token >= 97 && $ord_token <= 122) || ($ord_token == 61))) {
				$token {$i} = $pattern {mt_rand ( 0, 35 )};
			}
		}
		$model = spClass ( "oa_user_info" );
		$sum = $model->findCount ( array (
				'token' => $token
		) );
		if ($sum > 0) {
			return strtr ( $this->produceToken ( $len ), "+", "A" );
		} else {
			return $token;
		}
	}
	
	/**
	 * 产生定长的随机数
	 *
	 * @param string $length
	 */
	private function randomkeys($length) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
	
	/**
	 * END
	 */
	}
	
	
	
