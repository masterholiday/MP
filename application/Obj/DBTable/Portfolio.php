<?php
class Obj_DBTable_Portfolio extends Zend_Db_Table_Abstract
{
	protected $_name = 'iventor_portfolio';
	protected $_primary = 'Id';
	 
	
	public function addPortfolio($ivintor_id, $file_name){
		$data = array(
		        'IventorId' => (int)$ivintor_id,
		        'FileName' => $file_name
		        );
		
		return $this->insert($data);
	}
	
	public function getPortfolio($user, $id){
		$res = $this->fetchRow('Id ='.(int)$id.' AND IventorId = '.intval($user));
        if (!$res) return false;
        return $res->toArray();
	}
	
	public function delPortfolio($user, $id){
		$row = $this->getPortfolio($user, $id);
        if (!$row) return;
        $this->delete('Id ='.(int)$id);
		if(@unlink(APPLICATION_PATH.'/../public/img/users/'.$row['IventorId'].'/'.$row['FileName'])){
		    $this->delete('Id ='.(int)$id);
		    @unlink(APPLICATION_PATH.'/../public/img/users/'.$row['IventorId'].'/800x800_'.$row['FileName']);
		    @unlink(APPLICATION_PATH.'/../public/img/users/'.$row['IventorId'].'/199x169_'.$row['FileName']);
		    
		}
	}
	
	public function getAllUserPortfolio($user_id, $limit = 8, $start = 0){
		$sql = $this->select()
					->from('iventor_portfolio')
					->where('IventorId ='.$user_id)
					->limit($limit, $start);
		return $this->fetchAll($sql)->toArray();
	}
	
	public function getCountUserPortfolio($user_id){
		return count($this->fetchAll('IventorId ='.$user_id)->toArray());
	}

    public function getIventorPhotoCount($id) {
        $select = $this->select()->from($this, array("c" => new Zend_Db_Expr('COUNT(Id)')))->where("IventorId = ?", (int) $id);
        return $select->query()->fetchObject()->c;
    }
	
	
}
