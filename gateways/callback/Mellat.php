<?php
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");
include("../Mellat/config.php");
require_once("../Mellat/lib/nusoap.php");

$client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
$namespace = 'http://interfaces.core.sw.bps.com/';

$resId = $_POST['ResCode'];
$orderId = $_POST['SaleOrderId'];
$verifySaleOrderId = $_POST['SaleOrderId'];
$verifySaleReferenceId = $_POST['SaleReferenceId'];
$invoiceid = $_REQUEST['iid'] ;

$err = $client->getError();
if ($err)
{
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	exit();
}


$parameters = array(
					'terminalId' => $terminalId,
					'userName' => $userName,
					'userPassword' => $userPassword,
					'orderId' => $orderId,
					'saleOrderId' => $verifySaleOrderId,
					'saleReferenceId' => $verifySaleReferenceId);


$VerifyAnswer = $client->call('bpVerifyRequest' , $parameters , $namespace);

if ($VerifyAnswer == '0' and $$resId == 0)
{
	$parameters = array(
						'terminalId' => $terminalId,
						'userName' => $userName,
						'userPassword' => $userPassword,
						'orderId' => $orderId,
						'saleOrderId' => $verifySaleOrderId,
						'saleReferenceId' => $verifySaleReferenceId);

	$SetlleAnswer = $client->call('bpSettleRequest' , $parameters , $namespace);
	
	if ($SetlleAnswer == '0')
	{
		$Pay_Status = 'OK';
	}
}

if ($VerifyAnswer <> '0' AND $VerifyAnswer != '')
{
    $parameters = array(
						'terminalId' => $terminalId,
						'userName' => $userName,
						'userPassword' => $userPassword,
						'orderId' => $orderId,
						'saleOrderId' => $verifySaleOrderId,
						'saleReferenceId' => $verifySaleReferenceId);

	$InquiryAnswer = $client->call('bpInquiryRequest', $parameters, $namespace);

	if ($InquiryAnswer == '0')
	{
		$parameters = array(
							'terminalId' => $terminalId,
							'userName' => $userName,
							'userPassword' => $userPassword,
							'orderId' => $orderId,
							'saleOrderId' => $verifySaleOrderId,
							'saleReferenceId' => $verifySaleReferenceId);

		$SetlleAnswer = $client->call('bpSettleRequest', $parameters, $namespace);
	}
	else
	{
		$parameters = array(
							'terminalId' => $terminalId,
							'userName' => $userName,
							'userPassword' => $userPassword,
							'orderId' => $orderId,
							'saleOrderId' => $verifySaleOrderId,
							'saleReferenceId' => $verifySaleReferenceId);

		$result = $client->call('bpReversalRequest', $parameters, $namespace);
	}
}

$action = $SystemURL."/viewinvoice.php?id="."$invoiceid";
$gatewaymodule = "Mellat";

$GATEWAY = getGatewayVariables($gatewaymodule);

if (!$GATEWAY["type"])
{
	exit("Module Not Activated");
}

if ($Pay_Status == 'OK')
{
	addInvoicePayment($invoiceid , $verifySaleReferenceId , $_SESSION['amountTotal'] , $fee , $gatewaymodule);
	logTransaction($GATEWAY["name"],$_POST,"Successful");
	echo " <script>window.location ='".$action."'</script>";
}
else
{
	logTransaction($GATEWAY["name"],$_POST,"Unsuccessful");
	echo " <script>window.location ='".$action."'</script>";
}
