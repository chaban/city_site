<?php namespace Backend\Controllers;
use \Phalcon\Tag as Tag;

class ArticleCategoriesController extends ControllerBase
{

  public function initialize()
  {
    $this->view->setTemplateAfter('main');
    Tag::setTitle('Категории статей');
    parent::initialize();
  }

  public function indexAction()
	{
		$numberPage = 1;
		if ($this->request->isPost())
		{
			$query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "Models\ArticleCategories", $_POST);
			$query->order("id ASC, name ASC");
			$this->persistent->searchParams = $query->getParams();
			if (!\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$categories = \Models\ArticleCategories::find($this->persistent->searchParams);
			}
		} else
		{
			$numberPage = $this->request->getQuery("page", "int");
			if (!$numberPage or $numberPage <= 0)
			{
				$numberPage = 1;
			}
			if ($numberPage > 1 and !\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$categories = \Models\ArticleCategories::find($this->persistent->searchParams);
			} else
			{
				$categories = \Models\ArticleCategories::find();
				$this->persistent->searchParams = null;
			}
		}
		if (count($categories) == 0)
		{
			$this->flashSession->notice("Не найдено ни одной категории");

			$this->persistent->searchParams = null;
		}
		$paginator = new \Phalcon\Paginator\Adapter\Model(array(
			"data" => $categories,
			"limit" => 10,
			"page" => $numberPage));
		$page = $paginator->getPaginate();

		$this->view->setVar("page", $page);
		//$this->view->setVar('searchparams', $this->persistent->searchParams);
		//$this->view->setVar('numpage', $numberPage);
	}

  public function showAction($id = null)
  {
    if(!$this->request->isPost() && !$this->request->isAjax())
    {
      $id = $this->filter->sanitize($id, 'int');
      $model = $this->loadModel($id);
      $this->view->setVar('model', $model);
    }
  }

  public function createAction()
	{
		if ($this->request->isPost() && !$this->request->isAjax())
		{
		  $auth = $this->session->get('auth');
      if(!$auth)
      {
        return $this->response->redirect();
      }
			$model = new \Models\ArticleCategories();
			if (!$model->create($_POST))
			{

				foreach ($model->getMessages() as $message)
				{
					$this->flashSession->error((string )$message->getMessage());
					//echo var_dump($message->getType());
					//echo var_dump($message->getMessage());
				}
				return $this->response->redirect('backend/articleCategories/create');
			} else
			{
        $this->flashSession->success("Категория статей создана");
        return $this->response->redirect("backend/articleCategories");
			}
		} 
	}

  public function editAction($id = null)
	{
		if (!$this->request->isPost() && !$this->request->isAjax())
		{

			$id = $this->filter->sanitize($id, array("int"));

			$model = $this->loadModel($id);

			$this->view->setVar("id", $model->id);

			Tag::displayTo("name", $model->name);
		}elseif ($this->request->isPost() && !$this->request->isAjax())
		{
		  
      $id = $this->request->getPost('id', 'int');
			$model = $this->loadModel($id);
  
			if (!$model->update($_POST))
			{

				foreach ($model->getMessages() as $message)
				{
					$this->flashSession->error((string )$message->getMessage());
					//echo var_dump($message->getType());
					//echo var_dump($message->getMessage());
				}
				return $this->response->redirect('backend/articleCategories/edit/'.$model->id);
			} else
			{
        $this->flashSession->success("Статья удачно обновлена");
        return $this->response->redirect("backend/articleCategories");
			}
		}
	}
  
  	public function deleteAction($id = null)
	{

		$id = $this->filter->sanitize($id, array("int"));

		$model = $this->loadModel($id);

		if (!$model->delete())
		{
			foreach ($news->getMessages() as $message)
			{
				$this->flash->error((string )$message);
			}
			return $this->response->redirect("backend/articleCategories");
		} else
		{
			$this->flashSession->success("articleCategories was deleted");
			return $this->response->redirect("backend/articleCategories");
		}
	}
  
  public function loadModel($id)
	{
		$model = \Models\ArticleCategories::findFirst("id = '$id'");
		if (!$model)
		{
			$this->flashSession->error("Категория статей не найдена");
			return $this->response->redirect("backend/articleCategories/index");
		}
		return $model;
	}

}
