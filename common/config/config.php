<?php
return new \Phalcon\Config(array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'password',
		'dbname' => 'balash_phalcon',
		'charset' => 'utf8',
		),
	'application' => array(
		'modelsDir' => __dir__ . '/../common/models/',
		'formsDir' => __dir__ . '/../common/forms/',
		'libraryDir' => __dir__ . '/../common/library/',
		'pluginsDir' => __dir__ . '/../common/plugins/',
		'cacheDir' => __dir__ . '/../../var/cache/',
		'baseUri' => '/',
		'publicUrl' => 'balash.loc',
		'cryptSalt' => '$9diko$.f#11',
		'images_number' => '4'),
	'mail' => array(
		'fromName' => 'Городской сайт',
		'fromEmail' => 'webmaster@mail.com',
		'smtp' => array(
			'server' => '127.0.0.1',
			'port' => 587,
			'security' => 'tls',
			'username' => '',
			'password' => '',
			)),
	'comments' => array(//comments configurations may be used in future
		//you may override default config for all connecting models
		'defaultModelConfig' => array(
			//only registered users can post comments
			'registeredOnly' => false,
			'useCaptcha' => false,
			//allow comment tree
			'allowSubcommenting' => true,
			//display comments after moderation
			'premoderate' => false,
			//action for postig comment
			'postCommentAction' => 'comments/comment/postComment',
			//super user condition(display comment list in admin view and automoderate comments)
			'isSuperuser' => 'false',
			//order direction for comments
			'orderComments' => 'DESC',
			),
		//the models for commenting
		'commentableModels' => array(
			//model with individual settings
			'News' => array(
				'registeredOnly' => false,
				'useCaptcha' => false,
				'allowSubcommenting' => true,
				//config for create link to view model page(page with comments)
				'pageUrl' => array(
					'route' => 'backend/news/show',
					'data' => array('id' => 'id'),
					),
				),
			//model with default settings
			'Articles',
			),
		//config for user models, which is used in application
		'userConfig' => array(
			'class' => 'User',
			'nameProperty' => 'username',
			'emailProperty' => 'email',
			),
		),
	'models' => array('metadata' => array('adapter' => 'Memory'))));
