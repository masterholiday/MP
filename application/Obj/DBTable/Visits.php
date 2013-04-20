<?php
class Obj_DBTable_Visits extends Zend_Db_Table_Abstract
{
	protected $_name = 'visits';
	protected $_primary = 'id';

    public function getVisits($iid) {
        //return 
		//$sql = $this->select()->from($this)->where('iventorid = ?', $iid);//->query()->fetchAll(PDO::FETCH_OBJ);
		//return $this->fetchAll($sql)->toArray();
		$currentmonth = date("n");
		$currentyear = date("Y");
		$select = $this->select()->from($this)->where('iventorid = ' . (int)$iid . ' AND year = '. (int)$currentyear . ' AND month = '. (int)$currentmonth );
        return intval(@$select->query()->fetchObject()->visits);
		

    }
	
	


}

