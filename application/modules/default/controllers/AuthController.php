<?php
class AuthController extends DefaultBaseController
{
		
   	public function indexAction()
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')->setIdentityColumn('Email')->setCredentialColumn('Password');
        $authAdapter->setCredentialTreatment("MD5(?) AND Active = 1 AND SocialID = '' AND Social = '' AND UserType <> ".USER_TYPE_MANAGER);
        $username = trim($this->getRequest()->getPost('login', ''));
        $password = $this->getRequest()->getPost('password', '');
        $rememberMe = intval($this->getRequest()->getPost('remember', 0)) == 1 ? true : false;

        $authAdapter->setIdentity($username)->setCredential($password);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
      	if ($result->isValid()) {

            $identity = $authAdapter->getResultRowObject();
              if ($rememberMe) {
                  $oUser = new User();
                  $oUser->doRemember($identity->Id);
              }
            $authStorage = $auth->getStorage();
            if ($identity->UserType == USER_TYPE_INVENTOR) {
                $type = 'eventor';
            } else {
                $type = 'client';
            }
            $authStorage->write($identity);
            $this->_helper->json(array('ok' => 'ok', 'type' => $type), true, false);
            return;
        } else {
            $this->_helper->json(array('error' => 'error'), true, false);
            return;
        }
    }

    public function getHeaderAction() {
        $user = $this->getAuth();
        if ($user && isset($user->UserType) && $user->UserType == USER_TYPE_USER) {
            $arr = array();
            $s = $this->view->render('auth/loginarea.phtml');
            $arr['mapuserarea'] = $s;
            $s = $this->view->render('auth/topinfo.phtml');
            $arr['topinfo'] = $s;
            $arr['change'] = 1;
            $this->_helper->json($arr, true, false);
        }
        else {
            $this->_helper->json(array('change' => 0), true, false);
        }
    }

    public function checkPhoneNumberAction() {
        $user = $this->getAuth();
        if ($user && isset($user->UserType) && $user->UserType == USER_TYPE_USER) {
            $oUser = new User();
            if ($oUser->getPhoneNumber($user->Id) != '') {
                $this->_helper->json(array('exists' => 1), true, false);
            }
        }
        $this->_helper->json(array('exists' => 0), true, false);
    }


}