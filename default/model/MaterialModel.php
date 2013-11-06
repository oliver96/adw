<?php
class MaterialModel extends Model {
    // 表名称
    protected $table    = 'materials';
    // 主键
    protected $prikey   = 'id';
    // 字段
    protected $fields   = array('id', 'name', 'adv_id', 'type', 'url', 'size', 'created');
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
    
    public function getID2NameMap() {
        $materialMap = array();
        $materialList = $this->getList(array('field' => 'id, name'));
        while($materialRow = $materialList->nextRow()) {
            $id = $materialRow->get('id');
            $name = $materialRow->get('name');
            $materialMap[$id] = $name;
        }
        return $materialMap;
    }
}
?>
