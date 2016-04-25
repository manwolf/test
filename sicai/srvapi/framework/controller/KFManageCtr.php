<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
include_once 'tools/tokenGen.php';
/**
 * 功能：客服主管的操作管理，包括登陆、添加客服专员、删除客服专员、查看客服专员信息
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class KFManageCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'oa_user_info';
	}
	/**
	 * 查询客服专员基本信息
	 */
	public function queryKfUser() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixKF = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if (! $result) {
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $callback );
			exit ();
		}
		$city = $result ['0'] ['city'];
		if($city=='全国'){
			$msg->ResponseMsg ( 1, "您是全国权限，请选择要操作的城市", false, 0, $callback );
				return ;
		}
		if($city!='全国'){
			$querySql = 'select tid, user_name,telephone,city from oa_user_info where city="'.$city.'"';
		}
		if($city=='全国' && $newrow['city']){
			$querySql = 'select tid,user_name,telephone,city from oa_user_info where city="'.$newrow['city'].'"';
		}
		$model = spClass ( 'oa_user_info' );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		$results = $verify->record ();
			$msg->ResponseMsg ( 0, '成功', $result, $total_page, $prefixKF );
		} 
	
		
/**
 * END
 */
}
		