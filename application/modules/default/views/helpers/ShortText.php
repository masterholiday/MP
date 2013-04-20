<?php

class Zend_View_Helper_ShortText extends Zend_View_Helper_Abstract {

    public $view;

	public function shortText($text, $max)
	{
        $enc = mb_detect_encoding($text);
        if (mb_strlen($text, $enc) > $max) {
            $res = mb_substr($text, 0, $max - 3, $enc) . "...";
            return $res;
        }
        return $text;
	}

}