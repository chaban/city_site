<?php
namespace Frontend\Controllers;

use \Phalcon\Tag as Tag;

class PostsController extends ControllerBase
{

	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		\Phalcon\Tag::setTitle('Все блоги');
		parent::initialize();
	}

	public function indexAction()
	{
		$numberPage = 1;
		if (isset($_GET['tags']))
		{
		  $tags = $this->request->getQuery("tags", "string");
			$criteria = new \Phalcon\Mvc\Model\Criteria;
			$criteria->setModelName("\Models\Posts");
			//$criteria->setDI($this->di);
      $criteria->where("tags LIKE :tags:");
      $criteria->andWhere("status = :1:");
      $criteria->bind(array('tags' => "%$tags%", '1'=>'0'));
			$this->persistent->searchParams = $criteria->getParams();
			$models = \Models\Posts::find($criteria);
		} else
		{
			$numberPage = $this->request->getQuery("page", "int");
			if (!$numberPage or $numberPage <= 0)
			{
				$numberPage = 1;
			}
			if ($numberPage > 1 and !\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$models = \Models\Posts::find($this->persistent->searchParams);
			} else
			{
				$models = \Models\Posts::find("status = '0'");
				$this->persistent->searchParams = null;
			}
		}
		if (!$models->count())
		{
			$this->flashSession->notice("Ничего не найдено");

			$this->persistent->searchParams = null;
		}
		$paginator = new \Phalcon\Paginator\Adapter\Model(array(
			"data" => $models,
			"limit" => 10,
			"page" => $numberPage));
		$page = $paginator->getPaginate();

		$this->view->setVar("page", $page);
		//$this->view->setVar('searchparams', $this->persistent->searchParams);
		//$this->view->setVar('numpage', $numberPage);
	}

	public function showAction($id = null)
	{
		if (!$this->request->isPost() && !$this->request->isAjax())
		{
			$id = $this->filter->sanitize($id, 'int');
			$model = $this->loadModel($id);
			$this->view->setVar('model', $model);
			$this->view->setVar('user', $model->author);
		}
	}

	public function gettagsAction()
	{
		$this->view->disable();
		if ($this->request->isAjax())
		{
			if (isset($_GET['q']) && ($keyword = trim($_GET['q'])) !== '')
			{
				$tags = \Models\Tags::suggestTags($keyword);
				if ($tags !== array())
				{
					//echo implode("\n", $tags);
					echo \Helpers\CJSON::encode($tags);
				}
			}
		}
	}

	public function loadModel($id)
	{
		$model = \Models\Posts::findFirst("id = '$id'");
		if ($model === null)
		{
			$this->flashSession->error("Ничего не найдено");
			return $this->response->redirect("backend/posts/index");
		}
		return $model;
	}

}
