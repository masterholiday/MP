<?php
class ProfileController extends DefaultBaseController
{
    protected $_needbeloggedin = true;
		
   	public function indexAction()
    {
        $this->_helper->redirector('index','index','default');
    	
    }
    
    public function updateAction()
    {
        $this->_helper->redirector('index','index','default');
       // Zend_Auth::getInstance()->clearIdentity();
       // $this->_helper->redirector('index', 'index');
       //$city = new Obj_DBTable_Citys();
       //$row = $city->getAllCity();
       //$this->view->row = $row;
    }
    
    
    public function doUpdateAction()
    {$this->_helper->redirector('index','index','default');
        die();
        $id = trim($this->getRequest()->getParam('id', ''));
        $uid = trim($this->getRequest()->getParam('uid', ''));
        $email = trim($this->getRequest()->getParam('email', ''));
        $pass = trim($this->getRequest()->getParam('pass', ''));
        $pass2 = trim($this->getRequest()->getParam('pass2', ''));
        $firstname = trim($this->getRequest()->getParam('firstname', ''));
        $city = trim($this->getRequest()->getParam('city', ''));
        
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
        	$this->_helper->redirector('update', 'profile', 'default', array('error' => 'Неверно введен E-mail'));
        }
        
        if ($pass == '' || ($pass != $pass2)) {
        	$this->_helper->redirector('update', 'profile', 'default', array('error' => 'Пароли не совпадают'));
        	return;
        }
        if($firstname==''){
        	$this->_helper->redirector('update', 'profile', 'default', array('error' => 'Введите имя '));
        }
       
        $password = md5($pass);
        $activ=1;
        $user = new Obj_DBTable_Users();
        $row = $user->getUsers($id);
        $user_type=$row['UserType'];
        
        $user->updateUsers($id, $email, $password, $activ, $user_type, $uid);
        $profile = new Obj_DBTable_Profile();
        
        $profile->updateUsers($id, $firstname, '1');
        
        $reg = Zend_Auth::getInstance()->getIdentity();
       
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')
        ->setIdentityColumn('Id')
        ->setCredentialColumn('Id');
        $authAdapter->setIdentity($reg->Id)
        ->setCredential($reg->Id);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {
        	$identity = $authAdapter->getResultRowObject();
        	$authStorage = $auth->getStorage();
        	$authStorage->write($identity);
        	$this->_helper->redirector('index','index','default');
        	return;
        
        }        
        
        $this->_helper->redirector('index','index','default');

    }
        
      
}


