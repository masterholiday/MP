<?php

class IventorController extends DefaultBaseController
{
    protected $_needbeloggedin = false;

    public function indexAction()
    {
		$translit = new Zend_Filter_Translit();
		$tVisits = new Obj_DBTable_Visits();

        
		
        $user = $this->_auth;
        $iventorid = $this->getRequest()->getParam('ID', 0);
		$gotTitle = $this->getRequest()->getParam('title', 0);
        $invinfo = new Obj_DBTable_InventorInfo();
        if (!isset($user->UserType)) {
            $user->UserType = 0;
            $user->Id = 0;
        }
        if ($iventorid == 0 && isset($user->UserType) && $user->UserType == USER_TYPE_INVENTOR) {
            $info = $invinfo->getIventorInformation($user->Id);
            $this->_redirect("/iventor/".$info->Id."/".$translit->filter($info->CompanyName)."/");
            die();
        }
        else {
            $this->view->readonly = true;
            $iventorid = $invinfo->getIventorUserID($iventorid);
            if ($iventorid == $user->Id) {
                $this->view->readonly = false;
                $iventorid = $user->Id;
				$invinfo->update(array("visitedadminka" => 1), "UserId = ".intval($iventorid));
                $tab = new Obj_DBTable_ServiceCategories();
                $this->view->topCategories = $tab->getCategoriesForParent(0);
            }
            else {
                $o = new Obj_DBTable_Missing();
                $dates = $o->getIventorDates($iventorid);
                $nice = $o->makeNice($dates);
                $this->view->nice = $nice;
                $this->view->showButtons = !isset($this->_auth->Id) || $this->_auth->UserType == USER_TYPE_USER  ? true : false;
            }
        }
        $info = $invinfo->getIventorInformation($iventorid);
		$title = $translit->filter($info->CompanyName);
	
		if ($gotTitle!=$title) {
            header('Location: /404.html');
			echo $gotTitle."<br>";
			echo $title."<br>";
            die();
        }
		$visits = $tVisits->getVisits($iventorid);

		$currentmonth = (int)date("n");
		$currentyear = (int)date("Y");
		if($visits < 1){

		$tVisits->insert(array("iventorid" => $iventorid, "visits" => 1, "month"=>$currentmonth, "year"=>$currentyear));
		}
		else{
		$visits++;
		$where[] = "iventorid =".$iventorid;
		$where[] = "year =".$currentyear;
		$where[] = "month =".$currentmonth;
		$tVisits->update(array("visits" => $visits), $where);

		}
		
        if (!$info || !isset($info->Email)) {
            $this->_helper->redirector('index','index','default');
        }

        if ($info->Website != $this->checkWebsite($info->Website)) {
            $info->Website = $this->checkWebsite($info->Website);
            $invinfo->update(array("Website" => $info->Website), "Id = ".intval($info->Id));
        }

        $this->view->iventor = $info;
        
        $Obj_port = new Obj_DBTable_Portfolio();
        $count_port = $Obj_port->getCountUserPortfolio($iventorid);
        $count_page = ceil($count_port/10);
        $this->view->count_page = $count_page;
        $now_page = intval(abs($this->getRequest()->getParam('page', 1)));
        $this->view->nowpage = $now_page;
        $start = $now_page * 10 - 10;

        $rows = $Obj_port->getAllUserPortfolio($iventorid, 10, $start);

        if(!$rows && $start != 0){
            $this->_helper->redirector('index','iventor','default', array('page' => $count_page));
        }

        $this->view->iventorid = $iventorid;
        $this->view->rows = $rows;
        
        $Obj_ivent_service = new Obj_DBTable_IventorServices();
        $this->view->ivent_service = $Obj_ivent_service->getAllIventorServicesByIventorId($iventorid);

        $showCall = true;
        $showBest = true;

        if (count($this->view->ivent_service) > 0 && isset($this->_auth->Id) && $this->_auth->UserType == USER_TYPE_USER) {
            $s = $this->view->ivent_service[0];
            $t = new Obj_DBTable_Calls();
            if ($t->checkEventor($this->_auth->Id, $s['IventorId'])) $showCall = false;
            $t = new Obj_DBTable_Starred();
            if ($t->checkEventor($this->_auth->Id, $s['IventorId'])) $showBest = false;
        }
        $this->view->showCall = $showCall;
        $this->view->showStar = $showBest;
        $tVideo = new Obj_DBTable_Video();
        $this->view->videos = $tVideo->getVideos($iventorid);
		
		$this->view->headTitle($info->CompanyName." – отзывы, цены, заказ услуг – Мастерская Праздников");
		$this->view->headMeta()->appendName('description', "Хотите узнать правду о ". $info->CompanyName."? Смотрите портфолио, отзывы и цены на сайте Мастерская Праздников!");

    }

    private function checkWebsite($website) {
        $website = strtolower($website);
        if (trim($website) === '') return '';
        if (strpos($website, "http") === false) {
            $website = "http://".$website;
        }
        return $website;
    }
    
    public function uploadPortfolioAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $Obj_port = new Obj_DBTable_Portfolio();


        $count_port = $Obj_port->getCountUserPortfolio($user->Id);
        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($user->Id);
        $maxcount = $info->Premium == 1 ? 30 : 10;
        if ($count_port >= $maxcount) {
            echo htmlspecialchars(json_encode(array('error' => "Вы можете загрузить максимум ".$maxcount." фотографий.")), ENT_NOQUOTES); die();
        }

        $allowedExtensions = array("jpeg", "jpg", "png", "gif");
        $sizeLimit = 10 * 1024 * 1024;

        require(APPLICATION_PATH.'/../library/File/php.php');
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

        $pathToFile = APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/';
        if(!file_exists($pathToFile)){
            mkdir($pathToFile, '0755', true);
        }
        //echo $pathToFile;
        $result = $uploader->handleUpload($pathToFile);
        if (!isset($result['uploaded'])) {
            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); die();
        }
        $result['filename'] = $uploader->getName();

        $filename = $result['uploaded'];
        require_once APPLICATION_PATH.'/../library/PHPThumb/ThumbLib.inc.php';
        $_im = PhpThumbFactory::create($pathToFile.'/'.$filename);
        $_im->setOptions(array('resizeUp' => true));


        $iWidth = 1024;
        $iHeight = 0;
        $_im->resize($iWidth, $iHeight);
        $_im->save($pathToFile.'/'.$iWidth.'x'.$iHeight.'_'.$filename);

        $iWidth = 199;
        $iHeight = 169;
        $_im->adaptiveResize($iWidth, $iHeight);
        $_im->save($pathToFile.'/'.$iWidth.'x'.$iHeight.'_'.$filename);

        $Obj_portfolio = new Obj_DBTable_Portfolio();
        $id = $Obj_portfolio->addPortfolio($user->Id, $filename);

        $result['id'] = $id;
        $result['ivid'] = $user->Id;

        $count_port = $Obj_port->getCountUserPortfolio($user->Id);
        $count_page = ceil($count_port/10);
        $now_page = intval(abs($this->getRequest()->getParam('page', 1)));
        $pages = array();
        for($i = 1; $i <= $count_page; $i++){
            if($i == $now_page){
                $pages[] = '<a href="#" onclick="return false;" class="current">'.$i.'</a>';
            } else {
                $pages[] = '<a href="#" onclick="getPortfolioPage('.$i.', '.$user->Id.'); return false;">'.$i.'</a>';
            }
/*            if($i == $now_page){
                $pages[] = '<a href="'.$this->_helper->url->url(array('module' => 'default', 'controller' => 'iventor', 'action' => 'index', 'page' => $i), NULL, true).'" class="current">'.$i.'</a>';
            } else {
                $pages[] = '<a href="'.$this->_helper->url->url(array('module' => 'default', 'controller' => 'iventor', 'action' => 'index', 'page' => $i), NULL, true).'">'.$i.'</a>';
            }
*/
        }
        if (count($pages) < 2 || $info->Premium != 1) $pages = array();
        $result['pages'] = $pages;


        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); die();
    }
	
		public function translitcatAction() {
		$tcategory = $_POST['catid'];
		$ocat = new Obj_DBTable_ServiceCategories();
		$alias = $ocat->getAlias($tcategory);
	
		echo htmlspecialchars(json_encode($alias), ENT_NOQUOTES); die();
    }
	
	public function translitcityAction() {
		$tcity = $_POST['city'];
		$c = new Obj_DBTable_Citys();
		$city = $c->getAlias($tcity);
		echo htmlspecialchars(json_encode($city), ENT_NOQUOTES); die();

    }

    public function saveNewPhoneAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($user->Id);
        $regtry = new Zend_Session_Namespace('evregtry');
        $phone = isset($regtry->evNumber) ? $regtry->evNumber : '';
        if ($phone === '') {
            $this->_helper->json(array('error' => "Введите номер телефона!"), true, false);
        }
        if ($info->CountryID == 9908) {
            if (preg_match('/^\+?380/', $phone) != 1) {
                $this->_helper->json(array('error' => "Неформат"), true, false);
            }
        }
        else {
            if (preg_match('/^\+?(79|73|74|78)/', $phone) != 1) {
                $this->_helper->json(array('error' => "Неформат"), true, false);
            }
        }
        $invinfo->update(array("CompanyPhone" => $phone), "UserId = ".$user->Id);
        unset($regtry->evNumber);
        unset($regtry->smsBlockedUntil);
        unset($regtry->smsTryCount);
        unset($regtry->smsTryCount2);

        $this->_helper->json(array(), true, false);
    }

    public function addVideoAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $tVideo = new Obj_DBTable_Video();
        if ($tVideo->getCount($user->Id) >= 3) {
            $this->_helper->json(array('error' => "Достигнут предел количества видео"), true, false);
        }
        $link = $this->getRequest()->getParam('descr', '');
        $id = $tVideo->insert(array("UserID" => $user->Id, "VideoLink" => $link));
        $link = explode("/", $link);
        $id2 = $link[count($link) - 1];

        $this->_helper->json(array("id" => $id, "link" => $id2), true, false);
    }
    
    public function delVideoAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $id = intval($this->getRequest()->getParam('id'));
        $tVideo = new Obj_DBTable_Video();
        $tVideo->delete("ID = ".intval($id)." AND UserID = ".intval($user->Id));
        $this->_helper->json(array(), true, false);
    }

    public function delPortfolioAction(){
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $id = intval(trim($this->getRequest()->getParam('id', 0)));

        $Obj_port = new Obj_DBTable_Portfolio();
        $Obj_port->delPortfolio($user->Id, $id);


        $count_port = $Obj_port->getCountUserPortfolio($user->Id);
        $count_page = ceil($count_port/10);
        $now_page = intval(abs($this->getRequest()->getParam('page', 1)));
        while ($now_page > $count_page) $now_page--;
        $pages = array();
        for($i = 1; $i <= $count_page; $i++){
            if($i == $now_page){
                $pages[] = '<a href="#" onclick="return false;" class="current">'.$i.'</a>';
            } else {
                $pages[] = '<a href="#" onclick="getPortfolioPage('.$i.', '.$user->Id.'); return false;">'.$i.'</a>';
            }
        }
        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($user->Id);

        if (count($pages) < 2 || $info->Premium != 1) $pages = array();

        $start = $now_page * 10 - 10;
        if ($start < 0) $start = 0;

        $rows = $Obj_port->getAllUserPortfolio($user->Id, 10, $start);

        $this->_helper->json(array('error' => 0, 'pages' => $pages, 'images' => $rows), true, false);
    }
	
	public function referencesAction(){
        $iventorid = intval(trim($this->getRequest()->getParam('id', 0)));
		$link = $this->getRequest()->getParam('website', 0);
        $Obj_reference = new Obj_DBTable_References();
		
		$reference = $Obj_reference->getRefs($iventorid);

		$currentmonth = (int)date("n");
		$currentyear = (int)date("Y");
		if($reference < 1){

		$Obj_reference->insert(array("iventorid" => $iventorid, "reference" => 1, "link" =>$link, "month"=>$currentmonth, "year"=>$currentyear));
		}
		else{
		$reference++;
		$where[] = "iventorid =".$iventorid;
		$where[] = "year =".$currentyear;
		$where[] = "month =".$currentmonth;
		$Obj_reference->update(array("reference" => $reference), $where);

		}
		
		 $this->_helper->json(array('status' => 'added'), true, false);
		
		}

    public function getPortfolioAction(){
        $id = intval(trim($this->getRequest()->getParam('id', 0)));
        $Obj_port = new Obj_DBTable_Portfolio();

        $count_port = $Obj_port->getCountUserPortfolio($id);
        $count_page = ceil($count_port/10);
        $now_page = intval(abs($this->getRequest()->getParam('page', 1)));
        if ($now_page > $count_page) $now_page = $count_page;
        $pages = array();
        for($i = 1; $i <= $count_page; $i++){
            if($i == $now_page){
                $pages[] = '<a href="#" onclick="return false;" class="current">'.$i.'</a>';
            } else {
                $pages[] = '<a href="#" onclick="getPortfolioPage('.$i.', '.$id.'); return false;">'.$i.'</a>';
            }
        }
        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($id);
        if (count($pages) < 2 || $info->Premium != 1) $pages = array();

        $start = $now_page * 10 - 10;

        $rows = $Obj_port->getAllUserPortfolio($id, 10, $start);

        $this->_helper->json(array('error' => 0, 'pages' => $pages, 'images' => $rows), true, false);
    }

    public function changePassAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
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


    public function loadCategoryAction(){
        $parent_id = intval(trim($this->getRequest()->getParam('parent', 0)));
        $category = new Obj_DBTable_ServiceCategories();
        $cat = $category->getAllCategoriesByParent($parent_id);
        $this->_helper->json(array('cat' => $cat), true, false);
    }

    public function changeInfoAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $name = trim($this->getRequest()->getParam('name'));
        $website = trim($this->getRequest()->getParam('website'));
        $skype = trim($this->getRequest()->getParam('skype'));
        $city = intval($this->getRequest()->getParam('city'));
        if ($name == '') {
            $this->_helper->json(array('error' => 1, 'text' => "Ошибка! Название!"), true, false);
        }
        if ($city == 0) {
            $this->_helper->json(array('error' => 1, 'text' => "Ошибка! Город!"), true, false);
        }
        $photo = trim($this->getRequest()->getParam('photo'));
        $descr = trim($this->getRequest()->getParam('descr'));
		$translit = new Zend_Filter_Translit();
		
        $arUpdate = array();
        $arUpdate['CompanyName'] = $name;
		
        $arUpdate['Skype'] = $skype;
        $arUpdate['Website'] = $this->checkWebsite($website);

        $oAddress = new Address();
        $oIventor = new Obj_DBTable_InventorInfo();
        $info = $oIventor->getIventorInformation($user->Id);
        $cinfo = $oAddress->getCatalogCityInfo($city);
        if ($cinfo['country_id'] != $info->CountryID) {
            $this->_helper->json(array('error' => 1, 'text' => "Ошибка! НЕ НУЖНО ТАК ДЕЛАТЬ!"), true, false);
        }
        $arUpdate['CityId'] = $city;
        $arUpdate['Description'] = $descr;
        if ($photo != '') $arUpdate['Image'] = $photo;

        $oIventor->update($arUpdate, 'UserId = '.intval($user->Id));
	    $invinfo = new Obj_DBTable_InventorInfo();
	    $i = $invinfo->getInventorInfoByUserId($user->Id);
		$arUpdate['CompanyNameTrans'] = $translit->filter($name);
		$arUpdate['Id'] = $i["Id"];
        $url = $this->view->url(array('ID' => $i['Id'],  'title' => $translit->filter($name)), 'iventor');
		$this->_helper->json(array("s" => $url,"base"=>$this->view->baseUrl(),'error' => 0 ), true, false);

		
    }

	public function uploadLogo1Action() {
		$user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }

        $allowedExtensions = array("jpeg", "jpg", "png", "gif");
        $sizeLimit = 10 * 1024 * 1024;
		
		require(APPLICATION_PATH.'/../library/File/php.php');
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

        $pathToFile = APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/';
        if(!file_exists($pathToFile)){
            mkdir($pathToFile, '0755', true);
        }
        $result = $uploader->handleUpload($pathToFile);
        if (!isset($result['uploaded'])) {
            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); die();
        }
        $result['filename'] = $uploader->getName();
        $filename = $result['uploaded'];
		
		$result['ivid'] = $user->Id;

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); die();
	}
	
    public function uploadLogoAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
		$targ_w = $targ_h = 200;
		$jpeg_quality = 90;
		$filename = $_POST['src'];
		$file = APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$filename;
		
		list($width, $height, $image_type) = getimagesize($file);

		switch ($image_type)
		{
			case 1: $img_r = imagecreatefromgif($file); break;
			case 2: $img_r = imagecreatefromjpeg($file);  break;
			case 3: $img_r = imagecreatefrompng($file); break;
			default: return '';  break;
		}
		
		
		//$img_r = imagecreatefromjpeg(APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$filename);
		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
		
		 /* Check if this image is PNG or GIF to preserve its transparency */
    if(($image_type == 1) OR ($image_type==3))
    {
        imagealphablending($dst_r, false);
        imagesavealpha($dst_r,true);
        $transparent = imagecolorallocatealpha($dst_r, 255, 255, 255, 127);
        imagefilledrectangle($dst_r, 0, 0, $targ_w, $targ_h, $transparent);
    }
    imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
		$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		
   		

		ob_start();

    switch ($image_type)
    {
        case 1: imagegif($dst_r); break;
        case 2: imagejpeg($dst_r, $file, 100);  break; // best quality
        case 3: imagepng($dst_r, $file, 0); break; // no compression
        default: echo ''; break;
    }


		//imagejpeg($dst_r,APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$filename,$jpeg_quality);

        require_once APPLICATION_PATH.'/../library/PHPThumb/ThumbLib.inc.php';
        $_im = PhpThumbFactory::create(APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$filename);
        $_im->setOptions(array('resizeUp' => true));

        $iWidth = 200;
        $iHeight = 200;
        $_im->adaptiveResize($iWidth, $iHeight);
        $_im->save(APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$iWidth.'x'.$iHeight.'_'.$filename);
        $iWidth = 150;
        $iHeight = 150;
        $_im->adaptiveResize($iWidth, $iHeight);
        $_im->save(APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$iWidth.'x'.$iHeight.'_'.$filename);
        $iWidth = 70;
        $iHeight = 70;
        $_im->adaptiveResize($iWidth, $iHeight);
        $_im->save(APPLICATION_PATH.'/../public/img/users/'.$user->Id.'/'.$iWidth.'x'.$iHeight.'_'.$filename);

        $result['ivid'] = $user->Id;

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); die();
    }
    
    public function addServiceAction(){
        
        $User = $this->_auth;
        if (!isset($User->UserType) || $User->UserType != USER_TYPE_INVENTOR) {
        	$this->_helper->redirector('index','index','default');
        	die();
        }
        
    	$cat = intval(trim($this->getRequest()->getParam('pidcat')));
    	$city = intval(trim($this->getRequest()->getParam('city')));
    	$min = intval(trim($this->getRequest()->getParam('min')));
    	$max = intval(trim($this->getRequest()->getParam('max')));
        $id = intval(trim($this->getRequest()->getParam('id')));

        $oAddress = new Address();
        $oIventor = new Obj_DBTable_InventorInfo();
        $info = $oIventor->getIventorInformation($User->Id);
        $cinfo = $oAddress->getCatalogCityInfo($city);

        if ($cat == 0) {
            $this->_helper->json(array('error' => 'Выберите категорию'), true, false);
        }

        if ($cinfo['country_id'] != $info->CountryID) {
            $this->_helper->json(array('error' => "Ошибка! НЕ НУЖНО ТАК ДЕЛАТЬ!"), true, false);
        }

        $Obj_ivent_service = new Obj_DBTable_IventorServices();
        if ($id == 0) {
            $rows = $Obj_ivent_service->issetService($User->Id, $cat, $city, 0);
            if (count($rows) > 0) {
                $this->_helper->json(array('error' => 'Данная услуга уже существует'), true, false);
            }

            $id = $Obj_ivent_service->insert(array(
                'IventorId' => $User->Id,
                'CategoryId' => $cat,
                'CityId' => $city,
                'PriceId' => 0,
                'minPrice' => $min,
                'maxPrice' => $max
            ));
        }
        else {
            $rows = $Obj_ivent_service->issetService($User->Id, $cat, $city, $id);
            if (count($rows) > 0) {
                $this->_helper->json(array('error' => 'Данная услуга уже существует'), true, false);
            }

            $Obj_ivent_service->update(array(
                'CategoryId' => $cat,
                'CityId' => $city,
                'PriceId' => 0,
                'minPrice' => $min,
                'maxPrice' => $max
            ), "IventorId = ".$User->Id." AND Id = ".intval($id));
        }

    	$this->_helper->json(array('id' => $id), true, false);
    	return;
    }

    public function getServiceAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->json(array('error' => 'Ошибка'), true, false);
            die();
        }
        $id = intval($this->getRequest()->getParam('id'));
        $t = new Obj_DBTable_IventorServices();
        $arr = $t->getIventorServices($id);
        $tab = new Obj_DBTable_ServiceCategories();
        $arr3 = $tab->getCategories($arr['CategoryId']);
        $arr2 = $tab->getCategoriesForParent($arr3['ParentId']);
        $t2 = new Obj_DBTable_Citys();
        $arr4 = $t2->getCityAndCountry($arr['CityId']);
        $this->_helper->json(array("s" => $arr, "sublist" => $arr2, "category" => $arr3['ParentId'], "city" => $arr4), true, false);
    }

    public function searchesAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }

        $oSearch = new Search();
        $sType = $this->getRequest()->getParam('type', 'complex');
        $delete = intval($this->getRequest()->getParam('delete', 0));
        if ($delete !== 0) {
            $oSearch->deleteEventorSearch($delete, $this->_auth->Id);
        }
        $delete2 = intval($this->getRequest()->getParam('delete2', 0));
        if ($delete2 !== 0) {
            $oSearch->deleteEventorSearch2($delete2, $this->_auth->Id);
        }

        $this->view->stype = $sType;
        $page = intval($this->getRequest()->getParam('page', 0));
        $perpage = 5;
        $total = $oSearch->getEventorSearchesCount($this->_auth->Id, $sType);
        $pages = intval(ceil($total / $perpage));
        $arCurrPageAddr = array('controller' => 'iventor', 'action' => 'searches', 'type' => $sType, 'page' => $page);
        if ($page >= $pages) $page = $pages - 1;
        if ($page < 0) $page = 0;
        $list = $oSearch->getEventorSearches($this->_auth->Id, $sType, $page, $perpage);
        $this->view->list = $list;
        $arPageAddr = array('controller' => 'iventor', 'action' => 'searches', 'type' => $sType);

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
            //print_r($arPages);
            //die();
        }
        $this->view->cpage = $arCurrPageAddr;
    }

    public function delIventorServiceAction(){
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
        	$this->_helper->redirector('index','index','default');
        	die();
        }
    	$id = intval(trim($this->getRequest()->getParam('id')));
    	$Obj_ivent_service = new Obj_DBTable_IventorServices();
    	$Obj_ivent_service->deleteIventorServices($id, $user->Id);
    	$this->_helper->json(array('ok' => 'ok'), true, false);
    	return;
    }
    

    public function makeStarredAction() {
        $user = Zend_Registry::isRegistered('user') ? Zend_Registry::get('user') : new stdClass();
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $id = $this->getRequest()->getParam('id', 0);
        $oTransactions = new Obj_DBTable_Transaction();
        $oTransactions->makeStarred($id, $user->Id);
        $this->_helper->json(array('error' => 0));
    }


    public function busyDateAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $t = trim($this->getRequest()->getParam('t'));
        $dd = explode("-", $t);
        $d = date("Y-m-d", mktime(0, 0, 0, $dd[1], $dd[2], $dd[0]));
        $o = new Obj_DBTable_Missing();
        $o->changeDate($d, $user->Id);
        $dates = $o->getIventorDates($user->Id);
        $nice = $o->makeNice($dates);
        $this->_helper->json(array('error' => 0, 'd' => $d, 'nice' => $nice));
    }

    public function changeActiveAction() {
        $user = Zend_Registry::isRegistered('user') ? Zend_Registry::get('user') : new stdClass();
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $o = new Obj_DBTable_InventorInfo();
        $o->changeActive($user->Id);
        $this->_helper->json(array('error' => 0));
    }


    public function accountAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $invinfo = new Obj_DBTable_InventorInfo();
        $this->view->info = $invinfo->getIventorInformation($user->Id);
        $o = new Obj_DBTable_Missing();
        $dates = $o->getIventorDates($user->Id);
        $nice = $o->makeNice($dates);
        $this->view->dates = $dates;
        $this->view->nice = $nice;

        $of = new Obj_DBTable_Finance();
        $page = intval(abs($this->getRequest()->getParam('page', 0)));
        $year = intval(abs($this->getRequest()->getParam('year', 0)));
        $month = intval(abs($this->getRequest()->getParam('month', 0)));

        $this->view->year = $year;
        $this->view->month = $month;
        $perpage = 50;

        $total = $of->getIventorFinanceListCount($user->Id, $year, $month);
        $pages = intval(ceil($total / $perpage));

        if ($page >= $pages) $page = $pages - 1;
        if ($page < 0) $page = 0;
        $list = $of->getIventorFinanceList($user->Id, $year, $month, $page, $perpage);

        $monthesss = false;
        if (isset($_SESSION['monthesss'])) {
            $monthesss = @unserialize($_SESSION['monthesss']);
            if (!is_array($monthesss) || count($monthesss) < 0) $monthesss = false;
        }

        if (($page == 0 && $year == 0 && $month == 0) || $monthesss === false) {
            $monthesss = array();
            foreach ($list as $v) {
                $m = intval(date("m", strtotime($v->Date)));
                if (!in_array($m, $monthesss)) $monthesss[] = $m;
            }
            $_SESSION['monthesss'] = serialize($monthesss);
        }
        $this->view->monthesss = $monthesss;

        $this->view->list = $list;
        $arPageAddr = array('controller' => 'iventor', 'action' => 'account', 'year' => $year, 'month' => $month);
        $dots = array('html'=> '...', 'class' => '', 'href' => '');
        $this->view->burl = $this->_helper->url->url(array('controller' => 'iventor', 'action' => 'account'), null, true);
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


        $ts = new Obj_DBTable_Settings();
        $arPrice = array();
        if ($this->view->info->CountryID == 9908) {
            $arPrice[30] = $ts->getValue('monthpay_u')->KeyValue;
        }
        else {
            $arPrice[30] = $ts->getValue('monthpay_r')->KeyValue;
        }
        $arPrice[90] = 3 * $arPrice[30] * (100 - $ts->getValue('discount90')->KeyValue) / 100;
        $arPrice[180] = 6 * $arPrice[30] * (100 - $ts->getValue('discount180')->KeyValue) / 100;
        $arPrice[360] = 12 * $arPrice[30] * (100 - $ts->getValue('discount360')->KeyValue) / 100;
        $this->view->prices = $arPrice;


    }

    public function getPaymentFormAction() {
        $user = $this->_auth;
        if (!isset($user->UserType) || $user->UserType != USER_TYPE_INVENTOR) {
            $this->_helper->redirector('index','index','default');
            die();
        }
        $days = intval($this->getRequest()->getParam('days', 0));


        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($user->Id);

        $ts = new Obj_DBTable_Settings();
        $arPrice = array();
        if ($info->CountryID == 9908) {
            $arPrice[30] = $ts->getValue('monthpay_u')->KeyValue;
        }
        else {
            $arPrice[30] = $ts->getValue('monthpay_r')->KeyValue;
        }
        $arPrice[90] = 3 * $arPrice[30] * (100 - $ts->getValue('discount90')->KeyValue) / 100;
        $arPrice[180] = 6 * $arPrice[30] * (100 - $ts->getValue('discount180')->KeyValue) / 100;
        $arPrice[360] = 12 * $arPrice[30] * (100 - $ts->getValue('discount360')->KeyValue) / 100;
        if (!isset($arPrice[$days])) {
            $sum = $arPrice[30];
            $days = 30;
        }
        else {
            $sum = $arPrice[$days];
        }

        $Objfinance = new Obj_DBTable_Finance();
        $nextorderid = $Objfinance->addFinance($user->Id, $sum, $days, 'initiated');
        $merchant_id='i7123905401';
        $signature="dUVEYIeXvQaZZTHSOjaZWmZTBN0KSnWEQmo";
        $host = "masterholiday.net";
        $description = "Popolneniye personalnogo scheta ".$user->Email." na www.masterholiday.net";
        $xml="<request><version>1.2</version><result_url>http://".$host."/iventor/</result_url><server_url>http://".$host."/open/result</server_url><merchant_id>".$merchant_id."</merchant_id><order_id>".$nextorderid."</order_id><amount>$sum</amount><currency>".($info->CountryID == 9908 ? 'UAH' : 'RUR')."</currency><description>".$description."</description><default_phone></default_phone><pay_way>liqpay,card</pay_way></request>";
        $xml_encoded = base64_encode($xml);
        $lqsignature = base64_encode(sha1($signature.$xml.$signature,1));
        $this->_helper->json(array('error' => 0, 'xml' => $xml_encoded, 'sign' => $lqsignature));
    }
	
	
	
	 public function addStarAction() {
        if (!isset($this->_auth->Id)) {
            $this->_helper->json(array('error' => 'login'), true, false);
        }
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }

        $id = intval($this->getRequest()->getParam('id'));
        $sid = intval($this->getRequest()->getParam('sid'));
		
        $oUser = new User();
        $oUser->addStar($this->_auth->Id, $id, $sid);
        $catSearch = new Zend_Session_Namespace('catalog');
        $arHash = isset($catSearch->hashes) ? $catSearch->hashes : array();
        if (count($arHash) > 0) {
            $obj = unserialize(reset($arHash));
            $hash = key($arHash);
            foreach ($obj->pages as $p => $page) {
                foreach ($page as $k => $v) {
                    if ($v->EventorUserID == $id) {
                        $v->StarID = 1;
                        $obj->pages[$p][$k] = $v;
                    }
                }
            }
            $arHash = array($hash => serialize($obj));
            $catSearch->hashes = $arHash;
        }

        $this->_helper->json(array(), true, false);
    }

    public function removeStarAction(){
        if (!isset($this->_auth->Id)) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        $id = intval($this->getRequest()->getParam('id'));
        $oUser = new User();
        $oUser->removeStar($this->_auth->Id, $id);
        $catSearch = new Zend_Session_Namespace('catalog');
        $arHash = isset($catSearch->hashes) ? $catSearch->hashes : array();
        if (count($arHash) > 0) {
            $obj = unserialize(reset($arHash));
            if (isset($obj->pages)) {
                $hash = key($arHash);
                foreach ($obj->pages as $p => $page) {
                    foreach ($page as $k => $v) {
                        if ($v->EventorUserID == $id) {
                            $v->StarID = 0;
                            $obj->pages[$p][$k] = $v;
                        }
                    }
                }
                $arHash = array($hash => serialize($obj));
                $catSearch->hashes = $arHash;
            }
        }
        $this->_helper->json(array(), true, false);
    }

    public function requestCallAction() {
        if (!isset($this->_auth->Id)) {
            $this->_helper->json(array('error' => 'login'), true, false);
        }
        if ($this->_auth->UserType != USER_TYPE_USER) {
            $this->_helper->json(array('error' => 'Авторизируйтесь, пожалуйста'), true, false);
        }
        $oUser = new User();
        $phoneNumber = $oUser->getPhoneNumber($this->_auth->Id);
        if ($phoneNumber == '') {
            $this->_helper->json(array('error' => 'phone'), true, false);
        }
        $id = intval($this->getRequest()->getParam('id'));
        $sid = intval($this->getRequest()->getParam('sid'));
        $savephone = $this->getRequest()->getParam('saveNumber', 0);
        $clientName = $oUser->getClientName($this->_auth->Id);

        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($id);
        $a = new Obj_DBTable_IventorServices();

        $service = $a->getIventorServices($sid);
        $oServiceCategories = new Obj_DBTable_ServiceCategories();

        $subcat = $oServiceCategories->getCategories($service['CategoryId']);
        $pcat = $oServiceCategories->getCategories($subcat['ParentId']);

        $arrEmailData = array();
        $arrEmailData['clientname'] = $clientName;
        $arrEmailData['eventcategory'] = $pcat['CategoryName'].' &gt; '.$subcat['CategoryName'];
        $arrEmailData['clientphone'] = $phoneNumber;
        $this->sendHTMLLetter(file_get_contents(APPLICATION_PATH.'/../htmlletters/search_catalog.html'), 'Новая сделка', $info->Email, $arrEmailData);

        $oUser->requestCall($this->_auth->Id, $id, $sid, $phoneNumber, $clientName);

        if ($savephone == 1) $oUser->updatePhoneNumber($this->_auth->Id, '');
        $catSearch = new Zend_Session_Namespace('catalog');
        $arHash = isset($catSearch->hashes) ? $catSearch->hashes : array();
        if (count($arHash) > 0) {
            $obj = unserialize(reset($arHash));
            $hash = key($arHash);
            foreach ($obj->pages as $p => $page) {
                foreach ($page as $k => $v) {
                    if ($v->EventorUserID == $id) {
                        $v->CallID = 1;
                        $obj->pages[$p][$k] = $v;
                    }
                }
            }
            $arHash = array($hash => serialize($obj));
            $catSearch->hashes = $arHash;
        }
        $this->_helper->json(array("phone" => $phoneNumber), true, false);
    }
	
	
	
	
	
	
	
}



