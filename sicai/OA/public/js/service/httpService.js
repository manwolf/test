/**
 * Created by xinfeng on 2015/8/9.
 */
var httpService = angular.module('httpService', []);

httpService.factory('eTHttp',function($http){//ע��http����

    //paramsֻ���𴫵ݲ�����Ȼ��ƴ�ӳ�һ�������Url
    var runUserRequest = function(params){
            return $http({
                    method:'JSONP',
                    url:url + params + "&callback=JSON_CALLBACK"
            });
    };

    //����һ�����󵽵ķ������
    return{
        resultData:function(params){
            return runUserRequest(params);
        }
    };

});
