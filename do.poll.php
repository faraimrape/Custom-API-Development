<?php

$con=mysqli_connect("localhost","root","","paynow");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
$result = mysqli_query($con,"SELECT pollurl FROM TRANSACTION WHERE reference='".$_GET['ref']."'");
$url=mysqli_fetch_array($result);
	 mysqli_close($con);
	 
	 
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => ''
    )
);

//initiating the transaction and retrieving the results
$context  = stream_context_create($opts);

$result = file_get_contents($url ['pollurl']);

echo $result;
 ?>
 
 <html>

    <head>

        <meta charset="utf-8">

        <title>poll results</title>



    </head>



    <body>




</body>

</html>