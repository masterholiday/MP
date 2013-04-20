<?php
class StaticController extends DefaultBaseController
{
    private $_static;
    public function init()
    {
    	$this->_static = new Obj_DBTable_Static();
    }
    
   	public function indexAction()
    {
 
    }

    public function rulesAction(){
	$uri = $this->getRequest()->getRequestUri();
	if( ($uri != "/static/rules/") && ($uri!= "/static/rules")) {
	 header('Location: /404.html');
            die();
	}
       $this->view->text = $this->_static->getRules();
    }
    
    public function agreementAction(){
	$uri = $this->getRequest()->getRequestUri();
	if( ($uri != "/static/agreement/") && ($uri!= "/static/agreement")) {
	 header('Location: /404.html');
            die();
	}
		
    	$this->view->text = $this->_static->getAgreement();
	   }
    
    public function howThisWorkAction(){
	
        $user = Zend_Registry::isRegistered('user') ? Zend_Registry::get('user') : new stdClass();
        if (!isset($user->UserType) || $user->UserType == USER_TYPE_USER) {
			
            $this->_helper->redirector('how-this-work-client','static','default');
            die();
        }
        else {
			
            $this->_helper->redirector('how-this-work-iventor','static','default');
            die();
        }
    }

    public function howThisWorkClientAction(){
	$uri = $this->getRequest()->getRequestUri();
	if( ($uri != "/static/how-this-work-client/") && ($uri!= "/static/how-this-work-client")) {
			 header('Location: /404.html');
					die();
			}
     }

    public function howThisWorkIventorAction(){
	$uri = $this->getRequest()->getRequestUri();
	if( ($uri != "/static/how-this-work-iventor/") && ($uri!= "/static/how-this-work-iventor")) {
			 header('Location: /404.html');
					die();
			}
    }
}


