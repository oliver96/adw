Backbone.CommonModel = (function ( Backbone, _, $ ) {
    'use strict';
    
    var CommonModel = Backbone.Model.extend({
        patterns: {
            // 特别字符
            'specialCharacters' : '[^a-zA-Z 0-9]+'
            // 数字
            , 'digits' : '[0-9]'
            // 邮件地址
            , 'email' : '^[a-zA-Z0-9._-]+@[a-zA-Z0-9][a-zA-Z0-9.-]*[.]{1}[a-zA-Z]{2,6}$'
            // 手机号码
            , 'mobile' : '^1[3|4|5|8][0-9]\\d{4,8}$'
            // qq号码
            , 'qq' : '^\\s*[.0-9]{5,10}\\s*$'
        }
        
        , pattern : function(value, pattern) {
            var regExp = new RegExp(pattern);
            return regExp.test(value);
        }

        // Validators
        , validators: {
            // 最小长度
            minLength: function(value, minLength) {
                return value.length >= minLength;
            }
            // 最在长度
            , maxLength: function(value, maxLength) {
                return value.length <= maxLength;
            }
            // 是否是邮件格式
            , isEmail: function(value) {
                return CommonModel.prototype.pattern(value, CommonModel.prototype.patterns.email);
            }
            // 包含特别字符串
            , hasSpecialCharacter: function(value) {
                return CommonModel.prototype.pattern(value, CommonModel.prototype.patterns.specialCharacters);
            }
            // 手机号码 
            , isMobile: function(value) {
                console.log(CommonModel.prototype.patterns.mobile);
                return CommonModel.prototype.pattern(value, CommonModel.prototype.patterns.mobile);
            }
            // QQ号码 
            , isQQ: function(value) {
                return CommonModel.prototype.pattern(value, CommonModel.prototype.patterns.qq);
            }
        }
        , addError : function(elid, message) {
            var $wrapper = $('#' + elid).closest('.control-group')
                          .addClass('error')
                          .find('.controls')
                , $errorEl = $wrapper.find('.help-inline');
            
            if($errorEl.length > 0) {
                $errorEl.text(message);
            }
            else {
                $wrapper.append('<span class="help-inline">' +  message + '</span>')
            }
        }
        , addErrors : function(messages) {
            for(var elid in messages) {
                this.addError(elid, messages[elid]);
            }
        }
        , removeError : function(elid) {
            var $wrapper = $('#' + elid).closest('.control-group').removeClass('error');
            if($wrapper.hasClass('error')) {
                $wrapper.find('.help-inline').remove();
            }
        }
        , removeErrors : function() {
            var $els = $('.control-group.error');
            for(var i = 0, l = $els.length; i < l; i ++) {
                $($els[i]).removeClass('error').find('.help-inline').remove();
            }
        }
    });
    
    return CommonModel;
}( Backbone, _, jQuery ));


