<?php

class Zend_View_Helper_EventorComplexSearchResult extends Zend_View_Helper_Abstract {

    public $view;

	public function eventorComplexSearchResult($res)
	{
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();
        $s = "<div class='reddi' style='width: 930px; padding: 0px 10px; margin: 0px auto;'>";
        foreach ($res as $k => $v) $s .= $this->showResult($v, $k % 2 == 1);
        $s .= "<div class='clear h11'></div>";
        $s .= "</div><div class='clear h19'></div>";
        return $s;
	}

    private function showResult($search, $dark = false) {
        //print_r($search);die();
        ob_start();
        $past = strtotime($search->Date) < time();
        ?>
        <div class="complexev<?=($dark ? ' cevd' : '')?><?=($past ? ' cevdpast' : '')?>">
            <div class="name"><?=$search->Name?></div>
            <div class="evname"><span>Мероприятие:</span> <?=$search->ActivityName?></div>
            <div class="date"><span>Дата:</span> <?=date("d.m.Y", strtotime($search->Date))?> <?=$past ? '(не актуально)' : ''?></div>
            <div class="clear"></div>
            <div class="cat"><?=$search->Category?> &gt; <?=$search->SubCategory?></div>
            <div class="money"><span> Бюджет:</span> от <?=$search->minPrice?> до <?=$search->maxPrice?> <?=$search->CountryID == 9908 ? "грн" : "руб"?></div>
            <div class="clear"></div>
            <div class="city"><span>Город:</span> <?=$search->CityName?>, <?=$search->CountryName?></div>
            <div class="phone">Телефон: <?=$this->preparePhone($search->searchPhoneNumber)?></div>
            <div class="clear"></div>
            <div class="descr"><?=$search->Info?></div>
            <div class="butts">
                <a class="delete" onclick="deleteEventorSearch(<?=$search->DID?>);"></a>
                <a href="mailto:<?=$search->Email?>" class="email"></a>
                <div class="deletep"></div>
                <div class="emailp"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="complexevd"><div></div></div>
        <?php
        $s = ob_get_clean();
        return $s;
    }

    private function preparePhone($phone) {
        $phone = trim($phone);
        $subphone = substr($phone, 7, 3);
        $phone = '+'.substr($phone, 0, 7)."...".substr($phone, 10);
        $s = "<span>".$phone."</span> <a rel='".$subphone."'>показать</a>";
        return $s;
    }

}