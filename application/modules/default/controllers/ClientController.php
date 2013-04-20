<?php

class ClientController extends DefaultBaseController
{
    protected $_needbeloggedin = true;
    
    public function indexAction()
    {
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_redirect("/");
            return;
        }

	    $oSearch = new Search();
	    $sType = $this->getRequest()->getParam('type', 'complex');
	    $show = intval($this->getRequest()->getParam('show', 0));
	    $delete = intval($this->getRequest()->getParam('delete', 0));
        if ($delete !== 0) {
            $oSearch->deleteClientSearch($delete, $this->_auth->Id);
        }

        $this->view->showFirst = $show === 1;
	    $this->view->stype = $sType;
	    $page = intval($this->getRequest()->getParam('page', 0));
        $perpage = 5;
        $total = $oSearch->getClientSearchesCount($this->_auth->Id, $sType);
        $pages = intval(ceil($total / $perpage));
        $arCurrPageAddr = array('controller' => 'client', 'action' => 'index', 'type' => $sType, 'page' => $page);
        if ($page >= $pages) $page = $pages - 1;
        if ($page < 0) $page = 0;
        $list = $oSearch->getClientSearches($this->_auth->Id, $sType, $page, $perpage);
        $this->view->list = $list;
        $arPageAddr = array('controller' => 'client', 'action' => 'index', 'type' => $sType);

        $dots = array('html'=> '...', 'class' => '', 'href' => '');
        if (count($list)) {
            $arPages = array();
            $arPages2 = array();
            if ($page > 0) {
                $arPages[] = array('html'=> '&nbsp;', 'class' => 'prev', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page - 1)), null, true));
            }
            for ($i = 0; $i < $pages; $i++) {
                $arPages2[] = array('html'=> $i + 1, 'class' => $i == $page ? 'active' : '', 'href' => $i == $page ? '' : $this->_helper->url->url(array_merge($arPageAddr, array('page' => $i)), null, true));
            }

            if ($page - 3 > 0) {
                $arPages[] = $arPages2[0];
                $arPages[] = $dots;
                $arPages[] = $arPages2[$page - 1];
                $arPages[] = $arPages2[$page];
            }
            else {
                for ($i = 0; $i <= $page; $i++) $arPages[] = $arPages2[$i];
            }

            if ($pages - $page > 4) {
                $arPages[] = $arPages2[$page + 1];
                $arPages[] = $dots;
                $arPages[] = $arPages2[$pages - 1];
            }
            else {
                for ($i = $page + 1; $i < $pages; $i++) $arPages[] = $arPages2[$i];
            }

            if ($page < $pages - 1) {
                $arPages[] = array('html'=> '&nbsp;', 'class' => 'next', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page + 1)), null, true));
            }

            $this->view->pages = $arPages;
        }
        $this->view->cpage = $arCurrPageAddr;
    }

    public function starredAction()
    {
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_redirect("/");
            return;
        }

	    $oSearch = new Search();

        $page = intval($this->getRequest()->getParam('page', 0));
        $perpage = 15;
        $total = $oSearch->getClientStarredCount($this->_auth->Id);
        $pages = intval(ceil($total / $perpage));

        if ($page >= $pages) $page = $pages - 1;
        if ($page < 0) $page = 0;

        $arCurrPageAddr = array('controller' => 'client', 'action' => 'starred', 'page' => $page);
        $this->view->cpage = $arCurrPageAddr;
        $list = $oSearch->getClientStarredResult($this->_auth->Id, $page, $perpage);

        $this->view->list = $list;
        $arPageAddr = array('controller' => 'client', 'action' => 'starred', 'module' => 'default');

        $dots = array('html'=> '...', 'class' => '', 'href' => '');
        if (count($list)) {
            $arPages = array();
            $arPages2 = array();
            if ($page > 0) {
                $arPages[] = array('html'=> '&nbsp;', 'class' => 'prev', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page - 1)), null, true));
            }
            for ($i = 0; $i < $pages; $i++) {
                $arPages2[] = array('html'=> $i + 1, 'class' => $i == $page ? 'active' : '', 'href' => $i == $page ? '' : $this->_helper->url->url(array_merge($arPageAddr, array('page' => $i)), null, true));
            }

            if ($page - 3 > 0) {
                $arPages[] = $arPages2[0];
                $arPages[] = $dots;
                $arPages[] = $arPages2[$page - 1];
                $arPages[] = $arPages2[$page];
            }
            else {
                for ($i = 0; $i <= $page; $i++) $arPages[] = $arPages2[$i];
            }

            if ($pages - $page > 4) {
                $arPages[] = $arPages2[$page + 1];
                $arPages[] = $dots;
                $arPages[] = $arPages2[$pages - 1];
            }
            else {
                for ($i = $page + 1; $i < $pages; $i++) $arPages[] = $arPages2[$i];
            }

            if ($page < $pages - 1) {
                $arPages[] = array('html'=> '&nbsp;', 'class' => 'next', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page + 1)), null, true));
            }

            $this->view->pages = $arPages;
        }

    }

    public function deleteSearchServiceAction(){
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        $sid = intval($this->getRequest()->getParam('sid'));
        $cid = intval($this->getRequest()->getParam('cid'));
        $oSearch = new Search();
        $oSearch->deleteClientServiceSearch($sid, $cid, $this->_auth->Id);
        $this->_helper->json(array(), true, false);
    }
    
    public function deleteSearchCatalogAction(){
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        $id = intval($this->getRequest()->getParam('id'));
        $oSearch = new Search();
        $oSearch->deleteClientCatalogSearch($id, $this->_auth->Id);
        $this->_helper->json(array(), true, false);
    }

    public function settingsAction(){
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_redirect("/");
        }
        if(Zend_Auth::getInstance()->hasIdentity()){
            $oUser = new User();
            $profile = $oUser->getClientProfile($this->_auth->Id);
    		$this->view->phone = $profile->Phone;
    		$this->view->name = $profile->Name;

            $this->view->showChangePass = trim($this->_auth->SocialID) == '';

            $email = $profile->Email;
            if (strlen($email) > 22) {
                $pos = strpos($email, "@");
                $prevemail = $email;
                $email = "...".substr($prevemail, $pos);
                $currlength = strlen($email);
                $email = substr($prevemail, 0, 22 - $currlength).$email;
            }

    		$this->view->email = $email;
        }
    }

    public function getClientDataAction(){
        $User = $this->_auth;
        $oUser = new User();
        $profile = $oUser->getClientProfile($User->Id);
        $this->_helper->json(array('name' => $profile->Name, 'phone' => $profile->Phone, 'email' => $profile->Email), true, false);
        return;
    }
    
    public function updateClientDataAction(){
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_USER) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $oUser = new User();
        $oldPhone = $oUser->getPhoneNumber($user->Id);
        $name = strval(trim($this->getRequest()->getParam('name')));
        if ($name == '') {
            $this->_helper->json(array('result' => 'Введите инициалы'), true, false);
        }
        $email = strval(trim($this->getRequest()->getParam('email')));
        if ($email == '' && trim($this->_auth->SocialID) == '') {
            $this->_helper->json(array('result' => 'Введите E-mail!'), true, false);
            return;
        }
        if ($email != '') {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$email) {
                $this->_helper->json(array('result' => 'Неверно введен E-mail'), true, false);
                return;
            }
            if ($oUser->emailExists($email, $user->Id)) {
                $this->_helper->json(array('result' => 'Пользователь с таким E-mail уже зарегистрирован'), true, false);
                return;
            }
        }
        $phone = strval(trim($this->getRequest()->getParam('phone')));
        if ($phone != '') {
            if ($phone != $oldPhone) {
                $this->_helper->json(array('result' => 'Вы не подтвердили код.'), true, false);
            }
        }

        $t = new Obj_DBTable_Users();
        $t->update(array("Email" => $email), 'Id = '.$user->Id);
        $oUser->updateClientProfile($name, $email, $user->Id);
        $this->_helper->json(array(), true, false);
    }

    public function removePhoneAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_USER) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $oUser = new User();
        $oUser->updatePhoneNumber($user->Id, '');
        $this->_helper->json(array('error' => 0), true, false);
    }
    
    public function changePassAction() {
    	$user = $this->_auth;
    	if (!isset($user->UserType) || $user->UserType != USER_TYPE_USER) {
    		$this->_helper->redirector('index','index','default');
    		die();
    	}
    	$oldpass = trim($this->getRequest()->getParam('oldpass'));
    	$newpass = trim($this->getRequest()->getParam('newpass'));
    	$newpass2 = trim($this->getRequest()->getParam('newpass2'));
    	$oUser = new Obj_DBTable_Users();
    	if ($oUser->checkUserPass(Zend_Auth::getInstance()->getIdentity()->Id, $oldpass) != 1) {
    		$this->_helper->json(array('error' => 1, 'text' => 'Неверный пароль'), true, false);
    	}
    	if ($newpass == '' || $newpass != $newpass2) {
    		$this->_helper->json(array('error' => 1, 'text' => 'Неверный новый пароль'), true, false);
    	}
    	$oUser->changePassword(Zend_Auth::getInstance()->getIdentity()->Id, $newpass);
    	$this->_helper->json(array('error' => 0), true, false);
    }
    
}



