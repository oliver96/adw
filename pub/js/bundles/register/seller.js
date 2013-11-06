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
    seller.urlRoot = App.router.url({
        'm': 'register', 
        'a': 'saveSeller'
    });
    var MainPageView = Backbone.View.extend({
        el: $("body")
        , events : {
            'click #register_button' : 'saveEvent'
        }
        , initialize: function() {
            getStories();
            var optionHtml = '';
            $("#store_id").empty();
            for(var i = 0, l = App.stories.length; i < l; i++) {
                optionHtml = '<option value="' + App.stories[i].id + '">' + App.stories[i].name + "</option>";
                $("#store_id").append(optionHtml);  
            }
        }
        , saveEvent: function(e) {
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
                        window.location.href = App.router.url({'m' : 'register', 'a' : 'index'});
                    }
                }});
            }
        }
     });
     new MainPageView();
});
