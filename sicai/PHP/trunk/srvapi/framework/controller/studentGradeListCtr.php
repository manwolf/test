<?php
include_once 'base/crudCtr.php';
class studentGradeListCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'class_grade_list';
		
	}
	public function queryGrade()
	{
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		$city=$newrow['class_city'];
		$class_grade_category=$newrow['class_grade_category'];
		if (!$newrow){
			$querySql='select * from class_grade_list';
		}else{
			$querySql='select class_grade_item from class_grade_list where class_city='.$city. ' and '.'class_grade_category='.$class_grade_category;
// 			echo $querySql;
// 			exit;
			$model = spClass ( $this->tablename );
			$result = $model->findSql ( $querySql );
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		}
		return  true;
	}
}	
		
