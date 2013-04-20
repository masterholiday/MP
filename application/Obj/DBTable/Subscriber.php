<?php
class Obj_DBTable_Subscriber extends Zend_Db_Table_Abstract
{
	protected $_name = 'subscriber';
	protected $_primary = 'Id';
	 
	
	function getAllSubscriber(){
		return $this->fetchAll()->toArray();
	}
	
	public function addSubscriber($email){
		$data = array('Email' => $email);
		return $this->insert($data);
	}	

	public function delSubscriber($id){
		$this->delete('Id =' .$id);
	}
}

