/**
 * Ajax 代理调用
 * 数据模型  <a href="admin/menu/add" class="ajaxproxy"
 * proxy='{"method":"get", "timer":"2000", "formid":"testForm",
 * "callBefore":"function() {test(data);}", "callBack":"test(data);"}'>提交表单</a>
 * 注意：回调函数的参数只能是 data 否则会出错
 * @author      yangjian102621@gmail.com
 * @version     1.0.0
 * @since       2014-06-20
 * @copyright   FiiDee All Rights Reserved
 */
var AjaxProxy = {

    /* 默认参数设置 */
    options: {
        className: 'ajaxproxy',         /* 使用代理的dom class */
        callbackDelay: 1000,            /* 回调延时 */
        threadLock: true,               /* 是否开启线程锁定，防止段时间内多次点击 */
        timeInterval: 1000,             /* 两次操作的时间间隔， 如果没有开启线程锁定，则此项无效*/
        errorBox: null,                 /* 错误处理显示的dom id, 默认使用的是弹出tip的形式，则此参数默认无效 */
        method: 'get',                  /* 默认数据传输方式 */
        dataType: 'html',               /* 默认返回的数据格式 */
        formId: 'caddForm',             /* 表单的默认ID */
        validate : true,
        callBefore: function () {        /* 在执行ajax操作之前的调用函数 */
            //do nothing here
			return true;
        },

        callBack: function (data) {   /* 执行ajax之后的回调函数 */
            JDialog.tip.work({type:data.state, content:data.message, timer:AjaxProxy.options.callbackDelay, callback : function() {
                if ( AjaxProxy.options.location ) {
                    location.replace(AjaxProxy.options.location);
                }
            }});

            //document.getElementById(AjaxProxy.options.formId).reset();
        }
    },

    /* 初始化 */
    init: function (options) {
        /* 合并参数 */
        this.options = $.extend(this.options, options);

        var __self = this;
        /* 绑定事件 */
        $('.' + this.options.className).click(function () {
            var params = __self.getParams(this);
            /* 再次合并参数 */
            __self.options = $.extend(__self.options, params);
			 /* 请求发送之前的调用函数 */
            if ( !__self.options.callBefore() ) return false;

            if (__self.options.method == 'get') {
                __self.ajaxGet(__self.options, this);
            } else {
                __self.ajaxPost(__self.options, this);
            }

            return false;
        });

    },

    /**
     * 根据元素的属性获取参数
     * @param       obj     当前点击的元素
     */
    getParams: function (obj) {
        var paramStr = $(obj).attr('proxy');    /* 获取代理属性 */
        var href = $(obj).attr('href');         /* 获取href属性 */
        var params = {};
        if (paramStr) {
            params = $.parseJSON(paramStr);     /* 解析json数据格式 */
            //创建回调函数
            if ($.type(params.callBefore) == 'string') params.callBefore = new Function("data", 'return '+params.callBefore);
            if ($.type(params.callBack) == 'string') params.callBack = new Function("data", params.callBack);
        }
        params.url = href;
        return params;
    },

    /**
     * 以get方式发送请求并处理返回参数
     * @param       params   请求参数
     * @param       obj     当前被点击的对象
     */
    ajaxGet: function (params, obj) {

        var __self = this;
        if ( this.options.threadLock ) {       /* 锁定线程，防止多次点击 */
            try {
                $(obj).button('loading');
            } catch(e) {}
        }

        $.get(params.url, {run: Math.random()}, function (result) {

            switch ( __self.options.dataType ) {
                case 'html':
                    if (__self.options.errorBox) {
                        $('#' + __self.options.errorBox).html(result);
                    } else {
                        __self.options.callBack(result);
                    }
                    break;

                case 'json':
                    var data = $.parseJSON(result);
                    __self.options.callBack(data);
                    break;

                default :
                    alert('不支持的数据格式');
            }

            if (__self.options.threadLock) {       /* 解除锁定 */
                setTimeout(function () {
                    try {
                        $(obj).button('reset');
                    } catch(e) {}
                }, __self.options.timeInterval);
            }
        });
    },

    /**
     * 以post方式发送请求,通常要处理表单
     * @param       params   请求参数
     * @param       obj     当前被点击的对象
     */
    ajaxPost: function (params, obj) {

        var __self = this;
        /* 验证数据 */
        var __options = {
            formId : this.options.formId
        }
        if ( this.options.validate ) {
            var __form = new JForm(__options);      //新建表单工具
            if ( !__form.checkFormData() )  return false;
        }

        if ( this.options.threadLock ) {       /* 锁定线程，防止多次点击 */
            try {
                $(obj).button('loading');
            } catch (e) {}
        }

        /* 收集数据 */
        var formData = $('#' + __self.options.formId).serialize();
        $.post(params.url, formData, function (result) {

            switch ( __self.options.dataType ) {
                case 'html':
                    if (__self.options.errorBox) {
                        $('#' + __self.options.errorBox).html(result);
                    } else {
                        __self.options.callBack(result);
                    }
                    break;

                case 'json':
                    var data = $.parseJSON(result);
                    __self.options.callBack(data);
                    break;

                default :
                    alert('不支持的数据格式');
            }
			if (__self.options.threadLock) {       /* 解除锁定 */
				setTimeout(function () {
                    try {
                        $(obj).button('reset');
                    } catch (e) {}
                }, __self.options.timeInterval);
			}
        });
    }
};