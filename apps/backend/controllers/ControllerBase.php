<?php namespace Backend\Controllers;
use \Phalcon\Tag as Tag, \Phalcon\Mvc\Dispatcher;
class ControllerBase extends \Phalcon\Mvc\Controller
{
  protected function initialize()
  {
    Tag::prependTitle('Управление проектом | ');
  }
  
  public function beforeExecuteRoute(Dispatcher $dispatcher)
  {
    
    $auth = $this->session->get('auth');
    if($auth['role'] != 'Administrators')
    {
      return $this->response->redirect();
    } 
  }

  protected function forward($uri)
  {
    $uriParts = explode('/', $uri);
    return $this->dispatcher->forward(array('controller' => $uriParts[0], 'action' =>
        $uriParts[1]));
  }
}
