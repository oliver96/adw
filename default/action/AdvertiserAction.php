<?php
import("action.CommonAction");
import("model.AdvertiserModel");
import("model.IndustryModel");

class AdvertiserAction extends CommonAction {
    private $advModel = null;
    
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
        
        // 实例化广告主模型对象
        $advertiser = $this->getAdvModel();
        // 广告主总记录数
        $totalCount = $advertiser->getCount();
        
        $rows = array();
        if($totalCount > 0) {
            // 求出记录总页数
            $totalPage  = ceil($totalCount / $pageSize);
            // 获取广告主列表
            $advList    = $advertiser->getList(array(
                'order' => '`id` DESC'
            ), $page, $pageSize);
                
            // 获取行业映射表
            $indusModel = new IndustryModel();
            $indusMap   = $indusModel->getMap();
            while($row = $advList->nextRow()) {
                $rowAry     = $row->toArray();
                $indusid    = $rowAry['indus_id'];
                if(isset($indusMap[$indusid])) {
                    $rowAry['indus'] = $indusMap[$indusid];
                }
                $rows[] = $rowAry;
            }
        }
        
        $this->outputJson(array(
            'params' => $this->request->getParameters(),
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPage' => $totalPage
             , 'data' => $rows
        ));
    }
    
    protected function getData() {
        $id = $this->request->getParameter('id');
        if($id > 0) {
            $advertiser = $this->getAdvModel();
            $advRow     = $advertiser->getOne(array('id' => $id));
            if($advRow) {
                $advRowAry = $advRow->toArray();
                $this->outputJson($advRowAry);
            }
        }
    }
    
    protected function saveData() {
        $status = 0;
        $id     = $this->request->getParameter('id');
        $name   = $this->request->getParameter('name');
        $input  = $this->request->getInput();
        
        $advertiser = $this->getAdvModel();
        
        $existRecords = false;
        if($id > 0) {
            $existRecords = $advertiser->getCount(array('id' => "not {$id}", 'name' => $name));
        }
        else {
            $existRecords = $advertiser->getCount(array('name' => $name));
        }
        if($existRecords > 0) {
            $this->outputJson(array(
                'status' => false, 
                'errors' => array(
                    'adv_name' => '存在相同名称的记录'
                )
            ));
        }
        else {
            if($id > 0) {
                $advertiser->update($input, array('id' => $id));
            }
            else {
                $id = $advertiser->insert($input);
            }
            $this->outputJson(array('id' => $id));
        }
    }
    
    public function delete() {
        $output = array('success' => false);
        $ids = $this->request->getParameter('ids');
        if(!empty($ids)) {
            $advertiser = new AdvertiserModel();
            $advertiser->delete(array('id' => "IN (" . $ids . ")"));
            $output['success'] = true;
        }
        $this->outputJson($output);
    }
    
    // 获取广告主
    public function ajaxGetAdvertisers() {
        $advertiser = $this->getAdvModel();
        $advList    = $advertiser->getList(array('field' => 'id,name'));
        $rows       = array();
        while($advRow = $advList->nextRow()) {
            $rows[] = $advRow->toArray();
        }
        
        $this->outputJson($rows);
    }
    
    // 获取行业
    public function industries() {
        $industry   = new IndustryModel();
        $indusList  = $industry->getList();
        $rows       = array();
        while($indusRow = $indusList->nextRow()) {
            $rows[] = $indusRow->toArray();
        }
        
        $this->outputJson($rows);
    }
    
    // 获取广告主模型
    private function getAdvModel() {
        if($this->advModel == null) {
            $this->advModel = new AdvertiserModel();
        }
        return $this->advModel;
    }
}
?>
