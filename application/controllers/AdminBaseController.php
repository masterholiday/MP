<?php
class AdminBaseController extends Zend_Controller_Action {
	
	protected $_needbeloggedin = false;

	public function preDispatch() {
	   
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Fronend'));
        if (Zend_Auth::getInstance()->hasIdentity()) {
           	$oUser = Zend_Auth::getInstance()->getIdentity();
            Zend_Registry::set('user', $oUser);
            if($oUser->UserType != USER_TYPE_MANAGER) {
                $this->_helper->redirector('index','index','default');
            } elseif ($this->getRequest()->getActionName() == 'index' && $this->getRequest()->getControllerName() == 'index') {
                $this->_helper->redirector('manager','index','manager');
            }
        } else {
            if ($this->_needbeloggedin) {$this->_helper->redirector('index','index','manager');}
        }
        $this->_helper->layout()->setLayout('backend');
        $this->view->headTitle('Админпанель - Мастерская Праздников');
        $this->view->headMeta('', 'description');
        $this->view->headMeta('', 'keywords');
        $this->view->doctype('HTML5');
        $this->view->headMeta()->setHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    }
}