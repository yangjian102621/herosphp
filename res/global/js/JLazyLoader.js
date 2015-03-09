/**
 * 图片滚动加载
 ******************************************************************
 * 此专门为HerosPHP框架开发的javascript工具
 * 版权所有 (C) 2013.03-2013 网络星空工作室研发中心 并保留所有权利。
 ******************************************************************* 
 * @author		yangjian<yangjian102621@gmail.com>
 * @version		1.0
 */
var imgLazyLoad = {
	/**
	 * 配置对象，初始化是传入，src表示图片的真实地址在img中的属性
	 * time 表示延时加载时间
	 */
	imgConfig : null,
	/**
	 * 图片数组对象，将所有需要延时加载的Img标签全部存入数组中
	 */
	docImgs : new Array(),
	/**
	 * 已经加载的图片
	 */
	hasLoadImg : [],
	/**
	 * 初始化
	 */
	init : function(_options) {
		this.imgConfig = (arguments.length == 0) ? {src : 'name',time : 300,filter : null} : {src : _options.src || 'name',time : _options.time || 300, filter :  _options.filter};
		//获取所有img标签
		this.docImgs = document.images;
		//过滤图片，筛选出需要延时加载的图片
		if ( typeof this.imgConfig.filter === 'function' ) {
			imgs = [];
			for ( var i = 0; i < this.docImgs.length; i++ ) {
				if ( this.imgConfig.filter(this.docImgs[i]) ) imgs.push(this.docImgs[i]);
			}
			this.docImgs = imgs;
		}
		var that = this;

		//添加滚动事件
		window.onscroll = function () {
			setTimeout(function () {
				that.loadImg();
			}, that.imgConfig.time);
		};

		setTimeout(function(){
			that.loadImg();	
		},that.imgConfig.time);
		
	},
	/**
	 * 格式化css属性
	 * 如：把 background-color 转换成 backgroundColor
	 */
	cameLize : function(str) {
		return str.replace(/-(\w)/g,function(str_match,s){
			return s.toUpperCase();
		});
	},
	/**
	 * 获取css样式的属性
	 */
	getStyle : function(element,property) {
		if (arguments.length != 2) return false;
		var value = element.style[this.cameLize(property)];
		if (!value) {
			if (document.defaultView && document.defaultView.getComputedStyle) {
				var css = document.defaultView.getComputedStyle(element, null);
				value = css ? css.getPropertyValue(property) : null;
			} else if (element.currentStyle) {
				value = element.currentStyle[this.cameLize(property)];
			}
		}
		return value == 'auto' ? '' : value;
	},
	/**
	 * 加载图片
	 */
	loadImg : function() {
		if ( this.docImgs.length == 0 ) {
			window.onscroll = null;
			return;	
		}
		var offsetPage = window.pageYOffset ? window.pageYOffset : window.document.documentElement.scrollTop;
		var offsetWindow = offsetPage + Number(window.innerHeight ? window.innerHeight : document.documentElement.clientHeight);
		var _len = this.docImgs.length;
		if ( _len <= 0 || _len == undefined ) return false;
		for (var i = 0; i < _len; i++) {
			//如果图片已经加载了，就不再重新加载
			if ( this.hasLoadImg[i] != undefined ) continue;
			var attrSrc = this.docImgs[i].getAttribute(this.imgConfig.src);
			var o = this.docImgs[i];
			var _tagName = o.nodeName.toLowerCase();
			if (o) {
				var postPage = o.getBoundingClientRect().top + window.document.documentElement.scrollTop + window.document.body.scrollTop; 
				var postWindow = postPage + Number(this.getStyle(o, 'height').replace('px', ''));
				if ((postPage > offsetPage && postPage < offsetWindow) ||
				 (postWindow > offsetPage && postWindow < offsetWindow)) {
					if ( _tagName === "img" && attrSrc !== null ) {
						o.setAttribute("src", attrSrc);	//设置图片的真实地址
						o.removeAttribute(this.imgConfig.src);	 //移除自定义属性
						o.removeAttribute('class');	 //移除class属性

                        if ( typeof $ == 'function') {  //添加渐显示效果
                            o.style.display = "none";
                            $(o).fadeIn(1000);
                        }
						//o.removeAttribute('style');	 //移除style属性
						this.hasLoadImg[i] = o;
					}
					o = null;
				}
			}
		}
		
	}
};

imgLazyLoad.init({src:'data-src',time:500, filter:function(node){
	if ( node.className != 'juke_lazy_load' ) return false;
	return true; 
}}); 

