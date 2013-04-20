<?php
class Obj_DBTable_Profile extends Zend_Db_Table_Abstract
{
	protected $_name = 'profile';
	protected $_primary = 'Id';
	 
	public function getUsers($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("$id");
		}
		return $row->toArray();
	}
	 
		public function getProfileByUserId($id)
		{
		    
		   	$id = (int)$id;
			$row = $this->fetchRow('UserId = ' . $id);
			
			if(!$row) {
				throw new Exception("$id");
			}
			return $row->toArray();
		}
		
		
	public function addUsers($userid, $name, $cityid, $phone)
	{
	$data = array(
		        'UserId' => $userid,
		        'Name' => $name,
		        'CitysId' => $cityid,
		        'Phone' => $phone,
		        );
		
		$id = $this->insert($data);
		return $id;
		
		}

	public  function updateUsers($userid, $name, $cityid, $phone)
	{
		$data = array(
				'Name' => $name,
		        'CitysId' => $cityid,
		        'Phone' => $phone
		        );
		$this->update($data, 'UserId = ' .(int)$userid);
	}
	 
	public function deleteUsers($userid)
	{
		$this->delete('UserId = ' . (int)$userid);
	}

    public function phoneExists($phone) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('Phone = ?', trim($phone));
        return $select->query()->fetchObject()->c > 0;
    }

    public function getPhoneNumber($uid) {
        $select = $this->select()->from($this, array('Phone'))->where('UserId = ?', intval($uid));
        $res = $select->query()->fetchObject();
        if ($res) return trim($res->Phone);
        return '';
    }

    public function getClientName($uid) {
        $select = $this->select()->from($this, array('Name'))->where('UserId = ?', intval($uid));
        $res = $select->query()->fetchObject();
        if ($res) return trim($res->Name);
        return '';
    }

    public function getBlockedInfo($id) {
        $select = $this->select()->from($this, array('PhoneCodeRequests', 'PhoneCodeEnter', 'smsBlockedUntil'))->where('UserId = ?', $id);
        return $select->query()->fetchObject();
    }

    public function getProfileInfo($id) {
        $select = $this->select()->from($this, array('Email', 'Name', 'Phone'))->where('UserId = ?', $id);
        return $select->query()->fetchObject();
    }


    public function clearBlocked($id) {
        $this->update(array('PhoneCodeRequests' => 0, 'PhoneCodeEnter' => 0, 'smsBlockedUntil' => '0000-00-00'), 'UserId = '.intval($id));
    }
}
