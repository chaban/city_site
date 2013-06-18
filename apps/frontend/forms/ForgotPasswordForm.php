<?php

namespace Forms;

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Submit,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email;

use Shop\Models\Profiles;

class ForgotPasswordForm extends Form
{

	public function initialize()
	{
		$email = new Text('email', array(
			'placeholder' => 'Ваша почта'
		));

		$email->addValidators(array(
			new PresenceOf(array(
				'message' => 'Поле почты обязательно для заполнения'
			)),
			new Email(array(
				'message' => 'Не верно написан адерес почты'
			))
		));

		$this->add($email);

		$this->add(new Submit('Отправить', array(
			'class' => 'btn btn-primary'
		)));
	}

}