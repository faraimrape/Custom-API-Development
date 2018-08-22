<?php
require ("functions.php");
require ("settings.php");
//file_put_contents('postdata.txt', var_export($_POST, true));
///////////////////////////////////////////////getting ministry from table///////////////////////////////////////////////////

$con=db_cxn();
$results=mysqli_query($con,"SELECT * FROM `transaction` WHERE trans_ref='".$_GET['ref']."' ");
mysqli_close($con);

$row = mysqli_fetch_assoc($results);

//var_dump($row);
$ministry = $row["ministry"];
$applicationName = $row["app_name"];
$id_num=$row["id_num"];
//Getting The merchant key 

$merchant=get_min_credatials($ministry);
$Merchantkey=$merchant["Merchantkey"];

//posted response from paynow 

$fields = array(
                       
						'reference'=> $_POST['reference'],// system transaction reference number
						'paynowreference'=>$_POST['paynowreference'],// optional information to be displayed to customer about transaction
						'amount'=>$_POST['amount'], // amount two decimal place 
						'status'=>$_POST['status'],
						'pollurl'=>$_POST['pollurl'] // poll url
                        
                        
                );
				
// validating the data that has been returned	
$responsehash=CreateHash($fields,$Merchantkey);
if($responsehash==$_POST['hash']){ 

			//insert into the database the transaction
	$con=db_cxn();
	mysqli_query($con,"UPDATE TRANSACTION SET `paynowRef` ='".$fields['paynowreference']."',`paymentStatus`='".$fields['status']."',`paymentTS`=NOW() WHERE trans_ref='".$_GET['ref']."' ");
	mysqli_close($con);
	}
else 
	{
		echo "your data has been corrupted";
	}
	
/////////////////////////////////////////////////////////connecting and posting results to SAP CRM////////////////////////////////////////////

/********************************************************for dev************************************************/
	/*$LOGIN = array (
		"ASHOST"=>"192.168.1.46", // application server host name
		"SYSNR"=>"CR1",// system number
		"CLIENT"=>"300",// client
		"USER"=>"631139173P32",// user
		"PASSWD"=>"paynow2014",// password
		"CODEPAGE"=>"1100"
		);  // codepage
		
		/********************************************************for prd ************************************************/
	
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

	$rfcfunction = "ZGVT_SAVE_RECEIPT";
	
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
	

						saprfc_import ($rfchandle,"REFERENCE",$fields['reference']);
					
						saprfc_import ($rfchandle,"AMOUNT",$fields['amount']);
					
						saprfc_import ($rfchandle,"PAYNOW_REF",$fields['paynowreference']);
					 
						saprfc_import ($rfchandle,"POLLURL",$fields['pollurl']); 	
								
						saprfc_import ($rfchandle,"STATUS",$fields['status']); 
										    				
						saprfc_import ($rfchandle,"HASH",$fields['hash']); 
 

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
	
	saprfc_function_free($rfchandle);
 	saprfc_close($rfc);
	
/////////////////////////////////////////////////////////connecting and posting results to SAP ERP////////////////////////////////////////////
/*$LOGIN = array (
		"ASHOST"=>"192.168.1.47", // application server host name
		"SYSNR"=>"04",// system number
		"CLIENT"=>"050",// client
		"USER"=>"472000665A47",// user
		"PASSWD"=>"abap2012",// password
		"CODEPAGE"=>"1100"
		);  // codepage
		
		/********************************************************for prd ************************************************/
	
		$LOGIN = array (
		"ASHOST"=>"192.168.1.13", // application server host name
		"SYSNR"=>"00",// system number
		"CLIENT"=>"500",// client
		"USER"=>"rfcuser",// user
		"PASSWD"=>"1Welcome!",// password
		"CODEPAGE"=>"1100"
		);  // codepage  // codepage


 	//————- Set the name of the function

	$rfcfunction = "ZGVT_PAY_NOW4";
	
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
	

						saprfc_import ($rfchandle,"P_BUKRS",$ministry);
					
						saprfc_import ($rfchandle,"P_AMOUNT",$fields['amount']);
						
						saprfc_import ($rfchandle,"P_APP",$applicationName);
						
						saprfc_import ($rfchandle,"ID",$id_num);
						

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
	

	saprfc_function_free($rfchandle);
 	saprfc_close($rfc);


///////////////////////////////////////////////////////////////sending sms///////////////////////////////////////////////////////////////////////////
if($row['mobile']!=""){
	$Message="Your payment of $".$_POST['amount']." for application ".$_POST['reference']." was ".$_POST['status'];
	$user=$merchant['SmsUsername'];
	$sender=$merchant['sender'];
	$mobile=$row['mobile'];
	sendSms ($mobile,$Message,$user,$sender);
}
//file_put_contents('postdata.txt', $mobile);//var_export($_POST, true));
?>