<?php namespace Frontend\Controllers;
use \Phalcon\Tag as Tag;
class ControllerBase extends \Phalcon\Mvc\Controller
{
  protected function initialize()
  {
    Tag::prependTitle('Наш чудесный город | ');
  }

  protected function forward($uri)
  {
    $uriParts = explode('/', $uri);
    return $this->dispatcher->forward(array('controller' => $uriParts[0], 'action' =>
        $uriParts[1]));
  }
}
