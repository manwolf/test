/**
 * Created by pipe on 2015/4/10.
 */
/*
 * ng应用入口文件*/

var routerApp = angular.module('webApp', ['ngRoute', 'webControllers',
    'ngSanitize', 'MarketingController','teachingResearchControllers',
    'customerServiceControllers','loginControllers',
    'loginFirstController','authorityController']);
//路由视图

routerApp.config(function routes($routeProvider) {
    $routeProvider.when('/login', {//登录
            templateUrl: 'loginpage/login.html',
            controller: 'loginController'
        }).when('/loginFirst', {//第一次登陆修改密码登录
        templateUrl: 'loginpage/loginFirst.html',
        controller: 'loginFirstEditorController'
    }).when('/default', {             //首页
        templateUrl: 'default/default.html',
        controller: 'defaultController'
    })
    /**********教研系统*********/
      .when('/JyDefault', {//教研主页
            templateUrl: 'teacherAndResearch/JyDefault.html',
            controller: 'JyDefaultController'
        }).when('/teacherDetails', {//添加教师
            templateUrl: 'teacherAndResearch/teacherDetails.html',
            controller: 'teacherDetailsController'
        }).when('/updateTeacher', { //编辑教师
            templateUrl: 'teacherAndResearch/updateTeacher.html',
            controller: 'updateTeacherController'
        }).when('/JyTeacherCalendar', {//教研员查询教师日历
            templateUrl: 'teacherAndResearch/JyTeacherCalendar.html',
            controller: 'JyTeacherCalendarController'
        }).when('/shortCourseManage', {//短期课程管理
            templateUrl: 'teacherAndResearch/shortCourseManage.html',
            controller: 'shortCourseManageController'
        }).when('/courseDetails', {//添加短期课程详情
            templateUrl: 'teacherAndResearch/courseDetails.html',
            controller: 'courseDetailsController'
        }).when('/editorShortCourse', {//编辑短期课程详情
            templateUrl: 'teacherAndResearch/editorShortCourse.html',
            controller: 'editorShortCourseController'
        }).when('/teacherEvaluation', {//教师评价
            templateUrl: 'teacherAndResearch/teacherEvaluation.html',
            controller: 'teacherEvaluationController'
        }).when('/EquestionsManage', {//教师评价
            templateUrl: 'teacherAndResearch/E_questionsManage.html',
            controller: 'EquestionsManageController'
        }).when('/E_questionDetails', {//教师评价
            templateUrl: 'teacherAndResearch/E_questionDetails.html',
            controller: 'E_questionDetailsController'
        })
    /*********市场系统*********/
       .when('/SCmanage', {//市场管理首页
            templateUrl: 'marketingDepartment/SCmanage.html',
            controller: 'ManageScController'
        }).when('/SClibrary', {//码库页面
            templateUrl: 'marketingDepartment/SClibrary.html',
             controller:'LibraryScController'
        }).when('/scPromotorManage', {//市场递推人员管理
            templateUrl: 'marketingDepartment/scPromotorManage.html',
            controller: 'promotorScController'
        }).when('/scScrollingPicture', {//市场首页滚动图片
            templateUrl: 'marketingDepartment/scScrollingPicture.html',
            controller: 'scrollingPictureScController'
        }).when('/scDataDetails', {//市场数据管理详情
            templateUrl: 'marketingDepartment/scDataDetails.html',
            controller: 'scDataDetailsScController'
        })
    /*********客服系统**********/
        .when('/potentialCustomerData', {                                //潜在客户数据
            templateUrl: 'customer-server/potentialCustomerData.html',
            controller: 'potentialCustomerDataController'
        }).when('/applicationTransfer', {                              //学生调课
            templateUrl: 'customer-server/applicationTransfer.html',
            controller: 'applicationTransferController'
        }).when('/applicationTeacher', {                               //教师调课
            templateUrl: 'customer-server/applicationTeacher.html',
            controller: 'applicationTeacherController'
        }).when('/teacherCalendar', {                                //教师日历
            templateUrl: 'customer-server/teacherCalendar.html',
            controller: 'teacherCalendarController'
        }).when('/arrangingCourse', {                               //查询传统课程排课订单
            templateUrl: 'customer-server/arrangingCourse.html',
            controller: 'arrangingCourseController'
        }).when('/arrangingShortCourse', {                               //查询短期课程排课订单
            templateUrl: 'customer-server/arrangingShortCourse.html',
            controller: 'arrangingShortCourseController'
        }).when('/manageKf', {                                      //客服管理
            templateUrl: 'customer-server/KFmanage.html',
            controller: 'manageKfController'
        }).when('/customer_data', {                                 //已有客户
            templateUrl: 'customer-server/customer_data.html',
            controller: 'CustomerDataController'
        }).when('/SpellingLesson', {                                 //拼课客户数据
            templateUrl: 'customer-server/SpellingLesson.html',
            controller: 'SpellingLessonController'
        }).when('/SpellingData', {                                 //拼课数据管理
            templateUrl: 'customer-server/SpellingData.html',
            controller: 'SpellingDataController'
        }).when('/return_record', {                                  //回访记录
            templateUrl: 'customer-server/return_record.html',
            controller: 'ReturnRecordController'
        }) .when('/pointManagement', {                                  //高管主页
            templateUrl: 'customer-server/pointManagement.html',
            controller: 'pointManagementController'
        })
        //高管系统
        .when('/topManageSystem', {                                  //高管主页
            templateUrl: 'topManageSystem/topManageSystem.html',
            controller: 'topManageSystemController'
        }).otherwise({
            redirectTo: '/login'
        })

})

//做初始化配置
/**
 * 由于整个应用都会和路由打交道，所以这里把$state和$stateParams这两个对象放到$rootScope上，方便其它地方引用和注入。
 * 这里的run方法只会在angular启动的时候运行一次。
 * @param  {[type]} $rootScope
 * @param  {[type]} $state
 * @param  {[type]} $stateParams
 * @return {[type]}
 */
routerApp.run(function ($rootScope, $location) {

    $rootScope.$on('$routeChangeStart', function (evt, next, current) {
        // 一些初始化操作可以放在这
        logUtil("$routeChangeStart---->", "$routeChangeStart");


    })


});
