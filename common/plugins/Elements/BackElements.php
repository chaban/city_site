<?php namespace Elements;

/**
 * Elements
 *
 * Helps to build UI elements for the application
 */

class BackElements extends \Phalcon\Mvc\User\Component
{

  private $_headerMenu = array('pull-left' => array(
      'news' => array('caption' => 'Новости', 'action' => 'news'),
      'articles' => array('caption' => 'Статьи', 'action' => 'articles'),
      'interviews' => array('caption' => 'Интервью', 'action' => 'interviews'),
      //'adverts' => array('caption' => 'Объявления', 'action' => 'adverts'),
      'posts' => array('caption' => 'Блоги', 'action' => 'posts'),
      'users' => array('caption' => 'Пользователи', 'action' => 'users'),
      ), 'pull-right' => array('session' => array('caption' => 'Выйти', 'action' => '../session/logout'), ));

  private $_tabsArticleCategory = array(
    'Категории статей' => array(
      'controller' => 'articleCategories',
      'action' => 'index',
      'any' => false));
      
      private $_tabsUsers = array(
    'Пользователи' => array(
      'controller' => 'users',
      'action' => 'index',
      'any' => false),
    'Роли' => array(
      'controller' => 'roles',
      'action' => 'index',
      'any' => false),
    'Права доступа ролей' => array(
      'controller' => 'permissions',
      'action' => 'index',
      'any' => true));

  /**
   * Builds header menu with left and right items
   *
   * @return string
   */
  public function getMenu()
  {

    echo '<div class="nav-collapse">';
    $controllerName = $this->view->getControllerName();
    $actionName = $this->request->getServer('REQUEST_URI');
    foreach ($this->_headerMenu as $position => $menu)
    {
      echo '<ul class="nav ', $position, '">';
      foreach ($menu as $controller => $option)
      {
        $path_uri = explode('/', $actionName);
        $action_pos = explode('/', $option['action']);
        if (isset($path_uri[2]) && isset($action_pos[1]))
        {
          if ($action_pos[1] != $path_uri[2])
          {
            echo '<li>';
          }
          else
          {
            echo '<li class="active">';
          }
        }
        else
        {
          if ($controllerName == $controller)
          {
            echo '<li class="active">';
          }
          else
          {
            echo '<li>';
          }
        }

        echo \Phalcon\Tag::linkTo('backend/'.$option['action'], $option['caption']);
        echo '</li>';
      }
      echo '</ul>';
    }
    echo '</div>';
  }

  public function getTabs($sufix = '')
  {
    $controllerName = $this->view->getControllerName();
    $actionName = $this->view->getActionName();
    $tabs = '_tabs'.$sufix;
    echo '<ul class="nav nav-tabs">';
    foreach ($this->$tabs as $caption => $option)
    {
      if ($option['controller'] == $controllerName && ($option['action'] == $actionName || $option['any']))
      {
        echo '<li class="active">';
      }
      else
      {
        echo '<li>';
      }
      echo \Phalcon\Tag::linkTo('backend/'.$option['controller'] . '/' . $option['action'], $caption), '<li>';
    }
    echo '</ul>';
  }
}
