<?php 


function notifyMerch($notify_url,$payer_username,$custom,$txn_id,$currency_code,$amount)
{
	$amount=round($amount/BASIS,2);
	$params=compact('custom','amount','txn_id','payer_username','currency_code');
	httpPostAsync($notify_url, $params);
}

/*
function notifyMerch($merchID,$custom,$txn_id)
{
	$merchURL=getSingleDBValue("Select MerchNotifyURL from btcx.Users where UserID=$merchID");
	if($merchURL)
	{
		$params=compact('custom','amount','txn_id');
		curl_post_async($merchURL,$params);
	}
}
*/

?>