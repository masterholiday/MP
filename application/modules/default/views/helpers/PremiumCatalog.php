<?php

class Zend_View_Helper_PremiumCatalog extends Zend_View_Helper_Abstract {

    public $view;

	public function premiumCatalog($ev, $showButtons)
	{
        //print_r($ev);
        //die();
		$translit = new Zend_Filter_Translit();
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();
        ob_start();
        $image = trim($ev->Image) == '' ? 'catlogo.png' : 'img/users/'.$ev->EventorUserID."/150x150_".$ev->Image;
        ?>
            <div class="premium_eventor">
                <h3>
				<? //echo $ev->CompanyName;?>
				<a target="_blank"  href="<?=$this->view->url(array('ID' => $ev->EventorID,  'title' => $translit->filter($ev->CompanyName)), 'iventor')?>"><?=$ev->CompanyName?></a></h3>
				
				
                <div class="block">
                    <div class="delime"></div>
                    <div class="image">
                        <a target="_blank"  href="<?=$this->view->url(array('ID' => $ev->EventorID,  'title' => $translit->filter($ev->CompanyName)), 'iventor')?>">
                            <img src="<?=$this->view->baseUrl().'/'.$image?>" alt="" />
                        </a>
                    </div>
                    <div class="descrt"><?=trim($ev->Description)?></div>
                    <div class="clear h9"></div>
                    <div class="city">
                        <?=$ev->CityName?>, <?=$ev->minPrice?> - <?=$ev->maxPrice?> <?=$ev->CountryID == 9908 ? 'грн.' : 'руб.'?>
                    </div>
                    <div class="phone">
                        <span>Телефон: </span><?=$ev->CompanyPhone?>
                    </div>
                    <?php if($showButtons) { ?>
                    <div class="relpos">
                        <a class="iphone<?=$ev->CallID > 0 ? ' iphoned' : ''?>" eid="<?=$ev->EventorUserID?>" sid="<?=$ev->EventorServiceID?>"></a>
                        <a class="istar<?=$ev->StarID > 0 ? ' istard' : ''?>" eid="<?=$ev->EventorUserID?>" sid="<?=$ev->EventorServiceID?>"></a>
                        <div class="myhint callme"></div>
                        <div class="myhint callwait"></div>
                        <div class="myhint addstar"></div>
                        <div class="myhint delstar"></div>
                        <div class="prem"></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <?php
        $s = ob_get_clean();
        return $s;
	}

}