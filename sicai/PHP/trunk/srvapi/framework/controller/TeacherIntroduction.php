<?php
include_once 'base/crudCtr.php';
/**
 * 功能：添加教师简介及生活照
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class TeacherIntroduction extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_detail_info';
	}
	// 新增教师简介
	public function addIntroduction() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$teacher_tid = $newrow ['teacher_tid'];
		$teacher_intro = $newrow ['teacher_intro'];
		$t_image_url_1 = $newrow ['t_image_url_1'];
		$t_image_url_2 = $newrow ['t_image_url_2'];
		$teacher_information_title_1 = $newrow ['teacher_information_title_1'];
		$teacher_information_1 = $newrow ['teacher_information_1'];
		$teacher_information_title_2 = $newrow ['teacher_information_title_2'];
		$teacher_information_2 = $newrow ['teacher_information_2'];
		$teacher_information_title_3 = $newrow ['teacher_information_title_3'];
		$teacher_information_3 = $newrow ['teacher_information_3'];
// 		$teacher_information_title_4 = $newrow ['teacher_information_title_4'];
		$teacher_idea = $newrow ['teacher_idea'];
		// 查询教师电话号
		$querySql = 'select telephone from teacher_info where tid=' . $teacher_tid;
		$model = spClass ( $this->tablename );
		$resultqu = $model->findSql ( $querySql );
		$telephone = $resultqu [0] ['telephone'];
		// echo $telephone;
		// 判断服务器运行环境
		if (defined ( 'TestVersion' )) {// 测试

			$t_image_url_1 ="http://testapi.e-teacher.cn/testfile/Teacher_detail_image/" . $telephone . "_1.png";
			$t_image_url_2 ="http://testapi.e-teacher.cn/testfile/Teacher_detail_image/" . $telephone . "_2.png";
		} else {// 正式

			$t_image_url_1 ="http://image.e-teacher.cn/teacher_detail_image/" . $telephone . "_1.png";
			$t_image_url_2 ="http://image.e-teacher.cn/teacher_detail_image/" . $telephone . "_2.png";
		}
		
		// 添加教师简介及生活照
		$addSql ='insert teacher_detail_info set teacher_intro="' . $teacher_intro . '",teacher_tid=' . $teacher_tid . ',t_image_url_1="' . $t_image_url_1 . '",t_image_url_2="' . $t_image_url_2 . '",teacher_information_title_1="' . $teacher_information_title_1 . '",teacher_information_1="' . $teacher_information_1 . '",teacher_information_title_2="' . $teacher_information_title_2 . '",teacher_information_2="' . $teacher_information_2 . '",teacher_information_title_3="' . $teacher_information_title_3 . '",teacher_information_3="' . $teacher_information_3 . '",teacher_idea="'.$teacher_idea.'"';
		
		$model = spClass ( $this->tablename );
		$result = $model->runSql ( $addSql );
		if ($result <= 0) {
			return;
		}
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		// }
		return true;
	}
	//禁止以下action实例化基类
	function query() {
		return false;
	}
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