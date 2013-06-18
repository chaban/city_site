<?php
namespace Backend;

use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

	public function registerAutoloaders()
	{

		$loader = new \Phalcon\Loader();
		$loader->registerNamespaces(array(
			'Backend\Controllers' => __dir__ . '/controllers/',
			'Models' => __dir__ . '/../../common/models/',
      'Forms' => __dir__ . '/../../common/forms/',
			'Mail' => __dir__ . '/../../common/library/Mail/',
			'Auth' => __dir__ . '/../../common/library/Auth/',
			'Acl' => __dir__ . '/../../common/library/Acl/',
			'Models\Behaviors' => __dir__ . '/../../common/library/Behaviors/',
			'Helpers' => __dir__ . '/../../common/plugins/Helpers/',
			'Elements' => __dir__ . '/../../common/plugins/Elements',
      'Elrte' => __dir__ . '/../../common/plugins/elrte',
			'Vendors' => __dir__ . '/../../common/vendors/',
			'Vendors\Image' => __dir__ . '/../../common/vendors/image/',
			'Vendors\Image\Drivers' => __dir__ . '/../../common/vendors/image/drivers/',
			'Iwi' => __dir__ . '/../../common/library/ImageIwi/',
			));

		$loader->register();
	}

	public function registerServices($di)
	{

		/**
		 * Read configuration
		 */

		$config = $di->get('config');

		/**
		 * We register the events manager
		 */
		/*
		$di->set('dispatcher', function ()use($di)
		{
		$eventsManager = $di->getShared('eventsManager'); 
		$dispatcher = new Phalcon\Mvc\Dispatcher(); 
		$dispatcher->setEventsManager($eventsManager); 
		return $dispatcher;
		}
		); */

		/**
		 * Setting up the view component
		 */

		$di->set('view', function ()
		{
			$view = new \Phalcon\Mvc\View(); $view->setViewsDir(__dir__ . '/views/'); /* $view->setTemplateBefore('main');*/
				return $view; }
		);

		/**
		 * Database connection is created based in the parameters defined in the configuration file
		 */

		$di->set('db', function ()use($config)
		{
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
				"host" => $config->database->host,
				"username" => $config->database->username,
				"password" => $config->database->password,
				"dbname" => $config->database->dbname)); }
		);

		/**
		 * Register the flash service with custom CSS classes
		 */
		$di->set('flash', function ()
		{
			return new \Phalcon\Flash\Direct(array(
				'error' => 'alert alert-error',
				'success' => 'alert alert-success',
				'notice' => 'alert alert-info',
				)); }
		);

		/**
		 * Register the session flash service with the Twitter Bootstrap classes
		 */
		$di->set('flashSession', function ()
		{
			return new \Phalcon\Flash\Session(array(
				'error' => 'alert alert-error',
				'success' => 'alert alert-success',
				'notice' => 'alert alert-info',
				)); }
		);

		/**
		 * user menu elements
		 */
		$di->set('elements', function ()
		{
			return new \Elements\BackElements(); }
		);

		/**
		 * If the configuration specify the use of metadata adapter use it or use memory otherwise
		 */
		$di->set('modelsMetadata', function ()use($config)
		{
			if (isset($config->models->metadata))
			{
				$metadataAdapter = 'Phalcon\Mvc\Model\Metadata\\' . $config->models->metadata->adapter; return new $metadataAdapter
					(); }
		else
		{
			return new \Phalcon\Mvc\Model\Metadata\Memory(); }
	}
	);

	/**
	 * Custom authentication component
	 */
	$di->set('auth', function ()
	{
		return new \Auth\Auth(); }
	);

	/**
	 * Mail service uses AmazonSES
	 */
	$di->set('mail', function ()
	{
		return new \Mail\Mail(); }
	);

	/**
	 * Access Control List
	 */
	$di->set('acl', function ()
	{
		return new \Acl\Acl(); }
	);
}
}
