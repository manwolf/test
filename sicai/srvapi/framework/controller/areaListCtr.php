<?php
include_once 'base/crudCtr.php';
/**
 * 功能：获取城市列表信息
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class areaListCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'area_list';
	}
	// 获取城市列表
	function queryCity() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		// $newrow [1] = 1;
		$model = spClass ( $this->tablename );
		
		// $result = $model->findAll ( $newrow);
		if ($newrow ['area_city']) {
			// 查询城市列表 和 城市所对应图片
			$querySql = 'select area_city,urlone,urltwo,urlthree 
					from adimg_info a,area_list b 
					where a.city=b.area_city 
					and b.area_city="' . $newrow ['area_city'] . '" 
							group by area_city';
			// $demo=$model->dumpSql();
		} else {
			$querySql = 'select distinct area_city from area_list';
		}
		$result = $model->findSql ( $querySql );
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		
		return true;
	}
	
	// 获取城市列表
	function queryCityNoCallback() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$newrow [1] = 1;
		$model = spClass ( $this->tablename );
		
		// $result = $model->findAll ( $newrow);
		if ($newrow ['area_city']) {
			// 查询城市列表
			$querySql = 'select area_city,urlone,urltwo,urlthree from adimg_info a,area_list b
					where a.city=b.area_city and b.area_city="' . $newrow ['area_city'] . '" group by area_city';
			// $demo=$model->dumpSql();
		} else {
			$querySql = 'select distinct area_city from area_list';
		}
		$result = $model->findSql ( $querySql );
		$msg->ResponseMsg ( 0, 'success', $result, 0, "" );
		
		return true;
	}
	// 获取地区列表
	function queryDistrict() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $area_city = $capturs ['area_city'];
		$newrow = $capturs ['newrow'];
		$newrow [1] = 1;
		$model = spClass ( $this->tablename );
		
		// $result = $model->findAll ( $newrow);
		$querySql = "select distinct area_district  from area_list  where area_city='" . $newrow ['area_city'] . "'";
		// echo $querySql;
		$result = $model->findSql ( $querySql );
		// $demo=$model->dumpSql();
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		
		return true;
	}
	// 获取街道列表
	function queryTown() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		// $token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$newrow [1] = 1;
		$model = spClass ( $this->tablename );
		
		// $result = $model->findAll ( $newrow);
		// echo $querySql;
		$querySql = "select distinct area_town  from area_list  where area_city='" . $newrow ['area_city'] . "' and area_district ='" . $newrow ['area_district'] . "'";
		$result = $model->findSql ( $querySql );
		// $demo=$model->dumpSql();
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		
		return true;
	}
	//禁止下列action实例化基类
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