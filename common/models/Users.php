<?php
namespace Models;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;

class Users extends \Phalcon\Mvc\Model
{

	/**
	 * @var integer
	 *
	 */
	public $id;

	/**
	 * @var string
	 *
	 */
	public $username;

	/**
	 * @var string
	 *
	 */
	public $fullname;

	/**
	 * @var string
	 *
	 */
	public $email;

	/**
	 * @var string
	 *
	 */
	public $password;

	/**
	 * @var integer
	 *
	 */
	public $role_id;

	/**
	 * @var string
	 *
	 */
	public $active;

	/**
	 * @var string
	 *
	 */
	public $last_login_time;

	/**
	 * @var string
	 *
	 */
	public $create_time;

	/**
	 * @var string
	 *
	 */
	public $update_time;
	/**
	 * @var string
	 */
	public $mustChangePassword;

	/**
	 * @var string
	 */
	public $banned;

	/**
	 * @var string
	 */
	public $suspended;

	/**
	 * This model is mapped to the table user
	 */
	public function getSource()
	{
		return 'users';
	}

	/**
	 * Before create the user assign a password
	 */
	public function beforeValidationOnCreate()
	{
		if (empty($this->password))
		{

			//Generate a plain temporary password
			$tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

			//The user must change its password in first login
			$this->mustChangePassword = 'Y';

			//Use this password as default
			$this->password = $this->getDI()->getSecurity()->hash($tempPassword);

		} else
		{
			//The user must not change its password in first login
			$this->mustChangePassword = 'N';

		}

		//The account must be confirmed via e-mail
		$this->active = 'N';

		//The account is not suspended by default
		$this->suspended = 'N';

		//The account is not banned by default
		$this->banned = 'N';
	}

	/**
	 * Send a confirmation e-mail to the user if the account is not active
	 */
	public function afterSave()
	{
		if ($this->active == 'N')
		{

			$emailConfirmation = new EmailConfirmations();

			$emailConfirmation->usersId = $this->id;

			if ($emailConfirmation->save())
			{
				$this->getDI()->getFlash()->notice('Письмо с уведомлением было отправлено на почту ' . $this->email);
			}
		}
	}

	/**
	 * Validations and business logic 
	 */
	public function validation()
	{
		$this->validate(new EmailValidator(array('field' => 'email', 'required' => true)));
		$this->validate(new UniquenessValidator(array('field' => 'email', 'message' =>
				'Эта электронная почта знята другим пользователем')));
		$this->validate(new StringLengthValidator(array(
			'field' => 'name',
			'max' => 255,
			'min' => 2,
			'maximumMessage' => 'Слишком длинное имя пользователя',
			'minimumMessage' => 'Слишком короткое имя пользователя')));
		if ($this->validationHasFailed() == true)
		{
			return false;
		}
	}

	/**
	 * Initializer method for model.
	 */
	public function initialize()
	{
		$this->hasMany("id", "Models\Articles", "update_user_id", array('alias' => 'Articles'));
		$this->hasMany("id", "Models\Banner", "update_user_id", array('alias' => 'Banners'));
		$this->hasMany("id", "Models\Comments", "author", array('alias' => 'Comments'));
		$this->hasMany("id", "Models\Adverts", "update_user_id", array('alias' => 'Adverts'));
		$this->hasMany("id", "Models\Interviews", "update_user_id", array('alias' => 'Interviews'));
		$this->hasMany("id", "Models\News", "update_user_id", array('alias' => 'News'));
		$this->hasMany("id", "Models\Posts", "author_id", array('alias' => 'Posts'));
    
    $this->belongsTo('role_id', 'Models\Roles', 'id', array(
			'alias' => 'Role',
			'reusable' => true
		));

		$this->hasMany('id', 'Models\SuccessLogins', 'usersId', array(
			'alias' => 'successLogins',
			'foreignKey' => array(
				'message' => 'Пользователь не может быть удален'
			)
		));

		$this->hasMany('id', 'Models\PasswordChanges', 'usersId', array(
			'alias' => 'passwordChanges',
			'foreignKey' => array(
				'message' => 'Пользователь не может быть удален'
			)
		));

		$this->hasMany('id', 'Models\ResetPasswords', 'usersId', array(
			'alias' => 'resetPasswords',
			'foreignKey' => array(
				'message' => 'Пользователь не может быть удален'
			)
		));
	}

}
