<?php
import("action.CommonAction");
import("model.StoreModel");
import("model.LocationModel");

class StoreAction extends CommonAction {
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
        
        // 映射地域
        $location = new LocationModel();
        $locMap = $location->getID2NameMap();
        $provMap = $locMap['provMap'];
        $cityMap = $locMap['cityMap'];
        
        // 实例化广告主模型对象
        $store = new StoreModel();
        // 广告主总记录数
        $totalCount = $store->getCount();
        
        $rows = array();
        if($totalCount > 0) {
            // 求出记录总页数
            $totalPage  = ceil($totalCount / $pageSize);
            // 获取广告主列表
            $matList    = $store->getList(array(
                'order' => '`id` DESC'
            ), $page, $pageSize);
            
            while($row = $matList->nextRow()) {
                $rowAry     = $row->toArray();
                $provCode   = $row->get('province');
                $cityCode   = $row->get('city');
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
            $store   = new StoreModel();
            $tplRow     = $store->getOne(array('id' => $id));
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
        
        $input['created'] = date('Y-m-d H:i:s');
        
        $store = new StoreModel();
        
        $existRecords = false;
        if($id > 0) {
            $existRecords = $store->getCount(array('id' => "not {$id}", 'name' => $name));
        }
        else {
            $existRecords = $store->getCount(array('name' => $name));
        }
        if($existRecords > 0) {
            $this->outputJson(array(
                'status' => false, 
                'errors' => array(
                    'mat_name' => '存在相同名称的记录'
                )
            ));
        }
        else {
            if($id > 0) {
                $store->update($input, array('id' => $id));
            }
            else {
                $id = $store->insert($input);
            }
            $this->outputJson(array('id' => $id));
        }
    }
    
    public function delete() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $store = new StoreModel();
            $store->delete(array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function pause() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $store = new StoreModel();
            $store->update(array('status' => 0), array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function valid() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $store = new StoreModel();
            $store->update(array('status' => 1), array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    // 通过ajax获取门店列表
    public function ajaxGetStories() {
        $stories = array();
        $store = new StoreModel();
        $list = $store->getList(array(
            'field' => 'id, name'
        ));
        while($row = $list->nextRow()) {
            $id = $row->get('id');
            $name = $row->get('name');
            $stories[] = array('id' => $id, 'name' => $name);
        }
        $this->outputJson($stories);
    }
}

?>