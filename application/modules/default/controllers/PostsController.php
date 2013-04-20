<?php

class PostsController extends DefaultBaseController
{
    protected $_needbeloggedin = true;
    
    public function indexAction()
    {
        $this->view->err = $this->getRequest()->getParam('err');
        //Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Fronend'));
        $user = Zend_Auth::getInstance()->getIdentity();
	    $post = new Obj_DBTable_Posts();
	    $row = $post->getAllPostsByUserId($user->Id);
	    if($row == 'error'){
	    $this->view->error = 'Статье нет';
	    }else{
	    	$this->view->post = $row;
	    }
               
    }
    
      
    public function addPostsAction(){
    	$categotyid = trim($this->getRequest()->getParam('categoryid'));
    	$tags = trim($this->getRequest()->getParam('tags'));
    	$title = trim($this->getRequest()->getParam('title'));
    	$introtext = trim($this->getRequest()->getParam('introtext'));
    	$fulltext = trim($this->getRequest()->getParam('fulltext'));
    	
    	
    	if($title == ''){
    		$this->_helper->redirector('index','posts','default', array('err' => 'Введите зоголовок'));
    	}
    	if($introtext == ''){
    		$this->_helper->redirector('index','posts','default', array('err' => 'Введите введение'));
    	}
    	if($fulltext == ''){
    		$this->_helper->redirector('index','posts','default', array('err' => 'Введите текст стати'));
    	}
    	
    if(is_uploaded_file($_FILES["img"]["tmp_name"])){
        $type = explode('/', $_FILES['img']['type']);
        	if($type[1] == 'jpeg' || $type[1] == 'jpg' || $type[1] == 'gif' || $type[1] == 'png' || $type[1] == 'bmp'){    
        		$filename = md5(microtime(true)).'.'.$type[1];
       			$rez = move_uploaded_file($_FILES['img']['tmp_name'], APPLICATION_PATH.'/../public/img/articles/'.$filename); 
       			require_once APPLICATION_PATH.'/../library/PHPThumb/ThumbLib.inc.php';
       			$_im = PhpThumbFactory::create(APPLICATION_PATH.'/../public/img/articles/'.$filename);
       			$_im->setOptions(array('resizeUp' => true));
       			$iWidth = 148;
       			$iHeight = 144;
       			$_im->adaptiveResize($iWidth, $iHeight);
       			//$_im->resize($iWidth, $iHeight);
       			$_im->save(APPLICATION_PATH.'/../public/img/articles'.'/'.$iWidth.'x'.$iHeight.'_'.$filename);
        	}
        }
    	
    	Zend_Auth::getInstance()->hasIdentity();
    	$user = Zend_Auth::getInstance()->getIdentity();
    	$post = new Obj_DBTable_Posts();
    	$post->addPosts($user->Id, $categotyid, $tags, $title, $introtext, $fulltext, $filename);
    	$this->_helper->redirector('index','posts','default');
    	
    }
    
    public function editPostsAction(){
    	$id = $this->getRequest()->getParam('id');
    	$post = new Obj_DBTable_Posts();
    	$post_row = $post->getPosts($id);
    	$this->view->post = $post_row;
    	
    }
    
    public function doeditPostsAction(){
        $id = $this->getRequest()->getParam('id');
        $categotyid = trim($this->getRequest()->getParam('categoryid'));
        $tags = trim($this->getRequest()->getParam('tags'));
        $title = trim($this->getRequest()->getParam('title'));
        $introtext = trim($this->getRequest()->getParam('introtext'));
        $fulltext = trim($this->getRequest()->getParam('fulltext'));
         
        if($title == ''){
        	$this->_helper->redirector('index','posts','default', array('err' => 'Введите зоголовок'));
        }
        if($introtext == ''){
        	$this->_helper->redirector('index','posts','default', array('err' => 'Введите введение'));
        }
        if($fulltext == ''){
        	$this->_helper->redirector('index','posts','default', array('err' => 'Введите текст стати'));
        }
         
        Zend_Auth::getInstance()->hasIdentity();
        $user = Zend_Auth::getInstance()->getIdentity();
        $post = new Obj_DBTable_Posts();
        $post->updatePosts($id, $user->Id, $categotyid, $title, $tags, $introtext, $fulltext);
        $this->_helper->redirector('index','posts','default');
    }
        
    public function delPostsAction(){
    	$id = $this->getRequest()->getParam('id');
    	$post = new Obj_DBTable_Posts();
    	$post->deletePosts($id);
    	$this->_helper->redirector('index','posts','default');
    }
}



