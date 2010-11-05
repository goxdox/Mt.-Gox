<?php

namespace app\controllers;

class SupportController extends \lithium\action\Controller 
{
  public function index() 
  {		
  }
  
  public function advancedTrading()
  {
  		$title='Advanced Trading';
		
  		return compact('title');
  }
  
 public function tradeAPI()
  {
  	$title='Trade API';
		
  		return compact('title');
  }
  
public function send() 
  {		
  }
  
  public function btc()
  {
  	$title='What are Bitcoins';
		
  		return compact('title');
  }
  
}

?>