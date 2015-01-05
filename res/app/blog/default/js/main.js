/**
 * admin应用公共js代码
 * @author  yangjian<yangjian102621@gmail.com>
 */

/**
 * 初始化页面
 */
$(document).ready(function() {
    
    (function() {
        //自动计算数据列表表格的colspan
        $('.'+__global.contentList.emptyTd).each(function() {
                
            var th = $(this).parent().parent().prev().children(":first").children();
            $(this).attr('colspan', th.length);
            
        });
        
        //为内容表单绑定全选事件
        var form = document.getElementById(__global.contentList.formId);
        if ( form != null ) {
            
            var elements = form.elements;
            var length = elements.length;
            
            $('#'+__global.contentList.checkAllId).on('ifChecked', function() {   
                
                for ( var i = 0; i < length; i++ ) {
                    if ( elements[i].type != 'checkbox' 
                        || elements[i].name != __global.contentList.checkboxName ) continue;
                    
                    $(elements[i]).iCheck('check');
                }
                
            }).on('ifUnchecked', function() {
                for ( var i = 0; i < length; i++ ) {
                    if ( elements[i].type != 'checkbox' 
                        || elements[i].name != __global.contentList.checkboxName ) continue;
                    
                    $(elements[i]).iCheck('uncheck');
                }
            });
        }
        
    })();
    
    //初始化iCheck插件
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    
});

/**
 * 批量快速编辑列表内容 
 * 
 * @param       string      url     处理操作的url
 * @param       string      form_id     数据表单ID
 */
function quickEdit( url, form_id ) {
    
    form_id = form_id || __global.contentList.formId;
    var __data = $('#'+form_id).serialize();        //获取表单数据
    
    $.post(url, __data, function( res ) {
        
        JDialog.tip.work({type:res.state, content:res.message, timer:__global.jdialog.timer});
        
    }, 'json');
    
}

