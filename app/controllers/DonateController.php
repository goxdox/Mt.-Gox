<?php

namespace app\controllers;

class DonateController extends \lithium\action\Controller 
{
function error($str)
	{		
		$vars['error'] = $str;
		$vars['title'] = 'Error';
		
		$this->set($vars);
		$this->render('../error');		
	}
	
public function index()
  {
		global $gUserID;
		$this->_render['layout'] = 'clean';
		
		$business=$this->request->data['business'];
		
		$this->request->data['gUserID']=$gUserID;
		$this->request->data['title']="Donate to $business";
	  
		
		$currency_code=$this->request->data['currency_code'];
		if(!isset($this->request->data['return'])) $this->request->data['return']='/users/thanks';
	  	
	  	if($this->request->data['amount']>0)
	  	{
			db_connect();
		  
			$clean_name=strtolower($business);
			$sql="SELECT userid from Users where CleanName='$clean_name'";
			$merchID=getSingleDBValue($sql);
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
		  			DonateController::error("Unknown Currency.");
		  			return;
		  		}
		  		$this->request->data['merchID']=$merchID;

		  		 
				return $this->request->data;
		  	}else 
		  	{
		  		DonateController::error("Sorry Merchant Not Found.");
		  	}
	  	}else DonateController::error("Invalid");
  } 
  
	public function button()
	{
		global $gUserID;
  		$vars['title'] = 'Your Donate Button';
  		$vars['gUserID'] = $gUserID;	
  		
		
		return $vars;
	}
}

?>