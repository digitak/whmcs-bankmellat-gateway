<?php
session_start();
?>
<html>
<head>
	<script language="javascript" type="text/javascript">
	function postRefId (refIdValue)
	{
		var form = document.createElement("form");
		form.setAttribute("method", "POST");
		form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");
		form.setAttribute("target", "_self");
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("name", "RefId");
		hiddenField.setAttribute("value", refIdValue);
		form.appendChild(hiddenField);

		document.body.appendChild(form);
		form.submit();
		document.body.removeChild(form);
	}

	</script>
</head>
<body>
</body>
</html>

<?php
require_once("lib/nusoap.php");
include("config.php");

$client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
$namespace='http://interfaces.core.sw.bps.com/';

$_SESSION['moid'] = $_POST['ResNum'];
$invoideid = $_POST['ResNum'];
$orderId = $_POST['ResNum'].rand(1,99);
$amount = $_POST['Amount'];
$_SESSION['amountTotal'] = intval($_POST['Amount']);
$localDate = date('Ynd');
$localTime = date('His');
$callBackUrl = $_POST['SysURL']."modules/gateways/callback/Mellat.php?iid=$invoideid";
$additionalData = "" ;
$payerId = "0";
$userName ;
$parameters = array(
					'terminalId' => $terminalId,
					'userName' => $userName,
					'userPassword' => $userPassword,
					'orderId' => $orderId,
					'amount' => $amount,
					'localDate' => $localDate,
					'localTime' => $localTime,
					'additionalData' => $additionalData,
					'callBackUrl' => $callBackUrl,
					'payerId' => $payerId);

$result = $client->call('bpPayRequest', $parameters, $namespace);

$resultStr  = $result;

$res = explode (',',$resultStr);
$ResCode = $res[0];
$res[1];

if ($ResCode == "0")
{
	echo "<script language='javascript' type='text/javascript'>postRefId('" . $res[1] . "');</script>";
}
else
{
	echo "<script>alert('امکان اتصال وجود ندارد ، لطفاً دوباره تلاش کنید.');</script>";
	echo "<script>window.location ='".$_POST['SysURL']."/viewinvoice.php?id=$invoideid" ."'</script>";
}
?>