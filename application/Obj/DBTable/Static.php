<?php
class Obj_DBTable_Static extends Zend_Db_Table_Abstract
{
	protected $_name = 'static';
	protected $_primary = 'Id';
	 
	
	function getRules(){
		return $this->fetchRow('Id = 1')->toArray();
	}
	
	function updateRules($text){
	    $data = array('Text' => strval($text));
		$this->update($data, 'Id = 1');
	}
	
	function getAgreement(){
		return $this->fetchRow('Id = 2')->toArray();
	}
	
	function updateAgreement($text){
		$data = array('Text' => strval($text));
		$this->update($data, 'Id = 2');
	}
	
	function getHowThisWorkClient(){
		return $this->fetchRow('Id = 3')->toArray();
	}
	
	function updateHowThisWorkClient($text){
		$data = array('Text' => strval($text));
		$this->update($data, 'Id = 3');
	}
	
	function getHowThisWorkIventor(){
		return $this->fetchRow('Id = 4')->toArray();
	}
	
	function updateHowThisWorkIventor($text){
		$data = array('Text' => strval($text));
		$this->update($data, 'Id = 4');
	}
	
	
}

