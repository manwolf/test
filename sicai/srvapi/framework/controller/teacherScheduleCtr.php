<?php
include_once 'base/crudCtr.php';
class teacherScheduleCtr extends crudCtr {
	//查询所有教师60天时间状态
	public function __construct()  {
		$this->tablename = 'teacher_schedule';
	}
	
}