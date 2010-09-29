<?php 
function curl_post_async($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    //pete_assert(($fp!=0), "Couldn't open a socket to ".$url." (".$errstr.")");

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}

function notifyMerch($notify_url,$payer_username,$custom,$txn_id,$currency_code,$amount)
{
	$params=compact('custom','amount','txn_id','payer_username','currency_code');
	curl_post_async($notify_url, $params);
}

function notifyMerch($merchID,$custom,$txn_id)
{
	$merchURL=getSingleDBValue("Select MerchNotifyURL from btcx.Users where UserID=$merchID");
	if($merchURL)
	{
		$params=compact('custom','amount','txn_id');
		curl_post_async($merchURL,$params);
	}
}

?>