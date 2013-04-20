<?php

class Zend_View_Helper_ClientCatalogSearchResult extends Zend_View_Helper_Abstract {

    public $view;

	public function clientCatalogSearchResult($list)
	{
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();
        $s = "<div class='reddi' style='width: 931px; padding-bottom: 12px;'>";
        foreach ($list as $i => $l) $s .= $this->getResult($l, $i % 2 == 1);
        $s .= "</div>";
        return $s;
	}

    private function getResult($res, $dark) {
        ob_start();
		$translit = new Zend_Filter_Translit();
        if (trim($res->Image) != '') {
            $photo = $this->view->baseUrl().'/img/users/'.$res->EventorID."/150x150_".$res->Image;
        } else {
            $photo = $this->view->baseUrl().'/iventor.png';
        }
        $currency = $res->CountryID == 9908 ? "грн." : "руб.";
        ?>
        <div class="complexusr<?=($dark ? ' cusrd' : '')?>">
            <div class="image">
                <img src="<?=$photo?>" alt="">
            </div>
            <div class="evinfo">
                <div class="evname">
				<a target="_blank"  href="<?=$this->view->url(array('ID' => $res->EvID,  'title' => $translit->filter($res->CompanyName)), 'iventor')?>"><?=$res->CompanyName?></a>
                    <!--<a href="<?=$this->view->url(array('module' => 'default', 'controller' => 'iventor', 'action' => 'index', 'ID' => $res->EvID), null, true)?>"><?=$res->CompanyName?></a>-->
                </div>

                <div class="evcat"><?=$res->Category?> &gt; <?=$res->Subcategory?></div>
                <div class="evprice"><span>Бюджет: </span>от <?=$res->minPrice?> до <?=$res->maxPrice?> <?=$currency?></div>
                <div class="clear"></div>
                <div class="evphone"><span>Телефон: </span><?=$res->CompanyPhone?></div>
                <div class="evcity"><span>Город: </span><?=$res->CityName?>, <?=$res->CountryName?></div>
                <div class="clear"></div>
                <div class="evurl"><span>Сайт: </span><a target="_blank" href="<?=$res->Website?>"><?=$res->Website?></a></div>
                <div class="clear"></div>
                <div class="evtext"><?=$res->Description?></div>
                <div class="delsearch2">
                    <div></div>
                    <a scatid="<?=$res->ID?>"></a>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="complexusrd"><div></div></div>
        <?php
        $s = ob_get_clean();
        return $s;
    }

}