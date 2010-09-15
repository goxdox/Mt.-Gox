<?php
include('../../noserve/config.inc');
include('lib/functions.inc');

include('lib/common.inc');


db_connect();
$result="Data Unavailable";
$sql="SELECT HighBuy,LowSell,LastPrice From Ticker";
$data=mysql_query($sql);
if($data)
{
	$row=mysql_fetch_array($data);
	if($row)
	{	
		$buy=round( $row['HighBuy'],ROUNDING);
		$sell=round( $row['LowSell'],ROUNDING);
		$lastPrice=round( $row['LastPrice'],ROUNDING);
		$result="Mt. Gox Ticker || Last: $lastPrice | Sell: $buy  |  Buy: $sell  |";
	}
}

//Create the image
$img = @imagecreatetruecolor(400, 15)
  or die("Cannot Initialize new GD image stream");

//Make it transparent
imagesavealpha($img, true);
$trans_colour = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $trans_colour);


$textcolor = imagecolorallocate($img, 0, 0, 255);

// Write the string at the top left
imagestring($img, 2, 0, 0, $result, $textcolor);

// Output the image
header('Content-type: image/png');

imagepng($img);
imagedestroy($img);
?>
