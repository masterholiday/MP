<?php
class DefaultBaseController extends Zend_Controller_Action {
	
	var $_isAJAX = false;
    var $_auth;
	
	protected $_needbeloggedin = false;

    public function getAuth() {
        return $this->_auth;
    }
	
	public function preDispatch() {
        $redirect = trim($this->getRequest()->getParam('redirect'), '');
        if ($redirect !== '') {
            switch ($redirect) {
                case 'account':$this->view->iventorRedirect = $this->_helper->layout()->getView()->url(array('controller' => 'iventor', 'module' => 'default', 'action' => 'account'), null, true); break;
                case 'scomplex':$this->view->iventorRedirect = $this->_helper->layout()->getView()->url(array('controller' => 'iventor', 'module' => 'default', 'action' => 'searches', 'type' => 'complex'), null, true); break;
                case 'scatalog':$this->view->iventorRedirect = $this->_helper->layout()->getView()->url(array('controller' => 'iventor', 'module' => 'default', 'action' => 'searches', 'type' => 'catalog'), null, true); break;
            }
        }
        $bannershow = isset($_COOKIE['bannershow']) ? false : true;
        if ($bannershow) {
            $oBanner = new Obj_DBTable_Banner();
            $iBanner = $oBanner->getBanner();
            if ($iBanner !== false) {
                $iBanner->filetype = strpos(strtolower($iBanner->filename), ".swf") !== false ? 'flash' : 'image';
                $this->view->banner = $iBanner;
                $this->_helper->layout()->getView()->headScript()->appendFile($this->_helper->layout()->getView()->baseUrl().'/js/banner.js');
            }
        }

        if ($this->getRequest()->getControllerName() == 'catalog') {
            $this->_helper->layout()->setLayout('layoutcatalog');
        }
        $Obj_iventorinfo = new Obj_DBTable_InventorInfo();
        $this->view->lastiventors = $Obj_iventorinfo->getLastParters();

        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('Frontend'));
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $oUser = Zend_Auth::getInstance()->getIdentity();
            $this->_auth = $oUser;
            $this->view->auth = $oUser;
            if ($this->_auth->UserType == USER_TYPE_INVENTOR && isset($this->view->iventorRedirect)) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrl(str_replace($this->_helper->layout()->getView()->baseUrl(), '', $this->view->iventorRedirect))->redirectAndExit();
            }
        }
        else {
            if (isset($_COOKIE['rememberme'])) {
                $user = new User();
                if ($user->doCookieAuth($_COOKIE['rememberme'])) {
                    $oUser = Zend_Auth::getInstance()->getIdentity();
                    $this->_auth = $oUser;
                    $this->view->auth = $oUser;
                }
            }
            if ($this->_needbeloggedin) {
                $this->_helper->redirector('index', 'index', 'default');
            }
        }
	}

    public function postDispatch() {
        $authorization = new Zend_Session_Namespace('authorization');
        if (isset($authorization->rememberMeCode)) {
            setcookie("rememberme", $authorization->rememberMeCode, time() + 3600*24*7);
            unset($authorization->rememberMeCode);
        }
    }

    public function sendHTMLLetter($html, $subject, $email, $arr = false) {
        /*$config = array ('auth' => 'login', 'port' => '250',
            'username' => 'eventor@masterholiday.net',
            'password' => 'HdtGVo5tw');

        $transport = new Zend_Mail_Transport_Smtp('mail.masterholiday.net', $config);
        $mail = new Zend_Mail('UTF-8');*/
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                $html = str_replace("%".$k."%", $v, $html);
            }
        }
       /* $mail->setBodyHtml($html);
        $mail->setFrom('eventor@masterholiday.net', 'Мастерская Праздников');
        $mail->addTo($email);
        $mail->setSubject($subject);
        try {
                $mail->send($transport);
        }
        catch (Exception $e){
            //file_put_contents(APPLICATION_PATH.'/../public/email.txt', $html."\r\n", FILE_APPEND);
        }*/
		
		
		
		
		$api_key = "5615unk8kntwr449i5c87cbbw44rd57bu3u5uuoo";

			// Параметры сообщения
			// Если скрипт в кодировке UTF-8, не используйте iconv
			$email_from_name = "Мастерская Праздников";
			$email_from_email = "eventor@masterholiday.net";
			$email_to = $email;
			$email_body = $html;
			//urlencode(iconv('cp1251', 'utf-8',"Интернет сервис для поиска целевых клиентов"));
			$email_subject = $subject;
			$list_id = "937073";

			// Создаём POST-запрос
			$POST = array (
			  'api_key' => $api_key,
			  'email' => $email_to,
			  'sender_name' => $email_from_name,
			  'sender_email' => $email_from_email,
			  'subject' => $email_subject,
			  'body' => $email_body,
			  'list_id' => $list_id
			);

			// Устанавливаем соединение
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_URL, 
						'http://api.unisender.com/ru/api/sendEmail?format=json');
			$result = curl_exec($ch);

			if ($result) {
			  // Раскодируем ответ API-сервера
			  $jsonObj = json_decode($result);

			  if(null===$jsonObj) {
				// Ошибка в полученном ответе
				//echo "Invalid JSON";

			  }
			  elseif(!empty($jsonObj->error)) {
				// Ошибка отправки сообщения
				//echo "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";

			  } else {
				// Сообщение успешно отправлено
				//return 1;
				//echo "Email message is sent. Message id " . $jsonObj->result->email_id;

			  }
			} else {
			  // Ошибка соединения с API-сервером
			 // echo "API access error";
			}
		
		
		
		
    }

    public function hideBannerAction() {
        setcookie("bannershow", "1", 0, "/");
        $this->_helper->json(array(), true, false);
    }
}