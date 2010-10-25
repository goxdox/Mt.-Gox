<?php

namespace app\controllers;
include("code/lib/common.inc");

class ClaimController extends \lithium\action\Controller 
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
		// https://mtgox.com/claim?token=$token&email=$email
		
		db_connect();
		$email = $this->request->query['email'];
		$token = $this->request->query['token'];	
		
		$sql="SELECT fromID,currency,amount,note from SendMoney where toemail='$email' AND token='$token' AND status=1";
		//echo($sql);
		if(!$data=mysql_query($sql)) logMsg($sql);
		$row=mysql_fetch_array($data,MYSQL_ASSOC);
		if($row)
		{
			$merchID=$row['fromID'];
			$vars['title']="Claim money sent to you";
			$vars['note']=$row['note'];
			$vars['merchName']=getSingleDBValue("SELECT username from Users where userid=$merchID");
			$pAmount=round($row['amount']/BASIS,2);
			if($row['currency']==1) 
				$vars['amountStr']='$'.$pAmount;
			else $vars['amountStr']="$pAmount BTC";
			$vars['email']=$email;
			$vars['token']=$token;
			
  			return $vars;  	
		}else ClaimController::error('No money sent to this Email');
		
	}
}

?>