<?php
include_once 'base/crudCtr.php';
/**
 * 功能：教案上传 教师确认  状态：未使用
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class teacherEvaluation extends crudCtr {
	public function __construct() {
		$this->tablename = 'class_list';
		
	}
	//教师教案上传  输入上课开始和结束时间     貌似未使用--待确认
	function addTeacherGrammar()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// 		$tid = $newrow['tid'];
		if(!$newrow)
		{
			$msg->ResponseMsg ( 1, 'add is fail', null, 0, $prefixJS );
		}
		else
		{
			$addSql='insert class_list set ';
			foreach ($newrow  as  $k=>$v)
			{
// 				if(("class_start_time"==$k))
// 				{
// 					$v=$v.':00';
// 				}
				$addSql=$addSql.$k.'="'.$v.'",';
					
			}
			$addSql = substr($addSql,0,strlen($addSql)-1);
			//  		  echo $addSql;
			//  		  exit;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ($addSql);
			if ($result <= 0) {
				return;
			}
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		
		}
		return true;
	}
	//教师确认 上课完成  
	function updateTeacherClass()
	{
	$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		//$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		//	$updateSql='update  teacher_info set ';
		 
		$tid = $newrow['tid'];
		if(!$tid==null)
		{
			$updateSql='update class_list set teacher_confirm=1 where tid='.$tid;			
			$model = spClass ( $this->tablename );
			
			//      		  echo $updateSql;
			//       		  exit;
			$result = $model->runSql ($updateSql);
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );			 
		}
		else
		{
			$msg->ResponseMsg ( 1, '你必须提供一个主键tid', $result, 0, $prefixJS );
		}
		
		return true;
	}
}