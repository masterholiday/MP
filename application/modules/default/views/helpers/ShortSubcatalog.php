<?php

class Zend_View_Helper_ShortSubcatalog extends Zend_View_Helper_Abstract {

	public function shortSubcatalog($category)
	{
	$translit = new Zend_Filter_Translit();
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $s = "<ul id='sh-pcat".$category->ID."'>";
        $i = 0;
        $list = $category->subcategories;
        $step = (int) ceil(count($list) / 3);
        while ($i < $step) {
            for ($j = 0; $j < 3; $j++) {
                if (isset($list[$i + $step*$j])) {

				
                    $s .= "<li id='sh-subcat".$list[$i + $step*$j]->ID."'>
					<a href='".$view->url(array('title' => $list[$i +  + $step*$j]->Alias, 'category' => $list[$i + $step*$j]->ID ,'cityname'=>'all', 'page'=>'1'), 'catalog')."/'><span>".number_format($list[$i + $step*$j]->total, 0, '', ' ')."</span> ".stripslashes($list[$i +  + $step*$j]->Category)."</a></li>";
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