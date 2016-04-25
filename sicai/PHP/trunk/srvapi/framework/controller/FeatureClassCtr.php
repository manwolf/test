<?php
include_once 'base/checkCtr.php';
include_once 'base/crudCtr.php';
class FeatureClassCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'high_quality_courses';
	}
	/**
	 * 功能：特色（短期）课程功能
	 * 作者： 陈梦帆 、李坡
	 * 日期：2015年9月09日
	 */
	function addFeatureClass() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			$tid = $newrow ['tid'];
			// $class_type=$newrow['class_type'];
			$class_num = $newrow ['class_num'];
			if (defined ( 'TestVersion' )) // 如果为测试环境$url地址如下
{
				
				$url = "http://testapi.e-teacher.cn/testfile/Teacher_image/$class_num.png";
			} else {
				$url = "http://image.e-teacher.cn/teacher/$class_num.png";
			}
			$newrow ['high_quality_image'] = $url;
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '填写信息为空！', null, 0, $prefixJS );
			} else {
				if ($city != $newrow ['city']) { // 教研城市跟老师城市不匹配不能添加
					$msg->ResponseMsg ( 1, '对不起，你没有权限添加非本城市的老师！', 1, 0, $prefixJS );
					return;
				}
				if ($newrow ['class_num'] == null) {
					$msg->ResponseMsg ( 1, '课程编号不能为空！', 1, 0, $prefixJS );
					return;
				}
				$conditions = array (
						'class_num' => $newrow ['class_num'] 
				); // 判断是否有一样的手机号一样则不能重复添加
				$model = spClass ( 'high_quality_courses' );
				$conditions = array (
						'class_num' => $newrow ['class_num'] 
				); // 判断是否有一样的手机号一样则不能重复添加
				$model = spClass ( 'high_quality_courses' );
				$result = $model->find ( $conditions );
				if ($result ['class_num'] != null) {
					$msg->ResponseMsg ( 1, '此课程编号已存在，请勿重复添加！', 1, 0, $prefixJS );
				} else {
					$model = spClass ( 'high_quality_courses' );
					$result = $model->create ( $newrow );
				}
				$msg->ResponseMsg ( 0, '添加成功', $tid, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '身份验证失败', 1, 0, $prefixJS );
		}
	}
	function queryTypeWay() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 根据特色课程编号查询特色课程id
		$querySql = 'select tid from high_quality_courses where class_num="' . $class_num . '"';
		$model = spClass ( 'high_quality_courses' );
		$result = $model->findSql ( $querySql );
		$class_tid = $result [0] ['tid'];
		// 根据特色课程id查询特色课程上课类型
		$querySql = 'select class_type from high_quality_class_type where high_quality_courses_tid=' . $class_tid;
		$model = spClass ( 'high_quality_class_type' );
		$resulta = $model->findSql ( $querySql );
		// 根据特色课程id查询特色课程上课方式
		$querySql = 'select class_type from high_quality_class_way where high_quality_courses_tid=' . $class_tid;
		$model = spClass ( 'high_quality_class_way' );
		$results = $model->findSql ( $querySql );
		$result = array_merge ( $resulta, $results );
		$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
	}
	function addClassDetails() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$querySql = 'select tid from high_quality_courses where class_num="' . $class_num . '"';
		$model = spClass ( 'high_quality_courses' );
		$result = $model->find ( $querySql );
		$newrow ['high_quality_courses_tid'] = $result [0] ['tid'];
		$model = spClass ( 'class_quality_details' );
		$result = $model->create ( $newrow );
		$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
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
		$token = $newrow ['token'];
		$oa_user_info_tid = $newrow ['oa_user_info_tid'];
		// 为空时查询所有的短期课程信息
		if (! $newrow) {
			$querySql = 'select tid,high_quality_name,class_hour,create_time,high_quality_price,
					 city,state from high_quality_courses';
			$model = spClass ( $this->tablename );
			$result = @$model->findSql ( $querySql );
			// $page = $newrow ['page'] ? $newrow ['page'] : 1;
			// $result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
			// $pager = $model->spPager ()->getPager ();
			// $total_page = $pager ['total_page'];
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
		} else {
			$querySql = 'select tid,high_quality_name,class_hour,create_time,high_quality_price,
					 city from high_quality_courses where 1=1';
			if ($high_quality_name != '') {
				// 根据课程名称查询
				$querySql .= ' and  high_quality_name = "' . $high_quality_name . '"';
			}
			if ($class_hour != '') {
				// 根据课程的课时查询
				$querySql .= ' AND  class_hour = ' . $class_hour;
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
			// $result = @$model->findSql ( $querySql );
			$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
			$pager = $model->spPager ()->getPager ();
			$total_page = $pager ['total_page'];
			if ($result) {
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '找不到该课程', false, $total_page, $prefixJS );
			}
		}
	}
	// 修改该课程的使用状态，"0"为有效；“1”为无效 默认值为0
	function state() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$token = $newrow ['token'];
		$oa_user_info_tid = $newrow ['oa_user_info_tid'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, '请选中想要修改使用状态的课程名称！', null, 0, $prefixJS );
		} else {
			$querySql='select state from high_quality_courses where tid = '.$tid;
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			if(0 == $result[0]['state']){
				$updateSql = 'update high_quality_courses set state = 1 where tid = ' . $tid;
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				if($result){
					$msg->ResponseMsg ( 0, '修改成功！', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '修改失败！', $result, 0, $prefixJS );
				}
				
			}
			elseif(1 == $result[0]['state']){
				$updateSql = 'update high_quality_courses set state = 0 where tid = ' . $tid;
				$model = spClass ( $this->tablename );
				$result = $model->runSql ( $updateSql );
				if($result){
					$msg->ResponseMsg ( 0, '修改成功！', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '修改失败！', $result, 0, $prefixJS );
				}
				
			}
			
		}
		return true;
	}
}
