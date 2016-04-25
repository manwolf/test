/**
 * Created by xinfeng on 2015/8/12.
 */
/**
 * Created by xinfeng on 2015/8/10.
 */

//用户tid，存在则登录过了。
function isMarketLogin(){
    var userTid = window.localStorage.getItem(Tag + 'tid');
    if(!isEmpty(userTid)){
        return true;
    }

    return false;
}

//存储登陆用户的json对象
function setMarketUserJsonCache(key,jsonObi){
    window.localStorage.setItem(OATag+ key, JSON.stringify(jsonObi));
}
//取出登陆用户的json对象
function getMarketUserJsonCache(key){
    return JSON.parse(window.localStorage.getItem(OATag+ key));

}

//取得用户tid
function getMarketUserTid(){
    var marketUserTid = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).tid;
    //logUtil('marketUserTid----->',marketUserTid);
    if(isEmptyStr(marketUserTid)){
        marketUserTid =  -999;
    }
    return marketUserTid;
}

//取得用户token
function getMarketUserToken(){
    var marketUserToken = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).token;
    //logUtil('marketUserToken----->',marketUserToken);
    console.log('marketUserToken----->',marketUserToken)
    if(isEmptyStr(marketUserToken)){
        marketUserToken =  '';
    }
    return marketUserToken;
}

//取得用户名
function getMarketUserName(){
    var marketUserName = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).user_name;
    //logUtil('marketUserName----->',marketUserName);
    console.log('marketUserName----->',marketUserName)
    if(isEmptyStr(marketUserName)){
        marketUserName =  '';
    }
    return marketUserName;
}

//取得用户city
function getMarketUserCity(){
    var marketUserCity = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).sc_city;
    //logUtil('marketUserCity----->',marketUserCity);
    console.log('marketUserCity----->',marketUserCity)
    if(isEmptyStr(marketUserCity)){
        marketUserCity =  '';
    }
    return marketUserCity;
}









