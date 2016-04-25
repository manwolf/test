<?php
class user_info extends spModel
{
	public  $pk = "tid"; // 每个留言唯一的标志，可以称为主键
	public  $table = "user_info"; // 数据表的名称

/**
 * 这里我们建立一个成员函数来进行用户登录验证
 *
 */
public function login($telephone, $pwd){
	$conditions = array(
			'telephone' => $telephone,
			'login_pwd' => $pwd, 
	);
	// dump($conditions);
	// 检查用户名/密码，由于$conditions是数组，所以SP会自动过滤SQL攻击字符以保证数据库安全。
	if( $result = $this->find($conditions) ){
		// 成功通过验证，下面开始对用户的权限进行会话设置，最后返回用户ID
		// 用户的角色存储在用户表的acl字段中
		spClass('spAcl')->set($result['acl']); // 通过spAcl类的set方法，将当前会话角色设置成该用户的角色
		$_SESSION["user_info"] = $result; // 在SESSION中记录当前用户的信息
		return true;
	}else{
		// 找不到匹配记录，用户名或密码错误，返回false
		return false;
	}
}
}