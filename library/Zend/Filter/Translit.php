<?
class Zend_Filter_Translit implements Zend_Filter_Interface 
{
    
    /**
     * Производит фильтрацию в соответствии с назначением фильтра
     *
     * @param string $value
     * @return string
     */
    public function filter($zagol) 
    {
			$zagol = trim(rtrim($zagol, " \t.")); // Удоляет пробел и табуляцию
			//$register = mb_strtolower($zagol); // Преобразует строку в нижний регистр
			$register = mb_convert_case($zagol, MB_CASE_LOWER, "UTF-8");
			$rus = array(
			'а','б','в','г','д','е','ё','ж','з','є','і','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
			'~','!','@','#','%','^','&','*','(',')','_','+','-','=','`',',','.','/','<','>','{','}','[',']',';','\'','\\',':','"','|',
			' ','№','$','«','»','"'
			);
			$eng = array(
			'a','b','v','g','d','e','e','zh','z','e','i','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','scsh','','y','','','yu','ya',
			'','','','','','','','','','','-','','-','','','','.','','','','','','','','','','','','','',
			'-','','','','',''
			);
			$url = str_replace($rus, $eng, $register);
			$url = preg_replace('#(\W)+#','-', $url);
			//echo $url ;
        
        // Массив символов
       /* $letters = array(
			"а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e",
			"ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k",
            "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c",
            "ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "i", "ь" => "", "ъ" => "",
            "э" => "e", "ю" => "yu", "я" => "ya",
			"А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E",
			"Ё" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "J", "К" => "K",
            "Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C",
            "Ч" => "CH", "Ш" => "SH", "Щ" => "SH", "Ы" => "I", "Ь" => "", "Ъ" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA",
            
		);
		
		// Проходим по массиву и заменяем каждый символ фильтруемого значения
		foreach($letters as $letterVal => $letterKey) {
			$value = str_replace($letterVal, $letterKey, $value);
		}*/
		
        return $url;
    }
}

?>