/**
 * put your common js code here
 */
"use strict";
jQuery(window).load(function() {

	// Page Preloader
	jQuery('#preloader').delay(100).fadeOut(function(){
		jQuery('body').delay(100).css({'overflow':'visible'});
	});
});

//全局JS对象
var __global = {

	//JDialog 的配置参数
	jdialog: {
		timer: 2000,   //提示框的显示时间
		width: 60, //对话窗口的高度，小于100表示百分比
		height : 80, //对话框高度，小于100表示百分比
		borderWidth : 8, //边框厚度
		position : 70, //对话框距离页面顶部的距离
		lock : true, //是否锁屏
		effect : 1 //切换过度特效， 0， 1， 2
	},

	jformErrorMessage : {
		empty : "请填写{cname}.",
		email : "请输入合法的电子邮件地址.",
		idnum : "请输入合法的身份证号码.",
		number : "请输入有效的数字."
	},

	//列表内容的配置文档
	contentList: {
		formId: 'J_List_Form',   //列表表单ID
		checkboxName: 'ids[]',   //checkbox 的name属性，用来过滤全选的checkbox
		tableId: 'J_ListTable'         //数据类表ID
	},

	resizeLayout : function() {
		$('#right-section').height($(window).height() - 95);
	}

};

define(function(require, exports) {

	require("icheck");
	require("jdialog");
	require("select2");
	require("switch");
	require("jtemplate");
	require("jform");

	exports.init = function() {

		__global.resizeLayout();
		$(window).resize(function() {__global.resizeLayout();});

		//菜单收开事件
		$(".nav-parent > a").on('click', function(e) {

			if ( $(this).next().is(":visible") ) return;

			$(".nav-parent").find(".children").slideUp("fast");
			$(this).next().slideDown("fast");
			$(".nav-parent").find(".glyphicon-minus").addClass("glyphicon-plus").removeClass('glyphicon-minus');
			$(this).find(".pull-right").addClass("glyphicon-minus").removeClass('glyphicon-plus');
			e.stopPropagation();
		});

		//初始化菜单选择项
		$(".nav-parent").each(function(idx, ele) {

			$(ele).find(".children a").each(function(idx1, item) {
				var url = $(item).attr("href");
				if ( url == location.pathname ) {
					$(ele).find(".children").show();
					return false;
				}
			});

		});

		//绑定tr和checkbox的选中事件
		$(".table-hover tr").on("click", function(e) {
			$(this).find(".icheck").iCheck('toggle');
			e.stopPropagation(); //阻止冒泡
		});

		this.initElements();

		//删除确认
		$(".item-remove").on("click", function(e) {
			e.stopPropagation();
			var url = $(this).attr("href");
			var __confirm = JDialog.win.work({
				title : "对话框标题",
				width : 340,
				height : 180,
				borderWidth : 8,
				lock : true,
				effect : 0,
				content : '<div style="padding-top: 15px;">该操作会删除选中记录，继续操作吗？</div>',
				icon : 'warn',
				button : {
					'确认' : function() {
						$.get(url, function(data) {
							__confirm.close();
							if ( data.code == "000" ) {
								JDialog.tip.work({type:"ok", content:data.message, timer:1000});
								setTimeout(function() {location.reload();}, 1000)
							} else {
								JDialog.tip.work({type:"error", content:data.message, timer:__global.jdialog.timer});
							}
						},"json");
					},
					'取消' : function() {
						__confirm.close();
					}
				}
			});
			return false;
		});

	}

	exports.initElements = function() {
		//初始化list table collspan
		$(".J_List_Table .no-records").each(function() {

			var th = $(this).parent().parent().prev().children(":first").children();
			$(this).attr('colspan', th.length);

		});

		//switch
		$(".bswitch").bootstrapSwitch({
			onColor : 'success',
			offColor : 'danger'
		});

		//icheck初始化
		$(".icheck").iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
			radioClass: 'iradio_minimal-blue',
			increaseArea: '20%' // optional
		});
		//icheck全选事件
		$(".check-all").on("ifChecked", function() {
			$(this).parents("table").find('[name="'+__global.contentList.checkboxName+'"]').iCheck('check');
		}).on("ifUnchecked", function() {
			$(this).parents("table").find('[name="'+__global.contentList.checkboxName+'"]').iCheck('uncheck');
		});

		//select2
		$('.select2').select2({});
	}

	//通用表单验证处理
	exports.formCheckHandler = function(form_id, _lock) {
		var __form = new JForm({
			formId : form_id,
			continueCheck : false,
			showMessage : function( type, message, ele ) { //错误处理接口
				ele.focus();
				var errorElements = $(ele).parents(".form-group").next();
				if ( type == "error" && ele.type == 'text' ) {
					errorElements.addClass("text-error").html(message);
				} else {
					errorElements.removeClass("text-error").empty();
				}
			}
		});

		//设置报错提示信心
		__form.setMessage(__global.jformErrorMessage);

		return __form.checkFormData();
	}

	/**
	 * 通用回调处理
	 * @param data 服务器返回数据，对象
	 * @param successCallback 操作成功时回调函数
	 * @param failCallback 操作失败时回调函数
	 */
	exports.ajaxCallback = function(data, successCallback, failCallback) {
		if ( data.code == "000" ) {
			JDialog.tip.work({type:"ok", content:data.message, lock:true, timer:__global.jdialog.timer});
			if ( typeof successCallback == 'function' ) {
				successCallback();
			}
		} else {
			JDialog.tip.work({type:"error", content:data.message, timer:__global.jdialog.timer});
			if ( typeof failCallback == 'function' ) {
				failCallback();
			}
		}
	}

	/**
	 * 表单提交通用处理函数
	 * @param formId
	 * @param url
	 * @param successCallback
	 * @param failCallback
	 * @returns {boolean}
	 */
	exports.formSubmit = function(formId, url, successCallback, failCallback) {

		if ( !exports.formCheckHandler(formId) ) return false;
		//collect form data
		var formData = $('#' + formId).serialize();
		$.post(url, formData, function (result) {
			exports.ajaxCallback(result, successCallback, failCallback);
		}, 'json');
	}

});
