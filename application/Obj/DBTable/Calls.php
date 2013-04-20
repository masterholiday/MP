<?php
class Obj_DBTable_Calls extends Zend_Db_Table_Abstract
{
	protected $_name = 'calls';
	protected $_primary = 'ID';
	 
	public function checkEventor($uid, $eid)
	{
        $select = $this->select()->from($this, array("ID"))->where('UserID = ?', $uid)->where('EventorID = ?', $eid)->where('Date = ?', new Zend_Db_Expr('DATE(NOW())'));
        $res = $select->query()->fetchObject();
        if ($res) return true;
        return false;
	}

    public function getClientSearchesCount($uid) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(ID)')))->where('UserID = ?', $uid)->where('UserDeleted = 0');
        return $select->query()->fetchObject()->c;

    }

    public function getEventorSearchesCount($uid) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(ID)')))->where('EventorID = ?', $uid)->where('EventorDeleted = ?', 0);
        return $select->query()->fetchObject()->c;

    }

    public function getClientSearches($uid, $page, $limit) {
        $select = $this->select()->from($this)->where('calls.UserID = ?', $uid)->where('UserDeleted = 0');
        $select->limit($limit, $page * $limit)->order('calls.ID DESC');
        $select->join("inventorinfo", "inventorinfo.UserId = calls.EventorID", array("EvID" => "Id", "CompanyName", "CompanyPhone", "Website", "Image", "Description"));
        $select->join("iventor_services", "iventor_services.Id = calls.EventorServiceID", array("minPrice", "maxPrice"));
        $select->join(array("s1" => "service_categories"), "s1.Id = iventor_services.CategoryId", array("Subcategory" => "CategoryName"));
        $select->join(array("s2" => "service_categories"), "s2.Id = s1.ParentId", array("Category" => "CategoryName"));
        $select->join("city", "iventor_services.CityId = city.city_id", array("CityName" => "name"));
        $select->join("country", "city.country_id = country.country_id", array("CountryName" => "name", "CountryID" => "country_id"));
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

    public function getEventorSearches($uid, $page, $limit) {
        $select = $this->select()->from($this)->where('calls.EventorID = ?', $uid)->where('EventorDeleted = 0');
        $select->limit($limit, $page * $limit)->order('calls.ID DESC');
        $select->join("profile", "profile.UserId = calls.UserID", array("Name"));
        $select->join("iventor_services", "iventor_services.Id = calls.EventorServiceID", array("minPrice", "maxPrice"));
        $select->join(array("s1" => "service_categories"), "s1.Id = iventor_services.CategoryId", array("Subcategory" => "CategoryName"));
        $select->join(array("s2" => "service_categories"), "s2.Id = s1.ParentId", array("Category" => "CategoryName"));
        $select->join("city", "iventor_services.CityId = city.city_id", array("CityName" => "name"));
        $select->join("country", "city.country_id = country.country_id", array("CountryName" => "name", "CountryID" => "country_id"));
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

}
