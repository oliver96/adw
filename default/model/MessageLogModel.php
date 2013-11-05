<?php
class MessageLogModel extends Model {
    // 表名称
    protected $table    = 'message_log';
    // 主键
    protected $prikey   = 'id';
    // 字段
    protected $fields   = array('id', 'mobile', 'code', 'ret_no', 'ret_err', 'dateline');
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
}

?>
