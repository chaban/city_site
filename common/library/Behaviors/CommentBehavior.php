<?php
namespace Models\Behaviors;

/**
 * Behavior for commentabe models
 */
class CommentBehavior extends \Phalcon\Mvc\Model\Behavior
{
	private $_owner;

	/**
	 * @var string model primary key attribute
	 */
	public $pk = 'id';

	/**
	 * @var string alias to class
	 */
	public $class_name;

	/**
	 * @var string attribute name to present comment owner in admin panel. e.g: name - references to Page->name
	 */
	public $owner_title;

	public function __construct($options)
	{
		if (isset($options['class_name']))
		{
			$this->class_name = $options['class_name'];
		}

		if (isset($options['owner_title']))
		{
			$this->owner_title = $options['owner_title'];
		}

		if (isset($options['pk']))
		{
			$this->pk = $options['pk'];
		}
	}

	/**
	 * @return string pk name
	 */
	public function getObjectPkAttribute()
	{
		return $this->pk;
	}

	public function getClassName()
	{
		return $this->class_name;
	}

	public function getOwnerTitle()
	{
		$attr = $this->owner_title;
		return $this->getOwner()->$attr;
	}

	/**
	 *
	 * @return mixed
	 */
	public function afterDelete()
	{
		$pk = $this->getObjectPkAttribute();
		$comments = \Models\Comments::query()->where("class_name = :class_name:")->andWhere("object_pk = :object_pk:")->
			bind(array("class_name" => $this->getClassName(), "object_pk" => $this->getOwner()->$pk))->execute();
		if ($comments->count())
		{
			foreach ($comments as $comment)
			{
				$comment->delete();
			}
		}
	}

	/**
	 * @return string approved comments count for object
	 */
	public function getCommentsCount()
	{
		$pk = $this->getObjectPkAttribute();
		$comments = \Models\Comments::query()->where("class_name = :class_name:")->andWhere("object_pk = :object_pk:")->
			bind(array("class_name" => $this->getClassName(), "object_pk" => $this->getOwner()->$pk))->execute();
		return $comments->count();
	}

	public function getOwner()
	{
		return $this->_owner;
	}

	public function setOwner($owner)
	{
		$this->_owner = $owner;
	}

	/**
	 * Receive notifications from the Models Manager
	 *
	 * @param string $eventType
	 * @param Phalcon\Mvc\ModelInterface $model
	 */
	public function notify($eventType, $model)
	{
		if ($eventType == 'afterDelete')
		{
			return $this->afterDelete();
		}
	}
  public function missingMethod($model, $method, $arguments = null)
  {
    if (method_exists($this, $method))
    {
      $this->setOwner($model);
      $result = call_user_func_array(array($this, $method), $arguments);
      if ($result === null)
      {
        return '';
      }
      return $result;
    }
    return null;
  }
}
