<?php namespace Elements;

/**
 * Elements
 *
 * Helps to build UI elements for the application
 */

class Elements extends \Phalcon\Mvc\User\Component
{

  private $_headerMenu = array('pull-left' => array(
      'index' => array('caption' => 'Главная', 'action' => 'index'),
      'news' => array('caption' => 'Новости', 'action' => 'news'),
      'articles' => array('caption' => 'Статьи', 'action' => 'articles'),
      'interviews' => array('caption' => 'Интервью', 'action' => 'interviews'),
      //'adverts' => array('caption' => 'Объявления', 'action' => 'adverts'),
      'posts' => array('caption' => 'Блоги', 'action' => 'posts'),
      //'contact' => array('caption' => 'Контакт', 'action' => 'contact'),
      ), 'pull-right' => array('session' => array('caption' => 'Войти/Добавить объявление', 'action' => 'session/login'), ));

  private $_tabs = array('Мои объявления' => array(
      'controller' => 'posts',
      'action' => 'index',
      'any' => false), 'Профиль' => array(
      'controller' => 'profile',
      'action' => 'index',
      'any' => true));

  /**
   * Builds header menu with left and right items
   *
   * @return string
   */
  public function getMenu()
  {

    $auth = $this->session->get('auth');
    if ($auth)
    {
      unset($this->_headerMenu['pull-right']);
    }

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

        echo \Phalcon\Tag::linkTo($option['action'], $option['caption']);
        echo '</li>';
      }
      echo '</ul>';
    }
    if (isset($auth['role']) && $auth['role'] == 'Administrators')
      {
        echo '<ul class="nav pull-right"><li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
Профиль
<b class="caret"></b>
</a>
<ul class="dropdown-menu">
<li><a href="/backend/news/index">Панель администратора</a></li>
<li><a href="/backend/posts/index">Мой блог</a></li>
<li><a href="/session/logout">Выйти</a></li>
</ul>
</li></ul>';
      }elseif(isset($auth['role']) && $auth['role'] == 'Editors')
      {
        echo '<ul class="nav pull-right"><li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
Профиль
<b class="caret"></b>
</a>
<ul class="dropdown-menu">
<li><a href="/backend/posts/index">Мои публикации</a></li>
<li><a href="/session/logout">Выйти</a></li>
</ul>
</li></ul>';
      }
    echo '</div>';
  }

  public function getTabs()
  {
    $controllerName = $this->view->getControllerName();
    $actionName = $this->view->getActionName();
    echo '<ul class="nav nav-tabs">';
    foreach ($this->_tabs as $caption => $option)
    {
      if ($option['controller'] == $controllerName && ($option['action'] == $actionName || $option['any']))
      {
        echo '<li class="active">';
      }
      else
      {
        echo '<li>';
      }
      echo \Phalcon\Tag::linkTo($option['controller'] . '/' . $option['action'], $caption), '<li>';
    }
    echo '</ul>';
  }
}
