/**
 * Created by liu on 2015/9/14.
 */
var loginFirstController = angular.module('loginFirstController', ['httpService']);
//
loginFirstController.controller('loginFirstEditorController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        loading();
        $('html,body').width('100%').height('100%');
        alert('因为您是第一次登陆，所以需要修改密码')
        var tid = window.localStorage.getItem(OATag+"tid");
        var token = window.localStorage.getItem(OATag+"token");
        $scope.editorpwd= function () {
        //判断两次密码是否一致
            if ($scope.pwd1!=$scope.pwd2) {
                alert('两次密码不一致，请重输');
                return;
            }
            $http.jsonp(url + 'c=CPublicLogin&a=updatePwdFirst&token=' + token + '' +
                '&pwd=' + $scope.pwd1 + '&user_tid='+tid+'&tid='+tid+'&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        alert("修改成功，请重新登陆")
                        window.location.assign('#/login');
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };


    }]);