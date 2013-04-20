<?php
class Obj_DBTable_Citys extends Zend_Db_Table_Abstract
{
	protected $_name = 'city';
	protected $_primary = 'city_id';
	 
	
	public function getAllCity(){
		return $this->fetchAll()->toArray();
	}
	public function getCityAutocomplite($city, $country){
	    
	  	$sql = $this->select()
	  	        ->from(array('c' => 'city'), array('city_id', 'country_id', 'cityname' => 'name'))
				->join(array('ca' => 'country'), 'c.country_id = ca.country_id')			    
				->where('c.name LIKE ?', $city.'%');
        if ($country > 0) {
            $sql->where("ca.country_id = ?", $country);
        }
	  	$sql->setIntegrityCheck(false);
	  	
		$result = $this->fetchAll($sql);
		return $result->toArray();
		
	}
	
	public function getAllCityFromPagenator($count, $start){
		$sql = $this->select()
						->from('city')
						->limit($count, $start);
		$sql->setIntegrityCheck(false);
		
		$result = $this->fetchAll($sql)->toArray();
		return $result;
	}
	
	public function getCityAutocompliteForIventor($city, $userid){
		 
		$sql = $this->select()
		->from(array('c' => 'city'), array('city_id', 'country_id', 'cityname' => 'name'))
		->join(array('ivinf' => 'inventorinfo'), 'ivinf.UserId = '.$userid)
		->join(array('cit' => 'city'), 'ivinf.CityId = cit.city_id', array())
		->join(array('ca' => 'country'), 'c.country_id = ca.country_id')
		->where('c.country_id = cit.country_id')
		->where('c.name LIKE ?', $city.'%');
		$sql->setIntegrityCheck(false);
		$result = $this->fetchAll($sql);
		return $result->toArray();
	
	}
	
	
	public function getCountryId($cityid)
	{
	    $cityid = (int)$cityid;
		$sql = $this->select()
				->from('city', array('country_id'))
				->where('city_id = ?', $cityid);
		$sql->setIntegrityCheck(false);
		$row = $this->fetchRow($sql);
		return $row->toArray();
	}
	
	public function getCityAndCountry($cityid)
	{
		$sql = $this->select()
	  	        ->from(array('c' => 'city'), array('city_id', 'country_id', 'cityname' => 'name'))
				->join(array('ca' => 'country'), 'c.country_id = ca.country_id')			    
				->where('c.city_id = ?', $cityid);
	  	$sql->setIntegrityCheck(false);
	  	
		$result = $this->fetchRow($sql);
        if ($result) return $result->toArray();
        else return false;
	}
	
	public function getCity($id){
	    $row = $this->fetchRow('city_id = '.(int)$id);
	    if(!$row){
	    	return;
	    }
	    
	    return $id = $row->toArray();
	}
	
	 public function getId($alias) {
        $select = $this->select()->from($this)->where('city.alias LIKE ?', $alias);
       return intval(@$select->query()->fetchObject()->city_id);
    }
	
	public function getAlias($id) {
        $select = $this->select()->from($this)->where('city.city_id = ?', $id);
       return $select->query()->fetchObject()->alias;
    }
	
		public function isAlias($alias) {
       $select = $this->select()->from($this)->where('city.alias LIKE ?', $alias);
	   $row = $select->query()->fetchObject();
	   if(!$row) {
            return null;
		}	
       return $row->city_id;
    }
	
	public function getName($id) {
        $select = $this->select()->from($this)->where('city.city_id = ?', $id);
       return $select->query()->fetchObject()->name;
    }
	
	public function updateCity($id, $name){
		$data = array('name' => $name);
		$this->update($data, 'city_id = '.(int)$id);
	}
	
}
