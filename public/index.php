<?php
error_reporting(E_ALL);

try
{
	$config = include __dir__ . "/../common/config/config.php";
	/**
	 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
	 */
	$di = new \Phalcon\DI\FactoryDefault();
	$di->set('config', $config);

	$di->set('cookies', function ()
	{
		$cookies = new Phalcon\Http\Response\Cookies(); return $cookies; }
	);

	/**
	 * Registering a router
	 */

	$di->set('router', require __dir__ . '/../common/config/routes.php');
	/**
	 * The URL component is used to generate all kind of urls in the application
	 */
	$di->set('url', function ()
	{
		$url = new \Phalcon\Mvc\Url(); $url->setBaseUri('/'); $url->setBasePath(__dir__ ); return $url; }
	);

	/**
	 * We register the events manager
	 */
	$di->set('dispatcher', function ()use($di)
	{

		$eventsManager = $di->getShared('eventsManager'); /**
			 * if not found page
			 */ /*
			$eventsManager->attach("dispatch", function ($event, $dispatcher, $exception)
			{

			if ($event->getType() == 'beforeException')
			{
			switch ($exception->getCode())
			{
			case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND : case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND : $dispatcher->
			forward(array(
			'module' => 'frontend',
			'namespace' => 'Frontend\Controllers\\',
			'controller' => 'index',
			'action' => 'route404')); return false; }
			}
			} 
			);*/$dispatcher = new Phalcon\Mvc\Dispatcher(); $dispatcher->setEventsManager($eventsManager); return $dispatcher; }
	);

	/**
	 * Start the session the first time some component request the session service
	 */
	$di->set('session', function ()
	{
		$session = new \Phalcon\Session\Adapter\Files(); $session->start(); return $session; }
	);

	//Set the views cache service
	/*
	$di->set('viewCache', function () {
	//Cache data for one day by default
	$frontCache = new Phalcon\Cache\Frontend\Output(array("lifetime" => 86400)); //File backend settings
	$cache = new Phalcon\Cache\Backend\File($frontCache, array("cacheDir" => __dir__ . "/../var/cache/",));
	return $cache;
	}
	);*/
	/**
	 * Main logger file
	 */
	$di->set('logger', function ()
	{
		return new \Phalcon\Logger\Adapter\File(__dir__ . '/../var/logs/' . date('Y-m-d') . '.log'); }
	, true);

	/**
	 * Error handler
	 */
	set_error_handler(function ($errno, $errstr, $errfile, $errline)use ($di)
	{
		if (!(error_reporting() & $errno))
		{
			return; }
		$di->getFlash()->error($errstr); $di->getLogger()->log($errstr . ' ' . $errfile . ':' . $errline, Phalcon\Logger::
			ERROR); return true; }
	);

	/**
	 * Encryption service
	 */
	$di->set('crypt', function ()use($config)
	{
		$crypt = new Crypt(); $crypt->setKey('1234'); return $crypt; }
	);

	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	/**
	 * Register application modules
	 */
	//$application->registerModules(require __dir__ . '/../common/config/modules.php');
	// Register the installed modules
	$application->registerModules(array('frontend' => array(
			'className' => 'Frontend\Module',
			'path' => '../apps/frontend/Module.php',
			), 'backend' => array(
			'className' => 'Backend\Module',
			'path' => '../apps/backend/Module.php',
			)));
	echo $application->handle()->getContent();
}
catch (Phalcon\Exception $e)
{
	//echo $e->getMessage();
	echo get_class($e), ": ", var_dump($e->getMessage()), "\n";
	echo " File=", var_dump($e->getFile()), "\n";
	echo " Line=", var_dump($e->getLine()), "\n";
	echo $e->getMessage(), '<br>';
	echo nl2br(htmlentities($e->getTraceAsString()));
}
catch (PDOException $e)
{
	echo var_dump($e->getMessage());
}
