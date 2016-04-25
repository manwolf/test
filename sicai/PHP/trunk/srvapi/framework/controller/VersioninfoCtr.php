<?php
include_once 'base/crudCtr.php';
/**
 * 功能：录入和查询框架、zip包的版本信息
 * 作者： 陈梦帆
 * 日期：2015年9月6日
 */
class VersioninfoCtr extends crudCtr {
	public function __construct() {
		$this->tablename = 'container_info';
	}
	
	/**
	 * 新增容器版本信息
	 */
	function addContainer() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		// 容器类型，是student、teacher、OA
		$type = $newrow ['type'];
		// 容器可应用平台,H表示安卓平台(0不可用,1可用),G表示苹果平台(0不可用,1可用）
		$container_platform = $newrow ['container_platform'];
		// 容器的名字
		$container_name = $newrow ['container_name'];
		// 容器包的文件名后缀
		$container_filetype = $newrow ['container_filetype'];
		// 容器的版本号
		$container_version = $newrow ['container_version'];
		// 容器的详细描述
		$container_detaile = $newrow ['container_detaile'];
		// 匹配type
		$typeMatchArray = array (
				"student",
				"teacher",
				"OA" 
		);
		// 若容器的基本信息存在空，则返回“您的输入有误！请正确填写完整信息！”
		if ($container_platform == null or $container_name == null or $container_filetype == null or $container_version == null or $container_detaile == null) {
			$msg->ResponseMsg ( 1, '您的输入有误！请正确填写完整信息！', false, 0, $callback );
			exit ();
		}
		
		// 根据type确定存放地址
		switch ($type) {
			case "zip" : // 上传的是小应用
				$lastposition = 'base';
				break;
			default : // 上传的是容器
				$lastposition = 'eteacher';
				break;
		}
		// 根据zip_platform、type确定存放地址
		switch ($container_platform) {
			case 1 : // 安卓
				$platformString = "Android";
				break;
			case 2 : // 苹果
				$platformString = "IOS";
				break;
		}
		$container_position = '/framework/'.$platformString . '/'.$type . '/'.$lastposition.'/';
		
		
		
		// 根据container_platform、type确定存放地址
		switch ($container_platform) {
			case 1 : // 安卓
				$platformString = "Android";
				break;
			case 2 : // 苹果
				$platformString = "IOS";
				break;
		}
		
		
		
		$container_position = '/framework/'.$platformString . '/'.$type . '/eteacher/';
		// 检查是否已有重复的容器名称、文件类型和版本号
		$conditions = ' SELECT tid,container_version FROM container_info
				WHERE container_name = "' . $container_name . '"
					AND container_platform = "' . $container_platform . '"	
					AND container_version >= "' . $container_version . '"
				ORDER BY container_version DESC';
		$model = spClass ( 'container_info' );
		$result = $model->findsql ( $conditions );
		if ($result) {
			$msg->ResponseMsg ( 1, '新增容器' . $container_name . '的版本已存在' . $result ['0'] ['container_version'], false, 0, $callback );
			exit ();
		}
		$new = array (
				'container_name' => $container_name,
				'container_filetype' => $container_filetype,
				'container_version' => $container_version,
				'container_platform' => $container_platform,
				'container_detaile' => $container_detaile,
				'container_position' => $container_position				 
		);
		// 新增容器的版本信息
		$result = $model->create ( $new );
		if ($result) {
			$msg->ResponseMsg ( 0, '添加容器成功', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, '添加容器失败! ', false, 0, $callback );
			exit ();
		}
	}
	
	/**
	 * 新增指定容器下的小应用的版本信息
	 */
	function addSmallApp() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		// 纠正输入的值为undefined未定义的参数
		foreach ( $newrow as $key => $value ) {
			if ($value == "undefined") {
				$newrow [$key] = "";
			}
		}
		// 容器类型，是student、teacher、OA
		$type = $newrow ['type'];
		// 小应用的名字
		$zip_name = $newrow ['zip_name'];
		// 小应用包的文件名后缀
		$zip_filetype = $newrow ['zip_filetype'];
		// 小应用的版本号
		$zip_version = $newrow ['zip_version'];
		// 小应用的详细描述
		$zip_detaile = $newrow ['zip_detaile'];
		// 小应用依赖的容器tid
		$container_tid = ( integer ) $newrow ['container_tid'];
		// 若新增的小应用的基本信息存在空，则返回“您的输入有误！请正确填写完整信息！”
		if ($zip_name == null or $zip_filetype == null or $zip_version == null or $zip_detaile == null or $container_tid <= 0) {
			$msg->ResponseMsg ( 1, '您的输入有误！请正确填写完整信息！', false, 0, $callback );
			exit ();
		}
		
		// 检查该容器是否存在
		$model = spClass ( 'container_info' );
		$result = $model->findBy ( 'tid', $container_tid );
		if (! $result) {
			$msg->ResponseMsg ( 1, '指定的容器不存在', false, 0, $callback );
			exit ();
		}
		// 小应用的应用平台
		$zip_platform = $result['container_platform'];
		// 根据type确定存放地址
		switch ($type) {
			case "zip" : // 上传的是小应用
				$lastposition = 'base';
				break;
			default : // 上传的是容器
				$lastposition = 'eteacher';
				break;
		}
		// 根据zip_platform、type确定存放地址
		switch ($zip_platform) {
			case 1 : // 安卓
				$platformString = "Android";
				break;
			case 2 : // 苹果
				$platformString = "IOS";
				break;
		}
		$zip_position = '/framework/'.$platformString . '/'.$type . '/'.$lastposition.'/';		
		
		// 检查是否已有重复的容器名称、文件类型和版本号
		$conditions = ' SELECT tid,zip_version FROM zip_info
				WHERE zip_name = "' . $zip_name . '"
					AND zip_platform = "' . $zip_platform . '"
					AND zip_version >= "' . $zip_version . '"
					AND container_tid = "' . $container_tid . '"
				ORDER BY zip_version DESC';
		$model = spClass ( 'zip_info' );
		$result = $model->findsql ( $conditions );
		if ($result) {
			$msg->ResponseMsg ( 1, '新增小应用' . $container_name . '的版本已存在' . $result ['0'] ['container_version'], false, 0, $callback );
			exit ();
		}
		$new = array ( // 新增小应用的数组
				'zip_name' => $zip_name,
				'zip_filetype' => $zip_filetype,
				'zip_version' => $zip_version,
				'zip_platform' => $zip_platform,
				'zip_detaile' => $zip_detaile,
				'zip_position' => $zip_position,
				'container_tid' => $container_tid				
		);
		// 新增小应用的版本信息
		$result = $model->create ( $new );
		if ($result) {
			$msg->ResponseMsg ( 0, '添加小应用成功! ', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, '添加小应用失败! ', false, 0, $callback );
			exit ();
		}
	}
	/**
	 * 根据应用平台、APP端口、版本信息模糊搜索查询符合条件的容器版本的相关信息
	 */
	function queryALLContainer() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$container_name = $newrow ['container_name'];
		$container_version = $newrow ['container_version'];
		$container_platform = $newrow ['container_platform'];
		
		if (defined ( 'TestVersion' )) { // 测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else { // 正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
		
		// 查询所有的容器信息
		$querySql = 'SELECT tid,
				concat(container_name,"V",container_version,".",container_filetype) AS container_name,
				container_detaile,	
				concat("' . $domain_name . '",container_position,container_name,"V",container_version,".",container_filetype) AS url,					
				case container_platform
					when 1 then "安卓"
					when 2 then "苹果"
					when 3 then "共用"
					ELSE "无"
				end as 	container_platform,
				create_time FROM container_info
				where tid > 0 ';
		// $container_platform “2”表示手机类型为苹果时 ,“1”表示手机类型为安卓时
		if ($container_platform != null) {
			$querySql .= ' AND  container_platform = "'.$container_platform .'" ';
		}
		// 按容器名字搜索
		if ($container_name != null) {
			$querySql .= ' AND container_name  LIKE "%' . $container_name . '%"';
		}
		// 按版本号搜索
		if ($container_version != null) {
			$querySql .= ' AND container_version  LIKE "%' . $container_version . '%"';
		}
		$querySql .= ' ORDER BY create_time DESC';
		$model = spClass ( $this->tablename );
		$result = @$model->findSql ( $querySql );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $callback );
		} else {
			$msg->ResponseMsg ( 1, '容器不存在 !', false, $total_page, $callback );
			exit ();
		}
	}
	/**
	 * 根据版本号查询符合条件的小应用版本的相关信息
	 */
	function querySmallApp() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$zip_version = $newrow ['zip_version'];
		$container_tid = $newrow ['container_tid'];
		if (defined ( 'TestVersion' )) { // 测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else { // 正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
	
		// 查询所有的小应用信息
		$querySql = 'SELECT tid,
				concat(zip_name,"V",zip_version,".",zip_filetype) AS zip_name,
				zip_detaile,
				concat("' . $domain_name . '",zip_position,zip_name,"V",zip_version,".",zip_filetype) AS url,
				case zip_platform
					when 1 then "安卓"
					when 2 then "苹果"
					when 3 then "共用"
					ELSE "无"
				end as 	zip_platform,
				create_time FROM zip_info
				where container_tid = "'.$container_tid.'"';		
		// 按版本号搜索
		if ($zip_version != null) {
			$querySql .= ' AND zip_version  LIKE "%' . $zip_version . '%"';
		}
		$querySql .= ' ORDER BY create_time DESC';
		$model = spClass ( $this->tablename );
		$result = @$model->findSql ( $querySql );
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $callback );
		} else {
			$msg->ResponseMsg ( 1, '小应用不存在 !', false, $total_page, $callback );
			exit ();
		}
	}
	
	/**
	 * END
	 */
}
				

