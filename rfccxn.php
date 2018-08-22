<?php

	$LOGIN = array (
		"ASHOST"=>"192.168.1.13", // application server host name
		"SYSNR"=>"00",// system number
		"CLIENT"=>"500",// client
		"USER"=>"rfcuser",// user
		"PASSWD"=>"1Welcome!",// password
		"CODEPAGE"=>"1100"
		);  // codepage  // codepage

		
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
/*
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
	

						saprfc_import ($rfchandle,"MINISTRYCODE","U029");
					
						//saprfc_import ($rfchandle,"MINISTRYCODE",$mincode);

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
	var_dump($details);
	*/
	
	
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


?>