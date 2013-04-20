<?php
class Obj_DBTable_PostCategories extends Zend_Db_Table_Abstract
{

	protected $_name = 'post_categories';
	protected $_primary = 'Id';
	 
	public function getPostCategories($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			throw new Exception("Нет записи с Id - $id");
		}
		return $row->toArray();
	}

	public function getAllPostCategories()
	{
		$row = $this->fetchAll();
		if(!$row) {
			throw new Exception("Нет записи с Id - $id");
		}
		return $row->toArray();
	}
	
	public function addPostCategories($categoryname)
	{
		$data = array(
		        'CategoryName' => $categoryname
		        );
		$id = $this->insert($data);
		return $id;
		}
	 
	public  function updatePostCategories($id, $categooryname)
	{
		$data = array(
		        'CategoryName' => $categooryname
		        );
		$this->update($data, 'Id = ' . (int)$id);
	}
	 
	public function deletePostCategories($id)
	{
		$this->delete('Id = ' . (int)$id);
	}
}
