<?php

namespace app\controllers;
include("code/lib/common.inc");

class LrController extends \lithium\action\Controller 
{
	
	function error($str)
	{		
		$vars['error'] = $str;
		$vars['title'] = 'Error';
		
		$this->set($vars);
		$this->render('../error');		
	}
	
	public function success() 
	{	
			
	}
	
	public function fail() 
	{	
			
	}
}

?>