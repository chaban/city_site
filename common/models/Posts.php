<?php
namespace Models;

class Posts extends \Phalcon\Mvc\Model
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
	public $title;

	/**
	 * @var string
	 *
	 */
	public $body;

	/**
	 * @var string
	 *
	 */
	public $tags;

	/**
	 * @var integer
	 *
	 */
	public $status;

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
	 * @var integer
	 *
	 */
	public $author_id;

	/**
	 * @var string
	 *
	 */
	public $slug;

	const STATUS_DRAFT = 1;
	const STATUS_PUBLISHED = 0;
	const STATUS_ARCHIVED = 2;

	private $_oldTags;

	/**
	 * Initializer method for model.
	 */
	public function initialize()
	{
		$this->useDynamicUpdate(true);
		$this->addBehavior(new \Models\Behaviors\SlugBehavior(array(
			'slug_col' => 'slug', //The column name for the slug
			'title_col' => 'title', //The column name for the unqiue url
			'pk_col' => 'id', //Primary key
			'overwrite' => true, //Overwrite slug when updating
			'url_decode' => false //Decode url only usefull if you want to support high unicode characters in url
				)));
		$this->addBehavior(new \Models\Behaviors\CommentBehavior(array(
			'class' => '\Models\Behaviors\CommentBehavior',
			'class_name' => 'Posts',
			'owner_title' => 'title', // Attribute name to present comment owner in admin panel
			'pk' => 'id' //model's Primary key
				)));
		$this->belongsTo("author_id", "\Models\Users", "id", array('alias' => 'Author'));
	}

	public function validation()
	{
		$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array('field' => 'body', 'message' =>
				'Содержание объявления обязательно для заполнения')));
		$this->validate(new \Phalcon\Mvc\Model\Validator\StringLength(array(
			"field" => "title",
			"max" => 255,
			"min" => 2,
			"maximumMessage" => "Слишком длинное заглавие")));
		return $this->validationHasFailed() != true;
	}

	public function beforeValidationOnCreate()
	{
		$created = $this->readAttribute('create_time');
		$updated = $this->readAttribute('update_time');
		if (null === $created)
		{
			$this->create_time = new \Phalcon\Db\RawValue('NOW()');
		}
		if (null === $updated)
		{
			$this->update_time = new \Phalcon\Db\RawValue('NOW()');
		}
	}

	public function beforeValidationOnUpdate()
	{
		$created = $this->readAttribute('create_time');
		if (null === $created)
		{
			$this->create_time = new \Phalcon\Db\RawValue('NOW()');
		}
		$this->update_time = new \Phalcon\Db\RawValue('NOW()');
	}

	protected function afterSave()
	{
		\Models\Tags::updateFrequency($this->_oldTags, $this->tags);
	}

	protected function afterDelete()
	{
		\Models\Tags::updateFrequency($this->tags, '');
	}

	/* public function afterFetch()
	{
	$this->_oldTags = $this->tags;
	} */

	//in phalcon >= 1.2.0 must be used afterFetch()
	public static function find($parameters = NULL)
	{
		$results = parent::find($parameters = NULL);
    foreach($results as $result)
    {
		$_oldTags = $result->tags;
    }
		return $results;
	}

	public static function findFirst($parameters = NULL)
	{
		$result = parent::findFirst($parameters = null);
		$_oldTags = $result->tags;
		return $result;
	}

	public function getStatusOptions()
	{
		return array(
			self::STATUS_PUBLISHED => 'Опубликовано',
			self::STATUS_DRAFT => 'В черновиках',
			self::STATUS_ARCHIVED => 'В архиве',
			);
	}

	public function getStatusText()
	{
		$statusOptions = $this->getStatusOptions();
		return isset($statusOptions[$this->status]) ? $statusOptions[$this->status] : "Неопределенный статус ({$this->status})";
	}
  
  /**
	 * @return array a list of links that point to the post list filtered by every tag of this post
	 */
	public function getTagLinks()
	{
		$links=array();
		foreach(\Helpers\TextHelper::string2array($this->tags) as $tag)
			$links[]=\Phalcon\Tag::linkTo('posts/index?tags='.$tag, $tag);
		return $links;
	}

}
