<?php namespace Backend\Controllers;
use \Phalcon\Tag as Tag;

class InterviewsController extends ControllerBase
{

  public function initialize()
  {
    $this->view->setTemplateAfter('main');
    Tag::setTitle('Статьи');
    parent::initialize();
  }

  public function indexAction()
	{
		$numberPage = 1;
		if ($this->request->isPost())
		{
			$query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "Models\Interviews", $_POST);
			$query->order("id ASC, title ASC");
			$this->persistent->searchParams = $query->getParams();
			if (!\Helpers\Arr::is_array_empty($this->persistent->searchParams))
			{
				$interviews = \Models\Interviews::find($this->persistent->searchParams);
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
				$interviews = \Models\Interviews::find($this->persistent->searchParams);
			} else
			{
				$interviews = \Models\Interviews::find();
				$this->persistent->searchParams = null;
			}
		}
		if (count($interviews) == 0)
		{
			$this->flashSession->notice("Не найдено ни однго интервью");

			$this->persistent->searchParams = null;
		}
		$paginator = new \Phalcon\Paginator\Adapter\Model(array(
			"data" => $interviews,
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
		$this->assets->addJs('js/jquery.MultiFile.pack.js');
		if ($this->request->isPost() && !$this->request->isAjax())
		{
		  $auth = $this->session->get('auth');
      if(!$auth)
      {
        return $this->response->redirect();
      }
			$model = new \Models\Interviews();
      $model->create_user_id = $auth['id'];
      $model->update_user_id = $auth['id'];
			if (!$model->create($_POST))
			{

				foreach ($model->getMessages() as $message)
				{
					$this->flashSession->error((string )$message->getMessage());
					//echo var_dump($message->getType());
					//echo var_dump($message->getMessage());
				}
				return $this->response->redirect('backend/interviews/create');
			} else
			{
				$this->SaveImages($model);
        $this->flashSession->success("Интервью создано");
        return $this->response->redirect("backend/interviews");
			}
		} 
	}

  public function editAction($id = null)
	{
    $this->assets->addJs('js/jquery.MultiFile.pack.js');
		if (!$this->request->isPost() && !$this->request->isAjax())
		{

			$id = $this->filter->sanitize($id, array("int"));

			$model = $this->loadModel($id);

			$this->view->setVar("id", $model->id);
      $this->view->setVar('images', $model->getImages());

      Tag::displayTo("status", $model->status);
      Tag::displayTo("respondent", $model->respondent);
      Tag::displayTo("phrase", $model->phrase);
			Tag::displayTo("title", $model->title);
			Tag::displayTo("body", $model->body);
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
				$this->flashSession->error("Интервью не найдено");
        $this->response->redirect("backend/interviews/index");
			}
      $model->update_user_id = $auth['id'];
			if (!$model->update($_POST))
			{

				foreach ($model->getMessages() as $message)
				{
					$this->flashSession->error((string )$message->getMessage());
					//echo var_dump($message->getType());
					//echo var_dump($message->getMessage());
				}
				return $this->response->redirect('backend/interviews/edit/'.$model->id);
			} else
			{
				$this->SaveImages($model);
        $this->flashSession->success("Интервью удачно обновлено");
        return $this->response->redirect("backend/interviews");
			}
		}
	}
  
  public function deleteimageAction($id = null, $name = null)
	{
		if (!$this->request->isPost() && isset($id) && isset($name))
		{
			$id = $this->filter->sanitize($id, array("int"));
			$name = $this->filter->sanitize($name, array("striptags"));
			if (is_numeric($id))
			{
				$model = $this->loadModel($id);
				if (!$model)
				{
					$this->flashSession->error("Картинка не найдена");
					return $this->response->redirect("backend/interviews/index");
				}
				$model->deleteImage($name);
				$this->flashSession->success("Изображение удалено");
				$this->response->redirect("backend/interviews/edit/$model->id");
			} else
			{
				$this->response->redirect();
			}
		}

	}


 public function deleteAction($id)
	{

		$id = $this->filter->sanitize($id, array("int"));

		$model = $this->loadModel($id);

		if (!$model->delete())
		{
			foreach ($interviews->getMessages() as $message)
			{
				$this->flash->error((string )$message);
			}
			return $this->response->redirect("backend/interviews");
		} else
		{
			$this->flashSession->success("interviews was deleted");
			return $this->response->redirect("backend/interviews");
		}
	}
  
  private function SaveImages($model)
	{
		if (!(\Helpers\Arr::is_array_empty($_FILES['Interviews']['tmp_name'])))
		{
			$path = 'uploads' . DIRECTORY_SEPARATOR . 'interviews' . DIRECTORY_SEPARATOR . 'images_id_' . $model->id;
			$errors = array();
			$images = $model->getImages();
			if ($images)
			{
				$num = count($images);
			} else
			{
				$num = $this->config->application->images_number;
			}
			foreach ($_FILES['Interviews']['tmp_name'] as $key => $tmp_name)
			{
				if ($key >= $num)
				{
					break;
				}
				$file_name = $_FILES['Interviews']['name'][$key];
				$file_size = $_FILES['Interviews']['size'][$key];
				$file_tmp = $_FILES['Interviews']['tmp_name'][$key];
				$file_type = $_FILES['Interviews']['type'][$key];
				if (\Helpers\CFileHelper::is_kir($file_tmp))
				{
					$nameParts = explode('.', $file_tmp);
					$imageName = \Helpers\Translite::$rustable($nameParts[0]) . '.' . $nameParts[1];
				}
				if ($file_size > 259715)
				{
					$errors[] = 'Размер файла не больше 2 мегабайт';
				}
				$extensions = array(
					"gif",
					"jpg",
					"png",
					"jpeg");
				$file_ext = explode('.', $file_name);
				$file_ext = strtolower(end($file_ext));
				if (in_array($file_ext, $extensions) === false)
				{
					$errors[] = "Недопустимый формат файла, допускаются изображения форматов: jpg, gif, png, jpeg";
				}
				if (empty($errors) == true)
				{
					if (is_dir($path) == false)
					{
						mkdir("$path", 0775); // Create directory if it does not exist
					}
					if (is_dir("$path/" . $file_name) == false)
					{
						move_uploaded_file($file_tmp, $path . DIRECTORY_SEPARATOR . $file_name);
					}
				} else
				{
					foreach ($errors as $error)
					{
						$this->flashSession->error((string )$error);
					}
					return $this->response->redirect("backend/interviews/edit/$model->id");
				}
			}
		}
	}
  
  public function loadModel($id)
	{
		$model = \Models\Interviews::findFirst("id = '$id'");
		if (!$model)
		{
			$this->flashSession->error("Интервью не найдена");
			return $this->response->redirect("backend/interviews/index");
		}
		return $model;
	}

}
