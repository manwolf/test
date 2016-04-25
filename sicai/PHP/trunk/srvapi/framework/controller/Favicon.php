<?php
include_once 'base/crudCtr.php';
class Favicon extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_info';
	}
	/**
	 * 功能：查询老师和学生各自的头像
	 * 作者：陈梦帆
	 * 日期： 2015-08-31
	 */
	 //查询老师的头像  只是查一张图片  并没有干嘛
	function queryteacher(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if (! $newrow) {
		$msg->ResponseMsg ( 1, 'false', $result, 0, $prefixJS );
		}else {
			$querySql = 'select teacher_image from teacher_info where tid = ' .$newrow ['tid'];
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			}
			return true;
		} 
	 //查询学生的头像   只是查一张图片  并没有干嘛
	function queryuser(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, 'false', $result, 0, $prefixJS );
		}else {
			$querySql = 'select user_image from user_info where tid = ' .$newrow ['tid'];
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		}
		return true;
	}
}