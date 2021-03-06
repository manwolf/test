//教研系统管理员登录界面

var loginControllers = angular.module('loginControllers', ['httpService']);
loginControllers.controller('loginController', ['$scope', '$http', '$location'
    , function ($scope, $http, $location) {
        loading();
        $('html,body').width('100%').height('100%');
        $scope.login=function () {
            $http.jsonp(url + 'c=CPublicLogin&a=landAll&telephone=' + $scope.telephone + '&pwd=' + $scope.pwd + '&callback=JSON_CALLBACK')
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.lists = data.data;
                        setMarketUserJsonCache(marketEnum.marketUserBaseInfo, data.data[0]);

                        //将数据存到缓存
                        //var city=data.data[0].city;
                        //if(city=='全国'){
                        //    $('selectCity').modal.('show');
                        //    $http.jsonp(url + 'c=areaListCtr&a=queryCity&callback=JSON_CALLBACK')
                        //        .success(function (data) {
                        //            if (data.code == 0) {
                        //                $scope.lists=data.data;
                        //                $scope.city = [];//创建城市数组
                        //                $scope.lists.forEach(function(v,k){
                        //                    this.push(v.area_city);
                        //                },$scope.city);//将查询返回数据中城市数据存入创建的城市数组
                        //                logUtil("city------->",$scope.city)
                        //            }
                        //        });
                        //    city=$scope.selectCity;
                        //}
                        window.localStorage.setItem(OATag+"tid", data.data[0].tid);
                        window.localStorage.setItem(OATag+"user_name", data.data[0].user_name);
                        window.localStorage.setItem(OATag+"city", data.data[0].city);
                        window.localStorage.setItem(OATag+"token", data.data[0].token);
                        window.localStorage.setItem(OATag+"roles_name", data.data[0].roles_name);
                        //判断用户是否为第一次登录
                        logUtil("login_state-------->",data.data[0].login_state)
                        logUtil("tid-------->",data.data[0].tid)
                        logUtil("city-------->",data.data[0].city)
                        var sta = data.data[0].login_state;
                        if (sta == 0) {
                            //第一次登录修改密码
                            window.location.assign('#/loginFirst')

                        } else if(sta==1){
                            window.location.assign('#/default');
                        }
                    }
                    else {
                        alert(data.msg);
                    }
                })
        };
    }]);
