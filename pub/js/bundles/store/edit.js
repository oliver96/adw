define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
    , 'bundles/_models/store'
], function($, _, Backbone) {
    var store = new Store();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , template : _.template($('#store_form_template').html())
        , events : {
            // 禁用“回车键”，防止意外提交表单
            'keypress #store_form' : 'disableEnterKeyEvent'
            // 点击"省份"联动效果
            , 'change #province' : 'changeProvinceEvent'
            // 保存“广告主”事件
            , 'click #save_store_btn' : 'saveStoreEvent'
        }
        , initialize : function() {
            this.listenTo(store, 'change', this.render);
            if(App.params.id > 0) {
                store.set('id', App.params.id);
                store.fetch();
            }
            else {
                this.render();
            }
        }
        , render : function() {
            loadProvinces();
            var storeInfo = store.toJSON();
            storeInfo.provinces = App.provinces;
            storeInfo.cities = App.cities;
            this.$('#store_form').html(this.template(storeInfo));
            var provCode = $('#province').val();
            getCities(provCode);
        }
        , disableEnterKeyEvent : function(e) {
            if(e.which == 13) {
                return false;
            }
            return true;
        }
        , saveStoreEvent : function(e) {
            // 获取表单所有数据
            var formData = App.Utils.getFormData('store_form');
            
            store.set(formData);
            if(store.isValid()) {
                store.save(null, {'success' : function(model) {
                    var status = model.get('status');
                    var errors = model.get('errors');
                    if(status == false && errors && errors.length > 0) {
                        store.addErrors(model.get('errors'));
                    }
                    else {
                        window.location.href = $('#return_link').attr('href');
                    }
                }});
            }
        }
        , resetStoreEvent : function() {
            
        }
        , changeProvinceEvent: function(e) {
            var provCode = $('#province').val();
            getCities(provCode, 1);
            $("#city").empty();
            for(var i = 0, l = App.cities.length; i < l; i++) {
                var optionHtml = '<option value="' + App.cities[i].code + '">' + App.cities[i].name + "</option>";
                $("#city").append(optionHtml);  
            }
        }
    });
    
    new MainView();
});