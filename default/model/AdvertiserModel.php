<?php
class AdvertiserModel extends Model {
    // 表名称
    protected $table    = 'advertisers';
    // 主键
    protected $prikey   = 'id';
    // 字段
    protected $fields   = array('id', 'name', 'indus_id', 'credit', 'contact', 'email', 'detail', 'created');
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
     * 获取广告主ID与名称的对映表
     *
     * @return array
     */
    public function getId2NameMap() {
        $map = array();
        $list = $this->getList(array(
            'field' => array('id', 'name')
        ));
        while($row = $list->nextRow()) {
            $id = $row->get('id');
            $map[$id] = $row->get('name');
        }
        return $map;
    }
}
?>
