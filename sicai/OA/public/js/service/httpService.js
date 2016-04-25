/**
 * Created by xinfeng on 2015/8/9.
 */
var httpService = angular.module('httpService', []);

httpService.factory('eTHttp',function($http){//注入http服务

    //params只负责传递参数，然后拼接成一个请求的Url
    var runUserRequest = function(params){
            return $http({
                    method:'JSONP',
                    url:url + params + "&callback=JSON_CALLBACK"
            });
    };

    //返回一个请求到的服务对象
    return{
        resultData:function(params){
            return runUserRequest(params);
        }
    };

});
