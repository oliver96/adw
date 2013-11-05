<?php
class StoreModel extends Model {
    // 表名称
    protected $table    = 'stores';
    // 主键
    protected $prikey   = 'id';
    // 字段
    protected $fields   = array('id', 'name', 'province', 'city', 'addr', 'status', 'created');
    /**
     * 获取表名称
     * 
     * @return string 
     */
    public function getTableName() {
        return $this->table;
    }
    
    /**
     * 获主键名称
     * 
     * @return string 
     */
    public function getPrimaryKey() {
        return $this->prikey;
    }
    
    /**
     * 获取门店映射表
     * 
     * @return array
     */
    public function getID2NameMap() {
        $storeMap = array();
        $storeList = $this->getList(array('field' => 'id, name'));
        $storeMap = array();
        while($storeRow = $storeList->nextRow()) {
            $id = $storeRow->get('id');
            $name = $storeRow->get('name');
            $storeMap[$id] = $name;
        }
        return $storeMap;
    }
}
?>
