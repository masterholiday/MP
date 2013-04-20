<?php

class Zend_View_Helper_BasicCatalog extends Zend_View_Helper_Abstract {

    public $view;

	public function basicCatalog($ev)
	{
	$translit = new Zend_Filter_Translit();
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();
        ob_start();
        $image = trim($ev->Image) == '' ? 'catlogo.png' : 'img/users/'.$ev->EventorUserID."/150x150_".$ev->Image;
        ?>
        <div class="basic_eventor">
            <h3>
			<a target="_blank"  href="<?=$this->view->url(array('ID' => $ev->EventorID,  'title' => $translit->filter($ev->CompanyName)), 'iventor')?>"><?=$ev->CompanyName?></a>
			<!--<a target="_blank" href="<?=$this->view->url(array('module' => 'default', 'controller' => 'iventor', 'action' => 'index', 'ID' => $ev->EventorID), null, true)?>"><?=$ev->CompanyName?></a>-->
			
			</h3>
            <div class="block">
                <div class="delime"></div>
                <div class="image">
                    <img src="<?=$this->view->baseUrl().'/'.$image?>" alt="" />
                </div>
                <div class="clear h9"></div>
                <div class="city">
                    <?=$ev->CityName?><br /><?=$ev->minPrice?> - <?=$ev->maxPrice?> <?=$ev->CountryID == 9908 ? 'грн.' : 'руб.'?>
                </div>
                <div class="phone">
                    <span>Телефон: </span><?=$ev->CompanyPhone?>
                </div>
            </div>
        </div>
        <?php
        $s = ob_get_clean();
        return $s;
	}

}