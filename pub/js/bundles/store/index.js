define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'vendors/backbone/backbone-pcollection'
    , 'vendors/backbone/backbone-paginator'
    , 'bundles/_public/utils'
    , 'bundles/_models/store'
], function($, _, Backbone) {
    var StoreItemView = Backbone.View.extend({
        //列表标签.
        tagName : "tr"
        // 为单个元素缓存模板.
        , template : _.template($('#store_item_template').html())
        // 注册事件
        , events: {
            'click a.edit-link': 'editEvent'
        }

        // StoreView视图监听 model的事件变化,重新渲染
        // **Storeertiser** 和 **StoreView** 成一一对应的关系.
        , initialize : function() {
            this.model.bind('change', this.render, this);
            this.model.bind('destroy', this.remove, this);
        }
        // 重新渲染单条广告主的列表行.
        , render : function() {
            var $el = $(this.el);
            $el.html(this.template(this.model.toJSON()));
            return this;
        }
        , editEvent : function(e) {
            var id = parseInt($(e.target).closest('tr').find('input').val())
                , url = App.router.url({'m' : 'store', 'a' : 'edit', 'id' : id});
            window.location.href = url;
        }
    });
    
    // 创建一个带分页的数据行的集合
    var StoreList = Backbone.PaginatedCollection.extend({
        'model': Store
        , 'url': App.router.url({
            'm' : 'store'
            , 'a' : 'rows'
        })
    });
    
    // 列表对象实例化
    var storeList = new StoreList();
    
    // 分页条对象实例化
    var Paginator = Backbone.Paginator.extend({
        collection : storeList
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
        // 初始化事件
        , initialize: function() {
            // 绑定RESET事件，当storeList数据重置时确发onRenderList方法
            this.listenTo(storeList, 'reset', this.onRenderList);
            this.listenTo(storeList, 'change', this.onRenderList);
            
            // 从服务器获取广告主列表数据
            storeList.fetch();
        }
        // 渲染广告主列表
        , onRenderList: function() {
            this.$("#store_list").empty();
            if(storeList.length > 0) {
                storeList.each(this.addStoreRow, this);
            }
        }
        // 添加一行广告主数据行
        , addStoreRow: function(model) {
            var view = new StoreItemView({
                'model' : model
            });
            this.$("#store_list").append(view.render().el);
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
                var url = App.router.url({'m' : 'store', 'a' : 'edit', 'id' : id});
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
                var url = App.router.url({'m' : 'store', 'a' : 'delete'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       storeList.fetch();
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
                var url = App.router.url({'m' : 'store', 'a' : 'pause'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       storeList.fetch();
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
                var url = App.router.url({'m' : 'store', 'a' : 'valid'});
                $.post(url, {'ids': ids}, function(res) {
                   if(res.success) {
                       storeList.fetch();
                   }
                });
            }
        }
    });

    new MainView();
});