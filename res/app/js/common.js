/**
 * put your common js code here
 * Created by yangjian on 16-5-17.
 */
$(function() {

	"use strict";

	//绑定代码的shi
	$(".table-hover tr").on("click", function() {
		$(this).find(".minimal-blue input").iCheck('toggle');
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
					//JDialog.tip.work({type:type, content:message, timer:3000});

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
				JDialog.tip.work({type:"ok", content:data.message, timer:3000});
			} else {
				JDialog.tip.work({type:"error", content:data.message, timer:3000});
			}

		}
	});
});
