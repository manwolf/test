/**
 * Created by liu on 2015/8/14.
 */
var teachingResearchControllers = angular.module('teachingResearchControllers', ['httpService']);

//OA主页
teachingResearchControllers.controller('defaultController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        loading();
        $('html,body').width('100%').height('100%');
        $scope.jiaoyan = function () {
            window.location.assign('#/JyDefault');
        };//跳转到教研登录页面
        $scope.shichang = function () {
            window.location.assign('#/SCmanage');
        };//跳转到市场登录页面
        $scope.kefu = function () {
            window.location.assign('#/customer_data');
        }//跳转到客服登录界面
        $scope.renshi = function () {
            alert("敬请期待")
            //window.location.assign('#/SClogin');
        };
        $scope.xingzheng = function () {
            alert("敬请期待")
            //window.location.assign('#/SClogin');
        };
        $scope.chanpin = function () {
            alert("敬请期待")
            //window.location.assign('#/SClogin');
        };
        $scope.caiwu = function () {
            alert("敬请期待")
            //window.location.assign('#/SClogin');
        };
    }]);



/*****教研系统***********教研系统***********教研系统***********教研系统***********教研系统***********教研系统******/


//教研系统主页
teachingResearchControllers.controller('JyDefaultController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        //从缓存中取数据
        var jy_tid = window.localStorage.getItem(OATag +"tid");
        var jy_token = window.localStorage.getItem(OATag +"token");
        var jy_city = window.localStorage.getItem(OATag +"city");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        if (jy_city == null || jy_city == "null" || jy_city == undefined) {
            jy_city = '';
        } else {
            jy_city = window.localStorage.getItem(OATag +"jy_city");
        }
        //分页
        $scope.load=function(page){
            $http.jsonp(url + 'c=JYTeacherInfoCtr&a=queryAllTeacher&user_tid='+jy_tid+'&token=' + jy_token
                + '&page=' + page + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            //数据库中：1表示“男”，0表示“女”
                            e.teacher_sex = e.teacher_sex == 1 ? '男' : '女';
                            //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
                            e.post_status= e.post_status==0?'实习':(e.post_status= e.post_status==1?'在职':'离职');
                            //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
                            if(e.teachers_level==0){
                                e.teachers_level='初级教师';
                            }else if(e.teachers_level==1){
                                e.teachers_level='中级教师';
                            }else if(e.teachers_level==2){
                                e.teachers_level='高级教师';
                            }else if(e.teachers_level==3){
                                e.teachers_level='特级教师';
                            }
                            $scope.totalpage = data.pages;
                        })
                    }
                });
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);//查询第一页
            $scope.currentpage=1;//当前页第一页
        };
        //上一页
        $scope.currentpage = 1;//设置当前页为第一页
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;//点击上一页当前页自动加一
                $scope.load($scope.currentpage);//再用当前页查询数据
            }
        };
        //下一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;//如果当前页码大于1，点击下一页当前页面自动减1
                $scope.load($scope.currentpage);//再用当前页面查询数据
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.load($scope.totalpage);
            $scope.currentpage=$scope.totalpage;//设置当前页码为总页数，跳转到最后一页
        };
//查询教师和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
//跳转到编辑教师信息页面
        $scope.updateTeacher=function (item){
            var a=confirm("您是否要编辑该教师信息？")
            if(a==true){
                window.location.assign('#/updateTeacher')
                window.localStorage.setItem(OATag+"edteacher_tid",item.tid);
            }
        }
        //教师评价
        $scope.evaluationTeacher=function (item){
            var a=confirm("您是否要对该教师评价？")
            if(a==true){
                window.location.assign('#/teacherEvaluation')
                window.localStorage.setItem(OATag+"edteacher_tid",item.tid);
            }
        }
//  调整教师岗位状态
        $scope.updateTeacherState=function (){
            //将各职位状态转化为对应数据表里的数据
            //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
            if($scope.post_status=='实习'){
                $scope.post_status=0;
            }else if($scope.post_status=='在职'){
                $scope.post_status=1;
            }else if($scope.post_status=='离职'){
                $scope.post_status=2;
            }
           var  Params='c=JYTeacherInfoCtr&a=updateTeacherState&user_tid='+jy_tid+'&teacher_num='+$scope.teacher_num+'' +
                '&state='+$scope.post_status+'&token=' + jy_token;
            eTHttp.resultData(Params)
                .success(function (data) {
                    if (data.code == 0) {
                        alert("调整成功");
                        logUtil('teacher_num------>',$scope.teacher_num);
                        logUtil('post_status------>',$scope.post_status);
                        logUtil('teachers_level------>',$scope.teachers_level);
                        //调整成功岗位状态后更新页面显示
                        $scope.load(1);
                    }
                    else {
                        alert(data.msg);
                    }
                });
        }
//调整教师级别
        $scope.updateTeacherLevel=function (){
            //将各教师级别转化为对应数据表里的数据
            //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
            if($scope.teachers_level=='初级教师'){
                $scope.teachers_level=0;
            }else if($scope.teachers_level=='中级教师'){
                $scope.teachers_level=1;
            }else if($scope.teachers_level=='高级教师'){
                $scope.teachers_level=2;
            }else if($scope.teachers_level=='特级教师'){
                $scope.teachers_level=3;
            }
           var Params='c=JYTeacherInfoCtr&a=updateTeacherLevel&user_tid='+jy_tid+'&teacher_num='+$scope.teacher_num+'' +
                '&level='+$scope.teachers_level+'&token=' + jy_token;
            eTHttp.resultData(Params)
                .success(function (data) {
                    if (data.code == 0) {
                        alert("调整成功");
                        //修改教师级别后更新页面内容
                        $scope.load(1);
                    }
                    else {
                        alert(data.msg);
                    }
                });
        }
//快速查询
        $scope.speedsearch=function(){
            //快速查询，筛选条件不填设置为空
            if(isEmptyStr($scope.teacher_name)){
                $scope.teacher_name="";
            }
            if(isEmptyStr($scope.time_type)){
                $scope.time_type="";
            }else if($scope.time_type=="入职时间"){
                $scope.time_type=0;
            }else if($scope.time_type=="离职时间"){
                $scope.time_type=1;
            }
            if(isEmptyStr($scope.teacher_num)) {
                $scope.teacher_num="";
            }
            if(isEmptyStr($scope.begin_date)) {
                $scope.begin_date="";
            }
            if(isEmptyStr($scope.end_date)) {
                $scope.end_date="";
            }
            if(isEmptyStr($scope.post_status)) {
                $scope.post_status="";
                //将各职位状态转化为对应数据表里的数据
                //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
            }else  if($scope.post_status=='实习'){
                $scope.post_status=0;
            }else if($scope.post_status=='在职'){
                $scope.post_status=1;
            }else if($scope.post_status=='离职'){
                $scope.post_status=2;
            }
            //将各教师级别转化为对应数据表里的数据
            //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
            if(isEmptyStr($scope.teachers_level)) {
                $scope.teachers_level="";
            }else if($scope.teachers_level=='初级教师'){
                $scope.teachers_level=0;
            }else if($scope.teachers_level=='中级教师'){
                $scope.teachers_level=1;
            }else if($scope.teachers_level=='高级教师'){
                $scope.teachers_level=2;
            }else if($scope.teachers_level=='特级教师'){
                $scope.teachers_level=3;
            }
            $http.jsonp(url + 'c=JYTeacherInfoCtr&a=queryAllTeacher&user_tid='+jy_tid+'&token='+ jy_token
                + '&teacher_name='+$scope.teacher_name+'&teacher_num='+$scope.teacher_num
                +'&begin_date='+$scope.begin_date+'&end_date='+$scope.end_date+'&post_status='+$scope.post_status
                +'&teachers_level='+$scope.teachers_level+'&time_type='+$scope.time_type+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            //数据库中：1表示“男”，0表示“女”
                            e.teacher_sex = e.teacher_sex == 1 ? '男' : '女';
                            //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
                            e.post_status= e.post_status==0?'实习':(e.post_status= e.post_status==1?'在职':'离职');
                            //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
                            if(e.teachers_level==0){
                                e.teachers_level='初级教师';
                            }else if(e.teachers_level==1){
                                e.teachers_level='中级教师';
                            }else if(e.teachers_level==2){
                                e.teachers_level='高级教师';
                            }else if(e.teachers_level==3){
                                e.teachers_level='特级教师';
                            }
                            $scope.totalpage = data.pages;
                        })
                    }else{
                        alert(data.msg)
                    }
                });
        }
    }]);
//增加教师详情页面
teachingResearchControllers.controller('teacherDetailsController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        loading();
        var jy_tid = window.localStorage.getItem(OATag +"tid");
        var jy_token = window.localStorage.getItem(OATag +"token");
        var jy_city = window.localStorage.getItem(OATag +"city");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        if (isEmptyStr(jy_city)) {
            jy_city = '';
        }
        //查询城市
        $http.jsonp(url + 'c=areaListCtr&a=queryCity&callback=JSON_CALLBACK')
            .success(function (data) {
                if (data.code == 0) {
                    $scope.lists=data.data;
                    $scope.city = [];//创建城市数组
                    $scope.lists.forEach(function(v,k){
                        this.push(v.area_city);
                    },$scope.city);//将查询返回数据中城市数据存入创建的城市数组
                    logUtil("city------->",$scope.city)
                }
            });
        //查询区
        $scope.queryDistrict=function(){
               $http.jsonp(url + 'c=areaListCtr&a=queryDistrict&area_city='+$scope.teacher_city+'&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {
                           $scope.lists=data.data;
                           $scope.district=[];//创建区数组
                            $scope.lists.forEach(function(v,k){
                                this.push(v.area_district);
                            },$scope.district);//将查询返回数据中区数据存入创建的区数组
                            logUtil("district------->",$scope.district)
                    }
                      });
               }
        //查询镇
        $scope.queryTown=function(){
            $http.jsonp(url + 'c=areaListCtr&a=queryTown&area_city='+$scope.teacher_city
                +'&area_district='+$scope.teacher_district+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists=data.data;
                        $scope.town=[];//创建镇数组
                        $scope.lists.forEach(function(v,k){
                            this.push(v.area_town);
                        },$scope.town);//将查询返回数据中镇数据存入创建的镇数组
                        logUtil("town------->",$scope.town)
                    }
                });
        }
        //教师评价
        rat('star1','result1',1);//准时足时
        rat('star2','result2',1);//师容师表
        rat('star3','result3',1);//备课充分
        function rat(star,result,m){
            star= '#' + star;
            result= '#' + result;
            $(result).show();//将结果展示出
            $(star).raty({
                hints: ['1','2', '3', '4', '5'],
                path: "public/images/jiaoyan",//图片路径
                starOff: 'star-off-big.png',//点击之前的图片
                starOn: 'star-on-big.png',//点击之后的图片
                size: 24,//图片大小
                start: 40,
                showHalf: true,
                target: result,//结果显示到
                targetKeep : true,//targetKeep 属性设置为true，用户的选择值才会被保持在目标DIV中，否则只是鼠标悬停时有值，而鼠标离开后这个值就会消失
                click: function (score, evt) {
                    //直接取值
                  logUtil("star + score------->",star+" "+score);
                    if(star=="#star1"){//准时足时
                       $scope.teacher_num1=score;
                    }else if(star=="#star2"){//师容师表
                        $scope.teacher_num2=score;
                    }else if(star=="#star3"){//备课充分
                        $scope.teacher_num3=score;
                    }
                }
            });
        }

        //上传头像
        $scope.uploadImg = function(){
            /*** 上传头像***/
            $(function () {
                'use strict';
                $('#file').fileupload({
                    url: imgUrl + 'type=1' + '&teacher_phone='//上传的路径
                    + $scope.teacher_telephone,//$scope.teacher_telephone从添加成功后返回数据取电话值
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断上传的图片格式
                    success: function (data) {
                        alert("头像上传成功");
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //修改原来默认url为上传成功后图片的url
                        document.getElementById("img").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
        //添加教师生活照1
        $scope.uploadFamilyPhotoOne = function(){
            $(function () {
                'use strict';
                var uploadPhotoUrl = imgUrl + 'type=2&Teacher_detail_image_no=1&teacher_phone='
                    + $scope.teacher_telephone;//$scope.teacher_telephone从添加成功后返回数据取电话值
                $('#file2').fileupload({
                    url: uploadPhotoUrl,//上传路径
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//图片格式判断
                    success: function (data) {
                        alert("生活资料图片1上传成功");
                        console.log("upload successFamily1------>" + JSON.parse(data).data.url);
                        //将返回的url修改到默认图片
                        document.getElementById("img2").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
        //添加教师生活照2
        $scope.uploadFamilyPhotoTwo = function() {
            $(function () {
                'use strict';
                var uploadPhotoUrl = imgUrl + 'type=2&Teacher_detail_image_no=2&teacher_phone='
                    + $scope.teacher_telephone;//$scope.teacher_telephone从添加成功后返回数据取电话值
                $('#file3').fileupload({
                    url: uploadPhotoUrl,//上传路径
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断图片格式
                    success: function (data) {
                        alert("生活资料图片2上传成功");
                        console.log("upload successFamily2------>" + JSON.parse(data).data.url);
                        //修改原来默认url为上传成功后图片的url
                        document.getElementById("img3").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
        //添加教师基本信息
        $scope.addIntroduction = function () {
            logUtil("评价1----------》", $scope.teacher_num1);
            logUtil("评价2----------》", $scope.teacher_num2);
            logUtil("评价3----------》", $scope.teacher_num3);
            //isEmptyStr函数是判断所传参数是否为空或未定义
            if (isEmptyStr($scope.teacher_name)) {
                alert("请填写教师姓名");
                return;
            }
            if (isEmptyStr($scope.teacher_seniority)) {
                alert("请填写教师教龄");
                return;
            }
            if (isEmptyStr($scope.student_grade_min)) {
                alert("请填写教师授课最小年级");
                return;
            }
            if (isEmptyStr($scope.student_grade_max)) {
                alert("请填写教师授课最大年级");
                return;
            }
            if (isEmptyStr($scope.teacher_city)) {
                alert("请填写教师所在城市");
                return;
            }
            logUtil("teacher_city------>",$scope.teacher_city)
            if (isEmptyStr($scope.teacher_sex)) {
                alert("请选择教师性别");
                return;
            }
            if($scope.teacher_sex=='男'){
                $scope.teacher_sex=1;
            }else if($scope.teacher_sex=='女'){
                $scope.teacher_sex=0;
            }
            if(isEmptyStr($scope.teacher_idea)){
                alert("请填写教师箴言");
                return;
            }
            if (isEmptyStr($scope.teacher_intro)) {
                alert("请填写教学特色");
                return;
            }
            if (isEmptyStr($scope.teacher_success)) {
                alert("请填写成功案例");
                return;
            }
            if (isEmptyStr($scope.teacher_feature)) {
                alert("请填写教师愿景");
                return;
            }
            if (isEmptyStr($scope.teacher_num1)) {
                alert("请为准时准点打分");
                return;
            }
            if (isEmptyStr($scope.teacher_num2)) {
                alert("请为师容师表打分");
                return;
            }
            if (isEmptyStr($scope.teacher_num3)) {
                alert("请为备课充分打分");
                return;
            }
            if (isEmptyStr($scope.teacher_num)) {
                alert("请填写教师编号");
                return;
            }
            if (isEmptyStr($scope.telephone)) {
                alert("请填写教师联系电话");
                return;
            }
            if (isEmptyStr($scope.address)) {
                alert("请填写教师住址");
                return;
            }
            if (isEmptyStr($scope.graduated_from)) {
                alert("请填写教师毕业院校");
                return;
            }
            if (isEmptyStr($scope.graduation_date)) {
                alert("请填写教师毕业日期");
                return;
            }
            if (isEmptyStr($scope.teacher_major)) {
                alert("请填写教师专业");
                return;
            }
            if (isEmptyStr($scope.teacher_hiredate)) {
                alert("请填写教师入职时间");
                return;
            }
            if (isEmptyStr($scope.post_status)) {
                alert("请选择教师职位状态");
                return;
            }
            //将各职位状态转化为对应数据表里的数据
            //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
            if($scope.post_status=='实习'){
                $scope.post_status=0;
            }else if($scope.post_status=='在职'){
                $scope.post_status=1;
            }else if($scope.post_status=='离职'){
                $scope.post_status=2;
            }
            if (isEmptyStr($scope.teachers_level)) {
                alert("请选择教师级别");
                return;
            }
            //将各教师级别转化为对应数据表里的数据
            //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
            if($scope.teachers_level=='初级教师'){
                $scope.teachers_level=0;
            }else if($scope.teachers_level=='中级教师'){
                $scope.teachers_level=1;
            }else if($scope.teachers_level=='高级教师'){
                $scope.teachers_level=2;
            }else if($scope.teachers_level=='特级教师'){
                $scope.teachers_level=3;
            }
            $http.jsonp(url + 'c=JYTeacherInfoCtr&a=addTeacher&token=' + jy_token + '&user_tid='+jy_tid+'&teacher_name='+$scope.teacher_name
                + '&teacher_seniority=' + $scope.teacher_seniority + '&student_grade_min='+$scope.student_grade_min
                +'&student_grade_max=' +$scope.student_grade_max + '&teacher_city='+$scope.teacher_city+'&teacher_district='
                +$scope.teacher_district+'&teacher_town='+$scope.teacher_town+'&teacher_sex=' + $scope.teacher_sex
                + '&teacher_num=' + $scope.teacher_num+ '&telephone=' + $scope.telephone+ '&address=' + $scope.address
                + '&graduated_from=' + $scope.graduated_from+ '&graduation_date=' + $scope.graduation_date+
                '&teacher_major=' + $scope.teacher_major+ '&teacher_hiredate=' + $scope.teacher_hiredate+ '' +
                '&post_status=' + $scope.post_status+ '&teachers_level=' + $scope.teachers_level+ '&teacher_ontime_evaluation='+
                $scope.teacher_num1+'&teacher_appearance_evaluation='+$scope.teacher_num2+'&teacher_lesson_evaluation='+
                $scope.teacher_num3+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    logUtil("data.code--------",data.code)
                    if (data.code == 0) {
                            var tid = data.data[0].tid;//传到添加简介teacher_tid
                            $scope.teacher_telephone=data.data[0].telephone;//取返回数据的电话作为上传头像的参数
                        $scope.uploadImg();//上传头像
                        $scope.uploadFamilyPhotoOne();//上传生活资料图片1
                        $scope.uploadFamilyPhotoTwo();//上传生活资料图片2
                            //添加教师简介
                            $http.jsonp(url + 'c=TeacherIntroduction&a=addIntroduction&teacher_tid=' + tid + '&teacher_information_title_1=教学特色' +
                                '&teacher_information_1=' + $scope.teacher_intro + '&teacher_information_title_2=成功案例&teacher_information_2='
                                + $scope.teacher_success + '&teacher_information_title_3=教师愿景&teacher_information_3=' +
                                $scope.teacher_feature + '&teacher_idea='+$scope.teacher_idea+'&user_tid='+jy_tid+'&callback=JSON_CALLBACK')
                                .success(function (data) {
                                    if (data.code == 0) {
                                        alert('已保存教师资料,此教师将在24小时内生效');
                                        reset();
                                    }
                                    else{
                                        alert(data.msg);
                                    }
                                });
                    }else{
                        alert(data.msg);
                    }
                });
        }

    }]);
//编辑教师信息
teachingResearchControllers.controller('updateTeacherController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        loading();
        $('html,body').width('100%').height('100%');
        //从浏览器缓存取数据
        var edteacher_tid=window.localStorage.getItem(OATag+"edteacher_tid");
        var jy_tid = window.localStorage.getItem(OATag +"tid");
        var jy_token = window.localStorage.getItem(OATag +"token");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        var jy_city = window.localStorage.getItem(OATag +"city");
        if (jy_city == null || jy_city == "null" || jy_city == undefined) {
            jy_city = '';
        } else {
            jy_city = window.localStorage.getItem(OATag +"city");
        }

      //  //查询城市
      //  $http.jsonp(url + 'c=areaListCtr&a=queryCity&callback=JSON_CALLBACK')
      //      .success(function (data) {
      //          if (data.code == 0) {
      //              $scope.lists=data.data;
      //              $scope.city = [];//创建城市数组
      //              $scope.lists.forEach(function(v,k){
      //                  this.push(v.area_city);
      //              },$scope.city);//取查询出来的城市数据存到城市数组中
      //              logUtil("city------->",$scope.city)
      //          }
      //      });
      //  //查询区
      //$scope.queryDistrict=function(){
      //    $http.jsonp(url + 'c=areaListCtr&a=queryDistrict&area_city='+$scope.teacher_city+'&callback=JSON_CALLBACK')
      //        .success(function (data) {
      //            if (data.code == 0) {
      //                $scope.lists=data.data;
      //                $scope.district=[];//创建区数组
      //                $scope.lists.forEach(function(v,k){
      //                    this.push(v.area_district);
      //                },$scope.district);//取查询到的城市的辖区数据存到区数组
      //                logUtil("district------->",$scope.district)
      //                logUtil("teacher_city------->",$scope.teacher_city)
      //                logUtil("teacher_district------->",$scope.teacher_district)
      //            }
      //        });
      //}
      //  //查询镇
      //      $scope.queryTown=function(){
      //          $http.jsonp(url + 'c=areaListCtr&a=queryTown&area_city='+$scope.teacher_city+'&area_district='+$scope.teacher_district+'&callback=JSON_CALLBACK')
      //              .success(function (data) {
      //                  if (data.code == 0) {
      //                      $scope.lists=data.data;
      //                      $scope.town=[];//创建镇数组
      //                      $scope.lists.forEach(function(v,k){
      //                          this.push(v.area_town);
      //                      },$scope.town);//把取到的对应城市对应区的所有镇存到镇数组中
      //                      logUtil("town------->",$scope.town)
      //                  }
      //              });
      //      }
        $scope.queryTeacher=function(){
            $http.jsonp(url + 'c=JYTeacherInfoCtr&a=queryTeacher&token=' + jy_token
                + '&teacher_tid='+edteacher_tid+'&user_tid='+jy_tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists=data.data;
                        $scope.editName=data.data[0].teacher_name;
                        $scope.teacher_sex=data.data[0].teacher_sex;
                        //数据库中：1表示“男”，0表示“女”
                        if($scope.teacher_sex==1){
                            $scope.teacher_sex='男';
                        }else if($scope.teacher_sex==0){
                            $scope.teacher_sex='女';
                        }
                        //取数据到编辑界面对应位置
                        $scope.student_grade_max=data.data[0].student_grade_max;
                        $scope.student_grade_min=data.data[0].student_grade_min;
                        $scope.teacher_seniority=data.data[0].teacher_seniority;
                        $scope.teacher_city=data.data[0].teacher_city;
                        $scope.teacher_district=data.data[0].teacher_district;
                        $scope.teacher_town=data.data[0].teacher_town;
                        $scope.telephone=data.data[0].telephone;
                        $scope.teacher_num=data.data[0].teacher_num;
                        $scope.teacher_hiredate=data.data[0].teacher_hiredate;
                        $scope.address=data.data[0].address;
                        $scope.graduated_from=data.data[0].graduated_from;
                        $scope.graduation_date=data.data[0].graduation_date;
                        $scope.teacher_major=data.data[0].teacher_major;
                        $scope.post_status=data.data[0].post_status;
                        $scope.teacher_idea=data.data[0].teacher_idea;
                        //将取出的数据转化为对应的岗位状态
                        //将各职位状态转化为对应数据表里的数据
                        //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
                        if($scope.post_status==0){
                            $scope.post_status='实习';
                        }else if($scope.post_status==1){
                            $scope.post_status='在职';
                        }else if($scope.post_status==2){
                            $scope.post_status='离职';
                        }
                        $scope.teachers_level=data.data[0].teachers_level;
                        //将取出的数据转化为对应的教师级别
                        //将各教师级别转化为对应数据表里的数据
                        //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
                        if($scope.teachers_level==0){
                            $scope.teachers_level='初级教师';
                        }else if($scope.teachers_level==1){
                            $scope.teachers_level='中级教师';
                        }else if($scope.teachers_level==2){
                            $scope.teachers_level='高级教师';
                        }else if($scope.teachers_level==3){
                            $scope.teachers_level='特级教师';
                        }
                        $scope.teacher_information_3=data.data[0].teacher_information_3;
                        $scope.teacher_information_1=data.data[0].teacher_information_1;
                        $scope.teacher_information_2=data.data[0].teacher_information_2;
                        //$scope.queryDistrict();//查询区
                        //$scope.queryTown();//查询镇
                    }
                });
        }
        $scope.queryTeacher();
        //编辑教师信息功能
        $scope.updateTeacher=function(){
            logUtil('editName---->',$scope.editName);
            logUtil('teacher_area---->',$scope.teacher_area);
            logUtil('teacher_sex---->',$scope.teacher_sex);
            logUtil('student_grade_max---->',$scope.student_grade_max);
            //数据库中：1表示“男”，0表示“女”
            if($scope.teacher_sex=='男'){
                $scope.teacher_sex=1;
            }else if($scope.teacher_sex=='女'){
                $scope.teacher_sex=0;
            }//将岗位状态数据重新化为数据库对应的数据
            //数据库中：1表示“在职”，0表示“实习”，2表示“离职”
            if($scope.post_status=='实习'){
                $scope.post_status=0;
            }else if($scope.post_status=='在职'){
                $scope.post_status=1;
            }else if($scope.post_status=='离职'){
                $scope.post_status=2;
            }
             //将各教师级别转化为对应数据表里的数据
            //数据库中：0表示“初级教师”，1表示“中级教师”，2表示“高级教师”，3表示“特级教师”
            if($scope.teachers_level=='初级教师'){
                $scope.teachers_level=0;
            }else if($scope.teachers_level=='中级教师'){
                $scope.teachers_level=1;
            }else if($scope.teachers_level=='高级教师'){
                $scope.teachers_level=2;
            }else if($scope.teachers_level=='特级教师'){
                $scope.teachers_level=3;
            }
            $http.jsonp(url + 'c=JYTeacherInfoCtr&a=updateTeacher&token=' + jy_token
                + '&tid='+edteacher_tid+'&teacher_name='+$scope.editName+'&user_tid='+jy_tid+'&teacher_sex='+
                $scope.teacher_sex+'&student_grade_max='+$scope.student_grade_max+'&student_grade_min='+
                $scope.student_grade_min+'&teacher_seniority='+$scope.teacher_seniority+'&teacher_city='+$scope.teacher_city+
                '&teacher_district='+$scope.teacher_district+'&teacher_town='+$scope.teacher_town+'&telephone='+$scope.telephone+'&teacher_num='+$scope.teacher_num+'' +
                '&teacher_hiredate='+$scope.teacher_hiredate+'&address='+$scope.address+'&graduated_from='+$scope.graduated_from+'' +
                '&graduation_date='+$scope.graduation_date+'&teacher_major='+$scope.teacher_major+'&post_status='+$scope.post_status+'' +
                '&teachers_level='+$scope.teachers_level+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists=data.data;
                        //编辑教师与编辑教师简介一起保存
                        $scope.updateTeacherDet();
                        alert(data.msg)
                    }else{
                        alert(data.msg)
                    }
                });
        }
        //编辑简介
        $scope.updateTeacherDet=function(){
            logUtil('teacher_information_1---->',$scope.teacher_information_1);
            logUtil('teacher_information_2---->',$scope.teacher_information_2);
            logUtil('teacher_information_3---->',$scope.teacher_information_3);
            $http.jsonp(url + 'c=JYTeacherInfoCtr&a=updateTeacherDetail&token=' + jy_token
                + '&tid='+edteacher_tid+'&teacher_information_1='+$scope.teacher_information_1+'&teacher_information_2='
                +$scope.teacher_information_2+'&teacher_information_3='+$scope.teacher_information_3
                +'&teacher_idea='+$scope.teacher_idea+'&user_tid='+jy_tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists=data.data;
                        $scope.queryTeacher();
                    }else{
                        alert(data.msg)
                    }
                });
        }
//上传图片把原来图片覆盖
        $scope.uploadImg = function(){
            /** 上传头像 **/
            $(function () {
                'use strict';
                $('#file').fileupload({
                    url: imgUrl + 'type=1' + '&teacher_phone='
                    + $scope.telephone,//$scope.telephone取查询出来要编辑的用户电话号码
                    autoUpload: true,//自动上传的开关
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断图片的格式
                    success: function (data) {
                        alert("上传头像成功");
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //上传成功返回的url替代原来默认图片
                        document.getElementById("img").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
//添加教师生活照1
        $scope.uploadFamilyPhotoOne = function(){
            $(function () {
                'use strict';
                var uploadPhotoUrl = imgUrl + 'type=2&Teacher_detail_image_no=1&teacher_phone='
                    + $scope.telephone;//$scope.telephone取查询出来要编辑的用户电话号码
                $('#file2').fileupload({
                    url: uploadPhotoUrl,//上传的路径
                    autoUpload: true,//自动上传开关
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断图片格式
                    success: function (data) {
                        alert("上传教师生活资料图片成功")
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //上传成功返回的url替代原来默认图片
                        document.getElementById("img2").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
//添加教师生活照2
        $scope.uploadFamilyPhotoTwo = function() {
            $(function () {
                'use strict';
                var uploadPhotoUrl = imgUrl + 'type=2&Teacher_detail_image_no=2&teacher_phone='
                    + $scope.telephone;
                $('#file3').fileupload({
                    url: uploadPhotoUrl,//上传的路径
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断图片格式
                    success: function (data) {
                        alert("上传教师生活资料图片成功")
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //上传成功返回的url替代原来默认图片
                        document.getElementById("img3").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });

        }

    }]);
//教师评价管理
teachingResearchControllers.controller('teacherEvaluationController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        var token=window.localStorage.getItem(OATag+"token");
        var tid=window.localStorage.getItem(OATag+"tid");
        var city=window.localStorage.getItem(OATag+"city");
        var teacher_tid=window.localStorage.getItem(OATag+"edteacher_tid");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        $scope.load=function(page){
            var params='c=TeacherCommentCtr&a=getCommentListForSingleTeacher&token='
                +token+'&user_tid='+tid+'&page=' + page + '&city='+city+'&teacher_tid='+teacher_tid;
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.totalpage = data.pages;
                        $scope.lists = data.data;
                    }
                    else {
                        alert(data.msg);
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //下一页
        $scope.currentpage = 1;
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;
                $scope.load($scope.currentpage);
            }
        };
        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.load($scope.currentpage);
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage=$scope.totalpage;
            $scope.load($scope.totalpage);
        };
//页数查询
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
        //查询电话号码
        $scope.queryTelephone=function(){
            var params='c=TeacherCommentCtr&a=getRobotList&token='+token+'&user_tid='+tid+'&city='+city;
            eTHttp.resultData(params)
                .success(function(data){
                    if(data.code==0){
                    $scope.telephone1=data.data;
                    $scope.telephone2=[];
                        $scope.telephone1.forEach(function(a,b,c){
                            this.push(a.telephone)},$scope.telephone2)
                    }
                })
        }
        //添加教师评价
        $scope.addEvaluation=function(){
           //判断年是否符合规范
            if(isEmptyStr($scope.evl_y)){
                alert("请填写年");
                return;
            }
            if($scope.evl_y<2015){
                alert("请填写2015年以后的年份");
                return;
            }
            //判断月是否符合规范
            if(isEmptyStr($scope.evl_M)){
                alert("请填写月");
                return;
            }
            if($scope.evl_M<1||$scope.evl_M>12){
                alert("请填写正确的月份");
                return;
            }else if($scope.evl_M>1&&$scope.evl_M<10){
                $scope.evl_M1='0'+$scope.evl_M;
            }else{
                $scope.evl_M1= $scope.evl_M;
            }
            //判断日是否符合规范
            if(isEmptyStr($scope.evl_d)){
                alert("请填写日");
                return;
            }
            if($scope.evl_d<1||$scope.evl_d>31){
                alert("填写正确的天数");
                return;
            }else if($scope.evl_d>0&&$scope.evl_d<10){
                $scope.evl_d1='0'+$scope.evl_d;
            }else{
                $scope.evl_d1=$scope.evl_d;
            }
            if(isEmptyStr($scope.evl_h)){
                alert("请填写时");
                return;
            }
            if($scope.evl_h<0||$scope.evl_h>24){
                alert("请填写正确的小时");
                return;
            }else if($scope.evl_h>=0&&$scope.evl_h<10){
                $scope.evl_h1='0'+$scope.evl_h;
            }else{
                $scope.evl_h1=$scope.evl_h;
            }
            if(isEmptyStr($scope.evl_m)){
                alert("请填写分");
                return;
            }
            if($scope.evl_m<0||$scope.evl_m>60){
                alert("请填写正确的分");
                return;
            }else if($scope.evl_m>=0&&$scope.evl_m<10){
                $scope.evl_m1='0'+$scope.evl_m;
            }else{
                $scope.evl_m1=$scope.evl_m;
            }
            if(isEmptyStr($scope.evl_s)){
                alert("请填写秒");
                return;
            }
            if($scope.evl_s<0||$scope.evl_s>60){
                alert("请填写正确的小时");
                return;
            }else if($scope.evl_s>=0&&$scope.evl_s<10){
                $scope.evl_s1='0'+$scope.evl_s;
            }else{
                $scope.evl_s1= $scope.evl_s;
            }

            var comment_time= $scope.evl_y+"-"+$scope.evl_M1+"-"+$scope.evl_d1+" "+$scope.evl_h1+":"+$scope.evl_m1+":"+$scope.evl_s1;
            var params='c=TeacherCommentCtr&a=addTeacherComment&token='+token+'&user_tid='
                +tid+'&teacher_tid='+teacher_tid+'&telephone='+$scope.telephone+'&star_level='
                +$scope.star_level+'&comment_content='+$scope.comment_content+'&comment_time='+comment_time;
            eTHttp.resultData(params)
                .success(function(data){
                    if(data.code==0){
                        alert(data.msg);
                        $scope.load(1);
                        logUtil("comment_time-------->",comment_time);
                    }else{
                        alert(data.msg);
                    }
                })
        }
        //删除教师评价
        $scope.deleteTeacherEvl=function(item){
           var a=confirm("您确认要删除该条评论吗？")
            if(a==true){
                var params='c=TeacherCommentCtr&a=deleteTeacherComment&token='+token
                    +'&user_tid='+tid+'&tid='+item.tid;
                eTHttp.resultData(params)
                    .success(function(data){
                        if(data.code==0){
                            alert(data.msg);
                            $scope.load(1);
                        }else{
                            alert(data.msg);
                        }
                    })
            }
        }
        //查询单个家长评价
        $scope.queryOneEvaluation=function(item){
            var a=confirm("您确定要对该条评论进行修改吗？")
            if(a==true) {
                var params = 'c=TeacherCommentCtr&a=getCommentListForSingleTeacher&token='
                    + token + '&user_tid=' + tid + '&city=' + city + '&teacher_tid=' + teacher_tid + '&tid=' + item.tid;
                eTHttp.resultData(params)
                    .success(function (data) {
                        if (data.code == 0) {
                            $scope.ed_telephone = data.data[0].telephone;
                            $scope.ed_star_level = data.data[0].star_level;
                            $scope.ed_comment_content = data.data[0].comment_content;
                            $scope.ed_comment_time = data.data[0].comment_time;
                            $scope.ed_tid = data.data[0].tid;
                            $('#mymodele').modal('show');
                        }
                    })
            }
        }
        //修改评论
        $scope.editorEvl=function(){
                var params='c=TeacherCommentCtr&a=editTeacherComment&token='+token+'&user_tid='
                    +tid+'&teacher_tid='+teacher_tid+'&telephone='+$scope.ed_telephone+'&star_level='
                    +$scope.ed_star_level+'&comment_content='+$scope.ed_comment_content
                    +'&comment_time='+$scope.ed_comment_time+'&tid='+$scope.ed_tid;
                eTHttp.resultData(params)
                    .success(function(data){
                        if(data.code==0){
                            alert(data.msg);
                            $scope.load(1);
                        }else {
                            alert(data.msg)
                        }
                    })
        }
    }]);
//查询教师课程日历
teachingResearchControllers.controller('JyTeacherCalendarController',['$scope', '$http', '$location'
    , function ($scope,$http,$location) {
        loading();

        var teacher_tid = $location.search()['teacher_tid'];//获取页面穿过来的教师ID
        logUtil("teacher_tid--------->", teacher_tid);
        var jy_token = window.localStorage.getItem(OATag + "token");
        var tid = window.localStorage.getItem(OATag + "tid");
        //左侧 星期
        $scope.date = [];
        $scope.weeks = [];
        //输出的时间范围“-7”表示当前日期前7天，“60”表示当前日期后60天
        for (var i = -7; i < 60; i++) {
            var obj = {
                week: i == 0 ? '今天' : getCurrentTime(i).week,
                date: getCurrentTime(i).month + '/' + getCurrentTime(i).day,
                full: getCurrentTime(i).full
            }
            $scope.weeks.push(obj);
        }
        //    取数据
        $scope.timeunitDate = [];
        $scope.timeunitTime = [];
        var timeDateOld = '';
        //更新课表
        $scope.searchclass=function(){
            $http.jsonp(url + 'c=ChangeCourseCtr&a=KFTeacherCurriculum&tid='
                + teacher_tid + '&date=' + getCurrentTime(0).full + '&user_tid='
                +tid+'&token=' + jy_token+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        var tempArr = []; //临时的时间数组
                        data.data.forEach(function (e, i, a) {
                            var flag = false;
                            var schedule_date = e.schedule_date;// 日期
                            var schedule_time = e.schedule_time;// 时间
                            var time_busy = e.time_busy;// 忙闲状态
                            var user_name = e.user_name;// 学生名字
                            var class_content = e.class_content;// 上课内容
                            var class_tid = e.class_tid;//课程id
                            //根据状态判断课表是否有课
                            var teacher_entry_state = e.teacher_entry_state == 0 ? '未排课' : (e.teacher_entry_state == 1 ? '排课中' : '排课成功');
                            if (i == 0) {
                                timeDateOld = schedule_date;
                            }
                            if (schedule_date != timeDateOld) {
                                //维护一个时间数组
                                var tempBusy = 1;
                                var tempObj = null;
                                tempArr.forEach(function (e, i, a) {
                                    if (e.time_busy == 0) {
                                        //只要有一个闲就是闲
                                        tempBusy = 0;
                                    }
                                })
                                tempObj = timeDateOld;
                                $scope.timeunitDate.push(tempObj);
                                $scope.timeunitTime.push(tempArr);
                                tempArr = [];
                                //更新timeDateOld
                                timeDateOld = schedule_date;
                            }
                            //添加到临时时间数组中
                            tempArr.push({
                                schedule_date: schedule_date,
                                schedule_time: schedule_time,
                                time_busy: time_busy,
                                user_name: user_name,
                                class_content: class_content,
                                teacher_entry_state: teacher_entry_state,
                                class_tid: class_tid
                            });
                            if (i == a.length - 1) {
                                //最后一个的时候
                                //维护一个时间数组
                                var tempBusy = 1;
                                var tempObj = null;
                                tempArr.forEach(function (e, i, a) {
                                    if (e.time_busy == 0) {
                                        //只要有一个闲就是闲
                                        tempBusy = 0;
                                    }
                                })
                                tempObj = timeDateOld;
                                $scope.timeunitDate.push(tempObj);
                                $scope.timeunitTime.push(tempArr);
                            }
                        })
                        //定位 第几行
                        $scope.timeunitDate.forEach(function (e, i, a) {
                            $scope.weeks.forEach(function (f, j, b) {
                                if (e == f.full) {
                                    //第几行
                                    f.time_content = $scope.timeunitTime[i];
                                }
                            })
                        })
                    }
                })
        }
        //查询课表
        $scope.searchclass();
        //回到今天
        $scope.goTody = function () {
            $('body').animate({
                scrollTop:0
            })
        }
        $scope.goTody();
    }]);
//短期课程管理
teachingResearchControllers.controller('shortCourseManageController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        var token=window.localStorage.getItem(OATag+"token");
        var tid=window.localStorage.getItem(OATag+"tid");
        var city=window.localStorage.getItem(OATag+"city");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        $scope.load=function(page){
            var speedsearch=arguments[1];
            var params='c=FeatureClassCtr&a=querycourse&token='+token+'&user_tid='+tid+'&page=' + page + '&city='+city;
            if(speedsearch==1){
                if(isEmptyStr($scope.coursename)){
                    alert("请填写您要查询的课程名")
                    return;
                }
                params='c=FeatureClassCtr&a=querycourse&token='+token
                    +'&user_tid='+tid+'&high_quality_name='+$scope.coursename+'&city='+city;
            }
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.totalpage = data.pages;
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            if(e.state==0) {
                                e.state="有效";
                            }else if(e.state==1){
                                e.state="失效";
                            }
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //下一页
        $scope.currentpage = 1;
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;
                $scope.load($scope.currentpage);
            }
        };
        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.load($scope.currentpage);
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage=$scope.totalpage;
            $scope.load($scope.totalpage);
        };
//页数查询
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
//修改课程发布状态
        $scope.courseState=function (item) {
           var a=confirm("是否转化该课程状态？")
            if(a==true){
                $http.jsonp(url + 'c=FeatureClassCtr&a=state&token='+token
                    +'&user_tid='+tid+'&tid=' + item.tid + '&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {
                            $scope.lists = data.data;
                            $scope.load(1);
                        }
                        else {
                            alert(data.msg);
                        }
                    })
            }
        };
    }]);
//添加课程详情
teachingResearchControllers.controller('courseDetailsController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        loading();
        var tid=window.localStorage.getItem(OATag+"tid");
        var token=window.localStorage.getItem(OATag+"token");
        var city=window.localStorage.getItem(OATag+"city");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        $scope.addShortCourse=function(){
            if(isEmptyStr($scope.high_quality_name)){
                alert("请填写课程名");
                return;
            }
            if(isEmptyStr($scope.courses_type)){
                alert("请选择课程类型");
                return;
            }
            if($scope.courses_type=="精品课程"){
                $scope.courses_type=0;
            }else if($scope.courses_type=="外教"){
                $scope.courses_type=1;
            }
            if(isEmptyStr($scope.class_hour)){
                alert("请填写课程所需课时");
                return;
            }
            if(isEmptyStr($scope.high_quality_price)){
                alert("请填写课程的课时单价");
                return;
            }
            if(isEmptyStr($scope.class_type)){
                alert("请填写课程的上课类型");
                return;
            }
            if($scope.class_type=='一对一'){
                $scope.class_type=0;
            }else if($scope.class_type=='拼课'){
                $scope.class_type=1;
            }else if($scope.class_type=="两者都有"){
                $scope.class_type=2;
            }
            if(isEmptyStr($scope.class_way)){
                alert("请填写课程的上课方式");
                return;
            }
            if(isEmptyStr($scope.city)){
                alert("请填写课程所开放的城市");
                return;
            }
            if(isEmptyStr($scope.content_one)){
                alert("请填写课程适学学生");
                return;
            }
            if(isEmptyStr($scope.content_two)){
                alert("请填写课程简介");
                return;
            }
            //添加课程基本信息
            $http.jsonp(url + 'c=FeatureClassCtr&a=addFeatureClass&user_tid=' + tid + '&token=' + token
                + '&high_quality_name='+$scope.high_quality_name +'&class_hour='+$scope.class_hour
                +'&high_quality_price='+$scope.high_quality_price +'&city='+$scope.city+'&class_type='
                +$scope.class_type+'&courses_type='+$scope.courses_type+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.tid = data.data;
                        logUtil("tid===========",$scope.tid);
                        //添加课程上课方式
                        $http.jsonp(url + 'c=FeatureClassCtr&a=addClassway&user_tid=' + tid
                            + '&token=' + token + '&high_quality_courses_tid='+$scope.tid+'&high_quality_class_way='
                            +$scope.class_way+'&city='+city+'&callback=JSON_CALLBACK')
                            .success(function (data) {
                                if (data.code == 0) {
                                   logUtil("high_quality_class_way===========",$scope.class_way);
                                    logUtil("tid2===========",$scope.tid);
                                    //添加课程简介
                                    $http.jsonp(url + 'c=FeatureClassCtr&a=addClassDetails&user_tid=' + tid
                                        + '&token=' + token + '&high_quality_courses_tid='+$scope.tid+'&course_name='+
                                        $scope.high_quality_name+'&title_one=适学学生&city='+city+'&content_one='
                                        +$scope.content_one+'&title_two=课程介绍&content_two='+$scope.content_two
                                        +'&callback=JSON_CALLBACK')
                                        .success(function (data) {
                                            if (data.code == 0) {
                                                alert("添加课程成功");
                                                reset();
                                                logUtil("content_one===========",$scope.content_one);
                                                logUtil("content_two===========",$scope.content_two);
                                                logUtil("tid3===========",$scope.tid);
                                            }
                                            else {
                                                alert(data.msg);
                                            }
                                        });
                                }
                                else {
                                    alert(data.msg);
                                }
                            });

                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
        //上传头像
        $scope.uploadImg = function(){
            /*** 上传课程图片***/
            $(function () {
                'use strict';
                console.log("tid------>" + $scope.tid);
                $('#file').fileupload({
                    url: courseImgUrl + 'type=1&city='+$scope.city+ '&class_num='//上传的路径
                    + $scope.tid,//$scope.tid从添加成功后返回数据取课程id
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断上传的图片格式
                    success: function (data) {
                        alert("课程图片上传成功");
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //修改原来默认url为上传成功后图片的url
                        document.getElementById("img").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
        //添加课程详情图片
        $scope.uploadFamilyPhotoOne = function(){
            $(function () {
                'use strict';
                console.log("tid------>" + $scope.tid);
                $('#file2').fileupload({
                    url: courseImgUrl + 'type=2&city='+$scope.city+ '&class_num='//上传的路径
                    + $scope.tid,//$scope.tid从添加成功后返回数据取课程id
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断上传的图片格式
                    success: function (data) {
                        alert("课程图片上传成功");
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //修改原来默认url为上传成功后图片的url
                        document.getElementById("img").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
    }]);
//编辑短期课程
teachingResearchControllers.controller('editorShortCourseController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        var coursetid=$location.search()['course_tid'];
        var tid=window.localStorage.getItem(OATag+"tid");
        var token=window.localStorage.getItem(OATag+"token");
        var city=window.localStorage.getItem(OATag+"city");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        $scope.ed_queryCourse=function(){
            var params='c=FeatureClassCtr&a=queryFeatureClass&token='+token+'&user_tid='+
                tid+'&tid='+coursetid;
            eTHttp.resultData(params)
                .success(function(data){
                    if(data.code==0){
                        $scope.shortcourse=data.data;
                        $scope.ed_high_quality_name=data.data[0].high_quality_name;
                        $scope.ed_courses_type=data.data[0].courses_type;
                        if($scope.ed_courses_type==0){
                            $scope.ed_courses_type="精品课程";
                        }else if($scope.ed_courses_type==1){
                            $scope.ed_courses_type="外教";
                        }
                        $scope.ed_class_hour=data.data[0].class_hour;
                        $scope.ed_high_quality_price=data.data[0].high_quality_price;
                        $scope.ed_class_type=data.data[0].class_type;
                        if($scope.ed_class_type==0){
                            $scope.ed_class_type='一对一';
                        }else if($scope.ed_class_type==1){
                            $scope.ed_class_type='拼课';
                        }else if($scope.ed_class_type==2){
                            $scope.ed_class_type="两者都有";
                        }
                        $scope.ed_high_quality_class_way=data.data[0].high_quality_class_way;
                        $scope.ed_city=data.data[0].city;
                        $scope.ed_content_one=data.data[0].content_one;
                        $scope.ed_content_two=data.data[0].content_two;
                    }
                })
        }
        $scope.ed_queryCourse();
        $scope.editorShortCourse=function(){
            if($scope.ed_courses_type=="精品课程"){
                $scope.ed_courses_type=0;
            }else if($scope.ed_courses_type=="外教"){
                $scope.ed_courses_type=1;
            }

            if($scope.ed_class_type=='一对一'){
                $scope.ed_class_type=0;
            }else if($scope.ed_class_type=='拼课'){
                $scope.ed_class_type=1;
            }else if($scope.ed_class_type=="两者都有"){
                $scope.ed_class_type=2;
            }
            //编辑课程基本信息
            $http.jsonp(url + 'c=FeatureClassCtr&a=updateFeatureClass&user_tid=' + tid + '&token=' + token
                + '&high_quality_name='+$scope.ed_high_quality_name +'&class_hour='+$scope.ed_class_hour
                +'&high_quality_price='+$scope.ed_high_quality_price +'&city='+$scope.ed_city+'&class_type='
                +$scope.ed_class_type+'&courses_type='+$scope.ed_courses_type+'&high_quality_class_way='
                +$scope.ed_high_quality_class_way+'&title_one=适学学生&content_one='+$scope.ed_content_one+'&title_two=' +
                '课程介绍&content_two='+$scope.ed_content_two+'&tid='+coursetid+'&course_name='
                +$scope.ed_high_quality_name+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                        window.location.assign('#/shortCourseManage');
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
        //上传头像
        $scope.uploadImg = function(){
            /*** 上传课程图片***/
            $(function () {
                'use strict';
                console.log("tid------>" + $scope.tid);
                $('#file').fileupload({
                    url: courseImgUrl + 'type=1&city='+$scope.city+ '&class_num='//上传的路径
                    + $scope.tid,//$scope.tid从添加成功后返回数据取课程id
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断上传的图片格式
                    success: function (data) {
                        alert("课程图片上传成功");
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //修改原来默认url为上传成功后图片的url
                        document.getElementById("img").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
        //添加课程详情图片
        $scope.uploadFamilyPhotoOne = function(){
            $(function () {
                'use strict';
                console.log("tid------>" + $scope.tid);
                $('#file2').fileupload({
                    url: courseImgUrl + 'type=2&city='+$scope.city+ '&class_num='//上传的路径
                    + $scope.tid,//$scope.tid从添加成功后返回数据取课程id
                    autoUpload: true,//自动上传
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,//判断上传的图片格式
                    success: function (data) {
                        alert("课程图片上传成功");
                        console.log("upload success------>" + JSON.parse(data).data.url);
                        //修改原来默认url为上传成功后图片的url
                        document.getElementById("img").src= JSON.parse(data).data.url;
                    },
                    error: function (data, status, e) {
                    },
                    done: function (e, data) {
                    }
                })
            });
        }
    }]);
//题库管理主页
teachingResearchControllers.controller('EquestionsManageController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        var tid=window.localStorage.getItem(OATag+"tid");
        var token=window.localStorage.getItem(OATag+"token");
        var city=window.localStorage.getItem(OATag+"city");
        $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
        $scope.load=function(page){
            if(city!="全国"){
                $scope.querycity="";
            }
            var params='c=JYItemPoolCtr&a=queryItemPool&token='+token+'&user_tid='
                +tid+'&page=' + page + '&city='+$scope.querycity;
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        if(isEmptyStr($scope.pages)){
                            $scope.pages=1;
                        }
                        $scope.totalpage = data.pages;
                        $scope.lists = data.data;
                        $scope.lists.forEach(function(a,b,c){
                            if(a.test_class>=1&&a.test_class<=5){
                                a.test_class="小学"+ a.test_class+"年级";
                            }else if(a.test_class==6){
                                if(a.test_city=="重庆"){
                                    a.test_class="小学"+ a.test_class+"年级";
                                }else if(a.test_city=="上海"){
                                    a.test_class="初中"+ a.test_class+"年级";
                                }
                            }else if(a.test_class>=7&&a.test_class<=9){
                                a.test_class="初中"+ a.test_class+"年级";
                            }else if(a.test_class>=10&& a.test_class<=12){
                                a.test_class="高中"+ a.test_class+"年级";
                            }
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //下一页
        $scope.currentpage = 1;
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;
                $scope.load($scope.currentpage);
            }
        };
        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.load($scope.currentpage);
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage=$scope.totalpage;
            $scope.load($scope.totalpage);
        };
//页数查询
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
        //根据城市查询年级
        $scope.queryGrade=function(){
            var params='c=JYItemPoolCtr&a=queryClass&city='+city+'&token='+token+'&user_tid='+tid;
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists1=data.data;
                        $scope.EquestionGrade = [];//创建年级数组
                        $scope.lists1.forEach(function(v,k){
                            this.push(v.user_class);
                        },$scope.EquestionGrade);//将查询返回数据中年级数据存入创建的年级数组
                        logUtil("EquestionGrade------->",$scope.EquestionGrade)
                    }
                });
        }

        //查询城市
        $scope.queryCity=function(){
            $scope.queryGrade();
            var params='c=areaListCtr&a=queryCity';
           eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists2=data.data;
                        $scope.city = [];//创建城市数组
                        $scope.lists2.forEach(function(v,k){
                            this.push(v.area_city);
                        },$scope.city);//将查询返回数据中城市数据存入创建的城市数组
                        logUtil("city------->",$scope.city)
                    }
                });
        }
        //添加课程
        $scope.addEquestion=function(){
            if(isEmptyStr($scope.add_grade)){
                alert("请选择题库适应的年级");
                return;
            }
            if(isEmptyStr($scope.add_test_semester)){
                alert("请选择题库适应的学期");
                return;
            }
            if(isEmptyStr($scope.add_city)){
                alert("请选择题库适应的城市");
                return;
            }
            if($scope.add_grade=="小学1年级"){
                $scope.add_grade="1年级";
                $scope.test_class="小学"
            }else  if($scope.add_grade=="小学2年级"){
                $scope.add_grade="2年级";
                $scope.test_class="小学"
            }else  if($scope.add_grade=="小学3年级"){
                $scope.add_grade="3年级";
                $scope.test_class="小学"
            }else  if($scope.add_grade=="小学4年级"){
                $scope.add_grade="4年级";
                $scope.test_class="小学"
            }else  if($scope.add_grade=="小学5年级"){
                $scope.add_grade="5年级";
                $scope.test_class="小学"
            }else  if($scope.add_grade=="小学6年级"){
                $scope.add_grade="6年级";
                $scope.test_class="小学"
            }else  if($scope.add_grade=="初中6年级"){
                $scope.add_grade="6年级";
                $scope.test_class="初中"
            }else  if($scope.add_grade=="初中7年级"){
                $scope.add_grade="7年级";
                $scope.test_class="初中"
            }else  if($scope.add_grade=="初中8年级"){
                $scope.add_grade="8年级";
                $scope.test_class="初中"
            }else  if($scope.add_grade=="初中9年级"){
                $scope.add_grade="9年级";
                $scope.test_class="初中"
            }else  if($scope.add_grade=="高中10年级"){
                $scope.add_grade="10年级";
                $scope.test_class="高中"
            }else  if($scope.add_grade=="高中11年级"){
                $scope.add_grade="11年级";
                $scope.test_class="高中"
            }else  if($scope.add_grade=="高中12年级"){
                $scope.add_grade="12年级";
                $scope.test_class="高中"
            }
            var param='c=JYItemPoolCtr&a=createItemPool&token='+token+'&user_tid='+tid+'&test_class='
                +$scope.add_grade+'&teaching_material_type=公立教材&test_semester='
                +$scope.add_test_semester+'&test_city='+$scope.add_city+'&test_grade='+$scope.test_class;
            eTHttp.resultData(param)
                .success(function(data){
                    if(data.code==0){
                     reset();
                        alert(data.msg)
                    }else{
                        alert(data.msg)
                    }
                    $scope.load(1);
                })
        }
    }]);
//题库详情
teachingResearchControllers.controller('E_questionDetailsController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        var q_tid=$location.search()['q_tid'];
        var tid=window.localStorage.getItem(OATag+"tid");
            var token=window.localStorage.getItem(OATag+"token");
            var city=window.localStorage.getItem(OATag+"city");
            $scope.jy_name=window.localStorage.getItem(OATag+"user_name");
            $scope.roles_name=window.localStorage.getItem(OATag+"roles_name");
            $scope.load=function(page){
            var params='c=JYPaperStockCtr&a=askTestQuestion&token='+token+'&user_tid='
                +tid+'&page=' + page +'&tid='+q_tid;
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.totalpage = data.pages;
                        $scope.lists = data.data;
                        $scope.lists.forEach(function(a,b,c){
                            a.test_options_a=" A : "+ a.test_options_a;
                            a.test_options_b=" B : "+ a.test_options_b;
                            a.test_options_c=" C : "+ a.test_options_c;
                            a.test_options_d=" D : "+ a.test_options_d;
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //下一页
        $scope.currentpage = 1;
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;
                $scope.load($scope.currentpage);
            }
        };
        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.load($scope.currentpage);
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage=$scope.totalpage;
            $scope.load($scope.totalpage);
        };
//页数查询
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
    //添加题目
        $scope.addTestQuestion=function(){
            if(isEmptyStr($scope.add_q_subject)){
                alert("您还没有填写任何题目！");
                return;
            }
            //if(isEmptyStr( $scope.add_q_review)){
            //    alert("您还没有对该题目的任何点评！");
            //    return;
            //}
            if(isEmptyStr($scope.add_q_options_a)){
                alert("A选项您还没有填写任何内容！");
                return;
            }
            if(isEmptyStr($scope.add_q_options_b)){
                alert("B选项您还没有填写任何内容！");
                return;
            }
            if(isEmptyStr($scope.add_q_right_answer)){
                alert("您还没有填写题目的正确答案！");
                return;
            }
            var params='c=JYPaperStockCtr&a=addTestQuestion&token='+token+'&user_tid='
                +tid+'&tid='+q_tid+'&subject='+$scope.add_q_subject+'&review='+
                $scope.add_q_review+'&right_answer='+$scope.add_q_right_answer+
                '&options_a='+$scope.add_q_options_a+'&options_b='+$scope.add_q_options_b
                +'&options_c='+$scope.add_q_options_c+'&options_d='+$scope.add_q_options_d;
            eTHttp.resultData(params)
                .success(function(data){
                    if(data.code==0){
                        alert(data.msg);
                        reset();
                        $scope.load( $scope.currentpage);
                    }else{
                        alert(data.msg);
                    }
                })
        }
        //删除题目
        $scope.deleteTestQuestion=function(item){
           var a=confirm("您确认要删除此道题目？");
            if(a==true){
               var params='c=JYPaperStockCtr&a=deleteTestQuestion&token='+token+'&user_tid='+tid+'&tid='+item.tid;
               eTHttp.resultData(params)
                   .success(function(data){
                       if(data.code==0){
                           $scope.load( $scope.currentpage);
                           alert(data.msg);
                       }else{
                           alert(data.msg);
                       }
                   })
           }
        }
        //查询题目
        $scope.queryTestQuestion=function(item){
            var a=confirm("您确认要编辑此道题目？");
            if(a==true){
                $('#edquestion').modal('show');
                var params='c=JYPaperStockCtr&a=askTestQuestion&token='+token+'&user_tid='
                    +tid+'&tid='+q_tid+'&test_question_details_tid='+item.tid;
                eTHttp.resultData(params)
                    .success(function(data){
                        if(data.code==0){
                            $scope.question=data.data;
                            $scope.one_que_tid=data.data[0].tid;
                            $scope.ed_test_subject=data.data[0].test_subject;
                            $scope.ed_test_right_answers=data.data[0].test_right_answers;
                            if($scope.ed_test_right_answers==1){
                                $scope.ed_test_right_answers="A";
                            }else if($scope.ed_test_right_answers==2){
                                $scope.ed_test_right_answers="B";
                            }else if($scope.ed_test_right_answers==3){
                                $scope.ed_test_right_answers="C";
                            }else if($scope.ed_test_right_answers==4){
                                $scope.ed_test_right_answers="D";
                            }
                            $scope.ed_test_review=data.data[0].test_review;
                            $scope.ed_test_options_a=data.data[0].test_options_a;
                            $scope.ed_test_options_b=data.data[0].test_options_b;
                            $scope.ed_test_options_c=data.data[0].test_options_c;
                            $scope.ed_test_options_d=data.data[0].test_options_d;
                        }else{
                            alert(data.msg);
                        }
                    })
            }
        }
        //编辑题目
        $scope.editTestQuestion=function(){
                var params='c=JYPaperStockCtr&a=editTestQuestion&token='+token+'&user_tid='
                    +tid+'&tid='+$scope.one_que_tid+'&subject='+$scope.ed_test_subject+'&review='+
                    $scope.ed_test_review+'&right_answer='+$scope.ed_test_right_answers+
                    '&options_a='+$scope.ed_test_options_a+'&options_b='+$scope.ed_test_options_b
                    +'&options_c='+$scope.ed_test_options_c+'&options_d='+$scope.ed_test_options_d;
                eTHttp.resultData(params)
                    .success(function(data){
                        if(data.code==0){
                            alert(data.msg);
                            $scope.load( $scope.currentpage);
                        }else{
                            alert(data.msg);
                        }
                    })

        }
    }]);
