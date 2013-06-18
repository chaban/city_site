<?php
namespace Forms;

use Phalcon\Forms\Form, Phalcon\Forms\Element\Text, Phalcon\Forms\Element\Hidden, Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Submit, Phalcon\Forms\Element\Check, Phalcon\Validation\Validator\PresenceOf, Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical, Phalcon\Validation\Validator\StringLength, Phalcon\Validation\Validator\Confirmation;

class SignUpForm extends Form
{

	public function initialize($entity = null, $options = null)
	{

		$name = new Text('name');

		$name->setLabel('Имя');

		$name->addValidators(array(new PresenceOf(array('message' => 'Поле Имя обязательно для заполнения'))));

		$this->add($name);

		//Email
		$email = new Text('email');

		$email->setLabel('E-Mail');

		$email->addValidators(array(new PresenceOf(array('message' => 'Адрес электронной почты обязателен')), new Email(array
				('message' => 'Не верный адрес электронной почты'))));

		$this->add($email);
    /*
		//Password
		$password = new Password('password');

		$password->setLabel('Пароль');

		$password->addValidators(array(
			new PresenceOf(array('message' => 'Поле пароль обязательно')),
			new StringLength(array('min' => 8, 'messageMinimum' => 'Пароль слишком короткий. Минимум 8 знаков')),
			new Confirmation(array('message' => 'Не совпадение в полях паролей', 'with' => 'confirmPassword'))));

		$this->add($password);

		//Confirm Password
		$confirmPassword = new Password('confirmPassword');

		$confirmPassword->setLabel('Подтвердить пароль');

		$confirmPassword->addValidators(array(new PresenceOf(array('message' => 'Поле подтверждения пароля обязательно'))));

		$this->add($confirmPassword);
    */
		//Remember
		$terms = new Check('terms', array('value' => 'yes', 'class'=>'checkbox'));

		$terms->setLabel('Вы согласны с условиями соглашения');

		$terms->addValidator(new Identical(array('value' => 'yes', 'message' => 'Условия соглашения должны быть приняты')));

		$this->add($terms);

		//CSRF
		$csrf = new Hidden('csrf');

		$csrf->addValidator(new Identical(array('value' => $this->security->getSessionToken(), 'message' =>
				'сработала защита от CSRF')));

		$this->add($csrf);

		//Sign Up
		$this->add(new Submit('Зарегистрироваться', array('class' => 'btn btn-success')));

	}

	/**
	 * Prints messages for a specific element
	 */
	public function messages($name)
	{
		if ($this->hasMessagesFor($name))
		{
			foreach ($this->getMessagesFor($name) as $message)
			{
				$this->flash->error($message);
			}
		}
	}

}
