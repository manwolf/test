<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
/**
 * 功能：教师评论的相关信息，包括客户端查询教师评论列表，教研端的添加编辑功能
 * 作者： 孙广兢
 * 日期：2015年9月16日
 */
class TeacherCommentCtr extends crudCtr {

	/**
	 * 获取单个教师的家长评论列表
	 */
	public function getCommentListForSingleTeacher() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		//$token = $capturs ['token']; 暂无身份限制
		$newrow = $capturs ['newrow'];
		$teacher_tid = ( integer )$newrow ['teacher_tid'];		
		$tid = ( integer )$newrow ['tid'];//评论记录的tid，按此搜索单条记录
		if ($teacher_tid <= 0 ) {
			$msg->ResponseMsg ( 1, ' 请输入教师tid ! ', false, 0, $callback );
			exit ();
		}
		$querySql = '
					SELECT a.tid , a.user_tid, b.telephone, b.name, b.image_url , 
						a.comment_content  , a.star_level , a.comment_time    
				FROM teacher_comment a LEFT JOIN robot_info b ON a.user_tid = b.tid
					WHERE a.teacher_tid = "' . $teacher_tid . '" AND a.valid = 0 ';
		if($tid > 0){
			$querySql .= ' AND a.tid = "'.$tid.'"';
		}
		$querySql .= '	ORDER BY a.comment_time DESC	 ';
		$model = spClass ( 'teacher_comment' );
		$result = @$model->findSql ( $querySql );
		if (! $result) {
			$msg->ResponseMsg ( 0, ' 该教师暂无家长评论 ! ', $result, 0, $callback );
			exit ();
		} else {
			$msg->ResponseMsg ( 0, ' 查询教师的家长评论成功 ', $result, 0, $callback );
		}
	}
	
	/**
	 * 添加教师的家长评论
	 */
	public function addTeacherComment(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$oa_user_tid = $newrow['user_tid'];
		// 验证权限	
		$verify = new checkCtr ();
		$result = $verify->acl ();		
		if(!$result){
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $callback );
			exit ();
		}
		$city = $result ['0'] ['city'];		
		$telephone = $newrow ['telephone'];
		$comment_content = $newrow ['comment_content']; 
	// 过滤输入信息
		$teacher_tid = ( integer ) $newrow ['teacher_tid'];		 
		$star_level = ( integer ) $newrow ['star_level']; 		
		$comment_time = date ( 'Y-m-d H:i:s', strtotime ( $newrow ['comment_time'] ) ); // 保证日期是YYYY-mm-dd H:i:s的格式
		                                                 // 判断输入信息的有效性
		if ($teacher_tid <= 0 or $star_level <= 0 or $star_level > 5 
				or $comment_time <= "1970-01-01" or $comment_time > date ( 'Y-m-d H:i:s')   ) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		//判断该机器人评论者是否存在
		$model = spClass ( 'robot_info' ); // 初始化模型类
		$result = $model->findBy('telephone',$telephone);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该评论者不存在！', false, 0, $callback );
			exit ();
		}else{
			$user_tid = $result['tid'];
		}		
		
		//判断该教师是否存在	
		$model = spClass ( 'teacher_info' ); // 初始化模型类
		$result = $model->findBy('tid',$teacher_tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该教师不存在！', false, 0, $callback );
			exit ();
		}else{
			$teacher_city = $result['teacher_city'];
			$post_status = $result['post_status'];
			$teacher_hiredate = $result['teacher_hiredate'];
		}
		if($post_status != 1){
			$msg->ResponseMsg ( 1, '该教师非在职！', false, 0, $callback );
			exit ();
		}
		if($city != "全国"){
			if($teacher_city != $city ){
				$msg->ResponseMsg ( 1, '您无权操作其他城市的教师！', false, 0, $callback );
				exit ();
			}
		}
		if($teacher_hiredate > $comment_time){
			$msg->ResponseMsg ( 1, '评论时间应大于该教师的入职时间：'. $teacher_hiredate, false, 0, $callback );
			exit ();
		}		
		 if( iconv_strlen($comment_content,"UTF-8") > 30){
			$msg->ResponseMsg ( 1, '评论内容过长，请输入30个字符以内的文字！', false, 0, $callback );
			exit ();
		} 
		
		
		//判断是否重复添加
		$condition = array ( 
				'user_tid' => $user_tid,
				'teacher_tid' => $teacher_tid,
				'comment_content' => $comment_content,
				'comment_time' => $comment_time,
				'star_level' => $star_level,
				'valid' => 0,
		);
		$model = spClass ( 'teacher_comment' ); // 初始化模型类
		$result = $model->find( $condition ); // 进行新增操作
		if($result){
			$msg->ResponseMsg ( 1, '请不要对教师重复进行评论！', false, 0, $callback );
			exit ();
		}
					
		$new = array (
				'user_tid' => $user_tid,
				'teacher_tid' => $teacher_tid,
				'comment_content' => $comment_content,
				'comment_time' => $comment_time,
				'star_level' => $star_level,
				'creator_tid' => $oa_user_tid,				
		);
		$model = spClass ( 'teacher_comment' ); // 初始化模型类
		$result = $model->create ( $new ); // 进行新增操作
		if ($result) {
			@$verify->record();//记录该操作的访问者信息
			$msg->ResponseMsg ( 0, ' 评论添加成功！ ', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 评论添加失败！ ', false, 0, $callback );
			exit ();
		}
		
	}
	
	
	/**
	 * 编辑教师的家长评论
	 */
	public function editTeacherComment(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];	
		$oa_user_tid = $newrow['user_tid'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if(!$result){
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $callback );
			exit ();
		}
		$city = $result ['0'] ['city'];		
		$telephone = $newrow ['telephone'];
		$comment_content = $newrow ['comment_content'];
		// 过滤输入信息
		$tid = ( integer ) $newrow ['tid'];
		$teacher_tid = ( integer ) $newrow ['teacher_tid'];
		$star_level = ( integer ) $newrow ['star_level'];
		$comment_time = date ( 'Y-m-d H:i:s', strtotime ( $newrow ['comment_time'] ) ); // 保证日期是YYYY-mm-dd H:i:s的格式
		// 判断输入信息的有效性
	if ($teacher_tid <= 0 or $star_level <= 0 or $star_level > 5  
				or $comment_time <= "1970-01-01" or $comment_time > date ( 'Y-m-d H:i:s')   ) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		//判断该条评论是否存在
		$model = spClass ( 'teacher_comment' ); // 初始化模型类
		$result = $model->findBy('tid',$tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该条评论不存在！', false, 0, $callback );
			exit ();
		}
		
		//判断该机器人评论者是否存在
		$model = spClass ( 'robot_info' ); // 初始化模型类
		$result = $model->findBy('telephone',$telephone);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该评论者不存在！', false, 0, $callback );
			exit ();
		}else{
			$user_tid = $result['tid'];
		}
	
		//判断该教师是否存在
		$model = spClass ( 'teacher_info' ); // 初始化模型类
		$result = $model->findBy('tid',$teacher_tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该教师不存在！', false, 0, $callback );
			exit ();
		}else{
			$teacher_city = $result['teacher_city'];
			$post_status = $result['post_status'];
			$teacher_hiredate = $result['teacher_hiredate'];
		}
		if($post_status != 1){
			$msg->ResponseMsg ( 1, '该教师非在职！', false, 0, $callback );
			exit ();
		}
		if($city != "全国"){
			if($teacher_city != $city ){
				$msg->ResponseMsg ( 1, '您无权操作其他城市的教师！', false, 0, $callback );
				exit ();
			}
		}
		if($teacher_hiredate > $comment_time){
			$msg->ResponseMsg ( 1, '评论时间应大于该教师的入职时间：'. $teacher_hiredate, false, 0, $callback );
			exit ();
		}
		if( iconv_strlen($comment_content,"UTF-8") > 30){
			$msg->ResponseMsg ( 1, '评论内容过长，请输入30个字符以内的文字！', false, 0, $callback );
			exit ();
		} 
		//判断是否重复添加
		/* $condition = array (
				'user_tid' => $user_tid,
				'teacher_tid' => $teacher_tid,
				'comment_content' => $comment_content,
				'comment_time' => $comment_time,
				'star_level' => $star_level,
				'valid' => 0,
		);
		$model = spClass ( 'teacher_comment' ); // 初始化模型类
		$result = $model->find( $condition ); // 进行新增操作
		if($result['tid'] != $tid){
			$msg->ResponseMsg ( 1, '请不要对教师重复进行评论！', false, 0, $callback );
			exit ();
		} */
		
		$condition =array('tid' => $tid);
		$new = array ( 
				'user_tid' => $user_tid,
				'teacher_tid' => $teacher_tid,
				'comment_content' => $comment_content,
				'comment_time' => $comment_time,
				'star_level' => $star_level,
				'creator_tid' => $oa_user_tid,
		);
		$model = spClass ( 'teacher_comment' ); // 初始化模型类
		$result = $model->update ($condition, $new ); // 进行新增操作
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			@$verify->record();//记录该操作的访问者信息
			$msg->ResponseMsg ( 0, ' 评论修改成功！ ', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 评论修改失败！ ', false, 0, $callback );
			exit ();
		}
	
	}
	
	/**
	 * 删除评论
	 */
	public  function deleteTeacherComment(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if(!$result){
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $callback );
			exit ();
		}
		$city = $result ['0'] ['city'];
		$tid = ( integer ) $newrow ['tid'];				
		if ($tid <= 0) {
			$msg->ResponseMsg ( 1, ' 输入的评论tid有误！', false, 0, $callback );
			exit ();
		}
		//判断该教师是否存在
		$findSql= ' SELECT a.teacher_city 
				FROM teacher_info a ,teacher_comment b 
				WHERE a.tid= b.teacher_tid 
				AND b.tid = "'.$tid.'"	 limit 1';
		$model = spClass ( 'teacher_info' ); // 初始化模型类
		$result = $model->findSql($findSql);		
		if (!$result) {
			$msg->ResponseMsg ( 1, '该教师不存在！', false, 0, $callback );
			exit ();
		}else{
			$teacher_city = $result['0']['teacher_city'];
		}
		
		if($city != "全国"){
			if($teacher_city != $city ){
				$msg->ResponseMsg ( 1, '您无权操作其他城市的教师！', false, 0, $callback );
				exit ();
			}
		}		
		$model = spClass ( 'teacher_comment' );
		$runSql .= ' UPDATE teacher_comment SET valid = 1 WHERE  tid = ' . $tid;		
		$result = @$model->runSql( $runSql );
		$affectedRows = @$model->affectedRows ();
		if ($affectedRows) {
			@$verify->record();//记录该操作的访问者信息
			$msg->ResponseMsg ( 0, '删除教师的家长评论成功', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 删除教师的家长评论失败！ ', false, 0, $callback );
			exit ();
		}
		
	}
	
	/**
	 * 获取机器人评论者列表
	 */
	public  function getRobotList(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$city = $newrow['city'];
		$oa_user_tid = $newrow['user_tid'];
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if(!$result){
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $callback );
			exit ();
		}
		$oa_user_city = $result ['0'] ['city'];
		if($oa_user_city != "全国"){			
			if($oa_user_city != $city ){
				$msg->ResponseMsg ( 1, '您无权操作其他城市的教师！', false, 0, $callback );
				exit ();
			}
		}				
		// 查询本城市的
		$querySql = 'SELECT tid,city,name,telephone,image_url
				FROM robot_info';
			if($oa_user_city != "全国"){
				$querySql .= ' WHERE city = "' . $city . '"';
			}				
				$querySql .= ' ORDER BY city,telephone';
		$gb = spClass ( 'robot_info' );
		$result = @$gb->findSql ( $querySql ); // 查找
	  @$verify->record();//记录该操作的访问者信息
		$msg->ResponseMsg ( 0, '查询成功！', $result, 0, $callback );
	}
	
	
	/**
	 * END
	 */
	
}
	
	