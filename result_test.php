<?php
require ("functions.php");
//$url="http://erpecc.ttcs.co.zw:52000/paynow/result.php"; //result url 
$url="http://192.168.1.105:51000/paynow/result.php?ref=54ae58a6dac6c"; //result url 
$Merchantkey="4f4c88ec-40db-495f-ac2e-180de01dd1c9";

$fields = array(
                       
						'reference'=> '54ae58a6dac6c',// system transaction reference number
						'paynowreference'=>'test',// optional information to be displayed to customer about transaction
						'amount'=>'30.00', // amount two decimal place 
						'status'=>'paid',
						'pollurl'=>'https://paynow.pfms.gov.zw:52000/paynow/return.php?ref=54ae58a6dac6c', // poll url
                        
                        
                );
				
	// converting the array into post data including the hashing	
foreach($fields as $key=>$value) { 
	$fields_string .= $key.'='.$value.'&';
 }
rtrim($fields_string,'&');
$fields_string .='hash='.CreateHash($fields,$Merchantkey);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $fields_string
    )
);

//initiating the transaction and retrieving the results
$context  = stream_context_create($opts);

$result = file_get_contents($url, false, $context);

echo $result;
?>