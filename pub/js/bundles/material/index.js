define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'vendors/backbone/backbone-pcollection'
    , 'vendors/backbone/backbone-paginator'
    , 'bundles/_public/utils'
    , 'bundles/_models/material'
], function($, _, Backbone) {
    var MaterialItemView = Backbone.View.extend({
        tagName : "tr"
        , template : _.template($('#mat_item_template').html())
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
                , url = App.router.url({'m' : 'material', 'a' : 'edit', 'id' : id});
            window.location.href = url;
        }
    });
    

    var MatrialList = Backbone.PaginatedCollection.extend({
        'model': Material
        , 'url': App.router.url({
            'm' : 'material'
            , 'a' : 'rows'
        })
    });
    
    var materialList = new MatrialList();
    
    var Paginator = Backbone.Paginator.extend({
        collection : materialList
        , el : $('.pagination')
    });
    new Paginator();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , events: {
            'click #all_checked': 'checkAllEvent'           // 全选事件
            , 'click #edit_button': 'editEvent'             // 编辑事件
            , 'click #delete_button': 'deleteEvent'         // 删除事件
        }
        , initialize: function() {
            this.listenTo(materialList, 'reset', this.onRenderList);
            materialList.fetch();
        }
        , onRenderList: function() {
            this.$("#material_list").empty();
            if(materialList.length > 0) {
                materialList.each(this.addMaterialRow, this);
            }
        }
        , addMaterialRow: function(model) {
            var view = new MaterialItemView({
                'model' : model
            });
            this.$("#material_list").append(view.render().el);
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
                var url = App.router.url({'m' : 'material', 'a' : 'edit', 'id' : id});
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
                var url = App.router.url({'m' : 'material', 'a' : 'delete'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       materialList.fetch();
                   }
                });
            }
        }
    });

    new MainView();
});
