<?php
class Obj_DBTable_Finance extends Zend_Db_Table_Abstract
{
	protected $_name = 'finance';
	protected $_primary = 'Id';

	public function getFinance($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}

	public function getFinanceByOrderId($Orderid)
	{
        $Orderid = (int)$Orderid;
		$row = $this->fetchRow('Id = ' . $Orderid);
		if(!$row) {
			die();
		}
		return $row->toArray();
	}

	public function getAllFinance(){
	    $row = $this->fetchAll();
	    return $row->toArray();
	}


	public function addFinance($ivenrotid, $sum, $days, $status)
	{
		$data = array(
		        'Date' => new Zend_Db_Expr('NOW()'),
		        'IventorId' => $ivenrotid,
		        'Sum' => $sum,
		        'Status' => $status,
                'TransactionId' => 0,
                'OrderID' => '',
                'Days' => $days
		);
		$id = $this->insert($data);
		return $id;

	}

	public function addFinansTransaction($iventor_id, $sum, $transaction_id){
		$data = array(
		        'Date' => new Zend_Db_Expr('NOW()'),
		        'IventorId' => $iventor_id,
		        'Sum' => $sum,
		        'TransactionId' => $transaction_id,
                'Status' => '',
                'OrderID' => '',
		        );
		return $this->insert($data);
	}


	public function updateFinance($id, $sum, $status)
	{
		$data = array(
				'Date' => new Zend_Db_Expr('NOW()'),
				'Sum' => $sum,
				'Status' => $status
		);
		$this->update($data, 'Id = ' . $id);
	}

	public function countElement(){
	    $sql = "SELECT `OrderId` FROM `finance` ORDER BY Id";
		$row = $this->getAdapter()->fetchAll($sql);
		$count = max($row);
		return $count['OrderId'];
	}

	public function updateStatus($id, $status){
		$data = array(
		        'Date' => new Zend_Db_Expr('NOW()'),
		        'Status' => $status
		        );
		$this->update($data, 'Id = ' . $id);
	}

    public function getIventorFinanceListCount($id, $year = 0, $month = 0) {
        $select = $this->select()->from($this, array('c' => new Zend_Db_Expr('COUNT(Id)')))->where('IventorId = ?', $id)->where("finance.Status = '' OR finance.Status = 'success'");
        if ($year != 0) {
            $select->where('YEAR(Date) = ?', $year);
        }
        if ($month != 0) {
            $select->where('MONTH(Date) = ?', $month);
        }
        return $select->query()->fetchObject()->c;
    }

    public function getIventorFinanceList($id, $year = 0, $month = 0, $page = 0, $perpage = 20) {
        $select = $this->select()->from($this)->where('finance.IventorId = ?', $id)->where("finance.Status = '' OR finance.Status = 'success'");
        if ($year != 0) {
            $select->where('YEAR(finance.Date) = ?', $year);
        }
        if ($month != 0) {
            $select->where('MONTH(finance.Date) = ?', $month);
        }
        $select->join('inventorinfo', 'inventorinfo.UserId = finance.IventorId', array());
        $select->join('city', 'city.city_id = inventorinfo.CityId', array('CountryID' => 'country_id'));
        $select->setIntegrityCheck(false);
        $select->order("finance.Date DESC");
        $select->limit($perpage, $page * $perpage);
        //echo $select->__toString();
        return $select->query()->fetchAll(PDO::FETCH_CLASS);
    }


}
