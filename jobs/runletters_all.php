<?php


mysql_connect("localhost","holiday_user","wzhdQcL5");
mysql_select_db("letterbase_all") or die(mysql_error());

function smtpmail($my_array, $subject)
	{

		require_once('mailer/class.phpmailer.php');
		$mail = new PHPMailer(true);
		$mail->IsSMTP();

		try {
		  $body  = file_get_contents('http://masterholiday.net/letters/fornew2.html');
		  $body  = eregi_replace("[\]",'',$body);
		  
		  $mail->CharSet  = 'utf-8'; 
		  $mail->IsHTML(true);
		  //$mail->SMTPSecure = "ssl";
		  $mail->Host       = "mail.masterholiday.net"; 
		  $mail->SMTPDebug  = 2; 
		  $mail->SMTPAuth   = true;
		  $mail->Port       = 250; 
		  $mail->Username   = "info@masterholiday.net";
		  $mail->Password   = "wCKMQLkQ";
		  $mail->Sender = "info@masterholiday.net"; 
		  $mail->AddCustomHeader('Reply-to:info@masterholiday.net'); 
		  $mail->From="info@masterholiday.net"; 
		  $mail->FromName="Мастерская Праздников";
		  $mail->SetFrom("info@masterholiday.net", "Мастерская Праздников");
		  $mail->AddReplyTo('info@masterholiday.net', 'Мастерская Праздников');
		  $mail->Subject = htmlspecialchars($subject);
		  $mail->MsgHTML($body);
		  if($attach)  $mail->AddAttachment($attach);
		  
		  foreach ($my_array as $value) {
			echo $value."<br/>";
			$mail->AddAddress($value);
		   $mail->Send();
		   $mail->ClearAllRecipients(); // reset the `To:` list to empty
			}

			
			
		  
		  echo "Message sent Ok!</p>\n";
		  return true;
		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); 
		  return false;
		} catch (Exception $e) {
		echo $e->getMessage(); 
		return false;
		}
		
	}


// Allbase except mail and gmail
//$query_all = "SELECT *  FROM `addres` WHERE sent=0 AND `email` NOT LIKE '%mail.ru' AND (`email` NOT LIKE '%list.ru') AND (`email` NOT LIKE '%bk.ru') AND (`email` NOT LIKE '%inbox.ru') AND (`email` NOT LIKE '%gmail.com') ORDER BY RAND( )";
//Allbase except gmail
$query_all = "SELECT *  FROM `addres` WHERE sent=0 AND `email` NOT LIKE '%gmail.com' ORDER BY RAND( )";
// Baza Artistov except mail and gmail
//$query_all = "SELECT *  FROM `bazaartistiv` WHERE sent=0 AND `email` NOT LIKE '%mail.ru' AND (`email` NOT LIKE '%list.ru') AND (`email` NOT LIKE '%bk.ru') AND (`email` NOT LIKE '%inbox.ru')  AND (`email` NOT LIKE '%gmail.com') ORDER BY RAND( )";
// Baza Artistov except gmail
//$query_all = "SELECT *  FROM `bazaartistiv` WHERE sent=0 AND `email` NOT LIKE '%gmail.com' ORDER BY RAND( )";
$rez_all=mysql_query($query_all);


$i=1;
$my_array = array ();
$subject = 'Интернет сервис для поиска целевых клиентов';
while($row = mysql_fetch_array($rez_all)){
	if($i<11){
		$id = $row['id'];
		$email = $row['email'];
		$my_array[] = $email;
		$query= mysql_query("update addres set sent=100 where id =$id")or die(mysql_error());
		//$query= mysql_query("update bazaartistiv set sent=100 where id =$id")or die(mysql_error());
		$i++;
		}
	}

	smtpmail($my_array, $subject);
		


?>
