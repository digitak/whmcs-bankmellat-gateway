<?php

function Mellat_config() {
    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"Mellat Online Payment"),
     "Currencies" => array("FriendlyName" => "Currencies", "Type" => "dropdown", "Options" => "Rial,Toman", ),

    );
        return $configarray;
}

function Mellat_link($params) {
$systemurl = $params['systemurl'];
$invoiceNumber = $params['invoiceid'];

if ($params['Currencies'] == 'Rial')
{
     $amount = $params['amount']-'.00';
}else{
        $amount = $params['amount']-'.00'.'0';
        }
       


 $code = '<form id="pay" method="POST" action="modules/gateways/Mellat/Pay.php">
          <input type="hidden" name="ResNum" value="'.$invoiceNumber.'" />
          <input type="hidden" name="Amount" value="'.$amount.'">
          <input type="hidden" name="SysURL" value="'.$systemurl.'">
          <input type="submit" class="btn btn-success" value="پرداخت آنلاین" />
  </form>';

        return $code;



}
function Mellat_refund($params) {

echo $params['invoiceid'];

            }


?>