<?php
include_once 'base/crudCtr.php';
/**
 * 功能：移动框架的应用下载接口
 * 作者： 孙广兢
 * 日期：2015年8月27日
 */
class DownloadCtr extends crudCtr {
	
	/**
	 * 根据应用平台、城市、容器名字,查询最新版本的容器以及该容器下所有小应用的相关信息(包括下载URL)
	 */
	function queryNewALLUrl() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$token = $capturs ['token'];
		$newrow = $capturs ['newrow'];
		$container_name = $newrow ['container_name'];
		$city = $newrow ['city'];
		$platform = $newrow ['platform'];
		if (defined ( 'TestVersion' )) { // 测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else { // 正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
		if ($container_name == null or $platform == null) {
			$msg->ResponseMsg ( 1, ' 容器与应用平台不能为空!', false, 0, $callback );
			exit ();
		}
		if ($city == null) {
			$city = "";
		}
		//查询最新版本的容器信息
		$querySql = 'SELECT tid,
				container_name AS name,
				container_version AS version,
				"' . $city . '" AS city,
				container_detaile AS detaile,
				CONCAT(container_name,"V",container_version,".",container_filetype) AS package_name,	
				CONCAT("' . $domain_name . '",container_position,container_name,"V",container_version,".",container_filetype) AS url,					
				container_force AS isforce,
				container_platform AS platform
				FROM container_info
				WHERE container_name LIKE "%' . $container_name . '%" AND container_platform & ' . $platform . ' = ' . $platform . ' ORDER BY container_version DESC LIMIT 1';
		$model = spClass ( 'container_info' );
		$container_info = $model->findSql ( $querySql );
		//查询最新版本的小应用信息
		if ($container_info) {
			$querySql = 'SELECT tid,
					zip_name AS name,
					MAX(zip_version) AS version,
					zip_city AS city,
					zip_detaile AS detaile,
					CONCAT(zip_name,"' . $city . '","V",MAX(zip_version),".",zip_filetype) AS package_name,				
					CONCAT("' . $domain_name . '",zip_position,zip_name,"' . $city . '","V",MAX(zip_version),".",zip_filetype) AS url,
					zip_force AS isforce,
					zip_platform AS platform						
				FROM zip_info
				WHERE  container_tid =  "' . $container_info ['0'] ['tid'] . '" 
						AND zip_platform & ' . $platform . ' = ' . $platform;
			if ($city != "") {
				$querySql .= ' AND zip_city = "' . $city . '"';
			}
			
			$querySql .= ' GROUP BY zip_name ';
			$model = spClass ( 'zip_info' );
			$result = $model->findSql ( $querySql );
			
			if ($result) {
				$result = array_merge ( $container_info, $result );
				$msg->ResponseMsg ( 0, '成功', $result, 0, $callback );
			} else {
				$msg->ResponseMsg ( 0, '成功，但容器 ' . $container_name . '下没有小应用可供下载!', $container_info, 0, $callback );
				exit ();
			}
		} else {
			$msg->ResponseMsg ( 1, '容器 ' . $container_name . ' 不存在 !', false, 0, $callback );
			exit ();
		}
	}
	
	/**
	 * 新增容器
	 */
	function addContainer() {
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
		// 过滤输入信息
		$type = $newrow ['type']; // 容器类型，是student、teacher、OA
		$container_platform = ( integer ) $newrow ['container_platform']; // 容器的应用平台
		$container_name = $newrow ['container_name']; // 容器的名字
		$container_filetype = $newrow ['container_filetype']; // 容器包的文件名后缀
		$container_version = $newrow ['container_version']; // 容器的版本号
		$container_detaile = $newrow ['container_detaile']; // 容器的详细描述
		$container_force = $newrow ['container_force']; // 容器是否强制更新，默认为1（强制）
		                                                 // 判断输入信息的有效性
		$typeMatchArray = array (
				"student",
				"teacher",
				"OA" 
		); // 匹配type
		$forceMatchArray = array (
				"0",
				"1" 
		); // 匹配container_force
		if (in_array ( $type, $typeMatchArray ) == false or $container_platform <= 0 or $container_name == null or $container_filetype == null or $container_version == null or $container_detaile == null or in_array ( $container_force, $forceMatchArray ) == false) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		// 根据type确定存放地址
		$container_position = '/framework/' . $type . '/eteacher/';
		// 检查是否已有重复的容器名称、文件类型和版本号
		$conditions = ' SELECT tid,container_version FROM container_info 
				WHERE container_name = "' . $container_name . '"
					AND container_version >= "' . $container_version . '" 
				ORDER BY container_version DESC';
		$model = spClass ( 'container_info' );
		$result = $model->findsql ( $conditions );
		if ($result) {
			$msg->ResponseMsg ( 1, '新增容器' . $container_name . '的版本应高于已有版本号V' . $result ['0'] ['container_version'], false, 0, $callback );
			exit ();
		}
		$new = array ( // PHP的数组
				'container_name' => $container_name,
				'container_filetype' => $container_filetype,
				'container_version' => $container_version,
				'container_platform' => $container_platform,
				'container_detaile' => $container_detaile,
				'container_force' => $container_force,
				'container_position' => $container_position 
		);
		$result = $model->create ( $new ); // 进行新增操作
		if ($result) {
			$msg->ResponseMsg ( 0, '添加容器成功', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, '添加容器失败! ', false, 0, $callback );
			exit ();
		}
	}
	
	/**
	 * 新增指定容器下的小应用
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
		// 过滤输入信息
		$type = $newrow ['type']; // 小应用类型，是student、teacher、OA
		$zip_platform = ( integer ) $newrow ['zip_platform']; // 小应用的应用平台
		$zip_name = $newrow ['zip_name']; // 小应用的名字
		$zip_filetype = $newrow ['zip_filetype']; // 小应用包的文件名后缀
		$zip_version = $newrow ['zip_version']; // 小应用的版本号
		$zip_detaile = $newrow ['zip_detaile']; // 小应用的详细描述
		$zip_force = $newrow ['zip_force']; // 小应用是否强制更新，默认为1（强制）
		                                    // 由于目前客户端要求小应用zip包没有城市属性，因此强制将该字段内容设置为空
		$zip_city = "";
		// $zip_city = $newrow['zip_city'];//小应用的城市属性，暂时不需要填
		$container_tid = ( integer ) $newrow ['container_tid']; // 小应用的从属于哪一个容器
		                                                   // 判断输入信息的有效性
		$typeMatchArray = array (
				"student",
				"teacher",
				"OA" 
		); // 匹配type
		$forceMatchArray = array (
				"0",
				"1" 
		); // 匹配container_force
		if (in_array ( $type, $typeMatchArray ) == false or $zip_platform <= 0 or $zip_name == null or $zip_filetype == null or $zip_version == null or $zip_detaile == null or in_array ( $zip_force, $forceMatchArray ) == false or $container_tid <= 0) {
			$msg->ResponseMsg ( 1, '您的输入有误！', false, 0, $callback );
			exit ();
		}
		// 由于目前客户端要求小应用zip包没有城市属性，因此强制将该字段内容设置为空
		$zip_city = "";
		// 根据type确定存放地址
		$zip_position = '/framework/' . $type . '/base/';
		// 检查该容器是否存在
		$model = spClass ( 'container_info' );
		$result = $model->findBy ( 'tid', $container_tid );
		if (! $result) {
			$msg->ResponseMsg ( 1, '指定的容器不存在', false, 0, $callback );
			exit ();
		}
		// 记录该指定容器的相关信息
		$container = $result ['container_name'] . "V" . $result ['container_version'] . "." . $result ['container_filetype'];
		// 检查是否已有重复的容器名称、文件类型和版本号
		$conditions = ' SELECT tid,zip_version FROM zip_info
				WHERE zip_name = "' . $zip_name . '"
					AND zip_version >= "' . $zip_version . '"
					AND container_tid = "' . $container_tid . '"
				ORDER BY zip_version DESC';
		$model = spClass ( 'zip_info' );
		$result = $model->findsql ( $conditions );
		if ($result) {
			$msg->ResponseMsg ( 1, '指定容器' . $container . '中新增小应用' . $zip_name . '的版本应高于已有版本号V' . $result ['0'] ['zip_version'], false, 0, $callback );
			exit ();
		}
		$new = array ( // 新增小应用的数组
				'zip_name' => $zip_name,
				'zip_filetype' => $zip_filetype,
				'zip_version' => $zip_version,
				'zip_platform' => $zip_platform,
				'zip_detaile' => $zip_detaile,
				'zip_force' => $zip_force,
				'zip_position' => $zip_position,
				'zip_city' => $zip_city,
				'container_tid' => $container_tid 
		);
		$result = $model->create ( $new ); // 进行新增操作
		if ($result) {
			$msg->ResponseMsg ( 0, '在容器' . $container . '中添加小应用成功! ', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, '在容器' . $container . '中添加小应用失败! ', false, 0, $callback );
			exit ();
		}
	}
	/**
	 * 根据容器名字及指定版本号查询下载地址
	 */
	function queryGivenVersionUrl() {
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$container_name = $newrow ['container_name']; // 容器的名字
		$container_version = $newrow ['container_version']; // 容器的版本号
		if (defined ( 'TestVersion' )) {//测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else {//正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
		if ($container_name == null or $container_version == null) {
			$msg->ResponseMsg ( 1, ' 容器名字、版本号不能为空!', false, 0, $callback );
			exit ();
		}
		//查询指定版本的容器的信息
		$querySql = 'SELECT tid,
				container_name AS name,
				container_version AS version,				
				container_detaile AS detaile,
				CONCAT(container_name,"V",container_version,".",container_filetype) AS package_name,
				CONCAT("' . $domain_name . '",container_position,container_name,"V",container_version,".",container_filetype) AS url,
				container_force AS isforce,
				container_platform AS platform
				FROM container_info
				WHERE container_name LIKE "%' . $container_name . '%"				
						 AND container_version = "' . $container_version . '"';
		$model = spClass ( 'container_info' );
		$container_info = $model->findSql ( $querySql );
		if (! $container_info) {
			$msg->ResponseMsg ( 1, '容器 ' . $container_name . ' 不存在 !', false, 0, $callback );
			exit ();
		}
		//查询指定版本的容器下的所有小应用信息
		$querySql = 'SELECT tid,
					zip_name AS name,
					MAX(zip_version) AS version,
					zip_city AS city,
					zip_detaile AS detaile,
					CONCAT(zip_name,"V",MAX(zip_version),".",zip_filetype) AS package_name,
					CONCAT("' . $domain_name . '",zip_position,zip_name,"V",MAX(zip_version),".",zip_filetype) AS url,
					zip_force AS isforce,
					zip_platform AS platform
				FROM zip_info
				WHERE  container_tid =  "' . $container_info ['0'] ['tid'] . '"';
		$querySql .= ' GROUP BY zip_name ';
		$model = spClass ( 'zip_info' );
		$result = $model->findSql ( $querySql );
		if ($result) {
			$result = array_merge ( $container_info, $result );
			$msg->ResponseMsg ( 0, '成功', $result, 0, $callback );
		} else {
			$msg->ResponseMsg ( 1, '容器 ' . $container_name . '下没有小应用可供下载!', false, 0, $callback );
			exit ();
		}
	}
}

?>
