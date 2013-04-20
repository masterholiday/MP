<?php
class IndexController extends DefaultBaseController
{
    private $siteUrl = 'http://masterholiday.net/';

    public function indexAction()
    {

       	$Obj_post = new Obj_DBTable_Posts();
       	$this->view->articles = $Obj_post->getPostByLimit('4');

        $catalog = new Catalog();
        $this->view->shortCatalogList = $catalog->getShortList();

        $tab = new Obj_DBTable_ServiceCategories();
        $this->view->topCategories = $tab->getCategoriesForParent(0);
		
		$this->view->headTitle("Мастерская праздников MasterHoliday");
		$this->view->headMeta()->appendName('description', "Мастерская праздников MasterHoliday.");

    }

    public function facebookLoginAction()
    {
        $config = array(
            'consumerId' => '263138630476088',
            'consumerSecret' => '54f3754a401e43e1c97d2b74a1eef270',
            'callbackUrl' => $this->siteUrl.'index/facebook-login',
            'display' => SAuth_Adapter_Facebook::DISPLAY_PAGE,
        );

        $auth = Zend_Auth::getInstance();
        //создаем адаптер
        $adapter = new SAuth_Adapter_Facebook($config);
        //авторизация
        $result  = $auth->authenticate($adapter);
        //если прошла - редирект на index
        if ($result->isValid()) {
            $identity = $result->getIdentity();
            if ($this->doSocialLogin('fb', $identity['id'], '', $identity['name'])) {
                $this->_helper->layout()->disableLayout();
                ?>
                <html><head>
                    <script>
                        function refreshParent() {window.opener.getUserHeader(); window.opener.hideUserPopup(); window.close();}
                    </script>
                </head><body onload="refreshParent();"></body></html>
                <?php
                die();
            }
            else {
                ?>
            <html><head></head><body onload="window.close();"></body></html>
            <?php
                die();
            }

        } else {
            //если есть ошибки показываем их
            ?>
        <html><head></head><body onload="window.close();"></body></html>
        <?php
            die();
        }
    }

    public function vkLoginAction()
    {
        $config = array(
            'consumerId'			=> '3024206',
            'consumerSecret'		=> 'y1QXcqcIHzVnV05dwBM2',
            'callbackUrl' => $this->siteUrl.'index/vk-login',
        );

        if ($this->getRequest()->getParam('error') == 'access_denied') {
            ?>
        <html><head></head><body onload="window.close();"></body></html>
        <?php
            die();
        }

        $auth = Zend_Auth::getInstance();
        //создаем адаптер
        $adapter = new SAuth_Adapter_Vkontakte($config);
        //авторизация
        $result  = $auth->authenticate($adapter);
        //если прошла - редирект на index
        if ($result->isValid()) {
            $identity = $result->getIdentity();
            if ($this->doSocialLogin('vk', $identity['uid'], '', $identity['first_name'].' '.$identity['last_name'])) {
                $this->_helper->layout()->disableLayout();
                ?>
                <html><head>
                    <script>
                        function refreshParent() {window.opener.getUserHeader(); window.opener.hideUserPopup(); window.close();}
                    </script>
                </head><body onload="refreshParent();"></body></html>
                <?php
                die();
            }
            else {
                ?>
            <html><head></head><body onload="window.close();"></body></html>
            <?php
                die();
            }

        } else {
            //print_r($result);
            //die();
            ?>
        <html><head></head><body onload="window.close();"></body></html>
        <?php
            die();
        }
    }

    public function googleLoginAction()
    {
        /*
         *
        OAuth Consumer Key:	 test.masterholiday.net
        OAuth Consumer Secret:	 3p_ZBUFPVOuIjbTUTIORsnFl
         *
         * */
        $aGoogleConfig = array(
            'callbackUrl'       => $this->siteUrl.'index/google-login',
            'siteUrl'           => 'https://www.google.com/accounts/',
            'authorizeUrl'      => 'https://www.google.com/accounts/OAuthAuthorizeToken',
            'requestTokenUrl'   => 'https://www.google.com/accounts/OAuthGetRequestToken',
            'accessTokenUrl'    => 'https://www.google.com/accounts/OAuthGetAccessToken',
            'consumerKey'       => 'test.masterholiday.net',
            'consumerKey'       => '277786433425.apps.googleusercontent.com',
            'consumerSecret'    => '3p_ZBUFPVOuIjbTUTIORsnFl',
            'consumerSecret'    => 'CzXeDFxClCADXL9RxCD8zuBz'
        );

        $consumer = new Zend_Oauth_Consumer($aGoogleConfig);
        $token = null;

        if (isset($_GET['oauth_token'])) {
            try {
                $token = $consumer->getAccessToken( $_GET, unserialize($_SESSION['GOOGLE_REQUEST_TOKEN']) );

            }
            catch (Exception $e) {
                $this->_helper->layout()->disableLayout();
                ?>
                <html><head></head><body onload="window.close();"></body></html>
                <?php
                die();
            }
        }

        if (!$token) {
            $token = $consumer->getRequestToken(array('scope' => 'http://www-opensocial.googleusercontent.com/api/people/ https://www.googleapis.com/auth/userinfo#email'));
            $_SESSION['GOOGLE_REQUEST_TOKEN'] = serialize($token);
            $consumer->redirect();
        }
        else {
            $client = $token->getHttpClient($aGoogleConfig);
            $client->setUri('https://www-opensocial.googleusercontent.com/api/people/@me/@self');
            $client->setMethod(Zend_Http_Client::GET);
            $response = $client->request();
            $data = Zend_Json::decode($response->getBody());
            $client = $token->getHttpClient($aGoogleConfig);
            $client->setUri('https://www.googleapis.com/userinfo/email');
            $client->setMethod(Zend_Http_Client::GET);
            $response = $client->request();
            $emailData = explode('&', $response->getBody());
            if ($emailData) {
                foreach ($emailData as $sRow) {
                    $aRow = explode('=', $sRow);
                    $data[$aRow[0]] = $aRow[1];
                }
            }
            $oUser = new User();
            if ($oUser->emailExists($data['email']) && !$this->doSocialLogin('google', $data['entry']['id'], $data['email'], $data['entry']['name']['formatted'], true)) {
                $this->_helper->layout()->disableLayout();
                ?>
                <html><head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <script>
                        function refreshParent() {window.opener.showError('Пользователь с таким E-mail уже зарегистрирован'); window.close();}
                    </script>
                </head><body onload="refreshParent();"></body></html>
                <?php
                die();
            }
            if ($this->doSocialLogin('google', $data['entry']['id'], $data['email'], $data['entry']['name']['formatted'])) {
                $this->_helper->layout()->disableLayout();
                ?>
                <html><head>
                    <script>
                        function refreshParent() {window.opener.getUserHeader(); window.opener.hideUserPopup(); window.close();}
                    </script>
                </head><body onload="refreshParent();"></body></html>
                <?php
                die();
            }
            else {
                die('ERROR');
            }
        }
    }

    public function getSubcategoriesAction(){
        $id = $this->getRequest()->getParam('id');
        $tab = new Obj_DBTable_ServiceCategories();
        $list = $tab->getCategoriesForParent($id);
        $this->_helper->json($list, true, false);
    }


    public function registrationAction(){
    	
    }

    private function doSocialLogin($provider, $id, $email, $name, $check = false) {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')->setIdentityColumn('SocialID')->setCredentialColumn('Social');
        $authAdapter->setCredentialTreatment("? AND Active = 1 AND UserType <> ".USER_TYPE_MANAGER);
        $authAdapter->setIdentity($id)->setCredential($provider);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            $identity = $authAdapter->getResultRowObject();
            $authStorage = $auth->getStorage();
            $authStorage->write($identity);
            return true;
        } else {
            if ($check) return false;
            $oUser = new User();
            $oUser->addSocialUser($provider, $id, $email, $name);
            return $this->doSocialLogin($provider, $id, $email, $name);
        }
    }
    
    public function exitAction(){
        $oUser = new User();
        $oUser->doExit();
        $this->_helper->redirector('index', 'index');
    }
    
    public function doRegisterAction() {
        $email = trim($this->getRequest()->getParam('email', ''));
        $pass = strval(trim($this->getRequest()->getParam('pass', '')));
        $name = strval(trim($this->getRequest()->getParam('name', '')));
        $agree = intval(trim($this->getRequest()->getParam('agree', 0))) == 1;

        if ($name == 'Инициалы' || $name == '') {
            $this->_helper->json(array('result' => 'Введите инициалы'), true, false);
            return;
        }

        if ($pass == '') {
            $this->_helper->json(array('result' => 'Введите пароль'), true, false);
            return;
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->_helper->json(array('result' => 'Неверно введен E-mail'), true, false);
            return;
        }

        $oUser = new User();
        if ($oUser->emailExists($email)) {
            $this->_helper->json(array('result' => 'Пользователь с таким E-mail уже зарегистрирован'), true, false);
            return;
        }

        if (!$agree) {
            $this->_helper->json(array('result' => 'Вы должны согласится с Правилами Сервиса'), true, false);
            return;
        }

        $uid = $oUser->addSimpleUser($name, $email, $pass);

        if (!$uid || $this->simpleAuth($email, $pass)) {
            $this->_helper->json(array('result' => 'Вы успешно зарегестрировались!', 'ok' => 'ok'), true, false);
        }
        else {
            $this->_helper->json(array('result' => 'Ошибка регистрации'), true, false);
        }
    }


    private function simpleAuth($email, $pass) {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')
            ->setIdentityColumn('Email')
            ->setCredentialColumn('Password');
        $authAdapter->setCredentialTreatment("MD5(?) AND Active = 1 AND SocialID = '' AND Social = ''");
        $authAdapter->setIdentity($email)
            ->setCredential($pass);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

        if ($result->isValid()) {
            $identity = $authAdapter->getResultRowObject();
            $authStorage = $auth->getStorage();
            $authStorage->write($identity);
            return true;

        }else{
            return false;
        }
    }


    public function doEventorRegisterAction() {
        $regtry = new Zend_Session_Namespace('evregtry');
        $phone = isset($regtry->evNumber) ? trim($regtry->evNumber) : '';
        $email = trim($this->getRequest()->getParam('email', ''));
        $pass = strval(trim($this->getRequest()->getParam('pass', '')));
        $name = strval(trim($this->getRequest()->getParam('name', '')));
        $agree = intval(trim($this->getRequest()->getParam('agree', 0))) == 1;
        $city = intval(trim($this->getRequest()->getParam('city', 0)));

        if ($name == 'Название' || $name == '') {
            $this->_helper->json(array('result' => 'Введите Название'), true, false);
            return;
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->_helper->json(array('result' => 'Неверно введен E-mail'), true, false);
            return;
        }

        $oUser = new User();
        if ($oUser->emailExists($email)) {
            $this->_helper->json(array('result' => 'Пользователь с таким E-mail уже зарегистрирован'), true, false);
            return;
        }

        if ($city == 0) {
            $this->_helper->json(array('result' => 'Введите Город'), true, false);
            return;
        }

        if ($phone == '') {
            $this->_helper->json(array('result' => 'Введите телефон'), true, false);
            return;
        }

        if ($pass == '') {
            $this->_helper->json(array('result' => 'Введите пароль'), true, false);
            return;
        }

        if (!$agree) {
            $this->_helper->json(array('result' => 'Вы должны согласится с Правилами Сервиса'), true, false);
            return;
        }

        $key = md5(serialize(array($email, $name, $phone)));
        $uid = $oUser->addEventor($name, $email, $pass, $city, $phone, $key);

        if ($uid !== false) {
            $this->sendClientRegistrationEmail($email, $key);
            unset($regtry->evNumber);
            unset($regtry->smsBlockedUntil);
            unset($regtry->smsTryCount);
            unset($regtry->smsTryCount2);
            $this->_helper->json(array('result' => 'На почту было отправлено сообщение о подтверждении регистрации!', 'ok' => 'ok'), true, false);
        }
        else {
            $this->_helper->json(array('result' => 'Ошибка регистрации'), true, false);
        }
    }



    public function activregAction(){
        $kay = $this->getRequest()->getParam('kay', '');
        $oUser = new User();
        if ($oUser->doKeyAuth($kay)) {
            $this->_redirect("/iventor/");
        }
        else {
            $this->_redirect("/");
        }
    }

    public function restorePassAction() {
        $email = trim($this->getRequest()->getParam('email'));
        $user = new Obj_DBTable_Users();
        $pass = $user->restorePassword($email);
        if ($pass) {
            $html = file_get_contents(APPLICATION_PATH.'/../htmlletters/passrecovery.html');
            $html = str_replace('%NEWPASS%', $pass, $html);
            $this->sendHTMLLetter($html, 'Восстановление пароля', $email);
            $this->_helper->json(array('result' => 'Новый пароль выслан Вам на почтовый ящик', 'ok' => 'ok'), true, false);
        }
        else {
            $this->_helper->json(array('result' => 'Ошибка восстановления'), true, false);
        }
    }

    public function sendClientRegistrationEmail($email, $key)
    {
        $link = 'http://masterholiday.net/index/activreg?kay='.$key;
        $html = file_get_contents(APPLICATION_PATH.'/../htmlletters/regisclient.html');
        $html = str_replace('%LINK%', $link, $html);
        $html = str_replace('%LINKNAME%', $link, $html);
        $this->sendHTMLLetter($html, "Регистрация", $email);
    }


    public function smsAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        $justTest = intval($this->getRequest()->getParam('istest', 0));
        $oUser = new User();
        $info = $oUser->getBlockedInfo($this->_auth->Id);
        $blockedUntil = strtotime($info->smsBlockedUntil);
        if ($blockedUntil !== false) {
            if ($blockedUntil - time() <= 0) {
                $oUser->clearBlockSMSPopup($this->_auth->Id);
                $blockedUntil = false;
            }
            else {
                $seconds = $blockedUntil - time();
                $this->_helper->json(array('seconds' => $seconds, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка регистрации через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
        }
        if ($blockedUntil === false) {
            if ($info->PhoneCodeRequests >= 3) {
                $oUser->blockSMSPopup($this->_auth->Id);
                $this->_helper->json(array('seconds' => 15*60, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
            if ($justTest) {
                $this->_helper->json(array(), true, false);
            }
            $phone = $oUser->checkPhone($this->getRequest()->getParam('phone', ''));
            if ($phone === false) {
                $this->_helper->json(array('error' => 'Неверный номер телефона'), true, false);
            }
            if ($oUser->phoneExists($phone)) {
                $this->_helper->json(array('error' => 'Такой номер уже зарегестрирован'), true, false);
            }
            if (!$oUser->sendCode($phone)) {
                $this->_helper->json(array('error' => 'Произошла ошибка'), true, false);
            }
            $oUser->addCodeRequest($this->_auth->Id);
            $this->_helper->json(array(), true, false);
        }
        $this->_helper->json(array('error' => 'Ошибка'), true, false);
    }

    public function smsCheckAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        $oUser = new User();
        $info = $oUser->getBlockedInfo($this->_auth->Id);
        $blockedUntil = strtotime($info->smsBlockedUntil);
        if ($blockedUntil !== false) {
            if ($blockedUntil - time() <= 0) {
                $oUser->clearBlockSMSPopup($this->_auth->Id);
                $blockedUntil = false;
            }
            else {
                $seconds = $blockedUntil - time();
                $this->_helper->json(array('seconds' => $seconds, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
        }
        if ($blockedUntil === false) {
            if ($info->PhoneCodeEnter >= 3) {
                $oUser->blockSMSPopup($this->_auth->Id);
                $this->_helper->json(array('seconds' => 15*60, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
            $code = $this->getRequest()->getParam('code', '');
            $phone = $oUser->checkCode($code);
            if ($phone) {
                $oUser->updatePhoneNumber($this->_auth->Id, $phone->Number);
                $oUser->clearBlockSMSPopup($this->_auth->Id, $code);
                $this->_helper->json(array(), true, false);
            }
            else {
                if ($info->PhoneCodeEnter >= 2) {
                    $oUser->blockSMSPopup($this->_auth->Id);
                    $this->_helper->json(array('seconds' => 15*60, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
                }

                $oUser->addCodeResponse($this->_auth->Id, $info->PhoneCodeEnter + 1);
                $this->_helper->json(array('error' => 'Неверный код'), true, false);
            }
            $this->_helper->json(array(), true, false);
        }
        $this->_helper->json(array('error' => 'Ошибка'), true, false);
    }



    public function evsmsAction() {

        $regtry = new Zend_Session_Namespace('evregtry');
        $regtry->evNumber = '';

        $blockedUntil = isset($regtry->smsBlockedUntil) ? intval($regtry->smsBlockedUntil) : false;

        if ($blockedUntil !== false) {
            if ($blockedUntil - time() <= 0) {
                unset($regtry->smsBlockedUntil);
                unset($regtry->smsTryCount);
                unset($regtry->smsTryCount2);
                $blockedUntil = false;
            }
            else {
                $seconds = $blockedUntil - time();
                $this->_helper->json(array('seconds' => $seconds, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка регистрации через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
        }
        $tryCount = isset($regtry->smsTryCount) ? intval($regtry->smsTryCount) : 0;
        if ($blockedUntil === false) {
            if ($tryCount >= 3) {
                $regtry->smsTryCount = 0;
                $regtry->smsTryCount2 = 0;
                $regtry->smsBlockedUntil = time() + 15*60;
                $this->_helper->json(array('seconds' => 15*60, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }

            $oUser = new User();

            $phone = $oUser->checkPhone($this->getRequest()->getParam('phone', ''));
            if ($phone === false) {
                $this->_helper->json(array('error' => 'Неверный номер телефона'), true, false);
            }
            if ($oUser->phoneExists($phone)) {
                $this->_helper->json(array('error' => 'Такой номер уже зарегестрирован'), true, false);
            }

            if (!$oUser->sendCode($phone)) {
                $this->_helper->json(array('error' => 'Произошла ошибка'), true, false);
            }


            $tryCount++;
            $regtry->smsTryCount = $tryCount;
            $this->_helper->json(array(), true, false);
        }
        $this->_helper->json(array('error' => 'Ошибка'), true, false);
    }

    public function evsmsCheckAction() {
        $regtry = new Zend_Session_Namespace('evregtry');

        $blockedUntil = isset($regtry->smsBlockedUntil) ? intval($regtry->smsBlockedUntil) : false;

        if ($blockedUntil !== false) {
            if ($blockedUntil - time() <= 0) {
                unset($regtry->smsBlockedUntil);
                unset($regtry->smsTryCount);
                unset($regtry->smsTryCount2);
                $blockedUntil = false;
            }
            else {
                $seconds = $blockedUntil - time();
                $this->_helper->json(array('seconds' => $seconds, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
        }
        $tryCount = isset($regtry->smsTryCount2) ? intval($regtry->smsTryCount2) : 0;
        if ($blockedUntil === false) {
            if ($tryCount >= 3) {
                $regtry->smsTryCount = 0;
                $regtry->smsTryCount2 = 0;
                $regtry->smsBlockedUntil = time() + 15*60;
                $this->_helper->json(array('seconds' => 15*60, 'error' => 'Внимание! Вы три раза допустили ошибку. Следующая попытка через 15 минут. Ознакомтесь пока с Правилами сервиса и FAQ'), true, false);
            }
            $oUser = new User();
            $code = $this->getRequest()->getParam('code', '');
            $phone = $oUser->checkCode($code);
            if ($phone) {
                $regtry->evNumber = $phone->Number;
                unset($regtry->smsBlockedUntil);
                unset($regtry->smsTryCount);
                unset($regtry->smsTryCount2);
                $this->_helper->json(array(), true, false);
            }
            else {
                $tryCount++;
                $regtry->smsTryCount2 = $tryCount;
                $this->_helper->json(array('error' => 'Неверный код'), true, false);
            }
            $this->_helper->json(array(), true, false);
        }
        $this->_helper->json(array('error' => 'Ошибка'), true, false);
    }



    public function autocompliteAction(){
    	
        $qu = strval($this->getRequest()->getParam('query'));
        $country = intval($this->getRequest()->getParam('country'));
        $Obj_city = new Obj_DBTable_Citys();
        $row = $Obj_city->getCityAutocomplite($qu, $country);

        $suggestions = array();
        $ids = array();
        foreach ($row as $v) {
            $suggestions[] = $v['cityname'].", ".$v['name'];
            $ids[] = $v['city_id']."|".$v['country_id'];
        }

        $this->_helper->json(array('query' => $qu, 'suggestions' => $suggestions, 'data' => $ids));
        return;
    }
    
    public function postAction(){
	$translit = new Zend_Filter_Translit();
    	$id = intval(trim($this->getRequest()->getParam('id')));
		$titlePar = $this->getRequest()->getParam('title');
    	$Obj_post = new Obj_DBTable_Posts();
    	$Obj_post_cat = new Obj_DBTable_PostCategories();
    	$post = $Obj_post->getPosts($id);
        if (!$post) {
            header('Location: /404.html');
            die();
        }
		$title = $translit->filter($post['Title']);
		if ($title!=$titlePar) {
            header('Location: /404.html');
            die();
       }

    	$this->view->cat = $Obj_post_cat->getPostCategories($post['CategoryId']);
    	$this->view->post = $post;
    }
    
    public function allPostAction(){
    	$Obj_post = new Obj_DBTable_Posts();
    	$post = $Obj_post->getAllPost();
    	
    	$page = intval(abs($this->getRequest()->getParam('page', 0)));
    	$perpage = 10;
    	$total = count($post);
    	$pages = intval(ceil($total / $perpage));
    	
    	if ($page >= $pages) $page = $pages - 1;
    	if ($page < 0) $page = 0;
    	$list = $Obj_post->getPostByLimit($perpage, $page);
    	$this->view->user_search = $list;
    	$this->view->post = $list;
    	//$list = $transactions->getIventorTransactions($user->Id, $sType == "new" ? false : true, $page, $perpage);
    	$this->view->list = $list;
    	$arPageAddr = array('controller' => 'index', 'action' => 'all-post');
    	$dots = array('html'=> '...', 'class' => '', 'href' => '');
    	if (count($list)) {
    		$arPages = array();
    		$arPages2 = array();
    		if ($page > 0) {
    			$arPages[] = array('html'=> '&nbsp;', 'class' => 'prev', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page - 1)), null, true));
    		}
    		$j = 0;
    		for ($i = 0; $i < $pages; $i++) {
    			if ($i == $page) $j = $i;
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
		
		$this->view->headTitle("Все статьи – MasterHoliday");
		$this->view->headMeta()->appendName('description', "Все статьи – MasterHoliday");
    }
    
    public function getIdentAction(){
        //die('IndexController-getIdentAction');
        if(Zend_Auth::getInstance()->hasIdentity()){
    	    $this->_helper->json(array('result' => 'ok'), true, false);
    		return;
    	}else{
    	    $_SESSION['head'] = $this->getRequest()->getParam('head');
    	    $_SESSION['row'] = $this->getRequest()->getParam('row');
    	    $_SESSION['search'] = 1;
    	   
    	    $this->_helper->json(array('result' => 'not'), true, false);
    	    return;
    	}
    	
    }
    
    public function getSessAction(){
        $this->_helper->json(array('head' => $_SESSION['head'], 'row' => $_SESSION['row'], 'search' => $_SESSION['search']), true, false);
        return;
    }
    
    public function nextPostAction(){
    	$start = intval(trim($this->getRequest()->getParam('start')));
    	$Obj_post = new Obj_DBTable_Posts();
    	$post = $Obj_post->getPostByLimit(4, $start);
    	$i = 0;
    	foreach ($post as $p){
    	    if($post[$i]['Image'] == ''){
    	    	$post[$i]['Image'] = 'images/148x144.gif';
    	    }else{
    	        $post[$i]['Image'] = 'img/articles/148x144_'.$post[$i]['Image'];
    	    }
    	    
    		$post[$i]['IntroText'] = substr(strip_tags($post[$i]['IntroText']),0,254);
    		$post[$i]['DateCreated'] = date('d.m.Y', strtotime($post[$i]['DateCreated']));
    		$i++;
    	}
    	$this->_helper->json(array('post' => $post), true, false);
    	return;
    }
    
    
    public function getCountryPriceAction(){
    	$city_old_id = intval(trim($this->getRequest()->getParam('city_old_id')));
    	$city_new_id = intval(trim($this->getRequest()->getParam('city_new_id')));
    	//echo $city_old_id.'<br/>'.$city_new_id;
    	$Obj_city = new Obj_DBTable_Citys();
    	$country_old = $Obj_city->getCountryId($city_old_id);
    	$country_new = $Obj_city->getCountryId($city_new_id);
    	
    	if($country_old['country_id'] == $country_new['country_id']){
    		$this->_helper->json(array('rez' => 0));
    	}else{
    	    $Obj_price = new Obj_DBTable_ServicesPrices();
    	    $price = $Obj_price->getAllPriceByCountry($country_new['country_id']);
    	    $this->_helper->json(array('rez' => 1, 'country' => $country_new['country_id'], 'price' => $price));
    	}
    	
    }


}



