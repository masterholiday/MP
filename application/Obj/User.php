<?php
class User {

    public function emailExists($email, $uid = 0) {
        $t = new Obj_DBTable_Users();
        return $t->emailExists($email, $uid);
    }

    public function addSimpleUser($name, $email, $pass) {
        $t = new Obj_DBTable_Users();
        $arr = array(
            'Email' => $email,
            'Password' => md5($pass),
            'RegistrationDate' => new Zend_Db_Expr('NOW()'),
            'Social' => '',
            'UserType' => USER_TYPE_USER,
            'Active' => 1,
            'ActiveKay' => '',
            'SocialID' => ''
        );
        $uid = $t->insert($arr);
        if (!$uid) return false;
        $t2 = new Obj_DBTable_Profile();
        $arr2 = array(
            'UserId' => $uid,
            'Name' => $name,
            'Phone' => '',
            'Email' => $email
        );
        $pid = $t2->insert($arr2);
        if (!$pid) return false;
        return $uid;
    }

    public function addEventor($name, $email, $pass, $city, $phone, $key) {
        $t = new Obj_DBTable_Users();
        $arr = array(
            'Email' => $email,
            'Password' => md5($pass),
            'RegistrationDate' => new Zend_Db_Expr('NOW()'),
            'Social' => '',
            'UserType' => USER_TYPE_INVENTOR,
            'Active' => 0,
            'ActiveKay' => $key,
            'SocialID' => ''
        );
        $uid = $t->insert($arr);
        if (!$uid) return false;
        $ts = new Obj_DBTable_Settings();
        $dayso = $ts->getValue('premiumdays');
        $days = 0;
        if ($dayso) $days = intval($dayso->KeyValue);
        $t2 = new Obj_DBTable_InventorInfo();
        $premium = 0;
        if ($days > 0) $premium = 1;
        $arr2 = array(
            'UserId' => $uid,
            'CompanyName' => $name,
            'CompanyPhone' => $phone,
            'Website' => '',
            'Image' => '',
            'Description' => '',
            'CityId' => $city,
            'Balance' => 0,
            'Discount' => 0,
            'CountryId' => 0,
            'Active' => 1,
            'TotalRequests' => 0,
            'Email' => $email,
            'Premium' => $premium,
            'PremiumUntil' => new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL '.$days.' DAY)')
        );
        $pid = $t2->insert($arr2);
        if (!$pid) {
            $t->delete("Id = ".intval($uid));
        }
        return $uid;
    }

    public function addSocialUser($provider, $id, $email, $name) {
        $t = new Obj_DBTable_Users();
        $arr = array(
            'Email' => $email,
            'Password' => '',
            'RegistrationDate' => new Zend_Db_Expr('NOW()'),
            'Social' => $provider,
            'UserType' => USER_TYPE_USER,
            'Active' => 1,
            'ActiveKay' => '',
            'SocialID' => $id
        );
        $uid = $t->insert($arr);
        if (!$uid) return false;
        $t2 = new Obj_DBTable_Profile();
        $arr2 = array(
            'UserId' => $uid,
            'Name' => $name,
            'Phone' => '',
            'Email' => $email
        );
        $pid = $t2->insert($arr2);
        if (!$pid) return false;
        return $uid;
    }

    public function getPhoneNumber($id) {
        $t = new Obj_DBTable_Profile();
        return $t->getPhoneNumber($id);
    }

    public function getEventorPhoneNumber($id) {
        $t = new Obj_DBTable_InventorInfo();
        $res = $t->getInventorInfoByUserId($id);
        if (!$res) return false;
        return $this->checkPhone($res['CompanyPhone']);
    }

    public function getClientName($id) {
        $t = new Obj_DBTable_Profile();
        return $t->getClientName($id);
    }

    public function phoneExists($phone) {
        $t = new Obj_DBTable_Profile();
        $t2 = new Obj_DBTable_InventorInfo();
        return $t->phoneExists($phone) || $t2->phoneExists($phone);
    }

    public function doRemember($id) {
        $code = substr(md5(md5(time()).md5($id).md5(rand(1, 1000))), 0, 12);
        $t = new Obj_DBTable_Users();
        $t->update(array('RememberMe' => $code), 'Id = '.intval($id));
        $authorization = new Zend_Session_Namespace('authorization');
        $authorization->rememberMeCode = $code;
        return $code;
    }

    public function doCookieAuth($code) {
        $t = new Obj_DBTable_Users();
        $u = $t->findCookieCode($code);
        if ($u) {
            $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
            $authAdapter->setTableName('users')->setIdentityColumn('Id')->setCredentialColumn('Active')->setCredentialTreatment('?');
            $authAdapter->setIdentity($u->Id)->setCredential(1);
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $identity = $authAdapter->getResultRowObject();
                $authStorage = $auth->getStorage();
                $authStorage->write($identity);
                return true;
            } else {
                $this->doExit();
                return false;
            }
        }
        return false;
    }

    public function doKeyAuth($key) {
        $t = new Obj_DBTable_Users();
        $u = $t->findConfirmKey($key);
        if ($u) {
            $t->update(array("Active" => 1, "ActiveKay" => ""), "Id = ".intval($u->Id));
            $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
            $authAdapter->setTableName('users')->setIdentityColumn('Id')->setCredentialColumn('Active')->setCredentialTreatment('?');
            $authAdapter->setIdentity($u->Id)->setCredential(1);
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $identity = $authAdapter->getResultRowObject();
                $authStorage = $auth->getStorage();
                $authStorage->write($identity);
                return true;
            } else {
                $this->doExit();
                return false;
            }
        }
        return false;
    }

    public function doExit() {
        if (!Zend_Auth::getInstance()->hasIdentity()) return;
        $iden = Zend_Auth::getInstance()->getIdentity();
        $t = new Obj_DBTable_Users();
        $t->update(array('RememberMe' => ''), 'Id = '.intval($iden->Id));
        Zend_Auth::getInstance()->clearIdentity();
        setcookie("rememberme", "", 1);
    }

    public function getBlockedInfo($id) {
        $t = new Obj_DBTable_Profile();
        return $t->getBlockedInfo($id);
    }

    public function checkCode($code) {
        $t = new Obj_DBTable_Sms();
        return $t->findCode($code);
    }

    public function checkPhone($phone) {

        $phone = str_replace(array(' ', '(', ')', '-', '+'), '', $phone);
        $number = '/^\+?([87](?!95[4-79]|99[^2457]|907|94[^0]|336)([348]\d|9[0-689]|7[07])\d{8}|[1246]\d{9,13}|5[1-46-9]\d{8,12}|55[1-9]\d{9}|500[56]\d{4}|5016\d{6}|5068\d{7}|502[45]\d{7}|5037\d{7}|50[457]\d{8}|50855\d{4}|509[34]\d{7}|376\d{6}|855\d{8}|856\d{10}|85[0-4789]\d{8,10}|8[68]\d{10,11}|8[14]\d{10}|82\d{9,10}|852\d{8}|90\d{10}|96(0[79]|170|13)\d{6}|96[23]\d{9}|964\d{10}|96(5[69]|89)\d{7}|96(65|77)\d{8}|92[023]\d{9}|91[1879]\d{9}|9[34]7\d{8}|959\d{7}|989\d{9}|97\d{8,12}|99[^45]\d{7,11}|994\d{9}|9955\d{8}|380[34569]\d{8}|38[15]\d{9}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}|37[6-9]\d{7,11}|30[69]\d{9}|34[67]\d{8}|3[12359]\d{8,12}|36\d{9}|38[1679]\d{8}|382\d{8,9})$/';

        if (preg_match($number, $phone, $result) != 1) {
            return false;
        }

        $ua = '/^\+?380/';
        $ru = '/^\+?(79|73|74|78|8)/';
        if (preg_match($ua, $phone) != 1 && preg_match($ru, $phone) != 1) {
            return false;
        }
        return $result[1];
    }

    public function sendCode($phone) {
        if (!function_exists('send_sms')) include APPLICATION_PATH.'/../library/Sms/smscapi.php';
        $t = new Obj_DBTable_Sms();
        $code = mt_rand(10000, 99999);
        while ($t->countCode($code) > 0) {
            $code = mt_rand(10000, 99999);
        }

        $rez = send_sms($phone, 'Код подтверждения: ' . $code);

        if($rez[1] > 0){
            $t->addCode($code, $phone);
            return true;
        }else{
            return false;
        }
    }

    public function addCodeRequest($id) {
        $t = new Obj_DBTable_Profile();
        $t->update(array('PhoneCodeRequests' => new Zend_Db_Expr('PhoneCodeRequests + 1')), 'UserId = '.intval($id));
    }

    public function addCodeResponse($id, $val = false) {
        $t = new Obj_DBTable_Profile();
        if ($val !== false)
            $t->update(array('PhoneCodeEnter' => intval($val)), 'UserId = '.intval($id));
        else
            $t->update(array('PhoneCodeEnter' => new Zend_Db_Expr('PhoneCodeEnter + 1')), 'UserId = '.intval($id));
    }

    public function blockSMSPopup($id) {
        $t = new Obj_DBTable_Profile();
        $t->update(array('smsBlockedUntil' => date('Y-m-d H:i:s', time() + 15*60)), 'UserId = '.intval($id));
    }

    public function clearBlockSMSPopup($id, $code = false) {
        $t = new Obj_DBTable_Profile();
        $t->clearBlocked($id);
        if ($code !== false) {
            $t2 = new Obj_DBTable_Sms();
            $t2->delCode($code);
        }
    }

    public function updatePhoneNumber($id, $number) {
        $t = new Obj_DBTable_Profile();
        $t->update(array('Phone' => $number), 'UserId = '.intval($id));
    }

    public function updateSearchRequests($uid) {
        $t = new Obj_DBTable_InventorInfo();
        $t->update(array('TotalRequests' => new Zend_Db_Expr('TotalRequests + 1')), 'UserId = '.intval($uid));
    }

    public function addStar($uid, $eid, $sid) {
        $t = new Obj_DBTable_Starred();
        if (!$t->checkEventor($uid, $eid)) $t->insert(array("EventorID" => $eid, "UserID" => $uid, "EventorServiceID" => $sid));
    }

    public function removeStar($uid, $eid) {
        $t = new Obj_DBTable_Starred();
        $t->removeEventor($uid, $eid);
    }

    public function requestCall($uid, $eid, $sid, $cphone, $cname) {
        $t = new Obj_DBTable_Calls();
        $ephone = $this->getEventorPhoneNumber($eid);
        if (!$t->checkEventor($uid, $eid)) {
            if (!function_exists('send_sms')) include APPLICATION_PATH.'/../library/Sms/smscapi.php';
            if ($ephone !== false) send_sms($ephone, "Запрос от: ".$cname." +".$cphone);
            $t->insert(array("EventorID" => $eid, "UserID" => $uid, "CallPhoneNumber" => $cphone, "Date" => new Zend_Db_Expr('NOW()'), "EventorServiceID" => $sid, "UserDeleted" => 0));
        }
    }

    public function getClientProfile($uid) {
        $t = new Obj_DBTable_Profile();
        return $t->getProfileInfo($uid);
    }

    public function updateClientProfile($name, $email, $uid) {
        $t = new Obj_DBTable_Profile();
        $t->update(array("Name" => $name, "Email" => $email), "UserId = ".intval($uid));
        $t2 = new Obj_DBTable_Users();
        $t2->update(array("Email" => $email), "Id = ".intval($uid));
    }

    public function updatePremium() {
        $t = new Obj_DBTable_InventorInfo();
        $t->update(array('Premium' => 0, 'PremiumUntil' => '2000-01-01'), "Premium = 1 AND PremiumUntil < '".date("Y-m-d")."'");
    }

    public function getBasicSoon() {
        $t = new Obj_DBTable_InventorInfo();
        return $t->getBasicSoon();
    }

}
?>
