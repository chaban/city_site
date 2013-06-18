<?php
namespace Frontend\Controllers;
class CommentsController extends \Phalcon\Mvc\Controller
{
	public function createAction()
	{
		$comment = new \Models\Comments;
		if ($this->request->isPost() and !$this->request->isAjax() and isset($_POST['Comment']))
		{
			$comment->text = $this->filter->sanitize($_POST['Comment']['text'], 'string');
			$comment->name = $this->filter->sanitize($_POST['Comment']['name'], 'string');
			$comment->email = $this->filter->sanitize($_POST['Comment']['email'], 'email');
      $model_id = $this->filter->sanitize($_POST['Comment']['model_id'], "int");
      $model_name = $this->filter->sanitize($_POST['Comment']['model_name'], "alphanum");
      //$slug = $this->filter->sanitize($_POST['Comment']['model_slug'], "striptags");
      
			$auth = $this->session->get('auth');
			if (isset($auth['role']))
			{
				$comment->name = $auth['name'];
				$comment->email = $auth['email'];
			}
      
			$comment->class_name = $model_name;
			$comment->object_pk = $model_id;
			$comment->user_id = $auth['role'] ? $auth['id'] : 0;
			$comment->parent_comment_id = 0;
      $comment->status = 1;
      $model_name = strtolower($model_name);
			if ($comment->save() == false)
			{
				foreach ($comment->getMessages() as $message)
				{
					$this->flashSession->error((string )$message);
				}
				return $this->response->redirect("$model_name/show/$model_id/");
			}
			$this->flashSession->success("Ваш комментарий успешно добавлен. Он будет опубликован после проверки модератором.");
			return $this->response->redirect("$model_name/show/$model_id/");
		}
	}
}
