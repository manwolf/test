<?php
include_once 'base/crudCtr.php';
/**
 * 功能：教师查询/操作订单
 * 作者： 黄东
 * 日期：2015年8月31日
 */
class teacherConfirmation extends crudCtr {
	public function __construct() {
		$this->tablename = 'teacher_info';
	}
	// 教师查询订单
	function queryOrderInfo() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		
		$tid = $newrow ['tid'];
		if (! $newrow) {
			
			$msg->ResponseMsg ( 1, 'fail', $result, 0, $prefixJS );
			exit;
		} else {
			// 教师查询所有订单
			$querySql = 'select b.pay_done,b.create_time,d.class_count*2 as class_count,c.teacher_name,a.user_name,b.order_type,b.class_content,b.order_date,b.order_time,b.order_address,b.tid as order_tid
                         from user_info a,order_list b,teacher_info c,class_discount d
                         where c.tid=b.teacher_tid and a.tid=b.user_tid and b.class_discount_tid=d.tid and b.order_type !=2  and c.tid=' . $tid . '  group by b.tid'; //
			$model = spClass ( $this->tablename );
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, '亲爱的，您还没有订单', $result, 0, $prefixJS );
			}
		}
		
		return true;
	}
	
	// 教师课程记录
	function classRecordInfo() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$model = spClass ( $this->tablename );
		$tid = $newrow ['tid'];
		if (! $newrow) {
			
			$msg->ResponseMsg ( 0, 'fail', $result, 0, $prefixJS );
			exit;
		} else {
			// 教师查看订单详情
			$querySql = 'select c.user_evaluation_state,c.tid,a.teacher_name,b.user_name,d.class_content,c.class_no,c.class_start_date,c.class_start_time,d.order_type,c.teacher_confirm,c.user_confirm,c.teacher_grammar
                         from teacher_info a,user_info b,class_list c,order_list d 
                         where a.tid=d.teacher_tid and b.tid=d.user_tid and d.tid=c.order_tid and d.tid=' . $tid;
			
			if ($result = $model->findSql ( $querySql )) {
				$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
			} else {
				$msg->ResponseMsg ( 0, '没有发现该教师', null, 0, $prefixJS );
			}
		}
		
		return true;
	}
	
	// 教师确认订单 修改
	function addOrderInfoCtr() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$tid = $newrow ['tid'];
		if ($tid =='') {
			$msg->ResponseMsg ( 0, '请输入课程id', null, 0, $prefixJS );
			exit;
		} else {
			// 教师确认后修改课程状态
			$addSql = 'update class_list set teacher_confirm=1 where tid=' . $tid;
			$model = spClass ( $this->tablename );
			$result = $model->runSql ( $addSql );
			if ($result <= 0) {
				return;
			}
			$msg->ResponseMsg ( 0, 'success', $result, 0, $prefixJS );
		}
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
