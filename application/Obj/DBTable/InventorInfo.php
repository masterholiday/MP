<?php
class Obj_DBTable_InventorInfo extends Zend_Db_Table_Abstract
{
	protected $_name = 'inventorinfo';
	protected $_primary = 'Id';
	 
	public function getInventorInfo($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}

	public function getIventorsList()
	{
        $select = $this->select()->from($this, array("EventorID" => "Id", "Premium", "Description", "PremiumUntil", "Image", "CompanyName", "Email", "PremiumDays" => new Zend_Db_Expr('DATEDIFF(PremiumUntil, NOW())')))->order('inventorinfo.Id DESC');
        $select->join("users", "users.Id = inventorinfo.UserId", array("UserID" => "Id", "Active", "RegistrationDate"));
        $select->join("city", "city.city_id = inventorinfo.CityId", array("CityName" => "name"));
        $select->join("country", "country.country_id = city.country_id", array("CountryName" => "name"));
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
	}
	
		public function getFullIventorsList()
	{
        $select = $this->select()->from($this)->order('inventorinfo.Id DESC');
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
	}
	
	public function getLastParters($image = true){
	    if($image){
			$sql = $this->select()
							->from('users', array())
							->where('users.UserType = 2')
							->order('RegistrationDate DESC')
							->join('inventorinfo', 'inventorinfo.UserId = users.Id AND inventorinfo.Image <> ""')
							->limit(6, 0);
			$sql->setIntegrityCheck(false);
		return $this->fetchAll($sql)->toArray();
	    }else{
	        $sql = $this->select()
	        ->from('users', array())
	        ->where('users.UserType = 2')
	        ->order('RegistrationDate DESC')
	        ->join('inventorinfo', 'inventorinfo.UserId = users.Id')
	        ->limit(6, 0);
	        
	        $sql->setIntegrityCheck(false);
	        return $this->fetchAll($sql)->toArray();
	    }
	}

    public function getIventorInformation($userid) {
        $select = $this->select()->from($this)->where('inventorinfo.UserId = ?', intval($userid));
        $select->join('users', 'users.Id = inventorinfo.UserId', array('Email'));
        $select->join('city', 'city.city_id = inventorinfo.CityId', array('CityName' => 'name'));
        $select->join('country', 'country.country_id = city.country_id', array('CountryName' => 'name', 'CountryID' => 'country_id'));
        //$select->join('services_prices', 'services_prices.CountryId = city.country_id', array('minBallance' => new Zend_Db_Expr('MIN(Gain)')));
        //$select->joinLeft('missing', 'missing.IventorId = inventorinfo.UserId AND missing.Date = '."'".date('Y-m-d')."'", array('Inactive' => 'Id'));
        $select->setIntegrityCheck(false);
        return $select->query()->fetchObject();
    }

    public function getIventorUserID($id) {
        $select = $this->select()->from($this, array('UserId'))->where('Id = ?', intval($id));
        return intval(@$select->query()->fetchObject()->UserId);
    }
		
	public function getInventorInfoByUserId($userid)
	{
		$userid = (int)$userid;
		$row = $this->fetchRow('UserId = ' . $userid);
		if(!$row) {
			return;
		}
		return $row->toArray();
	}
	
	public function getInventorInfoForSearch($userid)
	{
        $select = $this->select()->from($this)->where('inventorinfo.UserId = ?', intval($userid));
        $select->join('users', 'users.Id = inventorinfo.UserId', array('Email'));
        $select->join('city', 'city.city_id = inventorinfo.CityId', array());
        $select->join('services_prices', 'services_prices.CountryId = city.country_id', array('minBallance' => new Zend_Db_Expr('MIN(Gain)')));
        $select->setIntegrityCheck(false);
        $r = $this->fetchRow($select);
        return $r->toArray();
	}

	public function addInventorInfo($userid, $companyname, $phone, $responsibleperson, $image,  $description, $cityid, $balance, $min_req = 0)
	{
	   
		$data = array(
		        'UserId' => $userid,
		        'CompanyName' => $companyname,
		        'CompanyPhone' => $phone,
		        'ResponsiblePerson' => $responsibleperson,
		        'Image' => $image,
		        'Description' => $description,
		        'CityId' => $cityid,
		        'Balance' => $balance,
		        'TotalRequests' => $min_req
		        );
		
		$id = $this->insert($data);
		return $id;
		}
		
	public function  getMinTotalRequest(){
		$select = $this->select()->from($this, array('m' => new Zend_Db_Expr('MIN(TotalRequests)')));
		$select->setIntegrityCheck(false);
		$row = $this->fetchRow($select);
		return $row->toArray();
	}
		
	public function getBalance($userid){
		$row = $this->fetchRow('UserId = ' . (int)$userid);
		return $row->toArray();
	}
	
	public function addPremium($userid, $days){
        $info = $this->getIventorInformation($userid);

        if (strtotime($info->PremiumUntil) - time() > 0) {
            $this->update(array('Premium' => 1, 'PremiumUntil' => new Zend_Db_Expr('DATE_ADD(PremiumUntil, INTERVAL '.intval($days).' DAY)')), 'UserId = ' . (int)$userid);
        }
        else {
            $this->update(array('Premium' => 1, 'PremiumUntil' => new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL '.intval($days).' DAY)')), 'UserId = ' . (int)$userid);
        }
	}
	
	public function updateDiscount($userid, $dis){
		$data = array('Discount' => $dis);
		$this->update($data, 'UserId = ' . (int)$userid);
	}
	
	public function updateBalance($userid, $balance){
		$data = array('Balance' => $balance);
		$this->update($data, 'UserId = ' . (int)$userid);
	}
	 
	public  function updateInventorInfo($userid, $companyname, $phone, $responsibleperson, $image,  $description, $cityid, $countryid)
	{
		$data = array(
		        'CompanyName' => $companyname,
		        'CompanyPhone' => $phone,
		        'ResponsiblePerson' => $responsibleperson,
		        'Image' => $image,
		        'Description' => $description,
		        'CityId' => $cityid,
		        'CountryId' => $countryid
		        );
		$this->update($data, 'UserId = ' . (int)$userid);
	}

	public  function updateInventorInfoInAdmin($userid, $companyname, $phone, $address, $cityid, $balance, $discount, $countryid, $desc, $filename)
	{
		$data = array(
				'CompanyName' => $companyname,
				'CompanyPhone' => $phone,
				'ResponsiblePerson' => '',
		        'AddressLine' => $address,
				'Balance' => $balance,
				'Discount' => $discount,
				'CityId' => $cityid,
		        'CountryId' => $countryid,
		        'Description' => $desc,
		        'Image' => $filename
		);
		$this->update($data, 'UserId = ' . (int)$userid);
	}
	
	public function deleteInventorInfo($id)
	{
		$this->delete('Id = ' . (int)$id);
	}
	
	public function deleteInventorByUserId($userid)
	{
		$this->delete('UserId = ' . (int)$userid);
	}

    public function changeActive($id) {
        $select = $this->select()->from($this, array('Active'))->where('UserId = ?', $id);
        if ($select->query()->fetchObject()->Active > 0) {
            $this->update(array('Active' => 0), 'UserId = '.intval($id));
        }
        else {
            $this->update(array('Active' => 1), 'UserId = '.intval($id));
        }
    }

    public function phoneExists($phone) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('CompanyPhone = ?', trim($phone));
        return $select->query()->fetchObject()->c > 0;
    }

    public function getOldPremium() {
        $select = $this->select()->from($this)->where('Premium = 1')->where('PremiumUntil < ?', new Zend_Db_Expr('NOW()'));
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

    public function getBasicSoon() {
        $select = $this->select()->from($this, array("c" => new Zend_Db_Expr('DATEDIFF(PremiumUntil, NOW())'), "Email"))->where('Premium = 1')->where('DATEDIFF(PremiumUntil, NOW()) <= 5');
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }



}
