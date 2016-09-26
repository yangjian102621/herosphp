/**
 * 筛选指定类型或者指定产品的图片
 * 需要传入图片筛选Id pid, 从数据库中筛选图片地址
 */
KindEditor.plugin('filefilter', function(K) {
        var editor = this, name = 'filefilter';
        // 点击图标时执行
        editor.clickToolbar(name, function() {

            var dialog = K.dialog({
				width : 500,
				title : '当前文章文件管理',
				body : '<iframe id="fileFrame" frameborder="0"  scrolling="no" width="'+editor.frameSize[0]+'" height="'+editor.frameSize[1]+'" src="'+editor.fileManagerUrl+'" allowtransparency="true"></iframe>',
				closeBtn : {
					name : '关闭',
					click : function(e) {
						dialog.remove();
					}
				},
				yesBtn : {
					name : '确定',
					click : function(e) {
						var _iframe = document.getElementById('fileFrame');
						var _data = _iframe.contentWindow.getImageData();
						for ( var i = 0; i < _data.url.length; i++ ) {
							var _html = '';
							switch ( _data.type[i] ) {
								case 'image':
									_html = '<img src="'+_data.url[i]+'" title="'+_data.title[i]+'" alt="'+_data.title[i]+'" /> <br />';
									break;
								case 'flash':
									_html = '<embed src="'+_data.url[i]+'" type="application/x-shockwave-flash" width="550" height="400" autostart="false" loop="true" /><br />';
									break;
								case 'media':
									_html = '<embed src="'+_data.url[i]+'" type="application/x-shockwave-flash" width="550" height="400" quality="high" /> <br />';
									break;
								case 'file':
									_html = '<a class="ke-insertfile" href="'+_data.url[i]+'" target="_blank">'+_data.url[i]+'</a><br />';
									break;
							}
							editor.insertHtml(_html);
							dialog.remove();
						}
					}
				},
				noBtn : {
					name : '取消',
					click : function(e) {
						dialog.remove();
					}
				}
			});
        });
		
});