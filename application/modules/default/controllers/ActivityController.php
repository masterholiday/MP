<?php

class ActivityController extends DefaultBaseController
{
   
    public function searchFormAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }

        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }

        $oUser = new User();

        $phoneNumber = $oUser->getPhoneNumber($this->_auth->Id);
        $clientProfile = $oUser->getClientProfile($this->_auth->Id);
        if ($phoneNumber == '') {
            $this->_helper->json(array('error' => 'Нужен номер телефона!'), true, false);
        }

        $params = $this->getRequest()->getParam('params');

        if (!isset($params['city']) || !isset($params['city']['id'])) {
            $this->_helper->json(array('error' => 'Не выбран город!'), true, false);
        }
        if (!isset($params['date']) || !isset($params['date']['y']) || !isset($params['date']['d']) || !isset($params['date']['m'])) {
            $this->_helper->json(array('error' => 'Не выбрана дата!'), true, false);
        }
        $date = mktime(0, 0, 0, (int)$params['date']['m'], (int)$params['date']['d'], (int)$params['date']['y']);
        if ($date < (time() - 3600*24)) {
            $this->_helper->json(array('error' => 'Не выбрана дата!'), true, false);
        }
        if (!isset($params['eventname']) || trim($params['eventname']) == '') {
            $this->_helper->json(array('error' => 'Не задано название!'), true, false);
        }
        if (!isset($params['servises']) || !is_array($params['servises']) || count($params['servises']) < 1) {
            $this->_helper->json(array('error' => 'Не выбраны услуги!'), true, false);
        }

        foreach ($params['servises'] as $s) {
            if (!isset($s['subcatid']) || !isset($s['minprice']) || !isset($s['maxprice'])) {
                $this->_helper->json(array('error' => 'Ошибка услуги!'), true, false);
            }
        }

        $oSearch = new Search();
        $clientName = $oUser->getClientName($this->getAuth()->Id);
        $sid = $oSearch->addSearch($date, $params['city']['id'], $params['eventname'], $this->getAuth()->Id, 0, $phoneNumber);
        $arSentNewSearch = array();
        $arSentNewSearch2 = array();

        $oServiceCategories = new Obj_DBTable_ServiceCategories();

        foreach ($params['servises'] as $service) {
            $eventors = $oSearch->findEventors($params['city']['id'], $service['subcatid'], $service['minprice'], $service['maxprice'], $this->_auth->Id);
            if (count($eventors) < 1) {
                $oSearch->addSearchService($sid, $service['subcatid'], 0, $service['minprice'], $service['maxprice'], $service['description'], $this->_auth->Id);
            }
            else {
                foreach ($eventors as $ev) {
                    $oSearch->addSearchService($sid, $service['subcatid'], $ev->IventorId, $service['minprice'], $service['maxprice'], $service['description'], $this->_auth->Id);
                    $oUser->updateSearchRequests($ev->IventorId);
                    if (intval($ev->prevSCount) < 1 && !in_array($ev->IventorId, $arSentNewSearch2)) {
                        $arSentNewSearch2[] = $ev->IventorId;
                        $ev->CompanyPhone = $oUser->checkPhone($ev->CompanyPhone);
                        if ($ev->CompanyPhone !== false) $this->sendSMSToEventor($ev->CompanyPhone, "Запрос от: ".$clientName." +".$phoneNumber);
                    }
                    if (!in_array($ev->IventorId, $arSentNewSearch)) {
                        $arSentNewSearch[] = $ev->IventorId;

                        $subcat = $oServiceCategories->getCategories($service['subcatid']);
                        $pcat = $oServiceCategories->getCategories($subcat['ParentId']);

                        $arrEmailData = array();
                        $arrEmailData['clientname'] = $clientName;
                        $arrEmailData['eventname'] = $params['eventname'];
                        $arrEmailData['eventdate'] = date('d.m.Y', $date);
                        $arrEmailData['eventcategory'] = $pcat['CategoryName'].' &gt; '.$subcat['CategoryName'];
                        $arrEmailData['eventdescription'] = $service['description'];
                        $arrEmailData['clientphone'] = $phoneNumber;
                        $arrEmailData['clientemail'] = trim($clientProfile->Email);
                        $this->sendHTMLLetter(file_get_contents(APPLICATION_PATH.'/../htmlletters/search_complex.html'), 'Новая сделка', $ev->Email, $arrEmailData);
                    }
                }
            }
        }
        if (isset($params['saveNumber']) && intval($params['saveNumber']) != 2) {
            $oUser->updatePhoneNumber($this->_auth->Id, '');
        }
        $this->_helper->json(array(), true, false);
    }

    public function sendSMSToEventor($to, $text) {
        if (!function_exists('send_sms')) include APPLICATION_PATH.'/../library/Sms/smscapi.php';
        //echo $to." ".$text."\n";
        send_sms($to, $text);
    }
    
	public function addSubscriberAction(){
		$email = strval(trim($this->getRequest()->getParam('email')));
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if ($email) {
			$sub = new Obj_DBTable_Subscriber();
			$select = $sub->select()
			->where('Email = ?', $email);
			$sel = $select->query();
		 
			$result = $sel->fetchObject();
			if($result == false){
				$sub->addSubscriber($email);
			}
		}
		$this->_helper->json(array('ok' => 'ok'), true, false);
	}	

}



