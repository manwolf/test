/**
 * Created by xinfeng on 2015/8/12.
 */
/**
 * Created by xinfeng on 2015/8/10.
 */

//�û�tid���������¼���ˡ�
function isMarketLogin(){
    var userTid = window.localStorage.getItem(Tag + 'tid');
    if(!isEmpty(userTid)){
        return true;
    }

    return false;
}

//�洢��½�û���json����
function setMarketUserJsonCache(key,jsonObi){
    window.localStorage.setItem(OATag+ key, JSON.stringify(jsonObi));
}
//ȡ����½�û���json����
function getMarketUserJsonCache(key){
    return JSON.parse(window.localStorage.getItem(OATag+ key));

}

//ȡ���û�tid
function getMarketUserTid(){
    var marketUserTid = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).tid;
    //logUtil('marketUserTid----->',marketUserTid);
    if(isEmptyStr(marketUserTid)){
        marketUserTid =  -999;
    }
    return marketUserTid;
}

//ȡ���û�token
function getMarketUserToken(){
    var marketUserToken = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).token;
    //logUtil('marketUserToken----->',marketUserToken);
    console.log('marketUserToken----->',marketUserToken)
    if(isEmptyStr(marketUserToken)){
        marketUserToken =  '';
    }
    return marketUserToken;
}

//ȡ���û���
function getMarketUserName(){
    var marketUserName = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).user_name;
    //logUtil('marketUserName----->',marketUserName);
    console.log('marketUserName----->',marketUserName)
    if(isEmptyStr(marketUserName)){
        marketUserName =  '';
    }
    return marketUserName;
}

//ȡ���û�city
function getMarketUserCity(){
    var marketUserCity = getMarketUserJsonCache(marketEnum.marketUserBaseInfo).sc_city;
    //logUtil('marketUserCity----->',marketUserCity);
    console.log('marketUserCity----->',marketUserCity)
    if(isEmptyStr(marketUserCity)){
        marketUserCity =  '';
    }
    return marketUserCity;
}









