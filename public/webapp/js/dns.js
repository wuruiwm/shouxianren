var dns = "http://app.jinshouzhou.com/api/v1/";
var url = "http://app.jinshouzhou.com";
var g_serverApi = "http://app.jinshouzhou.com/api/";

//极光推送
function ReceiveMessage () {
    var ajpush = api.require('ajpush');
    if (api.systemType == 'ios') {
        api.addEventListener({
            name: 'noticeclicked'
        }, function(ret, err) {
            if (ret && ret.value) {
                var ajpush = ret.value;
                //alert(JOSN.stringify(ret));
            }
        })
    };

    if (api.systemType == 'android') {
        ajpush.init(function(ret) {
            if (ret && ret.status) {
                api.addEventListener({
                    name: 'appintent'
                }, function(ret, err) {
                    if (JSON.stringify(ret) && ret.appParam.ajpush) {
                        var ajpush = ret.appParam.ajpush; 
                        var jsonStr = ajpush.extra;
                        var path = jsonStr.path; 
                        var id = jsonStr.id;
                        switch (path) {
                            case "news":
                            localStorage.setItem("articleID",id)
                                api.openWin({
                                    name: 'article',
                                    url: 'widget://html/frame0/article.html',
                                    pageParam: {
                                        'path':path,
                                        'id': 26
                                    },
                                });

                                break;
                            case "action":
                            localStorage.setItem("articleID",id)
                                api.openWin({
                                    name: 'frame1_details',
                                    url: 'widget://html/frame1/frame1_details.html',
                                    pageParam: {
                                        'path':path,
                                        'id': id
                                    },
                                });
                                break;
                            case "notice":
                                api.openWin({
                                    name: 'messagedesc',
                                    url: 'widget://html/frame4/message/messagedesc.html',
                                    pageParam: {
                                        'path':path,
                                        'pageid': id,
                                    },
                                });
                                break;
                            case "merchant_order":
                                api.openWin({
                                    name: 'sjcenter',
                                    url: 'widget://html/frame4/sjcenter/index.html',
                                    pageParam: {
                                        'path':path
                                    },
                                });
                                break;
                            case "user_order":
                                api.openWin({
                                    name: 'order',
                                    url: 'widget://html/frame4/order/index.html',
                                    pageParam: {
                                        'path':path
                                    },
                                });
                                break;
                        }
                    }
                })
            }
        });
    }
}


//动态权限获取设置
function confirmPer(perm) {
    var has = hasPermission(perm); //检测权限
    if (!has || !has[0] || !has[0].granted) { //判断是否拥有权限
        api.confirm({
            title: '提醒',
            msg: '没有获得 ' + perm + " 权限\n是否前往设置？",
            buttons: ['去设置', '取消']
        }, function(ret, err) {
            if (1 == ret.buttonIndex) {
                reqPermission(perm);
            }
        });
        return false;
    }
    return true;
}

function hasPermission(one_per) { //检测应用是否有某个或多个权限
    var perms = new Array();
    if (one_per) {
        perms.push(one_per);
    } else {
        var prs = document.getElementsByName("p_list");
        for (var i = 0; i < prs.length; i++) {
            if (prs[i].checked) {
                perms.push(prs[i].value);
            }
        }
    }
    //alert("检测权限 = " + JSON.stringify(perms));
    var rets = api.hasPermission({
        list: perms
    });
    if (!one_per) {
        //  alert('判断结果：' + JSON.stringify(rets));
        return;
    }
    return rets;
}

function reqPermission(one_per, callback) { //请求权限
    var perms = new Array();
    perms.push(one_per);

    api.requestPermission({ //向系统请求某个或多个权限
        list: perms,
        code: 100001
    }, function(ret, err) {
        if (callback) {
            callback(ret);
            return;
        }
        var str = '请求结果：\n';
        str += '请求码: ' + ret.code + '\n';
        str += "是否勾选\"不再询问\"按钮: " + (ret.never ? '是' : '否') + '\n';
        str += '请求结果: \n';
        var list = ret.list;
        for (var i in list) {
            str += list[i].name + '=' + list[i].granted + '\n';
        }
        //  alert(str);
        console.log(JSON.stringify(ret));
    });
}

// var dns = "http://sxr.ijiandian.com/api/v1/"
