<?php

namespace app\controllers;

class ExamplesController extends \lithium\action\Controller 
{
	
	function error($str)
	{		
		$vars['error'] = $str;
		$vars['title'] = 'Error';
		
		$this->set($vars);
		$this->render('../error');		
	}
	
	public function payAPI() 
  {		
  	$vars['title'] = 'Payment API Example';
		
	return $vars;	
  }
  
public function checkout() 
  {		
  	$vars['title'] = 'Checkout Example';
		
		return $vars;
  }
  
public function ipn() 
  {		
  	$vars['title'] = 'Payment Notification Example';
		
		return $vars;
  }
}

?>