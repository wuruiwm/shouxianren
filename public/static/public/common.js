window.base = {
    g_restUrl: 'http://app.jinshouzhou.com/adminapi/',
    getData: function (params) {
        if (!params.type) {
            params.type = 'get';
        }
        var that = this;
        $.ajax({
            type: params.type,
            url: this.g_restUrl + params.url,
            data: params.data,
            beforeSend: function (XMLHttpRequest) {
                if (params.tokenFlag) {
                    XMLHttpRequest.setRequestHeader('token', that.getLocalStorage('token'));
                }
            },
            success: function (res) {
                params.sCallback && params.sCallback(res);
            },
            error: function (res) {

                params.eCallback && params.eCallback(res);
            }
        });
    },

    setLocalStorage: function (key, val) {
        var exp = new Date().getTime() + 1000 * 60 * 60 * 2;
        var obj = {
            val: val,
            exp: exp
        };
        localStorage.setItem(key, JSON.stringify(obj));
    },

    getLocalStorage: function (key) {
        var info = localStorage.getItem(key);
        if (info) {
            info = JSON.parse(info);
            if (info.exp > new Date().getTime()) {
                return info.val;
            }
            else {
                this.deleteLocalStorage('token');
            }
        }
        return '';
    },

    deleteLocalStorage: function (key) {
        return localStorage.removeItem(key);
    },

    deleteAllLocalStorage: function () {
        return localStorage.clear();
    },

    getQueryString: function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)return unescape(r[2]);
        return null;
    },
}
