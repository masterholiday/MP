<?php
/*#!/usr/bin/php*/ //moved here becasue some of scripts using session in CLI mode

error_reporting(E_ALL);

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH). '/controllers',
    realpath(APPLICATION_PATH). '/Obj',
    realpath(APPLICATION_PATH). '/layouts',
    realpath(APPLICATION_PATH),
    get_include_path(),
)));



/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

include APPLICATION_PATH.'/constants/user-types.php';

$objINI = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

$dbparams = $objINI->resources->db->params->toArray();

$db = new Zend_Db_Adapter_Pdo_Mysql($dbparams);
Zend_Db_Table_Abstract::setDefaultAdapter($db);


$user = new User();
$user->updatePremium();

$users = $user->getBasicSoon();
$html = file_get_contents(APPLICATION_PATH.'/../htmlletters/lowmoney.html');
foreach ($users as $u) {
   /* $config = array ('auth' => 'login', 'port' => '250',
        'username' => 'eventor@masterholiday.net',
        'password' => 'HdtGVo5tw');

    $transport = new Zend_Mail_Transport_Smtp('mail.masterholiday.net', $config);
    $mail = new Zend_Mail('UTF-8');
    $mail->setBodyHtml($html);
    $mail->setFrom('eventor@masterholiday.net', 'Мастерская Прадников');
    $mail->addTo($u->Email);
    $mail->setSubject("Ваш “Премиум” аккаунт заканчивается через ".$u->c." дней.");
    try {
        $mail->send($transport);
    }
    catch (Exception $e){
    }*/
	$api_key = "5615unk8kntwr449i5c87cbbw44rd57bu3u5uuoo";

			echo $u->Email."<br>";
			echo $u->c."<br>";
			echo $html."<br>";
			// Параметры сообщения
			// Если скрипт в кодировке UTF-8, не используйте iconv
			$email_from_name = "Мастерская Праздников";
			$email_from_email = "eventor@masterholiday.net";
			$email_to = $u->Email;
			$email_body = $html;
			//urlencode(iconv('cp1251', 'utf-8',"Интернет сервис для поиска целевых клиентов"));
			$email_subject = "Ваш “Премиум” аккаунт заканчивается через ".$u->c." дней.";
			//$email_subject = "Ваш “Премиум” аккаунт заканчивается через  дней.";
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
				echo "Invalid JSON";

			  }
			  elseif(!empty($jsonObj->error)) {
				// Ошибка отправки сообщения
				echo "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";

			  } else {
				// Сообщение успешно отправлено
				//return 1;
				echo "Email message is sent. Message id " . $jsonObj->result->email_id;

			  }
			} else {
			  // Ошибка соединения с API-сервером
			  echo "API access error";
			}

	
	
}
die();



