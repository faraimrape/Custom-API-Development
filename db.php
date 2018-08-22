<?php
$con=mysqli_connect("localhost","root","TwentyThird01","paynow");
		// Check connection
		if (mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
	
			$results=mysqli_query($con,"SELECT * FROM `transaction` 	WHERE reference='53ba703feb8a0' ");
		mysqli_close($con);

        $row = mysqli_fetch_assoc($results);
	$ministry = $row["ministry"];
	var_dump($row);
	?>