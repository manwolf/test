var webControllers = angular.module('webControllers', ['httpService']);

/*********教研系统**************/

/********客服系统*********/
//第一次登陆修改客服密码
webControllers.controller('topManagerDefaultController', ['$scope', '$http', '$location','eTHttp'
    , function ($scope, $http, $location,eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        $scope.queryRoles = function () {
           var params='c=AddAclCtr&a=queryRoles';
        eTHttp.resultData(params)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
        $scope.queryRoles();
    }]);
