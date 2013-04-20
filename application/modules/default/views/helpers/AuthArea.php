<?php

class Zend_View_Helper_AuthArea extends Zend_View_Helper_Abstract {


    public $view;
	public function authArea()
	{	
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();
		

        $s = "<div class='mapuserarea'>";
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $s .= $this->showAuthenficated(Zend_Auth::getInstance()->getIdentity());
        }
        else {
            $s .= $this->showSimple();
        }
        $s .= "</div>";
        return $s;
	}

    private function showAuthenficated($user) {
	 $translit = new Zend_Filter_Translit();
	 $invinfo = new Obj_DBTable_InventorInfo();
	 $i = $invinfo->getInventorInfoByUserId($user->Id);
	 
        if ($user->UserType == USER_TYPE_USER) {
            ob_start();
            ?>
                <map id="mapuserarea" name="mapuserarea">
                     <area href="<?=$this->view->baseUrl()."/index/exit/";?>" title="Выйти" shape="circle" coords="31,84,30" />
                    <area href="<?=$this->view->baseUrl()."/client/";?>" title="Кабинет" shape="circle" coords="85,47,47" />
                </map>
                <img src="<?=$this->view->baseUrl()?>/images/profile_btn.png" width="132" height="116" alt="" usemap="#mapuserarea" />
            <?php
            $s = ob_get_clean();
            return $s;
        } else {
            ob_start();
			
            ?>	<map id="mapuserarea" name="mapuserarea">
                    <area href="<?=$this->view->baseUrl()."/index/exit/";?>" title="Выйти" shape="circle" coords="31,84,30" />
                    <area href="<?=$this->view->url(array('ID' => $i['Id'],  'title' => $translit->filter($i['CompanyName'])), 'iventor')?>" title="Кабинет" shape="circle" coords="85,47,47" />
					
					
                </map>
				
                <img src="<?=$this->view->baseUrl()?>/images/profile_btn.png" width="132" height="116" alt="" usemap="#mapuserarea" />
            <?php
            $s = ob_get_clean();
			
            return $s;
        }
		
    }

    private function showSimple() {
        ob_start();
        ?>
            <map id="mapuserarea" name="mapuserarea">
                <area href="#" onclick="showLoginPopup(); return false;" title="Войти" shape="circle" coords="31,84,30" />
                <area href="#" onclick="showRegisterMain(); return false;" title="Регистрация" shape="circle" coords="85,47,47" />
            </map>
            <img src="<?=$this->view->baseUrl()?>/images/circle_btn.png" width="132" height="116" alt="" usemap="#mapuserarea" />
        <?php
        $s = ob_get_clean();
        return $s;
    }
}