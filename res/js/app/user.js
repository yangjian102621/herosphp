/**
 * put your common js code here
 */
define(function(require, exports) {
	//加载依赖
	require("ajaxproxy");
	var common = require("common");

	//初始化AjaxProxy插件
	$('.ajaxproxy').AjaxProxy({
		dataType : "json",
		method : "post",
		formId : "content-add-form",
		formCheckHandler : function(form_id) {
			return common.formCheckHandler(form_id, true);
		},
		callbackDelay : __global.jdialog.timer,
		timeInterval : __global.jdialog.timer,
		callBack : function (data) {   /* 执行ajax之后的回调函数 */
			common.ajaxCallback(data);
		}
	});

	//绑定弹出框
	$('#popwin').on('click', function() {

		var templateId = $(this).data("template");
		var options = {
			title : "对话框标题",
			content : document.getElementById(templateId).innerHTML,
			icon : 'none',
			height : 600,
			button : {
				'保存' : function() {

					var formId = 'content-add-form';
					if ( !common.formCheckHandler(formId, false) ) return false;
					//collect form data
					var formData = $('#' + formId).serialize();
					popWin.lock();
					$.post(url, formData, function (result) {
						exports.ajaxCallback(result, function() {
							popWin.unlock();
						});
					}, 'json');

				},
				'取消' : function() {
					popWin.close();
				}
			}
		}
		var options = $.extend(__global.jdialog, options);
		var popWin = JDialog.win.work(options);

		common.initElements(); //初始化元素组件

	});

	//初始化编辑器控件
	require("editor");
	KindEditor.ready(function(K) {
		K.create('.editor', {
			uploadJson : '../php/upload_json.php',
			fileManagerJson : '../php/file_manager_json.php',
			allowFileManager : true,
			imageUploadLimit : 20,
			imageSizeLimit : '1MB',
			afterBlur: function(){this.sync();}
		});

	});

	//初始化上传控件
	require('jupload');
	$("#upload").JUpload({
		url : "/test/upload/upload",
		src : "src",
		datas : ["/res/upload/2016/091474885586-430720.jpg","/res/upload/2016/09/1474885661-791066.jpg"],
		image_container : "image-box"
	});

});
