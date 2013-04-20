<?php
class Obj_DBTable_IventorServices extends Zend_Db_Table_Abstract
{

	protected $_name = 'iventor_services';
	protected $_primary = 'Id';
	 
	public function getIventorServices($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		return $row->toArray();
	}
	
	public function getAllIventorServices()
	{
		$row = $this->fetchAll();
		return $row->toArray();
	}
	
		public function getAllCitiesByIventorId($cat)
	{
		$cat = (int)$cat;
		$sql = $this->select()
						->distinct()
						->from(array('iventor_services'), array('CityId'))
						//->from('iventor_services')
						->where('CategoryId = ' . $cat);
		$row = $this->fetchAll($sql);
		return $row->toArray();
	}
	
			public function getAllUniqueCategories()
	{

		$sql = $this->select()
						->distinct()
						->from(array('iventor_services'), array('CategoryId'));
		$row = $this->fetchAll($sql);
		return $row->toArray();
	}
	
	public function issetService($ivent_id, $cat_id, $city_id, $id){
        if ($id > 0) return $this->fetchAll('IventorId ='.(int)$ivent_id.' AND CategoryId = '.(int)$cat_id.' AND CityId = '.(int)$city_id.' AND Id <> '.intval($id))->toArray();
        else return $this->fetchAll('IventorId ='.(int)$ivent_id.' AND CategoryId = '.(int)$cat_id.' AND CityId = '.(int)$city_id)->toArray();
	}

	public function getIventorServicesByIventorId($iventorid)
	{
		$iventorid = (int)$iventorid;
		$row = $this->fetchRow('IventorId = ' . $iventorid);
        if ($row) return $row->toArray();
	}
	
	public function getAllIventorServicesByIventorId($iventorid)
	{
		$iventorid = (int)$iventorid;
		$sql = $this->select()
						->from('iventor_services')
						->where('IventorId = ' . $iventorid)
						->group('CategoryId')->group('CityId')->order('Id');
		$row = $this->fetchAll($sql);
		return $row->toArray();
	}
	
	public function getIventorServicesByCategoryId($categoryid)
	{
		$categoryid = (int)$categoryid;
		$row = $this->fetchRow('CategoryId = ' . $categoryid);
		return $row->toArray();
	}
	
	public function getIventorServicesByCityId($cityid)
	{
		$cityid = (int)$cityid;
		$row = $this->fetchRow('CityId = ' . $cityid);
		return $row->toArray();
	}
	
	public function getIventorServicesByPriceId($priceid)
	{
		$priceid = (int)$priceid;
		$row = $this->fetchRow('PriceId = ' . $priceid);
		return $row->toArray();
	}
	
	public function addIventorServices($iventorid, $categoryid, $cityid, $priceid)
	{
		$data = array(
		        'IventorId' => $iventorid,
		        'CategoryId' => $categoryid,
		        'CityId' => $cityid,
		        'PriceId' => $priceid
		        );
		$id = $this->insert($data);
		return $id;
		}
	 
	public  function updateIventorServices($id, $iventorid, $categoryid, $cityid, $priceid)
	{
		$data = array(
		        'IventorId' => $iventorid,
		        'CategoryId' => $categoryid,
		        'CityId' => $cityid,
		        'PriceId' => $priceid
		        );
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deleteIventorServices($id, $evid = 0)
	{
		$this->delete('Id = ' . (int)$id . " AND IventorId = ".$evid);
	}
	
	public function delAllIventServByIventId($userid){
		$this->delete('IventorId = '.(int)$userid);
	}
	
	public function searches($cat, $price, $cityid, $date){
	   
	    $sql = $this->select()
	    				->from(array('i' => 'iventor_services'))
                        ->join(array('inf' => 'inventorinfo'), 'inf.UserId = i.IventorId', array())
	    				->joinLeft(array('m' => 'missing'), 'i.IventorId = m.IventorId AND m.Date = "'.$date.'"', array('CalID' => 'Id'))
	    				->where('i.CategoryId = ?',$cat)
	    				->where('inf.Active = 1')
	    				->where('i.CityId = ?',$cityid)
	    				->where('i.PriceId = ?',$price);
        $sql->having("ISNULL(CalID)");
        $sql->order('inf.TotalRequests ASC')->limit(5);
	    
	    $sql->setIntegrityCheck(false);
	    $result = $this->fetchAll($sql);
		return $result->toArray();
	}

    public function findEventors($city, $category, $min, $max, $uid) {
        $select = $this->select()->from($this, array('IventorId'));
        $select->join("users", "users.Id = iventor_services.IventorId", array());
        $select->join("inventorinfo", "inventorinfo.UserId = users.Id", array('CompanyPhone', 'Email'));
        $select->where("users.Active = 1");
        $select->where("inventorinfo.Premium = 1");
        $select->where("iventor_services.CityId = ?", (int) $city);
        $select->where("iventor_services.CategoryId = ?", (int) $category);
		$select->where("iventor_services.maxPrice >= ".intval($min)." AND iventor_services.minPrice <= ".intval($max));
        /*$select->where("(iventor_services.minPrice <= ".intval($min)." AND iventor_services.maxPrice >= ".intval($min).") OR (iventor_services.minPrice <= ".intval($max)." AND iventor_services.maxPrice >= ".intval($max).")");*/
        $select->joinLeft("service_searches", "service_searches.EventorID = iventor_services.IventorId AND service_searches.UserID = ".intval($uid)." AND service_searches.SearchDate = DATE(NOW())", array("prevSCount" => new Zend_Db_Expr('COUNT(service_searches.Id)')));
        $select->group("iventor_services.IventorId");
        $select->order("inventorinfo.TotalRequests ASC")->limit(5);
        $select->setIntegrityCheck(false);
        //echo $select->__toString();
        //die();
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCategorySearchesCount($category, $city, $min, $max) {
        $select = $this->select()->from($this, array('Id'))->where('CategoryId = ?', $category);
        //$select->group('IventorId');
        if ($city > 0) {
            $select->where("CityId = ?", $city);
        }
        if ($min > 0) {
            $select->where("minPrice <= ?", $min);
        }
        if ($max > 0) {
            $select->where("maxPrice >= ?", $max);
        }
        $select2 = $this->select()->from($select, array('c' => new Zend_Db_Expr('COUNT(*)')));
        $select2->setIntegrityCheck(false);

        return $select2->query()->fetchObject()->c;
    }

    public function getCategorySearches($category, $city, $min, $max, $uid) {
        $select = $this->select()->from($this, array('EventorServiceID' => 'Id', 'CityId', 'minPrice', 'maxPrice'));
        $select->where('iventor_services.CategoryId = ?', $category);
        if ($city > 0) {
            $select->where("iventor_services.CityId = ?", $city);
        }
        if ($min > 0 && $max > 0) {
            /*$select->where("(iventor_services.minPrice <= ".intval($min)." AND iventor_services.maxPrice >= ".intval($min).") OR (iventor_services.minPrice <= ".intval($max)." AND iventor_services.maxPrice >= ".intval($max).")");*/
			
			$select->where("iventor_services.maxPrice >= ".intval($min)." AND iventor_services.minPrice <= ".intval($max));

        }
        $select->join("inventorinfo", "inventorinfo.UserId = iventor_services.IventorId", array("EventorUserID" => "UserId", "CompanyName", "CompanyPhone", "Description", "Image", "Premium", "EventorID" => "Id"));
        $select->join("users", "users.Id = inventorinfo.UserId", array());

        $select->order("inventorinfo.Premium DESC")->order(new Zend_Db_Expr('RAND()'));
        $select->setIntegrityCheck(false);

        $select->join("city", "city.city_id = iventor_services.CityId", array("CityName" => "name", "CountryID" => "country_id"));


        $select->joinLeft("calls", "calls.EventorID = iventor_services.IventorId AND calls.UserID = ".intval($uid)." AND calls.Date = DATE(NOW())", array("CallID" => "ID"));
        $select->joinLeft("starred", "starred.EventorID = iventor_services.IventorId AND starred.UserID = ".intval($uid), array("StarID" => "ID"));


        //echo $select->__toString();
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

}
