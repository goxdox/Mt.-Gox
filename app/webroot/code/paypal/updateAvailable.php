<?php 

$openDate=time()-(60*60*24*30);
$sql="SELECT UserID,amountHeld FROM Orders where amountHeld>0 and Date<$openDate";
$data=mysql_query($sql) or die(mysql_error());
while($row=mysql_fetch_array($data))
{
	$uid=$row['UserID'];
	$amountHeld=$row['amountHeld'];
	
	$sql="UPDATE Users set fundsHeld=fundsHeld-$amountHeld where userid=$uid";
	mysql_query($sql) or die(mysql_error());	
}

$sql="UPDATE Orders set amountHeld=0 where Date<$openDate";
mysql_query($sql) or die(mysql_error());


?>