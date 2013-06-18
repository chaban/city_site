<?php
namespace Backend\Controllers;

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
		if ($this->request->isPost())
		{
			$query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "Models\Posts", $_POST);
			$query->order("id ASC, title ASC");
			$this->persistent->searchParams = $query->getParams();
			if (!\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$posts = \Models\Posts::find($this->persistent->searchParams);
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
				$posts = \Models\Posts::find($this->persistent->searchParams);
			} else
			{
				$posts = \Models\Posts::find();
				$this->persistent->searchParams = null;
			}
		}
		if (count($posts) == 0)
		{
			$this->flashSession->notice("Ничего не найдено");

			$this->persistent->searchParams = null;
		}
		$paginator = new \Phalcon\Paginator\Adapter\Model(array(
			"data" => $posts,
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
	 $this->assets->addCss('css/select2.css');
		$this->assets->addJs('js/select2.min.js');
		if ($this->request->isPost() && !$this->request->isAjax())
		{
		  $auth = $this->session->get('auth');
      if(!$auth)
      {
        return $this->response->redirect();
      }
			$model = new \Models\Posts();
      $model->author_id = $auth['id'];
			if (!$model->create($_POST))
			{

				foreach ($model->getMessages() as $message)
				{
					$this->flashSession->error((string )$message->getMessage());
				}
				return $this->response->redirect('backend/posts/create');
			} else
			{
        $this->flashSession->success("Запись удачно создана");
        return $this->response->redirect("backend/posts");
			}
		} 
    $this->view->setVar('tags',\Models\Tags::getTagsForSelect());
	}

	public function editAction($id = null)
	{
	 $this->assets->addCss('css/select2.css');
		$this->assets->addJs('js/select2.js');
		if (!$this->request->isPost() && !$this->request->isAjax())
		{

			$id = $this->filter->sanitize($id, array("int"));

			$model = $this->loadModel($id);
			if (!$model)
			{
				$this->flashSession->error("Новость не найдена");
        $this->response->redirect("backend/posts/index");
			}
			$this->view->setVar("id", $model->id);
      $this->view->setVar('tags', \Models\Tags::getTagsForSelect());

			Tag::displayTo("id", $model->id);
			Tag::displayTo("title", $model->title);
			Tag::displayTo("body", $model->body);
			Tag::displayTo("status", $model->status);
      Tag::displayTo("tags", $model->tags);
		}elseif ($this->request->isPost() && !$this->request->isAjax())
		{
		  $auth = $this->session->get('auth');
      if(!$auth)
      {
        return $this->response->redirect();
      }
      $id = $this->request->getPost('id', 'int');
			$model = $this->loadModel($id);
      if (!$model)
			{
				$this->flashSession->error("Ничего не найдено");
        $this->response->redirect("backend/posts/index");
			}
      $model->author_id = $auth['id'];
			if (!$model->update($_POST))
			{

				foreach ($model->getMessages() as $message)
				{
					$this->flashSession->error((string )$message->getMessage());
					//echo var_dump($message->getType());
					//echo var_dump($message->getMessage());
				}
				return $this->response->redirect('backend/posts/edit/'.$model->id);
			} else
			{
        $this->flashSession->success("Запись удачно обновлена");
        return $this->response->redirect("backend/posts");
			}
		}
	}

	public function deleteAction($id = null)
	{

		$id = $this->filter->sanitize($id, array("int"));

		$model = $this->loadModel($id);

		if (!$model->delete())
		{
			foreach ($posts->getMessages() as $message)
			{
				$this->flash->error((string )$message);
			}
			return $this->response->redirect("backend/posts");
		} else
		{
			$this->flashSession->success("posts was deleted");
			return $this->response->redirect("backend/posts");
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
