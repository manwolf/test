var url;
var imgUrl;
var isDebug =4;
var OATag;
if (isDebug==1) {
    imgUrl = 'http://testapi.e-teacher.cn/srvapi/framework/controller/upLoadForH5.php?';
    courseImgUrl='http://testapi.e-teacher.cn/srvapi/framework/controller/upLoadImage.php?';
    url = 'http://testapi.e-teacher.cn/srvapi/framework?';
    OATag = "OA_test";
} else if(isDebug==2){
    imgUrl = 'http://api.e-teacher.cn/srvapi/framework/controller/upLoadForH5.php?';
    courseImgUrl='http://api.e-teacher.cn/srvapi/framework/controller/upLoadImage.php?';
    url = 'http://api.e-teacher.cn/srvapi/framework?';
    OATag = "OA_formal";
}else if(isDebug==3){
    imgUrl = 'http://rdoa.e-teacher.cn//srvapi/framework/controller/upLoadForH5.php?';
    courseImgUrl='http://rdoa.e-teacher.cn/srvapi/framework/controller/upLoadImage.php?';
    url = 'http://rdoa.e-teacher.cn//srvapi/framework?';
    OATag = "OA_rdoa";
}else if(isDebug==4){
    imgUrl = 'http://localhost/srvapi/framework/controller/upLoadForH5.php?';
    courseImgUrl='http://localhost/srvapi/framework/controller/upLoadImage.php?';
    url = 'http://localhost/srvapi/framework?';
    OATag = "OA_localhost";
}
