<?php
namespace app\controllers;
use \lithium\security\Auth;
use \lithium\analysis\Logger;
use app\models\User;
//include("code/lib/functions.inc");

class UsersController extends \lithium\action\Controller 
{
	
	public function register() 
  	{
  			if(!DEBUG) ensureSSL();
	}

  public function login() 
  {
  	if(!DEBUG)
	  ensureSSL();
    //$data=print_r($this->request,true);
    //Logger::alert("login: $data");
	
  }

public function welcome() 
	{
	}
	
public function thanks() 
	{
	}
	
	public function logout() 
	{
	}
	
	public function confirm() 
	{
		$orderID=$this->request->id;
		db_connect();
		$sql="SELECT MerchantID,Amount from btcx.MerchantOrders where OrderID=$orderID and CustomerID=$gUserID and status=0";
		if( $data=mysql_query($sql))
		{
			if($row=mysql_fetch_array($data))
			{
				$amount=$row['Amount'];
				$merchID=$row['MerchantID'];
				
				$merchName=getSingleDBValue("Select Username from btcx.Users where UserID=$merchID");
				
			}else $error="Order Not found.";	
		}else $error='SQL Error';
		
		$title='Confirm Order';
		
  		return compact('title','amount','merchName','orderID','error');
  		
	}
	
public function settings() 
	{
		global $gUserID;
		global $gMerchOn;
		
		$error='';
		db_connect();
		$sql="SELECT Email,TradeNotify,payAPIOn,MerchNotifyURL,merchToken from btcx.Users where UserID=$gUserID";
		if( $data=mysql_query($sql))
		{
			$row=mysql_fetch_array($data);
			if($row)
			{
				$email=$row[0];
				$notify=$row[1];
				$payAPIOn=$row[2];
				$noteurl=$row[3];
				$token=$row[4];
			}else $error="User Not found: $gUserID";	
		}else $error='SQL Error';
		
		$title='Change Settings';
		$merch=$gMerchOn;
		return compact('gUserID','title','email','notify','noteurl','token','payAPIOn','merch','error');
	}
	
	public function trades() 
	{
	}
	
	public function addFunds() 
	{
		$title='Fund your Account';
		
  		return compact('title');
	}
	
	public function withdraw() 
	{
	}
	
public function forgot() 
	{
	}
	
	public function resetPass()
	{
		$resetID=$this->request->params['args'][0];
		//echo($resetID);
		if($resetID)
		{
			//echo(print_r($this->request));
			db_connect();
			$sql="SELECT UserName from btcx.PasswordResets where resetID='$resetID'";
			//echo($sql);
			if( $data=mysql_query($sql))
			{
				if($row=mysql_fetch_array($data))
				{
					$name=$row[0];
				}else $error="Invalid";	
			}else $error='SQL Error';
		}else
		{
			$resetID=0;
			$error="Invalid ID";	
		}
		
		$title='Reset Password';
		
  		return compact('title','resetID','name','error');
	}
	
	public function admin() 
	{
	}
	
	public function sendMoney()
	{
		$title='Send Money';
		
  		return compact('title');	
	}
}

?>