<?php

class Zend_View_Helper_TopInfo extends Zend_View_Helper_Abstract {

    public $view;

	public function topInfo()
	{
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            if (Zend_Auth::getInstance()->getIdentity()->UserType == USER_TYPE_USER) return $this->showUser(Zend_Auth::getInstance()->getIdentity());
            else return $this->showEventor(Zend_Auth::getInstance()->getIdentity());
        }
        else {
            return $this->showSimple();
        }
	}

    private function showSimple() {
        ob_start();
        ?>
        <div class="top-info-not-auth">
            <h2>Планируете праздник?</h2>
			На нашем сайте Вы легко сможете подобрать надежных <br />исполнителей для Вашего мероприятия!
        </div>
        <?php
        $s = ob_get_clean();
        return $s;
    }

    private function showUser($user) {
        ob_start();

        if (trim($user->SocialID) != '') {
            $oUser = new User();
            $uname = $oUser->getClientName($user->Id);
        }
        else {
            $uname = trim($user->Email);
        }
        ?>
        <div class="top-info-user">
            <h3>Приветствуем вас <?=$uname?>!</h3>
            <div style="text-align: center;">Это ваш личный кабинет, здесь вы можете просматривать ваши запросы и найденных ивенторов.</div>
        </div>
        <?php
        $s = ob_get_clean();
        return $s;
    }

    private function showEventor($user) {
        ob_start();
        $oSearch = new Search();
        $total = $oSearch->getEventorSearchesCount($user->Id, 'complex');
        $total2 = $oSearch->getEventorSearchesCount($user->Id, 'catalog');
        $invinfo = new Obj_DBTable_InventorInfo();
        $info = $invinfo->getIventorInformation($user->Id);
        if ($info->Premium == 1) {
            $diff = strtotime($info->PremiumUntil) - time();
            $diff = $diff / (60*60*24);
            $diff = intval(ceil($diff));
            ?>
            <div class="top-info-user">
                <h3>Приветствуем вас <?=$user->Email?></h3>
                <div class="leftscount">Комплексных запросов: <a href="<?=$this->view->baseUrl()."/iventor/searches/type/complex/";?>"><?=$total?></a></div>
                <div class="leftscount rightscount">Запросов из каталога: <a href="<?=$this->view->baseUrl()."/iventor/searches/type/catalog/";?>"><?=$total2?></a></div>
                <div class="clear"></div>
                <div class="prem">
                    У вас <b>“Премиум”</b> аккаунт  -  <span <?=$diff < 10 ? 'class="r"' : ''?>><?=$diff?> дней</span>
                </div>
                <div class="continue"><a href="<?=$this->view->baseUrl()?>/iventor/account">продлить</a></div>
            </div>
            <?php
            $s = ob_get_clean();
            return $s;
        }
        else {
            ?>
            <div class="top-info-user">
                <h3>Приветствуем вас <?=$user->Email?></h3>
                <div class="freeuser1">У вас бесплатный аккаунт.</div>
                <div class="freeuser2">Вы упускаете массу полезных возможностей.</div>
                <div class="continue2"><a href="<?=$this->view->baseUrl()?>/iventor/account">Купить “Премиум” аккаунт.</a></div>
            </div>
            <?php
            $s = ob_get_clean();
            return $s;
        }
    }
}