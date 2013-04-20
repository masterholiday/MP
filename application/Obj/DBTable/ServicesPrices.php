<?php
class Obj_DBTable_ServicesPrices extends Zend_Db_Table_Abstract
{

	protected $_name = 'services_prices';
	protected $_primary = 'Id';
	 
	public function getServicesPrices($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
            return false;
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllServicesPrices()
	{
		$row = $this->fetchAll();
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}

	public function getAllPriceByCountry($country){
	    
		return $this->fetchAll('CountryId ='.(int)$country, 'Gain')->toArray();
	}
	
	public function addServicesPrices($lowprice, $hightprice, $gain, $country)
	{
		$data = array(
		        'LowPrice' => $lowprice,
		        'HightPrice' => $hightprice,
		        'Gain' => $gain,
		        'CountryId' => $country
		        );
		$id = $this->insert($data);
		return $id;
		}
	 
	public  function updateServicesPrices($id, $lowprice, $hightprice, $gain, $country)
	{
		$data = array(
		        'LowPrice' => $lowprice,
		        'HightPrice' => $hightprice,
		        'Gain' => $gain,
		        'CountryId' => $country
		        );
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deleteServicesPrices($id)
	{
		$this->delete('Id = ' . (int)$id);
	}
}
