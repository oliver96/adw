Seller = (function ( Backbone, _, $ ) {
    'use strict';
    var Seller = Backbone.CommonModel.extend({
        // 正则表达式
        urlRoot : App.router.url({
            'm': 'seller', 
            'a': 'api'
        })
        , defaults : {
            'id' : ''
            , 'name' : ''
            , 'store_id' : ''
            , 'store_name' : ''
            , 'mobile' : ''
            , 'status' : 0
            , 'created' : ''
        }
        , validate : function(data) {
            var hasError = false;
            this.removeErrors();
            if('' == data.name) {
                this.addError('name', '店员名称不能为空。');
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
            return hasError;
        }
    });
    
    return Seller;
}( Backbone, _, jQuery ));

