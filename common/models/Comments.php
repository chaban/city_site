<?php
namespace Models;

class Comments extends \Phalcon\Mvc\Model
{

	/**
	 * @var integer
	 *
	 */
	public $id;

	/**
	 * @var integer
	 *
	 */
	public $user_id;

	/**
	 * @var string
	 *
	 */
	public $created;

	/**
	 * @var string
	 *
	 */
	public $updated;

	/**
	 * @var string
	 *
	 */
	public $name;

	/**
	 * @var string
	 *
	 */
	public $email;

	/**
	 * @var string
	 *
	 */
	public $ip_address;

	/**
	 * @var string
	 *
	 */
	public $text;

	/**
	 * @var integer
	 *
	 */
	public $status;

	/**
	 * @var integer
	 *
	 */
	public $parent_comment_id;

	/**
	 * @var string
	 *
	 */
	public $class_name;

	/**
	 * @var integer
	 *
	 */
	public $object_pk;

	/**
	 * Validations and business logic 
	 */
	public function validation()
	{
		$this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array('field' => 'text', 'message' =>
				'Содержание объявления обязательно для заполнения')));

		$this->validate(new \Phalcon\Mvc\Model\Validator\StringLength(array(
			"field" => "name",
			"max" => 255,
			"min" => 2,
			"maximumMessage" => "Слишком длинное Имя")));

		$this->validate(new \Phalcon\Mvc\Model\Validator\Email(array(
			"field" => "email",
			"required" => false,
			'message' => 'Не верный адрес электронной почты')));

		if ($this->validationHasFailed() == true)
		{
			return false;
		}
	}

	const STATUS_WAITING = 0;
	const STATUS_APPROVED = 1;
	const STATUS_SPAM = 2;

	/**
	 * @var int status for new comments
	 */
	public $defaultStatus;

	/**
	 * Initializer method for model.
	 */
	public function initialize()
	{
		$this->useDynamicUpdate(true);
		$this->belongsTo("user_id", "\Models\Users", "id", array('alias' => 'User'));
		$this->belongsTo("id", "\Models\Comments", "parent_comment_id", array('alias' => 'Parent'));
		$this->HasMany("parent_comment_id", "\Models\Comments", "id", array('alias' => 'Childs'));
		$this->defaultStatus = self::STATUS_APPROVED;
	}

	public function beforeValidationOnCreate()
	{
		$request = $this->getDI()->getRequest();
		$this->status = $this->defaultStatus;
		$this->ip_address = $request->getClientAddress();
		$this->created = date('Y-m-d H:i:s');
    $this->updated = date('Y-m-d H:i:s');
	}

	public function beforeValidationOnUpdate()
	{
		$this->updated = date('Y-m-d H:i:s');
	}
  
  /**
	 * @static
	 * @return array
	 */
	public static function getStatuses()
	{
		return array(
			self::STATUS_WAITING  => 'Ждет одобрения',
			self::STATUS_APPROVED => 'Подтвержден',
			self::STATUS_SPAM     => 'Спам',
		);
	}
  
  /**
	 * @return string status title
	 */
	public function getStatusTitle()
	{
		$statuses = self::getStatuses();
		return $statuses[$this->status];
	}
  
  /**
	 * @return string
	 */
	public function getOwner_title()
	{
		if(!$this->getDirtyState() == \Phalcon\Mvc\Model::DIRTY_STATE_TRANSIENT)
		{
			try{
				$className = '\Models\\'.$this->class_name;
			}catch(\Phalcon\Exception $e){
				return null;
			}

			$model = $className::findFirst("id = '$this->object_pk'");
			if($model)
				return $model->getOwnerTitle();
		}
		return '';
	}
  
  public static function truncate(\Models\Comments $model, $limit)
	{
		$result = $model->text;
		$length = mb_strlen($result,'utf-8');
		if($length > $limit)
		{
			return mb_substr($result,0,$limit,'utf-8').'...';
		}
		return $result;
	}

	/*
	* Return array with prepared comments for given modelName and id
	* @return Comments array array with comments 
	*/

	public static function getCommentsTree($model)
	{
		$criteria = new \Phalcon\Mvc\Model\Criteria();
		$criteria->setModelName("\Models\Comments");
		$criteria->where('class_name = :class_name: and object_pk = :object_pk: and status <> :status:');
		$criteria->bind(array(
			"class_name" => $model->getClassName(),
			"object_pk" => $model->id,
			"status" => self::STATUS_SPAM));
		$criteria->order('created ASC');
		$comments = $criteria->execute();
		//return self::buildTree($comments);
    return $comments;
	}

	/*
	* recursively build the comment tree for given root node
	* @param array $data array with comments data
	* @int $rootID root node id
	* @return Comment array 
	*/

	private static function buildTree(&$data, $rootID = 0)
	{
		$tree = array();
		foreach ($data as $id => $node)
		{
			$node->parent_comment_id = $node->parent_comment_id === null ? 0 : $node->parent_comment_id;
			if ($node->parent_comment_id == $rootID)
			{
				unset($data[$id]);
				$node->childs = $this->buildTree($data, $node->id);
				$tree[] = $node;
			}
		}
		return $tree;
	}

}
