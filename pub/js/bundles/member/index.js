define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'vendors/backbone/backbone-pcollection'
    , 'vendors/backbone/backbone-paginator'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/member'
], function($, _, Backbone) {
    var MemberItemView = Backbone.View.extend({
        tagName : "tr"
        , template : _.template($('#member_item_template').html())
        , events: {
            'click a.edit-link': 'editEvent'
        }

        , initialize : function() {
            this.model.bind('change', this.render, this);
            this.model.bind('destroy', this.remove, this);
        }
        , render : function() {
            var $el = $(this.el);
            $el.html(this.template(this.model.toJSON()));
            return this;
        }
        , editEvent : function(e) {
            var id = parseInt($(e.target).closest('tr').find('input').val())
                , url = App.router.url({'m' : 'member', 'a' : 'edit', 'id' : id});
            window.location.href = url;
        }
    });
    

    var MemberList = Backbone.PaginatedCollection.extend({
        'model': Member
        , 'url': App.router.url({
            'm' : 'member'
            , 'a' : 'rows'
        })
    });
    
    var memberList = new MemberList();
    
    var Paginator = Backbone.Paginator.extend({
        collection : memberList
        , el : $('.pagination')
    });
    new Paginator();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , events: {
            'click #all_checked': 'checkAllEvent'           // 全选事件
            , 'click #edit_button': 'editEvent'             // 编辑事件
            , 'click #delete_button': 'deleteEvent'         // 删除事件
            , 'click #pause_button': 'pauseEvent'           // 暂停事件
            , 'click #valid_button': 'validEvent'           // 有效事件
        }
        , initialize: function() {
            this.listenTo(memberList, 'reset', this.onRenderList);
            memberList.fetch();
        }
        , onRenderList: function() {
            this.$("#member_list").empty();
            if(memberList.length > 0) {
                memberList.each(this.addMemberRow, this);
            }
        }
        , addMemberRow: function(model) {
            var view = new MemberItemView({
                'model' : model
            });
            this.$("#member_list").append(view.render().el);
        }
        , checkAllEvent : function(e) {
            var checked = $('#all_checked')[0].checked;
            $('input[name=row_checked]').each(function() {
                $(this)[0].checked = checked;
            });
        }
        , editEvent : function(e) {
            var checkeds = $('input[name=row_checked]:checked');
            if(checkeds.length > 0) {
                var id = checkeds[0].value;
                var url = App.router.url({'m' : 'member', 'a' : 'edit', 'id' : id});
                window.location.href = url;
            }
        }
        , deleteEvent : function(e) {
            var checkedids = [];
            $('input[name=row_checked]:checked').each(function() {
                checkedids.push($(this)[0].value);
            });
            if(checkedids.length > 0) {
                var ids = checkedids.join(',');
                var url = App.router.url({'m' : 'member', 'a' : 'delete'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       memberList.fetch();
                   }
                });
            }
        }
        , pauseEvent: function(e) {
            var checkedids = [];
            $('input[name=row_checked]:checked').each(function() {
                checkedids.push($(this)[0].value);
            });
            if(checkedids.length > 0) {
                var ids = checkedids.join(',');
                var url = App.router.url({'m' : 'member', 'a' : 'pause'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       memberList.fetch();
                   }
                });
            }
        }
        , validEvent: function(e) {
            var checkedids = [];
            $('input[name=row_checked]:checked').each(function() {
                checkedids.push($(this)[0].value);
            });
            if(checkedids.length > 0) {
                var ids = checkedids.join(',');
                var url = App.router.url({'m' : 'member', 'a' : 'valid'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       memberList.fetch();
                   }
                });
            }
        }
    });

    new MainView();
});