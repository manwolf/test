/**
 * Created by daiyingying on 2015/9/1.
 */
//输入框去除空格
String.prototype.Trim = function () {
    return this.replace(/\s+/g, "");
}

//去除换行
function ClearBr(key) {
    key = key.replace(/<\/?.+?>/g, "");
    key = key.replace(/[\r\n]/g, "");
    return key;
}

//显示登陆者类型
function showLabder(type,landerType){
    if(type=='SC'){
        landerType="市场专员"
    }
    if(type=='SC'){
        landerType="市场管理员"
    }
    return landerType;
}