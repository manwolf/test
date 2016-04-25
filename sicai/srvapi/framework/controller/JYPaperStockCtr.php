<?php
include_once 'base/crudCtr.php';
include_once 'base/checkCtr.php';
/**
 * 功能：OA题库创建、编辑、删除题目
 * 作者： 孙广兢
 * 日期：2015年9月16日
 */
class JYPaperStockCtr extends crudCtr {

	/**
	 * 查询试题
	 */
	public  function askTestQuestion(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];// 暂无身份限制
		$newrow = $capturs ['newrow'];
		$tid = ( integer ) $newrow ['tid']; //题库类型的tid
		$test_question_details_tid = ( integer ) $newrow ['test_question_details_tid']; //试题的tid
		// 验证权限
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if(!$result){
			$msg->ResponseMsg ( 1, "您无权限进入该操作", $result, 0, $callback );
			exit ();
		}
		$city = $result ['0'] ['city'];
		if ($tid <= 0 ) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		//判断该题库类型是否存在
		$model = spClass ( 'test_question_types' ); // 初始化模型类		
		$result = $model->findBy('tid',$tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该题库类型不存在！', false, 0, $callback );
			exit ();
		}else{
			$test_city = $result['test_city'];
		}
		//判断城市属性
		if($city != "全国"){
			if($test_city != $city ){
				$msg->ResponseMsg ( 1, '您无权操作其他城市！', false, 0, $callback );
				exit ();
			}
		}
		//查询试题
		$querySql = '
					SELECT aa.tid,aa.test_number,aa.test_subject,				
				aa.test_right_answers	,
				aa.test_review ,
				bb.test_options_a,bb.test_options_b,bb.test_options_c,bb.test_options_d 
					FROM test_question_details aa 
				, (
				SELECT 
	a.test_question_details_tid, 
	a.answers_options_content AS test_options_a, 
	b.answers_options_content AS test_options_b, 
	c.answers_options_content AS test_options_c, 
	d.answers_options_content AS test_options_d 
FROM 
	(
		SELECT 
			answers_options_content, 
			test_question_details_tid 
		FROM 
			test_alternative_answers 
		where 
			answers_options = "A"
	) a 
	left join (
		SELECT 
			answers_options_content, 
			test_question_details_tid 
		FROM 
			test_alternative_answers 
		where 
			answers_options = "B"
	) b ON a.test_question_details_tid = b.test_question_details_tid 
	left join (
		SELECT 
			answers_options_content, 
			test_question_details_tid 
		FROM 
			test_alternative_answers 
		where 
			answers_options = "C"
	) c ON a.test_question_details_tid = c.test_question_details_tid 
	left join (
		SELECT 
			answers_options_content, 
			test_question_details_tid 
		FROM 
			test_alternative_answers 
		where 
			answers_options = "D"
	) d ON a.test_question_details_tid = d.test_question_details_tid
				)  bb
					WHERE aa.tid = bb.test_question_details_tid
				AND aa.test_question_types_tid = "' . $tid . '"';
		if($test_question_details_tid >0){
			$querySql .= ' AND aa.tid = "'.$test_question_details_tid.'" ';
		}
		$model = spClass ( 'test_question_details' );	
		//分页
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if (!$result) {
			@$verify->record();//记录该操作的访问者信息
			$msg->ResponseMsg ( 0, '该题库为空！', $result, $total_page, $callback );
		}else{
			$msg->ResponseMsg ( 0, '题库查询成功！', $result, $total_page, $callback );
		}
	}
	/**
	 * 创建试题
	 */
	public function addTestQuestion() {
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
		
		$tid = ( integer ) $newrow ['tid']; //试题类型的tid
		$test_subject = $newrow ['subject'];//题目
		$test_review = $newrow ['review'];//点评
		$test_options_a = $newrow ['options_a'];//答案a的内容
		$test_options_b = $newrow ['options_b'];//答案b的内容
		$test_options_c = $newrow ['options_c'];//答案c的内容
		$test_options_d = $newrow ['options_d'];//答案d的内容
		$test_right_answers = $newrow ['right_answer']; //正确答案 ABCD
		if ($tid <= 0  or $test_subject == NULL
				or $test_options_a == NULL or $test_options_b == NULL				
		) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^[A-D]$/', $test_right_answers )  ) {
			$msg->ResponseMsg ( 1, '请输入正确答案编号的正确格式： A、B、C、D ', false, 0, $callback );
			exit ();
		}
		//判断选项是否重复
		if($test_options_a == $test_options_b
			or $test_options_a == $test_options_c
			or $test_options_a == $test_options_d
			or $test_options_b == $test_options_c
			or $test_options_b == $test_options_d
			or ($test_options_c != NULL AND $test_options_c == $test_options_d)
				){
			$msg->ResponseMsg ( 1, '题目中的可选项有重复！', false, 0, $callback );
			exit ();
		}
	
		//判断该题库类型是否存在
		$model = spClass ( 'test_question_types' ); // 初始化模型类
		$result = $model->findBy('tid',$tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该题库类型不存在！', false, 0, $callback );
			exit ();
		}else{
			$test_city = $result['test_city'];
		}
		//判断城市属性
		if($city != "全国"){
			if($test_city != $city ){
				$msg->ResponseMsg ( 1, '您无权操作其他城市！', false, 0, $callback );
				exit ();
			}
		}
		
		//判断该题库类型中该题目是否重复
		$model = spClass ( 'test_question_details' ); // 初始化模型类
		$condition = 'test_question_types_tid = "'.$tid.'"
				AND test_subject = "'. $test_subject.'"';				
		$result = $model->find($condition);
		if ($result) {
			$msg->ResponseMsg ( 1, '该题目已经存在！', false, 0, $callback );
			exit ();
		}
		//查询现在共多少道题
		$querySql = '
					SELECT count(*) AS Total_number,MAX(test_number) AS Max_number
				FROM test_question_details
					WHERE test_question_types_tid = "' . $tid . '"';
		$model = spClass ( 'test_question_details' );
		$result = @$model->findSql ( $querySql );
		if ($result) {
			$Total_number = $result['0']['Total_number'];
			$Max_number = $result['0']['Max_number'];
		}else{
			$Total_number = 0;
			$Max_number = 0;
		}
	
		//添加题目
		$newquestion = array (
				'test_question_types_tid' => $tid, 
				'test_number' => $Max_number +1 ,
				'test_subject' => $test_subject,
				'test_right_answers' => $test_right_answers,
				'test_review' => $test_review,
				'confirm_submit' => 1
		);		
		$model = spClass ( 'test_question_details' ); // 初始化模型类
		$test_question_details_tid = $model->create( $newquestion ); // 进行新增操作
		
		//新增答案		
		$createSql = ' INSERT INTO test_alternative_answers
				(test_question_details_tid,answers_options,answers_options_content)
				VALUES("'.$test_question_details_tid.'","A","'.$test_options_a.'"),
				("'.$test_question_details_tid.'","B","'.$test_options_b.'")';
		if($test_options_c){
			$createSql .= ' , ("'.$test_question_details_tid.'","C","'.$test_options_c.'")';
		}
		if($test_options_d){
			$createSql .= ' , ("'.$test_question_details_tid.'","D","'.$test_options_d.'")';
		}			
		$model = spClass ( 'test_alternative_answers' ); // 初始化模型类
		$result = $model->runSql($createSql ); // 进行新增操作
		
		if ($result) {
			//更新该题库的总数量
			$updateSql = ' UPDATE test_question_types 
					SET test_title_number = 1+ '.$Total_number .' WHERE tid = "'.$tid.'"';
			$model = spClass ( 'test_question_types' ); // 初始化模型类
			$result = @$model->runSql($updateSql);
			$affectedRows = @$model->affectedRows ();
			if ($affectedRows) {
				@$verify->record();//记录该操作的访问者信息
				$msg->ResponseMsg ( 0, ' 新增题目成功！ ', $result, 0, $callback );
			}
		} else {
			$msg->ResponseMsg ( 1, ' 新增题目失败！ ', false, 0, $callback );
			exit ();
		}
	
	}
	
	/**
	 * 修改试题
	 */
	public function editTestQuestion() {
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
		
		$tid = ( integer ) $newrow ['tid']; //试题的tid		
		$test_subject = $newrow ['subject'];//题目
		$test_review = $newrow ['review'];//点评		
		$test_options_a = $newrow ['options_a'];//答案a的内容
		$test_options_b = $newrow ['options_b'];//答案b的内容
		$test_options_c = $newrow ['options_c'];//答案c的内容
		$test_options_d = $newrow ['options_d'];//答案d的内容
		$test_right_answers = $newrow ['right_answer']; //正确答案 A-D
		if ($tid <= 0  or $test_subject == NULL 
				or $test_options_a == NULL or $test_options_b == NULL				
				) {
					$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
					exit ();
		}
		
		if (! preg_match ( '/^[A-D]$/', $test_right_answers )  ) {
			$msg->ResponseMsg ( 1, '请输入正确答案编号的正确格式： A、B、C、D ', false, 0, $callback );
			exit ();
		}
	//判断选项是否重复
		if($test_options_a == $test_options_b
			or $test_options_a == $test_options_c
			or $test_options_a == $test_options_d
			or $test_options_b == $test_options_c
			or $test_options_b == $test_options_d
			or ($test_options_c != NULL AND $test_options_c == $test_options_d)
				){
			$msg->ResponseMsg ( 1, '题目中的可选项有重复！', false, 0, $callback );
			exit ();
		}
		
		//判断该试题是否存在
		$model = spClass ( 'test_question_details' ); // 初始化模型类		
		$result = $model->findBy('tid',$tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该试题不存在！', false, 0, $callback );
			exit ();
		}else{
			$test_question_types = $result['test_question_types_tid'];
		}
							
		//编辑题目
		$newquestion = array (				
				'test_subject' => $test_subject,
				'test_right_answers' => $test_right_answers,				
		);
		$condition=array(
				'tid' => $tid,
		);
		$model = spClass ( 'test_question_details' ); // 初始化模型类
		$result = $model->update( $condition,$newquestion ); // 进行编辑操作
		$newquestionaffectedRows = @$model->affectedRows ();
		//编辑答案		
		$createSql = ' 
				UPDATE test_alternative_answers 
			   SET answers_options_content = 
				 CASE answers_options 
			        WHEN "A" THEN "'.$test_options_a.'" 
			        WHEN "B" THEN "'.$test_options_b.'" 
			        WHEN "C" THEN "'.$test_options_c.'"
			        WHEN "D" THEN "'.$test_options_d.'" 
			    END
			WHERE test_question_details_tid = "'.$tid.'"';		
		$model = spClass ( 'test_alternative_answers' ); // 初始化模型类
		$result = @$model->runSql($createSql ); // 进行编辑答案操作
		$newansweraffectedRows = @$model->affectedRows ();
		//判断编辑是否成功
		if ($newquestionaffectedRows > 0 OR $newansweraffectedRows > 0) {
			@$verify->record();//记录该操作的访问者信息
			$msg->ResponseMsg ( 0, ' 修改试题成功！ ', TRUE, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, ' 修改试题失败！ ', FALSE, 0, $callback );
			exit ();
		}		
		
	}
	
	/**
	 * 删除试题
	 */
	function deleteTestQuestion() {
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
				
		$tid = ( integer )$newrow ['tid'];	//试题的tid	
		if ($tid <= 0) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}	
		//判断该试题是否存在
		$model = spClass ( 'test_question_details' ); // 初始化模型类
		$result = $model->findBy('tid',$tid);
		if (!$result) {
			$msg->ResponseMsg ( 1, '该试题不存在！', false, 0, $callback );
			exit ();
		}else{
			$test_question_types = $result['test_question_types_tid'];
		}
		
		//删除试题
		$condition = ' tid = "' . $tid . '"';		
		$model = spClass ( 'test_question_details' );
		$result = @$model->delete ( $condition );
		$deleteQuestionAffectedRows = @$model->affectedRows ();
		//删除答案
		$condition = ' test_question_details_tid = "' . $tid . '"';
		$model = spClass ( 'test_alternative_answers' );
		$result = @$model->delete ( $condition );
		$deleteAnswerAffectedRows = @$model->affectedRows ();	
		
		//查询现在共多少道题
		$querySql = 'test_question_types_tid = "' . $test_question_types . '"';
		$model = spClass ( 'test_question_details' );
		$Total_number = @$model->findCount ( $querySql );
				
		//更新该题库的总数量
		$updateSql = ' UPDATE test_question_types
					SET test_title_number = '.$Total_number.'
							WHERE tid = "'.$test_question_types.'"';
		$model = spClass ( 'test_question_types' ); // 初始化模型类
		$result = $model->runSql($updateSql);		
		$affectedRows = @$model->affectedRows ();
	
		if ($affectedRows >0 ) {
			@$verify->record();//记录该操作的访问者信息
			$msg->ResponseMsg ( 0, '删除试题成功', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, '删除试题失败！', false, 0, $callback );
			exit ();
		}		
	}
	
	
	
	
	/**
	 * END
	 */
}
?>