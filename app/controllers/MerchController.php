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
		$this->request->data['title']="Checkout Using Mt. Gox";
	  
		$business=$this->request->data['business'];
		$currency_code=$this->request->data['currency_code'];
		if(!isset($this->request->data['return'])) $this->request->data['return']='/users/thanks';
	  	
	  	if($this->request->data['amount']>0)
	  	{
			db_connect();
		  
			$clean_name=strtolower($business);
			$sql="SELECT userid from Users where CleanName='$clean_name'";
			$merchID=getDBSingleValue($sql);
			if($merchID)
		  	{
		  		if($currency_code=="USD")
		  		{
		  			$this->request->data['dollarName']='$';
		  			$this->request->data['btcName']='';
		  		}else if($currency_code=="BTC")
		  		{
		  			$this->request->data['dollarName']='';
		  			$this->request->data['btcName']="BTC";
		  		}else 
		  		{
		  			MerchController::error("Unknown Currency.");
		  			return;
		  		}
		  		$this->request->data['merchID']=merchID;

				return $this->request->data;
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