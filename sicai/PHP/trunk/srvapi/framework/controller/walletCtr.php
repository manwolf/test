<?php
include_once 'base/crudCtr.php';
/**
 * 功能：钱包相关接口（包括 查询钱包余额、钱包支付）
 * 作者： 陈鸿润
 * 创建日期：2015年8月27日
 * 最新修改：2015年8月31日
 */
class walletCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'wallet_list';
	}
	# 子类对add实现空操作
	public function add(){
		exit();
	}
	# 子类对update实现空操作
	public function update(){
		exit();
	}
	/**
	 * 查询钱包余额
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */ 
	public function query() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];		
		if(!$newrow){
			$msg->ResponseMsg ( 1, "You didn't input user tid", $result, 0, $prefixJS );
			return false;
		}
		# 如果用户没有配置钱包，则给用户配置钱包
		$user_info = spClass ( "user_info" );
		$querySQL = "select * from user_info where tid = {$newrow[user_tid]}";
		$result = $user_info->findSql ( $querySQL );
		// 给用户添加钱包
		if (!$result [0] ['user_wallet_tid']){
			# 在钱包表中插入一条新的信息；并在MySql触发器中，将user_list的钱包外键（user_wallet_tid）设置为新插入的钱包tid
			$Sql = "insert wallet_list set user_tid='".$result[0]['tid']."'";
			$model = spClass ( 'wallet_list' );
			$result = $model->runSql ( $Sql );
		}
		# 根据user_tid查询钱包余额
		$model = spClass ( $this->tablename );
		$Sql = "select w.balance from user_info u,wallet_list w where u.user_wallet_tid=w.tid and u.tid='".$newrow['user_tid']."'";
		$result = $model->findSql ( $Sql );		
		
		if($result){
			$msg->ResponseMsg ( 0, '查询成功。', $result, 0, $prefixJS );
			return true;
		}else{
			$msg->ResponseMsg ( 1, '您还没有配置钱包，请重新登录。', $result, 0, $prefixJS );
			return false;
		}
	} 
	/**
	 * 用钱包支付订单(存储过程实现,并加入事务机制)
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月1日
	 */
	public function payByWallet(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs['token'];
		$newrow = $capturs ['newrow'];		
		# 连接数据库
		$con = mysql_connect('localhost','root','sicai') or die ('Could not connect: ' . mysql_error());
		mysql_select_db('eteacher',$con); # 选择数据库		
		mysql_query('START TRANSACTION'); # 开始事务		
		$isBad = 0; # 配置isBad初始值为0
		# 判断是否使用了邀请码。如果使用了邀请码，则用折后价格支付，并修改订单价格 | 如果未使用邀请码，则按订单价格支付，不修改订单价格
		if($newrow['invitation_code'] &&!('undefined' == $newrow['invitation_code']) ){
			$updateSql = "update pay_list set invitation_code ='".$newrow['invitation_code']."' where order_tid='".$newrow['order_tid']."' and tid > 0";
			$model = spClass ( 'pay_list' );
			$model->runSql ( $updateSql );
			$sql = "call eteacher.walletCtr_payByWallet(\"{$token}\",{$newrow['order_tid']},\"{$newrow['invitation_code']}\",@state_out)";
		}else{
			$sql = "call eteacher.walletCtr_payByWallet(\"{$token}\",{$newrow['order_tid']},\"1\",@state_out)";
		}
		$result = mysql_query($sql);
		# 如果操作失败，则isBad=1
		if(!$result){
			$isBad =1;
		}
		$state_out = mysql_query('select @state_out;');
		# 如果操作失败，则isBad=1
		if(!$state_out){
			$isBad =1;
		}
		$state_out = mysql_fetch_array($state_out);
		# 如果1==isBad，则对进行事务回滚
		if($isBad == 1){
			mysql_query('ROLLBACK ');
		}
		mysql_query('COMMIT'); # 结束事务
		mysql_close($con); # 关闭数据库连接
		# 对数据库返回值，进行判断，并进行相应操作
		switch($state_out['@state_out']){
			case 'success':
				$msg->ResponseMsg ( 0, '支付成功', 'T', 0, $prefixJS );
				break;
			case 'lack_of_balance':
				$msg->ResponseMsg ( 1, '您的钱包余额不足，请充值.', 'N', 0, $prefixJS );
				break;
			case 'token_error':
				$msg->ResponseMsg (1, "Token error.", null, 0, $prefixJS );
				break;
			default:
				$msg->ResponseMsg ( 1, '支付失败，请联系客服.', $result, 0, $prefixJS );
				break;
		}
		# 判断是否使用邀请码,若使用了邀请码，则插入使用记录，并使使用过的邀请码失效
		if($newrow['invitation_code']){			
			if(defined('TestVersion')){
				$url="http://testapi.e-teacher.cn/srvapi/framework/index.php?c=SCInvitationCtr&a=recordInvitationUse&invitation_code=".$newrow['invitation_code']."&order_tid=".$newrow['order_tid']."&callback=".$prefixJS;
				file_get_contents($url);
			}else{
				$url="http://api.e-teacher.cn/srvapi/framework/index.php?c=SCInvitationCtr&a=recordInvitationUse&invitation_code=".$newrow['invitation_code']."&order_tid=".$newrow['order_tid']."&callback=".$prefixJS;
				file_get_contents($url);
			}
		}
	}	
}