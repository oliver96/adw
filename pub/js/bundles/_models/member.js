Member = (function ( Backbone, _, $ ) {
    'use strict';
    var Member = Backbone.CommonModel.extend({
        // 正则表达式
        urlRoot : App.router.url({
            'm': 'member', 
            'a': 'api'
        })
        , defaults : {
            'id' : ''
            , 'name' : ''
            , 'real_name' : ''
            , 'mobile' : ''
            , 'qq' : ''
            , 'email' : ''
            , 'province' : ''
            , 'city' : ''
            , 'addr' : ''
            , 'store_id' : 0
            , 'seller_id' : 0
            , 'device' : ''
            , 'hard_addr' : ''
            , 'sim_no' : ''
            , 'invoice_no' : ''
            , 'status' : 1
        }
        , validate : function(data) {
            var hasError = false;
            this.removeErrors();
            if('' == data.name) {
                this.addError('name', '用户名称不能为空。');
                hasError = true;
            }
            if('' == data.real_name) {
                this.addError('real_name', '必须填写真实姓名。');
                hasError = true;
            }
            if('' == data.mobile) {
                this.addError('mobile', '手机号码不能为空。');
                hasError = true;
            }
            else if(!this.validators.isMobile(data.mobile)) {
                this.addError('mobile', '手机号码格式不是有效的。');
                hasError = true;
            }
            if('' == data.qq) {
                this.addError('qq', 'QQ号码不能为空。');
                hasError = true;
            }
            else if(!this.validators.isQQ(data.qq)) {
                this.addError('qq', 'QQ号码格式不是有效的。');
                hasError = true;
            }
            if('' == data.email) {
                this.addError('email', '邮件地址不能为空');
                hasError = true;
            }
            else if(!this.validators.isEmail(data.email)) {
                this.addError('email', '邮件地址格式不是有效的。');
                hasError = true;
            }
            if('' == data.addr) {
                this.addError('addr', '地址不能为空。');
                hasError = true;
            }
            if('' == data.device) {
                this.addError('device', '设备名称型号不能为空。');
                hasError = true;
            }
            if('' == data.hard_addr) {
                this.addError('hard_addr', 'MAC地址不能为空。');
                hasError = true;
            }
            if('' == data.sim_no) {
                this.addError('sim_no', '设备联网登记SIM卡号不能为空。');
                hasError = true;
            }
            return hasError;
        }
    });
    
    return Member;
}( Backbone, _, jQuery ));

