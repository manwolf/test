<?php
include_once 'base/crudCtr.php';
/**
 * 功能：新增、查看、删除移动框架文件
 * 作者： 孙广兢
 * 日期： 2015年9月22日 
 */
class KJVersionCtr extends crudCtr {
		
	/**
	 * 
	 * 学生端：容器名字：studentIOS.ipa  studentAndroid.apk; 小应用的名字：studentIOS.zip  studentAndroid.zip
	 * 教师端：容器名字：teacherIOS.ipa  teacherAndroid.apk; 小应用的名字：teacherIOS.zip  teacherAndroid.zip
	 * 文件进行md5加密后返给客户端的字段为file_MD5
	 */
	
	/**
	 * 查询最新版本文件
	 */
	function queryNewFramework(){
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
		$client = $newrow ['client'];// 客户端类型，学生端:1  ;  教师端:2
		$platform = $newrow ['platform'];// 应用平台,1:安卓 ； 2：苹果		
		if (defined ( 'TestVersion' )) { // 测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else { // 正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
		if (! preg_match ( '/^[1-2]$/', $client )) {
			$msg->ResponseMsg ( 1, '请输入客户端类型的正确格式，学生端:1 ;  教师端:2 ', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^[1-2]$/', $platform )) {
			$msg->ResponseMsg ( 1, '请输入应用平台的正确格式，   安卓：1；苹果： 2', false, 0, $callback );
			exit ();
		}				
		// 查询所有文件的版本信息
		$querySql = 'SELECT bb.tid,bb.client,bb.city,bb.type,	bb.platform,	bb.name,	
							CONCAT(bb.name,if(bb.platform = 1,"Android","IOS"),".",
							if(bb.type = 1,if(bb.platform = 1,"apk","ipa"),"zip")) AS package_name,
							bb.version,
							bb.detaile,
							CONCAT(if(bb.url = "" or bb.url = " ","","'.$domain_name.'"),bb.url) AS url,				
							bb.file_MD5,
							bb.create_time				
				FROM ( SELECT 	client,platform,type,name,MAX(version) AS version 
							FROM 	framework_info 
    						GROUP BY client,platform,type,name )	 aa
               LEFT JOIN  framework_info bb 
				ON  aa.client = bb.client AND aa.platform = bb.platform 
						AND aa.type = bb.type AND aa.name = bb.name  AND aa.version = bb.version
         WHERE 
						bb.tid > 0 
				   ';
		//按照客户端类型搜索
		if ($client > 0) {
			$querySql .= ' AND  aa.client = "'.$client .'" ';
		}		
		// 按照平台搜索
		if ($platform > 0) {
			$querySql .= ' AND  aa.platform = "'.$platform .'" ';
		}
		$querySql .= ' GROUP BY  bb.type,bb.name	ORDER BY bb.type,bb.name ';		
		$model = spClass ( 'framework_info' );
		$result = @$model->findSql( $querySql );
		$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $callback );	
		
	}
	/**
	 * 根据客户端类型、应用平台、城市、文件类型、文件名、版本号 等,查询容器以及该容器下所有小应用的相关信息(包括下载URL)
	 */
	function queryFramework() {
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
		// 是否只查询最新版本
		$isnew= $newrow ['isnew']; // 只查询最新：1 ；  查询所有：0
		$client = $newrow ['client'];// 客户端类型，学生端:1  ;  教师端:2
		$type = $newrow ['type'];// 类型，是1:容器  2：小应用
		$name = $newrow ['name'];// 名字,是student、teacher
		$version = $newrow ['version'];//版本号
		$platform = $newrow ['platform'];// 应用平台,1:安卓 ； 2：苹果
		$city = $newrow ['city'];//城市
	
		if (defined ( 'TestVersion' )) { // 测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else { // 正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
		if (! preg_match ( '/^[1-2]?$/', $client )) {
			$msg->ResponseMsg ( 1, '请输入客户端类型的正确格式，或不输入任何值，学生端:1 ;  教师端:2 ', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^[1-2]?$/', $type )  ) {
			$msg->ResponseMsg ( 1, '请输入文件类型的正确格式，或不输入任何值:   容器：1；小应用：2', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^(student|teacher)?$/', $name ) ) {
			$msg->ResponseMsg ( 1, '请输入文件名称的正确格式，或不输入任何值: student；teacher', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^[1-2]?$/', $platform )) {
			$msg->ResponseMsg ( 1, '请输入应用平台的正确格式，或不输入任何值:   安卓：1；苹果： 2', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^(([1-9]?[0-9])+(\.[1-9]?[0-9]){2})?$/', $version )) {
			$msg->ResponseMsg ( 1, '请输入版本号的正确格式，或不输入任何值，如1.0.0 ', false, 0, $callback );
			exit ();
		}
	
		// 查询所有文件的版本信息
		$querySql = 'SELECT tid,
				case client
					when 1 then "学生端"
					when 2 then "教师端"
					ELSE "无"
				end as client,
				city,
				case type
					when 1 then "容器"
					when 2 then "小应用"
					ELSE "无"
				end as type,
				case platform
					when 1 then "安卓"
					when 2 then "苹果"
					ELSE "无"
				end as platform,
				name,
				CONCAT(name,if(platform = 1,"Android","IOS"),".",
				if(type = 1,if(platform = 1,"apk","ipa"),"zip")) AS package_name,';
		if($isnew == 1){
			$querySql .= ' MAX(version) AS ';
		}
		$querySql .= ' version,
				detaile,
				CONCAT(if(url = "" or url = " ","","'.$domain_name.'"),url) AS url,
				CONCAT(if(url_backup = "" or url_backup = " ","","'.$domain_name.'"),url_backup) AS url_backup,
				file_MD5,
				create_time
				FROM framework_info
				WHERE tid > 0 ';
		//按照客户端类型搜索
		if ($client > 0) {
			$querySql .= ' AND  client = "'.$client .'" ';
		}
		//按照文件类型搜索
		if ($type > 0) {
			$querySql .= ' AND  type = "'.$type .'" ';
		}
		// 按照平台搜索
		if ($platform > 0) {
			$querySql .= ' AND  platform = "'.$platform .'" ';
		}
		// 按名字搜索
		if ($name != null) {
			$querySql .= ' AND name  LIKE "%' . $name . '%"';
		}
		// 按版本号搜索
		if ($version != null) {
			$querySql .= ' AND version  = "' . $version . '"';
		}
		// 按城市搜索
		if ($city != null) {
			$querySql .= ' AND city  LIKE "%' . $city . '%"';
		}
		if($isnew == 1){
			$querySql .= ' GROUP BY client,platform,type,name ';
		}		
		$querySql .= ' ORDER BY client,platform,type,name,version DESC ';
		//echo $querySql;
		$model = spClass ( 'framework_info' );		
		$page = $newrow ['page'] ? $newrow ['page'] : 1;
		$result = @$model->spPager ( $page, 10 )->findSql ( $querySql );
		$pager = $model->spPager ()->getPager ();
		$total_page = $pager ['total_page'];
		if ($result) {
			$msg->ResponseMsg ( 0, '查询成功', $result, $total_page, $callback );
		} else {
			$msg->ResponseMsg ( 1, '查询失败 !', false, $total_page, $callback );
			exit ();
		}
	}
	
	
	
	/**
	 * 新增移动框架文件信息
	 */
	function addFrameworkFile() {
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
		// 客户端类型，1:学生端  2：教师端
		$client= ( integer )$newrow ['client'];
		// 类型，1:容器  2：小应用
		$type = ( integer )$newrow ['type'];	
		// 名字,是student、teacher
		$name = $newrow ['name'];
		// 应用平台,1:安卓 ； 2：苹果
		$platform = ( integer ) $newrow ['platform'];		
		// 版本号
		$version = $newrow ['version'];
		// 详细描述
		$detaile = $newrow ['detaile'];
		//文件md5校验字符串
		$file_MD5 = $newrow ['file_MD5'];
		//url
		$url = $newrow ['url'];
		//url_backup
		$url_backup = $newrow ['url_backup'];
		
		//过滤输入信息
		// 匹配type	
		if (! preg_match ( '/^[1-2]$/', $client )) {
			$msg->ResponseMsg ( 1, '请输入客户端类型，学生端:1  ;  教师端:2 ', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^[1-2]$/', $type )) {
			$msg->ResponseMsg ( 1, '请输入文件类型:   容器：1、小应用： 2', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^(student|teacher)$/', $type ) ) {
		$msg->ResponseMsg ( 1, '请输入文件名称: 学生端：student； 教师端：teacher', false, 0, $callback );
		exit ();					
		}
		if (! preg_match ( '/^[1-2]$/', $platform )) {
			$msg->ResponseMsg ( 1, '请输入应用平台:   安卓：1、苹果： 2', false, 0, $callback );
			exit ();			
		}
		if (! preg_match ( '/^([1-9]?[0-9])+(\.[1-9]?[0-9]){2}$/', $version )) {
			$msg->ResponseMsg ( 1, '请输入正确的版本号，如1.0.0 ', false, 0, $callback );
			exit ();			
		}
		if ( $detaile == null) {
			$msg->ResponseMsg ( 1, '请填写版本升级的详细信息！', false, 0, $callback );
			exit ();
		}								
		if (!($type == 2 AND $platform == 2)) {	 // 非苹果容器,必填md5、url、url_backup			
			if (iconv_strlen($file_MD5,"UTF-8") != 32 ) {
				$msg->ResponseMsg ( 1, '请输入该文件的32位md5校验值！', false, 0, $callback );
				exit ();
			}
			if ( $url == null OR $url_backup == NULL ) {
				$msg->ResponseMsg ( 1, '请填写2个URL！', false, 0, $callback );
				exit ();
			}
		}	else{
			$file_MD5 = " ";
			$url = " ";
			$url_backup = " ";
		}	
		
		//查找该版本是否存在	
		$findconditions = array(
				'client' 	=> $client,
				'type' 		=> $type,
				'name' 		=> $name,
				'platform' => $platform ,
				'version'  => $version
		);				
		$model = spClass ( 'framework_info' );
		$exist = @$model->find( $findconditions );
		//如果存在该版本，则修改相关信息，否则为新增
		if($exist){
			$new = array (					
					'detaile' => $detaile,
					'url' => $url	,
					'url_backup' => $url_backup,
					'file_MD5' => $file_MD5,
					'create_time' => date('Y-m-d H:i:s'),
			);			
			// 修改版本信息
			$result = @$model->update($findconditions,$new );
			if ($result) {
				$msg->ResponseMsg ( 0, '修改版本成功', $result, 0, $callback );
			} else {
				$msg->ResponseMsg ( 1, '修改版本失败! ', false, 0, $callback );
				exit ();
			}
		}else{
			$new = array (
					'client' 	=> $client,
					'type' => $type,
					'name' => $name,
					'version' => $version,
					'platform' => $platform,
					'detaile' => $detaile,
					'url' => $url	,
					'url_backup' => $url_backup	,
					'file_MD5' => $file_MD5
			);
			// 新增版本
			$result = @$model->create ( $new );
			if ($result) {
				$msg->ResponseMsg ( 0, '添加版本成功', $result, 0, $callback );
			} else {
				$msg->ResponseMsg ( 1, '添加版本失败! ', false, 0, $callback );
				exit ();
			}
		}
		
		
	}
	
	
	
	
	/**
	 * 删除备份的指定容器及旗下所有小应用
	 */
	function deleteFramework(){
		$msg = new responseMsg ();
		$capturs = $this->captureParams ();
		$callback = $capturs ['callback'];
		$newrow = $capturs ['newrow'];
		$tid = (integer)$newrow ['tid'];
		// 查找信息，并删除相应文件
		$conditions = array('tid'=>$tid); // 构造条件
		$model = spClass ( 'framework_info' );
		$find = @$model->find( $conditions );
		if($find){			
			$url_backup = "/home/wwwroot/default".$find['url_backup'];
			$result = $this->deleteFile($url_backup);	
			if(!$result){
				$msg->ResponseMsg ( 1, '文件位置有误，删除失败 !', false, $total_page, $callback );
				exit ();
			}			
		}else{
			$msg->ResponseMsg ( 1, '容器不存在 !', false, $total_page, $callback );
			exit ();
		}		
		
		// 在数据库中删除存储信息
		$conditions = array('tid'=>$tid); // 构造条件	
		$model = spClass ( 'container_info' );
		$delete = @$model->delete( $conditions );			
		$deleteAffected = @$model->affectedRows();
		
		if ($deleteAffected >0 ) {
			$msg->ResponseMsg ( 0, '删除成功', $result, 1, $callback );
		} else {
			$msg->ResponseMsg ( 1, '删除失败 !', false, 0, $callback );
			exit ();
		}
	}
	
	/**
	 * 删除文件
	 * @param string $file
	 * @return boolean
	 */
	function deleteFile($file){	
			$msg = new responseMsg ();
			$capturs = $this->captureParams ();
			$callback = $capturs ['callback'];		
			if(file_exists($file)){
				if (!unlink($file)){
					$msg->ResponseMsg ( 1, '从服务器中删除文件失败 !', false, 0, $callback );
					return false;
				}else{
					return true;
				}
			}else{
				$msg->ResponseMsg ( 1, '文件不存在 !', false, 0, $callback );
				return false;
			}		
	}
	
	/**
	 * END
	 */
}
				
?>
