/**
 * Created by liu on 2015/9/6.
 */
var customerServiceControllers = angular.module('customerServiceControllers', ['httpService']);


/*****客服系统***********客服系统***********客服系统***********客服系统***********客服系统**********客服系统******/


//添加删除查询客服
customerServiceControllers.controller('manageKfController', ['$scope', '$http', '$location'
    , '$filter', function ($scope, $http, $location, $filter) {
        loading();
        $('html,body').width('100%').height('100%');
        var kf_token = window.localStorage.getItem(OATag+"token");
        var tid = window.localStorage.getItem(OATag+"tid");
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.login_type = window.localStorage.getItem(OATag+"login_type");

        logUtil("login_type=",$scope.login_type)
        //查询客服
        $http.jsonp(url + 'c=KFManageCtr&a=queryKfUser&user_tid='+tid
            +'&token=' + kf_token + '&callback=JSON_CALLBACK')
            .success(function (data) {
                if (data.code == 0) {
                    $scope.lists = data.data;
                    //新添客服按tid降序排序
                    var orderBy = $filter('orderBy');
                    $scope.order = function (predicate, reverse) {
                        $scope.lists = orderBy($scope.lists, predicate, reverse);
                    };
                    $scope.order('-tid', false);
                } else {
                    alert(data.msg);
                }
            })
        //增加客服
        $scope.addKf = function () {
            if (!$scope.kf_name1) {
                alert("姓名不能为空")
                return;
            }
            if (!$scope.kf_telephone) {
                alert("电话不能为空")
                return;
            }

            $http.jsonp(url + 'c=AddOaUserCtr&a=AddKF&user_name=' + $scope.kf_name1 + '&user_tid='+tid
                +'&token=' + kf_token + '&telephone=' + $scope.kf_telephone + '&roles=客服专员' +
                '&city='+$scope.kf_city+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert("添加成功");
                        reset();
                        $scope.lists = data.data;
                        $scope.queryKf();//添加成功后跟新客服列表
                    } else {
                        alert(data.msg);
                    }
                })
        }
        //新添和删除后更新客服列表
        $scope.queryKf = function () {
            $http.jsonp(url + 'c=KFManageCtr&a=queryKfUser&user_tid='+tid
                +'&token=' + kf_token + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        //新添的客服在第一位显示
                        var orderBy = $filter('orderBy');
                        $scope.order = function (predicate, reverse) {
                            $scope.lists = orderBy($scope.lists, predicate, reverse);
                        };
                        $scope.order('-tid', false);//按tid降序排序
                    } else {
                        if(isDebug){
                            alert(data.msg);
                        }
                    }
                })
        }
        //删除客服
        $scope.deleteKf = function () {
            $http.jsonp(url + 'c=KFManageCtr&a=deleteKfUser&tid=' + $scope.tid + '&user_tid='+tid
                +'&token=' + kf_token + '&callback=JSON_CALLBACK')

                .success(function (data) {
                    if (data.code == 0) {
                        alert("删除成功");
                        $scope.lists = data.data;
                        $scope.queryKf();//删除成功后更新客服列表
                    } else {
                        alert(data.msg);
                    }
                })
        }
    }]);
//已有客户数据
webControllers.controller('CustomerDataController', ['$scope', '$http', 'eTHttp'
    , function ($scope, $http, eTHttp) {
        setInterval(function () {
           var params='c=AlertCtr&a=alert';
            eTHttp.resultData(params)
                .success(function(data){
                    if(data.code==0){
                        $scope.list_count=data.data[0].count;
                        $scope.tol_count= window.localStorage.getItem(OATag+"count");
                        if(isEmptyStr($scope.tol_count)){
                            $scope.tol_count=$scope.list_count;
                            window.localStorage.setItem(OATag+"count",$scope.tol_count)
                        }
                        if(!isEmptyStr( $scope.tol_count)){
                            if($scope.tol_count<$scope.list_count){
                                alert("您有新订单，请注意查询");
                                $scope.tol_count=$scope.list_count;
                                window.localStorage.setItem(OATag+"count",$scope.tol_count)
                            }
                        }
                        logUtil("总数=",$scope.tol_count);
                    }
                })
        }, 15000);
        $('html,body').width('100%').height('100%');
        var token = window.localStorage.getItem(OATag+"token");
        var tid=window.localStorage.getItem(OATag+"tid");
        var city=window.localStorage.getItem(OATag+"city");
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        logUtil("token---->",token);
        var params = 'c=KFOrderListCtr&a=queryOrder&token=' + token+'&user_tid='+tid;
        $scope.currentParams = params;
        $scope.currentpage = 1;
        $scope.search = function(params){
            params = params + '&page=' + 1;
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        console.log(data.data);
                        $scope.pages = data.pages;          //获取总页数
                        var pages= $scope.pages;            //如果页数为空或没被定义，则默认总页数为1
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            //  e.class_content = e.class_content.substring(0,3);
                            e.pay_done = e.pay_done == 1 ? '已支付' : '未支付';           //判断筛选出来的数据的支付，使用和完成状态
                            e.invitation= (e.invitation==''|| e.invitation==null|| e.invitation==undefined)?'未使用': e.invitation;
                            e.order_state = e.order_state == 0 ? '已完成' : (e.order_state == 3 ? '已排课' : (e.order_state == 2 ? '已取消' : (e.order_state == 1 ? '未排课' : '其他')));
                        })
                    }else{
                        alert(data.msg);
                        $scope.pages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //订单检索
        $scope.searchOrder = function () {
            var ifexport=0;
            var searchType = arguments[0];      //将筛选条件分为大类跟小类，大类进行筛选，小类传参数
            var paramsValue = arguments[1];
            logUtil("searchType---->",searchType);
            logUtil("paramsValue---->",paramsValue);
            $scope.currentpage = 1;
          if(searchType == 0){                //当searchType=0代表支付状态
                params = 'c=KFOrderListCtr&a=queryOrder&token=' + token +
                    '&pay_done=' +paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;

                console.log(searchType);
            }else if(searchType == 1){          //当searchType=1代表课程排课状态
                params = 'c=KFOrderListCtr&a=queryOrder&token=' + token +
                    '&order_state=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 2){
                logUtil("paramsValue---->",paramsValue);
                params = 'c=KFOrderListCtr&a=queryOrder&token=' + token
                    + '&class_content=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 3){          //当searchType=3代表筛选购买课程的状态
                if(paramsValue == 0){
                    paramsValue = '试课';
                }else if(paramsValue == 1){
                    paramsValue = '购买套餐';
                }
                params = 'c=KFOrderListCtr&a=queryOrder&token=' + token
                    + '&class_count=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 4){          //当searchType=4代表筛选上门方式的状态
                params ='c=KFOrderListCtr&a=queryOrder&token=' + token + '&class_way=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 5){          //当searchType=4代表筛选首次下单时间的状态
                params ='c=KFOrderListCtr&a=queryOrder&token=' + token + '&date=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 6){          //当searchType=6代表筛选回访的状态
                params ='c=KFOrderListCtr&a=queryOrder&token=' + token + '&return_count=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 7){          //当searchType=7代表筛选剩余课时的状态
                params ='c=KFOrderListCtr&a=queryOrder&token=' + token + '&ramainingHour=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if(searchType == 8){          //当searchType=7代表筛选剩余课时的状态
            params ='c=KFOrderListCtr&a=queryOrder&token=' + token + '&having_teacher=' + paramsValue+'&user_tid='+tid;
            $scope.currentType = searchType;
            $scope.currentParams = params;
            console.log(searchType);
        }
            $scope.lists = [];    //每次对数据进行清空
            $scope.search(params);
            console.log(params);
        }
        //导出数据
        $scope.messageOut=function(params){
           var a=confirm("是否导出当前页数据？")
            if(a==true) {
                $scope.currentParams = $scope.currentParams + "&ifexport=" + 1;
                params = url + $scope.currentParams;
                window.location.href = params;
                logUtil("currentParams----------", $scope.currentParams);
                logUtil("params-----------", params);
            }
            };
         //查询区
        $scope.queryDistrict=function(){
            $http.jsonp(url + 'c=areaListCtr&a=queryDistrict&area_city='+city+'&callback=JSON_CALLBACK')
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
       ////添加选择数据简化
       // $scope.addSelectData=function(){
       //     var select_type=arguments[0];
       //     if(select_type==0){
       //         $scope.class_content_add=arguments[1];
       //     }else if(select_type==1){
       //         $scope.class_way_add=arguments[1];
       //     }else if(select_type==2){
       //         $scope.pay_type_add=arguments[1];
       //     }
       //     logUtil('select_type------------>',select_type)
       //     logUtil('select_data------------>',arguments[1])
       // }
       // 添加客户信息
        $scope.addOrder = function () {
            $('#myM2').modal('hide');
            var class_content;
            if($scope.class_content == '学前') {
                class_content = 0;
            }else if ($scope.class_content == '小学1年级') {
                class_content = 1;
            } else if ($scope.class_content == '小学2年级') {
                class_content = 2;
            } else if ($scope.class_content == '小学3年级') {
                class_content = 3;
            } else if ($scope.class_content == '小学4年级') {
                class_content = 4;
            } else if ($scope.class_content == '小学5年级') {
                class_content = 5;
            } else if ($scope.class_content == '初中(小学)6年级') {
                class_content = 6;
            } else if ($scope.class_content == '初中7年级') {
                class_content = 7;
            } else if ($scope.class_content == '初中8年级') {
                class_content = 8;
            } else if ($scope.class_content == '初中9年级') {
                class_content = 9;
            } else if ($scope.class_content == '高中10年级') {
                class_content = 10;
            } else if ($scope.class_content == '高中11年级') {
                class_content = 11;
            } else {
                class_content = 12;
            }
            var class_way;
            if ($scope.class_way == '教师上门') {
                class_way = 0;
            } else if ($scope.class_way == '咖啡馆') {
                class_way = 1;
            } else {
                class_way = 2;
            }
            var pay_type;
            if ($scope.pay_type == '银联') {
                pay_type = 3;
            } else if ($scope.pay_type == '现金') {
                pay_type = 4;
            } else if($scope.pay_type=='免费'){
                pay_type = 5;
            }
            $scope.order_date=$('#datatime').val();
            logUtil('$scope.order_date',$scope.order_date);
            $http.jsonp(url + 'c=KFOrderListCtr&a=addOrder&token=' + token + '&user_tid='+tid+'&user_name=' + $scope.user_name +
                '&user_area=' + $scope.user_area + '&teacher_name=' + $scope.teacher_name + '&class_content='
                + class_content + '&class_way=' + class_way+ '&class_count='
                + $scope.class_count + '&order_money=' + $scope.order_money +'&order_address='
                + $scope.order_address + '&order_phone=' + $scope.order_phone+ '&pay_type=' + pay_type
                + '&order_date=' + $scope.order_date + '&order_time=' + $scope.order_time +
                '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                        reset();
                        $scope.search(params);
                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        };
        //自动刷新
        $scope.refresh = function () {
            document.execCommand('Refresh');
        };
        //快速搜寻
        $scope.fastsearch = function () {
            $http.jsonp(url + 'c=KFOrderListCtr&a=queryOrder&token=' + token + '&user_name='
                + $scope.user_name + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            e.pay_done = e.pay_done == 1 ? '已支付' : '未支付';
                            e.invitation= (e.invitation==''|| e.invitation==null|| e.invitation==undefined)?'未使用': e.invitation;
                            e.order_state = e.order_state == 0 ? '已完成' : (e.order_state == 3 ? '已排课' : (e.order_state == 2 ? '已取消' : (e.order_state == 1 ? '未排课' : '其他')));
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                });
        };
        //转化状态
        $scope.updateOrderState = function () {
            $('#myM1').modal('hide');
            $http.jsonp(url + 'c=KFOrderListCtr&a=switchOrder&token=' + token + '&tid=' + $scope.tid +
                '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert('操作成功');
                        $scope.lists = data.data;
                        $scope.search(params);
                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //查询教师信息
        $scope.getTeacherName=function(){
            if(isEmptyStr($scope.teacher_name)){
                $scope.teacher_name="";
            }
            $http.jsonp(url + 'c=KFOrderListCtr&a=getTeacherName&teacher_name='+$scope.teacher_name
                +'&token=' + token + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.teacher_namearray=data.data;
                        $scope.teacher=[];//创建教师姓名数组
                        $scope.teacher1=[];//创建教师id数组
                        $scope.teacher_namearray.forEach(function(v,k){
                            this.push(v.teacher_name);
                        },$scope.teacher);//将查询返回数据中教师姓名数据存入创建的教师姓名数组
                        $scope.teacher_namearray.forEach(function(v,k){
                            this.push(v.tid);
                        },$scope.teacher1);//将查询返回数据中教师id数据存入创建的教师id数组

                        logUtil("teacher1------->",$scope.teacher1)
                        logUtil("teacher------->",$scope.teacher)


                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //根据城市查询课程套餐类型
        $scope.queryCourseType=function(){
            var params='c=KFOrderListCtr&a=getCourse&token=' + token + '&city=' + city+'&user_tid='+tid;
            eTHttp.resultData(params)
                .success(function (data){
                    if(data.code==0) {
                        $scope.getTeacherName();
                        $scope.queryDistrict();
                        $scope.courseType = data.data;
                        $scope.courseClass = [];
                        $scope.courseType.forEach(function (a, b, c) {
                            this.push(a.class_content);
                        }, $scope.courseClass)
                    }
                })
        }
        //根据所选年级跟课程套餐类型确定订单金额
        $scope.getCoursePrice=function(){
            var class_content;
            if($scope.class_content == '学前') {
                class_content = 0;
            }else if ($scope.class_content == '小学1年级') {
                class_content = 1;
            } else if ($scope.class_content == '小学2年级') {
                class_content = 2;
            } else if ($scope.class_content == '小学3年级') {
                class_content = 3;
            } else if ($scope.class_content == '小学4年级') {
                class_content = 4;
            } else if ($scope.class_content == '小学5年级') {
                class_content = 5;
            } else if ($scope.class_content == '初中(小学)6年级') {
                class_content = 6;
            } else if ($scope.class_content == '初中7年级') {
                class_content = 7;
            } else if ($scope.class_content == '初中8年级') {
                class_content = 8;
            } else if ($scope.class_content == '初中9年级') {
                class_content = 9;
            } else if ($scope.class_content == '高中10年级') {
                class_content = 10;
            } else if ($scope.class_content == '高中11年级') {
                class_content = 11;
            } else {
                class_content = 12;
            }
            var params='c=KFOrderListCtr&a=getCoursePrice&token=' + token
                + '&class_content=' + class_content+'&class_count='+$scope.class_count+'&user_tid='+tid;
            eTHttp.resultData(params)
                .success(function(data){
                    if(data.code==0) {
                        $scope.order_money=data.data[0].course_totle_price;
                        logUtil("class_content---------->",class_content)
                    }
                })
        }
        //分配老师
        $scope.allocatingTeacher=function(){
            for(i=0;i<$scope.teacher.length;i++){
                if($scope.teacher_name==$scope.teacher[i]){//匹配教师姓名数组中的教师姓名
                    $scope.tid=$scope.teacher1[i];//将对应的id赋值给分配老师的id
                    logUtil("teacher_tid------->",$scope.tid)
                }
            }
            $http.jsonp(url + 'c=KFOrderListCtr&a=addOrderTeacher&order_tid='+$scope.order_tid
                +'&teacher_tid='+$scope.tid+'&token=' + token + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                        $scope.search(params);
                        logUtil("teacher_tid------->",$scope.teacher[i])

                    } else {
                        alert(data.msg);
                    }
                })

        }
        //删除
        $scope.cancelOrder = function () {
            $('#myM').modal('hide');
            $http.jsonp(url + 'c=KFOrderListCtr&a=cancelOrder&token=' + token + '&tid=' + $scope.tid +
                '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert('操作成功');
                        $scope.lists = data.data;
                        $scope.search(params);
                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //分页
        $scope.load=function(page){
            params = $scope.currentParams + '&page=' + page;
            // alert(page);
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            e.pay_done = e.pay_done == 1 ? '已支付' : '未支付';
                            e.invitation= (e.invitation==''|| e.invitation==null|| e.invitation==undefined)?'未使用': e.invitation;
                            e.order_state = e.order_state == 0 ? '已完成' : (e.order_state == 3 ? '已排课' : (e.order_state == 2 ? '已取消' : (e.order_state == 1 ? '未排课' : '其他')));
                            $scope.totalpage = data.pages;
                        })
                    }else{
                        $scope.lists = [];
                        $scope.pages = 0;
                        $scope.currentpage = 0;
                    }
                });
        }
        $scope.load(1);
        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.load(1);    //查询第一页
        };
        //下一页
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;    //点击下一页，当前页面加一
                $scope.load($scope.currentpage);    //  查询
            }
        };
        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;       //点击上一页，当前页面减一
                $scope.load($scope.currentpage);    //查询
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage = $scope.totalpage;  //当前页面等于尾页
            $scope.load($scope.totalpage);          //查询
        };
        //查询订单和页数
        $scope.goto=function(){
            $scope.load($scope.page);
            $scope.currentpage = $scope.page;
        }
        $scope.load(1);
     $scope.alertAddress=function(item){
           $('#alertAddress1').modal('show');
         $scope.telephone1=item.order_phone;
         $scope.address1=item.order_address;
        }
    }]);
//回访记录
webControllers.controller('ReturnRecordController', ['$scope', '$http','$location'
    , function ($scope, $http) {
        $('html,body').width('100%').height('100%');
        var token = window.localStorage.getItem(OATag+"token");
        var kf_tid = window.localStorage.getItem(OATag+"tid");
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        //  var city=window.localStorage.getItem(OATag+"kf_city");
        //接口调用  添加纪录
        $scope.addjilu = function () {
            $('#my_Modal').modal('hide');

            $http.jsonp(url + 'c=KFreturnInfoCtr&a=addreturn&token=' + token + '' +
                '&return_content=' + $scope.return_content + '&return_remark=' + $scope.return_remark + '&return_name=' +
                $scope.return_name + '&return_phone=' + $scope.return_phone + '&user_tid='+kf_tid+'callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                        reset();
                        $scope.load($scope.currentpage);
                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })

        };

        //   跳转回访
        $scope.goDetail = function (item) {
            $scope.find = function () {
                $http.jsonp(url + 'c=KFreturnInfoCtr&a=queryAllreturn&token='
                    + token + '&user_tid=' +kf_tid + '&return_name=' + item.user_name
                    + '&return_phone=' + item.order_phone
                    + '&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {
                            // alert('成功');
                        }
                        else {
                            alert(data.msg);
                        }
                    })
            };
        }
        //查询记录接口
        $scope.find = function () {
            $http.jsonp(url + 'c=KFreturnInfoCtr&a=queryAllreturn&token='
                + token + '&return_name=' + $scope.return_name
                + '&return_phone=' + $scope.return_phone
                + '&user_tid=' +kf_tid + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        $scope.lists = data.data;
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
        //分页
        $scope.load=function(page){
            // alert(page);
            $http.jsonp(url + 'c=KFreturnInfoCtr&a=queryAllreturn&token=' + token
                + '&page=' + page + '&user_tid=' +kf_tid + '&callback=JSON_CALLBACK')
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
                            e.teacher_sex = e.teacher_sex == 1 ? '男' : '女';
                        })
                    }
                    else {
                        $scope.lists = [];
                        $scope.pages = 0;
                        $scope.currentpage = 0;
                    }
                });
        }
        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.load(1);
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
                //  alert('OP');
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage = $scope.totalpage;
            $scope.load($scope.totalpage);
        };
//查询回访和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
    }]);
//潜在客户数据
customerServiceControllers.controller('potentialCustomerDataController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        var kf_admin_token = window.localStorage.getItem(OATag+"token");
        var kf_tid=window.localStorage.getItem(OATag+"tid");
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        //获取当前时间
        var d = new Date();
        //获取当前小时、分、秒数值，小于10的自动补0
        var time = (d.getHours() < 10 ? '0' + d.getHours() : d.getHours()) + ":" +
            (d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes());
        //获取当前4位数年
        var Y= d.getFullYear();
        //获取当前月，小于10 自动补0
        var M= ((d.getMonth() + 1) < 10 ? '0' + (d.getMonth() + 1) : d.getMonth() + 1);
        //获取当前日，小于10 自动补0
        var day= d.getDate()<10?'0'+ d.getDate():d.getDate();
        var potential_date = new Date().format('yyyy-mm-dd hh:nn');//设置日期格式
//利用电话号码快速搜索
        $scope.speedsearch = function () {
            $http.jsonp(url + 'c=KFpotentialInfoCtr&a=queryAllPotential&token='
                + kf_admin_token+ '&user_tid=' + kf_tid + '&potential_phone=' +
                $scope.potential_phone + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        //将数据中的数字转化为相应功能描述
                        //在数据库中potential_change为1表示已转化，为0表示未转化
                        //在数据库中potential_register为1表示已注册，为0表示未注册
                        $scope.lists.forEach(function (e, i, a) {
                            e.potential_change = e.potential_change == 1 ? '已转化' : '未转化';
                            e.potential_register = e.potential_register == 1 ? '已注册' : '未注册';
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                });
        };
//添加潜在客户
        $scope.addpotential = function () {
            if (!$scope.potential_name) {
                alert("请填写客户名！");
                return;
            }
            if (!$scope.potential_phone) {
                alert("请填写客户电话！");
                return;
            }
            if (!$scope.potential_number) {
                alert("请填写客户回访次数！");
                return;
            }
            if (!$scope.potential_area) {
                alert("请填写客户所在区！");
                return;
            }
            if (!$scope.potential_address) {
                alert("请填写客户详细地址！");
                return;
            }
            $('html,body').width('100%').height('100%');
            $http.jsonp(url + 'c=KFpotentialInfoCtr&a=addpotential&token=' + kf_admin_token
                + '&potential_name=' + $scope.potential_name + '&potential_phone=' +
                $scope.potential_phone + '&potential_city=' + $scope.potential_city +
                '&potential_area=' + $scope.potential_area +'&potential_streets=' +
                $scope.potential_streets + '&potential_date=' + potential_date + '&potential_number=' +
                $scope.potential_number + '&potential_address=' +
                $scope.potential_address + '&user_tid=' + kf_tid + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        alert("录入成功");
                        //录入成功刷新页面
                        $scope.load(1);
                    }
                    else {
                        alert(data.msg);
                    }
                });
        };
        //设置默认查询数据
        var dateParams = 'c=KFpotentialInfoCtr&a=queryAllPotential&token=' + kf_admin_token+'&user_tid=' + kf_tid;
        $scope.currentParams = dateParams;//设置筛选分页链接
        $scope.currentpage = 1;//设置当前页为第一页
        var searchType = '';
        var paramsValue = '';
        //取数据
        $scope.getPotentData = function(dateParams){
            dateParams = dateParams + '&page=' + 1;
            eTHttp.resultData(dateParams)
                .success(function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                        reset();
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        //当总页数只有一页时设置page=1
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1;
                        }
                        $scope.lists = data.data;
                        //将数据中的数字转化为相应功能描述
                        //在数据库中potential_change为1表示已转化，为0表示未转化
                        //在数据库中potential_register为1表示已注册，为0表示未注册
                        $scope.lists.forEach(function (e, i, a) {
                            e.potential_change = e.potential_change == 1 ? '已转化' : '未转化';
                            e.potential_register = e.potential_register == 1 ? '已注册' : '未注册';
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                });

        }
        //大类筛选
        $scope.searchPotentData = function(){
            searchType = arguments[0];//筛选的类型
            paramsValue = arguments[1];//筛选的参数
            logUtil("searchType---->",searchType);
            logUtil("paramsValue---->",paramsValue);
            $scope.currentpage = 1;
            if(searchType == 0){//筛选类型0表示录入时间筛选
                var end_date = new Date().format('yyyy-mm-dd hh:nn');//设置当前时间为搜索结束时间
                var star_date;
                if (paramsValue == 1) {//筛选数据1表示1天内
                    star_date = Y + "-" + M + "-" + ((d.getDate() - 1)<10?'0'+ (d.getDate() - 1):(d.getDate() - 1))+ " " + time;
                } else if (paramsValue == 2) {//筛选数据2表示1周内
                    star_date = Y + "-" + M + "-" + ((d.getDate() - 7)<10?'0'+ (d.getDate() - 7):(d.getDate() - 7)) + " " + time;
                } else if (paramsValue == 3) {//筛选数据3表示1月内
                    star_date = Y + "-" + (d.getMonth() < 10 ? '0' + d.getMonth() : d.getMonth()) + "-" + day + " " + time;
                } else if (paramsValue == 4) {//筛选数据4表示1季度内
                    star_date = Y + "-" + ((d.getMonth() - 2) < 10 ? '0' + (d.getMonth() - 2) : (d.getMonth() - 2)) + "-" +day+ " " + time;
                } else {//大于一季度筛选
                    star_date = (d.getFullYear() - 100) + "-" + M + "-" + day + " " + time;
                }
                //设置录入时间的筛选url
                dateParams = 'c=KFpotentialInfoCtr&a=queryAllPotential&token='
                    + kf_admin_token +  '&user_tid='+ kf_tid + '&start_date='
                    + star_date + '&end_date='+ end_date;
            }else if(searchType == 1){//筛选类型为1表示回访次数
                //设置回访次数的筛选url
                dateParams = 'c=KFpotentialInfoCtr&a=queryAllPotential&token='
                    + kf_admin_token + '&page=1&user_tid=' + kf_tid
                    + '&potential_number=' + paramsValue;
            }else if(searchType == 2){//筛选类型2表示转化状态筛选
                //设置转化状态的筛选url
                dateParams = 'c=KFpotentialInfoCtr&a=queryAllPotential&token='
                    + kf_admin_token + '&user_tid=' + kf_tid
                    + '&potential_change=' + paramsValue;
            }
            $scope.currentParams = dateParams;//设置筛选url为所选择的筛选url
            $scope.getPotentData(dateParams);//对筛选分页
        }
        //分页
        $scope.load=function(page){
            dateParams = $scope.currentParams + '&page=' + page;
            eTHttp.resultData(dateParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        //总页数只有一页是默认为1
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        //将数据中的数字转化为相应功能描述
                        //在数据库中potential_change为1表示已转化，为0表示未转化
                        //在数据库中potential_register为1表示已注册，为0表示未注册
                        $scope.lists.forEach(function (e, i, a) {
                            e.potential_change = e.potential_change == 1 ? '已转化' : '未转化';
                            e.potential_register = e.potential_register == 1 ? '已注册' : '未注册';
                            $scope.totalpage = data.pages;
                        })
                    } else {
                        alert(data.msg);
                    }
                })
        }

        //首页
        $scope.goHome = function () {
            $scope.currentpage=1;
            $scope.load(1);
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
            $scope.currentpage = $scope.totalpage;
            $scope.load($scope.totalpage);
        };
//查询潜在客户和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
    }]);
//学生调课申请
customerServiceControllers.controller('applicationTransferController', ['$scope', '$http','eTHttp'
    , function ($scope, $http,eTHttp) {
        var token = window.localStorage.getItem(OATag+"token");
        var tid = window.localStorage.getItem(OATag+"tid");
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        //分页
        $scope.load=function(page){
            // alert(page);
            $http.jsonp(url + 'c=KFReplaceClassCtr&a=queryApply&token=' + token
                + '&page=' + page + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.totalpage = data.pages;
                    }
                });
        }
        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.load(1);
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
            $scope.currentpage = $scope.totalpage;
            $scope.load($scope.totalpage);
        };
        //查询回访和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
        $scope.applicationTranferData=function(){
            var querytype=arguments[0];//获取筛选条件类型
            var querydata=arguments[1];//获取筛选条件的参数值
            logUtil("querytype----->",querytype);
            logUtil("querydata----->",querydata);
            var  Params='c=KFReplaceClassCtr&a=queryApply&token=' + token + '&user_name=' + $scope.user_name+'&user_tid='+tid;//快速查询传参
            if(querytype==1){//querytype：1表示申请时间
                Params='c=KFReplaceClassCtr&a=queryApply&date=' + querydata + '&token=' + token+'&user_tid='+tid;//用点击获取的申请时间参数进行筛选
            }else if(querytype==2){//querytype：2表示申请类型
                Params='c=KFReplaceClassCtr&a=queryApply&user_classes=' + querydata + '&token=' + token+'&user_tid='+tid;//用点击获取的申请类型参数进行筛选
            }else if(querytype==3){//querytype：3表示申请状况
                Params='c=KFReplaceClassCtr&a=queryApply&service_reply_state	=' + querydata + '&token=' + token+'&user_tid='+tid;//用点击获取的申请状况参数进行筛选
            }
            eTHttp.resultData(Params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                    }
                    else {
                        alert(data.msg);
                    }
                });
        }
        //审核申请
        $scope.applicationAuditing = function (item) {
            var a=confirm("是否同意该请求");
            if(a==true){
                $http.jsonp(url + 'c=KFReplaceClassCtr&a=updateApplyState&tid=' + item.tid + '&token=' +
                    token + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {
                            $scope.lists = data.data;
                            $http.jsonp(url + 'c=KFReplaceClassCtr&a=queryApply&token=' + token
                                + '&user_tid='+tid+'&callback=JSON_CALLBACK&'
                            )
                                .success(function (data) {
                                    if (data.code == 0) {
                                        $scope.lists = data.data;
                                        $scope.load(1);
                                    }
                                })
                        }
                        else {
                            alert(data.msg);
                        }
                    });
            }
        };
    }]);
//教师调课申请
customerServiceControllers.controller('applicationTeacherController', ['$scope', '$http','eTHttp'
    , function ($scope, $http,eTHttp) {
        var token = window.localStorage.getItem(OATag+"token");
        var tid = window.localStorage.getItem(OATag+"tid");
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        //筛选查询教师调课申请
        $scope.applicationTeacherData=function(){
            var params="";
            var querytype=arguments[0];//湖区点击筛选样式
            var querydata=arguments[1];//获取点击筛选数据
            logUtil("querytype----->",querytype);
            logUtil("querydata----->",querydata);
            if(querytype==1){//querytype：1表示申请时间
                params='c=KFReplaceClassCtr&a=queryTeacherApply&date=' + querydata + '&token=' + token+'&user_tid='+tid;
            }else if(querytype==2){//querytype：2表示申请状况
                params='c=KFReplaceClassCtr&a=queryTeacherApply&user_classes=' + querydata + '&token=' + token+'&user_tid='+tid;;
            }else if(querytype==3){//querytype：3表示快速查询
                params='c=KFReplaceClassCtr&a=queryTeacherApply&token=' + token + '&teacher_name=' + $scope.teacher_name+'&user_tid='+tid;;
            }else{//无筛选时默认全部查询
                params='c=KFReplaceClassCtr&a=queryTeacherApply&token=' + token+'&user_tid='+tid;;
            }
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                    }else{
                        alert(data.msg);
                    }
                })
        }
        //教师调课申请审核
        $scope.applicationTeacherAuditing = function (item) {
            var a=confirm("是否同意该请求");
            if(a==true){
                $http.jsonp(url + 'c=KFReplaceClassCtr&a=updateTeacherApplyState&&tid=' + item.tid
                    + '&token=' + token + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {
                            $scope.lists = data.data;
                            $scope.applicationTeacherData();
                        }
                        else {
                            alert(data.msg);
                        }
                    });
            }
        };
        $scope.applicationTeacherData();//查询全部
    }]);
//显示课程表闲忙状态排课和取消课程
customerServiceControllers.controller('teacherCalendarController',['$scope', '$http', '$location'
    , function ($scope,$http,$location) {
        loading();
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        var teacher_tid = $location.search()['teacher_tid'];//获取页面穿过来的教师ID
        var class_hour = $location.search()['class_hour'];//获取页面传来的课时数
        var comNum = class_hour / 2;//课程数为课时数的1/2
        window.localStorage.setItem(OATag + "order_tid", $location.search()['order_tid']);//将传过来的order_tid存到内存
        var order_tid = window.localStorage.getItem(OATag + "order_tid");
        logUtil("order_tid--------->", order_tid);
        var kf_token = window.localStorage.getItem(OATag + "token");
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
            $http.jsonp(url + 'c=ChangeCourseCtr&a=KFTeacherCurriculum&user_tid='+tid+'&callback=JSON_CALLBACK&tid='
                + teacher_tid + '&date=' + getCurrentTime(0).full + '&token=' + kf_token)
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

                            var teacher_entry_state = e.teacher_entry_state == 0 ? '未排课' :  '已预约' ;
                            if(e.time_busy==1){
                                logUtil("time_busy1----------->",e.time_busy)
                                if(e.user_name==null){
                                    logUtil("user_name----------->",e.user_name)
                                    if(e.class_content==null){
                                        logUtil("class_content----------->",e.class_content)
                                        logUtil("time_busy2----------->",time_busy)
                                        class_content='已预约';
                                    }
                                }
                            }
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
        //排课和取消课程
        $scope.apply = function (item) {
            //timebusy：0表示闲，1表示忙
            if(item.time_busy==0)//状态为闲时排课
            {
                $http.jsonp(url + 'c=ChangeCourseCtr&a=addCourse&order_tid='+order_tid+'&class_start_date=' +
                    item.schedule_date + '&class_start_time='+ item.schedule_time+'&class_count='+comNum+'&token='+
                    kf_token+'&user_tid='+tid+'&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {

                            alert(data.msg);
                            item.time_busy = 1;//把“闲”状态变为忙状态
                            logUtil("排课次数为：",comNum);
                            $scope.searchclass();
                        } else {
                            alert(data.msg);
                        }
                    })
                    .error(function (err) {
                        console.log(err);
                    })
                $scope.searchclass();
                //状态为忙时取消课程
            }else if(item.time_busy==1){
                var a=confirm("您确认要取消该课程吗？")
                if(a==true){
                    $http.jsonp(url + 'c=ChangeCourseCtr&a=cancelCourse&order_tid='+order_tid+'&class_tid='
                        +item.class_tid+'&token='+kf_token+'&user_tid='+tid+'&callback=JSON_CALLBACK')
                        .success(function (data) {
                            if (data.code == 0) {
                                alert(data.msg);
                                item.time_busy = 0;//把忙闲时间转化为“闲”
                                $scope.searchclass();
                                //取消课程后更新页面
                            } else {
                                alert(data.msg);
                            }
                        })
                        .error(function (err) {
                            console.log(err);
                        })
                }
            }
        }

    }]);
//查询传统课程排课订单
customerServiceControllers.controller('arrangingCourseController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        $('html,body').width('100%').height('100%');
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        var token=window.localStorage.getItem(OATag +"token");
        var tid=window.localStorage.getItem(OATag +"tid");
        $scope.load=function(page){
            //  alert(page);
            $http.jsonp(url + 'c=KFOrderListCtr&a=queryOrder&order_state=1&pay_done=1&page='
                + page + '&token='+token+'&user_tid='+tid+'&class_count=2&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            e.pay_done = e.pay_done == 1 ? '已支付' : '未支付';
                            e.order_state= e.order_state==1?'未排课':'已排课';
                            $scope.totalpage = data.pages;
                        })
                    } else {
                        alert(data.msg);
                        $scope.pages = data.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //上一页
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
                //  alert('OP');
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage=$scope.totalpage;
            $scope.load($scope.totalpage);
        };
//查询排课订单和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
    }]);
//查询短期课程排课订单
customerServiceControllers.controller('arrangingShortCourseController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        var token=window.localStorage.getItem(OATag +"token");
        var tid=window.localStorage.getItem(OATag +"tid");
        $scope.load=function(page){
            $http.jsonp(url + 'c=ChangeCourseCtr&a=quality&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function (e, i, a) {
                            e.pay_done = e.pay_done == 1 ? '已支付' : '未支付';
                            e.order_state= e.order_state==1?'未排课':'已排课';
                            $scope.totalpage = data.pages;
                        })
                    } else {
                        alert(data.msg);
                        $scope.pages = data.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //上一页
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
                //  alert('OP');
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage=$scope.totalpage;
            $scope.load($scope.totalpage);
        };
//查询排课订单和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
    }]);
//拼课客户数据
customerServiceControllers.controller('SpellingLessonController', ['$scope', '$http','$location', 'eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        var token=window.localStorage.getItem(OATag+"token");
        var city=window.localStorage.getItem(OATag+"city")
        logUtil("token-----",token)
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        var tid=window.localStorage.getItem(OATag+"tid");
        var params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token+'&user_tid='+tid;
        $scope.currentParams = params;
        $scope.currentpage = 1;                 //设置当前页数为1
        $scope.spellingsearch = function(params){
            params = params + '&page=' + 1;
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        if(data.data==""){
                            alert("查无此信息")
                        }
                        $scope.pages = data.pages;          //获取总页数
                        var pages= $scope.pages;            //如果页数为空或没被定义，则默认总页数为1
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function(e,i,a){       //每次数据查询如果用户类型为空则默认为已上课一对一
                            if(e.class_way==null||e.class_way=="null"){
                                e.class_way="老师上门";
                            }
                            if(e.high_quality_courses_tid==null||e.high_quality_courses_tid=="null"){
                                e.high_quality_courses_tid="传统课程"
                            }
                            if(isEmpty(e.order_tid)){
                                e.order_tid="已上一对一课程用户"
                            }else{
                                e.order_tid="未上一对一课程用户"
                            }
                            e.spelling_state= e.spelling_state== 0 ? '取消拼课' : (e.spelling_state == 2 ? '正在上课' : (e.spelling_state == 3 ? '拼课完成' :  '正在拼课' ));
                        })
                    }else{
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }

        $scope.spellingOrder = function () {

            var searchType = arguments[0];          //将筛选条件分为大类和小类，大类为筛选条件，小类传参
            var paramsValue = arguments[1];
            logUtil("searchType---->", searchType);
            logUtil("paramsValue---->", paramsValue);
            $scope.currentpage = 1;

            if (searchType == 0) {//searchType=0代表年级筛选
                var teaching_grade;
                if(parseInt(paramsValue) <= 5 && parseInt(paramsValue) >= 1){
                    paramsValue = paramsValue + '年级';
                    teaching_grade='小学';
                } else if(parseInt(paramsValue)==6){
                    if(city=="上海"){
                        paramsValue =paramsValue + '年级';
                        teaching_grade='初中';
                    }else if(city=="重庆"){
                        paramsValue =paramsValue + '年级';
                        teaching_grade='小学';
                    }
                }else if(parseInt(paramsValue) <= 9 && parseInt(paramsValue) >6){
                    paramsValue =paramsValue + '年级';
                    teaching_grade='初中';
                }else if(parseInt(paramsValue) <= 12 && parseInt(paramsValue) >= 10){
                    paramsValue = paramsValue + '年级';
                    teaching_grade='高中';
                }
                logUtil("paramsValue---->",paramsValue);
                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token +
                    '&teache_class='+ paramsValue+'&user_tid='+tid+'&teaching_grade='+teaching_grade;
                $scope.currentType = searchType;
                $scope.currentParams = params;

                console.log(searchType);
            } else if (searchType == 1) {               //searchType=1代表拼课发起时间
                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token +
                    '&classtime=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            } else if (searchType == 2) {               //searchType=2代表拼课人数

                logUtil("paramsValue---->", paramsValue);

                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token + '&fightoffnum=' + paramsValue+'&user_tid='+tid;

                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            } else if (searchType == 3) {               //searchType=3代表用户类型
                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token + '&usertype=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);

            }else if (searchType == 4) {                //searchType=4代表上课方式
                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token + '&class_way=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if (searchType == 5) {                //searchType=5代表拼课课程类型
                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token + '&course_choice=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }else if (searchType == 6) {                //searchType=5代表分配老师
                params = 'c=KFfightoffCtr&a=queryFightOff&token=' + token + '&teacher_choice=' + paramsValue+'&user_tid='+tid;
                $scope.currentType = searchType;
                $scope.currentParams = params;
                console.log(searchType);
            }
            $scope.lists = [];
            $scope.spellingsearch(params);
            console.log(params);

        }
        //查询教师信息
        $scope.getTeacherName=function(){
            if(isEmptyStr($scope.teacher_name)){
                $scope.teacher_name="";
            }
            $http.jsonp(url + 'c=KFOrderListCtr&a=getTeacherName&teacher_name='+$scope.teacher_name
                +'&token=' + token + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.teacher_namearray=data.data;
                        $scope.teacher=[];//创建教师数组
                        $scope.teacher1=[];//创建教师数组
                        $scope.teacher_namearray.forEach(function(v,k){
                            this.push(v.teacher_name);
                        },$scope.teacher);//将查询返回数据中城市数据存入创建的城市数组
                        $scope.teacher_namearray.forEach(function(v,k){
                            this.push(v.tid);
                        },$scope.teacher1);//将查询返回数据中城市数据存入创建的城市数组

                        logUtil("teacher1------->",$scope.teacher1)
                        logUtil("teacher------->",$scope.teacher)


                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //分配老师
        $scope.allocatingTeacher=function(){
            for(i=0;i<$scope.teacher.length;i++){
                if($scope.teacher_name==$scope.teacher[i]){
                    $scope.tid=$scope.teacher1[i];
                    logUtil("teacher_tid------->",$scope.tid)
                }
            }
            $http.jsonp(url + 'c=KFOrderListCtr&a=addOrderTeacher&order_tid='+$scope.order_tid
                +'&teacher_tid='+$scope.tid+'&token=' + token + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        alert(data.msg);
                        $scope.spellingsearch(params);
                        logUtil("teacher_tid------->",$scope.teacher[i])

                    } else {
                        alert(data.msg);
                    }
                })

        }
        //分页
        $scope.load=function(page){
            params = $scope.currentParams + '&page=' + page;
            //  alert(page);
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        if (data.data != null && data.data.length > 0) {
                            $scope.pages = data.pages;
                            var pages = $scope.pages;
                            if (pages == null || pages == '' || pages == undefined) {
                                $scope.pages = 1
                            }
                            $scope.lists = data.data;
                            $scope.lists.forEach(function (e, i, a) {
                                if(e.class_way==null||e.class_way=="null"){
                                    e.class_way="老师上门";
                                }
                                if(e.high_quality_courses_tid==null||e.high_quality_courses_tid=="null"){
                                    e.high_quality_courses_tid="传统课程"
                                }
                                e.spelling_state = e.spelling_state == 0 ? '取消拼课' :
                                    (e.spelling_state == 2 ? '正在上课' : (e.spelling_state == 3 ? '拼课完成' : '正在拼课' ));
                                e.order_tid = e.order_tid == null ? '未上一对一课程' : "已上一对一课程";
                                $scope.totalpage = $scope.pages;   //尾页等于在总页数
                            })
                        } else {
                            $scope.lists = [];
                            $scope.pages = 0;
                            $scope.currentpage = 0;
                        }
                    }
                });
        }
        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.load(1);
        };
        //下一页
        $scope.next = function () {
            logUtil("currentpage----->",$scope.currentpage)
            logUtil("totalpage----->",$scope.totalpage)
            if ($scope.currentpage < $scope.totalpage) {
                $scope.currentpage++;
                $scope.load($scope.currentpage);
            }
        };
        //上一页
        $scope.prev = function () {
            logUtil("currentpage----->",$scope.currentpage)
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.load($scope.currentpage);
            }
        };
        //尾页
        $scope.endpage=function(){
            $scope.currentpage = $scope.totalpage;
            $scope.load($scope.totalpage);
        };
        //查询订单和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);

        //转换拼课接口
        $scope.conversion = function () {
            $('#myM1').modal('hide');
            $http.jsonp(url + 'c=KFfightoffCtr&a=conversion&token='
                + token + '&order_tid=' + $scope.order_tid
                + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
        //删除拼课接口
        $scope.DeleteLesson = function () {
            $('#myM').modal('hide');
            $http.jsonp(url + 'c=KFfightoffCtr&a=cancelFightOff&token='
                + token + '&user_tid='+tid+'&order_tid=' + $scope.order_tid
                + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
        //搜索姓名接口
        $scope.searchLesson = function () {
            $http.jsonp(url + 'c=KFfightoffCtr&a=queryFightOff&token='
                + token + '&user_tid='+tid+'&user_name=' + $scope.user_name
                + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        $scope.list.forEach(function (e, i, a) {
                            if(e.pay_done==1){
                                e.pay_done="已支付";
                            }else if(e.pay_done==0){
                                e.pay_done="未支付";
                            }
                            if(isEmpty(e.order_tid)){
                                e.order_tid="已上一对一课程用户"
                            }else{
                                e.order_tid="未上一对一课程用户"
                            }
                            if(e.class_way==null||e.class_way=="null"){
                                e.class_way="老师上门";
                            }
                            if(e.high_quality_courses_tid==null||e.high_quality_courses_tid=="null"){
                                e.high_quality_courses_tid="传统课程"
                            }
                        })
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };

    }]);
//拼课客户数据管理与详情
customerServiceControllers.controller('SpellingDataController', ['$scope', '$http','$location'
    , function ($scope, $http,$location) {
        $('html,body').width('100%').height('100%');
        var token=window.localStorage.getItem(OATag+"token");
        var tid=window.localStorage.getItem(OATag+"tid")
        var spelling_state;
        var spelling_state1=$location.search()['spelling_state'];
        if(spelling_state1=="取消拼课"){
            spelling_state=0;
        }else if(spelling_state1=="正在拼课"){
            spelling_state=1;
        }else if(spelling_state1=="正在上课"){
            spelling_state=2;
        }else if(spelling_state1=="拼课完成"){
            spelling_state=3;
        }
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        window.localStorage.setItem(OATag+"order_tid",$location.search()['order_tid']);
        var order_tid=window.localStorage.getItem(OATag+"order_tid");
        logUtil("order_tid------>",order_tid);
        //发起人的信息
        $http.jsonp(url + 'c=KFfightoffCtr&a=queryInitiatorDetails&user_tid='
            +tid+'&token=' + token + '&order_tid=' + order_tid +
            '&callback=JSON_CALLBACK')
            .success(function (data) {
                if (data.code == 0) {
                    // alert(data.msg);
                    $scope.lists = data.data;
                } else {
                    alert(data.msg);
                }
            })
            .error(function (err) {
                console.log(err);
            })
        //参与拼课人的信息
        $scope.queryFightOffDetails=function(){
            $http.jsonp(url + 'c=KFfightoffCtr&a=queryFightOffDetails&token='
                + token + '&spelling_state='+spelling_state+'&order_tid='
                + order_tid +'&user_tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.list = data.data;
                        $scope.list.forEach(function (e, i, a) {
                            if(e.pay_done==1){
                                e.pay_done="已支付";
                            }else if(e.pay_done==0){
                                e.pay_done="未支付";
                            }
                            if(isEmpty(e.order_tid)){
                                e.order_tid="已上一对一课程用户"
                            }else{
                                e.order_tid="未上一对一课程用户"
                            }
                            if(e.spelling_lesson_type==0){
                                e.spelling_lesson_type='';
                            }else if(e.spelling_lesson_type==1){
                                e.spelling_lesson_type='删除';
                            }
                        })
                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        };
        $scope.queryFightOffDetails();
        //删除参与人的订单
        $scope.DeletepersonLesson = function (item) {
            var a=confirm("您是否要删除该信息？")
            if(a==true) {
                $http.jsonp(url + 'c=KFfightoffCtr&a=cancelFightOff&token='
                    + token + '&user_tid='+tid+'&order_tid=' +item.tid
                    + '&callback=JSON_CALLBACK')
                    .success(function (data) {
                        if (data.code == 0) {
                            logUtil("tid------>",item.tid)
                            $http.jsonp(url + 'c=KFfightoffCtr&a=queryInitiatorDetails&token=' + token
                                + '&order_tid=' + order_tid + '&user_tid='+tid+'&callback=JSON_CALLBACK')
                                .success(function (data) {
                                    if (data.code == 0) {
                                        $scope.lists = data.data;
                                        $scope.queryFightOffDetails();
                                    } else {
                                        alert(data.msg);
                                    }
                                })
                                .error(function (err) {
                                    console.log(err);
                                })
                            $scope.pages = data.pages;
                            var pages = $scope.pages;
                            if (pages == null || pages == '' || pages == undefined) {
                                $scope.pages = 1
                            }
                            $scope.lists = data.data;
                            $scope.list.forEach(function (e, i, a) {
                                if(e.pay_done==1){
                                    e.pay_done="已支付";
                                }else if(e.pay_done==0){
                                    e.pay_done="未支付";
                                }
                                if(isEmpty(e.order_tid)){
                                    e.order_tid="已上一对一课程用户"
                                }else{
                                    e.order_tid="未上一对一课程用户"
                                }
                            })

                        }
                        else {
                            alert(data.msg);
                        }
                    })
            }
        };
    }]);
//积分管理
customerServiceControllers.controller('pointManagementController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        $scope.kf_name = window.localStorage.getItem(OATag+"user_name");
        $scope.roles_name = window.localStorage.getItem(OATag+"roles_name");
        var token=window.localStorage.getItem(OATag +"token");
        var tid=window.localStorage.getItem(OATag +"tid");
        //快速搜索
        $scope.speedSearchSort=function(){
            var sorttype=arguments[0];//第一个参数获取查询类型（排序类型）
            var time_sort=window.localStorage.getItem(OATag+"timesort");//按时间排序获取方式
            if(isEmptyStr(time_sort)){
                time_sort=1;
            }
            var classhour_sort=window.localStorage.getItem(OATag+"classhoursort");//按课时排序获取方式
            if(isEmptyStr(classhour_sort)){
                classhour_sort=1;
            }
            var point_sort=window.localStorage.getItem(OATag+"pointsort");//按时间排序获取方式
            if(isEmptyStr(point_sort)){
                point_sort=1;
            }
            var params;
           if(sorttype==0){//0表示按姓名或者电话查询
               if(isEmptyStr($scope.searchname)){
                   $scope.searchname="";
               }
               if(isEmptyStr($scope.searchtelephne)){
                   $scope.searchtelephne="";
               }
                params='c=userExchangePointsCtr&a=queryExchangePointsListForOA&user_name='
                   + $scope.searchname + '&user_telephone='+$scope.searchtelephne+'&token='+token+'&user_tid='+tid;
               $scope.currentparams=params;
           }else if(sorttype==1){//表示按注册时间排序
                params='c=userExchangePointsCtr&a=queryExchangePointsListForOA&sort_type='
                   + sorttype + '&ASC_or_DESC='+time_sort+'&token='+token+'&user_tid='+tid;
               $scope.currentparams=params;
               if(time_sort==1){//升序降序的切换
                   time_sort=2;
                   window.localStorage.setItem(OATag+"timesort",time_sort);
               }else if(time_sort==2){
                   time_sort=1;
                   window.localStorage.setItem(OATag+"timesort",time_sort);
               }
               logUtil("time_sort-------------",time_sort)
           }else if(sorttype==2){//按课时剩余数查询
               params='c=userExchangePointsCtr&a=queryExchangePointsListForOA&sort_type='
                   + sorttype + '&ASC_or_DESC='+classhour_sort+'&token='+token+'&user_tid='+tid;
               $scope.currentparams=params;
               if(classhour_sort==1){//升序降序的切换
                   classhour_sort=2;
                   window.localStorage.setItem(OATag+"classhoursort",classhour_sort);
               }else if(classhour_sort==2){
                   classhour_sort=1;
                   window.localStorage.setItem(OATag+"classhoursort",classhour_sort);
               }
               logUtil("classhour_sort-------------",classhour_sort)
           }else if(sorttype==3){//按积分数查询
               params='c=userExchangePointsCtr&a=queryExchangePointsListForOA&sort_type='
                   + sorttype + '&ASC_or_DESC='+point_sort+'&token='+token+'&user_tid='+tid;
               $scope.currentparams=params;
               if(point_sort==1){//升序降序的切换
                   point_sort=2;
                   window.localStorage.setItem(OATag+"pointsort",point_sort);
               }else if(point_sort==2){
                   point_sort=1;
                   window.localStorage.setItem(OATag+"pointsort",point_sort);
               }
               logUtil("pointsort-------------",point_sort)
           }
            $scope.currentpage = 1;
            $scope.currentParams=$scope.currentparams+"&page="+1;
            eTHttp.resultData($scope.currentParams)
                .success(function(data){
                    if(data.code==0){
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        //当总页数只有一页时设置page=1
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1;
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function(a,b,c){
                            if(a.user_sex==0){
                                a.user_sex="女";
                            }else if(a.user_sex==1){
                                a.user_sex="男";
                            }
                            $scope.totalpage = data.pages;
                        })
                    }else{
                        alert(data.msg);
                    }
                })
        }
        $scope.load=function(page){
            var params
            if(isEmptyStr($scope.currentparams)){
                 params= 'c=userExchangePointsCtr&a=queryExchangePointsListForOA&page='
                    + page + '&token='+token+'&user_tid='+tid;
            }else{
                 params=$scope.currentparams+"&page="+page;
            }
            eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.pages = data.pages;
                        var pages= $scope.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                        $scope.lists = data.data;
                        $scope.lists.forEach(function(a,b,c){
                            if(a.user_sex==0){
                                a.user_sex="女";
                            }else if(a.user_sex==1){
                                a.user_sex="男";
                            }
                            $scope.totalpage = data.pages;
                        })
                    } else {
                        alert(data.msg);
                        $scope.pages = data.pages;
                        if(pages==null||pages==''||pages==undefined){
                            $scope.pages=1
                        }
                    }
                })
        }
        //首页
        $scope.goHome = function () {
            $scope.load(1);
            $scope.currentpage=1;
        };
        //上一页
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
//查询排课订单和页数
        $scope.goto=function(){
            $scope.load($scope.page);
        }
        $scope.load(1);
    }]);