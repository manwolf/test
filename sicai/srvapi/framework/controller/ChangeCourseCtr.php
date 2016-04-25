<?php
		//include_once "tools/tokenGen.php";
		include_once 'TeacherIntroduction.php';
		//include_once "tools/defSqlInject.php";
		include_once 'base/crudCtr.php';
		include_once 'base/checkCtr.php';
		
		class ChangeCourseCtr extends crudCtr {
			public function __construct() {
				$this->tablename = 'teacher_info';
			}
			/**
			 * 功能：客服取消课程，排课，查询排课等操作
			 * 作者： 李坡
			 * 创建日期：2015年9月6日
			 */
	#客服取消课程
			function cancelCourse() {
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
				$verify = new checkCtr ();
				$result = $verify->acl ();
				
				if ($result) {
				$class_tid=$newrow['class_tid'];
				$order_tid=$newrow['order_tid'];
				//根据课程id查询老师状态tid
				$querySql="select tid from teacher_schedule  where class_tid=".$class_tid;			
				$model = spClass ( 'teacher_schedule' );
				$result = $model->findSql ( $querySql);
				$tid=$result[0]['tid'];
// 				echo $tid;
// 				exit;
				//根据课程id查询订单id
				$querySql="select order_tid from class_list where tid=".$class_tid;
				$model = spClass ( 'class_list' );
				$result= $model->findSql ( $querySql);
				$order_tid1=$result[0]['order_tid'];
// 				echo $order_tid1;
// 				exit;
				
				if($newrow && $order_tid1==$order_tid  ){ 
				//如果老师取消的订单跟课程订单一致则删除课程表里的课程
				$delSql = 'delete from class_list where tid>0 AND tid='.$class_tid ;
// 			    echo $delSql;
// 			    exit;
				$model = spClass ( 'class_list' );
				
				if( $result = $model->runSql ( $delSql )){
				//删除课程表里的课程之后修改老师状态为空闲
				$updateSql ="update teacher_schedule set time_busy=0 where tid=".$tid;
				$model = spClass ( 'teacher_schedule' );
				$result = $model->runSql ( $updateSql );
				
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '取消成功！', $result, 0, $prefixJS);
			
			}
				}
			else{
				$msg->ResponseMsg ( 1, '您无法取消非此订单下的课程！', 1, 0, $prefixJS );
			}
			}else{
				$msg->ResponseMsg(1, '身份验证失败', 1, 1, $prefixJS);
				
			}
			}
			
			#客服排课之后查询排课结果
			function KFTeacherCurriculum()
			{
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
				$verify = new checkCtr ();
				$result = $verify->acl ();
				if ($result) {
				$tid = $newrow['tid'];
				if( !$newrow)
				{
					$msg->ResponseMsg (1, 'tid为空', flase, 0, $prefixJS );
					return ;
				}
				else
				{//查询老师状态 
					$querySql='select o.tid as order_tid,s.teacher_entry_state,s.teacher_rest_state,s.time_busy,s.schedule_date,s.schedule_time,
				 u.user_name,o.class_content,s.tid,c.tid as class_tid
				 from  teacher_info t
				 left join  teacher_schedule s on t.tid=s.teacher_tid
				 left join  class_list c  on  s.class_tid=c.tid
				 left join  order_list o  on  c.order_tid=o.tid
				 left join    user_info u on o.user_tid=u.tid
                where   t.tid='.$tid;
				$model = spClass ( $this->tablename );
				$result = $model->findSql ( $querySql );
				$verify = new checkCtr ();
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功', $result, 0, $prefixJS );
				
				}
 			}else{
				$msg->ResponseMsg ( 1, '身份验证失败', 1, 0, $prefixJS );
				
			}
			}
	#客服加课
			function addCourse(){
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
				$verify = new checkCtr ();
				$result = $verify->acl ();
				if ($result) {
				$class_count=$newrow['class_count'];//课程总数
				$date=date('Y-m-d');//获取当前时间
// 				echo $date;
// 				exit;
// 				unset($newrow['date']);
				unset ($newrow['class_count']);
				unset($newrow['user_tid']);
					if(!$newrow['order_tid']){
					$msg->ResponseMsg ( 1, 'order_tid为空。', 1, 1, $prefixJS );
					return;
				}
				$order_tid=$newrow['order_tid'];
				$class_start_date=$newrow['class_start_date'];
				$class_start_time=$newrow['class_start_time'];
				//查询已拍的课程
				$querySql ="select count(*) as count from class_list where order_tid=".$order_tid;
// 				echo $querySql;
// 				exit;
				$model = spClass ( 'class_list' );
				$result = $model->findSql ( $querySql );
				$sum= $result[0]['count'];
				if($sum==$class_count){
					$msg->ResponseMsg ( 1, '此订单已经排完课！。', 1, 1, $prefixJS );
					return ;	
				}
				//根据订单tid查询老师的tid
				$querySql="select teacher_tid from order_list where tid=".$order_tid;
				$model = spClass ( 'order_list' );
				$result = $model->findSql($querySql);
				$teacher_tid=$result[0]['teacher_tid'];
			   
			if( $class_start_date !=null  && $class_start_time !=null )// 如果点击的地方课程表为空 则填写上课时间 添加课程到此处
			{   
				if($class_start_date <= $date){//如果排的课程是本天的 不能排课
					$msg->ResponseMsg ( 1, '不能排当天课程', false, 0, $prefixJS );
					return ;	
				}
				//查询这个时间点是否排过课 如果排过就不能继续拍
				$querySql="select count(order_tid)as count,order_tid from class_list where class_start_date='".$class_start_date."'" . "and class_start_time='".$class_start_time."'";
				$model = spClass ( 'class_list' );
				$result = $model->findSql($querySql);
				if($result[0]['order_tid'] != $order_tid && $result[0]['count']==0 ){
				$addSql = "insert class_list set ";
				
				foreach ( $newrow as $k => $v )
				{
					$addSql = $addSql . $k . '="' . $v . '",';
				}
				
				$addSql = substr ( $addSql, 0, strlen ( $addSql ) -1 );
// 				echo $addSql;
// 				exit;
				if ( $sum < $class_count ){   //如果排的课程小于总课数才能排课 反之不能排课
					$model = spClass ( 'class_list' );
					$result = $model->runSql ( $addSql );
					$affectedRows = @$model->affectedRows ();
					if ($affectedRows) {
					$verify = new checkCtr ();
					$results = $verify->record ();
					$msg->ResponseMsg ( 0, '排课成功', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '排课失败', false, 0, $prefixJS );
				}
				}else{
				
					$update='update order_list set order_state=3 where tid='.$order_tid;
					$model = spClass ( 'order_list' );
					$result = $model->runSql ( $updateSql );
					$msg->ResponseMsg ( 1, '该学生已没有剩余课程', 0, 1, $prefixJS );
				}
				}else{
					
					$msg->ResponseMsg ( 1, '已经排课，无需重复排课！', 0, 0, $prefixJS );
						
				}
				}
				else{
				$msg->ResponseMsg ( 1, '添加失败，请确定订单ID或者时间', 0, 1, $prefixJS );
					}
			}else{
				$msg->ResponseMsg(1, '身份验证失败', 0, 1, $prefixJS);
			}
			}
#客服查询老师
		function queryAllTeacher()
		{
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			unset($newrow['page']);	
			$kf_token=$newrow['token'];
			$verify = new checkCtr();
			$result1 = $verify->VerifyAuth("kf_token",$token,"kf_user_info");
			$result2 = $verify->VerifyAuth("kf_admin_token",$token,"kf_admin_info");
			
			if($result1){
				$result = $result1;
			}else{
				$result = $result2;
			}
			if($result){
				if (!$newrow && $result['kf_city']==null)//如果查询内容为空 教研员的城市为空，则查询全国老师
				{
					$querySql = 'select tid,teacher_name,teacher_name_en,teacher_sex,teacher_age,teacher_area,student_age_max,student_age_min,teacher_seniority,teacher_image,student_grade_max,student_grade_min,teacher_city,teacher_district,teacher_town,telephone from teacher_info' ;
					$model = spClass ( 'teacher_info' );
					$result =@$model->spPager($this->spArgs('page',1), 10)->findSql($querySql);
					$pager = $model->spPager()->getPager();
					$total_page=$pager['total_page'];
					$msg->ResponseMsg ( 0, 'success', $result, $total_page, $prefixJS );
					return ;
				}
				if($newrow && $result['kf_city']==null)//如果查询内容不为空 教研员的城市为空，则按条件查询全国老师
				{
					$querySql = ' select tid,teacher_name,teacher_name_en,teacher_sex,teacher_age,teacher_area,student_age_max,student_age_min,teacher_seniority,teacher_image,student_grade_max,student_grade_min,teacher_city,teacher_district,teacher_town,telephone from teacher_info where ';
					foreach ( $newrow as $k => $v )
					{
						$querySql = $querySql . $k . '="' . $v . '" and ';
					}
					$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
				
					$model = spClass ( 'teacher_info' );
				
					if ($result = @$model->spPager($this->spArgs('page',1), 10)->findSql($querySql))
					{
						$pager = $model->spPager()->getPager();
						$total_page=$pager['total_page'];
// 						echo $total_page;
// 						exit;
						$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
					}
					else
					{
						$msg->ResponseMsg ( 1, '这个教师不存在！', $result, 0, $prefixJS );
						return;
					}
				}
				if (!$newrow && $result['kf_city']!=null){//如果查询内容为空 教研员的城市不为空，则查询与教研员桐城市的老师
					$querySql = 'select distinct t.tid,t.teacher_name,t.teacher_name_en,t.teacher_sex,t.teacher_age,t.teacher_area,t.student_age_max,t.student_age_min,t.teacher_seniority,t.teacher_image,
					t.student_grade_max,t.student_grade_min,t.teacher_city,t.teacher_district,t.teacher_town,t.telephone from kf_user_info k,teacher_info t
					where k.kf_city = t.teacher_city AND k.kf_city ="'.$result['kf_city'].'"';
// 					echo $querySql;
// 					exit;
					$model = spClass ( 'teacher_info' );
					$result=@$model->spPager($this->spArgs('page',1), 10)->findSql($querySql);
					$pager = $model->spPager()->getPager();
// 					print_r($pager);
// 					exit;
					$total_page=$pager['total_page'];
// 					echo $total_page;
// 					exit;
					$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
					return ;
				}
				if($newrow && $result['kf_city']!=null){//如果查询内容不为空 教研员的城市不为空，则按条件查询与教研员桐城市的老师
// 					$querySql = ' select tid,teacher_name,teacher_name_en,teacher_sex,teacher_age,teacher_area,student_age_max,student_age_min,teacher_seniority,teacher_image,student_grade_max,student_grade_min,teacher_city,teacher_district,teacher_town,telephone from teacher_info where ';
					$querySql = 'select distinct t.tid,t.teacher_name,t.teacher_name_en,t.teacher_sex,t.teacher_age,t.teacher_area,t.student_age_max,t.student_age_min,t.teacher_seniority,t.teacher_image,
					t.student_grade_max,t.student_grade_min,t.teacher_city,t.teacher_district,t.teacher_town,t.telephone from kf_user_info k,teacher_info t
					where  k.kf_city = t.teacher_city and '  		; //AND j.jy_city ="'.$result['jy_city'].'"';
					foreach ( $newrow as $k => $v )
					{
						$querySql = $querySql . $k . '="' . $v . '" and ';
					}
					$querySql = substr ( $querySql, 0, strlen ( $querySql ) - 5 );
					// 			 echo $querySql;
					// exit;
						
					$model = spClass ( 'teacher_info' );
				
					if ($result=@$model->spPager($this->spArgs('page',1), 10)->findSql($querySql))
					{
						$pager = $model->spPager()->getPager();
						$total_page=$pager['total_page'];
						$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
					}
					else
					{
						$msg->ResponseMsg ( 1, '这个教师不存在！', 1, 0, $prefixJS );
						return true;
					}
					
				}
			}else{
					$msg->ResponseMsg ( 1, '对不起，您没权限！', 1, 0, $prefixJS );
						
				}
			}
		#查询老师课程表
		 function queryCourse()
		{
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
			$kf_token=$newrow['token'];
			$verify = new checkCtr();
			$result1 = $verify->VerifyAuth("kf_token",$token,"kf_user_info");
			$result2 = $verify->VerifyAuth("kf_admin_token",$token,"kf_admin_info");
				if($result1){
					$result = $result1;
					$city = $result1['kf_city'];
				}else{
					$result = $result2;
					$city = $result2['kf_admin_city'];
				}
			$teacher_tid=$newrow['teacher_tid'];
			if($result){
				if($teacher_tid != null )
				{
					$querySql='select c.tid,o.pay_done ,o.order_type,u.user_name,o.create_time,
                o.class_content,c.class_start_date,c.class_start_time,o.order_address,c.tid
                from  teacher_info t, order_list o, class_list c,user_info u where
                   t.tid=o.teacher_tid and c.order_tid =o.tid and  o.user_tid=u.tid and
                 o.pay_done=1 and  t.tid='.$teacher_tid .' order by class_start_time,class_start_date';
					// 						echo $querySql;
					// 				exit;
					$model = spClass ( 'user_info' );
					$result = $model->findSql ( $querySql );
					// 				print_r($result);
					// 				exit;
					$msg->ResponseMsg(0, "查找成功", $result, 0, $prefixJS);
				}
				else{
					$msg->ResponseMsg(1, "查找失败", 0, 1, $prefixJS);
				}
				}else{
					$msg->ResponseMsg(1, "对不起您没有权限", 0, 1, $prefixJS);
						
				}
			}
			#拼课排课
			function arrangeClass(){
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
// 			$verify = new checkCtr ();
// 			$result = $verify->acl ();
// 			if($result){
				$querySql="select distinct o.teacher_tid,o.tid,o.pay_done,t.teacher_name,u.user_name,o.order_state,o.class_content,c.class_content,c.class_hour
						from order_list o, user_info u,teacher_info t,class_discount c
						where o.teacher_tid=t.tid and o.user_tid=u.tid and o.class_discount_tid=c.tid 
						and o.pay_done=1 and o.order_state=1";
				$model = spClass ( 'order_list' );
				$result = $model->findSql ( $querySql );
				$msg->ResponseMsg(0, "查询成功", $result, 1, $prefixJS);
				
// 			}else{
// 				$msg->ResponseMsg(1, "对不起您没有权限", 0, 1, $prefixJS);
				
// 			}
			}
			//查询精品课程
			function quality (){
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
// 				$verify = new checkCtr ();
// 				$result = $verify->acl ();
// 				if($result){
					$querySql="select distinct o.teacher_tid,o.tid,o.pay_done,t.teacher_name,u.user_name,o.order_state,h.high_quality_name,h.class_hour
						from order_list o, user_info u,teacher_info t,high_quality_courses h
						where o.teacher_tid=t.tid and o.user_tid=u.tid 
						and o.pay_done=1 and o.order_state=1 and o.high_quality_courses_tid=h.tid and t.teacher_name is not null";
					$model = spClass ( 'order_list' );
					$result = $model->findSql ( $querySql );
					$msg->ResponseMsg(0, "查询成功", $result, 1, $prefixJS);
			
// 				}else{
// 					$msg->ResponseMsg(1, "对不起您没有权限", 0, 1, $prefixJS);
			
// 				}
			}
			
		
	/*//教师满70节课后提出占用其他时间
	function occupyTime()

	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		$querySql="select  teacher_tid from teacher_schedule";
		$teacher_tid = $newrow['teacher_tid'];
		$model = spClass ( 'teacher_schedule' );
		$result = $model->findSql ( $querySql );
// 		echo $result;
// 		exit;
		if($newrow){
				$updateSql = "update teacher_schedule set time_busy = 2 ";
				$model = spClass('teacher_schedule');
				$result = $model->runSql($updateSql);
				$msg->ResponseMsg(0, "sucess", $result, 1, $prefixJS);
			
			//$updateSql = "update teacher_schedule set audit = 0 ";
			//$model = spClass($this->tablename);
			
			//$result = $model->runSql($updateSql);
			// 		echo $result;
			// 		exit;
			//$msg->ResponseMsg(0, "successful", $result, 1, $prefixJS);
			
		}
		
	
	}
	    #老师调休搜索界面
		function queryTeacherRest() 
		{
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
			$begin_date=$newrow['begin_date'];
			$end_date=$newrow['end_date'];
			$teacher_tid=$newrow['teacher_tid'];
// 			unset($newrow['teacher_tid']);
// 			unset($newrow['begin_date']);
// 			unset($newrow['end_date']);
			$teacher_entry_state=$newrow['teacher_entry_state'];
			$teacher_name=$newrow['teacher_name'];
// 			echo $teacher_entry_state;
// 			exit;
			if (!$newrow)
			{   $querySql="select i.teacher_name, r.create_time,s.teacher_entry_state,r.teacher_date,r.teacher_time from teacher_info i,teacher_rest r,teacher_schedule s where  i.tid=r.teacher_tid AND r.teacher_tid=s.teacher_tid  group by i.tid";
				$model=spClass('teacher_info');
				$result=$model->findSql($querySql);
				$msg->ResponseMsg(0, "success", $result, 1, $prefixJS);
			}
			
		if($teacher_tid)
		{  
			$querySql="select   i.tid,i.teacher_name, r.create_time,s.teacher_entry_state,r.teacher_date,r.teacher_time from teacher_info i,teacher_rest r,teacher_schedule s where i.tid=r.teacher_tid AND r.teacher_tid=s.teacher_tid AND s.teacher_tid=i.tid AND s.teacher_tid='".$teacher_tid."'"."group by s.teacher_tid";
// 			echo $querySql;
// 			exit;
			$model=spClass('teacher_info');
			$result=$model->findSql($querySql);
			$msg->ResponseMsg(0, "success", $result, 1, $prefixJS);
		}
			if($newrow ['begin_date'] && $newrow ['end_date'])
			{
				$querySql="select i.tid, i.teacher_name, r.create_time,s.teacher_entry_state,r.teacher_date,r.teacher_time from teacher_info i,teacher_rest r,teacher_schedule s where i.tid=r.teacher_tid AND r.create_time >='" . $begin_date . "'&& r.create_time <= '" . $end_date . "'" ."group by i.tid"  ;
// 				echo $querySql;
// 				exit;
				$model = spClass ( 'teacher_info' );
				$result = $model->findSql ( $querySql );
				$msg->ResponseMsg ( 0, 'sucess', $result, 0, $prefixJS );
			
				return true;
		}
			if(  $newrow['teacher_entry_state']==1 || $newrow['teacher_entry_state']==2)
			{
				$querySql="select i.tid, i.teacher_name, r.create_time,s.teacher_entry_state,r.teacher_date,r.teacher_time from teacher_info i,teacher_rest r,teacher_schedule s where i.tid=r.teacher_tid AND r.teacher_tid=s.teacher_tid AND s.teacher_entry_state='".$teacher_entry_state."'"."group by i.tid";
				$model = spClass ( 'teacher_schedule' );
				$result = $model->findSql ( $querySql );
				$msg->ResponseMsg ( 0, 'sucess', $result, 0, $prefixJS );
				return true;
			}
		   if($teacher_name)
		   	{
				$querySql="select  i.tid,i.teacher_name, r.create_time,s.teacher_entry_state,r.teacher_date,r.teacher_time from teacher_info i,teacher_rest r,teacher_schedule s where  i.tid=r.teacher_tid AND i.teacher_name='".$teacher_name."'"."group by i.tid";
// 				echo $querySql;
// 				exit;
		   		$model = spClass ( 'teacher_info' );
				$result = $model->findSql ( $querySql );
// 				print_r($result);
// 				exit;
		   	}

			if($teacher_name=$result['teacher_name'])
			{	
				$msg->ResponseMsg ( 0, 'sucess', $result, 0, $prefixJS );
			}
		
		else 
		{
				$msg->ResponseMsg ( 0, 'The teacher not found', $result, 0, $prefixJS );
		}
		return  true;
		}
		
		
		
		
		#老师调休操作
		function teacherRest()
		{
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
			$teacher_entry_state=$newrow['teacher_entry_state'];
			unset($newrow['teacher_entry_state']);
			$teacher_tid=$newrow['teacher_tid'];
			$confirm=$newrow['confirm'];
			unset($newrow['confirm']);
// 			$querySql = "select teacher_date,teacher_time from teacher_rest  where teacher_tid= ".$teacher_tid;
// 			$model=spClass('teacher_rest');
// 			$result = $model->findSql ( $querySql );
		if ($teacher_entry_state=1 && $newrow['teacher_tid'] && $confirm==0)
		{//同意调休
			
			$updateSql = "update teacher_schedule set time_busy = 2 AND teacher_entry_state = 2 where teacher_tid=".$teacher_tid;
			$model = spClass('teacher_schedule');
			$result = $model->runSql($updateSql);
// 			print_r($result);
// 			exit;
			$msg->ResponseMsg(0, "Ask for leave has been successful", $result, 1, $prefixJS);
		}else {//不同意调休
			$updateSql = "update teacher_schedule set  teacher_entry_state = 3";
			$model = spClass('teacher_schedule');
			$result = $model->runSql($updateSql);
			$msg->ResponseMsg(1, "Request paid leave failure", $result, 1, $prefixJS);
		}
	}
	*/
	
	#显示课程剩余数
			function showLessonNumber ()
			{
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
				//$date = $newrow['date'];
				$order_tid=$newrow['order_tid'];
				$class_count=$newrow['class_count'];
				$querySql ="select count(*) as count from class_list where order_tid=".$order_tid;
				$model = spClass ( 'class_list' );
				$result = $model->findSql ( $querySql );
				$sum=$class_count - $result[0]['count'];
// 				echo $sum;
// 				exit;
			if($sum<=0){
				$msg->ResponseMsg ( 0, '剩余课数为0', 0, 0, $prefixJS );
			}else{
				
				$msg->ResponseMsg ( 0, 'sucess', $sum, 0, $prefixJS );
			}
	}
		

		
		
	
		}
