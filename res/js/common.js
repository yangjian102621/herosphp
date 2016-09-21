/**
 * put your common js code here
 */
//全局JS对象
var __global = {

	//JDialog 的配置参数
	jdialog: {
		timer: 2000,   //提示框的显示时间
		dialog_width: 80, //对话窗口的高度
		dialog_height : 90, //对话框高度
		max_width: 1280 //对话框的最大宽度
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

	//绑定tr和checkbox的选中事件
	$(".table-hover tr").on("click", function(e) {
		$(this).find(".minimal-blue input").iCheck('toggle');
		e.stopPropagation(); //阻止冒泡
	});

	//初始化list table collspan
	$(".J_List_Table .no-records").each(function() {

		var th = $(this).parent().parent().prev().children(":first").children();
		$(this).attr('colspan', th.length);

	});

	//初始化AjaxProxy插件
	$('.ajaxproxy').AjaxProxy({
		dataType : "json",
		method : "get",
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
		callbackDelay : __global.jdialog.timer,
		timeInterval : __global.jdialog.timer,
		callBack : function (data) {   /* 执行ajax之后的回调函数 */
			console.log(data);
			if ( data.code == "0" ) {
				JDialog.tip.work({type:"ok", content:data.message, lock:true, timer:__global.jdialog.timer});
			} else {
				JDialog.tip.work({type:"error", content:data.message, timer:__global.jdialog.timer});
			}

		}
	});

	//删除确认
	$(".item-remove").on("click", function(e) {
		e.stopPropagation();
		var url = $(this).attr("href");
		var __self = this;
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
						if ( data.code == "0" ) {
							$(__self).parents("tr").remove();
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
});
