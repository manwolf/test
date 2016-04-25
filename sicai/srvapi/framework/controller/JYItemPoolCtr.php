<?php
include_once 'base/checkCtr.php';
include_once 'base/crudCtr.php';
class JYItemPoolCtr extends crudCtr {
	/**
	 * 功能：创建题库，查询题库
	 * 作者： 李坡
	 * 日期：2015年9月17日
	 */
	//创建题库
		function createItemPool() {
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$token = $capturs ['token'];
			$newrow = $capturs ['newrow'];
			$verify = new checkCtr ();
			$result = $verify->acl ();
			if ($result) {
				$city=$result[0]['city'];//获取城市与题库城市匹配，城市不一样不能操作
				if($city==$newrow['test_city'] || $city==全国){
				if(!$newrow['test_semester'] && !$newrow['test_city'] && !$newrow['teaching_material_type']){
					$msg->ResponseMsg ( 1, '填写的信息为空！', false, 0, $prefixJS );
					return ;	
				}
				$querySql='select tid from test_question_types where test_class="'.$newrow['test_class'].'"'.' and test_grade="'.$newrow['test_grade'].'"'.' and test_semester="'.$newrow['test_semester'].'"'.'and test_city="'.$newrow['test_city'].'"'  ;
				$model=spClass('test_question_types');
				$results=$model->findSql($querySql);
				if($results[0]['tid']!=null){
					$msg->ResponseMsg ( 1, '您所添加的题库已存在', false, 0, $prefixJS );
					return ;	
				}
				$model = spClass ( 'test_question_types' );
				$result=$model->create($newrow);
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '添加成功！', $result, 0, $prefixJS );
			}else{
				$msg->ResponseMsg ( 1, '您不能操作非本城市的题库！', false, 0, $prefixJS );
			}
			}else{
				$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
			}
		}
			//查询年级
			function queryClass() {
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
				$verify = new checkCtr ();
				$result = $verify->acl ();
				if ($result) {
					$city=$result[0]['city'];
					if($city==全国 ){//城市为全国提示选择城市
						$msg->ResponseMsg ( 1, '您是全国权限，请选择要操作城市！', false, 0, $prefixJS );
						return ;
					}
					
				$querySql='select concat(class_grade_category,user_class) AS user_class  from class_price 
						where class_city="'.$newrow['city'].'" AND class_grade != 0 ';
				$model=spClass('class_price');
				$result=$model->findSql($querySql);
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功！', $result, 0, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
				}
			}
			//查询题库
			function queryItemPool() {
				$msg = new responseMsg ();
				$capturs = $this->captureParams ();
				$prefixJS = $capturs ['callback'];
				$token = $capturs ['token'];
				$newrow = $capturs ['newrow'];
				$verify = new checkCtr ();
				$result = $verify->acl ();
				if ($result) {
					$city=$result[0]['city'];
					$page = $newrow ['page'];
					if ($page == '' || $page == null || $page <= 0) {
						$page = 1;
					}
					unset ( $newrow ['page'] );
					unset ( $newrow ['user_tid'] );
				$querySql='select tid ,concat(test_grade,test_class)as class ,test_semester,teaching_material_type,test_title_number,test_city from test_question_types'; 
				if($city==全国 ){
					$querySql;
				}
				if($city!=全国){
					$querySql.=' where test_city="'.$city.'"';
				}
				if($city!=全国 && $newrow['city']!=null){
					$querySql.=' where test_city="'.$newrow['city'].'"';
				}
				$model=spClass('test_question_types');
				$result = @$model->spPager ( $this->spArgs ( 1, $page ), 10 )->findSql ( $querySql );
				$pager = $model->spPager ()->getPager ();
				$total_page = $pager ['total_page'];
				$results = $verify->record ();
				$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $prefixJS );
				}else{
					$msg->ResponseMsg ( 1, '对不起，您没有权限！', false, 0, $prefixJS );
				}
			}
		}