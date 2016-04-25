<?php
include_once 'base/crudCtr.php';
/**
 * 功能：移动框架的应用下载接口
 * 作者： 孙广兢
 * 日期：2015年8月27日
 * 修改：2015年9月21日 新增文件md5加密字符串、固定URL
 */
class DownloadCtr extends crudCtr {
	
	/**
	 * 根据应用平台、城市、容器名字,查询最新版本的容器以及该容器下所有小应用的相关信息(包括下载URL)
	 * 学生端：容器名字：studentIOS.ipa  studentAndroid.apk; 小应用的名字：studentIOS.zip  studentAndroid.zip
	 * 教师端：容器名字：teacherIOS.ipa  teacherAndroid.apk; 小应用的名字：teacherIOS.zip  teacherAndroid.zip
     * 文件进行md5加密后返给客户端的字段为file_MD5
	 */
	function queryNewALLUrl() {
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
		$client = $newrow ['container_name'];// 客户端类型，学生端:student  ;  教师端:teacher
		$platform = $newrow ['platform'];// 应用平台,1:安卓 ； 2：苹果		
		if (defined ( 'TestVersion' )) { // 测试环境服务器
			$domain_name = "http://testapi.e-teacher.cn";
		} else { // 正式环境服务器
			$domain_name = "http://app.e-teacher.cn";
		}
			
		if (! preg_match ( '/^(student|teacher)$/', $client )) {
			$msg->ResponseMsg ( 1, '请输入客户端类型的正确格式，  学生端：student；教师端： teacher', false, 0, $callback );
			exit ();
		}
		if (! preg_match ( '/^[1-2]$/', $platform )) {
			$msg->ResponseMsg ( 1, '请输入应用平台的正确格式，   安卓：1；苹果： 2', false, 0, $callback );
			exit ();
		}	
		if($client == "student"){
			$client = 1;
		}else{
			$client = 2;
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
	
		
}

?>
