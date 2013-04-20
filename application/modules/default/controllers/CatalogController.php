<?php
class CatalogController extends DefaultBaseController
{

    public function indexAction()
    {
		
        $catalog = new Catalog();
        $this->view->shortCatalogList = $catalog->getShortList();

        $tab = new Obj_DBTable_ServiceCategories();
        $this->view->topCategories = $tab->getCategoriesForParent(0);
		
		$t = new Obj_DBTable_ServiceCategories();
        $c = new Obj_DBTable_Citys();
        
		
		$cityname = $this->getRequest()->getParam('cityname', 0);
		$title = $this->getRequest()->getParam('title', 0);
		 $main = intval($this->getRequest()->getParam('main', 0));
		
		

		if(intval($this->getRequest()->getPost('category', 0)) != 0 ){

		$category = intval($this->getRequest()->getPost('category', 0));
        $city = intval($this->getRequest()->getPost('city', 0));
        $min = intval($this->getRequest()->getPost('min', 0));
        $max = intval($this->getRequest()->getPost('max', 0));
		}
		
		
		else{
		
		
		if ($main != 1) {
		if ($t->isAlias($title)==null) {
						header('Location: /404.html');
						die();
					}
					if ($cityname!="all" && $c->isAlias($cityname)==null) {
						header('Location: /404.html');
						die();
					}
					}
		$category = $t->getId($title);
		$city = $c->getId($cityname);
        $min = intval($this->getRequest()->getParam('min', 0));
        $max = intval($this->getRequest()->getParam('max', 0));
		
		
		}
		

   
		
        if ($main === 1) {
            $this->view->showLastIventors = true;
            $Obj_post = new Obj_DBTable_Posts();
            $this->view->articles = $Obj_post->getPostByLimit('4');
        }
        else {
            $this->view->showLastIventors = false;
        }

        $this->view->min = $min;
        $this->view->max = $max;

        $oSearch = new Search();
        $page = intval($this->getRequest()->getParam('page', 0));
		//$page = 0;

        $userID = isset($this->_auth->Id) && $this->_auth->UserType == USER_TYPE_USER  ? $this->_auth->Id : 0;

        $rows = 10;
        $catSearch = new Zend_Session_Namespace('catalog');
        $arHash = (isset($catSearch->hashes) && $category > 0) ? $catSearch->hashes : array();
        $hash = md5(serialize(array($category, $city, $min, $max, $userID)));
		
        if (isset($arHash[$hash])) {
            $glist = unserialize($arHash[$hash]);
            $this->view->sess = true;
        }
        else {
            $glist = $oSearch->getCatalogSearchResult($category, $city, $min, $max, $userID, $rows);

            $catSearch->hashes = array($hash => serialize($glist));
            $this->view->sess = false;
        }
        if (!isset($glist->pages)) {
            $glist = new stdClass();
            $glist->pages = array(0 => array());
        }
         $pages = count($glist->pages);
		 
		 if ( ($page>$pages && $pages!=0) || ($page<1)) {
						header('Location: /404.html');
						die();
					}
	
		 
	
        if ($page >= $pages) $page = $pages;// - 1;
        if ($page < 1) $page = 1;
		
		
		
        $this->view->showButtons = !isset($this->_auth->Id) || $this->_auth->UserType == USER_TYPE_USER  ? true : false;

        if (isset($glist->pages[$page-1])) $list = $glist->pages[$page-1];
        else $list = array();
        $this->view->list = $list;
        $arPageAddr = array('controller' => 'catalog', 'action' => 'index', 'module' => 'default', 'title' => $title, 'cityname'=> $cityname);
        if ($category > 0) {
            $arPageAddr['category'] = $category;
        }
        if ($city > 0) {
            $arPageAddr['city'] = $city;
        }
        if ($min > 0) {
            $arPageAddr['min'] = $min;
        }
        if ($max > 0) {
            $arPageAddr['max'] = $max;
        }

        $dots = array('html'=> '...', 'class' => '', 'href' => '');
        if (count($list)) {
            $arPages = array();
            $arPages2 = array();
            if ($page > 1) {
                $arPages[] = array('html'=> '&nbsp;', 'class' => 'prev', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page-1)), null, true));
            }
            for ($i = 1; $i <= $pages; $i++) {
                $arPages2[] = array('html'=> $i , 'class' => $i == $page ? 'active' : '', 'href' => $i == $page ? '' : $this->_helper->url->url(array_merge($arPageAddr, array('page' => $i)), null, true));
            }

            if ($page > 10) {
                $arPages[] = $arPages2[0];
                $arPages[] = $dots;
                $arPages[] = $arPages2[$page - 2];
                $arPages[] = $arPages2[$page-1];
            }
            else {
                for ($i = 1; $i <= $page; $i++) { 
				$arPages[] = $arPages2[$i-1];
				}
            }

            if ($pages - $page > 11) {
                $arPages[] = $arPages2[$page + 1];
                $arPages[] = $dots;
                $arPages[] = $arPages2[$pages - 1];
            }
            else {
                for ($i = $page; $i < $pages; $i++) {
				$arPages[] = $arPages2[$i];}
            }

            if ($page <= $pages-1) {
                $arPages[] = array('html'=> '&nbsp;', 'class' => 'next', 'href' => $this->_helper->url->url(array_merge($arPageAddr, array('page' => $page + 1)), null, true));
            }

            $this->view->pages = $arPages;
        }

        $oAddress = new Address();
        $cityinfodb = $oAddress->getCatalogCityInfo($city);
        $cityinfo = new stdClass();
        if ($cityinfodb) {
            $cityinfo->id = $cityinfodb['city_id'];
            $cityinfo->country = $cityinfodb['country_id'];
            $cityinfo->name = $cityinfodb['cityname'].', '.$cityinfodb['name'];
            $cityinfo->prevcountry = $cityinfodb['country_id'];
        }
        else {
            $cityinfo->id = 0;
            $cityinfo->country = 0;
            $cityinfo->name = '';
            $cityinfo->prevcountry = 0;
        }

        $this->view->cityinfo = $cityinfo;
        $categorySeo = $catalog->getCategorySeo($category, $city);

        if (count($categorySeo) > 0) {
            $this->view->categorySEO = array('title' => $categorySeo[0]->CategoryName, 'text' => $categorySeo[0]->Description);
            $parentCategory = intval($categorySeo[0]->ParentId);
			if($categorySeo[0]->title != ""){
			$this->view->headTitle($categorySeo[0]->title);
			$this->view->headMeta()->appendName('description', $categorySeo[0]->desc);}
			else{
			$to = new Obj_DBTable_ServiceCategories();
			$catname = $to->getName($category);
			$parname = $to->getName($to->getParent($category));
			$this->view->headTitle($catname." - каталог надежных исполнителей - Мастерская Праздников");
			$this->view->headMeta()->appendName('description', $catname.". Ищете надежную компанию? Смотрите отзывы и цены на сайте Мастерская Праздников!");
			}
        }
        else {
	
            $ocat = new Obj_DBTable_ServiceCategories();
            $catcat = $ocat->getCategories($category);
            $parentCategory = $catcat['ParentId'];
            $this->view->categorySEO = array('title' => "", 'text' => "");
			$t = new Obj_DBTable_ServiceCategories();
			
			if($city > 0 && $category>0){
			$cityo = new Obj_DBTable_Citys();
			$cityname = $cityo->getName($city);
			$to = new Obj_DBTable_ServiceCategories();
			$catname = $to->getName($category);
			$parname = $to->getName($to->getParent($category));
			$this->view->headTitle($catname." (".$cityname.") - каталог надежных исполнителей - Мастерская Праздников");
			$this->view->headMeta()->appendName('description', $parname.". ".$catname." в городе ".$cityname.". Каталог. Отзывы, цены и описание услуг. Мастерская праздников.");
			
			}
			elseif($city == 0 && $category>0){
			$to = new Obj_DBTable_ServiceCategories();
			$catname = $to->getName($category);
			$parname = $to->getName($to->getParent($category));
			$this->view->headTitle($catname." - каталог надежных исполнителей - Мастерская Праздников");
			$this->view->headMeta()->appendName('description', $catname.". Ищете надежную компанию? Смотрите отзывы и цены на сайте Мастерская Праздников!");
			}
			else{
			$this->view->headTitle("Каталог надежных исполнителей - Мастерская Праздников");
			$this->view->headMeta()->appendName('description', "Ищете надежную компанию? Смотрите отзывы и цены на сайте Мастерская Праздников!");
			}
        }
        $this->view->categoryID = $parentCategory;
        $this->view->categoryID2 = $category;

        $this->view->secCategories = $parentCategory == 0 ? array() : $tab->getCategoriesForParent($parentCategory);
				$Obj_ivent_service = new Obj_DBTable_IventorServices();
        $this->view->ivent_service = $Obj_ivent_service->getAllCitiesByIventorId($category);
		$this->view->catalogid = $category;
		
		
		
		
		
		

    }

   
		
	
	
	
	

}



