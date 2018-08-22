 <?php
 //var_dump($_POST);
//exit;
 $refkey=$_POST['desc'];
 $amount=$_POST['amount'];
 $applicationName=$_POST['app'];
$id_num=$_POST['id'];
//echo $bukrs;
include ("functions.php");

$ministrycode = $_POST["ministrycode"];
///////////////////////////////////////////////////////////////code to select ministry////////////////////////////////////////
/*
// $applicationName=$_POST['app'];
 //$ministrycode ="";
if($_POST["ministrycode"]=='0000'){
//$con=mysqli_connect("192.168.1.105","root","TwentyThird01","paynow");
$con=mysqli_connect("localhost","root","TwentyThird01","paynow");
//$con=mysqli_connect("localhost","root","","paynow");
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
			$results=mysqli_query($con,"SELECT * FROM applist	WHERE app_name='".$_POST["app"]."' ");
		
		//mysqli_close($con);
	

$row = mysqli_fetch_array($results);
$ministrycode = $row['min_code'];
}
else{
	$ministrycode = $_POST["ministrycode"];
}*/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$merchant=get_min_credatials($ministrycode);

$url="https://www.paynow.co.zw/interface/initiatetransaction";
//$url="http://paynow.webdevworld.com/interface/initiatetransaction"; /// url of initialising the transaction
//exit("waiting for PRD");
$Merchantkey=$merchant["Merchantkey"];

//var_dump($_POST);
//var_dump($merchant);
//exit;
//$Merchantkey="71a03cfe-c9b6-4c63-a5d8-b67f222da3ea"; //live
//$Merchantkey="e0be1737-6ea2-490c-8a22-9fec8428025d";// test

$transRef=uniqid();

$fields = array(
						'id'=>$merchant["IntergrateId"], //integration ID live
						'reference'=> $refkey,// system transaction reference number
						'amount'=>$amount, // amount two decimal place 
						//'amount'=>'0.01', // amount two decimal place 
						'additionalinfo'=>"",//$_POST['desc'],// optional information to be displayed to customer about transaction
						'returnurl'=>'https://paynow.pfms.gov.zw/paynow/return.php?ref='.$transRef, // return url
						'resulturl'=>'https://paynow.pfms.gov.zw/paynow/result.php?ref='.$transRef, // return result
						'authemail'=>$_POST['email'],
						//'authemail'=>'onlinepayments@ttcs.co.zw',
                        'status'=>'Message',
                        
                );

	
	// converting the array into post data including the hashing	
foreach($fields as $key=>$value) { 
	$fields_string .= $key.'='.$value.'&';
 }

rtrim($fields_string,'&');


$fields_string .='hash='.CreateHash($fields,$Merchantkey);

$fields['returnurl']= urlencode($fields['returnurl']);
$fields['resulturl']= urlencode($fields['resulturl']);

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


// parsing results into an array 
$temp=explode("&",$result);
$sets = array();
foreach ($temp as $value) 
{
	$array = explode('=', $value);
	$array[1] = trim($array[1], '"');
	$sets[$array[0]] = rawurldecode($array[1]);
}
	//var_dump($sets);
	if($sets['status']=="Ok")
	{
		// validating the data that has been returned	
		$responsehash=CreateHash($sets,$Merchantkey);

		if($responsehash==$sets['hash']){ 
			//insert into the database the transaction
			$con=mysqli_connect("localhost","root","TwentyThird01","paynow");
			
			// Check connection
			if (mysqli_connect_errno())
			  {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			  }

			mysqli_query($con,"INSERT INTO TRANSACTION (id_num,`ministry`, app_name, trans_ref, `reference`, `amount`, `narative`,`transStatus`,`pollurl`,browserurl,transdate,mobile)
			VALUES ('".$id_num."','".$ministrycode."', '".$applicationName."', '".$transRef."', '".$fields ['reference']."',".$fields ['amount'].", '".$fields ['additionalinfo']."','".$sets['status']."','".$sets['pollurl']."','".$sets['browserurl']."',NOW(),'".$_POST['Mobile']."')");

			mysqli_close($con);
			


			//////////////////////////////////////////// redirect to the payment page/////////////////////////////
?>
		
			<script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
			<link rel="stylesheet" href="https://code.jquery.com/ui/1.8.10/themes/smoothness/jquery-ui.css" type="text/css">
			<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.10/jquery-ui.min.js"></script>

			<script language="javascript" type="text/javascript">
				window.top.location ="<?php echo $sets['browserurl']?>";
			</script>

<?php
		}
		else 
		{
			echo "your data has been corrupted : ".$sets['error'];
		}
	}
	else{

		echo "Error: ".$sets['error'];

	}
			
			

?>