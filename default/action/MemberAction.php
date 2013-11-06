<?php
import("action.CommonAction");
import("model.MemberModel");
import("model.StoreModel");
import("model.SellerModel");
import("model.LocationModel");

class MemberAction extends CommonAction {
    public function index() {
        $this->output();
    }
    
    public function edit() {
        $this->output();
    }
    
    public function rows() {
        $totalPage  = 0;
        $page       = $this->request->getParameter('page');
        $pageSize   = $this->request->getParameter('pageSize');
        
        if(empty($page) || $page < 0) $page = 1;
        if(empty($pageSize) || $pageSize < 0) $pageSize = 10;
        
        // 映射门店
        $store = new StoreModel();
        $storeMap = $store->getID2NameMap();
        
        // 映射店员
        $seller = new SellerModel();
        $sellerMap = $seller->getID2NameMap();
        
        // 映射地域
        $location = new LocationModel();
        $locMap = $location->getID2NameMap();
        $provMap = $locMap['provMap'];
        $cityMap = $locMap['cityMap'];
        
        // 实例化广告主模型对象
        $member = new MemberModel();
        // 广告主总记录数
        $totalCount = $member->getCount();
        
        $rows = array();
        if($totalCount > 0) {
            // 求出记录总页数
            $totalPage  = ceil($totalCount / $pageSize);
            // 获取广告主列表
            $memberList    = $member->getList(array(
                'order' => '`id` DESC'
            ), $page, $pageSize);
            
            while($row = $memberList->nextRow()) {
                $storeid    = $row->get('store_id');
                $sellerid   = $row->get('seller_id');
                $provCode   = $row->get('province');
                $cityCode   = $row->get('city');
                $rowAry     = $row->toArray();
                $rowAry['store_name'] = isset($storeMap[$storeid]) ? $storeMap[$storeid] : '';
                $rowAry['seller_name'] = isset($sellerMap[$sellerid]) ? $sellerMap[$sellerid] : '';
                $rowAry['province'] = isset($provMap[$provCode]) ? $provMap[$provCode] : '';
                $rowAry['city'] = isset($cityMap[$cityCode]) ? $cityMap[$cityCode] : '';
                $rows[] = $rowAry;
            }
        }
        
        $this->outputJson(array(
            'page'        => $page
            , 'pageSize'    => $pageSize
            , 'totalPage'   => $totalPage
            , 'data'        => $rows
        ));
    }
    
    public function getData() {
        $output     = array();
        $id         = $this->request->getParameter('id');
        if($id > 0) {
            $member   = new MemberModel();
            $tplRow     = $member->getOne(array('id' => $id));
            if($tplRow) {
                $output = $tplRow->toArray();
            }
        }
        $this->outputJson($output);
    }
    
    protected function saveData() {
        $id     = $this->request->getParameter('id');
        $name   = $this->request->getParameter('name');
        $input  = $this->request->getInput();
        
        $member = new MemberModel();
        
        $existRecords = false;
        if($id > 0) {
            $existRecords = $member->getCount(array('id' => "not {$id}", 'name' => $name));
        }
        else {
            $existRecords = $member->getCount(array('name' => $name));
        }
        if($existRecords > 0) {
            $this->outputJson(array(
                'status' => false, 
                'errors' => array(
                    'name' => '存在相同名称的记录'
                )
            ));
        }
        else {
            if($id > 0) {
                $member->update($input, array('id' => $id));
            }
            else {
                $id = $member->insert($input);
            }
            $this->outputJson(array('id' => $id));
        }
    }
    
    public function delete() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $seller = new MemberModel();
            $seller->delete(array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function pause() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $seller = new MemberModel();
            $seller->update(array('status' => 0), array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function valid() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $seller = new MemberModel();
            $seller->update(array('status' => 1), array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function upload() {
        $fileInfo = $_FILES;
        $this->outputJson($fileInfo);
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
    
    public function ajaxGetMembers() {
        $members = array();
        $member = new MemberModel();
        $list = $member->getList(array(
            'field' => 'id, name'
        ));
        while($row = $list->nextRow()) {
            $id = $row->get('id');
            $name = $row->get('name');
            $members[] = array('id' => $id, 'name' => $name);
        }
        $this->outputJson($members);
    }
}

?>