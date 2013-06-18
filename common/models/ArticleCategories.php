<?php
namespace Models;

class ArticleCategories extends \Phalcon\Mvc\Model
{
	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	public function initialize()
	{
		$this->hasMany('id', '\Models\Articles', 'category', array('foreignKey' => array('message' =>
					'Категория статей не может быть удалена, потому что используется одной из статей')));
	}

	public function validation()
	{
		$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array('field' => 'name', 'message' =>
				'Название обязательно для заполнения')));
		$this->validate(new \Phalcon\Mvc\Model\Validator\StringLength(array(
			"field" => "name",
			"max" => 255,
			"min" => 2,
			"maximumMessage" => "Слишком длинное название")));
		return $this->validationHasFailed() != true;
	}
}
