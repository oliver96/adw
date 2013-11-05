<?php
class SellerModel extends Model {
    // 表名称
    protected $table    = 'sellers';
    // 主键
    protected $prikey   = 'id';
    // 字段
    protected $fields   = array('id', 'name', 'store_id', 'mobile', 'status', 'created');
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
     * 获取店员映射表
     * 
     * @return type
     */
    public function getID2NameMap() {
        $sellerMap = array();
        $sellerList = $this->getList(array('field' => 'id, name'));
        while($sellerRow = $sellerList->nextRow()) {
            $id = $sellerRow->get('id');
            $name = $sellerRow->get('name');
            $sellerMap[$id] = $name;
        }
        return $sellerMap;
    }
}
?>

