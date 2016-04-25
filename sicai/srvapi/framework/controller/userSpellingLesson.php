<?php
include_once 'base/crudCtr.php';
/**
 * 功能：拼课各种功能综合
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class userSpellingLesson extends crudCtr {
	public function __construct() {
		$this->tablename = 'user_spelling_lesson';
	}
	// 统计家长剩余未上课的所有课时数
	function userClassSurplusNum() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, '没有发现该学生id', $result, 0, $prefixJS );
			exit ();
		}
		// 统计家长剩余未上课的所有课时数 order_type=1非拼客费单  order_state=3 已排课单
		$querySql = 'select count(c.user_confirm)*2  as userClassSurplusNum
					   from user_info u,order_list o,class_list c
					   where  u.tid=o.user_tid and o.tid=c.order_tid and 
				       o.order_type=1 and o.order_state=3 c.user_confirm=0 and u.tid=' . $user_tid;
		
		// echo $querySql;
		// exit;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		// $result = $model->findSql ($querySql);
		if ($result [0] ['userClassSurplusNum'] > 0) {
			$msg->ResponseMsg ( 0, '您还有正在进行的一对一课程，是否需要客服为您调整？', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, '您还没有正在进行的一对一课程！', $result, 0, $prefixJS );
		}
		return true;
	}
	// 拼课信息 发起拼课页面
	function spellingLessonInformation() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$quality=$newrow['class_quality'];; //拼课类型  0普通  1精品
		if($quality ==''){
			$msg->ResponseMsg ( 1, '没有获取到拼课类型！', $result, 0, $prefixJS );
			exit;
		}
		// $user_tid=$result[0]['user_tid'];
		// 判断该学生是否已经 参与拼课 或者 发起拼课
		$querySql = 'select spelling_class_state from user_info where tid=' . $newrow ['user_tid'];
		$model = spClass ( $this->tablename );
		$resulqu = $model->findSql ( $querySql );
		// echo $resulqu[0]['spelling_class_state'];
		// exit;
		
		if (0 == $resulqu [0] ['spelling_class_state']) { // 0表示该学生目前没有参与或发起拼课
		                                                  
			// 判断有无经纬度上传
			if (! $newrow ['longitude'] || ! $newrow ['latitude']) {
				unset ( $newrow ['longitude'] );
				unset ( $newrow ['latitude'] );
			}
			// print_r($newrow);
			// exit;
			
			$model = spClass ( $this->tablename );
			// 新增
			$result = $model->create ( $newrow );
			if ($result <= 0) {
				return;
			}
			// 根据新增的tid 查询该条信息所有内容
			$result = $model->findAll ( array (
					$model->pk => $result 
			) );
			
			if (count ( $result ) > 0) {
				// 发起人发起拼课的同时为他增加一条未支付的订单
				$teacher_tid = $result [0] ['teacher_tid'];
				$user_tid = $result [0] ['user_tid'];
				$user_message = $result [0] ['user_message']; // 留言
				$user_phone = $result [0] ['user_phone'];
				$user_venue = $result [0] ['user_venue']; // 地址
				$tid = $result [0] ['tid'];
				$teaching_grade = $result [0] ['teaching_grade']; // 授课年级 类型
				$teache_class = $result [0] ['teache_class']; // 年级
				$spelling_type = $result [0] ['spelling_type']; // 类型
				$user_name = $result [0] ['user_name'];
				$class_quality=$result [0] ['class_quality']; //拼课类型  0普通  1精品
				$high_quality_courses_tid=$result [0] ['high_quality_courses_tid']; //精品课ID
				if($class_quality ==''){
					$msg->ResponseMsg ( 1, '没有获取到拼课类型！class_quality', $result, 0, $prefixJS );
					exit;
				}
				//如果发起普通拼课
				if(0==$class_quality){
					// 根据拼课信息 给发起人生成一张未支付订单    class_types默认为0 标记为普通
					$addOrder = 'insert into order_list (teacher_tid,user_tid,order_type,
				     order_remark,order_phone,order_address,
				     user_spelling_lesson_tid,order_name,spelling_lesson_type)
				     select ' . $teacher_tid . ',' . $user_tid . ',' . $spelling_type . ',"' . $user_message . '","' . $user_phone . '","' . $user_venue . '",' . $tid . ',"' . $user_name . '",0' . ' from  user_spelling_lesson a where a.tid=' . $tid;
					$model = spClass ( $this->tablename );
					$resultadd = $model->runSql ( $addOrder );
				}
				//如果发起精品拼课
				if(1==$class_quality){
					// 根据拼课信息 给发起人生成一张未支付订单 class_types=1 标记为精品  没有老师  
					$addOrder = 'insert into order_list (user_tid,order_type,
				     order_remark,order_phone,order_address,
				     user_spelling_lesson_tid,order_name,spelling_lesson_type,class_types,high_quality_courses_tid)
				     select ' . $user_tid . ',' . $spelling_type . ',"' . $user_message . '","' . $user_phone . '","' . $user_venue . '",' . $tid . ',"' . $user_name . '",0,1,'.$high_quality_courses_tid . ' from  user_spelling_lesson a where a.tid=' . $tid;
					$model = spClass ( $this->tablename );
					$resultadd = $model->runSql ( $addOrder );
				}
                $addOrder = '';
				// 当发起拼课成功后标记该学生已经开始拼课 不能再次发起或参与其他拼课
				$update = 'update user_info set spelling_class_state=1 where tid=' . $user_tid;
				$model = spClass ( $this->tablename );
				$resultup = $model->runSql ( $update );
				// $msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
				// 当学生状态改好了以后 客服那边更改初始状态
				$update = "update order_list set spelling_state=1 where order_type=3 and pay_done=0 and  user_tid=" . $user_tid;
				$model = spClass ( 'order_list' );
				$resultup = $model->runSql ( $update );
				// 将spelling_tid和经纬度上传到百度云服务器
				if ($newrow ['longitude'] && $newrow ['latitude']) {
					$url = "http://{$_SERVER['HTTP_HOST']}/srvapi/framework/controller/tools/lbscloud/geodata_v3_poi_create.php?longitude={$newrow['longitude']}&latitude={$newrow['latitude']}&spelling_tid={$result[0]['tid']}";
					file_get_contents ( $url );
				}
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, 'fail', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '您正在拼课中，不能重复拼课！', $result, 0, $prefixJS );
		}
		return true;
	}
	
	
	//拼课团伙的详情页面
	function query() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$class_quality=$newrow['class_quality'];
		$high_quality_courses_tid=$newrow['high_quality_courses_tid'];
		$city=$newrow['city'];
		// 查询参与者人数
		$querySql = 'select user_tid,participants_number,teaching_grade,teache_class,teacher_tid from user_spelling_lesson where tid=' . $tid;
		$model = spClass ( $this->tablename );
		$resulquery = $model->findSql ( $querySql );
		$user_tid=$resulquery[0]['user_tid'];//获取发起人ID
		$participants_number = $resulquery [0] ['participants_number']; // 参与者人数 包括发起人
		$teaching_grade = $resulquery [0] ['teaching_grade']; // 初中高
		$teache_class = $resulquery [0] ['teache_class']; // 一年级
		                                                  // 查询年级单价
		# 取出teacher_class中的数字
		$teacher_class_no = substr ( $teache_class, 0, strpos($teache_class,"年"));
		$querySql = "select c.class_price from class_price c, teacher_info t where 
						c.class_city=t.teacher_city and
				 		t.tid = \"{$resulquery [0] ['teacher_tid']}\" and c.class_grade = \"{$teacher_class_no}\";";
		$model = spClass ( $this->tablename );
		$resultp = $model->findSql ( $querySql );
		// 根据参与者人数确定订单原价及实际价格
		switch ($participants_number) {
			//根据人数计算价格
			case $participants_number : 
				//当拼课为普通课时(class_quality: 0为普通拼课  1为精品拼课 必传字段)
				if($class_quality ==''){
					$msg->ResponseMsg ( 1, '无法判断课程类型！', $result, 0, $prefixJS );
					exit;
				}
				if(0==$class_quality){
					$querySql = 'select c.class_count,c.class_disc,c.class_hour,c.spell_class_num from spelling_lesson_discount c,user_info u
		      		   where u.user_city=c.city  and u.tid='.$user_tid.' and c.spell_class_num='.$participants_number;
					$model = spClass ( $this->tablename );
					$resultd = $model->findSql ( $querySql );
					if(!$resultd){
						$msg->ResponseMsg ( 1, '没有获取到普通拼课的折扣及课时数信息 或不存在该拼课！', $result, 0, $prefixJS );
						exit;
					}
					$original_price = ($resultd [0] ['class_hour']) * ($resultp [0] ['class_price']); // 原价
					$actual_price = ($resultd [0] ['class_hour']) * ($resultp [0] ['class_price'])* $resultd [0] ['class_disc'];; // 实际价格
					
					// 更新评课信息价格 和所有人订单价格
					$updateSql = 'update user_spelling_lesson u,order_list o set  u.original_price=' . $original_price . ',u.actual_price=' . $actual_price . '
						   ,o.order_money=' . $actual_price . ',o.order_original_price=' . $original_price . ' where u.tid=o.user_spelling_lesson_tid and u.tid=' . $tid;
					$model = spClass ( $this->tablename );
					$resul = $model->runSql ( $updateSql );
                    // 不能以学生的身份进入 否则只能看到该学生的信息 根据拼课信息查询所有拼课 参与者的信息
// 					$querySql = 'select distinct u.tid as user_tid,l.tid as user_spelling_lesson_tid,o.tid as order_tid,t.teacher_name,t.teacher_image,l.course_package,o.order_name,u.user_image,l.spelling_lesson_number,l.participants_number,
// 	    		         l.teaching_grade,l.teache_class, l.original_price,l.actual_price,l.user_venue,l.teaching_week,l.teacher_time,o.spelling_lesson_type,o.class_types,
// 						 from user_info u inner join order_list o on u.tid=o.user_tid inner join
// 	    		         user_spelling_lesson  l  on o.user_spelling_lesson_tid=l.tid inner join teacher_info t
// 	    		         on t.tid=l.teacher_tid 
// 	    		         where l.tid=' . $tid . ' group by user_tid order by o.spelling_lesson_type ASC';
				
					$querySql = 'select distinct u.tid as user_tid,l.tid as user_spelling_lesson_tid,o.tid as order_tid,t.teacher_name,t.teacher_image,l.course_package,o.order_name,u.user_image,l.spelling_lesson_number,l.participants_number,
	    		         l.teaching_grade,l.teache_class, l.original_price,l.actual_price,l.user_venue,l.teaching_week,l.teacher_time,o.spelling_lesson_type,o.class_types
	    		         from user_info u inner join order_list o on u.tid=o.user_tid inner join
	    		         user_spelling_lesson l  on o.user_spelling_lesson_tid=l.tid inner join teacher_info t
	    		         on t.tid=l.teacher_tid 
	    		         where l.class_quality=0 and l.tid=' . $tid . ' group by user_tid order by o.spelling_lesson_type ASC';
					$model = spClass ( $this->tablename );
					$result = $model->findSql ( $querySql );
					if(!$result){
						$msg->ResponseMsg ( 1, '查询失败', $result, 0, $prefixJS );
						exit;
					}
					// 					//根据城市获取折扣拼课信息
					$querySql='select s.spell_class_num,s.class_disc from spelling_lesson_discount s,user_info u
												where s.city=u.user_city and u.tid='.$user_tid.' order by s.spell_class_num ASC';
					$model = spClass ( $this->tablename );
					$resultsd = $model->findSql ( $querySql );
					// 					print_r($resultsd);
					// 					exit;
					//将数组合体 插入一个数组里面
					$result[0]['class_disc']=$resultsd;
						
// 					//根据城市获取折扣拼课信息
// 					$querySql='select s.spell_class_num,s.class_disc from spelling_lesson_discount s,user_info u
// 												where s.city=u.user_city and u.tid='.$user_tid.' order by s.spell_class_num ASC';
// 					$model = spClass ( $this->tablename );
// 					$resultsd = $model->findSql ( $querySql );
					//将数组合体
// 					$result[0]['class_disc']=$resultsd;
					
					if ($result) {
						$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
					} else {
						$msg->ResponseMsg ( 1, '查询失败', $result, 0, $prefixJS );
					}
					break;
				}
				//当拼课为精品课时
				if(1==$class_quality){
					if($high_quality_courses_tid == ''){
						$msg->ResponseMsg ( 1, '不能获取拼课信息！', $result, 0, $prefixJS );
						exit;
					}
					//查询各城市   各种精品课 课时数和单价 及人数的折扣信息   匹配发起人城市
					$querySql='select h.class_hour,h.high_quality_price,s.class_disc from high_quality_courses h,user_info u,spelling_lesson_discount s
							   where h.city=u.user_city and s.city=u.user_city and u.tid='.$user_tid.' and  h.tid='.$high_quality_courses_tid.' and s.spell_class_num='.$participants_number;
					$model = spClass ( $this->tablename );
					$resulquer = $model->findSql ( $querySql );
					if(!$resulquer){
						$msg->ResponseMsg ( 1, '没有获取到精品拼课的价格或者折扣信息 或不存在该拼课！', $result, 0, $prefixJS );
						exit;
					}
					//原价
					$original_price=($resulquer[0]['class_hour'])*($resulquer[0]['high_quality_price']);
					//实际价格
					$actual_price=($resulquer[0]['class_hour'])*($resulquer[0]['high_quality_price'])*($resulquer[0]['class_disc']);
					//更新价格到订单表和拼课信息表
					$updateSql = 'update user_spelling_lesson u,order_list o set  u.original_price=' . $original_price . ',u.actual_price=' . $actual_price . '
						   ,o.order_money=' . $actual_price . ',o.order_original_price=' . $original_price . ' where u.tid=o.user_spelling_lesson_tid and u.tid=' . $tid;
					$model = spClass ( $this->tablename );
					$resul = $model->runSql ( $updateSql );
					// 获取精品课程详情页面
					$querySql = 'select distinct u.tid as user_tid,l.tid as user_spelling_lesson_tid,o.tid as order_tid,l.course_package,o.order_name,u.user_image,l.spelling_lesson_number,l.participants_number,
	    		         l.teaching_grade,l.teache_class, l.original_price,l.actual_price,l.user_venue,l.teaching_week,l.teacher_time,o.spelling_lesson_type,
							h.high_quality_name,h.high_quality_image,h.class_hour,h.high_quality_price,l.course_package,o.class_types
	    		         from user_info u inner join order_list o on u.tid=o.user_tid inner join
	    		         user_spelling_lesson  l  on o.user_spelling_lesson_tid=l.tid left join high_quality_courses h
							on l.high_quality_courses_tid=h.tid 
	    		         where l.class_quality=1 and l.tid=' . $tid . ' group by user_tid order by o.spelling_lesson_type ASC';
					
					$model = spClass ( $this->tablename );
					$result = $model->findSql ( $querySql );
// 					print_r($result);
					if(!$result){
						$msg->ResponseMsg ( 1, '查询失败', $result, 0, $prefixJS );
						exit;
					}
// 					//根据城市获取折扣拼课信息
					$querySql='select s.spell_class_num,s.class_disc from spelling_lesson_discount s,user_info u
												where s.city=u.user_city and u.tid='.$user_tid.' order by s.spell_class_num ASC';
					$model = spClass ( $this->tablename );
					$resultsd = $model->findSql ( $querySql );
// 					print_r($resultsd);
// 					exit;
					//将数组合体 插入一个数组里面
                    $result[0]['class_disc']=$resultsd;

					if ($result) {
						$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
					} else {
						$msg->ResponseMsg ( 1, '查询失败', $result, 0, $prefixJS );
					}
					break;
				}
				

			default :
		}

		return true;
	}
	/**
	 * 功能：拼课相关列表显示
	 * 作者：陈鸿润
	 * 修改人：陈鸿润
	 * 修改日期：2015年9月7日
	 */
	function querySpellingLesson() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		
		switch ($newrow ['type']) { // 1为参与人寻找拼课，2为我的拼课记录
			case 1 : // 寻找拼课
			         // 如果没有传值，结束脚本运行 || !$newrow['user_tid']
				if (! $newrow ["class_city"]) {
					$msg->ResponseMsg ( 0, '缺少必传条件，请检查', $result, 0, $prefixJS );
					exit ();
				}
				// 连接user_spelling_lesson u, teacher_info t, order_list o等表，进行查询
				$querySql ="select 
								if(u.tid=
									if(u.class_quality=0,
										(select u.tid from user_spelling_lesson u, teacher_info t, order_list o
										where u.teacher_tid = t.tid and o.user_spelling_lesson_tid = u.tid
										and u.pay_done = 0 and o.user_tid = \"{$newrow["user_tid"]}\"),
										(select u.tid from user_spelling_lesson u, high_quality_courses h, order_list o
										where u.high_quality_courses_tid = h.tid and o.user_spelling_lesson_tid = u.tid
										and u.pay_done = 0 and o.user_tid = \"{$newrow["user_tid"]}\")
									),1,0
								)
								as join_state,
								o.create_time,
								o.user_tid as order_user_tid,
                                t.teacher_image,
								t.teacher_name,
                                h.high_quality_image,
                                h.high_quality_name,
								u.*
							from user_spelling_lesson u LEFT JOIN teacher_info t ON u.teacher_tid=t.tid
														LEFT JOIN high_quality_courses h ON u.high_quality_courses_tid = h.tid
														inner JOIN order_list o ON u.tid = o.user_spelling_lesson_tid
							where o.spelling_lesson_type = 0 and u.pay_done=0
                            and (
                                u.spelling_lesson_number != u.participants_number or 
									u.tid = (
									select u.tid from user_spelling_lesson u, teacher_info t, order_list o
										where u.teacher_tid = t.tid and o.user_spelling_lesson_tid = u.tid
										and u.pay_done = 0 and o.user_tid = \"{$newrow["user_tid"]}\"
									) or
                                    u.tid = (
									select u.tid from user_spelling_lesson u, high_quality_courses h, order_list o
										where u.high_quality_courses_tid = h.tid and o.user_spelling_lesson_tid = u.tid
										and u.pay_done = 0 and o.user_tid = \"{$newrow["user_tid"]}\"
									) 
								)";
				// 根据城市寻找拼课
				if ($newrow ["class_city"]) {
					$querySql .= " and ( t.teacher_city = \"{$newrow["class_city"]}\" or h.city = \"{$newrow["class_city"]}\")";
				}
				// 根据区寻找拼课
				if ($newrow ["class_district"]) {
					$querySql .= " and t.teacher_district = \"{$newrow['class_district']}\"";
				}
				// 根据街道办寻找拼课
				if ($newrow ["class_place"]) {
					$querySql .= " and t.teacher_town = \"{$newrow['class_place']}\"";
				}
				// 根据年级寻找拼课
				if ($newrow ["class_grade"]) {
					$querySql .= " and u.teaching_grade=\"{$newrow['class_grade']}\"";
				}
				// 根据创建时间降序排序
				$querySql .= " order by o.create_time DESC;";
				// 查询数据库
				$model = spClass ( 'user_spelling_lesson' );
				$result = $model->findSql ( $querySql );
				// 根据总拼课人数（spelling_lesson_number）减去 实际拼课人数（participants_number）
				// 升序排序（冒泡）
				for($i = 0; $i < count ( $result ) - 1; $i ++) {
					for($j = 0; $j < count ( $result ) - 1 - $i; $j ++) {
						if (($result [$j] ['spelling_lesson_number'] - $result [$j] ['participants_number']) >= ($result [$j + 1] ['spelling_lesson_number'] - $result [$j + 1] ['participants_number'])) {
							$translation = $result [$j];
							$result [$j] = $result [$j + 1];
							$result [$j + 1] = $translation;
						}
					}
				}	
				# 加入排序条件
				if($newrow['sort']){
					switch($newrow['sort']){
						case 1: # 距离最近
							# 如果获取经纬度失败，则直接返回查询出来的$result
							if(!$newrow['longitude'] || !$newrow['latitude']){
								$msg->ResponseMsg ( 1, '获取经纬度失败。', $result, 0, $prefixJS );
								exit;
							}							
							$url = "http://{$_SERVER['HTTP_HOST']}/srvapi/framework/controller/tools/lbscloud/geosearch_v3_nearby.php?longitude={$newrow['longitude']}&latitude={$newrow['latitude']}";
							$spellig_tid_result = json_decode ( file_get_contents ( $url ) )->contents;
							# 判断百度地图接口是否调用成功，若不成功则使用本地算法实现距离最短排序
							# 由于百度地图出现未知bug，先用本地算法实现
							$spellig_tid_result = false;
							if($spellig_tid_result){
								foreach ( $spellig_tid_result as $tk => $tv ) {
									foreach ( $result as $bk => $bv ) {
										if ($tv->spelling_tid == $bv ['tid']) {
											$result_by_sort [] = $result [$bk];
											break;
										}
									}
								}
								$result = $result_by_sort;
							}else{ # 根据两个经纬度点，所确定的矩形，算出对角线的平方，并假定为两点之间的距离平方（升序排序）
								foreach( $result as $k => $v){
									$result[$k]['distance'] = pow( ($newrow['longitude']-$v['longitude']), 2) + pow( ($newrow['latitude']-$v['latitude']), 2);
								}
								// 升序排序（冒泡）
								for($i = 0; $i < count ( $result ) - 1; $i ++) {
									for($j = 0; $j < count ( $result ) - 1 - $i; $j ++) {
										if ( ($result [$j] ['distance']) >= ($result [$j + 1] ['distance']) ) {
											$translation = $result [$j];
											$result [$j] = $result [$j + 1];
											$result [$j + 1] = $translation;
										}
									}
								}
							}
							break;
						case 2 : // 时间最新
						         // 根据评课信息产生时间，降序排序
							for($i = 0; $i < count ( $result ) - 1; $i ++) {
								for($j = 0; $j < count ( $result ) - 1 - $i; $j ++) {
									if (strtotime ( $result [$j] ['create_time'] ) < strtotime ( $result [$j + 1] ['create_time'] )) {
										$translation = $result [$j];
										$result [$j] = $result [$j + 1];
										$result [$j + 1] = $translation;
									}
								}
							}
							break;
					}
				}
				# 过滤掉数组中，值为空的项
					foreach($result as $index_key => $index_key_value){
						foreach($index_key_value as $relate_key => $relate_value ){
							if(null === $relate_value){													
								continue;
							}
							$result_relate[$relate_key] = $relate_value;
						}						
						$result_filter[$index_key] = $result_relate;
						unset($result_relate);
					}
				$msg->ResponseMsg ( 0, '查询成功', $result_filter, 0, $prefixJS );
				break;
			case 2 : // 查询自己的拼课记录
			         // 如果传了user_tid,则是查询该tid的拼课记录
				if (!$newrow ['user_tid']) {
					$msg->ResponseMsg ( 1, '缺少必传参数user_tid', null, 1, $prefixJS );
					exit;
				}
					$querySql = "select o.create_time,o.pay_done as order_pay_done,
									o.spelling_lesson_type, t.teacher_image,
									t.teacher_name, h.high_quality_image, h.high_quality_name, u.* 
									from user_spelling_lesson u LEFT JOIN teacher_info t ON u.teacher_tid=t.tid
									LEFT JOIN high_quality_courses h ON u.high_quality_courses_tid = h.tid
									inner JOIN order_list o ON u.tid = o.user_spelling_lesson_tid
									where  o.user_tid = \"{$newrow["user_tid"]}\"
									order by o.create_time DESC;";
					$model = spClass ( 'user_spelling_lesson' );
					$user_spelling_lesson_result = $model->findSql ( $querySql );
					# 过滤掉数组中，值为空的项
					foreach($user_spelling_lesson_result as $index_key => $index_key_value){
						foreach($index_key_value as $relate_key => $relate_value ){
							if(null === $relate_value){													
								continue;
							}
							$result_relate[$relate_key] = $relate_value;
						}						
						$result_filter[$index_key] = $result_relate;
						unset($result_relate);
					}
					$msg->ResponseMsg ( 0, '查询成功', $result_filter, 1, $prefixJS );
				break;
		}
	}
	// 参与者加入拼课团伙
	function addSpellingLesson() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$user_tid = $newrow ['user_tid'];
		$user_name = $newrow ['user_name'];
		// $user_telephome=$newrow['user_telephome'];
		$user_remark = $newrow ['user_remark'];
		$user_spelling_lesson_tid = $newrow ['user_spelling_lesson_tid'];
		$class_quality=$newrow['class_quality']; //拼课类型  0普通  1精品
		$high_quality_courses_tid=$newrow['high_quality_courses_tid'];
		// 判断要加入拼课团伙的发起人是否支付 若支付 则不能加入
		$querySql = 'select o.pay_done,u.spelling_lesson_number,u.participants_number from order_list o,user_spelling_lesson u where o.user_spelling_lesson_tid=u.tid and spelling_lesson_type=0 and u.tid=' . $user_spelling_lesson_tid;
		$model = spClass ( $this->tablename );
		$resultqu = $model->findSql ( $querySql );
		
		// 参与者人数不能超过 发起人选择的最大人数 且发起人必须未支付
		if (0 == $resultqu [0] ['pay_done'] && ($resultqu [0] ['participants_number'] + 1) <= $resultqu [0] ['spelling_lesson_number']) {
			
			// 判断该学生是否已有参与拼课 或发起拼课
			$querySql = 'select spelling_class_state,telephone from user_info where tid=' . $newrow ['user_tid'];
			$model = spClass ( $this->tablename );
			$resulqu = $model->findSql ( $querySql );
			// echo $resulqu[0]['spelling_class_state'];
			// exit;
			$user_telephome = $resulqu [0] ['telephone'];
			//spelling_class_state 为0表示可以参与
			if (0 == $resulqu [0] ['spelling_class_state']) {
				//参与普通课程
				if(0==$class_quality){
					// 给新增的拼课合伙人 新增一条未支付订单
					$addSql = 'insert into order_list (teacher_tid,user_tid,order_type,
				order_remark,order_phone,order_address,
				user_spelling_lesson_tid,order_name,spelling_lesson_type)
				 select teacher_tid,' . $user_tid . ',spelling_type,"' . $user_remark . '",' . $user_telephome . ',user_venue,
				 				tid,"' . $user_name . '",1
				 from user_spelling_lesson a where a.tid=' . $user_spelling_lesson_tid;
					$model = spClass ( 'order_list' );
					$result = $model->findSql ( $addSql );
				}
				//参与精品课程
				elseif(1==$class_quality){
					if($high_quality_courses_tid == ''){
						$msg->ResponseMsg ( 1, '缺少精品课ID！！', $result, 0, $prefixJS );
						exit;
					}
					// 给新增的拼课合伙人 新增一条未支付订单
					$addSql = 'insert into order_list (user_tid,order_type,
				   order_remark,order_phone,order_address,
				   user_spelling_lesson_tid,order_name,spelling_lesson_type,class_types,high_quality_courses_tid)
				   select ' . $user_tid . ',spelling_type,"' . $user_remark . '",' . $user_telephome . ',user_venue,
				 				tid,"' . $user_name . '",1,1,'.$high_quality_courses_tid .'
				    from user_spelling_lesson a where a.tid=' . $user_spelling_lesson_tid;
					$model = spClass ( 'order_list' );
					$result = $model->findSql ( $addSql );
				}
				
// 				$model = spClass ( 'order_list' );
// 				$result = $model->findSql ( $addSql );
				if ($result <= 0) {
					return;
				}
				// 根据新增的tid 查询该条信息所有内容
				$result = $model->findAll ( array (
						$model->pk => $result 
				) );
				// echo $result[0]['tid'];
				// exit;
				if ($result > 0) {
					// 每增加一个参与者 拼课团伙里的参与者人数加1
					$updateSql = 'update user_spelling_lesson set participants_number=participants_number+1 where tid=' . $user_spelling_lesson_tid;
					$model = spClass ( $this->tablename );
					$resultup = $model->findSql ( $updateSql );
					// 该学生标记为正在参与拼课 或发起拼课
					$update = 'update user_info set spelling_class_state=1 where tid=' . $user_tid;
					$model = spClass ( $this->tablename );
					$resultup = $model->runSql ( $update );
					//學生加入拼課 狀態該為正在拼課                                     spelling_state
					$updateS = 'update order_list set spelling_state=1 where order_type=3 and pay_done=0 and  user_tid=' . $user_tid;
					$model = spClass ( $this->tablename );
					$results = $model->runSql ( $updateS );
// 					$update = "update order_list set spelling_state=1 where user_tid=" . $user_tid;
// 					$model = spClass ( 'order_list' );
// 					$resultup = $model->runSql ( $update );
					
					
					$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
				} else {
					$msg->ResponseMsg ( 1, 'fail', $result, 0, $prefixJS );
				}
			} else {
				$msg->ResponseMsg ( 1, '您已发起或正在参与拼课，请不要重复拼课！', $result, 0, $prefixJS );
			}
		} else {
			$msg->ResponseMsg ( 1, '该拼课人数已满，已停止加入！请选择其他拼课加入！谢谢！', $result, 0, $prefixJS );
		}
		return true;
	}
	// 查询当前时间后一周的教师时间状态
	function queryTimeState() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$teacher_tid = $newrow ['teacher_tid'];
		date_default_timezone_set ( 'PRC' ); // 设置时区
		$time = time (); // 获取当前时间
		$nowDate = date ( "Y-m-d", $time ); // 获取服务器当前年月日
		$a_date = strtotime ( $nowDate ); // 获取当前日期的时间戳
		$b_time = strtotime ( '+' . (1) . 'week', $a_date ); // 获取一周后时间戳
		$afterweekdate = date ( 'Y-m-d', $b_time ); // 获取七天后的日期
		
		if (! $newrow) {
			$msg->ResponseMsg ( 1, '请输入日期', $result, 0, $prefixJS );
			exit ();
		} else {
			$querySql = 'select schedule_date,schedule_time,time_busy from teacher_schedule where 
				schedule_date>="' . $nowDate . '" and schedule_date<"' . $afterweekdate . '" and teacher_tid=' . $teacher_tid;
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, 'fail', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	// 快速约课课
	function fastAboutClass() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		// $potential_phone=$newrow['potential_phone'];
		$user_tid = $newrow ['user_tid'];
		if($user_tid == ''){
			$msg->ResponseMsg ( 1, '您好！学生id不能为空！', $result, 0, $prefixJS );
			exit ();
		}
		// 查询该学生注册电话  以及该学生是否约过课fast_count>0
		$query = 'select telephone,fast_count from user_info where tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$resultqu = $model->findSql ( $query );
		if($resultqu[0]['fast_count']>0){
			$msg->ResponseMsg ( 1, '您好！快速约课每人仅限一次！', $result, 0, $prefixJS );
			exit ();
		}
		//将电话加入$newrow里面
		$telephone = $resultqu [0] ['telephone'];
		$newrow ['potential_phone'] = $telephone;
		
		// 新增
		
// 		$addSql = 'insert kf_potential_info set  potential_register = 1,'; // 快速约客都是已注册用户
// 		foreach ( $newrow as $k => $v ) {
			
// 			$addSql = $addSql . $k . '="' . $v . '",';
// 		}
		
// 		$addSql = substr ( $addSql, 0, strlen ( $addSql ) - 1 );
// 		// echo $addSql;
 		$model = spClass ( 'kf_potential_info' );
		 $result = $model->create ( $newrow );
		//$result = $model->runSql ( $addSql );
		// echo $result[0]['tid'];
		if ($result <= 0) {
			return;
		}
		// 根据新增的tid 查询该条信息所有内容
		$result = $model->findAll ( array (
				$model->pk => $result 
		) );
		
// 		print_r ($result) ;//['0']['tid'];
// 		exit;
		if ($result > 0) {
			
			// 查询该用户是否有订单
			$query = 'select count(tid) as num from order_list where user_tid=' . $user_tid;
			$model = spClass ( $this->tablename );
			$resultqu = $model->findSql ( $query );
			
// 			echo $resultqu[0] ['num'];
// 			exit;
			$update = 'update kf_potential_info set potential_register=1  where tid='. $result[0]['tid'];
			$model = spClass ( $this->tablename );
			$resultup = $model->runSql ( $update );
			if ($resultqu [0] ['num'] > 0 && $result[0]['tid'] !='') {
				// 如果有订单 状态改为已有客户 
				$update = 'update kf_potential_info set potential_change=0  where tid='. $result[0]['tid'];
				$model = spClass ( $this->tablename );
				$resultup = $model->runSql ( $update );
				
				
			}
			// 将快速约课次数+1
			$update='update user_info set fast_count=fast_count+1 where tid='.$user_tid;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $update );
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, 'fail', $result, 0, $prefixJS );
		}
		return true;
	}
	// 根据城市查询表中该城市对应所有拼课折扣信息
	function querySpellingDiscount() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$city = $newrow ['city'];
		if (! $newrow) {
			$msg->ResponseMsg ( 1, '请传入城市', $result, 0, $prefixJS );
			exit ();
		} else {
			//不返回人数为1时的折扣信息
			$querySql = 'select * from spelling_lesson_discount where spell_class_num !=1 and city = "' . $city . '"';
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '没有获取到折扣信息！', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	// 学生取消拼课 判断身份
	function cancelSpellingLesson() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$user_tid = $newrow ['user_tid'];
		// $student_identity=$newrow['student_identity']; //学生身份
		// 判断学生身份
		$querySql = 'select spelling_lesson_type from order_list o,user_spelling_lesson u where 
	 			u.tid=o.user_spelling_lesson_tid and o.user_tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		$spelling_lesson_type = $result [0] ['spelling_lesson_type'];
		// $msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
		// exit;
		// 如果学生身份是发起人
		if (0 == $result [0] ['spelling_lesson_type']) {
			// $querySql='select participants_number from user_spelling_lesson
			// where tid='.$tid;
			$querySql = 'select u.participants_number,o.pay_done from user_spelling_lesson u,order_list o 
	 				where u.tid=o.user_spelling_lesson_tid and u.tid=' . $tid;
			
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			// 没有参与者 且 发起人没支付
			if ($result [0] ['participants_number'] <= 1 && 0 == $result [0] ['pay_done']) {
				// 当没有参与者 时 可以取消
				$deleteSql = 'delete u,o from user_spelling_lesson u inner join order_list o on u.tid=o.user_spelling_lesson_tid
	 					where u.tid=' . $tid;
				
				$model = spClass ( $this->tablename );
				$resultup = $model->runSql ( $deleteSql );
				// $msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
				// 每人只能参与一个拼课 当发起人取消时 又可以发起或参与新的拼课
				$update = 'update user_info set  spelling_class_state=0 where tid=' . $user_tid;
				$resultup = $model->runSql ( $update );
				$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, '您好，由于有人参与您的拼课或您已支付，所以您不能取消拼课！', $result, 0, $prefixJS );
			}
		} // 判断当学生身份是参与者
            elseif (1 == $result [0] ['spelling_lesson_type']) {
			
			      // 查询发起人是否支付
			      $querySql = 'select o.user_tid,o.spelling_lesson_type,o.pay_done from order_list o,user_spelling_lesson s
	 			                where s.tid=o.user_spelling_lesson_tid and o.spelling_lesson_type=0  and s.tid=' . $tid;
			                   $model = spClass ( $this->tablename );
			                   $result = $model->findSql ( $querySql );
			                   // $msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			                   // echo $result[0]['pay_done'];
			                   // exit;
			                   if (0 == $result [0] ['pay_done']) { // 发起人未支付 参与者可以取消拼课
			                                     
				                 // 查询参与者的订单号
				               $querySql = 'select tid from order_list where user_spelling_lesson_tid=' . $tid . ' and
	 			                        	spelling_lesson_type=' . $spelling_lesson_type . ' and user_tid=' . $user_tid;
			                   $model = spClass ( $this->tablename );
				               $result = $model->findSql ( $querySql );
				               $order_tid = $result [0] ['tid'];
				               // echo $order_tid;
				               // exit;
				               // 删除参与者的订单信息
				               $deleteSql = 'delete from order_list where tid=' . $order_tid;
				               $model = spClass ( $this->tablename );
				               $resultup = $model->runSql ( $deleteSql );
				               // 参与者取消拼课后 拼课信息里人数-1
				               $updateSql = 'update user_spelling_lesson set participants_number=participants_number-1 where tid=' . $tid;
				               $model = spClass ( $this->tablename );
				               $resultup = $model->runSql ( $updateSql );
				               // 每人只能参与一个拼课 当参与者取消时 又可以发起或参与新的拼课
				               $update = 'update user_info set  spelling_class_state=0 where tid=' . $user_tid;
				               $model = spClass ( $this->tablename );
				               $resultup = $model->runSql ( $update );
				               $msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			           } else { // 否则不能取消拼课
				
				$msg->ResponseMsg ( 1, '您好！由于发起人已支付，所以您不能取消拼课！', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	// 参与者支付时判断发起人是否支付完成
	function theSponsorsPay() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid']; // 拼课id
		$user_tid = $newrow ['user_tid'];
		// 判断学生身份
		$querySql = 'select spelling_lesson_type from order_list o,user_spelling_lesson u where 
	 			u.tid=o.user_spelling_lesson_tid and o.user_tid=' . $user_tid;
		$model = spClass ( $this->tablename );
		$result = $model->findSql ( $querySql );
		$spelling_lesson_type = $result [0] ['spelling_lesson_type'];
		
		// 如果学生身份是发起人
		if (0 == $result [0] ['spelling_lesson_type']) {
			$msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
		} elseif (1 == $result [0] ['spelling_lesson_type']) { // 如果是参与者
		                                                       
			// 查询发起人是否支付
			$querySql = 'select o.user_tid,o.spelling_lesson_type,o.pay_done from order_list o,user_spelling_lesson s
	 			where s.tid=o.user_spelling_lesson_tid and o.spelling_lesson_type=0  and s.tid=' . $tid;
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			// $msg->ResponseMsg ( 0, 'secsess', $result, 0, $prefixJS );
			// echo $result[0]['pay_done'];
			// exit;
			if (0 == $result [0] ['pay_done']) { // 发起人未支付参与者不能支付
				
				$msg->ResponseMsg ( 1, '您好！请等待发起人支付！', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, '发起人已支付！您可以继续支付！', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	// 禁止以下action实例化基类
	function delete() {
		return false;
	}
	function update() {
		return false;
	}
	function add() {
		return false;
	}
}