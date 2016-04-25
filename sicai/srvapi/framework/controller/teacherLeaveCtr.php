<?php
include_once 'base/crudCtr.php';
class teacherLeaveCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_schedule';
	}	
	# 老师请假   未使用
	public function leave() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$audit = $newrow['audit'];
		$teahcer_tid =$newrow['teacher_tid'];
		$model = spClass ( $this->tablename );
		
		# 判断该教师是否上了70节课。若没有满70节课 ，则reurn false
		if($newrow){
			$querySql = "select count(o.tid) as count from teacher_info t, order_list o, class_list c
	 where  o.teacher_tid = t.tid and o.tid = c.order_tid and c.user_confirm =1 and t.tid =".$newrow['teacher_tid'];
			$querySql .= " and c.class_start_date >='".date('20y-m-01')."' and class_start_date <'".date('20y-').(date('m')+1).date('-01')."'";
			//echo $querySql;
			//exit;
			$result = $model->findSql ( $querySql );
	
			if( !($result[0]['count']*2 >=70) ){
				$msg->ResponseMsg ( 1, 'You less than section 70 class hours, can not leave.', false, 1, $prefixJS );
				return false;
			}
		}else {
			$msg->ResponseMsg ( 1, 'You have no input information.', false, 1, $prefixJS );
			return false;
		}    
		//echo true;
		//exit;
		# 根据输入的时间请假
		$start_year = substr ( $newrow['leave_start_date'], 0, 4 );
		$start_month = substr ( $newrow['leave_start_date'], 5, 2 );
		$end_month = substr ( $newrow['leave_end_date']."\n", 5, 2 );
		$start_day = substr ( $newrow['leave_start_date'], 8, 2 );
		$end_day = substr ( $newrow['leave_end_date'], 8, 2 );
		
		if ($start_month == date('m') && $end_month == date('m') && $start_year == date('20y') && $start_day >= date('d'))
		   {	
			$updateSql = "update teacher_schedule set audit= 1  where teacher_tid= ";
		   //$updateSql = "select * from teacher_schedule where teacher_tid = ";
// 		   	$updateSql = "update teacher_schedule set time_busy = 2 where teacher_tid = ";
 		    $updateSql = $updateSql.$newrow['teacher_tid']. ' and time_busy =0 and tid > 0 and ';
  			$updateSql .=  "((schedule_date = '".$newrow['leave_start_date']."' and schedule_time >= '".$newrow['leave_start_time']."')"; 
//  		 echo $updateSql;
//  		 exit;
 		 for($i = $start_day+1; $i< $end_day; $i++)
 		    {
 				$updateSql.= ' or '."( schedule_date = '".date('20y-m-').$i."')";
			}
  			$updateSql .=  " or (schedule_date = '".$newrow['leave_end_date']."' and schedule_time <= '".$newrow['leave_end_time']."'))";
//    			echo $updateSql;
//   			exit;
			$model = spClass ( 'teacher_schedule' );
// 			echo 11;
// 			exit;
			$result = $model->runSql($updateSql);
// 			echo $model;
// 			exit;
 			$msg->ResponseMsg ( 0, "Application has been submitted", $result, 1, $prefixJS );
 			
		   }
 		    else
 		        {
 			$msg->ResponseMsg ( 1, "The date you input errors.", false, 1, $prefixJS );
 		        }
 		
		   
	     }
}
	
	
 