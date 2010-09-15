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
  
}

?>