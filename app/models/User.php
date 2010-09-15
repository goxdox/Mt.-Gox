<?php

namespace app\models;
use \lithium\util\Validator;


class User extends \lithium\data\Model 
{
	
	 public $validates = array(
                             'username' => array(
                                         array('notEmpty', 'message' => 'Name cannot be empty'),
                                         array('isUniqueName', 'message' => 'Sorry name already taken.'),
                                        ),
                             'password' => 'Please enter something',
                            );
                           
	//protected $_meta = array('source' => 'phpbb.users');

	public static function __init(array $options = array()) 
	{
		parent::__init( $options );
        $self = static::_instance();
	        
	        
		User::applyFilter('save', function($self, $params, $chain) 
		{
            $post = $params['record'];

            if(!$post->userid) 
            {
                $post->date = time();
            } 

            $params['record'] = $post;

            return $chain->next($self, $params, $chain);
        }); 

        Validator::add('isUniqueName', function ($value, $format, $options) 
        {
            $conditions = array('username' => $value);

            // If editing the post, skip the current psot
            if(isset($options['values']['userid'])) 
            {
                return(true);
            }

            // Lookup for posts with same title
            return !User::find('first', array('conditions' => $conditions));
        }); 
	         
	} 
}

?>