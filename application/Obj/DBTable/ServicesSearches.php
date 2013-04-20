<?php
class Obj_DBTable_ServicesSearches extends Zend_Db_Table_Abstract
{
	protected $_name = 'service_searches';
	protected $_primary = 'Id';
	 
	public function getServicesSearches($id)
	{
	    $id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllServicesSearchesBySearchesId($searchesid)
	{
		$searchesid = (int)$searchesid;
		$row = $this->fetchAll('SearchesId = ' . $searchesid);
		if(!$row) {
			throw new Exception("��� ������ � Id - $searchesid");
		}
		return $row->toArray();
	}
	
	public function getAllServicesSearches()
	{
		$row = $this->fetchAll();
		return $row->toArray();
	}
	 
	public function addServicesSearches($searchesid, $categoryid, $time, $servicespricesid, $info)
	{
		$data = array(
		        'SearchesId' => $searchesid,
		        'CategoriesId' => $categoryid,
		        'Time' => $time,
		        'ServicesPricesId' => $servicespricesid,
		        'Info' => $info
		        );
		
		$id = $this->insert($data);
		return $id;
		
		}
	 
	public  function updateServicesSearches($id, $searchesid, $caregoryid, $time, $servicespricesid, $info)
	{
		$data = array(
				'SearchesId' => $searchesid,
		        'CategoriesId' => $caregoryid,
		        'Time' => $time,
		        'ServicesPricesId' => $servicespricesid,
		        'Info' => $info
		);
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deleteServicesSearches($id)
	{
		$this->delete('Id = ' . (int)$id);
	}

    public function getSearchServices($sid) {
        $select = $this->select()->from($this, array('Id', 'minPrice', 'maxPrice'))->where($this->_name.'.SearchID = ?', $sid)->where($this->_name.'.UserDeleted = 0');
        $select->join(array("sc1" => "service_categories"), "sc1.Id = ".$this->_name.".CategoriesId", array("Subcategory" => "CategoryName", "SubcategoryID" => "Id"));
        $select->join(array("sc2" => "service_categories"), "sc2.Id = sc1.ParentId", array("Category" => "CategoryName"));
        $select->joinLeft("inventorinfo", "inventorinfo.UserId = ".$this->_name.".EventorID", array("CompanyName", "CompanyPhone", "Website", "Image", "Description", "EventorID" => "Id", "EventorUserID" => "UserId"));
        $select->order("Category")->order("Subcategory");
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

    public function getIventorSearchesCount($id) {
        $select = $this->select()->from($this, array("c" => new Zend_Db_Expr('COUNT(Id)')))->where("EventorID = ?", (int) $id);
        return $select->query()->fetchObject()->c;
    }

}
