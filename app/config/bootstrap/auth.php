<?php

use \lithium\storage\Session;
use \lithium\security\Auth;
use \lithium\action\Dispatcher;
use \lithium\net\http\Router;
use \lithium\action\Response;
use \lithium\analysis\Logger;

Session::config(array(
  'default' => array('adapter' => 'Php')
));

Auth::config(array(
	'user' => array(
		'adapter' => 'Form',
		'model'   => 'User',
		'filters' => array('password' => 'md5')
	)
));


Dispatcher::applyFilter('_call', function($self, $params, $chain) 
{
	
	if($params['request']->type() =='html')
	{
    	$user=Session::read('user');
    	$params['callable']->set(array('user' => $user));
    	// $data=$params['request']->type(); //print_r($params,true);
    	//Logger::alert("filter: $data");
	}
    return $chain->next($self, $params, $chain);
}); 

Dispatcher::applyFilter('run', function($self, $params, $chain) 
{
	/*
    $url=$params['request']->url;
    //$data=Router::match($params['request']->params, $params['request']);
    //Logger::alert("filter: $url");
    $blacklist = array(
	    '/',
	    'users/login',
	    'users/register',
    	'groups'
    );
    $matches = in_array($url, $whitelist);
    if(!$matches)
    {
      $user=Session::read('user');
      if(!$user['user_id']) 
      {
 	      return new Response(array('location' => '/users/login'));
      }
    }*/
    return $chain->next($self, $params, $chain);
});

?>