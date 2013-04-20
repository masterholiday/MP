<?php
class OpenController extends DefaultBaseController
{
   	public function indexAction()
    {
    	die();
    }

    public function resultAction(){
        $signature = "dUVEYIeXvQaZZTHSOjaZWmZTBN0KSnWEQmo";
        $xmlstr = base64_decode($this->getRequest()->getParam('operation_xml'));
        $xmlsig = $this->getRequest()->getParam('signature', '');
        $sign = base64_encode(sha1($signature.$xmlstr.$signature, 1));
        if ($sign != $xmlsig) {
            die();
        }
        $xml = simplexml_load_string($xmlstr);
        $Objfinance = new Obj_DBTable_Finance();
        $Objfinance->updateFinance($xml->order_id, $xml->amount, $xml->status);
        if($xml->status == 'success'){
            $finance = $Objfinance->getFinanceByOrderId($xml->order_id);
            $Objiveninfo = new Obj_DBTable_InventorInfo();
	       	$Objiveninfo->addPremium($finance['IventorId'], $finance['Days']);

            $invinfo = new Obj_DBTable_InventorInfo();
            $info = $invinfo->getIventorInformation($finance['IventorId']);
            $arr = array();
            $arr['paysum'] = number_format($finance['Sum'], 2);
            $arr['paydays'] = intval($finance['Days']);
            $arr['premiumuntil'] = date('d.m.Y', strtotime($info->PremiumUntil));
            $this->sendHTMLLetter(file_get_contents(APPLICATION_PATH.'/../htmlletters/paymentreceived.html'), 'Вы внесли платеж на сервис Мастерская праздников', $info->Email, $arr);
        }
        die();
    }
}


