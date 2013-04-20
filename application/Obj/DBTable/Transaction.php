<?php
class Obj_DBTable_Transaction extends Zend_Db_Table_Abstract
{
	protected $_name = 'transaction';
	protected $_primary = 'Id';
	 
	public function addTransaction($iventid, $searchid, $iventserid, $userid)
	{
	    $data = array(
	            'IventorId' => $iventid,
	            'SetviceSearchId' => $searchid,
	            'IventorServiceId' => $iventserid,
	            'UserId' => $userid,
	            'Result' => 0
	            );
		return $this->insert($data);
	}
	
	public function getAllTransFromTo($from, $to){
		$sql = "SELECT * FROM `transaction` WHERE `SearchId` >= $from AND `SearchId`<= $to";
		$row = $this->getAdapter()->fetchAll($sql);
		return $row;
	}
	
	public function getAllUserTransaction($userId){
	    $row = $this->fetchAll('UserId = ' . (int)$userId, 'Id DESC');
		return $row->toArray();
	}
	
	public function getAllTransactionBySearchId($searchid){
		return $this->fetchAll('SetviceSearchId = '.$searchid)->toArray();
	}
	
	public function getTransaction($Id){
		$row = $this->fetchRow('Id = ' . $Id);
		return $row->toArray();
	}
	
	public function get10LastUserTransaction($userId){
		$sql = "SELECT * FROM `transaction` WHERE `IventorId` = $userId ORDER BY Id DESC LIMIT 10";
		$row = $this->getAdapter()->fetchAll($sql);
		return $row;
	}
	
	public function deleteTransaction($id)
	{
		$this->delete('Id = ' . (int)$id);
	}

    public function getIventorTransactionsCount($userid, $starred = false) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('transaction.IventorId = ?', $userid);
        if ($starred) $select->where('transaction.Result = 1');
        else $select->where('transaction.Result = 0');
        return $select->query()->fetchObject();
    }

    public function getIventorTransactions($userid, $starred = false, $page = 0, $perpage = 20) {
        $select = $this->select()->from($this, array('TransID' => 'Id'))->where('transaction.IventorId = ?', $userid);
        if ($starred) $select->where('transaction.Result = 1');
        else $select->where('transaction.Result = 0');
        $select->join('service_searches', 'service_searches.Id = transaction.SetviceSearchId', array('Info'));
        $select->join('searches', 'searches.Id = service_searches.SearchesId', array('ActivityName', 'Date', 'Address', 'SearchDate'));
        $select->join('users', 'users.Id = searches.UserId', array('Email'));
        $select->join('city', 'city.city_id = searches.CityId', array('CityName' => 'name'));
        $select->join('country', 'country.country_id = city.country_id', array('CountryName' => 'name'));
        $select->join('service_categories', 'service_categories.Id = service_searches.CategoriesId', array('ChildCategory' => 'CategoryName', 'ChildCategoryID' => 'Id'));
        $select->join('service_categories', 'service_categories_2.Id = service_categories.ParentId', array('ParentCategory' => 'CategoryName'));
        $select->join('profile', 'profile.UserId = transaction.UserId', array('Name', 'Phone'));
        $select->join('services_prices', 'services_prices.Id = service_searches.ServicesPricesId', array('LowPrice', 'HightPrice', 'CountryId'));
        $select->setIntegrityCheck(false);
        $select->order("searches.SearchDate DESC");
        $select->limit($perpage, $page * $perpage);
        return $select->query()->fetchAll(PDO::FETCH_CLASS);
    }

    public function makeStarred($id, $userid) {
        $this->update(array('Result' => 1), array('Id = '.intval($id).' AND IventorId = '.intval($userid)));
    }
}
