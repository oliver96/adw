<?php
class LocationModel extends Model {
    protected $table    = 'locations';
    protected $prikey   = 'id';
    protected $fields   = array('ID', 'location1', 'location2', 'location3', 
        'country', 'cn', 'cn_city');

    public function getTableName() {
        return $this->table;
    }

    public function getPrimaryKey() {
        return $this->prikey;
    }
    
    public function getID2NameMap() {
        $provMap = array();
        $cityMap = array();
        
        $locationList = $this->getList(array(
            'field' => "`location2` AS ProvCode, `cn` AS ProvName, `location3` AS CityCode, `cn_city` AS CityName"
            , 'where' => array('location1' => 'CN', 'location2' => "not '00'", 'location3' => "not '00'")
            , 'group' => "ProvCode, CityCode"
        ));
        
        while($locationRow = $locationList->nextRow()) {
            $provCode = $locationRow->get('ProvCode');
            $provName = $locationRow->get('ProvName');
            $cityCode = $locationRow->get('CityCode');
            $cityName = $locationRow->get('CityName');
            $provMap[$provCode] = $provName;
            $cityMap[$cityCode] = $cityName;
        }
        
        return array('provMap' => $provMap, 'cityMap' => $cityMap);
    }
}
?>

