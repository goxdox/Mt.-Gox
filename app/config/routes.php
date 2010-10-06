<?php
use \lithium\net\http\Router;
use \lithium\core\Environment;
global $gUserID;
/**
 * Uncomment the line below to enable routing for admin actions.
 * @todo Implement me.
 */
//Router::namespace('/admin', array('admin' => true));

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'view', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.html.php)...
 */

if($gUserID)
	Router::connect('/', array('controller' => 'trade') );
else Router::connect('/', array('controller' => 'users', 'action' => 'welcome') );

/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
Router::connect('/pages/{:args}', array('controller' => 'pages', 'action' => 'view'));

Router::connect('/register', array('controller' => 'users', 'action' => 'register'));
Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
//Router::connect('/create', array('controller' => 'scripts', 'action' => 'edit'));

/**
 * Connect the testing routes.
 */
if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => '\lithium\test\Controller'));
	Router::connect('/test', array('controller' => '\lithium\test\Controller'));
}

Router::connect('/merch/examples/payAPI', array('controller' => 'merch', 'action' => 'example_payAPI') );

/**
 * Finally, connect the default routes.
 */
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
Router::connect('/{:controller}/{:action}/{:args}');

?>