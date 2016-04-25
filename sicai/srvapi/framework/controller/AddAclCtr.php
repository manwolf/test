<?php
include_once 'base/checkCtr.php';
include_once 'base/crudCtr.php';
class AddAclCtr extends crudCtr {
	/**
	 * 功能：给角色分配权限，添加权限，添加角色，查询角色等操作
	 * 作者： 李坡
	 * 日期：2015年9月1日
	 */
	//添加时候没有分配权限
// 	function assignAcla() {
// 		$msg = new responseMsg ();
// 		$capturs = $this->captureParams ();
// 		$prefixJS = $capturs ['callback'];
// 		$token = $capturs ['token'];
// 		$newrow = $capturs ['newrow'];
// 		$verify = new checkCtr ();
// 		$result = $verify->acl ();
// 		if ($result) {
// 			//字符串替换
// 		$newrow['abc'] = str_replace( "'", '', $newrow['abc'] );
//   		$newrow['abc'] = str_replace( "\\", "", $newrow['abc'] );
// 		$a= json_decode($newrow['abc']);
//   		foreach($a as $k=>$v){//将对象转换为数组
//   			$a[$k] = array("roles_info_tid" => $a[$k]->roles_info_tid,
//   						   "acl_info_tid"=>$a[$k]->acl_info_tid
//   			);
//   		}
// 			$newrow = $a;
// 			$model = spClass ( 'roles_acl_info' );
// 			$result=$model->createAll($newrow);
// 			$results = $verify->record ();
// 			$msg->ResponseMsg ( 0, '添加成功！', true, 0, $prefixJS );
// 			}else{
// 				$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
// 			}
// 	}
	// 添加的时候分配权限
	function assignAcl() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		if (! $newrow) {
			$msg->ResponseMsg ( 1, '填写信息为空！', 1, 0, $prefixJS );
			return;
		}
		//字符串替换
		$newrow['abc'] = str_replace( "'", '', $newrow['abc'] );
		$newrow['abc'] = str_replace( "\\", "", $newrow['abc'] );
		$a= json_decode($newrow['abc']);
		foreach($a as $k=>$v){//将对象转换为数组
			$a[$k] = array("roles_info_tid" => $a[$k]->roles_info_tid);
		}
		$newrow = array (// 主表的数据
				'acl_name' => $newrow ['acl_name'],
				'controller' => $newrow ['controller'],
				'action' => $newrow ['action'],
				'acl_type'=>$newrow['acl_type'],
				'allot'=>'1',
				//副表数据
				'roles_acl_info' =>$a
		);
	
		//如果没有传数组就只添加主表数据
		if($newrow['roles_acl_info']==null){
			$new = array (
			'name' => $newrow ['name'],
			'controller' => $newrow ['controller'],
			'acl_type'=>$newrow['acl_type'],
			'action' => $newrow ['action']);
			$model = spClass ( 'acl_info' );
			$result=$model->create($new);
		}else{
		$model = spClass ( 'acl_info' );
		$model->spLinker ()->create ( $newrow );
		$results = $verify->record ();
		}
		$msg->ResponseMsg ( 0, '添加成功！', true, 0, $prefixJS );
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
	}
	}
	// 删除权限
	function delController() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		$newrow = array(
				'tid' => $newrow['tid'] // 增加到主表的主键
		);
		$model = spClass ( 'acl_info' );
		$result = $model->spLinker ()->delete( $newrow );
		$results = $verify->record ();
		$msg->ResponseMsg ( 0, '添加成功！', $result, 0, $prefixJS );
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
	}
	}
	//修改控制器或权限
	function updateController() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		$querySql='select tid from acl_info where controller="'.$newrow['controller'].'"' .'and action="'.$newrow['action'].'"';
		$model = spClass ( 'acl_info' );
		$result = $model->findSql ( $querySql );
		if($result[0]['tid']>0){
			$msg->ResponseMsg ( 1, '您修改的权限已存在！', false, 0, $prefixJS );
			return ;	
		}
		//修改acl_info表里的数据
		$update='update acl_info set acl_name="'.$newrow['acl_name'].'"'.' and controller="'.$newrow['controller'].'"'.' and action="'.$newrow['action'].'"'.' and acl_type="'.$newrow['acl_type'].'"';
		$model = spClass ( 'acl_info' );
		$result = $model->runSql ( $updateSql );
		if(1==$newrow['allot']){//allot为1时是分配过角色 
		//删除roles_acl_info表里相关数据
		$delSql='delete from roles_acl_info where tid>0 and acl_info_tid="'.$newrow['tid'].'"';
		$model=spClass('roles_acl_info');
		$result=$model->runSql($delSql);
		}
		//将数组里的值插入到roles_acl_info表里
		$newrow['abc'] = str_replace( "'", '', $newrow['abc'] );
		$newrow['abc'] = str_replace( "\\", "", $newrow['abc'] );
		$a= json_decode($newrow['abc']);
		foreach($a as $k=>$v){//将对象转换为数组
			$a[$k] = array("roles_info_tid" => $a[$k]->roles_info_tid,
					"acl_info_tid"=>$a[$k]->acl_info_tid
			);
		}
		$newrow = $a;
		$model = spClass ( 'roles_acl_info' );
		$result=$model->createAll($newrow);
		$results = $verify->record ();
		$msg->ResponseMsg ( 0, '添加成功！', $result, 0, $prefixJS );
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
		
	}
	}
	// 查询控制器/动作
	function queryAllController() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		$querySql = 'select tid,controller,action,acl_type,allot from acl_info';
		$model = spClass ( 'acl_info' );
		$result = $model->findSql ( $querySql );
		$results = $verify->record ();
		$msg->ResponseMsg ( 0, '添加成功！', $result, 0, $prefixJS );
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
		
	}
	}
	// 查询控制器/动作所属角色
// 	function querySubordinateRole() {
// 		$msg = new responseMsg ();
// 		$capturs = $this->captureParams ();
// 		$prefixJS = $capturs ['callback'];
// 		$token = $capturs ['token'];
// 		$newrow = $capturs ['newrow'];
// 		$verify = new checkCtr ();
// 		$result = $verify->acl ();
// 		if ($result) {
// 			if($newrow['tid']!=null){
// 			$querySql='select r.roles_name from roles_info r 
// 					left join  roles_acl_info b on r.tid=b.roles_info_tid 
// 					left join acl_info a on b.acl_info_tid=a.tid 
// 					where a.tid="'.$newrow['tid'].'"';
// 			$model = spClass ( 'acl_info' );
// 			$result = $model->findSql ( $querySql );
// 		}else{
// 			$msg->ResponseMsg ( 1, '查询的tid为空！', false, 0, $prefixJS );
// 		}
// 		}else{
// 			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
// 		}
// 	}
	// 按tid查询权限内容
	function queryController() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			//查询此tid对应的角色名
		$querySql = 'select r.roles_name from roles_info r 
 					left join roles_acl_info b on r.tid=b.roles_info_tid 
 					left join acl_info a on b.acl_info_tid=a.tid 
 					where a.tid="'.$newrow ['tid'] . '"';
		$model = spClass ( 'acl_info' );
		$result1 = $model->findSql ( $querySql );
		//查询tid对应的基本信息
		$querySql='select tid,controller,action,acl_type,allot,acl_name
					from acl_info where tid="'.$newrow['tid'].'"';
		$model = spClass ( 'acl_info' );
		$result2 = $model->findSql ( $querySql );
		//将两个结果合成一个数组
		$result = array_merge ( $result1, $result2 );
		$results = $verify->record ();
		$msg->ResponseMsg ( 0, '添加成功！', $result, 0, $prefixJS );
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
	}
	}
	//删除角色
// 	function delSubordinateRole() {
// 		$msg = new responseMsg ();
// 		$capturs = $this->captureParams ();
// 		$prefixJS = $capturs ['callback'];
// 		$token = $capturs ['token'];
// 		$newrow = $capturs ['newrow'];
// 		$verify = new checkCtr ();
// 		$result = $verify->acl ();
// 		if ($result) {
// 			$querySql='select tid from roles_acl_info where roles_info_tid="'.$newrow['roles_info_tid'].'"'.' and acl_info_tid="'.$newrow['acl_info_tid'].'"';
// 			$model = spClass ( 'roles_acl_info' );
// 			$result = $model->findSql ( $querySql );
// 			if($result[0]['tid']==null){
// 				$msg->ResponseMsg ( 1, '没有此记录！', false, 0, $prefixJS );
// 				return ;
// 			}
// 		$delSql='delete from user_roles_info where tid>0 and roles_info_tid="'.$newrow['roles_tid'].'"';
// 		$model = spClass ( 'acl_info' );
// 		$result = $model->findSql ( $querySql );
// 		}else{
// 			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
				
// 		}
// 	}
		
	//添加角色
	function addRoles() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			unset($newrow['user_tid']);
			if(!$newrow){
				$msg->ResponseMsg ( 1, '填写的信息为空！', false, 0, $prefixJS );
			}else{//判断添加的角色是否存在
		$querySql='select tid from roles_info where roles_name="'.$newrow['roles_name'].'"';	
		$model=spClass('roles_info');
		$result=$model->findSql($querySql);
		if($result[0]['tid']>0){
			$msg->ResponseMsg ( 1, '此角色已经存在！', false, 0, $prefixJS );
		}else{
			$addSql='insert roles_info set roles_name="'.$newrow['roles_name'].'"';
			$model=spClass('roles_info');
			$result=$model->runSql($addSql);
			}
		$msg->ResponseMsg ( 0, '添加成功！', false, 0, $prefixJS );
			}
		}else{
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
				
		}
	}
	// 查询角色
	function queryRoles() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		$querySql = 'select tid,roles_name from roles_info';
		$model = spClass ( 'roles_info' );
		$result = $model->findSql ( $querySql );
		$results = $verify->record ();
		$msg->ResponseMsg ( 0, '查询成功！', $result, 0, $prefixJS );
	}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
	}
	}
	}