<?php
class Obj_DBTable_SearchResult extends Zend_Db_Table_Abstract
{
	protected $_name = 'search_result';
	protected $_primary = 'Id';
	
	public function addSearchResult($userid, $iven_act_id, $gen_search_id){
		$data = array(
		        'UserId' => intval($userid),
		        'IventActivityId' => intval($iven_act_id),
		        'GeneralSearchId' => intval($gen_search_id)
		        );
		return $this->insert($data);
	}
	 
	public function getSearchResultByUser($userid){
		return $this->fetchAll('UserId ='.(int)$userid, 'Id DESC');
	}
	
}
