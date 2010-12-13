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
  
  public function buy()
  {
  	$title='Buy Bitcoins';
  	return compact('title');
  }
  
 public function sell()
  {
  	$title='Sell Bitcoins';
  	return compact('title');
  }
  
 public function history() 
  {
    		
  }
  
	 public function realTime()
	 {
	 	global $gUserID;
	 	if($gUserID) $vars['seshID']=session_id();
	 	else $vars['seshID']=0;
	 	
	 	$this->_render['layout'] = 'clean';
	 	$vars['title']='Real Time Mega Chart';
	  	return $vars;
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