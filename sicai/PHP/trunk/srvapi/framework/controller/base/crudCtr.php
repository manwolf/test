<?php
header ( "Content-type:application/json; charset=utf-8" );
include_once 'captureparam.php';
/**
 * 功能：对数据库的增删改查
 * 作者： 郑哥
 * 创建日期：2015年8月27日
 * 最新修改：2015年9月5日
 */
class crudCtr extends captureparam {
	public $queryFailedtipString = '未查询到符合条件数据';
	public $addFailedtipString = '添加失败';
	public $deleteFailedtipString = '删除失败';
	public $updateFailedtipString = '更新失败';
	public $tokenFailedtipString = '登录信息已过期[TOKEN]';
	public $tablename = '';
	public function __construct() {
		$this->tablename = '';
	}
	/**
	 * query
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	function query() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$newrow [1] = 1;
		$model = spClass ( $this->tablename );
		
		$result = $model->findAll ( $newrow);
		// $demo=$model->dumpSql();
		$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		
		return true;
	}
	/**
	 * add
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	public function add() {
		//echo 321;
// 		echo "add";
// 		exit;
		//echo('crudCtr->add');
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		// 验证请求参数
		if (! $this->checkAddParams ( $newrow, $token, $prefixJS )) {
			//echo('crudCtr->add->step 1');
			return;
		}
		$model = spClass ( $this->tablename );
		// /返回表的主键
// 		echo "add";
// 		exit;
		$result = $model->create($newrow);
		//echo('crudCtr->add->step 2');
		if ($result <= 0) {
			return;
		}
		$result = $model->findAll ( array (
				$model->pk => $result 
		) );
		
		if (count ( $result ) > 0) {
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, $this->addFailedtipString, array (), 1, $prefixJS );
		}
	}
	/**
	 * delete
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	function delete() {
		return true;
	}
	/**
	 * update
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	function update() {
		$msg = new responseMsg ();
		$capturs = $this->captureUpdateParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$condition = $capturs['condition'];
		$glabolCallback = $prefixJS;
		
	
		
		// 先检查是否传递了tid参数
		if (! $this->checkIdFiled ( $condition, $prefixJS )) {
			return;
		}
		// 验证请求参数
		if (! $this->checkUpdateParams ( $condition, $token, $prefixJS )) {
			return;
		}
		
		$model = spClass ( $this->tablename );
		@$result = $model->update ( $condition, $newrow );
		$result = $model->findAll ( $condition );
		$affectedrows = $model->affectedRows ();
		if (count ( $result ) > 0) {
			$msg->ResponseMsg ( 0, 'success', $result, 1, $prefixJS );
		} else {
			$msg->ResponseMsg ( 1, $this->updateFailedtipString, array (), 1, $prefixJS );
		}
		return true;
	}
	/**
	 * 验证修改或者删除是否提供主键tid
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	public function checkIdFiled($row, $prefixJS) {
		$msg = new responseMsg ();
		foreach ( $row as $k => $v )
			if ($k == 'tid') {
	
				return true;
			}
		$msg->ResponseMsg ( 1, '更新或者删除操作必须提供主键tid', array (), 0, $prefixJS );
		return false;
	}
	/**
	 * 验证操作员token是否正确
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	function verifyOperatorToken($tokenStr, $prefixJS) {
		return true;
	}
	/**
	 * 检查查询参数是否正确
	 * 作者：郑哥
	 * 修改人：
	 * 修改日期：2015年9月5日
	 */
	public function checkQueryParams($row, $token, $prefixJS) {
		return true;
	}
	public function checkAddParams($row, $token, $prefixJS) {
		return true;
	}
	public function checkUpdateParams($row, $token, $prefixJS) {
		return true;
	}
	public function checkDelParams($row, $token, $prefixJS) {
		return true;
	}
}
