/**
 * Created by xinfeng on 2015/8/10.
 */

var logTag = 'eTLog------>';

function logUtil(flag,msg){
    if(isDebug) {
        console.log(logTag + flag + msg);
    }
}