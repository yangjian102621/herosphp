/**
 * put your common js code here
 * Created by yangjian on 16-5-17.
 */
//全局JS对象
var __global = {

	//JDialog 的配置参数
	jdialog: {
		timer: 2000,   //提示框的显示时间
		winHeight: ($(window).height() - 120),    //弹出窗口的高度
		winBorderWidth: 8,    //弹出窗口的边框宽度
		winWidth: 90,         //弹出窗宽度
		widthType: 'percent',   //设置弹出窗口为父窗口百分比
		winMaxWidth: 1024,
		winSkin: 'default'    //弹出窗口的皮肤
	},

	//列表内容的配置文档
	contentList: {
		formId: 'J_List_Form',   //列表表单ID
		checkboxName: 'ids[]',   //checkbox 的name属性，用来过滤全选的checkbox
		tableId: 'J_ListTable'         //数据类表ID
	}
};
$(document).ready(function() {

	"use strict";

	//绑定代码的shi
	$(".table-hover tr").on("click", function() {
		$(this).find(".minimal-blue input").iCheck('toggle');
	});

	//初始化list table collspan
	$(".J_List_Table .no-records").each(function() {

		var th = $(this).parent().parent().prev().children(":first").children();
		$(this).attr('colspan', th.length);

	});

	//初始化AjaxProxy插件
	$('.ajaxproxy').AjaxProxy({
		dataType : "json",
		formCheckHandler : function(form_id) {

			var __form = new JForm({
				formId : form_id,
				continueCheck : true,
				showMessage : function( type, message, ele ) { //错误处理接口
					ele.focus();

					if ( type == "error" ) {
						$(ele).addClass("error");
						$(ele).next().addClass("error").html(message);
					} else {
						$(ele).removeClass("error");
						$(ele).next().removeClass("error").html("");
					}
				}
			});

			//设置报错提示信心
			__form.setMessage({
				empty : "请填写{cname}.",
				email : "请输入合法的电子邮件地址.",
				idnum : "请输入合法的身份证号码.",
				number : "请输入有效的数字."
			});

			return __form.checkFormData();

		},

		callBack : function (data) {   /* 执行ajax之后的回调函数 */
			console.log(data);
			if ( data.code == "0" ) {
				console.log(data.message);
				JDialog.tip.work({type:"ok", content:data.message, lock:true, timer:3000});
			} else {
				JDialog.tip.work({type:"error", content:data.message, timer:3000});
			}

		}
	});
});
