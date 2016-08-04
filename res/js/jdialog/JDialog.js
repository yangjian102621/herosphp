/**
 * JDialog是一个简单易用但是功能强大的开源JS弹出窗口，具有很强的扩展性和兼容性，兼容IE6.0，目前版本1.2.
 * 包括锁屏对象JDialog.lock， 提示工具 JDialog.tip， 确认框 JDialog.confirm  弹出窗口 JDialog.win 比artDialog功能更强大，使用更方便。
 * url : https://git.oschina.net/blackfox/JDialog
 * @dependence jquery
 * @author  yangjian102621@gmail.com
 * @version 2.0.0
 */

(function($) {

	//fly into effect
	$.fn.flyIn = function(transitionTime, callback) {

		var top = $(this).css("top");

		$(this).css({
			top : (-$(this).height()- 10) + "px",
			display : "block"
		}).animate({top : top}, transitionTime, callback);
	}

	//fly out effect
	$.fn.flyOut = function(transitionTime, callback) {

		$(this).animate({top : (-$(this).height()- 10)}, transitionTime, function() {
			$(this).hide();
			try {callback();} catch (e) {}
		});
	}

	//zoom in effect
	$.fn.zoomIn = function(transitionTime, callback) {

		var top = $(this).css("top");
		var left = $(this).css("left");
		var width = $(this).width();
		var height = $(this).height();

		$(this).css({ //show
			top : parseInt(top) + height/2 + "px",
			left : parseInt(left) + width/2 + "px",
			display : "block",
			width   : 0,
			height  : 0
		}).animate({
			top : top,
			left : left,
			width : width,
			height : height
		}, transitionTime, callback);
	}

	//zoom out effect
	$.fn.zoomOut = function(transitionTime, callback) {

		var top = $(this).css("top");
		var left = $(this).css("left");
		var width = $(this).width();
		var height = $(this).height();

		$(this).animate({
			top : parseInt(top) + height/2,
			left : parseInt(left) + width/2,
			width   : 0,
			height  : 0
		}, transitionTime, function() {
			$(this).hide();
			try {callback();} catch (e) {}
		});
	}

	//make element draggable
	$.fn.draggable = function(options) {

		var defaults = {
			handler : null
		}
		options = $.extend(defaults, options);
		var __self = this;
		$(options.handler).mousedown(function(e) {
			var offsetLeft = e.pageX - $(__self).position().left;
			var offsetTop = e.pageY - $(__self).position().top;
			$(document).mousemove(function(e) {
				//清除拖动鼠标的时候选择文本
				window.getSelection ? window.getSelection().removeAllRanges():document.selection.empty();
				$(__self).css({
					'top'  : e.pageY-offsetTop + 'px',
					'left' : e.pageX-offsetLeft + 'px'
				});
			});

		}).mouseup(function() {
			$(document).unbind('mousemove');
		});

	}

})(jQuery);

window.JDialog = {
	getDocWidth : function() {
		return Math.max(document.documentElement.clientWidth,document.body.clientWidth, $(document).width());
	},
	getDocHeight : function() {
		return Math.max(document.documentElement.clientHeight, document.body.clientHeight, $(document).height()) + document.documentElement.scrollTop;
	},
	getWindowHeight : function() {
		return $(window).height();
	},
	getWindowWidth : function() {
		return $(window).width();
	},

	transitionTime : 300,  //显示|隐藏过渡时间
};

JDialog.lock = {
	lockBak : null,
	options : null,

	//create elements
	create : function() {
		if ( this.lockBak != null ) return;
		var lockPanel = document.createElement('DIV');
		lockPanel.id = 'lock_panel';
		this.lockBak = lockPanel;
		document.body.appendChild(lockPanel);
	},

	/*
	 * set the size of lock panel
	 * 设置锁屏层的大小，确保锁定整个页面
	 */
	setSize : function() {
		if ( this.lockBak == null ) return;
		$(this.lockBak).css({
			'opacity' : this.options.opacity,
			'width' : JDialog.getDocWidth(),
			'height' : JDialog.getDocHeight()
		});
	},

	//显示锁屏
	show : function() {
		if ( this.lockBak == null ) return;
		$(this.lockBak).fadeIn(JDialog.transitionTime);
	},

	//隐藏锁屏
	hide : function() {
		if ( this.lockBak == null ) return;
		$(this.lockBak).fadeOut(JDialog.transitionTime);
	},

	//销毁窗口
	remove : function() {
		if ( this.lockBak != null ) {
			$(this.lockBak).remove();
			this.lockBak = null;
		}
	},

	//调用接口
	work : function( options ) {

		//合并参数
		this.options = $.extend({
			opacity : 0.5,
			timer : 0
		}, options);

		this.create();
		this.setSize();
		this.show();

		//绑定调整窗口大小事件
		var self = this;
		$(window).bind('resize', function() {
			self.setSize();
		});

		if ( this.options.timer > 0 ) {
			setTimeout(function() {
				JDialog.lock.fadeOut(JDialog.transitionTime);
			}, this.options.timer);
		}

	}
};

JDialog.tip = {
	tipBak : null,
	//content
	options : null,

	handler : null,

	create : function() {
		if ( this.tipBak != null ) {
			this.remove();
		}
		var T_BOX = $('<div class="jtip_box"></div>');
		var T_ICON = $('<div class="jtip_left_icon" id="jdialog_tip_icon"><!--提示图标--></div>');
		var T_CONTENT = $('<div class="jtip_content" id="jdialog_tip_content"></div>');
		var T_END = $('<div class="jtip_right" id="jtip_right"><!--右边圆角--></div>');

		T_BOX.append(T_ICON);
		T_BOX.append(T_CONTENT);
		T_BOX.append(T_END);
		$('body').append(T_BOX);
		this.tipBak = T_BOX;
	},

	//设置内容
	setContent : function() {

		if ( this.tipBak == null ) return;
		$('#jdialog_tip_content').html(this.options.content);

	},

	//元素居中
	center : function() {

		if ( this.tipBak == null ) return;
		var _scrollTop = window.document.body.scrollTop || window.document.documentElement.scrollTop;
		var _width = 0;
		if ( this.options.width ) {
			_width = this.options.width;
		} else {
			var b_width = $('#jdialog_tip_icon').width() + $('#jtip_loading').width() + 21 + $('#jtip_right').width();
			_width = b_width + $('#jdialog_tip_content').width();

		}
		var top = (JDialog.getWindowHeight() - $(this.tipBak).height())/2 + _scrollTop;
		if ( this.options.top != undefined ) {
			top = this.options.top + _scrollTop;
		}
		$(this.tipBak).css({
			'width' : _width +'px',
			'top'   : top + 'px',
			'left'  : (JDialog.getWindowWidth() - _width)/2 + 'px'
		});
	},

	//显示
	show : function () {
		if ( this.tipBak == null ) return;
		//确认消息类型
		switch ( this.options.type ) {
			case 'warn':
				$('#jdialog_tip_icon').addClass('jtip_warn');
				break;
			case 'ok':
				$('#jdialog_tip_icon').addClass('jtip_ok');
				break;
			case 'error':
				$('#jdialog_tip_icon').addClass('jtip_error');
				break;
			case 'loading':
				var _loading = $('<div id="jtip_loading" class="jtip_load_img"><em id="jtip_loading_icon"></em></div>');
				var _tip_icon = $('#jdialog_tip_icon');
				_tip_icon.addClass('jtip_loading');
				_loading.insertAfter(_tip_icon);
				break;
			default:
				$('#jdialog_tip_icon').addClass('jtip_warn');
				break;

		}

		$(this.tipBak).fadeIn();
	},

	//删除提示框
	remove : function() {
		if ( this.tipBak != null ) {

			try {
				clearTimeout(this.handler);
			} catch (e) {}

			this.tipBak.remove();
			this.tipBak = null;
		}

		if ( this.options.lock ) {
			try {JDialog.lock.hide();} catch (e) {}
		}
	},

	//外部调用接口
	work : function( options ) {

		//合并参数
		this.options = $.extend({
			type : 'warn', //消息类别
			content : 'Hello, World.', //消息内容
			lock : false, //是否锁屏
			timer : 1500 //显示时间
		}, options);

		this.create();
		this.setContent();
		this.show();
		this.center();

		//绑定调整窗口大小事件
		var self = this;
		$(window).bind('resize', function() {
			self.center();
		});

		if ( this.options.lock ) {
			try {JDialog.lock.work();} catch (e) {}
		}

		//自动隐藏
		var that = this;
		if ( this.options.timer > 0 ) {
			this.handler = setTimeout(function() {
				that.remove();
				if ( that.options.callback ) {
					that.options.callback();
				}
			}, this.options.timer);
		}
	}
};

JDialog.win = {

	work : function( options ) {
		return new __JWindow__(options);
	}
};

//窗口对象
var __JWindow__ = function( __options ) {
	__options = __options || {};
	var defaults = {
		title : 'This is the title',
		content : 'Hello, this is the content.',
		width : 600,
		height : 0, //窗口的高度：如果为0则为自适应
		borderWidth : 0, //边框的宽度，如果为0则表示不要边框
		borderOpacity : 0.4, //边框的透明度
		borderColor : '#000000',    //边框颜色
		hasTitle : true,    //是否出需要标题栏
		lock : true,   //弹出窗口的时候是否锁屏
		skin : 'default', //皮肤
		position : 'center', //初始化时窗口的位置，默认居中，也可以指定left,top
		effect : 0,
		maxEnable : false, //是否允许窗口最大化
		icon : "none", //需要显示的图标，如果为none则表示不显示(默认)
		maxWidth : 1920
	};
	//合并并补充参数
	var options = $.extend(defaults, __options);
	var o = {};
	o.winBox = null; //窗体
	o.winBg = null; //背景

	if ( options.width < 100 ) { //高度设置成百分比
		options.width = options.width * $(window).width()/100;
	}
	if ( options.height < 100 ) { //高度设置成百分比
		options.height = options.height * $(window).height()/100;
	}

	/* create elements */
	o.create = function() {

		o.winBox = $('<div class="jdialog_win_box jdialog_win_'+options.skin+'"></div>');
		if ( options.borderWidth > 0 ) {
			o.winBg = $('<div class="jdialog_win_box_bg"></div>');
		} else {
			o.winBox.addClass("box-shadow"); //添加阴影和圆角
		}

		//line 1, create title of window
		var title_box = $('<div class="jdialog_win_title_box"></div>');
		var win_title = $('<div class="jdialog_win_title">'+options.title+'</div>');

		var win_button = $('<div class="jdialog_win_button"></div>');
		var max_button = $('<a class="jdialog_win_max_button" href="javascript:void(0)" title="最大化"></a>');
		var close_button = $('<a class="jdialog_win_close_button" href="javascript:void(0)" title="关闭"></a>');

		//bind events for title button
		max_button.on("click", function(e) {o.resizeToMax(); e.stopPropagation();}); //最大化窗口
		close_button.on("click", function() {o.close();}); //关闭窗口

		if ( options.maxEnable ) {
			win_button.append(max_button);
		}
		win_button.append(close_button);
		title_box.append(win_title);
		title_box.append(win_button);
		o.winBox.append(title_box);

		//line 2, creat icon and content container
		var win_content = $('<div class="jdialog_win_CBOX"></div>');
		//add icon
		var icon = $('<span class="jdialog_win_icon"></span>');
		var content = $('<div class="jdialog_win_content">'+options.content+'</div>');
		if ( options.icon != "none" ) {
			win_content.append(icon);
		}
		win_content.append(content);
		o.winBox.append(win_content);

		//line 3, create button
		var btnBox = $('<div class="jdialog_win_button_container"></div>');
		var btn = $('<div class="jdialog_win_buttonInner"></div>');

		//create button
		if ( options.button ) {
			var JButton = function( name, callback ) {
				var btn = $('<a href="javascript:void(0);" class="btn btn-primary">'+name+'</a>');
				btn.click(function() {
					callback();
				});
				return  btn;
			}
			//put all buttons to button box
			for ( var name in options.button ) {
				var __button =  new JButton(name, options.button[name])
				btn.append(__button);
			}
			btnBox.append(btn);
			o.winBox.append(btnBox);
		}

		o.winBox.data('me',{
			title : win_title,
			content : win_content,
			max_btn : max_button
		});

		//绑定拖动事件
		title_box.mousedown(function(e) {
			var offsetLeft = e.pageX - o.winBox.position().left;
			var offsetTop = e.pageY - o.winBox.position().top;
			$(document).mousemove(function(e) {
				//清除拖动鼠标的时候选择文本
				window.getSelection ? window.getSelection().removeAllRanges():document.selection.empty();
				o.winBox.css({
					'top'  : e.pageY-offsetTop + 'px',
					'left' : e.pageX-offsetLeft + 'px'
				});
				//移动背景层
				if ( o.winBg != null ) {
					o.winBg.css({
						'top'  : e.pageY-offsetTop - options.borderWidth + 'px',
						'left' : e.pageX-offsetLeft - options.borderWidth + 'px'
					});
				}
			});
		}).mouseup(function() {
			$(document).unbind('mousemove');
		});

		if ( o.winBg != null ) {
			$('body').append(o.winBg);
		}
		$('body').append(o.winBox);
		//set the icon
		o.setIcon( options.icon );
	}

	/* set the icon of window */
	o.setIcon = function( itype ) {

		if ( itype == "none" ) return;
		o.winBox.find('.jdialog_win_icon').addClass("jdialog_win_icon_"+itype);
	}

	/* set position of window */
	o.setPosition = function() {

		var left,top;
		var height = options.height > 0 ? options.height : o.winBox.height();
		if ( options.height == 0 && options.button != undefined ) {
			height += 60;   //加上按钮层的高度
			options.height = height;
		}
		if ( options.position == 'center' ) {
			var scroll_top = window.document.body.scrollTop || window.document.documentElement.scrollTop;
			top = (JDialog.getWindowHeight() - height)/2 + scroll_top;
			left = (JDialog.getWindowWidth() - options.width)/2;
		}
		else if ( typeof options.position == "number" ) {
			top = options.position;
			left = (JDialog.getWindowWidth() - options.width)/2;
		}
		else if ( Object.prototype.toString.apply(options.position) === "[object Object]" ) {
			top = options.position.top;
			left = options.position.left;
		}
		if ( top < 0 ) top = 0;
		if ( left < 0 ) left = 0;

		o.winBox.css({  //set the position of window
			'width' : options.width + 'px',
			'height' : height + 'px',
			'top'   : top - 1 +  'px',
			'left'  : left - 1 + 'px'
		});

		if ( o.winBg != null ) { //set position of window background
			o.winBg.css({
				'opacity' : options.borderOpacity,
				'background-color' : options.borderColor,
				'width'   : options.width + options.borderWidth * 2 + 'px',
				'height'  : height + options.borderWidth * 2 +'px',
				'top'     : top - options.borderWidth + 'px',
				'left'    : left - options.borderWidth + 'px'
			});
		}

		//调整内容宽度
		if ( options.icon == "none" ) {
			o.winBox.find(".jdialog_win_content").css({width:"100%"});
		} else {
			o.winBox.find(".jdialog_win_content").css({width: (o.winBox.width() - 20 - o.winBox.find(".jdialog_win_icon").width() - 15) + "px"});
		}

	}

	/* show the window */
	o.show = function() {
		if ( o.winBox == null ) return;

		if ( options.lock ) {
			try {JDialog.lock.work();} catch (e) {}
		}
		switch ( options.effect ) {

			case 0:
				o.winBox.fadeIn(JDialog.transitionTime);
				if ( o.winBg != null ) {
					o.winBg.fadeIn(JDialog.transitionTime);
				}
				break;

			case 1:
				o.winBox.flyIn(JDialog.transitionTime);
				if ( o.winBg != null ) {
					o.winBg.flyIn(JDialog.transitionTime);
				}
				break;

			case 2:
				o.winBox.zoomIn(JDialog.transitionTime);
				if ( o.winBg != null ) {
					o.winBg.zoomIn(JDialog.transitionTime);
				}
				break;
		}
	}

	/* clear the window's content */
	o.clear = function() {
		if ( o.winBox == null ) return;
		o.winBox.find('.jdialog_win_CBOX').empty();
		o.setPosition();
	}

	/* make window to max size */
	o.resizeToMax = function() {
		var data = o.winBox.data('smax');
		var max_btn = o.winBox.data('me').max_btn;
		if ( data == undefined ) {
			//记录最大化之前窗口的状态
			o.winBox.data('smax',{
				top   : o.winBox.position().top,
				left  : o.winBox.position().left,
				width : o.winBox.width() + 2 * parseInt(o.winBox.css("border-width")),
				height: o.winBox.height() + 2 * parseInt(o.winBox.css("border-width"))
			});

			o.winBox.animate({
				'top'    : 0,
				'left'   : 0,
				'width'  : $(window).width(),
				'height' : $(window).height(),
				'opacity': 1
			},'fast').css('z-index', 9999);
			//设置还原按钮属性
			max_btn.attr({
				'class' : 'jdialog_win_reduce_button',
				'title' : '还原'
			});

		} else {	//还原窗口

			o.winBox.animate({
				'top'    : data.top,
				'left'   : data.left,
				'width'  : data.width,
				'height' : data.height,
				'opacity': 1
			},'fast');
			//移除当前窗口状态
			o.winBox.removeData('smax');
			//设置最大化按钮属性
			max_btn.attr({
				'class' : 'jdialog_win_max_button',
				'title' : '最大化'
			});
		}

	}

	//关闭窗体
	o.close = function() {

		if ( o.winBox != null ) {

			switch ( options.effect ) {

				case 0:
					o.winBox.fadeOut(JDialog.transitionTime);
					if (o.winBg != null ) {
						o.winBg.fadeOut(JDialog.transitionTime);
					}
					break;

				case 1:
					o.winBox.flyOut(JDialog.transitionTime);
					if (o.winBg != null ) {
						o.winBg.flyOut(JDialog.transitionTime);
					}
					break;

				case 2:
					o.winBox.zoomOut(JDialog.transitionTime);
					if (o.winBg != null ) {
						o.winBg.zoomOut(JDialog.transitionTime);
					}
					break;
			}

			try {   //hide the lock panel
				JDialog.lock.hide();
			} catch (e) {}
		}

	}

	//绑定调整窗口大小事件
	$(window).bind('resize', function() {
		o.setPosition();
	});

	//初始化窗口
	o.create();
	o.setPosition();
	o.show();
	o.options = options;
	return o;
};
