<?php
$url="http://txt.co.zw/Remote/SendMessage";
$fields = array(
                        'Username'=>'ttcsremote4', //username
						'Recipients'=> $_POST['recipients'],// comma separated string, will replace leading 0 with country code in smshop if not specified
						'Body'=>$_POST['body'], // message
						'sending_number'=>$_POST['sending_number'],// (Optional) The number eg “263772123456” or 11 Character word eg “Txt.co.zw” that you would like the recipient to see as the source.  Note these numbers and words must be presetup and linked to your account.  Words may not be available depending on country and network.
						
                );
				
	// converting the array into post data including the hashing	
foreach($fields as $key=>$value) { 
	$fields_string .= $key.'='.$value.'&';
 }

rtrim($fields_string,'&');



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