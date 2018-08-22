<?php
require ("functions.php");
file_put_contents('postdata.txt', var_export($_POST, true));
//$sets = $_POST;
$fields = array(
                       
						'reference'=> $_POST ['reference'],// system transaction reference number
						'paynowreference'=>$_POST['paynowreference'],// optional information to be displayed to customer about transaction
						'amount'=>$_POST['amount'], // amount two decimal place 
						'status'=>$_POST['status'],
						'pollurl'=>$_POST['pollurl'] // poll url
                        
                        
                );
				

$Merchantkey="e0be1737-6ea2-490c-8a22-9fec8428025d";

		// validating the data that has been returned	
		$responsehash=CreateHash($fields,$Merchantkey);

		if($responsehash==$_POST['hash']){ 
		
		
		//insert into the database the transaction
		$con=mysqli_connect("localhost","root","TwentyThird01","paynow");
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
			mysqli_query($con,"UPDATE TRANSACTION SET `paynowRef` ='".$fields['paynowreference']."',`paymentStatus`='".$fields['status']."'
			WHERE reference='".$fields['reference']."' ");
		
		mysqli_close($con);
	


		
		}
		else 
		{
			echo "your data has been corrupted";
			
		}

	
echo"This is the result page <br>";
?>