<?php
class Obj_DBTable_Searches extends Zend_Db_Table_Abstract
{
	protected $_name = 'searches';
	protected $_primary = 'Id';
	 
	public function getSearches($id)
	{
	    $id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllSearches()
	{
		$row = $this->fetchAll();
		return $row->toArray();
	}
	
	public function addSerchUserId($id, $userid){
	    $data = array(
	    		'UserId' => $userid,
	    		);
	    $this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function addSearches($date, $cityid, $activityname, $personcunt, $phone, $address, $userid = 0)
	{
	    $data = array(
		        'Date' => $date,
		        'CityId' => $cityid,
		        'ActivityName' => $activityname,
		        'PersonsCount' => $personcunt,
		        'Phone' => $phone,
		        'Address' => $address,
	            'UserId' => $userid,
	            'SearchDate' => new Zend_Db_Expr('NOW()')
		        );
		$id = $this->insert($data);
		return $id;
		
		}
	 
	public  function updateSearches($id, $date, $cityid, $activityname, $personcunt, $phone, $address)
	{
		$data = array(
				'Date' => $date,
		        'CityId' => $cityid,
		        'ActivityName' => $activityname,
		        'PersonsCount' => $personcunt,
		        'Phone' => $phone,
		        'Address' => $address,
		        'SearchDate' => new Zend_Db_Expr('NOW()')
		);
		$this->update($data, 'Id = ' . (int)$id);
	}
	
	public function countViewSerch($userid){
	    $date = date('Y-m-d');
	    $sql = "SELECT * FROM `searches` WHERE `UserId` = $userid AND `SearchDate` LIKE '$date%'";
	    $row = $this->getAdapter()->fetchAll($sql);
	    return count($row);
	}

	public function getAllSearchByNowDay(){
		$date = date('Y-m-d');
		$sql = "SELECT `Id` FROM `searches` WHERE `SearchDate` = '$date' ORDER BY Id";
		$row = $this->getAdapter()->fetchAll($sql);
		return $row;
	}
	
	public function getCuntUserSearch($user_id){
		return $this->fetchAll('UserId ='.(int)$user_id);
	}
	
	public function getAllUserSerch($userid,  $start = 0, $count = 5){
		$sql = "SELECT `searches`.*, city.country_id FROM `searches`, `city` WHERE `city`.city_id = `searches`.CityId AND `searches`.`UserId` = $userid ORDER BY `searches`.`SearchDate` DESC LIMIT ".$start.",".$count."";
		
		$row = $this->getAdapter()->fetchAll($sql);
		return $row;
	}
	
	public function deleteSearches($id)
	{
		$this->delete('Id = ' . (int)$id);
	}

    public function getEventorSearchesCount($uid, $catalog) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(searches.Id)')))->where('categorySearch = ?', $catalog);
        $select->join("service_searches", "service_searches.SearchID = searches.Id", array());
        $select->where("service_searches.EventorDeleted = 0")->where("service_searches.EventorID = ?", $uid);
        $select->setIntegrityCheck(false);
        return $select->query()->fetchObject()->c;
    }

    public function getClientSearchesCount($uid, $catalog) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('UserId = ?', $uid)->where('categorySearch = ?', $catalog)->where('UserDeleted = 0');
        return $select->query()->fetchObject()->c;
    }

    public function getClientSearches($uid, $catalog, $page, $limit) {
        $select = $this->select()->from($this, array('Id', 'Date', 'ActivityName'))->where('searches.UserId = ?', $uid)->where('categorySearch = ?', $catalog)->where('UserDeleted = 0');
        $select->limit($limit, $page * $limit)->order('searches.SearchDate DESC');
        $select->join("city", "searches.CityId = city.city_id", array("CityName" => "name"));
        $select->join("country", "city.country_id = country.country_id", array("CountryName" => "name", "CountryID" => "country_id"));
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

    public function getEventorSearches($uid, $catalog, $page, $limit) {
        $select = $this->select()->from($this)->where('searches.categorySearch = ?', $catalog);
        $select->limit($limit, $page * $limit)->order('searches.SearchDate DESC');
        $select->join("service_searches", "service_searches.SearchID = searches.Id", array("DID" => "Id", "Info", "minPrice", "maxPrice"));
        $select->join(array("sc1" => "service_categories"), "service_searches.CategoriesId = sc1.Id", array("SubCategory" => "CategoryName"));
        $select->join(array("sc2" => "service_categories"), "sc2.Id = sc1.ParentId", array("Category" => "CategoryName"));
        $select->join("profile", "searches.UserId = profile.UserId", array("Email", "Name"));
        $select->join("city", "searches.CityId = city.city_id", array("CityName" => "name"));
        $select->join("country", "city.country_id = country.country_id", array("CountryName" => "name", "CountryID" => "country_id"));
        $select->where('service_searches.EventorID = ?', $uid);
        $select->where('service_searches.EventorDeleted = 0');
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }
}
