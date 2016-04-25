<?php
include_once 'base/crudCtr.php';
/**
 * 功能：统计下载量（苹果 或 安卓）
 * 作者： 陈鸿润
 * 创建日期：2015年8月27日
 * 最新修改：2015年8月31日
 */
class originTypeSataCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'origin_type';
	}
	# 子类对add实现空操作
	public function add(){
		exit();
	}
	# 子类对update实现空操作
	public function update(){
		exit();
	}
	/**
	 * 返回各设备下载情况
	 */
	public function showDeviceNum(){			
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$prefixJS = $capturs ['callback'];
			$model = spClass($this->tablename);
			$result = $model->find(array($model->pk => 1)); 
			unset($result[$model->pk]);
			$msg->ResponseMsg ( 0, '查询成功', $result, 1, $prefixJS );
	}
	/**
	 * 判断下载来源系统类型
	 */
	public function getDeviceType(){
		# 获取HTTP_USER_AGENT
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		# 设置type的初始值为other
		$type = 'other';
		if(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
			$type = 'ios';
		}
		if(strpos($agent, 'android')){
			$type = 'android';
		}
		if(strpos($agent, 'windows')){
			$type = 'windows';
		}
		if(strpos($agent, 'macintosh')){
			$type = 'macintosh';
		}
		return $type;
	}
	/**
	 * 更新下载量(下载量加1)
	 */
	public function download(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$prefixJS = $capturs ['callback'];
		# 查询origin_type表中，对应设备的下载量
		$model = spClass($this->tablename);
		$result = $model->find(array($model->pk => 1)); // 查询andriod表中基数
		$condition = array($model->pk => $result[$model->pk]); // 设置修改tid
		# 设置message默认值
		$message = "增加其他设备下载量的下载次数成功";
		unset($result[$model->pk]);
		# 调用getDeviceType，判断客户端来源类型，并增加下载量
		switch($this->getDeviceType()){
			case 'ios':		// 客户端设备类型为iPhone或iPad;				
				$result['ios']++; // 增加ios下载量
				$result = $model->update($condition,$result);
				break;
			case 'android':		// 客户端设备类型为安卓手机或安卓平板电脑;				
				$result['android']++; // 增加android下载量
				$result = $model->update($condition,$result);
				break;
			case 'windows':		// 客户端设备系统为window系列;				
				$result['windows']++; // 增加windows下载量
				$result = $model->update($condition,$result);
				break;
			case 'macintosh' :	// 客户端设备类型为Mac机;
				$result['macintosh']++; // 增加macintosh下载数
				$result = $model->update($condition,$result);
				break;
			default :			// 客户端设备类型为其它;				
				$result['others']++; // 增加others下载数
				$result = $model->update($condition,$result);
				break;
		}
		$msg->ResponseMsg ( 0, $message, $result, 1, $prefixJS );
	}
	# 重置下载量
// 	public function resetDownStat(){
// 		$model = spClass($this->tablename);
// 		$msg = new responseMsg ();
// 		$prefixJS = 'callback';
// 		$condition = array(tid => 1);
// 		$result = array(
// 					'ios' => 0,
// 					'android' => 0,
// 					'windows' => 0,
// 					'macintosh' => 0
// 				);
// 		$model->update($condition,$result);
// 		//echo "重置成功";
// 		$msg->ResponseMsg ( 0, 'Reset successful', $result, 1, $prefixJS );
// 	}
}