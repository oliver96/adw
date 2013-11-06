<?php
import("action.CommonAction");
import("model.SellerModel");
import("model.StoreModel");

class SellerAction extends CommonAction {
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
        
        // 实例化广告主模型对象
        $seller = new SellerModel();
        // 广告主总记录数
        $totalCount = $seller->getCount();
        
        $rows = array();
        if($totalCount > 0) {
            // 求出记录总页数
            $totalPage  = ceil($totalCount / $pageSize);
            // 获取广告主列表
            $matList    = $seller->getList(array(
                'order' => '`id` DESC'
            ), $page, $pageSize);
            
            while($row = $matList->nextRow()) {
                $storeid    = $row->get('store_id');
                $rowAry     = $row->toArray();
                $rowAry['store_name'] = $storeMap[$storeid];
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
            $seller   = new SellerModel();
            $tplRow     = $seller->getOne(array('id' => $id));
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
        
        $seller = new SellerModel();
        
        $existRecords = false;
        if($id > 0) {
            $existRecords = $seller->getCount(array('id' => "not {$id}", 'name' => $name));
        }
        else {
            $existRecords = $seller->getCount(array('name' => $name));
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
                $seller->update($input, array('id' => $id));
            }
            else {
                $id = $seller->insert($input);
            }
            $this->outputJson(array('id' => $id));
        }
    }
    
    public function delete() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $seller = new SellerModel();
            $seller->delete(array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function pause() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $seller = new SellerModel();
            $seller->update(array('status' => 0), array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    public function valid() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $seller = new SellerModel();
            $seller->update(array('status' => 1), array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    // 通过ajax获取店员列表
    public function ajaxGetSellers() {
        $sellers = array();
        $seller = new SellerModel();
        $list = $seller->getList(array(
            'field' => 'id, name'
        ));
        while($row = $list->nextRow()) {
            $id = $row->get('id');
            $name = $row->get('name');
            $sellers[] = array('id' => $id, 'name' => $name);
        }
        $this->outputJson($sellers);
    }
}

?>