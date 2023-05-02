<?php
//const
$signature = "SIGNATURE"; //put your signature, from Ari10 tech team
$widgetId = "WIDGET ID"; //put your widget id, from Ari10 tech team
$enviroment = "test"; //set env test or production
$returnUrl = "https://ari10.com";

//variables
$amount = 100; //set value of transaction (we suggest to use 100,200,500)

//enviroment URL
$enviromentJsName = "main";
$enviromentBaseUrl = "https://gateway.ari10.com";
if ($enviroment == "test"){
	$enviromentJsName = "main-tst";
	$enviromentBaseUrl = "https://gateway-dev.ari10.com";
}

//data
$data =  [
   "widgetBaseUrl" => $returnUrl, //return url
   "offeredCurrencyCode" => "PLN", //currency
   "offeredAmount" => $amount, //amount
]; 

//signature
$hashedSignature = hash_hmac(
    'sha256',
	($amount*100).$data['offeredCurrencyCode'].$data['widgetBaseUrl'],
    $signature,
    false
);
$data['signature'] = $hashedSignature;

//curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $enviromentBaseUrl.'/goods/transaction');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Ari10-Widget-Id: '.$widgetId;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = json_decode(curl_exec($ch));
curl_close($ch);

//!!!remember to store $result->transactionId on your side
;
?>
<html>
	<title>BLIK payment</title>
	<head>
		<script>
			widget_id_6851681344231 = "<?php echo $widgetId ?>"
			widget_language_1776290735652 = "pl"
		</script>
		<script src="<?php echo $enviromentBaseUrl ?>/widget/<?php echo $enviromentJsName ?>.min.js"></script>
	</head>
	<body>
		<script>
			function showWidget(){
			
			   window.dispatchEvent(
			     new CustomEvent('ari10-widget-start-commodities-payment-request', {
			       detail: {
			         transactionId: '<?php echo $result->transactionId ?>'
			       }
			     })
			   );
			
			   elem = document.getElementsByClassName('App');
			   if (elem.length == 0) {
			       setTimeout(showWidget, 200);
			   }
			}

			window.onload = showWidget

			window.addEventListener(
				'ari10-widget-transaction-canceled-event',
				(event) => {
					console.log('Received transaction canceled event: ', JSON.stringify(event.detail));
					window.location.href = '<?php echo $returnUrl ?>';
				}
			);
		</script>
	</body>
</html>