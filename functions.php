<?php


// function to create the hash
 function CreateHash ($value,$MerchantKey){

	$string ="";
	foreach ($value as $key => $value){
		if(strtoupper($key) !="HASH" ){
			$string .=$value;
		}
	}
	$string.=$MerchantKey;


	
	$hash =hash("sha512",$string);
	return strtoupper($hash);
}

///////////////////////////////////////////////////////////////////////////////////////////function to check status of a transaction

 function StatusPoll ($refno,$MerchantKey){
 $con=db_cxn();
/*$con=mysqli_connect("localhost","root","TwentyThird01","paynow");
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }*/
			$results=mysqli_query($con,"SELECT * FROM TRANSACTION	WHERE trans_ref='".$refno."' ");
		
		//mysqli_close($con);
	

$row = mysqli_fetch_array($results);

//var_dump($row);
$url= $row['pollurl'];
$result = file_get_contents($url, false);
//echo $result;

// parsing results into an array 
	$temp=explode("&",$result);
	$sets = array();
	foreach ($temp as $value) {
		$array = explode('=', $value);
		$array[1] = trim($array[1], '"');
		$sets[$array[0]] = rawurldecode($array[1]);
	}
return $sets;
	
 
 }


////////////////////////////////////////////////////////////function to send sms//////////////////////////////////////////

 function sendSms ($mobileno,$Message,$user,$id){

$url="https://www.txt.co.zw/Remote/SendMessage";
$fields = array(
                        //'Username'=>'min.lands.remote', //username
'Username'=>$user, //username
						'Recipients'=> $mobileno,// comma separated string, will replace leading 0 with country code in smshop if not specified
						'Body'=>$Message, // message
						'sending_number'=>$id,// (Optional) The number eg “263772123456” or 11 Character word eg “Txt.co.zw” that you would like the recipient to see as the source.  Note these numbers and words must be presetup and linked to your account.  Words may not be available depending on country and network.
						
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

}

/////////////////////////////////////////////////////////get marchant key, integration id and sms username//////////////////////////////
function get_min_credatials($mincode)
{
require ("settings.php");
//var_dump($LOGIN);
/***************************************************8 for dev ************************************************************************/
/*$LOGIN = array (
		"ASHOST"=>"192.168.1.46", // application server host name
		"SYSNR"=>"CR1",// system number
		"CLIENT"=>"300",// client
		"USER"=>"631139173P32",// user
		"PASSWD"=>"paynow2014",// password
		"CODEPAGE"=>"1100"
		);  // codepage
		
		/**************************************************** for production********************************************************/
		
	/*	$LOGIN = array (
		"ASHOST"=>"192.168.1.32", // application server host name
		"SYSNR"=>"05",// system number
		"CLIENT"=>"500",// client
		"USER"=>"rfcuser",// user
		"PASSWD"=>"1Welcome!",// password
		"CODEPAGE"=>"1100"
		);  // codepage
*/
 	//————- Set the name of the function

	$rfcfunction = "ZGVT_GET_MERC_KEY";
	
	 //———— Make a connection to the SAP server
 	$rfc = saprfc_open($LOGIN);

 	if(!$rfc) {  // We have failed to connect to the SAP server
    	echo "Failed to connect to the SAP server".saprfc_error();
    	exit(1);
 	}

 	//———- Locate the function and discover the interface
 	$rfchandle = saprfc_function_discover($rfc, $rfcfunction);

 	if(!$rfchandle){ // We have failed to discover the function
		echo "We have failed to discover the function".saprfc_error($rfc);
		exit(1);
 	}
	

						//saprfc_import ($rfchandle,"MINISTRYCODE","0000");
					
						saprfc_import ($rfchandle,"MINISTRYCODE",$mincode);

 	//——— Call the function and check for errors
 	$rfcresults = saprfc_call_and_receive($rfchandle);

 	if ($rfcresults != SAPRC_OK){
		if ($rfcresults == SAP_EXCEPTION){
			$error = ("Exception raised:".saprfc_exception($rfchandle));
		}else{
			$error = ("Call error:".saprfc_error($rfchandle));
		}
		echo $error;
		exit();
 	}
	
	$Merchantkey = saprfc_export ($rfchandle,"MERC_KEY");
	$IntergrateId = saprfc_export ($rfchandle,"INTERGRATE_ID");
	$SmsUsername = saprfc_export ($rfchandle,"SMS_USERNAME");

	saprfc_function_free($rfchandle);
 	saprfc_close($rfc);
	
	$details = array("Merchantkey"=>$Merchantkey,"IntergrateId"=>$IntergrateId,"SmsUsername"=>$SmsUsername);
	
	
	return $details;
}
/////////////////////////////////////////////////database connection////////////////////////////////////////////////////
function db_cxn()
{
$con=mysqli_connect("localhost","root","TwentyThird01","paynow");
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
		  return $con;
}
?>