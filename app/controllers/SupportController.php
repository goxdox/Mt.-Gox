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
  
 public function margin()
  {
  	$title='Margin';
		
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
  
public function darkPool()
  {
  	$title='Dark Pool';
		
  		return compact('title');
  }
  
 public function contact()
  {
  	$title='Contact Us';
		
  		return compact('title');
  }
  
  public function noWebSocket()
  {
  	$title='Incompatible Browser';
		
  		return compact('title');
  }
  
  
}

?>