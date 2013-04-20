<?php

mysql_connect("localhost","holiday_user","wzhdQcL5");
mysql_select_db("masterholid2ver") or die(mysql_error());




$rus = array(
			'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
			'~','!','@','#','%','^','&','*','(',')','_','+','-','=','`',',','.','/','<','>','{','}','[',']',';','\'','\\',':','"','|',
			' ','№','$','«','»','"'
			);
			$eng = array(
			'a','b','v','g','d','e','e','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','scsh','','y','','','yu','ya',
			'','','','','','','','','','','-','','-','','','','.','','','','','','','','','','','','','',
			'-','','','','',''
			);
mysql_query('set names utf8');
$rez_all=mysql_query("SELECT *  FROM `service_categories` ORDER BY Id");

while($row = mysql_fetch_array($rez_all)){

		$id = $row['Id'];
		$name = $row['CategoryName'];
			$newname = trim(rtrim($name, " \t.")); // Удоляет пробел и табуляцию
			$register = mb_convert_case($newname, MB_CASE_LOWER, "UTF-8");
			$url = str_replace($rus, $eng, $register);
			$url = preg_replace('#(\W)+#','-', $url);
		echo "Instering #".$id." with alias:".$url."<br>";
		$query= mysql_query("update service_categories set alias='$url' where Id ='$id'")or die(mysql_error());


	}

 
?>