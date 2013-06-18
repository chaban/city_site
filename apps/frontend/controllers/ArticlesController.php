<?php
namespace Frontend\Controllers;

use \Phalcon\Tag as Tag;

class ArticlesController extends ControllerBase
{

	public function initialize()
	{
		$this->view->setTemplateAfter('news');
		\Phalcon\Tag::setTitle('Статьи');
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
		$models = \Models\Articles::find("status = '0'");
		if (!$models->count())
		{
		 	return $this->flashSession->notice("Не найдено ни одной новости");
		}
		$paginator = new \Phalcon\Paginator\Adapter\Model(array(
			"data" => $models,
			"limit" => 10,
			"page" => $numberPage));
		$page = $paginator->getPaginate();

		$this->view->setVar("page", $page);
	}

	public function showAction($id = null, $slug = null)
  {
    $id = $this->filter->sanitize($id, "int");
    
    $images = array();
    $model = \Models\Articles::findFirst(array("conditions" => "id = ?1", "bind" => array(1 => "$id")));
    $images = $model->getImages();
    $user = $model->owner;
    Tag::setTitle($model->title);
    $this->view->setVar('model', $model);
    $this->view->setVar('images', $images);
    $this->view->setVar('user', $user);
  }

}
