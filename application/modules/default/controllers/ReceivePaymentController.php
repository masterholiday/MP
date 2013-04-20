<?php
class ReceivePaymentController extends DefaultBaseController
{
    protected $_needbeloggedin = false;
		
   	public function indexAction()
    {
           die();
    	
    }
    
    public function yandexAction()
    {
        die();
        ob_start();
        $sha = "";
        $sha .= $_POST['notification_type'].'&'.$_POST["operation_id"]."&".$_POST["amount"]."&".$_POST["currency"]."&".$_POST["datetime"]."&".$_POST["sender"]."&".$_POST["codepro"]."&"."CkiT0B99LCtYEDjsg+PJbES7"."&".$_POST["label"];
        $sha = sha1($sha);

        var_dump($sha == $_POST['sha1_hash']);
        print_r($_POST);
        $s = ob_get_clean();
        file_put_contents("/tmp/yandex.log", $s);
        die();
    }
    
    
}


