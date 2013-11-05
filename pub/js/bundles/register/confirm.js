define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
], function($, _, Backbone) {
    var MainPageView = Backbone.View.extend({
        el: $("body")
        , events : {
            'click #activate_button' : 'registerEvent'
        }
        , registerEvent : function(e) {
            var url = App.router.url({'m' : 'register', 'a' : 'activate'});
            $.get(url, function(res) {
                if(res.id > 0) {
                    window.location.href = App.router.url({'m' : 'register', 'a' : 'index'});
                }
            });
        }
    });
    new MainPageView();
});
