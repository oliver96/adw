define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
    , 'bundles/_models/member'
], function($, _, Backbone) {
    var member = new Member();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , template : _.template($('#member_form_template').html())
        , events : {
            'change #province' : 'changeProvinceEvent'
            // 保存“广告主”事件
            , 'click #save_member_btn' : 'saveMemberEvent'
        }
        , initialize : function() {
            this.listenTo(member, 'change', this.render);
            if(App.params.id > 0) {
                member.set('id', App.params.id);
                member.fetch();
            }
            else {
                this.render();
            }
        }
        , render : function() {
            loadProvinces();
            getStories();
            getSellers();
            var memberInfo = member.toJSON();
            memberInfo.provinces = App.provinces;
            memberInfo.cities = App.cities;
            memberInfo.stories = App.stories;
            memberInfo.sellers = App.sellers;
            this.$('#member_form').html(this.template(memberInfo));
            
            this.changeProvinceEvent();
        }
        , saveMemberEvent : function(e) {
            // 获取表单所有数据
            var formData = App.Utils.getFormData('member_form');
            
            member.set(formData);
            if(member.isValid()) {
                member.save(null, {'success' : function(model) {
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
