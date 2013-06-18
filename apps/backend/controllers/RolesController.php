<?php
namespace Backend\Controllers;

use Phalcon\Tag, Phalcon\Mvc\Model\Criteria, Phalcon\Paginator\Adapter\Model as Paginator;

use Forms\RolesForm, Models\Roles;

/**
 * Controllers\RolesController
 *
 * CRUD to manage roles
 */
class RolesController extends ControllerBase
{

	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		$this->view->setTemplateBefore('users');
		Tag::setTitle('Управление ролями пользователей');
	}

	/**
	 * Default action, shows the search form
	 */
	public function indexAction()
	{
		$numberPage = 1;
		if ($this->request->isPost())
		{
			$query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "\Models\Roles", $_POST);
			$query->order("id ASC, name ASC");
			$this->persistent->searchParams = $query->getParams();
			if (!\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$models = \Models\Roles::find($this->persistent->searchParams);
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
				$models = \Models\Roles::find($this->persistent->searchParams);
			} else
			{
				//$models = \Models\Roles::query()->order("id ASC, name ASC")->execute();
				$models = \Models\Roles::find();
				$this->persistent->searchParams = null;
			}
		}
		if (count($models) == 0)
		{
			$this->flashSession->notice("Не найдено");

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

	/**
	 * Creates a new Profile
	 *
	 */
	public function createAction()
	{
	 Tag::displayTo("active", "Y");
		if ($this->request->isPost())
		{

			$model = new Roles();

			$model->assign(array(
				'name' => $this->request->getPost('name', 'striptags'),
				'active' => $this->request->getPost('active')
				));

			if (!$model->save())
			{
				$this->flash->error($model->getMessages());
			} else
			{

				$this->flashSession->success("Новая роль добавлена");
        Tag::resetInput();
        return $this->response->redirect("backend/roles");
			}
		}
	}

	/**
	 * Edits an existing Profile
	 *
	 * @param int $id
	 */
	public function editAction($id)
	{
		if (!$this->request->isPost())
		{
			$id = $this->filter->sanitize($id, array("int"));
			$model = \Models\Roles::findFirst(array("conditions" => "id = ?1", "bind" => array(1 => "$id")));
			if (!$model)
			{
				$this->flashSession->error("Такая роль не найдена");
				return $this->response->redirect("backend/roles");
			}
			Tag::displayTo("name", $model->name);
			Tag::displayTo("active", $model->active);
			$this->view->setVar("model", $model);
		} elseif ($this->request->isPost())
		{
			$id = $this->request->getPost('id', 'int');
			$model = \Models\Roles::findFirst("id = '$id'");
			if (!$model)
			{
				$this->flashSession->error("Такая роль не найдена");
				return $this->response->redirect("backend/roles/index");
			}
			$model->assign(array(
				'name' => $this->request->getPost('name', 'striptags'),
				'active' => $this->request->getPost('active')));

			if (!$model->save())
			{
				$this->flash->error($model->getMessages());
			} else
			{

				$this->flashSession->success("Данные о роли обновлены");
				return $this->response->redirect("backend/roles");
			}
		}
	}

	/**
	 * Deletes a Profile
	 *
	 * @param int $id
	 */
	public function deleteAction($id)
	{

		$profile = Roles::findFirstById($id);
		if (!$profile)
		{
			$this->flash->error("Роль не найдена");
      return $this->dispatcher->forward(array('action' => 'index'));
		}

		if (!$profile->delete())
		{
			$this->flash->error($profile->getMessages());
		} else
		{
			$this->flash->success("Роль удалена");
		}

		return $this->dispatcher->forward(array('action' => 'index'));
	}

}
