<?php
class Obj_DBTable_FaqCategories extends Zend_Db_Table_Abstract
{

	protected $_name = 'faq_categories';
	protected $_primary = 'Id';
	 
	public function getFaqCategory($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllFaqCategory()
	{
		$row = $this->fetchAll(NULL, 'UserType');
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function  getAllFaqCategoryByClient(){
	    $row = $this->fetchAll('UserType = '.USER_TYPE_USER);
	    if(!$row) {
	    	throw new Exception("��� ������ � Id - $id");
	    }
	    return $row->toArray();
	}
	
	
	public function  getAllFaqCategoryByIventor(){
		$row = $this->fetchAll('UserType = '.USER_TYPE_INVENTOR);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}

	
	public function addFaqCategory($faqcategoryname, $user)
	{
		$data = array(
		        'FaqCategoryName' => $faqcategoryname,
		        'UserType' => $user
		        );
		$id = $this->insert($data);
		return $id;
		}
	 
	public  function updateFaqCategory($id, $faqcategoryname)
	{
		$data = array(
		        'FaqCategoryName' => $faqcategoryname,
		        );
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deleteFaqCategory($id)
	{
		$this->delete('Id = ' . (int)$id);
	}
}
