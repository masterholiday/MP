<?php
class Obj_DBTable_References extends Zend_Db_Table_Abstract
{
	protected $_name = 'references';
	protected $_primary = 'id';

    public function getRefs($iid) {
		$currentmonth = date("n");
		$currentyear = date("Y");
		$select = $this->select()->from($this)->where('iventorid = ' . (int)$iid . ' AND year = '. (int)$currentyear . ' AND month = '. (int)$currentmonth );
        return intval(@$select->query()->fetchObject()->reference);
		

    }
	
	


}