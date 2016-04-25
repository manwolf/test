<?php
include_once 'base/crudCtr.php';
/**
 * 功能：获取各城市折扣信息
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class cityDiscountCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'class_discount';
	}
	// 根据城市查询折扣率和次数
	function query() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		if (! $newrow){ // 若$newrow为空 返回false
               $msg->ResponseMsg ( 1, '您没有输入任何内容！', $result, 0, $prefixJS );
               exit;
		} else {
			// 根据城市获取折扣信息
			$Sql = "select * from class_discount where ";
			foreach ( $newrow as $k => $v ) {
				if ($k == 'class_year_month') {
					continue;
				}
				$Sql = $Sql . $k . '="' . $v . '" and ';
			}
			$Sql = substr ( $Sql, 0, strlen ( $Sql ) - 5 );
			$model = spClass ( $this->tablename );
			// echo $Sql;
			// exit;
			if ($result = $model->findSql ( $Sql )) {
				$msg->ResponseMsg ( 0, 'Success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 1, 'not found', $result, 0, $prefixJS );
			}
		}
		return true;
	}
	//禁止以下action实例化基类
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
