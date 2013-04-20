<?php

class Zend_View_Helper_ClientComplexSearchResult extends Zend_View_Helper_Abstract {

    public $view;

	public function clientComplexSearchResult($res)
	{
        $layout = Zend_Layout::getMvcInstance();
        $this->view = $layout->getView();
        $d = strtotime($res->Date);
        $past = time() - $d > 86400 ? 'past' : '';
        $s = "<div class='reddi ".$past."'>";
        $s .= "<h4>".stripslashes(strip_tags($res->ActivityName))."</h4>";
        $k = count($res->services) - 1;
        foreach ($res->services as $i => $serv) $s .= $this->getService($serv, $res->Id, time() - $d > 86400, $i == $k);
        $s .= "</div>";
        return $s;
	}

    private function getService($serv, $sid, $past = false, $last = false) {
        ob_start();
        ?>
        <div class="request" id="s<?=$serv->ID?>">
            <?=$this->getServiceHeader($serv, $sid, $past)?>
            <ul>
                <?php
                    if (count($serv->eventors) > 0) {
                        foreach ($serv->eventors as $e) {
                            echo $this->getServiceEventor($e);
                        }
                    }
                    else echo $this->getEmptyEventor();
                ?>
                <?=!$last ? '<li class="oneone"><div class="grey one"></div></li>' : ''?>
            </ul>
        </div>
        <?php
        $s = ob_get_clean();
        return $s;
    }

    private function getServiceHeader($serv, $sid, $past) {
        ob_start();
        ?>
            <div class="headline">
                <div class="plusminus">
                    <a></a>
                </div>
                <div class="delsearch">
                    <div></div>
                    <a sid="<?=$serv->SubcategoryID?>|<?=$sid?>|<?=$serv->ID?>"></a>
                </div>
                <div class="categname"><?=$serv->Category?> &gt; <?=$serv->Subcategory?></div>
                <div class="specinfo">
                    <div class="date">
                        <span>Дата: </span> <?=date("d.m.Y", strtotime($serv->Date))?>
                        <?=($past ? "<b>(не актуально)</b>" : "")?>
                    </div>
                    <div class="city">
                        <span>Город: </span> <b><i><?=$serv->City?>, <?=$serv->Country?></i></b>
                    </div>
                    <div class="price">
                        <span>Бюджет: </span> <?=$serv->Min?> - <?=$serv->Max?> <?=$serv->Currency?>.
                    </div>
                </div>
            </div>
        <?php
        $s = ob_get_clean();
        return $s;
    }

    private function getServiceEventor($evs) {
        ob_start();
		$translit = new Zend_Filter_Translit();
        if (trim($evs->Image) != '') {
            $photo = $this->view->baseUrl().'/img/users/'.$evs->UserID."/70x70_".$evs->Image;
        } else {
            $photo = $this->view->baseUrl().'/70x70.png';
        }
        ?>
        <li style="display: none;">
            <div class="gwl unvisible"><div></div></div>
            <div class="grey">
                <div class="image">
                    <img src="<?=$photo?>" alt="" />
                </div>
                <div class="description">
                    <div class="title">
<a target="_blank"  href="<?=$this->view->url(array('ID' => $evs->ID,  'title' => $translit->filter($evs->CompanyName)), 'iventor')?>"><?=$evs->CompanyName?></a>
					<!--<a target="_blank" href="<?=$this->view->url(array('module' => 'default', 'controller' => 'iventor', 'action' => 'index', 'ID' => $evs->ID), null, true)?>"><?=$evs->CompanyName?></a>--></div>
                    <div class="phoneaddr">
                        <div class="phone">
                            <span>Телефон: </span> <?=$evs->CompanyPhone?>
                        </div>
                        <div class="addr">
                            <span>Сайт: </span> <a href="<?=$evs->Website?>"><?=str_replace("http://", '', $evs->Website)?></a>
                        </div>
                    </div>
                    <div class="descrtext"><?=$evs->Description?></div>
                </div>
            </div>
            <div class="gwl"><div></div></div>
        </li>

        <?php
        $s = ob_get_clean();
        return $s;
    }
    private function getEmptyEventor() {
        ob_start();
        ?>
            <li style="display: none;">
               <div class="grey">
                   <div class="notfound">
                       К сожалению в данное время по вашему запросу нет доступных ивенторов. Мы в кратчайшие сроки постараемся найти ивенторов для вас. За обновлениеями сервиса, следите с помощью рассылки или на наших социальных старницах.
                   </div>
               </div>
                <div class="gwl"><div></div></div>
                <div class="gwl"><div></div></div>
            </li>
        <?php
        $s = ob_get_clean();
        return $s;
    }


}