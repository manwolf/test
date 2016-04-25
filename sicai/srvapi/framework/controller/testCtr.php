<?php
include_once 'base/crudCtr.php';
class testCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'user_info';
	}
	# 子类对add实现空操作
	public function add(){
		exit();
	}
	# 子类对update实现空操作
	public function update(){
		exit();
	}
	# 子类对query实现空操作
	public function query(){
		exit();
	}
	public function queryTest(){
		$model = spClass ( 'user_info' );
		$result = $model->findAll ( array(tid => '1') );
		print_r($result);
	}
	//测试自用类  无视
	public function queryTryLesson(){ # 查询试课限制状态		
		if(defined('TestVersion')){
			#测试环境
			$Sql = "select user_free_count,user_free_num from user_info where telephone =".$_REQUEST['telephone'];
			$model = spClass ( 'user_info' );
			$result = $model->findSql( $Sql );
			print_r($result);
		}else{
			#正式环境
		
		}
	}
	public function delTryLesson(){  # 消除试课限制
		if(defined('TestVersion')){
			#测试环境
			$Sql = "update user_info set user_free_count = 0, user_free_num = 0 where tid>0 and telephone =".$_REQUEST['telephone'];
			$model = spClass ( 'user_info' );
			$result = $model->runSql( $Sql );
			$this->queryTryLesson();
		}else{
			#正式环境
				
		}
	}
	public function queryChangeLesson(){ # 查询调课限制状态
		if(defined('TestVersion')){
			#测试环境
			$Sql = "select user_free_count, user_free_num, user_change_state, user_nextmonth_state from user_info where telephone =".$_REQUEST['telephone'];
			$model = spClass ( 'user_info' );
			$result = $model->findSql( $Sql );
			print_r($result);
		}else{
			#正式环境
	
		}
	}
	public function delChangeLesson(){  # 消除调课限制
		if(defined('TestVersion')){
			#测试环境
			$Sql = "update user_info set user_free_count = 0, user_free_num = 0, user_change_state = 0, user_nextmonth_state = 0,user_free_count=0 where tid>0 and telephone =".$_REQUEST['telephone'];
			$model = spClass ( 'user_info' );
			$result = $model->runSql( $Sql );
			$this->queryChangeLesson();
		}else{
			#正式环境
	
		}
	}
	public function delInterview(){  # 消除试课限制
		if(defined('TestVersion')){
			#测试环境
			$Sql = "update user_info set user_free_count = 0, user_free_num = 0, user_change_state = 0, user_nextmonth_state = 0,user_free_count=0, user_add_class=0 where tid>0 and telephone =".$_REQUEST['telephone'];
			$model = spClass ( 'user_info' );
			$result = $model->runSql( $Sql );
			$this->queryChangeLesson();
		}else{
			#正式环境
	
		}
	}
	public function queryTeacherGrammar(){ # 查询教案
		if(defined('TestVersion')){
			#测试环境
			$Sql = "select teacher_grammar from class_list where tid =".$_REQUEST['tid'];;
			$model = spClass ( 'class_list' );
			$result = $model->findSql( $Sql );
			print_r($result);
		}else{
			#正式环境
	
		}
	}
	public function delTeacherGrammar(){ # 清除教案
		if(defined('TestVersion')){
			#测试环境
			$Sql = "update class_list set teacher_grammar = null where tid =".$_REQUEST['tid'];
			$model = spClass ( 'class_list' );
			$result = $model->runSql( $Sql );
			$this->queryTeacherGrammar();
		}else{
			#正式环境
				
		}		
	}
	public function delAllTeacherGrammar(){ # 教案全清
		if(defined('TestVersion')){
			#测试环境
			$Sql = "update class_list set teacher_grammar = null where tid > 0";
			$model = spClass ( 'class_list' );
			$result = $model->runSql( $Sql );
			if($result){
				echo 'success';
			}else {
				echo 'fail';
			}
		}else{
			#正式环境
				
		}		
	}
	public function queryTeacherImage(){ # 查询教师头像
		if(defined('TestVersion')){
			#测试环境
			$Sql = "select teacher_image from teacher_info where telephone =".$_REQUEST['tid'];;
			$model = spClass ( 'teacher_info' );
			$result = $model->findSql( $Sql );
			print_r($result);
		}else{
			#正式环境
	
		}
	}
	public function delTeacherImage(){ # 清除教师头像
		if(defined('TestVersion')){
			#测试环境
			$Sql = "update teacher_info set teacher_image = null where telephone =".$_REQUEST['tid'];
			$model = spClass ( 'teacher_info' );
			$result = $model->runSql( $Sql );
			$this->queryTeacherGrammar();
		}else{
			#正式环境
	
		}
	}
}