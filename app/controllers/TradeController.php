<?php
//include('../noserve/config.inc');
//include('code/lib/functions.inc');
//include('code/lib/session.php');


namespace app\controllers;

class TradeController extends \lithium\action\Controller 
{
  public function index() 
  {		
  	$title='Buy & Sell';
  	//if(isset($_SESSION['UserID'])) $userID=
  	return compact('title');
  }
  
 public function history() 
  {
    		
  }
  
	 public function realTime()
	 {
	 	$this->_render['layout'] = 'clean';
	 	$title='Real Time Mega Chart';
	  	return compact('title');
	 }
	 
	public function dashboard()
	 {
	 	$this->_render['layout'] = 'clean';
	 	$title='Real Time Dashboard';
	  	return compact('title');
	 }
	 
	 public function megaChart()
	 {
	 	$this->_render['layout'] = 'clean';
	 	$title='Mega Chart';
	  	return compact('title');
	 }
}

?>