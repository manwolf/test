/**
 * Created by pipe on 2015/4/20.
 */
$('body').waitMe({
    bg: '#fff',
    color:'#ff4a4a'
});
$(window).load(function () {
    setTimeout(function () {
        $('body').waitMe('hide');
    },1000)
});


var pageFlag;
//滚动翻页函数
function scrollPage(callback){
    pageFlag = true;
    $(window).on('scroll', function () {
        var bodyHeight = $('body').height();
        var windowHeight = $(window).height();
        var scrollTop = $(window).scrollTop();
        //滚动到底部的时候
        if(scrollTop + windowHeight - bodyHeight >= 0){
            if(pageFlag){
                callback();
            }
        }
    })
}
function getCurrentTime(num) {
    var currentDate = new Date();
    // console.log("currentDate.getDay()------>" + currentDate.getDay());
    if(num){
        var oneDay = 60*60*24*1000;
        currentDate.setTime(new Date().getTime()+oneDay*num);
    }
    var weeks = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六']
    return {
        full: currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1 < 10 ? '0' + (currentDate.getMonth() + 1) : currentDate.getMonth() + 1) + '-' +
        (currentDate.getDate() < 10 ? '0' + currentDate.getDate() : currentDate.getDate()),
        year: currentDate.getFullYear(),
        month: currentDate.getMonth() + 1 < 10 ? '0' + (currentDate.getMonth() + 1) : currentDate.getMonth() + 1,
        day: currentDate.getDate() < 10 ? '0' + currentDate.getDate() : currentDate.getDate(),
        week: weeks[currentDate.getDay()]
    }
}

// 查询 url参数
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}

//切换loading
function loading(){
    $('body').waitMe({
        bg: '#fff',
        color:'#ff4a4a'
    });
    setTimeout(function () {
        $('body').waitMe('hide');
    },1000)
}


//判断字符串是否为空

function isEmptyStr(str){
    if (str == null || str == undefined || str == '' || str== 'null') {
            return true;
    }
    return false;
}


//判断字符串是否为空(除了空字符串'')

function isEmptyStr2(str){
    if (str == null || str == undefined || str== 'null') {
        return true;
    }
    return false;

}



//判断字符串是否为数字  判断正整数 /^[1-9]+[0-9]*]*$/

function checkRate(num)
{
    var reg = new RegExp("^[0-9]*$");

    if (!reg.test(num))
    {
        return false;
    }else{
        return true;

    }
}


//将字符串类型转为int类型

function toInt(str){
    if(!isEmptyStr(str)){
        return parseInt(str);
    }else{
        return 0;
    }


}


//将字符串类型转为Float类型

function toFloat(str,num){
    if(!isEmptyStr(str)){
        return parseFloat(str).toFixed(num);
    }else{
        return 0;
    }


}

/**
 * 去掉字符串前后的空格
 * @param str 入参:要去掉空格的字符串
 * @returns
 */
function trimAll(str){
    return str.replace(/(^s*)|(s*$)/g, '');

};
/**
 * 去掉字符串前的空格
 * @param str 入参:要去掉空格的字符串
 * @returns
 */
function trimLeft(str){
    return str.replace(/^s*/g,'');

};
/**
 * 去掉字符串后的空格
 * @param str 入参:要去掉空格的字符串
 * @returns
 */
function trimRight(str){
    return str.replace(/s*$/,'');
}
/**
 * 判断字符串是否为空
 * @param str 传入的字符串
 * @returns
 */
function isEmpty(str){
    if(str != null && str.length > 0)
    {
        return true;
    }
    return false;
}
/**
 * 判断两个字符串子否相同
 * @param str1
 * @param str2
 * @returns {Boolean}
 */
function isEquals(str1,str2){
    if(str1==str2){
        return true;
    }
    return false;
}
/**
 * 忽略大小写判断字符串是否相同
 * @param str1
 * @param str2
 * @returns {Boolean}
 */
function isEqualsIgnorecase(str1,str2){
    if(str1.toUpperCase() == str2.toUpperCase())
    {
        return true;
    }
    return false;
}
/**
 * 判断js对象的长度
 * @param obj
 * @param min
 * @param max
 * @returns {Boolean}
 */
function checkLength(obj,min,max){
    if(obj.length < min || obj.length > max) {
        return false;
    } else {
        return true;
    }
}
/**
 * 判断是否是数字
 * @param value
 * @returns {Boolean}
 */
function isNum(value){
    if( value != null && value.length>0 && isNaN(value) == false){
        return true;
    }
    else{
        return false;
    }

}
/**
 * 判断是否是中文
 * @param str
 * @returns {Boolean}
 */
function isChine(str){
    var reg = /^([u4E00-u9FA5]|[uFE30-uFFA0])*$/;
    if(reg.test(str)){
        return false;
    }
    return true;
}
/**
 * 获取年
 * @returns
 */
function getYear() {
    var year = null;
    var dateTime = new Date();
    year = dateTime.getFullYear();

    return year;
}
/**
 * 获取月
 * @returns
 */
function getMonth() {
    var month = null;
    var dateTime = new Date();
    month = dateTime.getMonth() + 1;
    return month;
}
/**
 * 获取天
 * @returns
 */
function getDay() {
    var day = null;
    var dateTime = new Date();
    day = dateTime.getDate();
    return day;

}
/**
 * 获取小时
 * @returns
 */
function getHour() {
    var hour = null;
    var dateTime = new Date();
    hour = dateTime.getHours();
    return hour;
}
/**
 * 获取分钟
 * @returns
 */
function getMinute() {
    var minute = null;
    var dateTime = new Date();
    minute = dateTime.getMinutes();
    return minute;
}
/**
 * 获取秒
 * @returns
 */
function getSecond() {
    var second = null;
    var dateTime = new Date();
    second = dateTime.getSeconds();
    return second;
}

/**
 * 是否是闰年
 * @returns {Boolean}
 */
function isLeapYear(){
    var flag = false;
    if((this.getYear() % 4 == 0 && this.getYear() % 100 !=0)
        || (this.getYear() % 400 == 0)){
        flag = true;
    }
    return flag;
}

function a(){
    $(function () {
        $('#myTab a:first').tab('show');//初始化显示哪个tab

        $('#myTab a').click(function (e) {
            e.preventDefault();//阻止a链接的跳转行为
            $(this).tab('show');//显示当前选中的链接及关联的content
        })
    })
}

Date.prototype.format = function(format) {
    if (isNaN(this)) return '';
    var o = {
        'm+': this.getMonth() + 1,
        'd+': this.getDate(),
        'h+': this.getHours(),
        'n+': this.getMinutes(),
        's+': this.getSeconds(),
        'S': this.getMilliseconds(),
        'W': ["日", "一", "二", "三", "四", "五", "六"][this.getDay()],
        'q+': Math.floor((this.getMonth() + 3) / 3)
    };
    if (format.indexOf('am/pm') >= 0) {
        format = format.replace('am/pm', (o['h+'] >= 12) ? '下午' : '上午');
        if (o['h+'] >= 12) o['h+'] -= 12;
    }
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
}


//判断取回的数据的数组是否有值
function isArrayData(data){
    if(data.data != null && data.data.length > 0){
        return true;
    }
    return false;
}


//输入框去除空格
String.prototype.Trim = function() {
    return this.replace(/\s+/g, "");
}
//去除换行
function ClearBr(key) {
    key = key.replace(/<\/?.+?>/g,"");
    key = key.replace(/[\r\n]/g, "");
    return key;
}
//清空表单数据
function reset(){
    $("input").val("");
    $("textarea").val("");
    $("select").val("");
}