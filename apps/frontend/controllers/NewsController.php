<?php
namespace Frontend\Controllers;

use \Phalcon\Tag as Tag;

class NewsController extends ControllerBase
{

	public function initialize()
	{
		$this->view->cleanTemplateBefore();
		\Phalcon\Tag::setTitle('Новости');
		parent::initialize();
	}

	public function indexAction()
	{
		$numberPage = 1;
		$numberPage = $this->request->getQuery("page", "int");
		if (!$numberPage or $numberPage <= 0)
		{
			$numberPage = 1;
		}
		$news = \Models\News::find("status = '0'");
		if (count($news) == 0)
		{
		 	return $this->flashSession->notice("Не найдено ни одной новости");
		}
		$paginator = new \Phalcon\Paginator\Adapter\Model(array(
			"data" => $news,
			"limit" => 10,
			"page" => $numberPage));
		$page = $paginator->getPaginate();

		$this->view->setVar("page", $page);
	}

	public function showAction($id = null, $slug = null)
  {
    $id = $this->filter->sanitize($id, "int");
    
    $images = array();
    $model = \Models\News::findFirst(array("conditions" => "id = ?1", "bind" => array(1 => "$id")));
    $images = $model->getImages();
    $user = $model->owner;
    Tag::setTitle($model->title);
    $this->view->setVar('model', $model);
    $this->view->setVar('images', $images);
    $this->view->setVar('user', $user);
  }

}
