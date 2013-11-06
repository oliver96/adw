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
    store.urlRoot = App.router.url({
        'm': 'register', 
        'a': 'saveStore'
    });
    var MainPageView = Backbone.View.extend({
        el: $("body")
        , events : {
            'change #province' : 'changeProvinceEvent'
            , 'click #register_button' : 'saveEvent'
        }
        , initialize: function() {
            this.loadProvinces();
        }
        , loadProvinces: function() {
            getProvinces();
            var optionHtml = '';
            for(var i = 0, l = App.provinces.length; i < l; i++) {
                optionHtml = '<option value="' + App.provinces[i].code + '">' + App.provinces[i].name + "</option>";
                $("#province").append(optionHtml);  
            }

            var provCode = $('#province').val();
            this.changeProvince(provCode);
        }
        , changeProvince: function(provCode) {
            getCities(provCode, 1);
            $("#city").empty();
            for(var i = 0, l = App.cities.length; i < l; i++) {
                var optionHtml = '<option value="' + App.cities[i].code + '">' + App.cities[i].name + "</option>";
                $("#city").append(optionHtml);  
            }
        }
        , changeProvinceEvent: function(e) {
            var provCode = $('#province').val();
            this.changeProvince(provCode);
        }
        , saveEvent: function(e) {
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
                        window.location.href = App.router.url({'m' : 'register', 'a' : 'seller'});
                    }
                }});
            }
        }
     });
     new MainPageView();
});
