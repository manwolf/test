<?php
include_once 'base/crudCtr.php';
/**
 * 功能：精品课程功能
 * 作者： 陈梦帆
 * 日期：2015年9月08日
 */
class a extends crudCtr {
	public function __construct() {
		$this->tablename = 'high_quality_courses';
	}
	// 查询短期课程的基本信息
	function querycourse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$high_quality_name = $newrow ['high_quality_name'];
		$class_hour = $newrow ['class_hour'];
		$create_time = $newrow ['create_time'];
		$high_quality_price = $newrow ['high_quality_price'];
		$city = $newrow ['city'];
		// 为空时查询所有的短期课程信息
		if (! $newrow) {
			$querySql = 'select high_quality_name,class_hour,create_time,high_quality_price,
					 city from high_quality_courses';
			$model = spClass ( $this->tablename );
			$result = @$model->findSql ( $querySql );
// 			$page = $newrow ['page'] ? $newrow ['page'] : 1;
// 			$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
// 			$pager = $model->spPager ()->getPager ();
// 			$total_page = $pager ['total_page'];
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $callback );
		} else {
			$querySql = 'select high_quality_name,class_hour,create_time,high_quality_price,
					 city from high_quality_courses where 1=1';
			if ($high_quality_name != '') {
				// 根据课程名称查询
				$querySql .= ' and  high_quality_name = "' . $high_quality_name . '"';
			}
			if ($class_hour != '') {
				// 根据课程的课时查询
				$querySql .= ' AND  class_hour = ' . $class_hour  ;
			}
			if ($create_time != '') {
				// 根据课程的创建时间查询
				$querySql .= ' AND  create_time LIKE "%' . $create_time . '%"';
			}
			if ($high_quality_price != '') {
				// 根据课程的单价查询
				$querySql .= ' AND  high_quality_price = "' . $high_quality_price . '"';
			}
			if ($city != '') {
				// 根据该课程所在城市查询
				$querySql .= ' AND  city = "' . $city . '"';
			}
			$model = spClass ( $this->tablename );
			$result = @$model->findSql ( $querySql );
// 			$page = $newrow ['page'] ? $newrow ['page'] : 1;
// 			$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
// 			$pager = $model->spPager ()->getPager ();
// 			$total_page = $pager ['total_page'];
			if ($result) {
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $callback );
			} else {
				$msg->ResponseMsg ( 1, '找不到该课程', false, $total_page, $callback );
			}
		}
	}
	// 修改该课程的使用状态，"0"为有效；“1”为无效   默认值为0
	function state() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
	if (! $newrow) {
			$msg->ResponseMsg ( 1, '请选中想要修改使用状态的课程名称！', null, 0, $prefixJS );
		} else {
			$updateSql = 'update high_quality_courses set state = 0 where tid = ' . $tid;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $updateSql );
			$msg->ResponseMsg ( 0, '修改成功！', $result, 0, $prefixJS );
		}
		return true;
	}
}
	
 		
	
