define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
    , 'bundles/_models/seller'
], function($, _, Backbone) {
    var seller = new Seller();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , template : _.template($('#seller_form_template').html())
        , events : {
            // 禁用“回车键”，防止意外提交表单
            'keypress #seller_form' : 'disableEnterKeyEvent'
            // 保存“广告主”事件
            , 'click #save_seller_btn' : 'saveSellerEvent'
        }
        , initialize : function() {
            this.listenTo(seller, 'change', this.render);
            if(App.params.id > 0) {
                seller.set('id', App.params.id);
                seller.fetch();
            }
            else {
                this.render();
            }
        }
        , render : function() {
            getStories();
            var sellerInfo = seller.toJSON();
            sellerInfo.stories = App.stories;
            this.$('#seller_form').html(this.template(sellerInfo));
        }
        , disableEnterKeyEvent : function(e) {
            if(e.which == 13) {
                return false;
            }
            return true;
        }
        , saveSellerEvent : function(e) {
            // 获取表单所有数据
            var formData = App.Utils.getFormData('seller_form');
            
            seller.set(formData);
            if(seller.isValid()) {
                seller.save(null, {'success' : function(model) {
                    var status = model.get('status');
                    var errors = model.get('errors');
                    if(status == false && errors && errors.length > 0) {
                        seller.addErrors(model.get('errors'));
                    }
                    else {
                        window.location.href = $('#return_link').attr('href');
                    }
                }});
            }
        }
        , resetStoreEvent : function() {
            
        }
    });
    
    new MainView();
});