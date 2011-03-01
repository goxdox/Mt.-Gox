<?php

namespace app\controllers;


class OauthController extends \lithium\action\Controller 
{
	
	function error($str)
	{		
		$vars['error'] = $str;
		$vars['title'] = 'Error';
		
		$this->set($vars);
		$this->render('../error');		
	}
	
	
	public function register()
	{
		global $gUserID;
		
		$this->_render['layout'] = 'clean';
		
		$vars['title'] = 'Allow Access';
		return($vars);
	}
}

?>