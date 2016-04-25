
var API_Base_URLS = [ "HTTP://rdoa.e-teacher.cn/srvapi/framework/",
                      "HTTP://testapi.e-teacher.cn/srvapi/framework/",
                      "HTTP://localhost/srvapi/framework/",
                      "-----------下面 为 正式运行环境 ，请谨慎操作----------",
                      "HTTP://api.e-teacher.cn/srvapi/framework/"];

var API_Doc = [
{
	name : "Register user",
	explan : "用户注册",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "passportCtr",
		a : "registerAction",
		callback : "callback",
		telephone : "telephone",
		login_pwd : "",
		user_city:"城市",
		sc_num:"非必填"
	}
}, 
{
	name : "User login ",
	explan : "用户登录",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "passportCtr",
		a : "loginAction",
		callback : "callback",
		telephone : "",
		login_pwd : "",
		sc_num:"非必填"
	}
}, 
{
	name : "Test Token",
	explan : "用户登录",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		callback : "callback",
		c : "passportCtr",
		a : "testToken"
			
	}
},
{
	name : "Upload Image 页面图像上传说明",
	explan : "上传图片",
	path : "photoCtr.php",
	method : "request method:  POST",
	parameter : {
		c : "photoCtr",
		a : "Upload",
		callback : "callback"
			
	}

},
{
	name : "getRegVerifyidAction",
	explan : "注册时发手机验证码",
	path : "smsCtr.php",
	method : "verify sms ",
	parameter : {
		c : "smsCtr",
		a : "getRegVerifyidAction",
		callback : "callback",
		telephone : "varchar"
	}
},
{
	name : "verifySMSAction",
	explan : "检查手机收到的验证码是否正确",
	path : "smsCtr.php",
	method : "verify sms ",
	parameter : {
		c : "smsCtr",
		a : "verifySMSAction",
		callback : "callback",
		tid : "100000",
		verify_code : ""
	}
},
//{
//	name : "getTeacherList",
//	explan : "获取老师列表。teacher_class_fees 课时单价",
//	path : "teacherInfoCtr.php",
//	method : "getTeacherList ",
//	parameter : {
//		c : "teacherInfoCtr",
//		a : "getTeacherList",
//		callback : "callback",
//		teacher_city : "上海",
//		teacher_district : "浦东",
//		teacher_town : "全区",
//		student_grade_max : "4",
//		student_grade_min : "1"
//	}
//}, 
//{
//	name : "getTeacherDetailInfo",
//	explan : "获取老师列表。teacher_class_fees 课时单价",
//	path : "teacherInfoCtr.php",
//	method : "getTeacherDetailInfo ",
//	parameter : {
//		c : "teacherInfoCtr",
//		a : "getTeacherDetailInfo",
//		callback : "callback",
//		teacher_tid : ""
//	}
//}, 
{
	name : "getTeacherSchedule",
	explan : "查询某位老师的已被预约时间",
	path : "teacherScheduleCtr.php",
	method : "query ",
	parameter : {
		c : "teacherScheduleCtr",
		a : "query",
		callback : "callback",
		teacher_tid : "1",
		schedule_date : "2015-05-16"
	}
}, 
{
	name : "query",
	explan : "获取地区列表。",
	path : "areaListCtr.php",
	method : "query",
	parameter : {
		c : "areaListCtr",
		a : "query",
		callback : "callback"
	}
}, 
{
	name : "queryCity",
	explan : "获取城市列表。",
	path : "areaListCtr.php",
	method : "queryCity",
	parameter : {
		c : "areaListCtr",
		a : "queryCity",
		area_city:"",
		callback : "callback"
	}
}, 
{
	name : "queryDistrict",
	explan : "获取地区列表。",
	path : "areaListCtr.php",
	method : "queryDistrict",
	parameter : {
		c : "areaListCtr",
		a : "queryDistrict",
		callback : "callback",
		area_city : "上海"
	}
}, 
{
	name : "queryTown",
	explan : "获取地区列表。",
	path : "areaListCtr.php",
	method : "queryTown",
	parameter : {
		c : "areaListCtr",
		a : "queryTown",
		callback : "callback",
		area_city : "上海",
		area_district : "浦东"

	}
}, 
{
	name : "query",
	explan : "获取学生可选年龄",
	path : "studentAgeListCtr.php",
	method : "query ",
	parameter : {
		c : "studentAgeListCtr",
		a : "query",
		callback : "callback"
	}
}, 
{
	name : "query",
	explan : "获取学生可选年级",
	path : "studentGradeListCtr.php",
	method : "query ",
	parameter : {
		c : "studentGradeListCtr",
		a : "query",
		tid:"",
		grade:"",
		class_city:"",
		callback : "callback"
	}
}, 
{
	name : "Update student info",
	explan : "更新学生信息",
	path : "userInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "userInfoCtr",
		a : "update",
		token : "token",
		tid : "1",
		callback : "callback",
		user_name : "孔明",
		user_sex : "0",
		user_age : "10",
		user_address : "上海市浦东新区张杨路707号生命人寿大厦2232",
		user_grade : "1",
		user_free_num:"",
		user_city:""
	}
},
{
	name : "set user  image",
	explan : "更新学生头像",
	path : "userInfoCtr.php",
	method : "request method:  post",
	parameter : {
		c : "userInfoCtr",
		a : "setUserImg",
		token : "token",
		callback : "callback",
		userid : "1"
		

	}
}, 
{
	name : "add",
	explan : "预约课程。其中order_type为 0表示免费试课。1表示付费上课。2表示钱包充值。3表示拼课。",
	path : "orderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "orderListCtr",
		a : "add",
		token : "token",
		callback : "callback",
		teacher_tid : "1",
		user_tid : "1",
		order_date : "2015-09-18",
		order_time : "08:00",
		order_type : "1",
		//order_money : "150",
		order_remark : "",
		order_phone : "",
		order_address:"",
		
		//class_content:"授课内容",
		class_discount_tid:"",
		class_way:"上门服务",
		//order_original_price:"订单原价",
		class_grade:"课程年级 数字",
		class_types:"课程类型 0为普通课程 1为精品课程",
		high_quality_courses_tid:"精品课程的ID号",
		callback : "callback"
			
	}
}, 
//{
//	name : "class_list",
//	explan : "获得订单的课数order_tid  自动生成课程记录class——list",
//	path : "automatic.php",
//	method : "request method:  get",
//	parameter : {
//		c : "automatic",
//		a : "addOrderList",
//		//token : "token",
//		tid:"order_tid",  //order_tid
//		order_date:"上课日期",
//		class_count:"总课数",
//		callback : "callback"
//		//user_tid : "1"
//		
//	}
//}, 
{
	name : "queryOrder",
	explan : "预约课程--高级接口  详细查询",
	path : "orderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "orderListCtr",
		a : "queryOrder",
		token : "token",
		callback : "callback",
		user_tid : "1"
		
	}
}, 
{
	name : "query",
	explan : "家长（学生）查询上课订单",
	path : "orderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "orderListCtr",
		a : "query",
		token : "token",
		user_tid : "1",
		teacher_tid : "1"
		
	}
},
{
	name : "上分界线",
	explan : "上分界线",
	path : "",
	method : "request method:  get",
	parameter : {
		c : "-----陈鸿润-----STRAT-------",		
	}
},
{
	name : "changeDeviceTypeStat",
	explan : "判断是从哪种类型的设备下载的应用，并增加数据",
	path : "originTypeSataCtr.php",
	method : "request method:  get",
	parameter : {
		c : "originTypeSataCtr",
		a : "download",
		callback : "callback"
	}
}, 
{
	name : "showDeviceNum",
	explan : "该接口返回当前，各类型设备的下载量",
	path : "originTypeSataCtr.php",
	method : "request method:  get",
	parameter : {
		c : "originTypeSataCtr",
		a : "showDeviceNum",
		callback : "callback"
	}
},
{
	name : "jPush",
	explan : "对所有用户推送信息",
	path : "versionInfo.php",
	method : "request method:  get",
	parameter : {
		c : "jPushCtr",  
		a : "push",
		callback : "prefixJS",
		message : ""
	}
},
{
	name : "jPush",
	explan : "根据ID推送信息",
	path : "versionInfo.php",
	method : "request method:  get",
	parameter : {
		c : "jPushCtr",  
		a : "pushtoalias",
		callback : "prefixJS",
		tid: "",
		title: "",
		message : ""
	}
},
{
	name : "jPush",
	explan : "根据ID推送信息,tid需要把数组放在json中传过来 -- 格式：{\"0\":\"3\",\"1\":\"2\",\"3\":\"1\"}",
	path : "versionInfo.php",
	method : "request method:  get",
	parameter : {
		c : "jPushCtr",  
		a : "pushBytid",
		callback : "prefixJS",
		tid_array_json: "",
		title: "",
		message : ""
	}
},
{
	name : "add",
	explan : "支付订单",
	path : "payListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "payListCtr",
		a : "add",
		token : "token",
		callback : "callback",
		order_tid : "1",
		pay_type : "0",
		pay_done : "0"
	}
},
{
	name : "payListCtr",
	explan : "支付订单：pay_method=0----微信支付：pay_type=0 | 支付宝支付：pay_type=1 | 钱包支付：pay_type=2 | 银联支付:pay_type=3 <br>" +
			" 钱包充值：pay_method=1----微信充值：pay_type=0 | 支付宝充值：pay_type=1 | 银联支付：pay_type=2 | 退款:pay_type=3 ",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "payListCtr", 
		a : "addPayList",		
		order_tid:"",
		pay_method:"",
		pay_type:"",
		out_trade_no:"",
		user_tid:"",
		money:"只有钱包充值时，需要传",
		invitation_code:"",
		token:"",
		callback: "callback"
	}
},
{
	name : "payListCtr",
	explan : "完成支付",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "payListCtr", 
		a : "updatePayList",
		token:"",
		callback: "callback",
		out_trade_no:"",
		user_tid:"",
	}
},
{
	name : "payListCtr",
	explan : "客服查询该订单支付状态",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "payListCtr", 
		a : "queryOrderForKF",		
		out_trade_no:"",			
	}
},
{
	name : "payListCtr",
	explan : "H5支付-----银联支付：type=3",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "payListCtr", 
		a : "payForH5",
		token:"",
		order_tid:"",
		pay_type :"",
		invitation_code:"",
	}
},
{
	name : "walletCtr",
	explan : "--查询--  查询用户钱包余额",
	path : "walletCtr.php",
	method : "request method:  get",
	parameter : {
		c : "walletCtr", 
		a : "query",
		user_tid: "",
		callback: "callback"
	}
},
{
	name : "walletCtr",
	explan : "--付订单--  用钱包付订单",
	path : "walletCtr.php",
	method : "request method:  get",
	parameter : {
		c : "walletCtr", 
		a : "payByWallet",
		order_tid: "",
		invitation_code: "",
		user_tid: "",
		token:"",
		callback: "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "--文件上传，避免跨域问题和重定向问题--: <br> " +
			"调用方式:http://testapi.e-teacher.cn/srvapi/framework/controller/upLoadForH5.php?callback=JSON_CALLBACK&type=0&class_tid=20 <br>" +
			"type(用GET方式传输): 0为上传教师教案 | 1为上传教师头像  | 2为上传教师生活照 -- 文件(用POST方式传输) <br>" +
			"--返回值--: code: {0-成功,1-失败} url: 文件访问地址",
	path : "uploadCtr.php",
	method : "request method:  get",
	parameter : {
		c : "uploadCtr",
		a : "upLoadForH5",
		callback : "callback",
		type : "type=0教师教案 | 1教师头像  | 2教师生活照 | 3首页轮播图",
		class_tid : "仅type=0需上传",
		teacher_phone : "仅type=1、2需上传",
		Teacher_detail_image_no: "仅type=2需上传 | 1为第一张 | 2为第二张",
		city : "仅type=3需上传",
		image_no : "仅type=3需上传| 1为第一张 | 2为第二张 | 3为第三张"
	}
},
{
	name : "Get JSAPI Para",
	explan : "--文件上传--: <br> " +
			"type(用GET方式传输): 0为教师教案上传  1为教师头像上传   <br> " +
			"文件(用POST方式传输) <br>" +
			"--返回值--: code: {0-成功,1-失败} url: 文件访问地址",
	path : "uploadCtr.php",
	method : "request method:  get",
	parameter : {
		c : "uploadCtr",
		a : "uploadForTeacher",
		callback : "callback",
		type : ""
	}
},
{
	name : "Get JSAPI Para",
	explan : "参与者寻找拼课",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "querySpellingLesson",
		class_city : "城市",
		class_district : "区",
		class_place : "街道办",
		class_grade : "小学",
		sort : "0为距离最近，1为最新发起"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询试课限制状态 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "queryTryLesson",
		telephone : "13127599862"
	}
},
{
	name : "Get JSAPI Para",
	explan : "消除试课限制 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "delTryLesson",
		telephone : "13127599862"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询调课限制状态 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "queryChangeLesson",
		telephone : "13127599862"
	}
},
{
	name : "Get JSAPI Para",
	explan : "消除调课限制 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "delChangeLesson",
		telephone : "13127599862"
	}
},
{
	name : "Get JSAPI Para",
	explan : "消除试课限制 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "delInterview",
		telephone : "13127599862"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询教案 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "queryTeacherGrammar",
		tid : "10"
	}
},
{
	name : "Get JSAPI Para",
	explan : "清除教案 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "delTeacherGrammar",
	}
},
{
	name : "Get JSAPI Para",
	explan : "清除所有教案 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "delAllTeacherGrammar",
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询教师头像- 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "queryTeacherImage",
		tid : ""
	}
},
{
	name : "Get JSAPI Para",
	explan : "清除教师头像 - 用于测试，仅在测试端有效",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "testCtr",
		a : "delTeacherImage",
		tid : ""
	}
},
{
	name : "下分界线",
	explan : "下分界线",
	path : "",
	method : "request method:  get",
	parameter : {
		c : "-----陈鸿润-----END----------",		
	}
},
{
	name : " ",
	explan : "上分界线",
	path : "",
	method : "request method:  get",
	parameter : {
		
		c : "↓↓↓↓↓请注意！以下是李坡写的↓↓↓↓↓",		
	}
},
{
	name : "JYLand JyLand JyLand",
	explan : "教研登录",
	path : "JYUserInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYUserInfoCtr",  
		a : "jyland", 
		token: "",
		jy_telephone: "",		
		jy_pwd: "",
		callback: "callback"
	}
},

{
	name : "JyUpdate JyUpdate JyUpdate",
	explan : "修改教研员",
	path : "JYUserInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYUserInfoCtr",  
		a : "jyupdate",
		token: "",
		tid:"",
		jy_pwd :"",
		jy_name : "",
		jy_name_en : "",
		jy_sex : "",
		jy_age : "",
		jy_seniority : "",
		jy_district : "",
		jy_town : "",
		jy_image : "",
		callback : "callback"
	}
},


{
	name : "jyquery  jyquery  jyquery ",
	explan : "查询全国教研信息",
	path : "JYUserInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYUserInfoCtr",
		a : "queryAllJy",
		token: "",
		tid : "",
		jy_telephone :"",
		jy_name : "",
		jy_name_en : "",
		jy_sex : "",
		jy_age : "",
		jy_seniority : "",
		jy_city : "",
		jy_district : "",
		jy_town : "",
		jy_image : "",
		callback : "callback"
		
	}
},

{
	name : "sysLand sysLand sysLand",
	explan : "系统管理员登录",
	path : "sysAdminCtr.php",
	method : "request method:  get",
	parameter : {
		c : "sysAdminCtr",  
		a : "sysland", 
		token: "",
		sys_name: "",		
		sys_pwd: "",
		callback: "callback"
	}
},
{
	name : "JyAdd JyAdd JyAdd",
	explan : "增加教研员  ",
	path : "sysAdminCtr.php",
	method : "request method:  get",
	parameter : {
		c : "sysAdminCtr",  
		a : "addJy",
		jy_telephone : "",
		jy_pwd :"",
		jy_city : "",
		token: "",
		callback : "callback"
	}
},
{
	name : "addCity",
	explan : "增加城市",
	path : "sysAdminCtr.php",
	method : "request method:  get",
	parameter : {
		c : "sysAdminCtr",  
		a : "addCity",
		token: "",
		area_city:"",
		callback : "callback"
	}
},
{
	name : "queryCity",
	explan : "查询城市",
	path : "qureyCityCtr.php",
	method : "request method:  get",
	parameter : {
		c : "queryCityCtr",  
		a : "queryCity",
		token: "",
		area_city : "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服添加课程操作",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "addCourse",
		token:"",
		order_tid:"",
		class_count:"",
		class_start_date:"",
		class_start_time:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服取消课程操作",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "cancelCourse",
		token:"",
//		date:"",
		class_tid:"",
		order_tid:"",
		callback : "callback"
	}
},
{
	name : "jydelete  jydelete  jydelete ",
	explan : "删除教研信息",
	path : "JYUserInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYUserInfoCtr",
		a : "jydelete",
		token: "",
		tid : "1",
		callback : "callback"
	}
},
{
	name : "jydelete  jydelete  jydelete ",
	explan : "添加特色课程信息",
	path : "FeatureClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "FeatureClassCtr",
		a : "addFeatureClass",
		class_num:"",
		high_quality_name:"",
		class_hour:"",
		high_quality_price:"",
		high_quality_introduce:"",
		city:"",
		class_type:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "删除老师信息",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "deleteTeacher",
		tid:"",
		token: "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "根据tid查询老师信息",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "queryTeacher",
		user_tid:"",
		token:"",
		teacher_tid:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "更新老师岗位状态",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "updateTeacherState",
		user_tid:"",
		token:"",
		teacher_num:"",
		state:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "更新老师级别",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "updateTeacherLevel",
		user_tid:"",
		token:"",
		teacher_num:"",
		level:"",
		callback : "callback"
	}
},
//{
//	name : "Get JSAPI Para",
//	explan : "学生查询老师信息",
//	path : "teacherInfoCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "teacherInfoCtr",
//		a : "queryTeacher",
//		tid : "",
//		teacher_name : "",
//		teacher_name_en : "",
//		teacher_sex : "",
//		teacher_age : "",
//		teacher_area : "",
//		student_age_max : "",
//		student_age_min : "",
//		teacher_seniority : "",
//		teacher_image : "",
//		student_grade_max : "",
//		student_grade_min : "",
//		teacher_city : "",
//		teacher_district : "",
//		teacher_town : "",
//        callback : "callback"
//	}
//},
//{
//	name : "Get JSAPI Para",
//	explan : "查询老师信息",
//	path : "JYTeacherInfoCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "JYTeacherInfoCtr",
//		a : "queryTeacher",
//		tid : "",
//		//jy_telephpne:"",
//		jy_city:"",
//		jy_token:"",
//		
//		callback : "callback"
//	}
//},
{
	name : "Get JSAPI Para",
	explan : "增加老师信息",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "addTeacher",
		user_tid:"",
		token: "",
		teacher_name : "",
		teacher_seniority : "",
		student_grade_min : "",
		student_grade_max : "",
		teacher_area : "",
		teacher_sex : "",
		teacher_city : "",
		teacher_district : "",
		teacher_town : "",
		teacher_ontime_evaluation:"准时足时",
		teacher_appearance_evaluation:"师容师表",
		teacher_lesson_evaluation:"备课充分",
		teacher_num:"老师编号",
		telephone : "电话",
		address:"地址",
		graduated_from:"毕业院校",
		graduation_date:"毕业时间",
		teacher_major:"主修课程",
		teacher_hiredate:"入职时间",
		post_status:"岗位状态",
		teachers_level:"教师级别",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "修改老师信息",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "updateTeacher",
		user_tid:"",
		token: "",
		teacher_tid:"",
		teacher_name : "",
		teacher_name_en : "",
		teacher_sex : "",
		teacher_age : "",
		teacher_area : "",
		teacher_seniority : "",
		teacher_image : "",
		student_grade_max : "",
		student_grade_min : "",
		teacher_district : "",
		teacher_town : "",
		telephone:"",
		login_pwd:"",
		teacher_num:"",
		teacher_hiredate:"",
		address:"",
		graduated_from:"",
		graduation_date:"",
		teacher_major:"",
		teacher_information_1:"",
		teacher_information_2:"",
		teacher_information_3:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "修改老师信息",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "updateTeacherDetail",
		user_tid:"",
		token:"",
		teacher_tid:"",
		teacher_information_1:"",
		teacher_information_2:"",
		teacher_information_3:"",
		teacher_idea:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "教师登陆",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "landingTeacher",
		telephone : "",
		login_pwd : "",
		token: "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "按条件查询市場專員業績",
	path : "SCDateCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCDateCtr",
		a : "queryScDate",
		time : "時間  全部 不傳 今天傳1 本週傳2 本月傳3",
		state: "全部 不傳   在職傳0 離職1",
		name:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "按条件查询註冊的人",
	path : "SCDateCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCDateCtr",
		a : "queryIndex",
		time : "全部不傳 本日1 本周2 本月3",
		state: "全部不傳 註冊1 快速約課2 免費試課3 付費上課4",
		telephone:"",
		sc_num:"客服专员的编号",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "更改市場專員的在職",
	path : "SCDateCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCDateCtr",
		a : "updateScLevel",
		sc_num : "市場專員的編號",
		state: "0是在職 1是離職",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "按条件查询註冊的人",
	path : "alertCtr.php",
	method : "request method:  get",
	parameter : {
		c : "alertCtr",
		a : "alert",
	
		callback : "callback"
	}
},

{
	name : "Get JSAPI Para",
	explan : "查询全国老师信息",
	path : "JYTeacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYTeacherInfoCtr",
		a : "queryAllTeacher",
		user_tid:"",
		token: "",
		page:"",
		teacher_num:"",
		teacher_name : "",
		post_status:"",
		teacher_hiredate:"",
		teachers_level:"",
		teacher_city:"",
		begin_date:"",
		end_date:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询全国老师信息",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "queryAllTeacher",
		token :"",
		page:"",
		tid:"",
		teacher_name : "",
		teacher_name_en : "",
		teacher_sex : "",
		teacher_age : "",
		teacher_area : "",
		student_age_max : "",
		student_age_min : "",
//		teacher_class_fees:"",
		teacher_seniority : "",
//      teacher_image : "",
		student_grade_max : "",
		student_grade_min : "",
		teacher_city : "",
		teacher_district : "",
		teacher_town : "",
		telephone : "",
		callback : "callback"
	}
},
//{
//	name : "Get JSAPI Para",
//	explan : "分页",
//	path : "displayPagingCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "displayPagingCtr",
//		a : "displayPaging",
//		
//		page:"",
//		
//		callback : "callback"
//	}
//},
//{
//	name : "Get JSAPI Para",
//	explan : "第一次登陆修改密码",
//	path : "JYTeacherInfoCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "JYTeacherInfoCtr",
//		a : "updatePwdFirst",
//		
//		token: "",
//		tid:"",
//		pwd:"",
//		callback : "callback"
//	}
//},
{
	name : "Get JSAPI Para",
	explan : "第一次登陆修改密码",
	path : "JYUserInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "JYUserInfoCtr",
		a : "updatePwdFirst",
		token: "",
		tid:"",
		pwd:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "修改市场专员信息",
	path : "SCMarketerCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCMarketerCtr",
		a : "updateMarketer",
		token: "",
		tid:"",
		sc_name : "",
		sc_name_en : "",
		sc_sex : "",
		sc_age : "",
		sc_city : "",
		sc_district : "",
		sc_town : "",
		sc_pwd:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "市场专员登录",
	path : "SCMarketerCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCMarketerCtr",
		a : "landMarketer",
		token: "",
		sc_telephone:"",
		sc_pwd:"",
		callback:"callback"
	}

},

{
	name : "Get JSAPI Para",
	explan : "第一次登陆修改密码",
	path : "SCMarketerCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCMarketerCtr",
		a : "updatePwdFirst",
		tid:"",
		token: "",
		pwd:"",
		callback : "callback"
	}
},

{
	name : "Get JSAPI Para",
	explan : "同城市的市场专员查看同城市的用户注册量",
	path : "SCMarketerCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCMarketerCtr",
		a : "queryRegister",
		sc_city:"",
		token: "",
		begin_date:"",
		end_date:"",
		callback:"callback"
	}
},
//{
//	name : "Get JSAPI Para",
//	explan : "老师课程达到70节课后，占用余下时间",
//	path : "ChangeCourseCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "ChangeCourseCtr",
//		a : "returnInformation",
//		teacher_tid:"",
//		callback:"callback"
//	}
//},

//{
//	name : "Get JSAPI Para",
//	explan : "客服安排课程表",
//	path : "ChangeCourseCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "ChangeCourseCtr",
//		a : "courseScheduling",
//		//user_name:"",
//		//class_count:"",
//		order_tid:"",
//		class_start_date:"",
//		class_start_time:"",	
//		callback:"callback"
//	}
//},

{
	name : "Get JSAPI Para",
	explan : "添加市场专员",
	path : "SCManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCManageCtr",
		a : "addMarketer",
		sc_city:"",
		token: "",
		sc_telephone:"",
		sc_num:"",
		callback:"callback"
	}
},
{
	name : "Get JSAPI Para",

	explan : "公共登录接口",
	path : "CPublicLogin.php",
	method : "request method:  get",
	parameter : {
		c : "CPublicLogin",
		a : "landMarketer",
		login_type:"",
		token: "",
		telephone:"",
		pwd:"",
		callback : "callback"			
	
	}
},
{
	name : "Get JSAPI Para",

	explan : "公共登录接口",
	path : "CPublicLogin.php",
	method : "request method:  get",
	parameter : {
		c : "CPublicLogin",
		a : "landAll",
		telephone:"",
		pwd:"",
		callback : "callback"			
	
	}
},
{
	name : "Get JSAPI Para",

	explan : "公共登录接口",
	path : "CPublicLogin.php",
	method : "request method:  get",
	parameter : {
		c : "CPublicLogin",
		a : "updatePwdFirst",
		tid:"",
		token:"",
		pwd:"",
		callback : "callback"			
	
	}
},
{
	name : "Get JSAPI Para",

	explan : "添加课程显示下过订单的所有学生姓名",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "showName",
		token: "",
		callback : "callback"		
	
	}


},

{
	name : "Get JSAPI Para",
	explan : "市场管理员登陆",
	path : "SCManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCManageCtr",
		a : "scAdminLand",
		
		sc_telephone: "",
		sc_pwd : "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "添加市场专员",
	path : "SCManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCManageCtr",
		a : "addMarketer",
		token: "",
		sc_telephone: "",
		sc_pwd : "",
		sc_city:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "删除市场专员",
	path : "SCManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCManageCtr",
		a : "deleteMarketer",
		token: "",
		tid:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询全国市场专员信息",
	path : "SCManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCManageCtr",
		a : "queryAllMarketer",
		token: "",
		tid:"",
		sc_name : "",
		sc_name_en : "",
		sc_sex : "",
		sc_age : "",
		sc_city : "",
		sc_district : "",
		sc_town : "",
		sc_telephone : "",
		callback : "callback"
	}

},
{
	name : "Get JSAPI Para",
	explan : "修改手机号码",
	path : "sysAdminCtr.php",
	method : "request method:  get",
	parameter : {
		c : "sysAdminCtr",
		a : "updateTel",
		telephone:"",
		type:"",
		token: "",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询拼课",
	path : "KFfightoffCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFfightoffCtr",
		a : "queryFightOff",
		token:"",
		page:"页数",
		user_name:"",
		fightoffstate:"拼课状态 0正在拼课 1拼课完成 2已经上课3拼课取消",
		classes:"年级 0-12",
		classtime:"拼课时间 一天传1 3天传3 一周传7 一个月传30 三个月内传90 ",
		fightoffnum:"参与人数 几个人传几",
		usertype:"是否有一对一课程 0为有 1为没",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询拼课详情",
	path : "KFfightoffCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFfightoffCtr",
		a : "queryFightOffDetails",
		token:"",
		page:"",
		order_tid:"",
		spelling_state:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "发起人的详情",
	path : "KFfightoffCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFfightoffCtr",
		a : "queryInitiatorDetails",
		token:"",
		order_tid:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "取消拼课",
	path : "KFfightoffCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFfightoffCtr",
		a : "cancelFightOff",
		token:"",
		order_tid:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "转换拼课",
	path : "KFfightoffCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFfightoffCtr",
		a : "conversion",
		token:"",
		order_tid:"",
		callback : "callback"
	}
},{
	name : "Get JSAPI Para",
	explan : "转换拼课",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "arrangeClass",
		token:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "测试专用",
	path : "test.php",
	method : "request method:  get",
	parameter : {
		c : "test",
		a : "login",
		telephone:"",
		pwd:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "测试专用",
	path : "test.php",
	method : "request method:  get",
	parameter : {
		
		c : "test",
		a : "query",
		tid:"",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "测试专用",
	path : "test2.php",
	method : "request method:  get",
	parameter : {
		
		c : "test2",
		a : "login",
		telephone:"",
		login_pwd:"",
		callback : "callback"
	}
},
{
	name : "下分界线",
	explan : "下分界线",
	path : "",
	method : "request method:  get",
	parameter : {
		c : "↑↑↑↑↑↑请注意！以上是李坡写的↑↑↑↑↑",		
	}
},
{
	name : "Get JSAPI Para",
	explan : "教师订单查询",
	path : "teacherConfirmation.php",
	method : "request method:  get",
	parameter : {
		c : "teacherConfirmation",
		a : "queryOrderInfo",
		tid : "",
//		user_name : "",
//		user_sex : "",
//		user_garde : "",
//		order_type : "",
//		order_time : "",
//		order_address : "",
//        order_phone : "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "订单详情查询  tid是order_tid",
	path : "teacherConfirmation.php",
	method : "request method:  get",
	parameter : {
		c : "teacherConfirmation",
		a : "classRecordInfo",
		tid : "",
//		teacher_name : "",
//		user_name: "",
//		user_garde : "",
//		order_type : "",
//		order_time : "",
//		order_address : "",
//        order_phone : "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "教师确认  tid为class_tid",
	path : "teacherConfirmation.php",
	method : "request method:  get",
	parameter : {
		c : "teacherConfirmation",
		a : "addOrderInfoCtr",
        tid:"",
		//order_tid : "",
		//class_start_date:"",
	    //class_start_time:"",
//	    class_start_time2:"",
//		class_end_time1:"",
//		class_end_time2:"",
		// state:"",
		//class_no : "",
		//teacher_confirm : "",
		//user_confirm:"",

		callback : "callback"
	}
},
//{
//	name : "resetDownStat",
//	explan : " **仅用于调试** 重置各类型设备下载量",
//	path : "originTypeSataCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "originTypeSataCtr",
//		a : "resetDownStat",
//	}
//}, 
 
{
	name : "The personnel of the service login",
	explan : "教师上传教案",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "teacherEvaluation",  //教师评价
		a : "addTeacherGrammar",    //教师教案上传
		order_tid:"",
		class_start_date:"",
		class_start_time:"",
		teacher_grammar:""
	}
}, 
{
	name : "The personnel of the service login",
	explan : "教师确认上课",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "teacherEvaluation",  //教师评价
		a : "updateTeacherClass",    //教师确认完成上课
		tid:""
		
	}
}, 
 
//{
//	name : "The personnel of the service login",
//	explan : "预约自动生成课程记录",
//	path : "automatic.php",
//	method : "request method:  get",
//	parameter : {
//		c : "automatic",  //预约自动生成课程
//		a : "addOrderList", //查询order_list 插入class_list
//		tid:"",
//		//order_tid:"",
//		//teacher_tid : "1",
//		//user_tid : "1",
//		order_date : "2015-05-18",
//		order_time : "06:03:07",
//		//order_type : "1",
//		//order_money : "150",
//		//order_remark : "",
//		//order_phone : "",
//		order_address:""
//		
//	}
//}, 
{
	name : "The personnel of the service login",
	explan : " 客服人员登陆",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "serPerCtr",
		a : "login",
		serPer_phone: "",
		serPer_passwd: "",
		serPer_token: "",
		callback: ""			
	}
}, 
{
	name : "The personnel of the service login",
	explan : " 客服查询订单",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "serPerCtr",
		a : "queryOrder",
		callback: ""
	}
}, 
{
	name : "query examination paper",
	explan : " 查询试题信息  -根据年级查询试题信息-",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "examPaperCtr",
		a : "queryExamPaper",
		tid: "",
		exam_grade: "",
		callback: ""
	}
}, 
{
	name : "add examination paper",
	explan : " 添加试题信息",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "examPaperCtr",
		a : "addExamPaper",
		exam_grade: "",
		exam_title: "",
		exam_content: "",
		callback: ""
	}
}, 
{
	name : "delete examination paper",
	explan : " 删除试题信息  -根据年级删除试题信息",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "examPaperCtr",
		a : "deleteExamPaper",
		tid: "",
		callback: ""
	}
}, 
{
	name : "update examination paper",
	explan : " 修改试题信息 -根据年级修改试题信息",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "examPaperCtr",
		a : "updateExamPaper",
		tid: "",
		exam_grade: "",
		exam_title: "",
		exam_content: "",
		callback: ""
	}
}, 
{
	name : "The teacher ask for leave",
	explan : " 教师请假 不能请假返回false | 可以请假返回true,并修改time_busy为2",
	path : "index.php",
	method : "request method:  get",
	parameter : {
		c : "teacherLeaveCtr",
		a : "leave",
		teacher_tid: "2",
		leave_start_date : "2015-06-28",
		//leave_start_time : "08:00",
		leave_end_date : "2015-06-30",
		//leave_end_time : "13:30",
		audit:"",
		callback: "callback"
	}
},

{
	name : "add teacher Introduction",
	explan : "新增教师简介信息; tid是老师的tid",
	path : "TeacherIntroduction.php",
	method : "request method:  get",
	parameter : {
		c : "TeacherIntroduction",  //新增教师简介
		a : "addIntroduction",  //新增教师简介
		
		teacher_intro: "",	
		teacher_tid: "",
//		t_image_url_1:"",
//		t_image_url_2:"",
		teacher_information_title_1:"",
		teacher_information_1:"",
		teacher_information_title_2:"",
		teacher_information_2:"",
		teacher_information_title_3:"",
		teacher_information_3:"",
//		teacher_information_title_4:"",
		teacher_idea:"",
		callback: "callback"
	}
},
{
	name : "gmLand gmLand gmLand",
	explan : "教师端管理员登陆  --吴",
	path : "gmTeacher.php",
	method : "request method:  get",
	parameter : {
		c : "gmTeacher",  //新增教师简介
		a : "gmLand",  //新增教师简介
//		tid: "",
		gm_user: "",		
		gm_pwd: ""
	}
},
{
	name : "gmLand gmLand gmLand",
	explan : "版本查询--安卓，微信，ios",
	path : "versionInfo.php",
	method : "request method:  get",
	parameter : {
		c : "versionInfo",  //版本查询
		a : "queryVersionInfo",  //新增教师简介
		//tid: "",
		version_source: "",		
		version_number: ""
	}
},

{
	name : "userCourseCalendar",
	explan : "查询当月接口  传学生ID --user_tid",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "userCourseCalendar",  // 学生课程日历页面
		a : "queryCourse",  //  查询当月课程
		tid: "",
		callback: "callback"
	}
},
{
	name : "userCourseCalendar",
	explan : "次月课程调整接口",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "userCourseCalendar",  // 学生课程日历页面
		a : "userMakeAdjustments",  // 次月课程调整
		 user_tid:"",
//		 order_tid:"",            //订单id
		 user_classes:"传调课类型",
		 date: "传客户端当前日期 年月日",
		 user_classes_need:"传家长需求",   //家长次月调课需求
		 callback: "callback"
	}
},
//{
//	name : "userCourseCalendar",
//	explan : "客服返回次月课程调整结果",
//	path : "userCourseCalendar.php",
//	method : "request method:  get",
//	parameter : {
//		c : "userCourseCalendar",  // 学生课程日历页面
//		a : "returnMakeAdjustments",  // 返回次月调整结果
//		 user_tid: "" ,
//		 user_classes:"传调课类型",
//		 user_classes_need:"传家长需求",
//		 callback: "callback"
//	}
//},
{
	name : "userCourseCalendar",
	explan : "家长要求加课  ",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "userCourseCalendar",  // 学生课程日历页面
		a : "userAddClass",  //家长要求加课
		  user_tid:"",
//		  order_tid:"",
		  user_classes: "传课程类型",     
		  user_classes_need:"家长需求",
		  callback: "callback"
	}
},
//{
//	name : "userCourseCalendar",
//	explan : "客服返回加课结果",
//	path : "userCourseCalendar.php",
//	method : "request method:  get",
//	parameter : {
//		c : "userCourseCalendar",  // 学生课程日历页面
//		a : "returnAddClass",  //客服返回加课结果
//		  tid: ""
//		
//	}
//},
{
	name : "userCourseCalendar",
	explan : "家长提出本月换课",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "userCourseCalendar",  // 学生课程日历页面
		a : "userChangeClass",  //家长提出本月换课
		user_tid:"",
		user_classes: "传课程类型",       
		class_tid:"课程id",     
		date: "客户端当前日期 年月日",                //接受客户端时间
		class_date:"原上课日期",         //原上课日期
		class_time:"原上课时间",         //原上课时间
		hope_date:"想要换课的日期",         //想要换课的日期
		hope_time:"想要换课的时间" ,         //想要换课的时间
		callback: "callback"
	}
},
//{
//	name : "userCourseCalendar",
//	explan : "客服返回换课结果",
//	path : "userCourseCalendar.php",
//	method : "request method:  get",
//	parameter : {
//		c : "userCourseCalendar",  // 学生课程日历页面
//		a : "returnChangeClass",  //客服返回换课结果
//		tid:""
//		
//	}
//},
{
	name : "userCourseCalendar",
	explan : "家长原上课日期  返回教室忙闲状态",
	path : "userCourseCalendar.php",
	method : "request method:  get",
	parameter : {
		c : "userCourseCalendar",  // 学生课程日历页面
		a : "teachersStateTime",  //返回教师繁忙状态
		
		teacher_tid:"",
		startDate:"",            //接收开始日期
		endDate:"",              //接受结束日期
		callback: "callback"
	}
},

{
	name : "Get JSAPI Para",
	explan : "家长确认过后的课程列表-->user_tid",
	path : "usersCourseRecord.php",
	method : "request method:  get",
	parameter : {
		c : "usersCourseRecord",
		a : "userConfirmedCourse",
		tid : "",
		//order_tid : "",
		// class_start_time:"",
		// class_end_time:"",
		// state:"",
		//class_no : "",
		// teacher_confirm:"",
		//user_confirm : "",

		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "家长未确认过的课程列表--》user_tid",
	path : "usersCourseRecord.php",
	method : "request method:  get",
	parameter : {
		c : "usersCourseRecord",
		a : "userUnrecognizedCourse",
		tid : "",
		//order_tid : "",
		// class_start_time:"",
		// class_end_time:"",
		// state:"",
		//class_no : "",
		// teacher_confirm:"",
		//user_confirm : "",

		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "家长确认,tid为class_list的tid",
	path : "usersCourseRecord.php",
	method : "request method:  get",
	parameter : {
		c : "usersCourseRecord",
		a : "updateClassInfoCtr",
		tid : "",
		//order_tid : "",
		// class_start_time:"",
		// class_end_time:"",
		// state:"",
		//class_no : "",
		// teacher_confirm:"",
		//user_confirm : "",

		callback : "callback"
	}
},
{
	name : "The personnel of the service login",
	explan : "//家长对老师评价   每次确认后。。。 class tid",
	path : "usersCourseRecord.php",
	method : "request method:  get",
	parameter : {
		c : "usersCourseRecord",  //家长评价
		a : "addUsersEvaluation", //家长评价提交
		//tid:"",
		//user_tid:"",
		//teacher_tid:"",
		tid:"",
		order_tid:"",
		teacherOntime:"",        //教师准时评价
		lessonPlanReady:"",      //教案准备评价
		classroomInteraction:"", //课内互动评价
		user_evaluation:"" ,  //家长评价的具体内容
		callback: "callback"
	}
},

{
	name : "Get JSAPI Para",
	explan : "统计家长剩余未上课的所有课时数--》user_tid",
	path : "usersCourseRecord.php",
	method : "request method:  get",
	parameter : {
		c : "usersCourseRecord",
		a : "userClassSurplusNum",
		tid : "",
		//order_tid : "",
		// class_start_time:"",
		// class_end_time:"",
		// state:"",
		//class_no : "",
		// teacher_confirm:"",
		//user_confirm : "",

		callback : "callback"
	}
},



{
	name : "dongdongaini",
	explan : " 录入城市年级信息   获取城市自动分为12年级",
	path : "cityUnitPriceCtr.php",
	method : "request method:  get",
	parameter : {
		c : "cityUnitPriceCtr",
		a : "addCity",
		class_city: "上海",
//		class_grade: "",
		callback: "callback"
	}
},
{
	name : "dongdongaini",
	explan : " 根据指定城市，查询年级和单价",
	path : "cityUnitPriceCtr.php",
	method : "request method:  get",
	parameter : {
		c : "cityUnitPriceCtr",
		a : "queryCity",
		class_city: "上海",
//		class_grade_category: "初中",
		callback: "callback"
	}
}, 
{
	name : "dongdongaini ",
	explan : " 根据指定城市，查询购买数量，以及其对应的折扣 //暂没用到",
	path : "cityUnitPriceCtr.php",
	method : "request method:  get",
	parameter : {
		c : "cityUnitPriceCtr",
		a : "queryClassDiscount",
		class_city: "",
//		class_year_month: "",
		callback: ""
	}
},
{
	name : "cityDiscountCtr ",
	explan : " 根据城市查询折扣率和次数",
	path : "cityDiscountCtr.php",
	method : "request method:  get",
	parameter : {
		c : "cityDiscountCtr",
		a : "query",
		class_city: "",
		class_year_month: "",
		callback: ""
	}
},


//{
//	name : "Get JSAPI Para",
//	explan : "查询老师头像URL",
//	path : "teacherInfoCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "teacherInfoCtr",
//		a : "queryTeacherImg",
//		tid : "",		
//		callback : "callback"
//	}
//},

{
	name : "userCourseCalendar",
	explan : "教师上课统计   ---教师ID",
	path : "JS_classStatistics.php",
	method : "request method:  get",
	parameter : {
		c : "JS_classStatistics",  //教师上课统计
		a : "teacherIndex",  //查询教师课程信息
		tid:"",    //传教师id
		date:"",
		callback: "callback"
	}
},
{
	name : "userCourseCalendar",
	explan : "教师课程日历安排 teacher——tid",
	path : "JS_classStatistics.php",
	method : "request method:  get",
	parameter : {
		c : "JS_classStatistics",  //教师上课统计
		a : "teacherCurriculum",  //查询教师课程信息
		tid:"",    //传教师id
		date:"当前日期",   //当前时间
		teacher_class:"老师总课数",
		callback: "callback"
	}
},
{
	name : "userCourseCalendar",
	explan : "教师调休  教师当月总课数达到70，其他无视" +
			"返回申请状态0、1 (家长端对应显示0、1)每时间断只能调整一次，需提前24小时申请",
	path : "JS_classStatistics.php",
	method : "request method:  get",
	parameter : {
		c : "JS_classStatistics",  //教师上课统计
		a : "teacherPaidLeave",  //查询教师课程信息
		tid:"传教师id",    //传教师id
		teacher_nowdate:"教师当前日期",   
		teacher_date:"教师选择的需要调的那节课的日期", 
		teacher_time:"教师选择的需要调的那节课的时间", 
		
		callback: "callback"
	}
},

{
	name : "userCourseCalendar",
	explan : "教师查看学生评价 class_tid",
	path : "JS_classStatistics.php",
	method : "request method:  get",
	parameter : {
		c : "JS_classStatistics",  //教师上课统计
		a : "userEvaluationResults",  //  查看评价
		tid:"class_tid",    //传课程id
		//teacher_grammar:"教案图片",
		
		callback: "callback"
	}
},
{
	name : "userCourseCalendar",
	explan : "教师个人信息 ",
	path : "JS_classStatistics.php",
	method : "request method:  get",
	parameter : {
		c : "JS_classStatistics",  //教师上课统计
		a : "teacherPersonalInformation",  // 教师查看个人信息
		tid:"teacher_tid",    //传教师id
		//teacher_grammar:"教案图片",		
		callback: "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "增加潜在客户信息",
	path : "KFpotentialInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFpotentialInfoCtr",
		a : "addpotential",
		user_tid : "",
		token:"",
		tid:"",
		potential_name : "",
		potential_phone : "",
		potential_city : "",
		potential_area : "",
		potential_streets : "",
		potential_change : "",
		potential_date : "",
		potential_number : "",
		potential_address : "",
		potential_register : "",
		user_class :"年级  一年级", 
		user_grade :"年级 类型  初中高", 
		callback : "callback"
	}
}, 
{
	name : "Get JSAPI Para",
	explan : "修改潜在客户状态",
	path : "KFpotentialInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFpotentialInfoCtr",
		a : "state",
		user_tid : "",
		token:"",
		tid:"潜在客户的tid",
	    callback : "callback"
	}
}, 
{
	name : "Get JSAPI Para",
	explan : "查询潜在客户信息",
	path : "KFpotentialInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFpotentialInfoCtr",
		a : "queryAllPotential",
		user_tid : "",
		token:"",
		page:"",
		tid:"",
		potential_name:"",
		potential_phone:"",
		potential_city:"",
		potential_area:"",
		potential_streets:"",
		potential_change:"",
		potential_date:"",
		start_date:"",
		end_date:"",
		potential_number:"",
		potential_address:"",
		potential_register :"",
		callback : "callback"
	}
}, 
{
	name : "Get JSAPI Para",
	explan : "添加回访记录",
	path : "KFreturnInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFreturnInfoCtr",
		a : "addreturn",
		user_tid : "",
		token:"",
		tid:"",
		return_time : "",
		return_content : "",
		return_remark : "",
		return_name : "",
		return_phone : "",
		city : "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询回访记录",
	path : "KFreturnInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFreturnInfoCtr",
		a : "queryAllreturn",
		user_tid : "",
		token:"",
		page:"",
		tid:"",
		return_time : "",
		return_name : "",
		return_phone : "",
		order_list_sum : "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "回访记录接口值",
	path : "KFreturnInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFreturnInfoCtr",
		a : "queryreturn",
		user_tid : "",
		token:"",
		return_name : "",
		return_phone: "",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "根据回访次数查询用户",
	path : "KFreturnInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFreturnInfoCtr",
		a : "queryReturnByVisitTimes",
		user_tid : "",
		token:"",
		callback : "callback",
		return_count : ""		
	}
},

{
	name : "Get JSAPI Para",
	explan : "显示剩余课程数",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "showLessonNumber",
		order_tid:"",
		//class_content:"",
		callback : "callback",
		
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服排课查看",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "KFTeacherCurriculum",
		tid:"",
		date:"",
		callback : "callback",
		
	}
},
//{
//	name : "Get JSAPI Para",
//	explan : "显示课程总数",
//	path : "ChangeCourseCtr.php",
//	method : "request method:  get",
//	parameter : {
//		c : "ChangeCourseCtr",
//		a : "showAllNumber",
//		order_tid:"",
//		class_count:"",
//		callback : "callback",
//		
//	}
//}

{
	name : "Get JSAPI Para",
	explan : "客服查询订单",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "queryOrder",
		callback : "callback",
		token:"",		
		page:"",
		pay_done : "",
		order_state:"",	
		tid:"",
		telephone:"",
		teacher_name:"",
		user_name:"",
		user_city : "" ,
		user_area : "" ,		
		class_content: "" ,
		class_count : "" ,
		class_way : "" ,
		date:"",		
		ramainingHour : "" ,		
		return_count : "",
		having_teacher:"",
		ifexport:"",
		filetype:"",
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服主管取消订单",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "cancelOrder",
		callback : "callback",	
		token:"",
		tid:""				
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服订单转入",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "switchOrder",
		callback : "callback",	
		token:"",
		tid:""				
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服更改订单异常状态",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "updateNormalState",
		callback : "callback",	
		token:"",
		tid:"",
		normal_state:""	
				
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服手动添加订单",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "addOrder",
		callback : "callback",	
		token:"",
		user_name:"",		
		user_city : "" ,
		user_area : "" ,
		teacher_name:"",			
		class_content:"",		
		class_way : "" ,		
		class_count :"",
		order_address : "" ,
		order_phone : "" ,		
		order_money : "" ,
		pay_type: "" ,
		order_date : "" ,		
		order_time : "" ,
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服获取教师姓名列表",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "getTeacherName",
		callback : "callback",	
		token:"",
		user_city : "" 
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服手动添加订单时获取课程信息列表",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "getCourse",
		callback : "callback",	
		token:"",
		user_city : "" 
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服手动添加订单时根据城市和年级查询课程价格",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "getCoursePrice",
		callback : "callback",	
		token:"",
		user_city : "",
		class_content:"学生年级",
		class_count:"课程信息"
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服人员为短期课程的订单分配教师",
	path : "KFOrderListCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFOrderListCtr",
		a : "addOrderTeacher",
		callback : "callback",	
		token:"",		
		order_tid:"",
		teacher_tid:""
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服管理员登录",
	path : "KFManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFManageCtr",
		a : "kfAdminLand",		
		kf_admin_telephone : "",
		kf_admin_pwd : "",
		callback : "callback"
	}

},

{
	name : "Get JSAPI Para",
	explan : "客服管理员查询所属客服",
	path : "KFManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFManageCtr",
		a : "queryKfUser",
		page:"",
		kf_admin_token:"",				
		callback : "callback"
	}
},

{
	name : "Get JSAPI Para",
	explan : "客服管理员删除所属客服",
	path : "KFManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFManageCtr",
		a : "deleteKfUser",		
		kf_admin_token:"",
		tid : "",		
		callback : "callback"
	}

},
{
	name : "Get JSAPI Para",
	explan : "客服管理员增加所属客服",
	path : "KFManageCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFManageCtr",
		a : "addKfUser",			
		kf_admin_token:"",
		kf_name : "",
		kf_telephone : "",
		kf_pwd: "",		
		callback : "callback"
	}

},
{
	name : "Get JSAPI Para",
	explan : "客服专员登录",
	path : "KFUserCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFUserCtr",
		a : "KfUserLand",
		kf_telephone : "",	
		kf_pwd: "",		
		callback : "callback"
	}

},
{
	name : "Get JSAPI Para",
	explan : "第一次登陆修改密码",
	path : "KFUserCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFUserCtr",
		a : "updatePwdFirst",
		token:"",
		pwd:"",
		callback : "callback"
	}
},
/*{
	name : "Get JSAPI Para",
	explan : "客服回复老师调休",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "queryTeacherRest",
		teacher_entry_state:"",
		teacher_tid:"",
		teacher_name:"",
		begin_date:"",
		end_date:"",
		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "老师调休操作",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "teacherRest",
		
		teacher_tid:"",	
		confirm:"",
		teacher_entry_state:"",
		callback : "callback"
	}

}*/
{
	name : "Get JSAPI Para",
	explan : "客服查询学生换课申请",
	path : "KFReplaceClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFReplaceClassCtr",
		a : "queryApply",
		callback : "callback",
		token:"",	
		page:"",
		user_name:"",
		user_classes : "" ,				
		service_reply_state: "" ,		

		start_time:"",
		end_time : "" ,	 

		date:""			 

	}
},
{
	name : "Get JSAPI Para",
	explan : "客服更改学生换课申请状态",
	path : "KFReplaceClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFReplaceClassCtr",
		a : "updateApplyState",
		callback : "callback",	
		token:"",
		tid:"",
		service_reply_state:"",
		service_reply:"",
				
	}

},
{
	name : "Get JSAPI Para",
	explan : "客服查询教师调休申请",
	path : "KFReplaceClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFReplaceClassCtr",
		a : "queryTeacherApply",
		callback : "callback",
		token:"",
		page:"",
		teacher_name:"",
		teacher_entry_state : "" ,				
		date:""	,	 
	}
},
{
	name : "Get JSAPI Para",
	explan : "客服更改教师调休申请状态",
	path : "KFReplaceClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "KFReplaceClassCtr",
		a : "updateTeacherApplyState",
		callback : "callback",	
		token:"",
		tid:""	,			
	}

},

{
	name : "Get JSAPI Para",
	explan : "客服查询教师课程表",
	path : "ChangeCourseCtr.php",
	method : "request method:  get",
	parameter : {
		c : "ChangeCourseCtr",
		a : "queryCourse",
		kf_tid:"",
		kf_token:"",
		kf_city:"",
		teacher_tid:"",
		callback : "callback"
	}
},


{
	name : "Get JSAPI Para",
	explan : "移动框架设计：根据应用平台、城市、容器名字,查询最新版本的容器以及该容器下所有最新版小应用的相关信息(包括下载URL)",
	path : "DownloadCtr.php",
	method : "request method:  get",
	parameter : {
		c : "DownloadCtr",
		a : "queryNewALLUrl",		
		callback : "callback",		
		container_name : "",		
		city :"",
		platform : ""		
	}
},
{
	name : "Get JSAPI Para",
	explan : "移动框架设计：查询指定版本的容器及该容器下所有小应用的下载地址",
	path : "DownloadCtr.php",
	method : "request method:  get",
	parameter : {
		c : "DownloadCtr",
		a : "queryGivenVersionUrl",		
		callback :"callback",		
		container_name : "",
		container_version : "",	
	}
},
{
	name : "Get JSAPI Para",
	explan : "移动框架设计：新增容器",
	path : "DownloadCtr.php",
	method : "request method:  get",
	parameter : {
		c : "DownloadCtr",
		a : "addContainer",		
		callback :"callback",	
		type:"student或者teacher或者OA",
		container_name : "studentContainer",
		container_filetype:"apk",
		container_version : "1.0.0",
		container_platform : "3",
		container_detaile : "移动框架学生端容器",		
		container_force : "1",		
	}
},
{
	name : "Get JSAPI Para",
	explan : "移动框架设计：新增指定容器下的小应用",
	path : "DownloadCtr.php",
	method : "request method:  get",
	parameter : {
		c : "DownloadCtr",
		a : "addSmallApp",		
		callback :"callback",
		type:"student或者teacher或者OA",
		zip_name : "student",
		zip_filetype:"zip",		
		zip_version : "1.0.0",		
		zip_platform : "3",
		zip_city:"",
		zip_detaile : "移动框架学生端容器下的小应用",		
		zip_force : "1",
		container_tid:""		
	}
},


{
	name : "getTeacherList",
	explan : "获取老师列表。teacher_class_fees 课时单价",
	path : "teacherInfoCtr.php",
	method : "getTeacherList ",
	parameter : {
		c : "teacherInfoCtr",
		a : "getTeacherList",
		callback : "callback",
		teacher_city : "上海",
		teacher_district : "浦东",
		teacher_town : "全区",
		student_grade_max : "4",
		student_grade_min : "1",
		class_start_time:"每节课开始时间格式08:00"
	}
}, 
{
	name : "getTeacherDetailInfo",
	explan : "获取老师列表--简介",
	path : "teacherInfoCtr.php",
	method : "getTeacherDetailInfo ",
	parameter : {
		c : "teacherInfoCtr",
		a : "getTeacherDetailInfo",
		callback : "callback",
		teacher_tid : ""
	}
},
{
	name : "Get JSAPI Para",
	explan : "学生查询老师信息",
	path : "teacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "teacherInfoCtr",
		a : "queryTeacher",
		tid : "",
		teacher_name : "",
		teacher_name_en : "",
		teacher_sex : "",
		teacher_age : "",
		teacher_area : "",
		student_age_max : "",
		student_age_min : "",
		teacher_seniority : "",
		teacher_image : "",
		student_grade_max : "",
		student_grade_min : "",
		teacher_city : "",
		teacher_district : "",
		teacher_town : "",
        callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询老师头像URL",
	path : "teacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "teacherInfoCtr",
		a : "queryTeacherImg",
		tid : "",		
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询每天上课时间段",
	path : "teacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "teacherInfoCtr",
		a : "classTimeEveryDay",
		//tid : "",		
		callback : "callback"
	}
},

{
	name : "Get JSAPI Para",
	explan : "内部人工对教师评价",
	path : "teacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "teacherInfoCtr",
		a : "teacherArtificialEvaluation",
		teacher_tid : "",		
		callback : "callback"
	}
},
{
	name : "chenmengfan",
	explan : "根据不同城市，老师性别，上课年级，所在区，所在街道筛选不同的老师信息（陈梦帆PC端）",
	path : "teacherInfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "teacherInfoCtr",
		a : "filterteacher",
		teacher_city : "老师所在城市",
		teacher_sex : "老师性别",
		student_grade_max : "老师所教的最大年级",
		student_grade_min : "老师所教的最小年级",
		teacher_district : "老师所在区",
		teacher_town : "老师所在街道",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "客户端根据城市及邀请码，从服务器返回验证信息，包括是否有效、折扣率",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "askInvitation",
		token :"",
		callback : "callback",
		city : "上海",
		invitation_code :"123456"
	}
},
{
	name : "Get JSAPI Para",
	explan : "市场专员或主管查询本城市的邀请码信息",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "queryInvitationinfo",
		token :"",
		page:"",
		callback : "callback",		
		state :"1",
		invitation_name:""
	}
},
{
	name : "Get JSAPI Para",
	explan : "市场主管导出本城市的邀请码信息",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "exportFile",
		token :"",		
		callback : "callback",		
		state :"",
		tid : "",
		filetype:""
	}
},
{
	name : "Get JSAPI Para",
	explan : "市场主管或市场专员查询某一类邀请码的详细使用列表",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "askInvitationUseList",
		token :"",
		page:"",
		callback : "callback",		
		state :"1",
		invitation_code:"",
		invitation_info_tid:"1"
	}
},
{
	name : "Get JSAPI Para",
	explan : "市场主管创建邀请码",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "createInvitation",
		token :"",
		callback : "callback",		
		invitation_name :"",
 		invitation_amount : "",
 		invitation_discount : "",
 		invitation_times:"",
 		invitation_start_date	: " ",
 		invitation_end_date : "" ,
 		invitation_city : "" ,
 		invitation_code_prefix : ""
	}
},
{
	name : "Get JSAPI Para",
	explan : "市场主管使某一类邀请码失效",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "invitationInvalid",
		token :"",
		callback : "callback",		
		tid :"" 			
	}
},
{
	name : "Get JSAPI Para",
	explan : "记录邀请码的使用信息",
	path : "SCInvitationCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCInvitationCtr",
		a : "recordInvitationUse",		
		callback : "callback",		
		order_tid :"",
		invitation_code:""
	}
},
{
	name : "Get JSAPI Para",
	explan : "查询学生是否还有正在进行的一对一课程",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "userClassSurplusNum",		
		user_tid :"学生id",		

		callback : "callback"
	}
},

{
	name : "dongdongaiani",
	explan : "评课信息  发起评课页面",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "spellingLessonInformation",		
		teacher_tid:"",
		user_tid:"",
		user_name:"",
		user_phone:"",
		spelling_lesson_number:"评课总人数",
		//participants_number:"参与人数",
		user_venue:"学生上课地点",
		course_package:"课程套餐",		
		teaching_week:"上课时间  周几上课",
		teacher_time:"上课时间",
		user_message:"留言",		
		teaching_grade:"初中",
		teache_class:"1年级",
		//original_price:"原价",
		//actual_price:"实际价格",
		//spelling_type:"3拼客",
		//class_discount_tid:"",
		longitude : "经度（隐藏传参）",
		latitude : "维度（隐藏传参）",
		class_quality:"拼客类型  0普通课  1精品课  默认0 ",
		high_quality_courses_tid:"精品课ID",
		callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "拼课详情",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "query",		
		tid :"user_spelling_lesson_tid",	
		class_quality:"拼客类型  0普通课  1精品课  默认0",
		high_quality_courses_tid:"精品课ID",
		//city:"当前城市  如'上海'",
        callback : "callback"
	}
},
{
	name : "Get JSAPI Para",
	explan : "参与者寻找拼课 type=1为寻找拼课列表 | type=2为我的拼课记录 --- sort=1为距离最近 | sort=2为时间最新",
	path : "testCtr.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "querySpellingLesson",
		callback : "callback",
		type : "",
		user_tid : "",
		class_city : "",
		class_district : "",
		class_place : "",
		class_grade : "",
		sort : "",
		longitude : '121.525333（传参条件sort=1）',
		latitude : '31.180603（传参条件sort=1）',		
	}
},
{
	name : "dongdongaini",
	explan : "参与者加入拼客团伙",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "addSpellingLesson",
		
		user_tid:"",
		user_spelling_lesson_tid:"评课团伙的id号",
		user_name : "",
		//user_telephome:"",
		user_remark:"留言",
		class_quality:"拼客类型  0普通课  1精品课  默认0 ",
		high_quality_courses_tid:"精品课ID",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "查询当前时间后一周的教师时间状态  ",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "queryTimeState",
		teacher_tid:"",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "快速约客",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "fastAboutClass",
		
		potential_name:"姓名",
		market_code:"市场代码",
		potential_city:"城市",
		potential_area:"区",
		potential_streets:"街道",
		potential_address:"详细地址",
		user_grade:"初中",
		user_class:"1-12年级",
		user_tid:"",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "根据城市获取拼客折扣信息",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "querySpellingDiscount",
		city:"上海",
		
		
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "学生取消拼课  ",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "cancelSpellingLesson",
		tid:"拼客信息tid",
		user_tid:"",
		//student_identity:"学生身份 订单详情有返回 spelling_lesson_type 0发起人 1参与者",
		
		
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "参与者支付时判断发起人是否已经支付完成 ",
	path : "userSpellingLesson.php",
	method : "request method:  get",
	parameter : {
		c : "userSpellingLesson",
		a : "theSponsorsPay",
		tid:"拼客信息tid",
		user_tid:"",
		callback : "callback"
	}
},
{
	name : "chenmengfan",
	explan : "老师头像查询",
	path : "Favicon.php",
	method : "request method:  get",
	parameter : {
		c : "Favicon",
		a : "queryteacher",
		tid : "老师的tid",
		callback : "callback"
	}
},
{
	name : "chenmengfan",
	explan : "学生头像查询",
	path : "Favicon.php",
	method : "request method:  get",
	parameter : {
		c : "Favicon",
		a : "queryuser",
		tid : "学生的tid",
		callback : "callback"
	}
},

{
	name : "sgj",
	explan : "从客户端ip地址解析出城市信息",
	path : "getClientInfo.php",
	method : "request method:  get",
	parameter : {
		c : "getClientInfo",
		a : "getClientCity",
		callback : "callback"
	}
},

{
	name : "dongdongaini",
	explan : "精品课程类型列表  根据城市获取",
	path : "highQualityCourses.php",
	method : "request method:  get",
	parameter : {
		c : "highQualityCourses",
		a : "getHighQualityList",
		city:"上海",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "获取各个精品课程的授课内容   根据精品课程类型的id",
	path : "highQualityCourses.php",
	method : "request method:  get",
	parameter : {
		c : "highQualityCourses",
		a : "getHighQualityContentInfo",
		tid:"high_quality_courses_tid",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "各种精品课程的上课方式   根据精品课程类型的id",
	path : "highQualityCourses.php",
	method : "request method:  get",
	parameter : {
		c : "highQualityCourses",
		a : "getHighQualityClassWay",
		tid:"high_quality_courses_tid",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "//获取精品课详情 简介",
	path : "highQualityCourses.php",
	method : "request method:  get",
	parameter : {
		c : "highQualityCourses",
		a : "getClassQualityDetails",
		tid:"high_quality_courses_tid",
		callback : "callback"
	}
},
{
	name : "dongdongaini",
	explan : "//获取一周上课时间表 仅供选择",
	path : "highQualityCourses.php",
	method : "request method:  get",
	parameter : {
		c : "highQualityCourses",
		a : "getWeekTime",
		//tid:"high_quality_courses_tid",
		callback : "callback"
	}
},
{
	name : "chenemngfan",
	explan : "新增容器版本信息",
	path : "VersioninfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "VersioninfoCtr",
		a : "addContainer",
		type : "容器类型student、teacher、OA",
		container_platform : "容器可应用平台1为安卓2为苹果",
		container_name : "容器的名字",
		container_filetype : "容器包的文件名后缀",
		container_version : "容器的版本号如1.0.1",
		container_detaile : "容器的详细描述",
		callback : "callback"
	}
}, 
{
	name : "chenemngfan",
	explan : "新增指定容器下的小应用的版本信息",
	path : "VersioninfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "VersioninfoCtr",
		a : "addSmallApp",
		type : "小应用类型student、teacher、OA",
		container_tid : "小应用依赖的容器tid",
		zip_platform : "小应用的可应用平台1为安卓2为苹果",
		zip_name : "小应用的的名字",
		zip_filetype : "小应用包的文件名后缀",
		zip_version : "小应用的版本号如1.0.1",
		zip_detaile : "小应用的详细描述",
		callback : "callback"
	}
},
{
	name : "chenemngfan",
	explan : "根据应用平台、APP端口、版本信息模糊搜索查询符合条件的容器版本的相关信息",
	path : "VersioninfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "VersioninfoCtr",
		a : "queryALLContainer",		
		container_platform : "容器可应用平台1为安卓2为苹果",
		container_name : "容器的名字student、teacher、OA",
		container_version : "容器的版本号如1.0.1",
		callback : "callback"
	}
},
{
	name : "chenemngfan",
	explan : "根据版本号查询符合条件的小应用版本的相关信息",
	path : "VersioninfoCtr.php",
	method : "request method:  get",
	parameter : {
		c : "VersioninfoCtr",
		a : "querySmallApp",
		container_tid : "小应用所依赖容器的tid",
		zip_version : "容器的版本号如1.0.1",
		callback : "callback"
	}
},


{
	name : "chenemngfan",
	explan : "查询短期课程的基本信息",
	path : "FeatureClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "FeatureClassCtr",
		a : "querycourse",
		high_quality_name : "短期课程的名称",
		class_hour : "课时",
		create_time : "创建时间",
		high_quality_price : "课程单价（元/课时）",
		city : "城市",
		state : "状态有效或无效",
		user_tid : "",
		token:"",
		callback : "callback"
	}
},
{
	name : "chenemngfan",
	explan : "修改短期课程的使用状态",
	path : "FeatureClassCtr.php",
	method : "request method:  get",
	parameter : {
		c : "FeatureClassCtr",
		a : "state",
		tid : "短期课程的tid",
		user_tid : "",
		token:"",
		callback : "callback"
	}
},
{
	name : "sgj",
	explan : "市场主管管理市场专员及统计地推结果：按条件查询或导出市场专员的基本信息",
	path : "SCDataCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCDataCtr",
		a : "queryScUser",
		time : "时间  全部：不传；今天：1 ；本周：2； 本月：3",
		state: "全部：不传；   在职：0；离职：1",
		name:"",
		ifexport:"是否导出  导出：1；不导出：0  默认为0",
		filetype:"导出文件类型  doc: word文档； txt：记事本文件；xls: Excel文件 （默认为xls）",
		callback : "callback"
	}
},
{
	name : "sgj",
	explan : "市场主管管理市场专员及统计地推结果：按条件查询或导出某一个市场专员的详细地推结果",
	path : "SCDataCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCDataCtr",
		a : "querySingleScUser",
		time : "时间  全部：不传；今天：1 ；本周：2； 本月：3",
		state: "全部：不传；注册：1；快速约课：2；免费试课：3；付费上课：4",
		telephone:"",
		sc_num:"市场专员的编号",
		ifexport:"是否导出  导出：1；不导出：0  默认为0",
		filetype:"导出文件类型  doc: word文档； txt：记事本文件；xls: Excel文件 （默认为xls）",
		callback : "callback"
	}
},
{
	name : "sgj",
	explan : "市场主管管理市场专员及统计地推结果：更改市场专员在职状态",
	path : "SCDataCtr.php",
	method : "request method:  get",
	parameter : {
		c : "SCDataCtr",
		a : "updateScLevel",
		sc_num : "市场专员的编号號",
		state: "  在职：0；离职：1",		
		callback : "callback"
	}
},


];




