<?php
class Obj_DBTable_Sms extends Zend_Db_Table_Abstract
{
	protected $_name = 'sms';
	protected $_primary = 'Id';
	 
	public function addCode($code, $phone){
        $this->delete("added < DATE_SUB(NOW(),INTERVAL 1 DAY)");
		$data = array('Code' => $code, 'Number' => $phone, 'added' => new Zend_Db_Expr('NOW()'));
		return $this->insert($data);
	}

    public function countCode($code) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('Code = ?', $code);
        return $select->query()->fetchObject()->c;
    }

	public function delCode($code){
		$this->delete('Code =' .$code);
	}

    public function findCode($code) {
        $select = $this->select()->from($this, array('Number'))->where('Code = ?', $code);
        return $select->query()->fetchObject();
    }
}

