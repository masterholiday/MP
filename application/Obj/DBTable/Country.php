<?php
class Obj_DBTable_Country extends Zend_Db_Table_Abstract
{
	protected $_name = 'country';
	protected $_primary = 'country_id';
	 
	public function getCountry($id)
	{
	    $id = (int)$id;
		$row = $this->fetchRow('country_id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getCountryName($id)
	{
		$id = (int)$id;
		$query = "SELECT `name` FROM `country` WHERE country_id = ".$id;
		
		$row = $this->getAdapter()->fetchRow($query);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row;
	}
	
	public function getAllCountry()
	{
		$row = $this->fetchAll();
		return $row->toArray();
	}
	 
	public function addCountry($name)
	{
		$data = array(
		        'name' => $name,
		        );
		$id = $this->insert($data);
		return $id;
		
		}
	 
	public  function updateCountry($id, $name)
	{
		$data = array(
				'name' => $name,
		);
		$this->update($data, 'country_id = ' . (int)$id);
	}
	 
	public function deleteCountry($id)
	{
		$this->delete('country_id = ' . (int)$id);
	}
}
