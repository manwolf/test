<?php
include_once 'base/crudCtr.php';
/**
 * 功能：精品课程功能
 * 作者： 黄东
 * 日期：2015年9月06日
 */
class highQualityCourses extends crudCtr {
	public function __construct() {
		$this->tablename = 'high_quality_courses';
	}
	//获取精品课程列表  按城市获取
	function getHighQualityList(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$city=$newrow['city'];
		$courses_type=$newrow['courses_type'];
		if($city=='' || $courses_type==''){
			$msg->ResponseMsg ( 1, '没有获取到城市或课程类型！', $result, 1, $prefixJS );
			exit;
		}
		$querySql='select * from high_quality_courses where state=0 and city="'.$city.'" and courses_type='.$courses_type;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($result){
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		}else{
			if(0==$courses_type){
				$msg->ResponseMsg ( 1, '您好！本城市暂不支持精品课程！', $result, 1, $prefixJS );
				exit;
			}
			if(1==$courses_type){
				$msg->ResponseMsg ( 1, '您好！本城市暂不支持外教课程！', $result, 1, $prefixJS );
				exit;
			}
			
// 			$msg->ResponseMsg ( 1, '您好！本城市暂不支持精品课程！', $result, 1, $prefixJS );
		}
				
		
	}
	//获取各个精品课程的授课内容  
	function getHighQualityContentInfo(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$tid=$newrow['tid'];
		if(!$newrow){
			$msg->ResponseMsg ( 1, '缺少high_quality_courses_tid！', $result, 1, $prefixJS );
			exit;
		}
		$querySql='select * from high_quality_content where high_quality_courses_tid="'.$tid.'"';
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($result){
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '您好！此精品课程暂无授课内容！', $result, 1, $prefixJS );
		}
		
	}
	//获取各个精品课程允许的上课方式 
	function getHighQualityClassWay(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$tid=$newrow['tid'];
		if(!$newrow){
			$msg->ResponseMsg ( 1, '缺少high_quality_courses_tid！', $result, 1, $prefixJS );
			exit;
		}
		$querySql='select * from high_quality_class_way where high_quality_courses_tid="'.$tid.'"';
		//SELECT * FROM eteacher.high_quality_class_way where   high_quality_courses_tid=1;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($result){
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '查询失败，请检查此课程是否录入上课方式！', $result, 1, $prefixJS );
		}
		
	}
	//获取精品课详情 简介
	function getClassQualityDetails(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$high_quality_courses_tid=$newrow['tid'];
		if(!$newrow){
			$msg->ResponseMsg ( 1, '缺少high_quality_courses_tid！', $result, 1, $prefixJS );
			exit;
		}
		$querySql='select * from class_quality_details  where high_quality_courses_tid='.$high_quality_courses_tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($result){
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '该精品课暂无介绍！', $result, 1, $prefixJS );
		}
	}
	//获取一周上课时间表  只是给客服显示  并没有什么用
	function getWeekTime(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$querySql='select date,state_time,end_time  from  week_time  where tid>0';
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($result){
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '查询失败，改精品课暂无介绍！', $result, 1, $prefixJS );
		}
	}
}