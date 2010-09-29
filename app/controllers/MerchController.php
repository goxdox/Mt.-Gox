<?php

namespace app\controllers;

class MerchController extends \lithium\action\Controller 
{
  public function index() 
  {		
  }
  
  
 public function about() 
  {		
  	$title="About Payment Services";
  	return compact('title');
  }
  
public function cb_example() 
  {		
  }
  
   public function widget() 
  {		
  	global $gUserID;
  	return compact('gUserID');
  }
  
	public function checkout()
	{
		// load the merch info 
		global $gUserID;
		$title="Checkout Using Mt. Gox";
	  
		$notify_url=$this->request->notify_url;
		$business=$this->request->business;
		$currency_code=$this->request->currency_code;
		$item_name=$this->request->item_name;
		$custom=$this->request->custom;
		if(isset($this->request->return)) $return=$this->request->return;
		else $return='/users/thanks';
	  	$amount=$this->request->amount;
	  	
	  	if($amount>0)
	  	{
			db_connect();
		  
			$clean_name=strtolower($business);
			$sql="SELECT userid from Users where CleanName='$clean_name'";
			$merchID=getDBSingleValue($sql);
			if($merchID)
		  	{
		  		if($currency_code=="USD")
		  		{
		  			$dollarName='$';
		  			$btcName='';
		  		}else if($currency_code=="BTC")
		  		{
		  			$dollarName='';
		  			$btcName="BTC";
		  		}else 
		  		{
		  			MerchController::error("Unknown Currency.");
		  			return;
		  		}

				return compact('dollarName','btcName','gUserID','title','merchID','amount','notify_url','business','currency_code','item_name','custom','return');
		  	}else 
		  	{
		  		MerchController::error("Sorry Merchant Not Found.");
		  	}
	  	}else MerchController::error("Invalid");
	}
	
	function error($str)
	{		
		$vars['error'] = $str;
		$vars['title'] = 'Error';
		
		$this->set($vars);
		$this->render('../error');		
	}
}

?>