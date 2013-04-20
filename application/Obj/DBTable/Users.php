<?php
class Obj_DBTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name = 'users';
	protected $_primary = 'Id';
	 
	public function getUsers($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}

	public function getAllIvent(){
	    $query = "SELECT `Id`, `Email` FROM `users` WHERE UserType = ".USER_TYPE_INVENTOR;
	    $row = $this->getAdapter()->fetchAll($query);
	    return $row;
	}
	
	public function getAllIventFromPagenator($count, $start){
		$sql = $this->select()
						->from('users')
						->limit($count, $start)
						->where('UserType = '.USER_TYPE_INVENTOR)->order('RegistrationDate DESC');
		$sql->setIntegrityCheck(false);
		
		$result = $this->fetchAll($sql)->toArray();
		return $result;
	}
	
	public function getAllUserFromPagenator($count, $start){
		$sql = $this->select()
		->from('users')
		->limit($count, $start)
		->where('UserType = '.USER_TYPE_USER)->order('RegistrationDate DESC');
		$sql->setIntegrityCheck(false);
	
		$result = $this->fetchAll($sql)->toArray();
		return $result;
	}
	
	public function getAllUserTypeUser(){
		$query = "SELECT `Id`, `Email`,`RegistrationDate` FROM `users` WHERE UserType = ".USER_TYPE_USER;
		$row = $this->getAdapter()->fetchAll($query);
		return $row;
	}
	
	public function addUsers($email, $pass, $uid = 0,  $usertype = 1, $active = 0)
	{
		$kay = md5(microtime(true));
		
		$data = array(
		        'Email' => $email,
		        'Password' => $pass,
		        'RegistrationDate' => new Zend_Db_Expr('NOW()'),
		        'Active' => $active,
		        'UserType' => $usertype,
		        'ActiveKay' => $kay,
		        'UId' => $uid
		);
		$id = $this->insert($data);
		return $id;
		
	}
	 
	public  function updateUsers($id, $email, $pass, $activ, $user_type ,$uid, $activ_kay=0)
	{
		$data = array(
				'Email' => $email,
		        'Password' => $pass,
		        'RegistrationDate' => new Zend_Db_Expr('NOW()'),
		        'Active' => $activ,
		        'UserType' => $user_type,
		        'ActiveKay' => $activ_kay,
		        'UId' => $uid
		);
		$this->update($data, 'Id = ' . (int)$id);
	}

	public function updateEmail($id, $email){
		$data = array('Email' => $email);
		$this->update($data, 'Id = ' . (int)$id);
	}
	
	public function deleteUsers($id)
	{
		$this->delete('Id = ' . (int)$id);
	}

    public function checkUserPass($id, $pass) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('Id = ?', intval($id))->where('Password = ?', md5($pass));
        return intval($select->query()->fetchObject()->c);
    }

    public function changePassword($id, $pass) {
        $this->update(array('Password' => md5($pass)), 'Id = '.intval($id));
    }

    public function restorePassword($email) {
        $select = $this->select()->from($this, array('Id'))->where('Email = ?', trim($email));
        $o = $select->query()->fetchObject();
        if ($o) {
            $pass = substr(md5(md5(microtime(true)).$o->Id), 0, 12);
            $this->update(array('Password' => md5($pass)), 'Id = '.$o->Id);
            return $pass;
        }
        return false;
    }

    public function emailExists($email, $cid) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('Email = ?', trim($email))->where('Id <> ?', $cid);
        return $select->query()->fetchObject()->c > 0;
    }

    public function findCookieCode($code) {
        $select = $this->select()->from($this, array('Id'))->where('RememberMe = ?', $code);
        return $select->query()->fetchObject();
    }

    public function findConfirmKey($code) {
        $select = $this->select()->from($this)->where('ActiveKay = ?', $code)->where('Active = 0');
        return $select->query()->fetchObject();
    }


}
