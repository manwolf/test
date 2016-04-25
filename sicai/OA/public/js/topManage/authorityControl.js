/**
 * Created by daiyingying on 2015/9/18.
 */
/**
 * Created by Christina on 2015/8/12.
 */
var authorityController = angular.module('authorityController', ['httpService']);

/******************************************市场系统**********************************************/
//邀请码管理界面
authorityController.controller('topManageSystemController', ['$scope', '$http', '$location', 'eTHttp'
    , function ($scope, $http, $location, eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        //取出登入用户相关信息的json对象
        getMarketUserJsonCache(marketEnum.marketUserBaseInfo);
        //取出登陆用户名
        $scope.scLanderName = getMarketUserName();
        $scope.user_tid = getMarketUserTid();
        $scope.user_token = getMarketUserToken();
        //用户退出市场系统
        $scope.quit = function () {
            $("#quitModal").modal('show');
        }
        $scope.confirmQuit = function () {
            $("#quitModal").modal('hide');
            //跳转页面延时
            setTimeout("window.location.assign('#/SClogin')", 700);
        }


    }]);

