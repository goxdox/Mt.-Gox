<?php

namespace app\controllers;
//include("code/lib/functions.inc");
include("code/lib/common.inc");

/*
Margin works like this:
	People can turn on margin trading from settings
	They have to add money to their margin account
	We have a maximum leverage people can use
		We will start this low and slowly increase it as it seems possible
		
	Margin orders are seperate from their normal holdings
	
	

*/

class MarginController extends \lithium\action\Controller 
{
	function getMarginBalance()
	{
		global $gUserID;
		
		if($gUserID)
		{
			db_connect();
			$sql="SELECT marginBalance from Users where userid=$gUserID";
			return(round(getSingleDBValue($sql)/BASIS,2));
		}
		return(0);
	}
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
		
		$vars['gUserID'] = $gUserID;
		$vars['marginBalance']=MarginController::getMarginBalance();
		$vars['title'] = 'Your Margin Account';
		return($vars);
	}
	
	public function fund()
	{
		global $gUserID;
		
		$vars['gUserID'] = $gUserID;
		$vars['marginBalance']=MarginController::getMarginBalance();
		$vars['title'] = 'Add or Remove Funds';
		return($vars);
	}
}

?>