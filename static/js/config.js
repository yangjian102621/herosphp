;(function () {
	/*IE8下未开启调试，console出现错误提示兼容*/
	window.console = window.console || {};
	window.console.log = window.console.log || function () {
		};
	var getCookie = function (name) {
		var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
		if (arr != null) {
			return unescape(arr[2]);
		}
		return null;
	};
	/* Local variable */
	var host = "//" + location.host;
	var debug = /*getCookie("debugFlag") ? true : false*/true;

	var config = {
		//模块系统的基础路径
		base: '/res/app/static/',
		vars: {
			//"path":"/static/finance/js/",
			"plugins": "js/plugins",
			"app": "js/app",
		},
		//别名
		alias: {
			"bootstrap": "{plugins}/bootstrap/bootstrap.min.js",
			"icheck": "{plugins}/icheck/icheck.min.js",
			"jdialog": "{plugins}/jdialog/JDialog.min.js",
			"select2": "{plugins}/select2/select2.min.js",
			"switch": "{plugins}/switch/bootstrap-switch.min.js",
			"ajaxproxy": "{plugins}/AjaxProxy.min.js",
			"jtemplate": "{plugins}/JTemplate.min.js",
			"jform": "{plugins}/JForm.min.js",
			"datetimepicker": "{plugins}/datetimepicker/js/bootstrap-datetimepicker.min.js",
			"editor": "{plugins}/kindeditor/kindeditor.js",
			"jupload": "{plugins}/jupload/JUpload.min.js",

			"login": "{app}/login.js",
			"common": "{app}/common.js",
			"user": "{app}/user.js",

		},
		preload: [
			"bootstrap"
		],

		//错误信息查看
		debug: 1,

		//文件映射
		map: [
			//可配置版本号
			//['.css', '.css?v=' + version],
			//['.js', '.js?v=' + version]
		],

		// 文件编码
		charset: function (url) {
			if (url.indexOf(".gbk.") > -1) {
				return "GBK";
			}
			return "UTF-8";
		}
	};
	var alias = config.alias;
	var v = typeof(rev) == "undefined" ? '' : '?v=' + rev;
	var suffix = (debug ? ".js" : ".min.js");
	for (var key in alias) {
		if (alias[key].indexOf(".min") == -1 && alias[key].indexOf("WdatePicker") == -1 && alias[key].indexOf(".js") != -1) {
			alias[key] = alias[key].replace(/\.js/, suffix) + v;
		}
	}
	seajs.config(config);
})();
