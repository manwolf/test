<div class='searchBar' id='searchBar' style="display:block ; line-height: 40px;min-height:40px;background:red;">
	<input type="date" class="commonInput grayBorder" id='beginDate' placeholder="开始日期" value="<?=date('Y-m-d',time() - 86400)?>" style="height:26px;width:110px;margin-left:5px"/>
	~<input type="date" class="commonInput grayBorder" id='endDate' placeholder="结束日期" value="<?=date('Y-m-d',time() - 86400)?>" style="height:26px;width:110px;margin-left:5px"/>
	<select id="onDuty" class='commonSelect grayBorder'>
		<option value='-1' <?=$onDuty == -1 ? "selected='selected'" : ''?>>-- 状态 --</option>
		<option value='1' <?=$onDuty == 1 ? "selected='selected'" : ''?>>已录入</option>
		<option value='0' <?=$onDuty == 0 ? "selected='selected'" : ''?>>未录入</option>
		<option value='2' <?=$onDuty == 2 ? "selected='selected'" : ''?>>修改审批中</option>
	</select>
	<input class="typeahead" type="text" name="mediaText" id="mediaText" placeholder="渠道" value="<?=$media ? SNMedia::getNameByID($media) : ''?>" style='margin-left: 10px;width:180px'>
	<input type="hidden" name="media" id="media" value="<?=$media?>">
	<input class="typeahead" style='margin-left:10px;width:180px' type="text" id="campaignText" placeholder="广告活动" value="<?=$campaign ? SNCampaign::getNameById($campaign) : ''?>">
	<input type="hidden" name="campaign" id="campaign" value="<?=$campaign?>">
	<input type='text' class='commonInput grayBorder' style='width:50px;' placeholder='包ID' id='apk' value="<?=$apk ? $apk : ''?>">
	<input class='xyjButtonSmall qq' style='margin-left:20px' type="button" onclick='searchActionData()' value="搜索">
	<input class='xyjButtonSmall weibo' style='margin-left:20px' type="button" onclick='initActionData()' value="初始化">
</div>