Advertising = (function ( Backbone, _, $ ) {
    'use strict';
    var Advertising = Backbone.CommonModel.extend({
        // 正则表达式
        urlRoot : App.router.url({
            'm': 'advertising', 
            'a': 'api'
        })
        , defaults : {
            'id' : ''
            , 'name' : ''
            , 'adv_id' : 0
            , 'material_id' : 0
            , 'member_id' : 0
        }
        , validate : function(data) {
            var hasError = false;
            this.removeErrors();
            if('' == data.name) {
                this.addError('ad_name', '广告名称不能为空');
                hasError = true;
            }
            if(parseInt(data.adv_id) == 0) {
                this.addError('adv_id', '必须选择所属的广告主。');
                hasError = true;
            }
            if(parseInt(data.material_id) == 0) {
                this.addError('material_id', '必须选择所属的广告素材。');
                hasError = true;
            }
            return hasError;
        }
    });
    
    return Advertising;
}( Backbone, _, jQuery ));
