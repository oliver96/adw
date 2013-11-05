Store = (function ( Backbone, _, $ ) {
    'use strict';
    var Store = Backbone.CommonModel.extend({
        // 正则表达式
        urlRoot : App.router.url({
            'm': 'store', 
            'a': 'api'
        })
        , defaults : {
            'id' : ''
            , 'name' : ''
            , 'province' : ''
            , 'city' : ''
            , 'addr' : ''
            , 'status' : 0
            , 'created' : ''
        }
        , validate : function(data) {
            var hasError = false;
            this.removeErrors();
            if('' == data.name) {
                this.addError('name', '用户名称不能为空。');
                hasError = true;
            }
            if('' == data.addr) {
                this.addError('addr', '必须输入详细地址。')
            }
            return hasError;
        }
    });
    
    return Store;
}( Backbone, _, jQuery ));

