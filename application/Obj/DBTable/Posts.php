<?php
class Obj_DBTable_Posts extends Zend_Db_Table_Abstract
{

	protected $_name = 'posts';
	protected $_primary = 'Id';
	 
	public function getPosts($id)
	{
		$id = (int)$id;
		$row = $this->fetchRow('Id = ' . $id);
		if(!$row) {
			return false;
		}
		return $row->toArray();
	}
	
	public function getAllPost(){
		$row = $this->fetchAll();
		return $row->toArray();
	}
	
	public function getPostByLimit($limit,$start = 0){
		$sql = $this->select()
			->from('posts')
			->order('DateCreated DESC')
			->limit($limit, $start);
		$sql->setIntegrityCheck(false);
		return $result = $this->fetchAll($sql)->toArray();
	}
	

	public function getPostsByUserId($userid)
	{
	   	$userid = (int)$userid;
		$row = $this->fetchRow('UserId = ' . $userid);
		if(!$row) {
			return 'error';
		}else{
		return $row->toArray();}
	}
	
	public function getAllPostsByUserId($userid)
	{
		$userid = (int)$userid;
		$row = $this->fetchAll('UserId = ' . $userid);
		if(!$row) {
			return 'error';
		}else{
			return $row->toArray();
		}
	}
	
	public function getPostsByCaregoryId($categoryid)
	{
		$categoryid = (int)$categoryid;
		$row = $this->fetchRow('Id = ' . $categoryid);
		if(!$row) {
			throw new Exception("��� ������ � Id - $categoryid");
		}
		return $row->toArray();
	}
	
	public function getAllPostsByCaregoryId($categoryid)
	{
		$categoryid = (int)$categoryid;
		$row = $this->fetchAll('CategoryId = ' . $categoryid);
		if(!$row) {
			throw new Exception("��� ������ � Id - $categoryid");
		}
		return $row->toArray();
	}
	
	public function addPosts($userid, $categoryid, $tags, $title, $introtext, $fulltext, $image = 0)
	{
		$data = array(
		        'UserId' => $userid,
		        'DateCreated' => new Zend_Db_Expr('NOW()'),
		        'CategoryId' => $categoryid,
		        'Title' => $title,
		        'Tags' => $tags,
		        'IntroText' => $introtext,
		        'FullText' => $fulltext,
		        'Image' => $image
		        );
		
		$id = $this->insert($data);
		return $id;
		}
	 
	public  function updatePosts($id, $userid, $categoryid, $title, $tags, $introtext, $fulltext, $image = 0)
	{
		$data = array(
		        'UserId' => $userid,
		        'DateCreated' => new Zend_Db_Expr('NOW()'),
		        'CategoryId' => $categoryid,
		        'Tags' => $tags,
		        'Title' => $title,
		        'IntroText' => $introtext,
		        'FullText' => $fulltext,
		        'Image' => $image
		        );
		$this->update($data, 'Id = ' . (int)$id);
	}
	
	public  function updatePostsAdmin($id, $categoryid, $title, $tags, $introtext, $fulltext, $seotitle, $seodesc, $seokey, $image)
	{
	    if($image){
			$data = array(
				'DateCreated' => new Zend_Db_Expr('NOW()'),
				'CategoryId' => $categoryid,
				'Tags' => $tags,
				'Title' => $title,
				'IntroText' => $introtext,
				'FullText' => $fulltext,
		        'SeoTitle' =>$seotitle,
		        'SeoDescription' => $seodesc,
		        'SeoKeywords' => $seokey,
				'Image' => $image
			);
	    }else{
	        $data = array(
	        		'DateCreated' => new Zend_Db_Expr('NOW()'),
	        		'CategoryId' => $categoryid,
	        		'Tags' => $tags,
	        		'Title' => $title,
	        		'IntroText' => $introtext,
	        		'FullText' => $fulltext,
	        		'SeoTitle' =>$seotitle,
	        		'SeoDescription' => $seodesc,
	        		'SeoKeywords' => $seokey
	        	
	        );
	    }
		$this->update($data, 'Id = ' . (int)$id);
	}
	
	public  function addPostsAdmin($userid, $categoryid, $title, $tags, $introtext, $fulltext, $seotitle, $seodesc, $seokey, $image = 0)
	{
		$data = array(
				'UserId' => $userid,
				'DateCreated' => new Zend_Db_Expr('NOW()'),
				'CategoryId' => $categoryid,
				'Tags' => $tags,
				'Title' => $title,
				'IntroText' => $introtext,
				'FullText' => $fulltext,
				'SeoTitle' =>$seotitle,
				'SeoDescription' => $seodesc,
				'SeoKeywords' => $seokey,
				'Image' => $image
		);
		$this->insert($data);
	}
	 
	public function deletePosts($id)
	{
		$this->delete('Id = ' . (int)$id);
	}
}
