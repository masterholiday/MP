<?php
class Obj_DBTable_Video extends Zend_Db_Table_Abstract
{
	protected $_name = 'iventor_video';
	protected $_primary = 'ID';
	 
	public function getCount($uid){
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(ID)')))->where('UserID = ?', $uid);
        return $select->query()->fetchObject()->c;
	}

    public function getVideos($uid) {
        return $this->select()->from($this)->where('UserID = ?', $uid)->query()->fetchAll(PDO::FETCH_OBJ);
    }

}

