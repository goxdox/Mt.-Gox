<?php
$protocol = "https";

ob_start();
function db_connect()
{
	global $db_hostname;
	global $db_username;
	global $db_password;
	global $db_name;
	//echo("HOST: $db_hostname, $db_username, $db_password, $db_name");
	mysql_connect($db_hostname, $db_username, $db_password) or die(mysql_error());
	//echo("<br>Passed");
	mysql_select_db($db_name) or die(mysql_error());
}

function ensureSSL()
{
	global $DEBUG;
	if(! $DEBUG)
  	{
	  	if($_SERVER['SERVER_PORT']!=443) 
	  	{
			$sslport=443; //whatever your ssl port is
			$url = "https://". $_SERVER['SERVER_NAME'] . ":" . $sslport . $_SERVER['REQUEST_URI'];
			header("Location: $url");
			die();
		}
  	}
}
function generateRandomString($length, $letters = '1234567890qwertyuiopasdfghjklzxcvbnmABCDEFGHIJKLMNOPQRSTUVWXTZ')
  {
      $s = '';
      $lettersLength = strlen($letters)-1;
     
      for($i = 0 ; $i < $length ; $i++)
      {
      $s .= $letters[rand(0,$lettersLength)];
      }
     
      return $s;
  } 

function dbConnectNamed($name)
{
	global $db_hostname;
	global $db_username;
	global $db_password;
	//echo("HOST: $db_hostname, $db_username, $db_password, $db_name");
	mysql_connect($db_hostname, $db_username, $db_password) or die(mysql_error());
	//echo("<br>Passed");
	mysql_select_db($name) or die(mysql_error());
}

function getSingleDBValue($sql)
{
	$data=mysql_query($sql) or die($sql."<br>".mysql_error());
	
	$row=mysql_fetch_array($data);
	
	return($row[0]);
}

function db_sess_connect()
{
	global $session_db;
	global $db_hostname;
	global $db_username;
	global $db_password;

	// TODO: this is needed to get it to work in lithium's default.html.php 
	//echo("HOST: $db_hostname, $db_username, $db_password, $db_name");
	//if(isset($db_hostname))
		$session_db = mysql_connect($db_hostname, $db_username, $db_password) or die(mysql_error());
	//else  $session_db = mysql_connect('localhost', 'land', '') or die(mysql_error());
	mysql_select_db('sessions', $session_db);
}

function db_sess_disconnect()
{
	global $session_db;

	mysql_close($session_db);
}

function sess_get($unique_id)
{
	//echo '<p>Fetching session data</p>';
	global $session_db;
	global $session_timeout;
	$min_timestamp = time() - $session_timeout;

	$session_get_sql = "select SessionData from sessions.Sessions where UniqueID='$unique_id'";
	//echo "<p>[$session_get_sql]</p>";
	if ($session_get_result = mysql_query($session_get_sql, $session_db))
	{
	
		$session_data_array = mysql_fetch_assoc($session_get_result);
		
		return $session_data_array['SessionData'];
	}
	mysql_error();
	return false;
}

function sess_put($unique_id, $session_data)
{
	//echo '<p>Writing session data</p>';
	global $session_db;
	global $appName;
	$curr_timestamp = time();

	# ESCAPE SLASHES
	$session_data = addslashes($session_data);

	$session_put_sql = "replace into sessions.Sessions values('$unique_id', '$curr_timestamp', '$session_data', '$appName')";
	//echo "<p>$session_put_sql</p>";
	mysql_query($session_put_sql, $session_db) or die(mysql_error());
}

function sess_del($unique_id)
{
	global $session_db;

	$session_delete_sql = "delete from sessions.Sessions where UniqueID='$unique_id'";
	return mysql_query($session_delete_sql, $session_db);
}

function sess_clean($session_lifetime)
{
	global $session_db;
	$min_timestamp = time() - $session_lifetime;

	$session_cleanup_sql = "delete from sessions.Sessions where LastAccess < '$min_timestamp'";
	return mysql_query($session_cleanup_sql, $session_db);
}


function redirectToHttps() 
{
        if($protocol == "https") 
        { //only if site is currently set to https
		if($_SERVER['SERVER_PORT'] != "443") { //if not using https, redirect them there        
			$thispage = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        		header("Location: $thispage");
		}
	}
}

function redirectToHttp() {
	if($_SERVER['SERVER_PORT'] != "80") { //if not using http, redirect them there        
		$thispage = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        	header("Location: $thispage");
	}
}

/**
	* Function for validating values
	* @access private
	*/
	function _sql_validate_value($var)
	{
		if (is_null($var))
		{
			return 'NULL';
		}
		else if (is_string($var))
		{
			return "'" . $var . "'";
		}
		else
		{
			return (is_bool($var)) ? intval($var) : $var;
		}
	}

/**
	* Build sql statement from array for insert/update/select statements
	*
	* Idea for this from Ikonboard
	* Possible query values: INSERT, INSERT_SELECT, MULTI_INSERT, UPDATE, SELECT
	*
	*/
	function sql_build_array($query, $assoc_ary = false)
	{
		if (!is_array($assoc_ary))
		{
			return false;
		}

		$fields = $values = array();

		if ($query == 'INSERT' || $query == 'INSERT_SELECT')
		{
			foreach ($assoc_ary as $key => $var)
			{
				$fields[] = $key;

				if (is_array($var) && is_string($var[0]))
				{
					// This is used for INSERT_SELECT(s)
					$values[] = $var[0];
				}
				else
				{
					$values[] = _sql_validate_value($var);
				}
			}

			$query = ($query == 'INSERT') ? ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')' : ' (' . implode(', ', $fields) . ') SELECT ' . implode(', ', $values) . ' ';
		}
		else if ($query == 'MULTI_INSERT')
		{
			$ary = array();
			foreach ($assoc_ary as $id => $sql_ary)
			{
				// If by accident the sql array is only one-dimensional we build a normal insert statement
				if (!is_array($sql_ary))
				{
					return sql_build_array('INSERT', $assoc_ary);
				}

				$values = array();
				foreach ($sql_ary as $key => $var)
				{
					$values[] = $this->_sql_validate_value($var);
				}
				$ary[] = '(' . implode(', ', $values) . ')';
			}

			$query = ' (' . implode(', ', array_keys($assoc_ary[0])) . ') VALUES ' . implode(', ', $ary);
		}
		else if ($query == 'UPDATE' || $query == 'SELECT')
		{
			$values = array();
			foreach ($assoc_ary as $key => $var)
			{
				$values[] = "$key = " . $this->_sql_validate_value($var);
			}
			$query = implode(($query == 'UPDATE') ? ', ' : ' AND ', $values);
		}

		return $query;
	}
	
function logMsg($msg)
{
	global $gLogDir;
	$fd = fopen("$gLogDir/phplog.txt", "a");
	$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg; 
	fwrite($fd, $str . "\r\n");
	fclose($fd);
}

function httpPost($url, $strRequest)
{
	//open connection
	$ch = curl_init();
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$strRequest);
	
	//execute post
	$result = curl_exec($ch);
	
	//close connection
	curl_close($ch);
	return($result);
}
	
function httpsPost($url, $strRequest)
{
	// Initialisation
	$ch=curl_init();
	// Set parameters
	curl_setopt($ch, CURLOPT_URL, $url);
	// Return a variable instead of posting it directly
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// Active the POST method
	curl_setopt($ch, CURLOPT_POST, 1) ;
	// Request
	curl_setopt($ch, CURLOPT_POSTFIELDS, $strRequest);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	// execute the connexion
	$result = curl_exec($ch);
	// Close it
	curl_close($ch);
	return $result;
} 
?>
