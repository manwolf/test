/**
 * Created by Christina on 2015/8/12.
 */
var MarketingController = angular.module('MarketingController', ['httpService']);

/******************************************市场系统**********************************************/
//邀请码管理界面
MarketingController.controller('ManageScController', ['$scope', '$http', '$location', 'eTHttp'
    , function ($scope, $http, $location, eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');

        //取出登入用户相关信息的json对象
        getMarketUserJsonCache(marketEnum.marketUserBaseInfo);
        //取出登陆用户名
        $scope.scLanderName = getMarketUserName();
        $scope.user_tid = getMarketUserTid();
        $scope.user_token = getMarketUserToken();
        //用户退出市场系统
        $scope.quit = function () {
            $("#quitModal").modal('show');
        }
        $scope.confirmQuit = function () {
            $("#quitModal").modal('hide');
            //跳转页面延时
            setTimeout("window.location.assign('#/SClogin')", 700);
        }
        //用户返回市场管理
        $scope.goSCmanage = function () {
            window.location.assign('#/SCmanage');
        }
        //首页tab的设置
        $(function () {
            $('#inviteTab a:first').tab('show');//初始化显示哪个tab
            $('#inviteTab a').click(function (e) {
                e.preventDefault();//阻止a链接的跳转行为
                $(this).tab('show');//显示当前选中的链接及关联的content
            })
        })

        //获取邀请码信息
        var getCodeParams = 'c=SCInvitationCtr&a=queryInvitationinfo&state=' + 3 +
            '&invitation_name=' + ''
            + '&user_tid=' + $scope.user_tid
            + '&token=' + $scope.user_token;
        $scope.currentpage = 1;
        $scope.currentParams = getCodeParams;
        //获取邀请码接口
        $scope.getCodeList = function (getCodeParams) {
            getCodeParams = getCodeParams + '&page=' + 1;
            eTHttp.resultData(getCodeParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.inviteLists = data.data;
                        $scope.totalpages = data.pages;
                        //当pages的值为NULL时显示为1
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                        }
                    } else {
                        $scope.inviteLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //点击事件搜索邀请码
        $scope.searchInvite = function () {
            $scope.currentpage = 1;
            var arg = arguments[0];
            if ($scope.invitation_name == null) {
                $scope.invitation_name = '';
            }
            if ($scope.currentState == null) {
                $scope.currentState = '';
            }
            if (arg != null) {
                $scope.currentState = arg;
                getCodeParams = 'c=SCInvitationCtr&a=queryInvitationinfo&state=' + arg +
                    '&invitation_name=' + $scope.invitation_name
                    + '&user_tid=' + $scope.user_tid
                    + '&token=' + $scope.user_token;
                $scope.currentParams = getCodeParams;
            } else {
                getCodeParams = 'c=SCInvitationCtr&a=queryInvitationinfo&state=' + $scope.currentState +
                    '&invitation_name=' + $scope.invitation_name
                    + '&user_tid=' + $scope.user_tid
                    + '&token=' + $scope.user_token;
                $scope.currentParams = getCodeParams;
            }

            $scope.getCodeList(getCodeParams);
        }

        //转入某分页
        $scope.loadPage = function (page) {
            getCodeParams = $scope.currentParams + '&page=' + page;
            eTHttp.resultData(getCodeParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.inviteLists = data.data;
                        $scope.totalpages = data.pages;
                        //当pages的值为NULL时显示为1
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                        }
                    } else {
                        $scope.inviteLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }

        //页面初始化显示第一页
        $scope.loadPage(1);

        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.loadPage(1);
        };

        //下一页
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpages) {
                $scope.currentpage++;
                $scope.loadPage($scope.currentpage);
            }
        };

        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.loadPage($scope.currentpage);
            }
        };

        //尾页
        $scope.endpage = function () {
            $scope.currentpage = $scope.totalpages;
            $scope.loadPage($scope.totalpages);
        };

        //查询具体某页数
        $scope.goThePage = function () {
            if ($scope.page <= $scope.totalpages) {
                $scope.currentpage = $scope.page;
                $scope.loadPage($scope.page);
            } else {
                alert("该页码不存在！");
            }
        }

        //弹出新建邀请码modal
        $scope.AuthLimited = function () {
            $('#NewInviteCodeModal').modal('show');
        }
        //新建邀请码
        var newCodeParams = '';
        $scope.invitation_code_prefix = "";
        $scope.CreatInviteCode = function (newCodeParams) {
            eTHttp.resultData(newCodeParams)
                .success(function (data) {
                    if (data.code == 0) {
                        alert('您创建邀请码成功！');
                        //新建邀请码完成后重新显示邀请码
                        $scope.loadPage(1);
                        $('#NewInviteCodeModal').modal('hide');
                    } else {
                        alert(data.msg);
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //创建邀请码点击事件
        $scope.addInviteCode = function () {
            if (isEmptyStr($scope.invitation_name)) {
                alert('请填写邀请码');
                return;
            }
            if (isEmptyStr($scope.invitation_amount)) {
                alert('请填发放总量');
                return;
            }
            if (parseInt($scope.invitation_amount) < 0 || parseInt($scope.invitation_amount) > 9999) {
                alert('发放总量请输入0~9999的值');
                return;
            }
            //if (isEmptyStr($scope.invitation_discount)) {
            //    alert('请填写优惠折扣');
            //    return;
            //}
            if (parseInt($scope.invitation_discount) < 0 || parseInt($scope.invitation_discount) >= 1) {
                alert('优惠折扣为0~1(包括0)的数');
                return;
            }
            if (isEmptyStr($scope.invitation_times)) {
                alert('请填写最大使用次数');
                return;
            }
            if (parseInt($scope.invitation_times) < 0 || parseInt($scope.invitation_times) > 9999) {
                alert('最大使用次数为1~9999之间的数');
                return;
            }
            if (parseInt($scope.invitation_times) < 0) {
                alert('每张邀请码的有效次数至少为1');
                return;
            }
            if (isEmptyStr($scope.invitation_start_date)) {
                alert('请填写生效时间');
                return;
            }
            if (isEmptyStr($scope.invitation_end_date)) {
                alert('请填写过期时间');
                return;
            }
            if (isEmptyStr($scope.invitation_city)) {
                alert('请填写城市');
                return;
            }
            //日期控件取值
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            //去除输入框空格及换行
            $scope.invitation_code_prefix = ClearBr($scope.invitation_code_prefix.Trim());
            $scope.invitation_name = ClearBr($scope.invitation_name.Trim());
            //创建邀请码接口
            newCodeParams = 'c=SCInvitationCtr&a=createInvitation&invitation_name=' + $scope.invitation_name
                + '&invitation_amount=' + $scope.invitation_amount
                + '&invitation_discount=' + $scope.invitation_discount
                + '&invitation_start_date=' + start_date
                + '&invitation_end_date=' + end_date
                + '&invitation_city=' + $scope.invitation_city
                + '&invitation_code_prefix=' + $scope.invitation_code_prefix
                + '&invitation_times=' + $scope.invitation_times
                + '&user_tid=' + $scope.user_tid
                + '&token=' + $scope.user_token;
            $scope.CreatInviteCode(newCodeParams);
        }

        //进入码库页面
        $scope.SClibrary = function (item) {
            window.location.assign('#/SClibrary?eachCodeTid=' + item.tid);
        };

        //市场主管使得邀请码失效
        $scope.makeInvalidModal = function (item) {
            $('#InvalidModal').modal('show');
            $scope.codeItem = item;
        }

        $scope.makeInvitationInvalid = function (invalidCodeParams, codeItem) {
            eTHttp.resultData(invalidCodeParams)
                .success(function (data) {
                    if (data.code == 0) {
                        alert("邀请码已失效");
                        codeItem.invitation_valid = "已失效";
                        $('#InvalidModal').modal('hide');
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }

        $scope.invitationInvalid = function (codeItem) {
            var invalidCodeParams = 'c=SCInvitationCtr&a=invitationInvalid&tid=' + codeItem.tid
                + '&user_tid=' + $scope.user_tid
                + '&token=' + $scope.user_token;
            $scope.makeInvitationInvalid(invalidCodeParams, codeItem);
        }
    }]);

//邀请码码库界面
MarketingController.controller('LibraryScController', ['$scope', '$http', '$location', 'eTHttp'
    , function ($scope, $http, $location, eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        //取出登入用户相关信息的json对象
        getMarketUserJsonCache(marketEnum.marketUserBaseInfo);
        //取出登陆用户名
        $scope.scLanderName = getMarketUserName();
        $scope.user_tid = getMarketUserTid();
        $scope.user_token = getMarketUserToken();
        //用户退出市场系统
        $scope.quit = function () {
            $("#quitModal").modal('show');
        }
        $scope.confirmQuit = function () {
            $("#quitModal").modal('hide');
            //跳转页面延时
            setTimeout("window.location.assign('#/SClogin')", 700);
        }
        //返回市场管理首页
        $scope.goSCmanage = function () {
            window.location.assign('#/SCmanage');
        }
        //获取某一邀请码的tid
        var inviteCodetid = $location.search()['eachCodeTid'];

        //码库页tab的设置
        $(function () {
            $('#inviteTab a:first').tab('show');//初始化显示哪个tab
            $('#inviteTab a').click(function (e) {
                e.preventDefault();//阻止a链接的跳转行为
                $(this).tab('show');//显示当前选中的链接及关联的content
            })
        })

        //获取码库的信息
        var libraryCodePrams = 'c=SCInvitationCtr&a=askInvitationUseList&state=' + 3
            + '&invitation_info_tid=' + inviteCodetid + '&invitation_code=' + ''
            + '&user_tid=' + $scope.user_tid
            + '&token=' + $scope.user_token;
        $scope.currentParams = libraryCodePrams;
        $scope.currentpage = 1;
        //码库接口
        $scope.getInvitionCode = function (libraryCodePrams) {
            libraryCodePrams = libraryCodePrams + '&page=' + 1;
            eTHttp.resultData(libraryCodePrams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.invitionLists = data.data;
                        $scope.totalpages = data.pages;
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                        }
                    } else {
                        $scope.invitionLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }

        //点击事件搜索码库
        $scope.searchInvition = function () {
            $scope.currentpage = 1;
            var arg = arguments[0];
            if ($scope.currentState == null) {
                $scope.currentState = '';
            }
            if ($scope.invitation_code == null) {
                $scope.invitation_code = '';
            }
            if (arg != null) {
                $scope.currentState = arg;
                var libraryCodePrams = 'c=SCInvitationCtr&a=askInvitationUseList&state=' + arg +
                    '&invitation_info_tid=' + inviteCodetid
                    + '&user_tid=' + $scope.user_tid
                    + '&token=' + $scope.user_token +
                    '&invitation_code=' + $scope.invitation_code;
                $scope.currentParams = libraryCodePrams;
            } else {
                var libraryCodePrams = 'c=SCInvitationCtr&a=askInvitationUseList&state=' +
                    $scope.currentState
                    + '&user_tid=' + $scope.user_tid
                    + '&token=' + $scope.user_token + '&invitation_info_tid=' +
                    inviteCodetid + '&invitation_code=' + $scope.invitation_code;
                $scope.currentParams = libraryCodePrams;
            }
            $scope.getInvitionCode(libraryCodePrams);

        }
        //转入某分页
        $scope.loadPage = function (page) {
            libraryCodePrams = $scope.currentParams + '&page=' + page;
            eTHttp.resultData(libraryCodePrams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.invitionLists = data.data;
                        $scope.totalpages = data.pages;
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                        }
                    } else {
                        $scope.inviteLists = [];
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;

                    }

                })
                .error(function (err) {
                    console.log(err);
                })
        }

        //页面初始化显示第一页
        $scope.loadPage(1);

        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.loadPage(1);
        };
        //下一页
        $scope.currentpage = 1;
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpages) {
                $scope.currentpage++;
                $scope.loadPage($scope.currentpage);
            }
        };
        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.loadPage($scope.currentpage);
            }
        };

        //尾页
        $scope.endpage = function () {
            $scope.currentpage = $scope.totalpages;
            $scope.loadPage($scope.totalpages);
        };

        //查询某特定页
        $scope.goThePage = function () {
            if ($scope.page <= $scope.totalpages) {
                $scope.currentpage = $scope.page;
                $scope.loadPage($scope.page);
            } else {
                alert("该页码不存在！");
            }
        }

        //是否确认导出数据弹框
        $scope.confirmExportData = function () {
            $('#sureExportData').modal('show');
            $scope.codeItem = item;
        }
        //导出数据
        $scope.filetype = 'xls';
        $scope.exportData = function () {
            if ($scope.currentState == null) {
                $scope.currentState = '';
            }
            var exportUrl = url + 'c=SCInvitationCtr&a=exportFile&token=' + $scope.user_token +
                '&state=' + $scope.currentState + '&callback=JSON_CALLBACK'
                + '&user_tid=' + $scope.user_tid +
                '&tid=' + inviteCodetid + '&filetype=' + $scope.filetype;
            window.location.href = exportUrl;
            $('#sureExportData').modal('hide');
        }
    }]);

//市场递推人员管理界面
MarketingController.controller('promotorScController', ['$scope', '$http', '$location', 'eTHttp'
    , function ($scope, $http, $location, eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        //取出登入用户相关信息的json对象
        getMarketUserJsonCache(marketEnum.marketUserBaseInfo);
        //取出登陆用户名
        $scope.scLanderName = getMarketUserName();
        $scope.user_tid = getMarketUserTid();
        $scope.user_token = getMarketUserToken();

        //用户退出市场系统
        $scope.quit = function () {
            $("#quitModal").modal('show');
        }
        $scope.confirmQuit = function () {
            $("#quitModal").modal('hide');
            //跳转页面延时
            setTimeout("window.location.assign('#/SClogin')", 700);
        }
        //获取市场递推人员信息
        $scope.ifexport = 0;
        $scope.filetype = 'xls';
        var getPromoterParams = 'c=SCDataCtr&a=queryScUser&time=' + '' +
            '&state=' + '' + '&name=' + '' + '&user_tid=' + $scope.user_tid
            + '&ifexport=' + $scope.ifexport + '&filetype=' + $scope.filetype + '&token=' + $scope.user_token;
        $scope.currentpage = 1;
        $scope.currentParams = getPromoterParams;
        //获取递推人员列表接口
        $scope.getPromoterList = function (getPromoterParams) {
            getPromoterParams = getPromoterParams + '&page=' + 1;
            eTHttp.resultData(getPromoterParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.PromoterLists = data.data;
                        $scope.totalpages = data.pages;
                        //当pages的值为NULL时显示为1
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                            $scope.currentpage = 1;
                        }

                    } else {
                        $scope.PromoterLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //点击事件搜索递推人员
        $scope.argTime = '';
        $scope.argState = '';
        $scope.searchPromotor = function () {
            var pSearchType = arguments[0];
            var paramsValue = arguments[1];
            //大类筛选
            if (pSearchType == 0) {
                if (paramsValue == 4) {
                    $scope.argTime = '';
                } else {
                    $scope.argTime = paramsValue;
                }
            } else if (pSearchType == 1) {
                if (paramsValue == 3) {
                    $scope.argState = '';
                } else {
                    $scope.argState = paramsValue;
                }
            }
            else if (pSearchType == 3) {
                $scope.ifexport = '1';
            }
            if (isEmptyStr($scope.promotor_name)) {
                $scope.promotor_name = '';

            }
            getPromoterParams = 'c=SCDataCtr&a=queryScUser&time=' + $scope.argTime +
                '&state=' + $scope.argState + '&name=' + $scope.promotor_name
                + '&ifexport=' + $scope.ifexport + '&filetype=' + $scope.filetype
                + '&user_tid=' + $scope.user_tid
                + '&token=' + $scope.user_token;
            $scope.getPromoterList(getPromoterParams);
            $scope.ifexport = '0';
            if (pSearchType == 3) {
                window.location.href = url + getPromoterParams + "&callback=JSON_CALLBACK";
                $('#sureExportData').modal('hide');
            }
        }
        //$scope.getPromoterList(getPromoterParams);
        //转入某分页
        $scope.loadPage = function (page) {
            getPromoterParams = $scope.currentParams + '&page=' + page;
            eTHttp.resultData(getPromoterParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.PromoterLists = data.data;
                        $scope.totalpages = data.pages;
                        //当pages的值为NULL时显示为1
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                            $scope.currentpage = 1;
                        }
                    } else {
                        $scope.PromoterLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }

        //页面初始化显示第一页
        $scope.loadPage(1);

        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.loadPage(1);
        };

        //下一页
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpages) {
                $scope.currentpage++;
                $scope.loadPage($scope.currentpage);
            }
        };

        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.loadPage($scope.currentpage);
            }
        };

        //尾页
        $scope.endpage = function () {
            $scope.currentpage = $scope.totalpages;
            $scope.loadPage($scope.totalpages);
        };

        //查询具体某页数
        $scope.goThePage = function () {
            if ($scope.page <= $scope.totalpages) {
                $scope.currentpage = $scope.page;
                $scope.loadPage($scope.page);
            } else {
                alert("该页码不存在！");
            }
        }

        //进入市场数据详细界面
        $scope.turnDataDetails = function (item) {
            window.location.assign('#/scDataDetails?sc_num=' + item.tid);
        }
        //转变岗位状态modal
        $scope.turnJobStateModal = function () {
            $('#changJobState').modal('show');
        }

        //确认改变岗位状态
        $scope.TurnState = function (getTurnStateParams) {
            if (isEmptyStr($scope.scNumber)) {
                alert('请填写市场专员编号');
                return;
            }
            if (isEmptyStr($scope.scState)) {
                alert('请填写转换状态');
                return;
            }
            eTHttp.resultData(getTurnStateParams)
                .success(function (data) {
                    if (data.code == 0) {
                        alert("调整岗位状态成功！");
                        $scope.loadPage(1);
                        $('#changJobState').modal('hide');
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        $scope.confirmTurnState = function () {
            var getTurnStateParams = 'c=SCDataCtr&a=updateSCUserstate&num=' + $scope.scNumber +
                '&state=' + $scope.scState + '&user_tid=' + $scope.user_tid
                + '&token=' + $scope.user_token;
            $scope.TurnState(getTurnStateParams);
        }

        //新增市场专员modal
        $scope.addPromotor = function () {
            $('#addPromotorModal').modal('show');
        }
        //确认新增市场专员
        $scope.confirmAddPromotor = function () {

            if (isEmptyStr($scope.addSc_name)) {
                alert('请填写姓名');
                return;
            }
            if (isEmptyStr($scope.addSc_num)) {
                alert('请填写编号');
                return;
            }
            if (isEmptyStr($scope.addSc_city)) {
                alert('请填写城市');
                return;
            }
            if (isEmptyStr($scope.addSc_phone)) {
                alert('请填写电话号码');
                return;
            }
            $scope.newPromotor = function (getNewPromotorParams) {
                eTHttp.resultData(getNewPromotorParams)
                    .success(function (data) {
                        if (data.code == 0) {
                            alert("新增市场专员成功！");
                            $scope.loadPage(1);
                            $('#addPromotorModal').modal('hide');

                        } else {
                            alert("新增市场专员失败！");
                        }
                    })
                    .error(function (err) {
                        console.log(err);
                    })
            }
            var getNewPromotorParams = 'c=AddOaUserCtr&a=addSC&user_tid=' + $scope.user_tid +
                '&city=' + $scope.addSc_city +
                '&telephone=' + $scope.addSc_phone +
                '&roles_name=' + '市场专员' +
                '&user_name=' + $scope.addSc_name +
                '&token=' + $scope.user_token +
                '&num=' + $scope.addSc_num;
            $scope.newPromotor(getNewPromotorParams);
        }
        //是否确认导出数据弹框
        $scope.confirmExportData = function () {
            $('#sureExportData').modal('show');
        }

        //筛选条件设置样式
        $("#select1 dd").click(function () {
            $(this).addClass("selected").siblings().removeClass("selected");
            if ($(this).hasClass("select-all")) {
                $("#selectA").remove();
            } else {
                var copyThisA = $(this).clone();
                if ($("#selectA").length > 0) {
                    $("#selectA a").html($(this).text());
                } else {
                    $(".select-result dl").append(copyThisA.attr("id", "selectA"));
                }
            }
        });

        $("#select2 dd").click(function () {
            $(this).addClass("selected").siblings().removeClass("selected");
            if ($(this).hasClass("select-all")) {
                $("#selectB").remove();
            } else {
                var copyThisB = $(this).clone();
                if ($("#selectB").length > 0) {
                    $("#selectB a").html($(this).text());
                } else {
                    $(".select-result dl").append(copyThisB.attr("id", "selectB"));
                }
            }
        });

        $("#select")


    }]);

//市场数据管理详情界面
MarketingController.controller('scDataDetailsScController', ['$scope', '$http', '$location', 'eTHttp'
    , function ($scope, $http, $location, eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        //取出登入用户相关信息的json对象
        getMarketUserJsonCache(marketEnum.marketUserBaseInfo);
        //取出登陆用户的类型
        var sc_login_type = window.localStorage.getItem(OATag + "sc_login_type");
        //登陆者类型显示
        $scope.scLanderType = window.localStorage.getItem(OATag + "scLanderType");
        //取出登陆用户名
        $scope.scLanderName = getMarketUserName();
        $scope.user_tid = getMarketUserTid();
        $scope.user_token = getMarketUserToken();
        //用户退出市场系统
        $scope.quit = function () {
            $("#quitModal").modal('show');
        }
        $scope.confirmQuit = function () {
            $("#quitModal").modal('hide');
            //跳转页面延时
            setTimeout("window.location.assign('#/SClogin')", 700);
        }

        //获取某一市场递推人员编号
        var sc_num = $location.search()['sc_num'];
        if (isEmptyStr(sc_num)) {
            sc_num = '';
        }
        //获取某一递推人员详细信息
        $scope.ifexport = 0;
        $scope.filetype = 'xls';
        var getDetailParams = 'c=SCDataCtr&a=querySingleScUser&time=' + '' +
            '&state=' + '' + '&telephone=' + '' + '&tid=' + sc_num
            + '&ifexport=' + $scope.ifexport + '&filetype=' + $scope.filetype
            + '&user_tid=' + $scope.user_tid
            + '&token=' + $scope.user_token;
        $scope.currentpage = 1;
        $scope.currentParams = getDetailParams;
        //获取详细信息接口
        $scope.getDetailList = function (getDetailParams) {
            getDetailParams = getDetailParams + '&page=' + 1;
            eTHttp.resultData(getDetailParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.DetailLists = data.data;
                        $scope.totalpages = data.pages;
                        //当pages的值为NULL时显示为1
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                            $scope.currentpage = 1;
                        }
                    } else {
                        $scope.DetailLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }
        //$scope.getDetailList(getDetailParams);

        //点击事件搜索详细信息
        $scope.argTime = '';
        $scope.argState = '';
        $scope.searchDetail = function () {
            var pSearchType = arguments[0];
            var paramsValue = arguments[1];
            //大类筛选
            if (pSearchType == 0) {
                if (paramsValue == 4) {
                    $scope.argTime = '';
                } else {
                    $scope.argTime = paramsValue;
                }
            } else if (pSearchType == 1) {
                if (paramsValue == 5) {
                    $scope.argState = '';
                } else {
                    $scope.argState = paramsValue;
                }
            } else if (pSearchType == 3) {
                $scope.ifexport = 1;
            }
            if (isEmptyStr($scope.detail_telephone)) {
                $scope.detail_telephone = '';

            }
            var getDetailParams = 'c=SCDataCtr&a=querySingleScUser&time=' + $scope.argTime +
                '&state=' + $scope.argState + '&telephone=' + $scope.detail_telephone
                + '&tid=' + sc_num + '&ifexport=' + $scope.ifexport + '&filetype=' + $scope.filetype
                + '&user_tid=' + $scope.user_tid
                + '&token=' + $scope.user_token;
            $scope.getDetailList(getDetailParams);
            $scope.ifexport = 0;
            if (pSearchType == 3) {
                window.location.href = url + getDetailParams + "&callback=JSON_CALLBACK";
                $('#sureExportData').modal('hide');
            }
        }


        //转入某分页
        $scope.loadPage = function (page) {
            getDetailParams = $scope.currentParams + '&page=' + page;
            eTHttp.resultData(getDetailParams)
                .success(function (data) {
                    if (data.code == 0) {
                        $scope.DetailLists = data.data;
                        $scope.totalpages = data.pages;
                        //当pages的值为NULL时显示为1
                        if ($scope.totalpages == null || $scope.totalpages == '' ||
                            $scope.totalpages == undefined) {
                            $scope.totalpages = 1;
                            $scope.currentpage = 1;
                        }
                    } else {
                        $scope.DetailLists = [];
                        //当邀请码不存在是pages为0
                        $scope.totalpages = 0;
                        $scope.currentpage = 0;
                    }
                })
                .error(function (err) {
                    console.log(err);
                })
        }

        //页面初始化显示第一页
        $scope.loadPage(1);

        //首页
        $scope.goHome = function () {
            $scope.currentpage = 1;
            $scope.loadPage(1);
        };

        //下一页
        $scope.next = function () {
            if ($scope.currentpage < $scope.totalpages) {
                $scope.currentpage++;
                $scope.loadPage($scope.currentpage);
            }
        };

        //上一页
        $scope.prev = function () {
            if ($scope.currentpage > 1) {
                $scope.currentpage--;
                $scope.loadPage($scope.currentpage);
            }
        };

        //尾页
        $scope.endpage = function () {
            $scope.currentpage = $scope.totalpages;
            $scope.loadPage($scope.totalpages);
        };

        //查询具体某页数
        $scope.goThePage = function () {
            if ($scope.page <= $scope.totalpages) {
                $scope.currentpage = $scope.page;
                $scope.loadPage($scope.page);
            } else {
                alert("该页码不存在！");
            }
        }

        //是否确认导出数据弹框
        $scope.confirmExportData = function () {
            $('#sureExportData').modal('show');
        }


        //筛选条件设置样式
        $("#select1 dd").click(function () {
            $(this).addClass("selected").siblings().removeClass("selected");
            if ($(this).hasClass("select-all")) {
                $("#selectA").remove();
            } else {
                var copyThisA = $(this).clone();
                if ($("#selectA").length > 0) {
                    $("#selectA a").html($(this).text());
                } else {
                    $(".select-result dl").append(copyThisA.attr("id", "selectA"));
                }
            }
        });

        $("#select2 dd").click(function () {
            $(this).addClass("selected").siblings().removeClass("selected");
            if ($(this).hasClass("select-all")) {
                $("#selectB").remove();
            } else {
                var copyThisB = $(this).clone();
                if ($("#selectB").length > 0) {
                    $("#selectB a").html($(this).text());
                } else {
                    $(".select-result dl").append(copyThisB.attr("id", "selectB"));
                }
            }
        });


    }]);

//首页滚动图片管理界面
MarketingController.controller('scrollingPictureScController', ['$scope', '$http', '$location', 'eTHttp'
    , function ($scope, $http, $location, eTHttp) {
        loading();
        $('html,body').width('100%').height('100%');
        //取出登入用户相关信息的json对象
        getMarketUserJsonCache(marketEnum.marketUserBaseInfo);
        //取出登陆用户的类型
        var sc_login_type = window.localStorage.getItem(OATag + "sc_login_type");
        //登陆者类型显示
        $scope.scLanderType = window.localStorage.getItem(OATag + "scLanderType");
        //取出登陆用户名
        $scope.scLanderName = getMarketUserName();
        //用户退出市场系统
        $scope.quit = function () {
            $("#quitModal").modal('show');
        }
        $scope.confirmQuit = function () {
            $("#quitModal").modal('hide');
            //跳转页面延时
            setTimeout("window.location.assign('#/SClogin')", 700);
        }

    }]);