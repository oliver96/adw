define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'bootstrap'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
], function($, _, Backbone) {
    var MainPageView = Backbone.View.extend({
        el: $("body")
        , events : {
            'keypress #verify_code' : 'entryVerifyCodeEvent'
            , 'click #reverify_button' : 'sendVerifyCodeEvent'
            , 'click #register_button' : 'registerEvent'
        }
        , entryVerifyCodeEvent : function(e) {
            var verifyCode = $('#verify_code').val();

            if('' !== verifyCode) {
                $('#register_button').removeClass('disabled');
            }
        }
        , sendVerifyCodeEvent : function(e) {
            $('#reverify_button').addClass('disabled');
            var num = 30;
            var counter = function() {
                if(0 === num) {
                    $('#reverify_button').text('重新获取验证码').removeClass('disabled');
                }
                else {
                    if(num == 30) {
                        var url = App.router.url({'m' : 'register', 'a' : 'sendMessage'});
                        $.get(url, function(res) {});
                    }
                    $('#reverify_button').text('重新获取验证码(' + num + '秒)');
                    num --;
                    setTimeout(counter, 1000);
                }
            }
            counter();
        }
        , registerEvent : function(e) {
            if(!$(this).hasClass('disabled')) {
                var common = new Backbone.CommonModel();
                var verifyCode = $('#verify_code').val();
                var checked = $('#accept')[0].checked;
                var ok = true;
                if('' == verifyCode) {
                    common.addError('verify_code', '');
                    ok = false;
                }
                else {
                    common.removeError('verify_code');
                }
                if(!checked) {
                    common.addError('accept', '');
                    ok = false;
                }
                else {
                    common.removeError('accept');
                }
                if(ok) {
                    var code = $('#verify_code').val();
                    var url = App.router.url({'m' : 'register', 'a' : 'doVerify'});
                    $.post(url, {'code': code}, function(res) {
                        if(res.success) {
                            window.location.href = App.router.url({'m' : 'register', 'a' : 'confirm'});
                        }
                        else {
                            common.addError('verify_code', '验证码输入错误！');
                        }
                    });
                }
            }
        }
     });
     new MainPageView();
});
