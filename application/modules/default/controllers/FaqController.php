<?php

class FaqController extends DefaultBaseController
{
    protected $_needbeloggedin = false;

    public function indexAction()
    {
	
			$uri = $this->getRequest()->getRequestUri();
			
			 
			
			if( ($uri != "/faq/") && ($uri!= "/faq") && ($uri != "/faq/index/type/iventor") && ($uri!= "/faq/index/type/iventor/") && ($uri != "/faq/index/type/user/") && ($uri != "/faq/index/type/user")) {
					 header('Location: /404.html');
							die();
					
			}

     	$Obj_faq_cat = new Obj_DBTable_FaqCategories();
     	   

        $type = $this->getRequest()->getParam('type', 'user');
        if ($type != 'iventor') {
            $type = 'user';
            $this->view->faq_cat = $Obj_faq_cat->getAllFaqCategoryByClient();
            $this->view->Obj_faq = new Obj_DBTable_FAQ();
        }else{
            $this->view->faq_cat = $Obj_faq_cat->getAllFaqCategoryByIventor();
            $this->view->Obj_faq = new Obj_DBTable_FAQ();
        }
        $this->view->type = $type;
        

    }

    public function iventorAction() {
		$uri = $this->getRequest()->getRequestUri();
			if( ($uri != "/faq/iventor/") && ($uri!= "/faq/iventor")) {
					 header('Location: /404.html');
							die();
					
			}
    }
    
   
}



