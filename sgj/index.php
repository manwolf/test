<?php
// $pn = 'com.youweosoft.onelevel';
// $pn='com.soyui.pkx.mm';
$pn = $_REQUEST ['pn'];
 function getAppDetailByWeb($pn){
	$url = "http://www.wandoujia.com/apps/{$pn}";
	$response = @file_get_contents ( $url );
	$pat = '{<title>\s*(「豌豆荚」官方网站)|(精品安卓应用每日推荐\s*-\s*豌豆荚)\s*<\/title>}isx';
	if (preg_match ( $pat, $response )) {
		return null;
	}

	$html_regex = array ();
	$html_regex ['icon'] = '{ <div \s* class="app-icon">\s* <img \s* src="(?P<icon>[^>]*)" \s* itemprop="image" \s* width="\d*" \s* height="\d*" \s* alt="[^>]*" \s*\/> \s*  <\/div>
					}isx';
	$html_regex ['name'] = '{ \s*	<p \s* class="app-name"> \s* <span \s* class="title" \s* itemprop="name">\s*(?P<name>[^>]*)\s*<\/span>\s*<\/p>
	        }isx';
	$html_regex ['tagLine'] = '{<p \s* class="tagline">\s* (?P<tagLine>[^>]*) \s* <\/p>

			}isx';
	$html_regex ['packageName'] = '{ <a \s* data-install="[^>]*" \s* data-like="[^>]*" \s* data-name="[^>]*"
				\s* data-pn="(?P<packageName>[^>]*)" \s* class="install-btn" \s* rel="nofollow" \s* style="display:inline-block;"
				\s* href="(?P<durl>[^>]*)" \s* data-track="[^>]*">\s*高速下载\s*<\/a>
			}isx';
	$html_regex ['icount'] = '{ <i \s* itemprop="interactionCount" \s* content="UserDownloads:(?P<dcountValue>\d*) \s*">
				\s* (?P<icountValue>\d*) \s* (?P<icountUnit>[^>]*)? \s* <\/i>

		
	}isx';
	$html_regex ['verified-info'] = '{ <span \s* class="verified-info"> \s* <s \s* class="tag \s* security-info \s* (?P<securityStatus>\S*) \s* "
				\s*data-md5="(?P<apkmd5>[0-9a-z]{32})"><\/s>
				\s* <s \s* class="tag \s* (?P<adsType>[^>]*)"><\/s>
				\s* <s \s* class="tag \s* permission-info \s* (?P<permission>\S*)\s*"> \s* <\/s>
				\s* <s \s* class="tag \s* verify-info \s* (?P<official>[^>]*)">\s*<\/s>
				\s* <\/span>
			}isx';
	$html_regex ['editorDesc'] = '{ <div \s* class="cols \s* clearfix"> \s* <div \s* class="col-left">\s*
			<div \s* class \s* = \s* "editorComment" \s* >\s* <h2 \s* class="block-title" \s* > [^>]* <\/h2>
				\s* <div \s* class="con" \s* > \s* (?P<editorDesc>[^>]*) \s* <\/div> \s* <\/div>
				\s*<div \s* class="screenshot">
			}isx';
	$html_regex ['snap'] = '{ <div \s*class="j-scrollbar-wrap">\s*<div \s* class="view-box">\s*<div \s* data-length="\d*" \s* class="overview" \s* style="width:\d*px">
                \s*<img \s* src="(?P<snap0>[^>]*)" \s* itemprop="screenshot" \s* alt="[^>]*" \s* width="\d*" \s* onmousedown="return \s* false" \s* \/>
				(?:\s*<img \s* src="(?P<snap1>[^>]*)" \s* itemprop="screenshot" \s* alt="[^>]*" \s* width="\d*" \s* onmousedown="return \s* false" \s* \/>)?
				(?:\s*<img \s* src="(?P<snap2>[^>]*)" \s* itemprop="screenshot" \s* alt="[^>]*" \s* width="\d*" \s* onmousedown="return \s* false" \s* \/>)?
				(?:\s*<img \s* src="(?P<snap3>[^>]*)" \s* itemprop="screenshot" \s* alt="[^>]*" \s* width="\d*" \s* onmousedown="return \s* false" \s* \/>)?
				(?:\s*<img \s* src="(?P<snap4>[^>]*)" \s* itemprop="screenshot" \s* alt="[^>]*" \s* width="\d*" \s* onmousedown="return \s* false" \s* \/>)?
			}isx';
	$html_regex ['desc'] = '{
		<div \s* class="desc-info">
             \s* <h2 \b [^>]* > [^>]* <\/h2>
             \s* <div \b [^>]* > (?P<desc>.*) <\/div>
		\s* <a \s* style="display:none;" \s* class="more-link" \s* rel="nofollow">\s*更多 \s* <i \s* class="arrow-down">\s* <\/i>\s* <\/a>
         \s* <\/div>			
           }isx';
	$html_regex ['changlog'] = '{ <div \s* class="change-info">
		\s* <h2 \s* class="block-title"> \s* [^>]* \s* </h2>
		\s* <div \s* data-originheight="100" \s* class="con" > (?P<changlog>.*) </div>
		\s* <a \s* style="display:none;" \s* class="more-link">\s*更多 \s* <i \s* class="arrow-down">\s*<\/i>\s*<\/a>
        \s* <\/div> \s*  <\/div>
	}isx';
	$html_regex ['size'] = '{ <dt>大小<\/dt>\s* <dd> \s* (?P<sizeValue>\d*\.?\d*) (?P<sizeUnit>[M|G|K]) [^>]* <meta \s* itemprop="fileSize" \s* content="\d*"\/>\s* <\/dd>
						}isx';
	$html_regex ['cate'] = '{ <dt>分类<\/dt>\s*<dd \s* class="tag-box">
                               (?:\s*<a \s* href="[^>]*" \s* itemprop="SoftwareApplicationCategory" \s* >(?P<cate0>[^>]*)<\/a>)?
                               (?:\s*<a \s* href="[^>]*" \s* itemprop="SoftwareApplicationCategory" \s* >(?P<cate1>[^>]*)<\/a>)?
                               (?:\s*<a \s* href="[^>]*" \s* itemprop="SoftwareApplicationCategory" \s* >(?P<cate2>[^>]*)<\/a>)?
							   (?:\s*<a \s* href="[^>]*" \s* itemprop="SoftwareApplicationCategory" \s* >(?P<cate3>[^>]*)<\/a>)?
							   (?:\s*<a \s* href="[^>]*" \s* itemprop="SoftwareApplicationCategory" \s* >(?P<cate4>[^>]*)<\/a>)*
                           \s* <\/dd>
				}isx';
	$html_regex ['updateTime'] = '{ <dt>更新<\/dt>\s* <dd><time \s* id="baidu_time" \s* itemprop="datePublished" \s* datetime="(?P<updateTime>[^>]*)">[^>]*<\/time><\/dd>
						}isx';
	$html_regex ['versionName'] = '{ <dt>版本<\/dt>\s* <dd>(?P<versionName>[^>]*)<\/dd>
						}isx';
	$html_regex ['permissions'] = '{ <ul \s* id="j-perms-list" \s* class="perms-list" \s* style="display:none">
                        (?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission0>[^>]*) \s* <\/span> \s* <\/li>)?
                        (?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission1>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission2>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission3>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission4>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission5>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission6>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission7>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission8>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission9>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission10>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission11>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission12>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission13>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission14>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission15>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission16>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission17>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission18>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission19>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission20>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission21>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission22>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission23>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission24>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission25>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission26>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission27>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission28>[^>]*) \s* <\/span> \s* <\/li>)?				
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission29>[^>]*) \s* <\/span> \s* <\/li>)?
				 		(?: \s* <li> \s* <span \s* class="perms" \s* itemprop="permissions"> \s* (?P<permission30>[^>]*) \s* <\/span> \s* <\/li>)*	
				\s* <\/ul>
				}isx';
	$html_regex ['Author'] = '{<dt>网站<\/dt> .*
                 \s* <dd \s* itemprop="author" \s* itemscope \s* itemtype="http:\/\/schema\.org\/Organization"> \s* <span>
                        (\s*<meta \s* content="(?P<developerAuthor>[^>]*)" \s* itemprop="name">)?
						(\s*<meta \s* content="(?P<developerWebsite>[^>]*)" \s* itemprop="url">)?
                     \s* <\/span> .* \s* <\/dd>
			}isx';
	$html_regex ['from'] = '{<dt>来自</dt>\s* <dd\b[^>]*> \s* (<a[^>]*>)? (?P<from>.*)提供 (</a>)? \s*</dd>
			}isx';
	//初始化数组
	$match = array (
			'icon' => '',
			'name' => '',
			'tagLine' => '',
			'packageName' => '',
			'durl' => '',
			'dcountValue' => '',
			'icountValue' => '',
			'icountUnit' => '',
			'securityStatus' => '',
			'apkmd5' => '',
			'adsType' => '',
			'permission' => '',
			'official' => '',
			'editorDesc' => '',
			'snap0' => '',
			'snap1' => '',
			'snap2' => '',
			'snap3' => '',
			'snap4' => '',
			'desc' => '',
			'changlog' => '',
			'sizeValue' => '',
			'sizeUnit' => '',
			'cate0' => '',
			'cate1' => '',
			'cate2' => '',
			'cate3' => '',
			'cate4' => '',
			'updateTime' => '',
			'versionName' => '',
			'permission0' => '',
			'permission1' => '',
			'permission2' => '',
			'permission3' => '',
			'permission4' => '',
			'permission5' => '',
			'permission6' => '',
			'permission7' => '',
			'permission8' => '',
			'permission9' => '',
			'permission10' => '',
			'permission11' => '',
			'permission12' => '',
			'permission13' => '',
			'permission14' => '',
			'permission15' => '',
			'permission16' => '',
			'permission17' => '',
			'permission18' => '',
			'permission19' => '',
			'permission20' => '',
			'permission21' => '',
			'permission22' => '',
			'permission23' => '',
			'permission24' => '',
			'permission25' => '',
			'permission26' => '',
			'permission27' => '',
			'permission28' => '',
			'permission29' => '',
			'permission30' => '',
			'developerAuthor' => '',
			'developerWebsite' => '',
			'from' => '',
	);
	foreach ( $html_regex as $key => $regexStr ) {
		if (preg_match_all ( $regexStr, $response, $mt )) {
			foreach ( $mt as $k => $v ) {
				if (is_numeric ( $k )) {
					unset ( $mt [$k] );
				} else {
					$match [$k] = $mt [$k] [0];
				}
			}
		} else {
			echo "$pn 的$key 通过Web捕获时，正则匹配失败\n";
		}
	}
	//判断App网页是否正确
	if(!isset($match['packageName'])){
		return null;
	}

	// 格式化数组$match,使其与api返回信息样式相同
	//print_r($match);
	//die();
	// 转换广告类型
	switch (strtolower ($match['adsType'] )) {
		case "no-ad" :
			$adsType = 'NONE';
			break;
		case "adv-embed" :
			$adsType = 'EMBEDED';
			break;
		default:
			$adsType = '';
	}
	// 转换文件大小
	switch (strtoupper ( $match ['sizeUnit'] )) {
		case "K" :
			$size = intval ( $match ['sizeValue'] * 1024 );
			break;
		case "M" :
			$size = intval ( $match ['sizeValue'] * 1024 * 1024 );
			break;
		case "G" :
			$size = intval ( $match ['sizeValue'] * 1024 * 1024 * 1024 );
			break;
		default:
			$size = $match ['sizeValue'];
	}
	// 转换时间
	date_default_timezone_set('UTC');
	$time = strtotime( $match ['updateTime'] ) * 1000;
	// 下载量字符串
	if ($match['dcountValue'] > 10000000) {
		$dcountStr = round ( $match ['dcountValue'] / 10000000 ) . "亿";
	} else if ($match ['dcountValue'] > 10000) {
		$dcountStr = round ( $match ['dcountValue'] / 10000 ) . '万';
	} else {
		$dcountStr = $match ['dcountValue'];
	}
	// 安装量 数字
	switch ($match ['icountUnit']) {
		case "亿" :
			$icount = intval ( $match ['icountValue'] * 10000000 );
			break;
		case "万" :
			$icount = intval ( $match ['icountValue'] * 10000 );
			break;
		default :
			$icount = intval ( $match ['icountValue'] );
			break;
	}
	// 安装量 字符串
	$icountStr = $match ['icountValue'] . ' ' . $match ['icountUnit'];

	//开始格式化应用详情$APPRD
	$APPRD = array (
			'apks' => array(
					'0' =>array(
							'adsType' => $adsType,
							'bytes' => $size,
							'creation' => $time,
							'downloadUrl' => Array (
									'market' => $match ['from'],
									'url' => $match ['durl'],
									),
							'language' => Array ('简体中文'),
							'md5' => $match ['apkmd5'],
							'minSdkVersion' => 8,
							'official' => strtolower ( $match ['official'] ) == 'wdj-verified' ? 1 : 0,
							'paidType' => '',
							'permissions' => Array (
									'0' => $match ['permission0'],
									'1' => $match ['permission1'],
									'2' => $match ['permission2'],
									'3' => $match ['permission3'],
									'4' => $match ['permission4'],
									'5' => $match ['permission5'],
									'6' => $match ['permission6'],
									'7' => $match ['permission7'],
									'8' => $match ['permission8'],
									'9' => $match ['permission9'],
									'10' => $match ['permission10'],
									'11' => $match ['permission11'],
									'12' => $match ['permission12'],
									'13' => $match ['permission13'],
									'14' => $match ['permission14'],
									'15' => $match ['permission15'],
									'16' => $match ['permission16'],
									'17' => $match ['permission17'],
									'18' => $match ['permission18'],
									'19' => $match ['permission19'],
									'20' => $match ['permission20'],
									'21' => $match ['permission21'],
									'22' => $match ['permission22'],
									'23' => $match ['permission23'],
									'24' => $match ['permission24'],
									'25' => $match ['permission25'],
									'26' => $match ['permission26'],
									'27' => $match ['permission27'],
									'28' => $match ['permission28'],
									'29' => $match ['permission29'],
									'30' => $match ['permission30']
							),
							'pubKeySignature' => '',
							'securityStatus' => strtoupper($match ['securityStatus']),
							'signature' => '',
							'superior' => '',
							'verified' => '',
							'versionCode' => 1,
							'versionName' => $match ['versionName']
					)
			),
			'categories' => Array (
					'0' => Array (
							'alias' => '',
							'level' => '',
							'name' => $match ['cate0'],
							'type' => ''
					),
					'1' => Array (
							'alias' => '',
							'level' => '',
							'name' => $match ['cate1'],
							'type' => ''
					),
					'2' => Array (
							'alias' => '',
							'level' => '',
							'name' => $match ['cate2'],
							'type' => ''
					),
					'3' => Array (
							'alias' => '',
							'level' => '',
							'name' => $match ['cate3'],
							'type' => ''
					),
					'4' => Array (
							'alias' => '',
							'level' => '',
							'name' => $match ['cate4'],
							'type' => ''
					)
			),
			'changelog' => $match ['changlog'],
			'description' => $match ['desc'],
			'developer' => Array (
					'email' => '',
					'id' => '',
					'intro' => '',
					'name' => $match ['developerAuthor'],
					'urls' => '',
					'verified' => '',
					'website' => $match ['developerWebsite'],
					'weibo' => ''
			),
			'downloadCount' => $match ['dcountValue'],
			'downloadCountStr' => $dcountStr,
			'icons' => Array (
					'px48' => '',
					'px100' => '',
					'px256' => $match ['icon'],
					'px78' => '',
					'px24' => '',
					'px68' => '',
					'px36' => ''
			),
			'installedCount' => $icount,
			'installedCountStr' => $icountStr,
			'likesRate' => '',
			'packageName' => $match ['packageName'],
			'publishDate' => $time,
			'screenshots' => Array (
					'small' => Array (
							'0' => $match ['snap0'],
							'1' => $match ['snap1'],
							'2' => $match ['snap2'],
							'3' => $match ['snap3'],
							'4' => $match ['snap4']
					),
					'normal' => Array (
					)
			),
			'stat' => Array (
					'weeklyStr' => ''
			),
			'tagline' => $match ['tagLine'],
			'tags' => Array (
					'0' => Array (
							'tag' => $match ['cate0'],
							'weight' => ''
					),
					'1' => Array (
							'tag' => $match ['cate1'],
							'weight' => ''
					),
					'2' => Array (
							'tag' => $match ['cate2'],
							'weight' => ''
					),
					'3' => Array (
							'tag' => $match ['cate3'],
							'weight' => ''
					),
					'4' => Array (
							'tag' => $match ['cate4'],
							'weight' => ''
					)
			),
			'title' => $match ['name'],
			'trusted' => strtolower ( $match ['permission'] ) == 'trusted' ? 1 : 0
	);
	return $APPRD ;

}
$response = getAppDetailByWeb($pn);

if (!$response) {
	echo "{$pn} can not be found .\n";
	return null;
}else{
	echo "<pre>";print_r($response);echo "</pre>";
}
$screenshots = $response['screenshots'];
$shotlist = array();
if (isset($screenshots['normal']) && $screenshots['normal']) {
	$shotlist 	= $screenshots['normal'];

}elseif (isset($screenshots['small']) && $screenshots['small']){
	$shotlist 	= $screenshots['small'];
}
echo "<pre>";
print_r($shotlist);
echo "</pre>";
$appRD = array();
$ind 		= 0;
$usedInd 	= 1;
while ($usedInd <= 5) {
	if (!isset($shotlist[$ind])) {
		break;
	}
	
	$sk = 'snap' . $usedInd; 
	$appRD->snap1 = $shotlist[$ind];	
	$ind++;
	$usedInd++;
}
echo "<pre>";
print_r($appRD);
echo "</pre>";
