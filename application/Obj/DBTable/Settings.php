<?php
class Obj_DBTable_Settings extends Zend_Db_Table_Abstract
{
	protected $_name = 'settings';
	protected $_primary = 'ID';
	 
	public function getValue($key)
	{
        return $this->select()->from($this)->where('KeyName = ?', $key)->query()->fetchObject();
	}
}
