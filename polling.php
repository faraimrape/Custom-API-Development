<?php

$con=mysqli_connect("localhost","root","","paynow");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
$result = mysqli_query($con,"SELECT * FROM TRANSACTION");


?>

 <html>

    <head>

        <meta charset="utf-8">

        


        <!-- load Zebra_Form's stylesheet file -->
        <link rel="stylesheet" href="public/css/zebra_form.css">


    </head>



    <body>

	
	
	
	
	
	




	
  <table border ="0"  cellspacing="1" cellpadding="8" bgcolor="#96bd62" class="tablecat">

    <tr>
	<th>Reference</th>
    <th align="left">Amount </th>
   <th align="left">Description </th>
	
  
	
  </tr>
  <?php 
  
  if(mysqli_num_rows($result)<1)
  {
  echo "<tr><td colspan='4'>No Records found for your selection</td></tr>";
  }
  else
  {
  
  while($results = mysqli_fetch_array($result))
{ ?>
  
  
  <tr bgcolor="#ffffff">
   
    <td><a href="do.poll.php?ref=<?PHP echo $results ['reference']; ?>" class="listlink"><?PHP echo $results ['reference']; ?></a></td>
        
	<td><?PHP echo $results ['amount']; ?></td>
	<td><?PHP echo $results ['narative']; ?></td>
	
    </tr>
   <?php }}
   mysqli_close($con);
   ?>

</table>






</body>

</html>