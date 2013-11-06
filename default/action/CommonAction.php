<?php
class CommonAction extends Action {
    public function initAction(& $context, & $request, & $response) {
        parent::initAction($context, $request, $response);

        $navs = array(
            array(
                'header' => '门店管理',
                'childs' => array(
                    array(
                        'text' => '门店',
                        'icon' => 'icon-shopping-cart',
                        'url' => Core::url(array('m' => 'store', 'a' => 'index'))
                    ),
                    array(
                        'text' => '店员',
                        'icon' => 'icon-th-list',
                        'url' => Core::url(array('m' => 'seller', 'a' => 'index'))
                    )
                )
            ),
            array(
                'header' => '保卡管理',
                'childs' => array(
                    array(
                        'text' => '保卡',
                        'icon' => 'icon-resize-full',
                        'url' => Core::url(array('m' => 'member', 'a' => 'index'))
                    )
                )
            ),
            array(
                'header' => '广告',
                'childs' => array(
                    array(
                        'text' => '广告主',
                        'icon' => 'icon-home',
                        'url' => Core::url(array('m' => 'advertiser', 'a' => 'index'))
                    ),
                    array(
                        'text' => '素材',
                        'icon' => 'icon-th-list',
                        'url' => Core::url(array('m' => 'material', 'a' => 'index'))
                    ),
                    array(
                        'text' => '广告',
                        'icon' => 'icon-picture',
                        'url' => Core::url(array('m' => 'advertising', 'a' => 'index'))
                    )
                )
            ),
            array(
                'header' => '统计',
                'childs' => array(
                    array(
                        'text' => '上网流量',
                        'icon' => 'icon-retweet',
                        'url' => Core::url(array('m' => 'statistics', 'a' => 'flow'))
                    ),
                    array(
                        'text' => '广告数据',
                        'icon' => 'icon-th-large',
                        'url' => Core::url(array('m' => 'statistics', 'a' => 'ad'))
                    )
                    ,
                    array(
                        'text' => '短信记录',
                        'icon' => 'icon-th-large',
                        'url' => Core::url(array('m' => 'statistics', 'a' => 'sms'))
                    )
                )
            )
        );

        $this->assign('navs', $navs);
        
        $params = $this->request->getParameters();
        if(empty($params)) $params = array();
        $this->assign('params', String::jsonEncode($params));
        
        file_put_contents("/tmp/adsys.log", $_SERVER['PHP_SELF'] . "\r\n", FILE_APPEND);
    }
    
    // 每个模块的列表页， 可以根据不同业务逻辑进行重载方法
    public function index() {
        $this->output();
    }
    
    // 每个模块的编辑页， 可以根据不同业务逻辑进行重载方法
    public function edit() {
        $this->output();
    }
    
    // 公共“添加”，“删除”，“编辑”的接口代理
    public function api() {
        $method = $this->request->getMethod();
        switch(strtoupper($method)) {
            case 'GET' :
                $this->getData();
            break;
            case 'PUT' :
                $this->saveData();
            break;
        }
    }
    
    // 获取省份列表
    public function ajaxGetProvince() {
        $locationModel = new LocationModel();
        $list = $locationModel->getList(array(
            'field' => "`location2` AS ProvCode, `cn` AS ProvName"
            , 'where' => array('location1' => 'CN', 'location2' => "not '00'")
            , 'group' => "ProvCode"
        ));
        
        $provinces = array();
        while($row = $list->nextRow()) {
            $code = $row->get('ProvCode');
            $name = $row->get('ProvName');
            $provinces[] = array('code' => $code, 'name' => $name);
        }
        
        $this->outputJson($provinces);
    }
    
    // 获取城市列表
    public function ajaxGetCity() {
        $cities = array();
        $provCode = $this->request->getParameter('provCode');
        if($provCode) {
            $locationModel = new LocationModel();
            $list = $locationModel->getList(array(
                'field' => "`location3` AS CityCode, `cn_city` AS CityName"
                , 'where' => array('location1' => 'CN', 'location2' => $provCode, 'location3' => "not '00'")
                , 'group' => "CityCode"
            ));
            
            while($row = $list->nextRow()) {
                $code = $row->get('CityCode');
                $name = $row->get('CityName');
                $cities[] = array('code' => $code, 'name' => $name);
            }
        }
        $this->outputJson($cities);
    }
}

?>
