<?php
class Obj_DBTable_Banner extends Zend_Db_Table_Abstract
{
	protected $_name = 'banner';
	protected $_primary = 'ID';
	 
	public function getBanner(){
        $select = $this->select()->from($this);
        return $select->query()->fetchObject();
	}

}

