<?php
class Obj_DBTable_FAQ extends Zend_Db_Table_Abstract
{

	protected $_name = 'faq';
	protected $_primary = 'Id';
	 
	public function getFAQ($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllFAQByUser()
	{
		$row = $this->fetchAll('UserType = '.USER_TYPE_USER);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllFAQByIventor()
	{
		$row = $this->fetchAll('UserType = '.USER_TYPE_INVENTOR);
		if(!$row) {
			throw new Exception("��� ������ � Id - $id");
		}
		return $row->toArray();
	}
	
	public function getAllFaqByCategory($cat){
	    $row = $this->fetchAll('FaqCategoryId = '.(int)$cat);
	    if(!$row) {
	    	throw new Exception("��� ������ � Id - $id");
	    }
	    return $row->toArray();
	}

	
	public function addFAQ($faqcategoryid, $question, $answer, $user)
	{
		$data = array(
		        'FaqCategoryId' => $faqcategoryid,
		        'Question' => $question,
		        'Answer' => $answer,
		        'UserType' => $user
		        );
		$id = $this->insert($data);
		return $id;
		}
	 
	public  function updateFAQ($id, $faqcategoryid, $question, $answer)
	{
		$data = array(
		        'FaqCategoryId' => $faqcategoryid,
		        'Question' => $question,
		        'Answer' => $answer
		        );
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deleteFAQ($id)
	{
		$this->delete('Id = ' . (int)$id);
	}
}
