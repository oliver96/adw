<?php
class MemberModel extends Model {
    // 表名称
    protected $table    = 'members';
    // 主键
    protected $prikey   = 'id';
    // 字段
    protected $fields   = array('id', 'name', 'real_name', 'mobile', 'qq', 'email', 
        'province', 'city', 'addr', 'store_id', 'seller_id',
        'invoice_no', 'device', 'hard_addr', 'sim_no', 'status', 'created');
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
    
    public function getID2InvoiceMap() {
        $memberMap = array();
        $memberList = $this->getList(array('field' => 'id, invoice_no'));
        while($memberRow = $memberList->nextRow()) {
            $id = $memberRow->get('id');
            $invoice = $memberRow->get('invoice_no');
            $memberMap[$id] = $invoice;
        }
        return $memberMap;
    }
}
?>
