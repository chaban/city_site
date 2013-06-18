<?php
namespace Models;

use Phalcon\Mvc\Model;

/**
 * Shop\Models\Profiles
 *
 * All the users registered in the application
 */
class Roles extends Model
{
	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $active;

	public function getSource()
	{
		return "roles";
	}

	public function initialize()
	{
		$this->hasMany('id', 'Models\Users', 'role', array('alias' => 'users', 'foreignKey' => array('message' =>
					'Роль не может быть удалена потому что используется пользователями')));

		$this->hasMany('id', 'Models\Permissions', 'profilesId', array('alias' => 'permissions'));
	}

}
