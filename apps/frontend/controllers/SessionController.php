<?php
namespace Frontend\Controllers;

use Forms\LoginForm, Forms\SignUpForm, Forms\ForgotPasswordForm, Auth\Auth, Auth\Exception as AuthException, Models\Users,
	Models\ResetPasswords;

class SessionController extends ControllerBase
{

	public function initialize()
	{
		$this->view->setTemplateBefore('main');
		parent::initialize();
	}

	public function indexAction()
	{

	}

	public function signupAction()
	{
		\Phalcon\Tag::appendTitle(" - Регистрация");
		$form = new SignUpForm();

		if ($this->request->isPost())
		{

			if ($form->isValid($this->request->getPost()) != false)
			{

				$user = new Users();

				$user->assign(array(
					'name' => $this->request->getPost('name', 'striptags'),
					'email' => $this->request->getPost('email'),
					'password' => $this->security->hash($this->request->getPost('password')),
					'profilesId' => 2));

				if ($user->save())
				{
					return $this->dispatcher->forward(array('controller' => 'index', 'action' => 'index'));
				}

				$this->flash->error($user->getMessages());
			}

		}
		$pass = $this->security->checkHash('bob', '$2a$08$MtaK0TBGL0Wh4IVpMYMwlu.SmRpxja3r5n46edlPINe5uzlqi1Vym');
		$this->view->form = $form;
		$this->view->setVar('pass', $pass);
	}

	/**
	 * Starts a session in the admin backend
	 */
	public function loginAction()
	{
		\Phalcon\Tag::appendTitle(" - Вход");

		$form = new LoginForm();

		try
		{

			if (!$this->request->isPost())
			{

				if ($this->auth->hasRememberMe())
				{
					return $this->auth->loginWithRememberMe();
				}

			} else
			{

				if ($form->isValid($this->request->getPost()) == false)
				{
					foreach ($form->getMessages() as $message)
					{
						$this->flash->error($message);
					}
				} else
				{

					$this->auth->check(array(
						'email' => $this->request->getPost('email', 'email'),
						'password' => $this->request->getPost('password'),
						'remember' => $this->request->getPost('remember')));
					$auth = $this->session->get('auth');
					$this->flashSession->notice($auth['role']);
					if (!$auth)
					{
						$this->flashSession->notice("Нужно войти или зарегистрироваться");
						return $this->response->redirect("session/login");
					}
					if ($auth['role'] == 'Administrators')
					{
						$this->flashSession->success("Здравствуйте администратор. Вы находитесь в области управления сайтом, в разделе Новости.");
						return $this->response->redirect("backend/news");
					}
					return $this->response->redirect();
				}
			}

		}
		catch (AuthException $e)
		{
			$this->flash->error($e->getMessage());
		}

		$this->view->form = $form;
	}

	/**
	 * Shows the forgot password form
	 */
	public function forgotPasswordAction()
	{
		$form = new ForgotPasswordForm();

		if ($this->request->isPost())
		{

			if ($form->isValid($this->request->getPost()) == false)
			{
				foreach ($form->getMessages() as $message)
				{
					$this->flash->error($message);
				}
			} else
			{

				$user = Users::findFirstByEmail($this->request->getPost('email'));
				if (!$user)
				{
					$this->flash->success('Не найден аккаунт привязанный к этой почте');
				} else
				{

					$resetPassword = new ResetPasswords();
					$resetPassword->usersId = $user->id;
					if ($resetPassword->save())
					{
						$this->flash->success('Удачно! Пожалуйста проверте Вашу электронную почту для смены пароля');
					} else
					{
						foreach ($resetPassword->getMessages() as $message)
						{
							$this->flash->error($message);
						}
					}
				}
			}
		}

		$this->view->form = $form;
	}

	/**
	 * Closes the session
	 */
	public function logoutAction()
	{
		$this->auth->remove();

		return $this->response->redirect();
	}

}
