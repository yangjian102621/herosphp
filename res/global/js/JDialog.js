/**
 * 弹出框工具包,此前端工具专门为HerosPHP所开发，并且和HerosPHP高性能轻量级PHP框架一起开源
 * 您可以放心将其用于您的项目中也可以用于商业用途。HerosPHP-1.2下载地址http://download.csdn.net/detail/yangjian8801/5618767
 * @import jquery.js 1.4 and the over       需要引进jquery框架1.4以上
 * @author  yangjian<yangjian102621@gmail.com> qq:906388445
 * @version 1.3
 * @date    2012.11.20
 * @updatetime 2013-06-20
 * 版权所有：网络星空工作室
 */
 
var JDialog = {
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
    }
};

/**
 * 锁屏层
 * @param    options    选项
 * @param    opacity    锁屏层的透明度
 * @param    site_url   锁屏iframe的框架src(修复IE下不能盖住flash和select标签)
 * @param    timer      锁屏的时间，不传该参数表示锁屏不自动隐藏，需要手动触发事件
 */
JDialog.lock = {
    lockBak : null,
    options : {
        opacity : 0.2,
        site_url : 'none'
    },
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
        $(this.lockBak).fadeIn(500);
    },
    
    //隐藏锁屏
    hide : function() {
        if ( this.lockBak == null ) return;
        $(this.lockBak).fadeOut(500);
    },
    
    //销毁窗口
    remove : function() {
        if ( this.lockBak != null ) {
            $(this).lockBak.remove();
            this.lockBak = null;
        }
    },
    
    //调用接口
    work : function( options ) {

        //合并参数
        this.options = $.extend(this.options, options);

        this.create();
        this.setSize();
        this.show();

        //绑定调整窗口大小事件
        var self = this;
        $(window).bind('resize', function() {
            self.setSize();
        });
        
        if ( typeof this.options.timer != 'number' ) return;
        var that = this;
        setTimeout(function() {
            that.hide();
        }, this.options.timer);
    }
};

/**
 * 提示框
 * @param    options.type           消息类别
 * @param    options.content        内容  
 * @param    options.timer          提示消息显示的时间   
 * @param    options.border         提示框的边框颜色，如果为"none"则表示不需要边框  
 */
JDialog.tip = {
    tipBak : null,
    //content
    options : null,

    create : function() {
        if ( this.tipBak != null ) return;
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
        $(this.tipBak).css({
            'width' : _width +'px',
            'top'   : (JDialog.getWindowHeight() - $(this.tipBak).height())/2 + _scrollTop + 'px',
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
    
    //隐藏
    hide : function() {
        if ( this.tipBak != null ) {
            this.tipBak.remove();
            this.tipBak = null;
        }
    },

    //外部调用接口
    work : function( options ) {

        this.options = {
            type : 'warn',
            content : '测试提示内容',
            timer : 0
        };
        //合并参数
        this.options = $.extend(this.options, options);

        this.create();
        this.setContent();
        this.show();
        this.center();
        
        //绑定调整窗口大小事件
        var self = this;
        $(window).bind('resize', function() {
            self.center();
        });
        
        //自动隐藏
        var that = this;
        if ( this.options.timer > 0 ) {
            setTimeout(function() {
                that.hide();
                if ( that.options.callback ) {
                    that.options.callback();
                }
            }, this.options.timer);
        }
    }
};

/**
 * 弹出窗口,只有关闭按钮
 * @param   options  
 * @param   options.width               弹出窗口的宽度, 高度自适应。
 * @param   options.title               弹出窗口的标题
 * @param   options.content             弹出窗口的内容
 * @param   options.borderWidth         是否有边框以及边框的宽度
 * @param   options.borderOpacity       边框的透明度
 * @param   options.borderColor         边框的颜色
 * @param   options.callback            关闭宽口的回调函数
 * @param   options.hasTitle            是否需要出现标题,如果不需要标题，则会删除window的边框和背景，完全由用户自定义窗口的样式。同时，控件也会失去拖动功能。
 * @param   options.skin                控件的皮肤默认为default
 * @param   options.position            设置对话框的位置, string | object
                                        center       => 居中显示
                                        selecter     => 参照物的选择器,对话框会出现在参照物的正上方  
                                        Array        => [top, left] 
 */
JDialog.win = {

    winBak   : null,
    winBg    : null,
    options : null,

    create : function() {
        
        if ( this.winBak != null ) return;
        var win_box = $('<div id="jdialog_win_box" class="'+this.options.skin+'" style="position:absolute; display:none;"></div>');
        //创建背景层
        if ( this.options.borderWidth != 0 ) {
            this.winBg = $('<div id="jdialog_win_box_bg" style="position:absolute; display:none;"></div>'); 
        }
        var title_box = $('<div id="jdialog_win_title_box"></div>');
        var win_content = $('<div id="jdialog_win_content"></div>');
        
        var win_title = $('<div id="jdialog_win_title"></div>');
        var win_close_button = $('<a class="jdialog_win_close" href="javascript:void(0)" title="关闭"><!--关闭按钮--></a>');
        var that = this;
        win_close_button.click(function() {
            if ( JDialog.lock.lockBak ) JDialog.lock.hide();
            that.hide();
            return false;       //消除IE跳转问题
        });
        
        if ( this.options.hasTitle ) {
            title_box.append(win_title);
            title_box.append(win_close_button);
            win_box.append(title_box);  
        } else {
            win_box.css({'border':'0px solid red', 'background':'none'});   
        }
        win_box.append(win_content);
        
        /* 添加拖拽事件 */
        $(title_box).mousedown(function(e) {
            var offsetLeft = e.pageX - $(win_box).position().left;
            var offsetTop = e.pageY - $(win_box).position().top;
            $(document).mousemove(function(e) {
                //清除拖动鼠标的时候选择文本
                window.getSelection ? window.getSelection().removeAllRanges():document.selection.empty();
                $(win_box).css({
                    'top'  : e.pageY-offsetTop + 'px',
                    'left' : e.pageX-offsetLeft + 'px'  
                });
                //移动背景层
                if ( that.winBg != null ) {
                    $(that.winBg).css({
                        'top'  : e.pageY-offsetTop - that.options.borderWidth + 'px',
                        'left' : e.pageX-offsetLeft - that.options.borderWidth + 'px'   
                    }); 
                }
            });
        }).mouseup(function() {
            $(document).unbind('mousemove');    
        });
        this.winBak = win_box;
        $('body').append(win_box);
        if ( this.winBg != null ) $('body').append(this.winBg);
    },
    
    //设置对话框的位置
    setPosition : function() {

        if ( this.winBak == null ) return;
        var _left = 0;
        var _top = 0;
        var _position = this.options.position;
        if ( typeof _position == 'string' ) {
            //居中显示
            if ( this.options.position == 'center' ) {
                var _scrollTop = window.document.body.scrollTop || window.document.documentElement.scrollTop;
                _top = (JDialog.getWindowHeight() - $(this.winBak).height())/2 + _scrollTop;
                _left = (JDialog.getWindowWidth() - this.options.width)/2;
            } else {
                //获取参照物元素的位置
                var _offset = $(_position).offset();
                _top = _offset.top - $(this.winBak).height();
                _left = _offset.left + $(_position).width()/2 - $(this.winBak).width()/2;
            }
            //直接设置对话框到指定的位置
        } else if ( Object.prototype.toString.apply(this.options.position) === "[object Array]" ) {
            _top = _position[0];
            _left = _position[1];   
        }
        if ( _top < 0 ) _top = 0;
        if ( _left < 0 ) _left = 0;
        
        $(this.winBak).css({
            'width' : this.options.width + 'px', 
            'top'   : _top + 'px',
            'left'  : _left + 'px'
        });
        
        //设置背景层位置
        if ( this.winBg == null ) return;
        $(this.winBg).css({
            'opacity' : this.options.borderOpacity,
            'background-color' : this.options.borderColor,
            'width'   : this.options.width + this.options.borderWidth * 2 + 'px',
            'height'  : $(this.winBak).height() + this.options.borderWidth * 2 +'px',
            'top'     : _top - this.options.borderWidth + 'px',
            'left'    : _left - this.options.borderWidth + 'px'
        });
    },
    
    /*设置标题和内容*/
    setContent : function() {
        if ( this.winBak == null ) return;
        //设置标题栏的宽度，使文字居中
        var close_width = $('#jdialog_win_title_box .jdialog_win_close').width();
        $('#jdialog_win_title').html(this.options.title);
        if ( typeof this.options.content == 'string' ) {
            $('#jdialog_win_content').html(this.options.content);
        } else {
            if ( this.options.content == null ) return;
            $('#jdialog_win_content').append(this.options.content);
        }
    },

    /* 清空弹出框中的内容 */
    empty : function() {
        if ( this.winBak == null ) return;
        $('#jdialog_win_content').empty();
    },

    //显示
    show : function() {
        if ( this.winBak == null ) return;
        $(this.winBak).fadeIn();
        if ( this.winBg != null ) $(this.winBg).fadeIn();
    },

    //隐藏
    hide : function() {
        if ( this.winBak != null ) {
            $(this.winBak).hide();
            $(this.winBg).hide();
        }
    },

    //移除dom
    remove : function() {

        if ( this.winBak != null ) {
            $(this.winBak).remove();
            $(this.winBg).remove();
            this.winBak = null;
            this.winBg = null;
        }

        if ( typeof this.options.callback == 'function' ) this.options.callback();
    },
    
    work : function( options ) {

        this.options = {
            title : '这里显示窗口标题',
            content : '这里显示内容',
            width : 600,
            borderWidth : 8,
            borderOpacity : 0.4,
            borderColor : '#000000',
            hasTitle : true,
            skin : 'default',
            position : 'center',
            maxWidth : 1024
        };
        
        //合并参数
        this.options = $.extend(this.options, options);
        
        this.options.skin = 'jdialog_win_' +  this.options.skin;
                
        if (this.options.widthType == 'percent') {
            this.options.width = this.options.width * JDialog.getWindowWidth()/100;
            if ( this.options.width  > this.options.maxWidth )
                this.options.width = this.options.maxWidth;
        } 
        this.create();
        this.setContent();
        this.setPosition();
        this.show();
        
        //绑定调整窗口大小事件
        var self = this;
        $(window).bind('resize', function() {
            self.setPosition();
        });
    }
};

/**
 * 弹出小型确认框, 并返回对话框的一个引用
 * @param   options
 * @param   width       int         对话框宽度
 * @param   content     string          对话框显示的内容
 * @param   title       string      对话框标题
 * @param   options.position            设置对话框的位置, string | object
                                        center       => 居中显示
                                        selecter     => 参照物的选择器,对话框会出现在参照物的正上方  
                                        Array        => [top, left]
 * @param   button      object      按钮对象 名称 => 对象, name : function()
 * @param   skin        string      对话框皮肤
 * @param   icon        string      对话框图表，显示不同的类型图标
 */
JDialog.confirm = {
    conBak : null,
    conBg : null,   //提示框背景
    options : null,     //用户参数对象
    //创建元素
    create : function() {
        if ( this.conBak !=  null ) return;
        var confirmBox = $('<div id="jdialog_confirm" class="'+this.options.skin+'"></div>');
        this.conBg = $('<div id="jdialog_confirm_box_bg"></div>'); 
        //line one
        var confirmTitle = $('<div id="jdialog_confirm_titleBox"></div>');
        var title = $('<div id="jdialog_confirm_title"></div>');
        var close = $('<a href="javascript:void(0)" title="关闭"><!--关闭--></a>');
        
        var me = this;
        close.click(function() {
            me.hide();  
            if ( JDialog.lock.lockBak ) JDialog.lock.hide();
            return false;
        });
        
        /* 添加拖拽事件 */
        $(confirmTitle).mousedown(function(e) {
            
            var offsetLeft = e.pageX - $(confirmBox).position().left;
            var offsetTop = e.pageY - $(confirmBox).position().top;
            $(document).mousemove(function(e) {
                //清除拖动鼠标的时候选择文本
                window.getSelection ? window.getSelection().removeAllRanges():document.selection.empty();
                $(confirmBox).css({
                    'top'  : e.pageY-offsetTop + 'px',
                    'left' : e.pageX-offsetLeft + 'px'  
                });
                
                $(me.conBg).css({
                    'top'  : e.pageY-offsetTop - me.options.borderWidth + 'px',
                    'left' : e.pageX-offsetLeft - me.options.borderWidth + 'px'   
                }); 
            });
            
        }).mouseup(function() {
            $(document).unbind('mousemove');    
        });
        
        confirmTitle.append(title);
        confirmTitle.append(close);
        //line two
        var contentBox = $('<div id="jdialog_confirm_CBOX"></div>');
        
        //icon
        var icon = $('<span id="jdialog_confirm_icon"></span>');
        var content = $('<div id="jdialog_confirm_content"></div>');
        contentBox.append(icon);
        contentBox.append(content);
                
        //line three, create button
        var btnBox = $('<div id="jdialog_confirm_button"></div>');
        var btn = $('<div id="jdialog_confirm_buttonInner"></div>');
        
        //创建button对象
        var JButton = function( name, callback ) {
            var _button = $('<a href="javascript:void(0);">'+name+'</a>');
            _button.click(function() {
                callback();
                return false;       //消除IE跳转问题  
            });
            return  _button;
        }
        //循环将所有按钮加入按钮盒子中
        for ( var name in this.options.button ) {
            var _button =  new JButton(name, this.options.button[name])
            btn.append(_button);
        }
        btnBox.append(btn);
        
        confirmBox.append(confirmTitle);
        confirmBox.append(contentBox);
        confirmBox.append(btnBox);
        $('body').append(confirmBox);
        $('body').append(this.conBg);
        //设置类别图标
        this.setIcon( this.options.icon );
        this.conBak = confirmBox;
    },
    
    /**
     * 设置图标
     * @param       string      itype       图标类型
     */
    setIcon : function( itype ) {
        var icon = $('#jdialog_confirm_icon');
        switch ( itype ) {
            case 'warn':
                icon.addClass('jdialog_confirm_icon_warn');
                break;  
            case 'ok':
                icon.addClass('jdialog_confirm_icon_ok');
                break;  
            case 'error':
                icon.addClass('jdialog_confirm_icon_error');
                break;  
            case 'help':
                icon.addClass('jdialog_confirm_icon_help');
                break;  
            case 'sad':
                icon.addClass('jdialog_confirm_icon_sad');
                break;
            case 'smile':
                icon.addClass('jdialog_confirm_icon_smile');
                break;  
            case 'laugh':
                icon.addClass('jdialog_confirm_icon_laugh');
                break;
            case 'loader':
                icon.addClass('jdialog_confirm_icon_loader');
                break;      
            case 'none':
                icon.hide();
                break;
            default:
                icon.addClass('jdialog_confirm_icon_warn');
                break;  
        }     
    },
    
    //设置内容
    setContent : function( __content ) {
        __content = __content || this.options.content;
        if ( this.conBak == null ) return;
        $('#jdialog_confirm_title').html(this.options.title);
        //设置内容框的宽度
        var _width = this.options.width - $('#jdialog_confirm_icon').width() - 32;
        if ( this.options.icon == 'none' ) {
            _width = this.options.width;
        }
        $('#jdialog_confirm_content').css('width', _width+'px').html(__content);
    },
    
    //元素居中
    setPosition : function() {
        
        if ( this.conBak == null ) return;
        var left = 0;
        var top = 0;
        var position = this.options.position;
        if ( typeof position == 'string' || typeof position == 'object' ) {
            //居中显示
            if ( position == 'center' ) {
                var _scrollTop = window.document.body.scrollTop || window.document.documentElement.scrollTop;
                top = (JDialog.getWindowHeight() - $(this.conBak).height())/2 + _scrollTop;
                left = (JDialog.getWindowWidth() - this.options.width)/2;
            } else {
                //获取参照物元素的位置
                var offset = $(position).offset();
                switch ( this.options.placement ) {
                    case 'left' :
                        top = offset.top - $(this.conBak).height()/2 + $(position).height()/2;
                        left = offset.left - $(this.conBak).width() -10;
                        break;
                    case 'right':
                        top = offset.top - $(this.conBak).height()/2;
                        left = offset.left + $(this.options.position).width() + $(this.conBak).width();
                        break;
                    case 'bottom':
                        top = offset.top + $(this.conBak).height() + 10;
                        left = offset.left + $(this.options.position).width()/2 - $(this.conBak).width()/2;
                        break;
                    default :
                        top = offset.top - $(this.conBak).height() - 10;
                        left = offset.left + $(this.options.position).width()/2 - $(this.conBak).width()/2;
                }
            }
            //直接设置对话框到指定的位置
        } else if ( Object.prototype.toString.apply(this.options.position) === "[object Array]" ) {
            top = position[0];
            left = position[1];
        }
        
        $(this.conBak).css({
            'width' : this.options.width +'px',
            'top'   : top + 'px',
            'left'  : left + 'px'
        });
        
        //背景的位置
        $(this.conBg).css({
            'opacity' : this.options.opacity,
            'width' : this.options.width + this.options.borderWidth * 2 +'px',
            'height' : $(this.conBak).height() + this.options.borderWidth * 2 +'px',
            'top'   : top - this.options.borderWidth + 'px',
            'left'  : left - this.options.borderWidth + 'px'
        });
        
    },
    
    //显示元素
    show : function() {
        if ( this.conBak == null ) return;
        $(this.conBak).show();
        $(this.conBg).show();
    },
    
    //隐藏元素
    hide : function(timer) {
        
        timer = timer || 0;
        if ( this.conBak == null ) return;
        $(this.conBak).hide(timer);
        $(this.conBg).hide(timer);
        $(window).unbind('resize');
        
    },

    //移除元素
    remove : function() {
        if ( this.conBak != null ) {
            $(this.conBak).remove();
            $(this.conBg).remove();
            this.conBak = null;
            this.conBg = null;
        }
    },
    
    //调用接口
    work : function( options ) {
        
        this.options = {
            width : 300,            //宽度
            title : '操作提示',       //标题
            content : '确定要执行该操作吗？',     //提示内容
            position : 'center',      //显示位置, 默认为居中显示, 也可以在指定的参照物显示
            placement : 'top', //辅助显示位置，这个设置只对position设置为object有用
            skin : 'default',       //皮肤
            icon : 'warn',          //提示图标
            button : {},        //操作按钮组
            borderWidth : 8,    //提示框边框宽度
            opacity : 0.3,      //边框透明度
            maxWidth : 500      //最大宽度
        };
        //合并参数
        this.options = $.extend(this.options, options);

        this.options.skin = 'jdialog_confirm_' + this.options.skin;
        if ( this.options.shadow ) this.options.skin += ' jdialog_confirm_'+this.options.skin+'_shadow';
        
        if (this.options.widthType == 'percent') {      //按百分比取值
            this.options.width = this.options.width * JDialog.getWindowWidth()/100;
            window.alert(this.options.width);
            if ( this.options.width  > this.options.maxWidth )
                this.options.width = this.options.maxWidth;
        } 

        this.create();
        this.setContent();
        this.setPosition();
        this.show();
        
        //绑定调整窗口大小事件
        var self = this;
        $(window).bind('resize', function() {
            self.setPosition();
        });
    }
};
