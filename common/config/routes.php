<?php $router = new \Phalcon\Mvc\Router();
$router->setDefaultModule("frontend");

$router->removeExtraSlashes(true);
/**
 * Frontend routes
 */
$router->add('', array(
  'module' => 'frontend',
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 'index',
  'action' => 'index'));
 
$router->add('/', array(
  'module' => "frontend",
  'namespace' => 'Frontend\Controllers\\',
  'controller' => "index",
  'action' => "index"))->setName('front-index');

$router->add('/:controller', array(
  'module' => "frontend",
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 1,
  'action' => "index"))->setName('front-controller');

$router->add('/:controller/:action', array(
  'module' => "frontend",
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 1,
  'action' => 2,
  ))->setName('front-action');

$router->add('/:controller/:action/:params', array(
  'module' => "frontend",
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 1,
  'action' => 2,
  'params' => 3))->setName('front-full');

$router->add("/catalog/category/{url:[a-zA-Z0-9\-]+}",
  array(
  'module' => 'frontend',
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 'catalog',
  'action' => 'index'));

$router->add("/catalog/category",
  array(
  'module' => 'frontend',
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 'catalog',
  'action' => 'index'));
$router->add("/product/show/{id:[0-9]}/{url:[a-zA-Z0-9\-]+}",
  array(
  'module' => 'frontend',
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 'product',
  'action' => 'show'));
  
  $router->add('/confirm/{code}/{email}', array(
  'namespace' => 'Frontend\Controllers\\',
	'controller' => 'user_control',
	'action' => 'confirmEmail'
));

$router->add('/reset-password/{code}/{email}', array(
  'namespace' => 'Frontend\Controllers\\',
	'controller' => 'user_control',
	'action' => 'resetPassword'
));
  
/**
 * Ajax requests  section
 */
 
$router->add('/ajax/getcontent', array(
  'module' => 'frontend',
  'namespace' => 'Frontend\Controllers\\',
  'controller' => 'catalog',
  'action' => 'getcontent'));


/**
 * Backend routes
 */
$router->add('/backend', array(
  'module' => "backend",
  'namespace' => 'Backend\Controllers\\',
  'controller' => "category",
  'action' => "index"))->setName('back-index');

$router->add('/backend/:controller', array(
  'module' => "backend",
  'namespace' => 'Backend\Controllers\\',
  'controller' => 1,
  'action' => "index"))->setName('back-controller');

$router->add('/backend/:controller/:action', array(
  'module' => "backend",
  'namespace' => 'Backend\Controllers\\',
  'controller' => 1,
  'action' => 2,
  ))->setName('back-action');

$router->add('/backend/:controller/:action/:params', array(
  'module' => "backend",
  'namespace' => 'Backend\Controllers\\',
  'controller' => 1,
  'action' => 2,
  'params' => 3))->setName('back-full');

return $router;
