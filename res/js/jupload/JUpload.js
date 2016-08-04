/**
 * 基于iframe实现的异步上传插件
 * https://git.oschina.net/blackfox/ajaxUpload
 * @author yangjian<yangjian102621@gmail.com>
 * @version 1.0.0
 * @since 2016.06.02
 */
(function($) {

	if ( Array.prototype.remove == undefined ) {
		Array.prototype.remove = function(item) {
			for ( var i = 0; i < this.length; i++ ) {
				if ( this[i] == item ) {
					this.splice(i, 1);
					break;
				}
			}
		}
	}
	//图片裁剪
	if ( !$.fn.imageCrop ) {
		$.fn.imageCrop = function(__width, __height) {
			$(this).on("load", function () {

				var width, height, left, top;
				var orgRate = this.width/this.height;
				var cropRate = __width/__height;
				if ( orgRate >= cropRate ) {
					height = __height;
					width = __width * orgRate;
					top = 0;
					left = (width - __width)/2;
				} else {
					width = __width;
					height = __height / orgRate;
					left = 0;
					//top = (height - __height)/2;
					top = 0;
				}
				$(this).css({
					"position" : "absolute",
					top : -top + "px",
					left : -left + "px",
					width : width + "px",
					height : height + "px"
				});
			});
		}
	}
	//单个上传文件
	$.fn.JUpload = function(__options) {
		var options = $.extend({
			src : "src",
			url : null,
			callback : null,
			image_container : null,
			twidth : 113,
			theight : 113
		}, __options);
		var __self = this;
		var images = []; //已经上传的图片列表
		var frameName = "iframe_"+Math.random();
		var $form = $('<form action="'+options.url+'" target="'+frameName+'" enctype="multipart/form-data" method="post"></form>');
		var $input = $('<input type="file" name="'+options.src+'" class="upload-input" />');
		var $iframe = $('<iframe name="'+frameName+'" class="upload-iframe"></iframe>');
		//给按钮绑定点击事件
		$(this).on("click", function() {
			$input.trigger("click");
		});
		//绑定上传事件
		$input.on("change", function() {
			$form[0].submit();
		});
		$iframe.on("load", function() {
			var html = this.contentWindow.document.getElementsByTagName("body")[0].innerHTML;
			if ( !html ) return false;
			try {
				var data = $.parseJSON(html);
				if ( typeof options.callback == "function" ) {
					options.callback(data);
				} else if ( options.image_container != null ) {
					addImage(data);
				}
			} catch (e) {
				console.log(e);
			}
		});
		$form.append($input);
		$('body').append($form);
		$('body').append($iframe);
		if ( options.image_container ) {
			$("#"+options.image_container).addClass("clearfix");
		}

		//添加图片
		function addImage(data) {
			if ( data.code != "0" ) return;
			var builder = new StringBuilder();
			builder.append('<div class="img-wrapper"><div class="img-container" style="width: '+options.twidth+'px; height: '+options.theight+'px">');
			builder.append('<img src="'+data.message+'">');
			builder.append('<div class="file-opt-box clearfix"><span class="remove">删除</span></div></div></div>');
			var $image = $(builder.toString());
			$("#"+options.image_container).append($image);
			$image.find("img").imageCrop(options.twidth, options.theight);
			//$image.hover(function() {     //这里的hover效果已经通过css实现了
			//	$(this).find(".file-opt-box").show();
			//}, function() {
			//	$(this).find(".file-opt-box").hide();
			//});
			//删除图片
			$image.find(".remove").on("click", function() {
				try {
					var src = $(this).parent().prev().attr("src");
					images.remove(src);
					$image.remove();
					$(__self).attr("data-src", images.join(","));
				} catch (e) {console.log(e);}
			});

			images.push(data.message);
			$(__self).attr("data-src", images.join(","));
		}

	}

	//string builder
	var StringBuilder = function() {
		var buffer = new Array();
		StringBuilder.prototype.append = function(str) {
			buffer.push(str);
		}
		StringBuilder.prototype.toString = function () {
			return buffer.join("");
		}

	}

})(jQuery);