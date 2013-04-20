<?php
class Obj_DBTable_ServiceCategories extends Zend_Db_Table_Abstract
{
	protected $_name = 'service_categories';
	protected $_primary = 'Id';

    public function getShortList() {
        $select = $this->select()->from($this, array('CatID' => 'ID', 'CategoryName'))->where("service_categories.ParentId = 0");
        $select->join(array("sc2" => "service_categories"), "sc2.ParentId = service_categories.Id", array("SubcategoryName" => "CategoryName", "SubID" => "ID"));
        $select->joinLeft("iventor_services", "sc2.Id = iventor_services.CategoryId AND iventor_services.IventorId IN (SELECT UserId FROM inventorinfo WHERE inventorinfo.`Active` = 1)", array("total" => new Zend_Db_Expr("COUNT(iventor_services.ID)")));
        $select->order("service_categories.CategoryName");
        $select->order("SubcategoryName");
        $select->group("SubID");
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }


	public function getCategories($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
            return false;
			throw new Exception("BIDA");
		}
		return $row->toArray();
	}
	
	
		public function getAllNotEmptyCategories($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
            return false;
			throw new Exception("BIDA");
		}
		return $row->toArray();
	}

	
		
	 public function getId($alias) {
        $select = $this->select()->from($this)->where('service_categories.alias LIKE ?', $alias);
       return intval(@$select->query()->fetchObject()->Id);
    }
	
	
	 public function getAlias($id) {
        $select = $this->select()->from($this)->where('service_categories.Id = ?', $id);
       return $select->query()->fetchObject()->alias;
    }
	
	public function isAlias($alias) {
       $select = $this->select()->from($this)->where('service_categories.alias LIKE ?', $alias);
	   $row = $select->query()->fetchObject();
	   if(!$row) {
            return null;
		}	
       return $row->Id;
    }
	
	 public function getName($id) {
        $select = $this->select()->from($this)->where('service_categories.Id = ?', $id);
       return $select->query()->fetchObject()->CategoryName;
    }
	
	 public function getParent($id) {
        $select = $this->select()->from($this)->where('service_categories.Id = ?', $id);
       return $select->query()->fetchObject()->ParentId;
    }

    
	
	
    public function getCategoriesForParent($parent) {
        $select = $this->select()->from($this)->where('ParentId = ?', intval($parent))->order('CategoryName');
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }

	public function getAllCategoriesByParent($parentid = 0)
	{
	   	$parentid = (int)$parentid;
	   	$row = $this->fetchAll('ParentId = ' . $parentid);
		if(!$row) {
			throw new Exception("BIDA");
		}
		return $row->toArray();
	}
	
	public function getCategoriesByParent($parentid = 0)
	{
		$parentid = (int)$parentid;
		$row = $this->fetchRow('ParentId = ' . $parentid);
		if(!$row) {
			throw new Exception("BIDA");
		}
		return $row->toArray();
	}

	public function addCategories($categoryname, $parentid = 0)
	{
	    $data = array(
		        'CategoryName' => $categoryname,
		        'ParentId' => $parentid
	            );
	    
	    
		$id = $this->insert($data);
		
		return $id;
		
		}
	 
	public  function updateCategories($id, $categoryname, $parentid = 0)
	{
	  $data = array(
				'CategoryName' => $categoryname,
		        'ParentId' => $parentid
				);
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deleteCategories($id)
	{
		$this->delete('Id = ' . (int)$id);
	}

    public function getCategorySeo($catid, $cityid) {
        $select = $this->select()->from($this)->where('service_categories.Id = ?', $catid);
        $select->join("city_category_text", "city_category_text.CategoryID = service_categories.Id", array("Description","desc","title"));
        $select->where("city_category_text.CityID = ? OR city_category_text.CityID = 0", $cityid);
        $select->order("city_category_text.CityID DESC");
        $select->setIntegrityCheck(false);
        return $select->query()->fetchAll(PDO::FETCH_OBJ);
    }
}
