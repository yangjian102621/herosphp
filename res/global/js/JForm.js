/**
 * 表单验证工具
 * @version 1.1
 * @author yangjian102621@gmail.com
 * @since   2014.06.17
 */
window.JForm = function ( options ) {

    this.options = {
        formId : '',        //要验证的表单的id
        filter : function( ele ) {  //需要验证的表单元素过滤
            return $(ele).attr('required') == undefined;
        },

        checkSuccess : true,    //验证成功

        continueCheck : false, //是否连续验证

        showMessage : function( type, message, ele ) { //错误处理接口
            alert('message');
            //JDialog.tip.work({type:type, content:message, timer : 2000});
        }
    };

    $.extend(this.options, options); //合并options

    this.options.pattern = {
        //用户名
        uname: /^[0-9|a-z|\-|_]+$/i,
        //邮箱
        email: /^[a-z0-9]{1}\w{1,18}@[a-z0-9]{1,20}(\.[a-z]{1,6}){1,3}$/i,
        //网址url
        url: /^https?:\/\/(www\.)?[\w-]{1,}(\.[a-z|0-9]{1,}){1,2}/i,
        //域名
        domain: /[\w-]{1,}(\.[a-z|0-9]{1,}){1,2}$/i,
        //手机号码
        mobile: /^1[3|4|5|8][0-9]{9}$/,
        //电话号码
        phone: /^[0-9]{2,5}[-][0-9]{7,8}$/,
        //数字
        number: /^[0-9]+$/,
        //中文
        cn : /[\u4E00-\u9FA5]/g,
        //IP
        ip: /^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/
    }

    $.extend(this.options.pattern, options.pattern);    //合并正则表达式

    /* checkbox 集合缓存 */
    this.checkbox = {};

    /**
     * 验证表单数据合法性
     * @param formid 要验证的表单id
     * @param filter 元素验证过滤器
     */
    JForm.prototype.checkFormData = function (formid, filter) {
    	
        if ( formid ) this.options.formId = formid;
        if ( typeof filter == 'function' ) this.options.filter = filter;
        var form = document.getElementById(this.options.formId);
        var elements = form.elements, len = elements.length;

        for (var i = 0; i < len; i++) {

            var ele = elements[i], value = ele.value;
            if ( this.options.filter(ele) ) continue;

            //普通数据的非空验证
            var tipText = $(ele).attr('placeholder');
            if ( value.trim() == '') {
                this.errorMessage('error', tipText + '不能为空！', ele);
                if (this.options.continueCheck) continue
                else break;
            }

            //数据长度的验证
            var minLength = $(ele).attr('min-length'), maxLength = $(ele).attr('max-length');
            if ( minLength && value.length < minLength ) {
                this.errorMessage('error', tipText+"最小长度为"+minLength, ele);
                if (this.options.continueCheck) continue
                else break;
            }
            if ( maxLength && value.length > maxLength ) {
                this.errorMessage('error', tipText+"最大长度为"+maxLength, ele);
                if (this.options.continueCheck) continue
                else break;
            }

            //checkbox验证
            if ( ele.type == 'checkbox' && !this.checkbox[ele.name] ) {

                var max_check = parseInt($(ele).attr("max-check"));
                var min_check = parseInt($(ele).attr("min-check"));
                var checked = 0;
                var checkboxs = $(form).find("input[name='"+ele.name+"']");

                for ( var n = 0; n < checkboxs.length; n++ ) {
                    if ( checkboxs[n].checked ) checked++;
                }

                if ( min_check && checked < min_check ) {
                    this.errorMessage('error', tipText+'至少要选中'+min_check+"项！", ele);
                    if (this.options.continueCheck) continue
                    else break;
                }

                if ( max_check && checked > max_check ) {
                    this.errorMessage('error', tipText+'最多选中'+max_check+"项！", ele);
                    if (this.options.continueCheck) continue
                    else break;
                }

                this.checkbox[ele.name] = 1;    //同样名称的checkbox只检查一个
            }

            //密码及重复密码验证
            var dtype = $(ele).attr('dtype');
            if ( dtype == 'password' ) {

                if ( this.options.continueCheck ) {

                    var pass_rank = this.checkPassRank(value);
                    switch ( pass_rank ) {
                        case 0:
                        case 1:
                            this.errorMessage('error', this.checkPassRank(value), ele);
                            break;

                        case 2:
                            this.errorMessage('warn', this.checkPassRank(value), ele);
                            break;

                        case 3:
                            this.errorMessage('ok', this.checkPassRank(value), ele);
                    }
                    continue;
                }

            } else if ( dtype == 'repass' ) {
                var password = $(ele).attr('for-password')
                if ( value != $('#'+password).val().trim() ) {
                    this.errorMessage('error', "两次输入密码不一致！", ele);
                    if (this.options.continueCheck) continue
                    else break;
                }

            } else {    /* 正则判断 */
                if ( !this.checkOneItem(ele) ) {
                    this.errorMessage('error', tipText + '格式错误！', ele);
                    if (this.options.continueCheck) continue
                    else break;
                }
            }

            //ajax验证
            var url = $(ele).attr('ajax');
            if ( url != undefined ) {

                var self = this;
                $.get(url, {data:value}, function(res) {
                    self.errorMessage(res.state, res.message, ele);
                }, 'json');

                continue;

            }

            //数据验证通过
            if ( this.options.continueCheck && ele.type != 'checkbox' && ele.type != 'radio' )
                this.errorMessage('ok', tipText+"填写正确！", ele);

        }

        if ( this.options.checkSuccess ) {
            return true;
        } else {
            return false;
        }
    };

    /* 检测某一元素的值是否合法 */
    JForm.prototype.checkOneItem = function (ele) {

        var type = $(ele).attr('dtype');
        if (!type) return true;
        var value = $(ele).val().trim();

        if ( type != 'idnum' && this.options.pattern[type] ) {

            return this.options.pattern[type].test(value);

        } else {    /* 如果是身份证，则进行校验码验证 */
            return this.verifyGmsfhLast(value);
        }

    };

    /* 对18位身份证进行校验码验证 */
    JForm.prototype.verifyGmsfhLast = function (value) {

        if (value.length != 18) return false;
		//加权因子
        var wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
		//校验码对应值 
        var vi = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
        var ai = new Array(17);
        var sum = 0;
        var remaining, verifyNum;

        for (var i = 0; i < 17; i++) ai[i] = parseInt(value.substring(i, i + 1));

        for (var m = 0; m < ai.length; m++) sum = sum + wi[m] * ai[m];

        remaining = sum % 11;
        if (remaining == 2) verifyNum = "X";
        else verifyNum = vi[remaining];

        return (verifyNum == value.substring(17, 18));
    };

    /**
     *
     * 1.如果密码少于6位，那么就认为这是一个太弱密码。返回0
     * 2.如果密码只由数字、小写字母、大写字母或其它特殊符号当中的一种组成，则认为这是一个弱密码。返回1
     * 3.如果密码由数字、小写字母、大写字母或其它特殊符号当中的两种组成，则认为这是一个中度安全的密码。返回2
     * 4.如果密码由数字、小写字母、大写字母或其它特殊符号当中的三种以上组成，则认为这是一个比较安全的密码。返回3
     * @param str
     */
    JForm.prototype.checkPassRank = function( str ) {

        if ( str.length <= 5 ) return 0;
        var mode = 0;
        //获取该字符串的所有组成模式
        for ( var i = 0; i < str.length; i++ ) {
            mode |= this.charMode(str.charCodeAt(i));
        }
        return this.getModeNum(mode);
    };

    /**
     * 计算一个字符所属的类型
     * 数字|小写字母|大写字母|特殊字符
     */
    JForm.prototype.charMode = function( code ) {
        if ( code >= 48 && code <= 57 ) //数字 00000000 00000000 00000000 00000001
            return 1;
        if ( code >= 65 && code <= 90 ) //大写字母 00000000 00000000 00000000 00000010
            return 2;
        if ( code >= 97 && code <= 122 ) //小写 00000000 0000000 00000000 00000100
            return 4;
        else
            return 8; //特殊字符    0000000 0000000 00000000 00001000
    }

    /**
     * 获取一共有过少种组合模式，并转换为十进制的表示模式
     * @param number 模式总数
     * 00000010
     * 00000001
     */
    JForm.prototype.getModeNum = function( number ) {
        var modes = 0;
        for ( var i = 0; i < 4; i++ ) {
            if ( number & 1 ) modes++;
            number>>>=1;    //向右移动一位
        }
        return modes;
    };

    /**
     * 错误信息处理
     * @param type
     * @param message
     * @param ele
     */
    JForm.prototype.errorMessage = function( type, message, ele ) {

        if ( type == 'error' ) this.options.checkSuccess = false;
        this.options.showMessage(type, message, ele);    //调用用户的错误信息处理接口

    };
};
