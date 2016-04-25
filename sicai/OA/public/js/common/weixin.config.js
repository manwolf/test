/**
 * Created by Administrator on 2015/4/10.
 */
//微信JSAPI前端校验
function wxConfigFun(configData) {
    console.log("请求后的sign参数：", configData);
    if (configData.code != 0 || configData.data == null) {
        alert(configData.msg);
        return;
    }
    var signParam = configData.data;
    //进行校验
    wx.config({
        debug: false,
        appId: signParam.appId,
        timestamp: signParam.timestamp,
        nonceStr: signParam.nonceStr,
        signature: signParam.signature,
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            // 'translateVoice',
            // 'startRecord',
            // 'stopRecord',
            // 'onRecordEnd',
            // 'playVoice',
            // 'pauseVoice',
            // 'stopVoice',
            // 'uploadVoice',
            // 'downloadVoice',
            // 'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            // 'scanQRCode',
            // 'chooseWXPay',
            // 'openProductSpecificView',
            // 'addCard',
            // 'chooseCard',
            // 'openCard',
            'closeWindow'
        ]
    });
}
//微信公众号标识
var publicNo = 'xtlj';
var configUrl = 'http://wx.fyxmt.com/app/' + publicNo + '/wxConfig?url=' + encodeURIComponent(location.href.split('#')[0]) + '&callback=wxConfigFun';
var scriptStr = '<scr' + 'ipt type="text/javascript" src="' + configUrl + '"></scr' + 'ipt>';
//alert('scriptUrl:'+ scriptStr);
document.write(scriptStr);


wx.ready(function () {
    console.log('分享内容:',window.shareData);
    // 2.1 监听“分享给朋友”，按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareAppMessage({
        title: window.shareData.tTitle,
        desc: window.shareData.tContent,
        link: window.shareData.tLink,
        imgUrl: window.shareData.imgUrl,
        trigger: function (res) {
            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
            //alert('用户点击发送给朋友');
        },
        success: function (res) {
            //alert('已分享');
        },
        cancel: function (res) {
            alert('已取消');
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });


    // 2.2 监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareTimeline({
        title: window.shareData.tTitle,
        desc: window.shareData.tContent,
        link: window.shareData.tLink,
        imgUrl: window.shareData.imgUrl,
        trigger: function (res) {
            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
            //alert('用户点击分享到朋友圈');
        },
        success: function (res) {
            //alert('已分享');
        },
        cancel: function (res) {
            alert('已取消');
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });

    // 2.3 监听“分享到QQ”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareQQ({
        title: window.shareData.tTitle,
        desc: window.shareData.tContent,
        link: window.shareData.tLink,
        imgUrl: window.shareData.imgUrl,
        trigger: function (res) {
            // alert('用户点击分享到QQ');
        },
        complete: function (res) {
            // alert(JSON.stringify(res));
        },
        success: function (res) {
            // alert('已分享');
        },
        cancel: function (res) {
            alert('已取消');
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });

    // 2.4 监听“分享到微博”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareWeibo({
        title: window.shareData.tTitle,
        desc: window.shareData.tContent,
        link: window.shareData.tLink,
        imgUrl: window.shareData.imgUrl,
        trigger: function (res) {
            // alert('用户点击分享到微博');
        },
        complete: function (res) {
            //alert(JSON.stringify(res));
        },
        success: function (res) {
            //alert('已分享');
        },
        cancel: function (res) {
            alert('已取消');
        },
        fail: function (res) {
            alert(JSON.stringify(res));
        }
    });




});