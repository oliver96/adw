define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
    , 'bundles/_models/advertising'
], function($, _, Backbone) {
    var ad = new Advertising();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , template : _.template($('#ad_form_template').html())
        , events : {
            // 禁用“回车键”，防止意外提交表单
            'keypress #ad_form' : 'disableEnterKeyEvent'
            // 保存“广告主”事件
            , 'click #save_ad_btn' : 'saveAdEvent'
        }
        , initialize : function() {
            this.listenTo(ad, 'change', this.render);
            if(App.params.id > 0) {
                ad.set('id', App.params.id);
                ad.fetch();
            }
            else {
                this.render();
            }
        }
        , render : function() {
            getAdvertisers();
            getMaterials();
            getMembers();
            
            var adInfo = ad.toJSON();
            adInfo.advertisers = App.advertisers;
            adInfo.materials = App.materials;
            adInfo.members = App.members;
            
            this.$('#ad_form').html(this.template(adInfo));
        }
        , disableEnterKeyEvent : function(e) {
            if(e.which == 13) {
                return false;
            }
            return true;
        }
        , saveAdEvent : function(e) {
            // 获取表单所有数据
            var formData = App.Utils.getFormData('ad_form');
            ad.set(formData);
            if(ad.isValid()) {
                ad.save(null, {'success' : function(model) {
                    var status = model.get('status');
                    var errors = model.get('errors');
                    if(status == false && errors && errors.length > 0) {
                        ad.addErrors(model.get('errors'));
                    }
                    else {
                        window.location.href = $('#return_link').attr('href');
                    }
                }});
            }
        }
    });
    
    new MainView();
});
