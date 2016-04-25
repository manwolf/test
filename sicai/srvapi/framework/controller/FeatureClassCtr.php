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
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '填写信息为空！', null, 0, $prefixJS );
			return ;
			} 
			if ($city == $newrow ['city'] || $city==全国 ) { // 教研城市跟老师城市不匹配不能添加
			$querySql='select high_quality_name from high_quality_courses where city="'.$newrow['city'].'"';		
			$model = spClass ( 'high_quality_courses' );
			$result=$model->findSql($querySql);
			if($result[0]['high_quality_name']==$newrow['high_quality_name']){
				$msg->ResponseMsg ( 1, '对不起，此课程名已存在！', 1, 0, $prefixJS );
				
			}
			$model = spClass ( 'high_quality_courses' );
					$result = $model->create ( $newrow );
					$verify = new checkCtr ();
					$results = $verify->record ();
				$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
					}else{
						$msg->ResponseMsg ( 1, '对不起，你没有权限添加非本城市的老师！', 1, 0, $prefixJS );
						return ;
					}
		}

		else {
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );

			}
		}
		//按tid查询特色课程详情
		function queryFeatureClass() {
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
			$verify = new checkCtr ();
			$result = $verify->acl ();
			if ($result) {
				$city=$result[0]['city'];
				//根据tid查询精品课程城市属性
				$querySql='select tid,city from high_quality_courses where tid="'.$newrow['tid'].'"';
				$model = spClass ( 'high_quality_courses' );
				$result = $model->findSql ( $querySql );
				if($result[0]['tid']==null){
					$msg->ResponseMsg ( 1, '没有此课程！', 1, 0, $prefixJS );
					return ;	
				}
				if($city=='全国'||$city==$result[0]['city']){//根据城市匹配是否一致，不一致不能查询
				$querySql='select h.tid,h.high_quality_name,h.class_hour,h.high_quality_price,h.city,h.class_type,h.courses_type,q.high_quality_class_way,c.title_one,c.content_one,c.title_two,c.content_two 
				from high_quality_courses h
				left join class_quality_details  c on h.tid=c.high_quality_courses_tid
				left join high_quality_class_way q on q.high_quality_courses_tid=h.tid where h.tid="'.$newrow['tid'].'"';
				$model = spClass ( 'high_quality_courses' );
				$result = $model->findSql ( $querySql );
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的特色课程', $result, 0, $prefixJS );
						
				}
			}else{
				
					$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
						
				}
		}
		//修改特色课程
		function updateFeatureClass() {
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
			$verify = new checkCtr ();
			$result = $verify->acl ();
			if ($result) {
				$city=$result[0]['city'];
				unset($newrow['user_tid']);
				$tid=$newrow['tid'];
				$newr['high_quality_name']=$newrow['high_quality_name'];
				$newr['class_hour']=$newrow['class_hour'];
				$newr['city']=$newrow['city'];
				$newr['class_type']=$newrow['class_type'];
				$newr['courses_type']=$newrow['courses_type'];
				$new['title_one']=$newrow['title_one'];
				$new['content_one']=$newrow['content_one'];
				$new['title_two']=$newrow['title_two'];
				$new['content_two']=$newrow['content_two'];
				$new['course_name']=$newrow['course_name'];
				if($city=='全国' || $city==$newr['city']){//判断更改的城市和操作人的城市是否一致
				$updateSql = 'update high_quality_courses  set  ';
					foreach ( $newr as $k => $v ) {
						if ($k == 'tid') {
							continue;
						}
						$updateSql = $updateSql . $k . '="' . $v . '",';
					}
					$updateSql = substr ( $updateSql, 0, strlen ( $updateSql ) - 1 );
					$model = spClass ( 'high_quality_courses' );
					$tidsql = ' where tid=' . $tid;
					$updateSql = $updateSql . $tidsql;
					$result = $model->runSql ( $updateSql );
					if($result){//如果精品课程基本信息修改成功则修改上课方式表里的数据
						$updateSql='update high_quality_class_way set high_quality_class_way="'.$newrow['high_quality_class_way'].'"'. ' where high_quality_courses_tid="'.$tid.'"' ;
						$model=spClass('high_quality_class_way');
						$result = $model->runSql ( $updateSql );
					 if($result){//如果修改成功就修改精品课程详情
					 	$updateSql='update class_quality_details set ';
					 	foreach ( $new as $k => $v ) {
					 		if ($k == 'tid') {
					 			continue;
					 		}
					 		$updateSql = $updateSql . $k . '="' . $v . '",';
					 	}
					 	$updateSql = substr ( $updateSql, 0, strlen ( $updateSql ) - 1 );
					 	$model = spClass ( 'high_quality_courses' );
					 	$tidsql = ' where high_quality_courses_tid=' . $tid;
					 	$updateSql = $updateSql . $tidsql;
					 	$result = $model->runSql ( $updateSql );
					 	
					 	 }
					}
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, '修改成功', $result, 0, $prefixJS );
						
			}else{
				$msg->ResponseMsg ( 1, '对不起，您不能将特色课程所在城市修改为非本城市！',false, 0, $prefixJS );
				
			}
			}else{
			
				$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
			}
			}
			
// 		function queryTypeWay() {
// 			$msg = new responseMsg ();
// 			$capturs = $this->captureParams ();
// 			$prefixJS = $capturs ['callback'];
// 			$token = $capturs ['token'];
// 			$newrow = $capturs ['newrow'];
// 			if(!$newrow){
// 				$msg->ResponseMsg ( 1, '课程编号为空', 1, 0, $prefixJS );
				
// 			}else{
// 			//根据特色课程编号查询特色课程id
// 			$querySql='select tid from high_quality_courses where class_num="'.$newrow['class_num'].'"';
// 			$model = spClass ( 'high_quality_courses' );
// 			$result = $model->findSql ( $querySql );
// 			$class_tid=$result[0]['tid'];
// 			//根据特色课程id查询特色课程上课类型
// 			$querySql='select class_type from high_quality_class_type where high_quality_courses_tid='.$class_tid;
// 			$model = spClass ( 'high_quality_class_type' );
// 			$resulta = $model->findSql ( $querySql );
// 			//根据特色课程id查询特色课程上课方式
// 			$querySql='select class_type from high_quality_class_way where high_quality_courses_tid='.$class_tid;
// 			$model = spClass ( 'high_quality_class_way' );
// 			$results = $model->findSql ( $querySql );
// 			$result = array_merge ( $resulta, $results );
// 			$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
// 		}
// 		}
	


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
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '填写信息为空！', null, 0, $prefixJS );
			} else {
				if ($city == $newrow ['city']|| $city==全国 ) { // 教研城市跟老师城市不匹配不能添加
			$model = spClass ( 'class_quality_details' );
		$result = $model->create ( $newrow );
		$verify = new checkCtr ();
		$results = $verify->record ();
		$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
		}else{
			$msg->ResponseMsg ( 1, '对不起，你没有权限添加非本城市的老师！', 1, 0, $prefixJS );
		}
	}
		}else{
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', $result, 0, $prefixJS );
				
		}
	}
	//添加课程上课方式
	function addClassway() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$city = $result [0] ['city'];
			if (! $newrow) {
				$msg->ResponseMsg ( 1, '填写信息为空！', null, 0, $prefixJS );
			} else {
				if ($city == $newrow ['city']|| $city==全国 ) { // 教研城市跟老师城市不匹配不能添加
		
			$model = spClass ( 'high_quality_class_way' );
			$result = $model->create ( $newrow );
			$verify = new checkCtr ();
			$results = $verify->record ();
			$msg->ResponseMsg ( 0, '添加成功', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '对不起，你没有权限添加非本城市的老师！', 1, 0, $prefixJS );
				}
			}
		}else{
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', 1, 0, $prefixJS );
				
		}
	}
	
	// 查询短期课程的基本信息
	function querycourse() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
			$citya=$result[0]['city'];
			$page = $newrow ['page'];
				
		$high_quality_name = $newrow ['high_quality_name'];
		$class_hour = $newrow ['class_hour'];
		$create_time = $newrow ['create_time'];
		$high_quality_price = $newrow ['high_quality_price'];
		$city = $newrow ['city'];
		unset ( $newrow ['page'] );
		
		if($citya == $city || $citya ==全国){
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
					 city,state from high_quality_courses where 1=1';
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
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '找不到该课程', false, 1, $prefixJS );
			}
		}
		}else{
			$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', 1, $total_page, $prefixJS );
		}
		}else{
			$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 1, $prefixJS );
				
		}
		}
	// 修改该课程的使用状态，"0"为有效；“1”为无效 默认值为0
	function state() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$verify = new checkCtr ();
		$result = $verify->acl ();
		if ($result) {
		$citya=$result[0]['city'];
		$tid = $newrow ['tid'];
		$querySql='select city from high_quality_courses where tid="'.$tid.'"';
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		if($citya==$result[0]['city'] || $citya ==全国){
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
					$verify = new checkCtr ();
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, '修改成功！', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '修改失败！', $result, 0, $prefixJS );
				}
				
			}
			
		}
		return true;
	}else{
		$msg->ResponseMsg ( 1, '对不起，您不能操作非本城市的老师', false, 1, $prefixJS );
	}
		}else{
		$msg->ResponseMsg ( 1, '对不起，您没有权限！', $result, 0, $prefixJS );
	}
	}
}


