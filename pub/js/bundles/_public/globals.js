function loadProvinces() {
    getProvinces();
    getCities(App.provinces[0].code);
}
function getProvinces() {
    if(typeof App.provinces == 'undefined') {
        $.ajax({
            'url' : App.router.url({'m' : 'member', 'a' : 'ajaxGetProvince'})
            , 'async' : false
            , 'success' : function(res) {
                App.provinces = res;  
            }
        });
    }
}
function getCities(provCode, nocache) {
    nocache = typeof nocache == 'undefined' ? 0 : nocache;
    if(typeof App.cities == 'undefined' || 1 == nocache) {
        $.ajax({
            'url' : App.router.url({'m' : 'member', 'a' : 'ajaxGetCity'})
            , 'type' : 'POST'
            , 'data' : {'provCode': provCode}
            , 'async' : false
            , 'success' : function(res) {
                App.cities = res;  
            }
        });
    }
}
function getStories() {
    if(typeof App.stories == 'undefined') {
        $.ajax({
            'url' : App.router.url({'m' : 'store', 'a' : 'ajaxGetStories'})
            , 'async' : false
            , 'success' : function(res) {
                App.stories = res;  
            }
        });
    }
}
function getSellers() {
    if(typeof App.sellers == 'undefined') {
        $.ajax({
            'url' : App.router.url({'m' : 'seller', 'a' : 'ajaxGetSellers'})
            , 'async' : false
            , 'success' : function(res) {
                App.sellers = res;  
            }
        });
    }
}
function getAdvertisers() {
    if(typeof App.advertisers == 'undefined') {
        $.ajax({
            'url' : App.router.url({'m' : 'advertiser', 'a' : 'ajaxGetAdvertisers'})
            , 'async' : false
            , 'success' : function(res) {
                App.advertisers = res;  
            }
        });
    }
}
function getMaterials() {
    if(typeof App.materials == 'undefined') {
        $.ajax({
            'url' : App.router.url({'m' : 'material', 'a' : 'ajaxGetMaterials'})
            , 'async' : false
            , 'success' : function(res) {
                App.materials = res;  
            }
        });
    }
}
function getMembers() {
    if(typeof App.members == 'undefined') {
        $.ajax({
            'url' : App.router.url({'m' : 'member', 'a' : 'ajaxGetMembers'})
            , 'async' : false
            , 'success' : function(res) {
                App.members = res;  
            }
        });
    }
}