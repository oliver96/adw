define([
    'jquery'
    , 'underscore'
    , 'backbone'
    , 'vendors/bootstrap/bootstrap-fileupload'
    , 'bundles/_public/globals'
    , 'bundles/_public/utils'
    , 'bundles/_models/common'
    , 'bundles/_models/material'
], function($, _, Backbone) {
    App.Utils.loadCss("bootstrap-fileupload");
    var material = new Material();
    
    var MainView = Backbone.View.extend({
        el: $("body")
        , template : _.template($('#mat_form_template').html())
        , events : {
            // 禁用“回车键”，防止意外提交表单
            'keypress #material_form' : 'disableEnterKeyEvent'
            // 保存“广告主”事件
            , 'click #save_material_btn' : 'saveMaterialEvent'
        }
        , initialize : function() {
            this.listenTo(material, 'change', this.render);
            if(App.params.id > 0) {
                material.set('id', App.params.id);
                material.fetch();
            }
            else {
                this.render();
            }
        }
        , render : function() {
            // 获取广告主
            getAdvertisers();
            var materialInfo = material.toJSON();
            materialInfo.advertisers = App.advertisers;
            this.$('#material_form').html(this.template(materialInfo));
        }
        , disableEnterKeyEvent : function(e) {
            if(e.which == 13) {
                return false;
            }
            return true;
        }
        , saveMaterialEvent : function(e) {
            // 获取表单所有数据
            var formData = App.Utils.getFormData('material_form');
            this.uploadFile(formData, function(formData) {
                material.set(formData);
                if(material.isValid()) {
                    material.save(null, {'success' : function(model) {
                        var status = model.get('status');
                        var errors = model.get('errors');
                        if(status == false && errors && errors.length > 0) {
                            material.addErrors(model.get('errors'));
                        }
                        else {
                            window.location.href = $('#return_link').attr('href');
                        }
                    }});
                }
            });
        }
        , uploadFile : function(formData, callback) {
            // 获取文件对象
            var fileObj = document.getElementById("file").files[0]; 
            // 接收上传文件的后台地址 
            var FileController = App.router.url({'m': 'material', 'a': 'upload'}); 
           
            // FormData 对象
            var form = new FormData();
            form.append("id", material.get('id'));  // 可以增加表单数据
            form.append("file", fileObj);           // 文件对象

            // XMLHttpRequest 对象
            var xhr = new XMLHttpRequest();
            xhr.open("post", FileController, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var fileRet = JSON.parse(xhr.responseText);
                    formData['url'] = fileRet['url'];
                    if(typeof callback !== 'undefined') {
                        callback(formData);
                    }
                }
            }
            xhr.send(form);
        }
    });
    
    new MainView();
});