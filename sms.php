<?php
//application to test POSTing functionality from SAP... temp app
//irrelevant

if((isset($_POST['username']))&&(isset($_POST['sending_number']))&&(isset($_POST['body']))&&(isset($_POST['recipients'])))
	{
	$a= rand(0, 10);
	if($a % 2==0)
		{
		echo 'SUCCESS: 1234';
		}
	else
		{
		echo 'ERROR: Failed to send SMS';
		}
	}

?>