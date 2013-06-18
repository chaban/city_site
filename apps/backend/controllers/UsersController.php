<?php
namespace Backend\Controllers;

use Phalcon\Tag, Phalcon\Mvc\Model\Criteria, Phalcon\Paginator\Adapter\Model as Paginator;

use Forms\ChangePasswordForm, Forms\UsersForm, Models\Users, Models\PasswordChanges;

/**
 * Shop\Controllers\UsersController
 *
 * CRUD to manage users
 */
class UsersController extends ControllerBase
{

	public function initialize()
	{
		$this->view->setTemplateAfter('main');
		Tag::setTitle('Управление пользователями');
	}

	/**
	 * Default action, shows the search form
	 */
	public function indexAction()
	{
		$numberPage = 1;
		if ($this->request->isPost())
		{
			$query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "\Models\Users", $_POST);
			$query->order("id ASC, name ASC");
			$this->persistent->searchParams = $query->getParams();
			if (!\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$models = \Models\Users::find($this->persistent->searchParams);
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
				$models = \Models\Users::find($this->persistent->searchParams);
			} else
			{
				//$models = \Models\Users::query()->order("id ASC, name ASC")->execute();
				$models = \Models\Users::find();
				$this->persistent->searchParams = null;
			}
		}
		if (count($models) == 0)
		{
			$this->flashSession->notice("Не найдено ни одного пользователя");

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
	 * Show for users
	 */
	public function showAction($id = null)
	{
		$id = $this->filter->sanitize($id, array("int"));
		$model = \Models\Users::findFirst(array("conditions" => "id = ?1", "bind" => array(1 => "$id")));
		if (!$model)
		{
			$this->flashSession->error("Такого пользователя не существует");
			return $this->response->redirect("backend/users/index");
		}
		//$images = $model->getImages();
		Tag::setTitle("Данные о пользователе - " . $model->name);
		$this->view->setVar('model', $model);
	}

	/**
	 * Creates a User
	 *
	 */
	public function createAction()
	{
	 Tag::displayTo("role_id", 3);
		if ($this->request->isPost())
		{

			$model = new Users();

			$model->assign(array(
				'name' => $this->request->getPost('name', 'striptags'),
				'role_id' => $this->request->getPost('role_id', 'int'),
				'email' => $this->request->getPost('email', 'email'),
				));

			if (!$model->save())
			{
				$this->flash->error($model->getMessages());
			} else
			{

				$this->flashSession->success("Новый пользователь добавлен");
        Tag::resetInput();
        return $this->response->redirect("backend/users");
			}
		}
	}

	/**
	 * Saves the user from the 'edit' action
	 *
	 */
	public function editAction($id = null)
	{
		if (!$this->request->isPost())
		{
			$id = $this->filter->sanitize($id, array("int"));
			$model = \Models\Users::findFirst(array("conditions" => "id = ?1", "bind" => array(1 => "$id")));
			if (!$model)
			{
				$this->flashSession->error("Такой пользователь не найден");
				return $this->response->redirect("backend/users");
			}
			Tag::displayTo("name", $model->name);
			Tag::displayTo("email", $model->email);
			Tag::displayTo("active", $model->active);
			Tag::displayTo("role_id", $model->role->id);
			Tag::displayTo("banned", $model->banned);
			Tag::displayTo("suspended", $model->suspended);

			$this->view->setVar("model", $model);
		} elseif ($this->request->isPost())
		{
			$id = $this->request->getPost('id', 'int');
			$model = \Models\Users::findFirst("id = '$id'");
			if (!$model)
			{
				$this->flashSession->error("Такой пользователь не найден");
				return $this->response->redirect("backend/users/index");
			}
			$model->assign(array(
				'name' => $this->request->getPost('name', 'striptags'),
				'role_id' => $this->request->getPost('role_id', 'int'),
				'email' => $this->request->getPost('email', 'email'),
				'banned' => $this->request->getPost('banned'),
				'suspended' => $this->request->getPost('suspended'),
				'active' => $this->request->getPost('active')));

			if (!$model->save())
			{
				$this->flash->error($model->getMessages());
			} else
			{

				$this->flashSession->success("Данные о пользователе обновлены");
				return $this->response->redirect("backend/users");
			}
		}
	}

	/**
	 * Deletes a User
	 *
	 * @param int $id
	 */
	public function deleteAction($id)
	{

		$model = Users::findFirstById($id);
		if (!$model)
		{
			$this->flash->error("Пользователь не найден");
			return $this->dispatcher->forward(array('action' => 'index'));
		}

		if (!$model->delete())
		{
			$this->flash->error($model->getMessages());
		} else
		{
			$this->flash->success("Пользователь удален");
		}

		return $this->dispatcher->forward(array('action' => 'index'));
	}

}
