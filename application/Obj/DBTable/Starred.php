<?php
class Obj_DBTable_Starred extends Zend_Db_Table_Abstract
{
	protected $_name = 'starred';
	protected $_primary = 'ID';
	 
	public function checkEventor($uid, $eid)
	{
        $select = $this->select()->from($this, array("ID"))->where('UserID = ?', $uid)->where('EventorID = ?', $eid);
        $res = $select->query()->fetchObject();
        if ($res) return true;
        return false;
	}

    public function removeEventor($uid, $eid) {
        $this->delete("UserID = ".intval($uid)." AND EventorID = ".intval($eid));
    }

    public function getUserStarredCount($uid) {
        return $this->select()->from($this, array("c" => new Zend_Db_Expr("COUNT(ID)")))->where("UserID = ?", (int) $uid)->query()->fetchObject()->c;
    }

    public function getUserStarred($uid, $page, $perpage) {
        $select = $this->select()->from($this, array("StarID" => "ID"))->where("starred.UserID = ?", (int) $uid);
        $select->limit($perpage, $perpage*$page);
        $select->join("iventor_services", "iventor_services.Id = starred.EventorServiceID", array("minPrice", "maxPrice","EventorServiceID"=>"Id"));
        $select->join("inventorinfo", "inventorinfo.UserId = iventor_services.IventorId", array("EventorUserID" => "UserId", "CompanyName", "CompanyPhone", "Description", "Image", "Premium",  "EventorID" => "Id"));

        $select->order("starred.ID DESC");
        $select->setIntegrityCheck(false);

        $select->join("city", "city.city_id = iventor_services.CityId", array("CityName" => "name", "CountryID" => "country_id"));


        $select->joinLeft("calls", "calls.EventorID = iventor_services.IventorId AND calls.UserID = ".intval($uid)." AND calls.Date = DATE(NOW())", array("CallID" => "ID"));
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

}
