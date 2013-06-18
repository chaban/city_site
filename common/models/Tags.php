<?php
namespace Models;

class Tags extends \Phalcon\Mvc\Model
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
	public $name;

	/**
	 * @var integer
	 *
	 */
	public $frequency;

	public function initialize()
	{
		$this->useDynamicUpdate(true);
	}

	/**
	 * Returns tag names and their corresponding weights.
	 * Only the tags with the top weights will be returned.
	 * @param integer the maximum number of tags that should be returned
	 * @return array weights indexed by tag names.
	 */
	public static function findTagWeights($limit = 20)
	{
		$models = \Models\Tags::find(array(
			'order' => 'frequency DESC',
			'limit' => $limit,
			));

		$total = 0;
		foreach ($models as $model)
			$total += $model->frequency;

		$tags = array();
		if ($total > 0)
		{
			foreach ($models as $model)
				$tags[$model->name] = 8 + (int)(16 * $model->frequency / ($total + 10));
			ksort($tags);
		}
		return $tags;
	}

	/**
	 * Suggests a list of existing tags matching the specified keyword.
	 * @param string the keyword to be matched
	 * @param integer maximum number of tags to be returned
	 * @return array list of matching tag names
	 */
	public static function suggestTags($keyword, $limit = 20)
	{
		$tags = \Models\Tags::find(array(
			'conditions' => 'name LIKE :keyword:',
			'bind' => array('keyword' => '%' . strtr($keyword, array(
					'%' => '\%',
					'_' => '\_',
					'\\' => '\\\\')) . '%', ),
			'order' => 'frequency DESC',
			'limit' => $limit,
			));
		$names = array();
		foreach ($tags as $tag)
			$names[] = $tag->name;
		return $names;
	}

	public static function updateFrequency($oldTags, $newTags)
	{
		$oldTags = \Helpers\TextHelper::string2array($oldTags);
		$newTags = \Helpers\TextHelper::string2array($newTags);
		self::addTags(array_values(array_diff($newTags, $oldTags)));
		self::removeTags(array_values(array_diff($oldTags, $newTags)));
	}

	public static function addTags($tags)
	{
		$names = '';
		if (!\Helpers\Arr::is_array_empty($tags))
		{
			$names = \Helpers\TextHelper::array2string($tags);
		}
		$names = rtrim($names, " ,");
		$criteria = new \Phalcon\Mvc\Model\Criteria;
		$criteria->setModelName("\Models\Tags");
		//$criteria->setDI($this->di);
		//$criteria->addInCondition('name',$tags);
		$criteria->andWhere("name IN ($names)");
		//$this->updateCounters(array('frequency' => 1), $criteria);

		$models = \Models\Tags::find($criteria);
		if ($models->count())
		{
			foreach ($models as $model)
			{
				$model->frequency = $model->frequency + 1;
				$model->update();
			}
		}
		foreach ($tags as $name)
		{
			$conditions = "name = :name:";
			$parameters = array("name" => $name);
			if (!\Models\Tags::find(array($conditions, "bind" => $parameters))->count())
			{
				$tag = new \Models\Tags;
				$tag->name = $name;
				$tag->frequency = 1;
				$tag->save();
			}
		}
	}

	public static function removeTags($tags)
	{
		if (empty($tags))
			return;
		$names = '';
		if (!\Helpers\Arr::is_array_empty($tags))
		{
			$names = \Helpers\TextHelper::array2string($tags);
		}
		$names = rtrim($names, " ,");
		$criteria = new \Phalcon\Mvc\Model\Criteria;
		$criteria->setModelName("\Models\Tags");
		//$criteria->setDI($this->di);
		//$criteria->addInCondition('name',$tags);
		$criteria->andWhere("name IN ($names)");
		//$this->updateCounters(array('frequency' => 1), $criteria);

		$models = \Models\Tags::find($criteria);
		if ($models->count())
		{
			foreach ($models as $model)
			{
				$model->frequency = $model->frequency - 1;
				$model->update();
			}
		}
		foreach (\Models\Tags::find("frequency<='0'") as $model)
		{
			$model->delete();
		}
	}
  
  
	public static function getTagsForSelect()
	{
		$names = array();
    $tags = \Models\Tags::find();
    $tags->setHydrateMode(\Phalcon\Mvc\Model\Resultset::HYDRATE_OBJECTS);
		if ($tags->count())
		{
		  foreach($tags as $tag)
      {
        $names[$tag->id] = $tag->name;
      }
      //return \Helpers\CJSON::encode($names);
      return $names;
		}
	}
}
