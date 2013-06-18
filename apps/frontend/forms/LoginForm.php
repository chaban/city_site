<?php

namespace Forms;

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit,
	Phalcon\Forms\Element\Check,
	Phalcon\Forms\Element\Hidden,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical;

use Shop\Models\Profiles;

class LoginForm extends Form
{

	public function initialize()
	{
		//Email
		$email = new Text('email', array(
			'placeholder' => 'Email'
		));

		$email->addValidators(array(
			new PresenceOf(array(
				'message' => 'Адрес электронной почты обязателен'
			)),
			new Email(array(
				'message' => 'Не верный адрес электронной почты'
			))
		));

		$this->add($email);

		//Password
		$password = new Password('password', array(
			'placeholder' => 'Пароль'
		));

		$password->addValidator(
			new PresenceOf(array(
				'message' => 'Пароль обязателен'
			))
		);

		$this->add($password);

		//Remember
		$remember = new Check('remember', array(
			'value' => 'yes'
		));

		$remember->setLabel('Запомнить меня');

		$this->add($remember);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(
			new Identical(array(
				'value' => $this->security->getSessionToken(),
				'message' => 'сработала защита от CSRF'
			))
		);

		$this->add($csrf);

		$this->add(new Submit('Войти', array(
			'class' => 'btn btn-success'
		)));
	}

}