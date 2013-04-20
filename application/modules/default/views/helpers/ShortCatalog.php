<?php

class Zend_View_Helper_ShortCatalog extends Zend_View_Helper_Abstract {

	public function shortCatalog($list)
	{
        $s = "<ul>";
        $i = 0;
        $step = (int) ceil(count($list) / 3);
        //echo $step;
        //print_r($list);
        while ($i < $step) {
            for ($j = 0; $j < 3; $j++) {
                if (isset($list[$i + $step*$j])) {

                    $s .= "<li id='sh-cat".$list[$i + $step*$j]->ID."'><div>".number_format($list[$i + $step*$j]->total, 0, '', ' ')."</div> ".stripslashes($list[$i +  + $step*$j]->Category)."</li>";
                }
                else {
                    $s .= "<li style='visibility: hidden;'>&nbsp;</li>";
                }
            }
            $i++;
        }
        $s .= "</ul>";

        return $s;
	}
}