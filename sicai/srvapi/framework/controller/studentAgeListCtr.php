<?php
include_once 'base/crudCtr.php';
class studentAgeListCtr extends crudCtr 
{
	public function __construct() 
	{
		$this->tablename = 'student_age_list';
	}
}