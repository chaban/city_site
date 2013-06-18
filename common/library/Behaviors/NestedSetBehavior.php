<?php namespace Shop\Models\Behaviors;
/**
 * NestedSetBehavior class file.
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @link https://github.com/yiiext/nested-set-behavior
 */

/**
 * Provides nested set functionality for a model.
 *
 * @version 1.06
 * @package yiiext.behaviors.model.trees
 */
class NestedSetBehavior extends \Phalcon\Mvc\Model\Behavior
{

  private $_owner;
  private $_hasManyRoots = false;
  private $_rootAttribute = 'root';
  private $_leftAttribute = 'lft';
  private $_rightAttribute = 'rgt';
  private $_levelAttribute = 'level';

  private $_ignoreEvent = false;
  private $_deleted = false;
  private $_id;

  private static $_cached;

  private static $_c = 0;

  public function __construct($options)
  {
    if (isset($options['rootAttribute']))
    {
      $this->_rootAttribute = $options['rootAttribute'];
    }

    if (isset($options['leftAttribute']))
    {
      $this->_leftAttribute = $options['leftAttribute'];
    }

    if (isset($options['rightAttribute']))
    {
      $this->_rightAttribute = $options['rightAttribute'];
    }

    if (isset($options['levelAttribute']))
    {
      $this->_levelAttribute = $options['levelAttribute'];
    }
  }

  /**
   * Named scope. Gets descendants for node.
   * @param int $depth the depth.
   * @return Phalcon\Mvc\ModelInterface the owner.
   */
  public function descendants($depth = null)
  {
    $owner = $this->getOwner();

    if ($depth !== null)
    {
      $owner = $owner::query()->where("{$this->_leftAttribute} > :lft:")->addWhere("{$this->_rightAttribute} < :rgt:")->
        addWhere("level <= :level:")->bind(array(
        "lft" => $owner->{$this->_leftAttribute},
        "rgt" => $owner->{$this->_rightAttribute},
        "level" => $owner->{$this->_levelAttribute} + $depth))->order("{$this->_leftAttribute}")->execute();
    }
    else
    {
      $owner = $owner::query()->where("{$this->_leftAttribute} > :lft:")->addWhere("{$this->_rightAttribute} < :rgt:")->
        bind(array("lft" => $owner->{$this->_leftAttribute}, "rgt" => $owner->{$this->_rightAttribute}))->order("{$this->_leftAttribute}")->
        execute();
    }

    return $owner;
  }

  /**
   * Named scope. Gets children for node (direct descendants only).
   * @return Phalcon\Mvc\ModelInterface the owner.
   */
  public function children()
  {
    return $this->descendants(1);
  }

  /**
   * Named scope. Gets ancestors for node.
   * @param int $depth the depth.
   * @return Phalcon\Mvc\ModelInterface the owner.
   */
  public function ancestors($depth = null)
  {
    $owner = $this->getOwner();

    if ($depth !== null)
    {
      $owner = $owner::query()->where("{$this->_leftAttribute} < :lft:")->addWhere("{$this->_rightAttribute} > :rgt:")->
        addWhere("level >= :level:")->bind(array(
        "lft" => $owner->{$this->_leftAttribute},
        "rgt" => $owner->{$this->_rightAttribute},
        "level" => $owner->{$this->_levelAttribute}-$depth))->order("{$this->_leftAttribute}")->execute();
    }
    else
    {
      $owner = $owner::query()->where("{$this->_leftAttribute} < :lft:")->addWhere("{$this->_rightAttribute} > :rgt:")->
        bind(array("lft" => $owner->{$this->_leftAttribute}, "rgt" => $owner->{$this->_rightAttribute}))->order("{$this->_leftAttribute}")->
        execute();
    }

    return $owner;
  }

  /**
   * Named scope. Gets root node(s).
   *
   * @return Phalcon\Mvc\ModelInterface[] the owner.
   */
  public function roots()
  {
    $owner = $this->getOwner();
    return $owner::find($this->_leftAttribute . ' = 1');
  }

  /**
   * Named scope. Gets parent of node.
   *
   * @return Phalcon\Mvc\ModelInterface the owner.
   */
  public function parent()
  {
    $owner = $this->getOwner();

    $owner = $owner::query()->where("{$this->_leftAttribute} < :lft:")->addWhere("{$this->_rightAttribute} > :rgt:")->
      bind(array("lft" => $owner->{$this->_leftAttribute}, "rgt" => $owner->{$this->_rightAttribute}))->order("{$this->_rightAttribute}")->
      limit(1)->execute();

    return $owner->getFirst();
  }

  /**
   * Named scope. Gets previous sibling of node.
   * @return Phalcon\Mvc\ModelInterface the owner.
   */
  public function prev()
  {
    $owner = $this->getOwner();
    $db = $owner->getDbConnection();
    $criteria = $owner->getDbCriteria();
    $alias = $db->quoteColumnName($owner->getTableAlias());
    $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->_rightAttribute) . '=' . ($owner->{$this->
      _leftAttribute}-1));

    if ($this->_hasManyRoots)
    {
      $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->_rootAttribute) . '=' . CDbCriteria::
        PARAM_PREFIX . CDbCriteria::$paramCount);
      $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $owner->{$this->_rootAttribute};
    }

    return $owner;
  }

  /**
   * Named scope. Gets next sibling of node.
   * @return Phalcon\Mvc\ModelInterface the owner.
   */
  public function next()
  {
    $owner = $this->getOwner();
    $db = $owner->getDbConnection();
    $criteria = $owner->getDbCriteria();
    $alias = $db->quoteColumnName($owner->getTableAlias());
    $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->_leftAttribute) . '=' . ($owner->{$this->
      _rightAttribute} + 1));

    if ($this->_hasManyRoots)
    {
      $criteria->addCondition($alias . '.' . $db->quoteColumnName($this->_rootAttribute) . '=' . CDbCriteria::
        PARAM_PREFIX . CDbCriteria::$paramCount);
      $criteria->params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $owner->{$this->_rootAttribute};
    }

    return $owner;
  }

  /**
   * Create root node if multiple-root tree mode. Update node if it's not new.
   *
   * @param boolean $runValidation whether to perform validation.
   * @param boolean $attributes list of attributes.
   * @return boolean whether the saving succeeds.
   */
  public function save($attributes = null)
  {
    $owner = $this->getOwner();

    if (!$owner->id)
    {
      return $this->makeRoot($attributes);
    }

    return $owner->update($attributes);
  }

  /**
   * Create root node if multiple-root tree mode. Update node if it's not new.
   *
   * @param boolean $runValidation whether to perform validation.
   * @param boolean $attributes list of attributes.
   * @return boolean whether the saving succeeds.
   */
  public function saveNode($attributes = null)
  {
    return $this->save($attributes);
  }

  /**
   * Deletes node and it's descendants.
   * @return boolean whether the deletion is successful.
   */
  public function deleteNode()
  {
    return $this->deleteTree();
  }

  /**
   * Deletes node and it's descendants.
   * @return boolean whether the deletion is successful.
   * @throws CDbException
   * @throws Exception
   */
  public function deleteTree()
  {
    $owner = $this->getOwner();

    if ($owner->getIsNewRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node cannot be deleted because it is new.');
    }

    if ($this->getIsDeletedRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node cannot be deleted because it is already deleted.');
    }

    if ($owner->isLeaf())
    {
      $result = $owner->delete();
    }
    else
    {
      $condition = $this->_leftAttribute . '>=' . $owner->{$this->_leftAttribute} . ' AND ' . $this->_rightAttribute .
        '<=' . $owner->{$this->_rightAttribute};

      //$params = array();
      //$result = $owner->deleteAll($condition, $params) > 0;
      foreach ($owner::find($condition) as $record)
      {
        $result = $record->delete() > 0;
      }
    }

    if (!$result)
    {
      return false;
    }

    $this->shiftLeftRight($owner->{$this->_rightAttribute} + 1, $owner->{$this->_leftAttribute}-$owner->{$this->
      _rightAttribute}-1);

    $this->correctCachedOnDelete();

    return true;
  }

  /**
   * Prepends node to target as first child.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @param array $attributes list of attributes.
   * @return boolean whether the prepending succeeds.
   */
  public function prependTo($target, $attributes = null)
  {
    return $this->addNode($target, $target->{$this->_leftAttribute} + 1, 1, $attributes);
  }

  /**
   * Prepends target to node as first child.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @param boolean $runValidation whether to perform validation.
   * @param array $attributes list of attributes.
   * @return boolean whether the prepending succeeds.
   */
  public function prepend($target, $attributes = null)
  {
    return $target->prependTo($this->getOwner(), $attributes);
  }

  /**
   * Appends node to target as last child.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @param array $attributes list of attributes.
   * @return boolean whether the appending succeeds.
   */
  public function appendTo($target, $attributes = null)
  {
    return $this->addNode($target, $target->{$this->_rightAttribute}, 1, $attributes);
  }

  /**
   * Appends target to node as last child.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @param array $attributes list of attributes.
   * @return boolean whether the appending succeeds.
   */
  public function append($target, $attributes = null)
  {
    return $target->appendTo($this->getOwner(), $attributes);
  }

  /**
   * Inserts node as previous sibling of target.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @param array $attributes list of attributes.
   * @return boolean whether the inserting succeeds.
   */
  public function insertBefore($target, $attributes = null)
  {
    return $this->addNode($target, $target->{$this->_leftAttribute}, 0, $attributes);
  }

  /**
   * Inserts node as next sibling of target.
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @param array $attributes list of attributes.
   * @return boolean whether the inserting succeeds.
   */
  public function insertAfter($target, $runValidation = true, $attributes = null)
  {
    return $this->addNode($target, $target->{$this->_rightAttribute} + 1, 0, $attributes);
  }

  /**
   * Move node as previous sibling of target.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @return boolean whether the moving succeeds.
   */
  public function moveBefore($target)
  {
    return $this->moveNode($target, $target->{$this->_leftAttribute}, 0);
  }

  /**
   * Move node as next sibling of target.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @return boolean whether the moving succeeds.
   */
  public function moveAfter($target)
  {
    return $this->moveNode($target, $target->{$this->_rightAttribute} + 1, 0);
  }

  /**
   * Move node as first child of target.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @return boolean whether the moving succeeds.
   */
  public function moveAsFirst($target)
  {
    return $this->moveNode($target, $target->{$this->_leftAttribute} + 1, 1);
  }

  /**
   * Move node as last child of target.
   *
   * @param Phalcon\Mvc\ModelInterface $target the target.
   * @return boolean whether the moving succeeds.
   */
  public function moveAsLast($target)
  {
    return $this->moveNode($target, $target->{$this->_rightAttribute}, 1);
  }

  /**
   * Move node as new root.
   * @return boolean whether the moving succeeds.
   * @throws CDbException
   * @throws CException
   * @throws Exception
   */
  public function moveAsRoot()
  {
    $owner = $this->getOwner();

    if (!$this->_hasManyRoots)
    {
      throw new Phalcon\Mvc\Model\Exception('Many roots mode is off.');
    }

    if ($owner->getIsNewRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node should not be new record.');
    }

    if ($this->getIsDeletedRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node should not be deleted.');
    }

    if ($owner->isRoot())
    {
      throw new Phalcon\Mvc\Model\Exception('The node already is root node.');
    }

    $db = $owner->getDbConnection();

    if ($db->getCurrentTransaction() === null)
      $transaction = $db->beginTransaction();

    try
    {
      $left = $owner->{$this->_leftAttribute};
      $right = $owner->{$this->_rightAttribute};
      $levelDelta = 1 - $owner->{$this->_levelAttribute};
      $delta = 1 - $left;

      $owner->updateAll(array(
        $this->_leftAttribute => new CDbExpression($db->quoteColumnName($this->_leftAttribute) . sprintf('%+d', $delta)),
        $this->_rightAttribute => new CDbExpression($db->quoteColumnName($this->_rightAttribute) . sprintf('%+d', $delta)),
        $this->_levelAttribute => new CDbExpression($db->quoteColumnName($this->_levelAttribute) . sprintf('%+d', $levelDelta)),
        $this->_rootAttribute => $owner->getPrimaryKey(),
        ), $db->quoteColumnName($this->_leftAttribute) . '>=' . $left . ' AND ' . $db->quoteColumnName($this->
        _rightAttribute) . '<=' . $right . ' AND ' . $db->quoteColumnName($this->_rootAttribute) . '=' . CDbCriteria::
        PARAM_PREFIX . CDbCriteria::$paramCount, array(CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++ => $owner->{
          $this->_rootAttribute}));

      $this->shiftLeftRight($right + 1, $left - $right - 1);

      if (isset($transaction))
        $transaction->commit();

      $this->correctCachedOnMoveBetweenTrees(1, $levelDelta, $owner->getPrimaryKey());
    }
    catch (exception $e)
    {
      if (isset($transaction))
        $transaction->rollback();

      throw $e;
    }

    return true;
  }

  /**
   * Determines if node is descendant of subject node.
   * @param Phalcon\Mvc\ModelInterface $subj the subject node.
   * @return boolean whether the node is descendant of subject node.
   */
  public function isDescendantOf($subj)
  {
    $owner = $this->getOwner();
    $result = ($owner->{$this->_leftAttribute} > $subj->{$this->_leftAttribute}) && ($owner->{$this->_rightAttribute} <
      $subj->{$this->_rightAttribute});

    if ($this->_hasManyRoots)
      $result = $result && ($owner->{$this->_rootAttribute} === $subj->{$this->_rootAttribute});

    return $result;
  }

  /**
   * Determines if node is leaf.
   * @return boolean whether the node is leaf.
   */
  public function isLeaf()
  {
    $owner = $this->getOwner();

    return $owner->{$this->_rightAttribute}-$owner->{$this->_leftAttribute} === 1;
  }

  /**
   * Determines if node is root.
   * @return boolean whether the node is root.
   */
  public function isRoot()
  {
    return $this->getOwner()->{$this->_leftAttribute} == 1;
  }

  /**
   * Returns if the current node is deleted.
   * @return boolean whether the node is deleted.
   */
  public function getIsDeletedRecord()
  {
    return $this->_deleted;
  }

  /**
   * Sets if the current node is deleted.
   * @param boolean $value whether the node is deleted.
   */
  public function setIsDeletedRecord($value)
  {
    $this->_deleted = $value;
  }

  /**
   * Handle 'afterConstruct' event of the owner.
   * @param CEvent $event event parameter.
   */
  public function afterConstruct($event)
  {
    $owner = $this->getOwner();
    self::$_cached[get_class($owner)][$this->_id = self::$_c++] = $owner;
  }

  /**
   * Handle 'afterFind' event of the owner.
   * @param CEvent $event event parameter.
   */
  public function afterFind($event)
  {
    $owner = $this->getOwner();
    self::$_cached[get_class($owner)][$this->_id = self::$_c++] = $owner;
  }

  /**
   * @param int $key.
   * @param int $delta.
   */
  private function shiftLeftRight($key, $delta)
  {
    $owner = $this->getOwner();

    foreach (array($this->_leftAttribute, $this->_rightAttribute) as $attribute)
    {
      $condition = $attribute . '>=' . $key;
      foreach ($owner::find($condition) as $record)
      {
        $record->$attribute = $record->$attribute + $delta;
        $record->save();
      }
    }
  }

  /**
   * @param Phalcon\Mvc\ModelInterface $target.
   * @param int $key.
   * @param int $levelUp.
   * @param array $attributes.
   * @return boolean.
   * @throws CDbException
   * @throws CException
   * @throws Exception
   */
  private function addNode($target, $key, $levelUp, $attributes)
  {
    $owner = $this->getOwner();

    if (!$owner->getIsNewRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node cannot be inserted because it is not new.');
    }

    if ($owner->getIsDeletedRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node cannot be inserted because it is deleted.');
    }

    if ($target->getIsDeletedRecord())
    {
      throw new Phalcon\Mvc\Model\Exception('The node cannot be inserted because target node is deleted.');
    }

    if ($owner == $target)
    {
      throw new Phalcon\Mvc\Model\Exception('The target node should not be self.');
    }

    if (!$levelUp && $target->isRoot())
    {
      throw new Phalcon\Mvc\Model\Exception('The target node should not be root.');
    }

    $this->shiftLeftRight($key, 2);
    $owner->{$this->_leftAttribute} = $key;
    $owner->{$this->_rightAttribute} = $key + 1;
    $owner->{$this->_levelAttribute} = $target->{$this->_levelAttribute} + $levelUp;

    $result = $owner->create($attributes);
    if (!$result)
    {
      return false;
    }

    $this->correctCachedOnAddNode($key);
    return true;
  }

  /**
   * @param array $attributes.
   * @return boolean.
   * @throws CException
   * @throws Exception
   */
  private function makeRoot($attributes)
  {
    $owner = $this->getOwner();
    $owner->{$this->_leftAttribute} = 1;
    $owner->{$this->_rightAttribute} = 2;
    $owner->{$this->_levelAttribute} = 1;

    if ($this->_hasManyRoots)
    {
      $db = $owner->getConnection();

      if ($db->isUnderTransaction() === null)
      {
        $transaction = $db->beginTransaction();
      }

      try
      {
        $result = $owner->create($attributes);

        if (!$result)
        {
          if (isset($transaction))
          {
            $transaction->rollback();
          }
          return false;
        }

        $pk = $owner->{$this->_rootAttribute} = $owner->getPrimaryKey();
        $owner->updateByPk($pk, array($this->_rootAttribute => $pk));

        if (isset($transaction))
        {
          $transaction->commit();
        }
      }
      catch (exception $e)
      {
        $db->rollback();
        throw $e;
      }
    }
    else
    {

      if (count($owner->roots()))
      {
        throw new Phalcon\Mvc\Model\Exception('Cannot create more than one root in single root mode.');
      }

      $result = $owner->create($attributes);
      if (!$result)
      {
        return false;
      }
    }

    return true;
  }

  /**
   * @param Phalcon\Mvc\ModelInterface $target.
   * @param int $key.
   * @param int $levelUp.
   * @return boolean.
   * @throws CDbException
   * @throws CException
   * @throws Exception
   */
  private function moveNode($target, $key, $levelUp)
  {
    $owner = $this->getOwner();

    if ($owner->getIsNewRecord())
      throw new Phalcon\Mvc\Model\Exception('The node should not be new record.');

    if ($this->getIsDeletedRecord())
      throw new Phalcon\Mvc\Model\Exception('The node should not be deleted.');

    if ($target->getIsDeletedRecord())
      throw new Phalcon\Mvc\Model\Exception('The target node should not be deleted.');

    if ($owner->equals($target))
      throw new Phalcon\Mvc\Model\Exception('The target node should not be self.');

    if ($target->isDescendantOf($owner))
      throw new Phalcon\Mvc\Model\Exception('The target node should not be descendant.');

    if (!$levelUp && $target->isRoot())
      throw new Phalcon\Mvc\Model\Exception('The target node should not be root.');

    $db = $owner->getDbConnection();

    if ($db->getCurrentTransaction() === null)
      $transaction = $db->beginTransaction();

    try
    {
      $left = $owner->{$this->_leftAttribute};
      $right = $owner->{$this->_rightAttribute};
      $levelDelta = $target->{$this->_levelAttribute}-$owner->{$this->_levelAttribute} + $levelUp;

      if ($this->_hasManyRoots && $owner->{$this->_rootAttribute} !== $target->{$this->_rootAttribute})
      {
        foreach (array($this->_leftAttribute, $this->_rightAttribute) as $attribute)
        {
          $owner->updateAll(array($attribute => new CDbExpression($db->quoteColumnName($attribute) . sprintf('%+d', $right -
              $left + 1))), $db->quoteColumnName($attribute) . '>=' . $key . ' AND ' . $db->quoteColumnName($this->
            _rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount, array(CDbCriteria::PARAM_PREFIX .
              CDbCriteria::$paramCount++ => $target->{$this->_rootAttribute}));
        }

        $delta = $key - $left;

        $owner->updateAll(array(
          $this->_leftAttribute => new CDbExpression($db->quoteColumnName($this->_leftAttribute) . sprintf('%+d', $delta)),
          $this->_rightAttribute => new CDbExpression($db->quoteColumnName($this->_rightAttribute) . sprintf('%+d', $delta)),
          $this->_levelAttribute => new CDbExpression($db->quoteColumnName($this->_levelAttribute) . sprintf('%+d', $levelDelta)),
          $this->_rootAttribute => $target->{$this->_rootAttribute},
          ), $db->quoteColumnName($this->_leftAttribute) . '>=' . $left . ' AND ' . $db->quoteColumnName($this->
          _rightAttribute) . '<=' . $right . ' AND ' . $db->quoteColumnName($this->_rootAttribute) . '=' . CDbCriteria::
          PARAM_PREFIX . CDbCriteria::$paramCount, array(CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++ => $owner->{
            $this->_rootAttribute}));

        $this->shiftLeftRight($right + 1, $left - $right - 1);

        if (isset($transaction))
          $transaction->commit();

        $this->correctCachedOnMoveBetweenTrees($key, $levelDelta, $target->{$this->_rootAttribute});
      }
      else
      {
        $delta = $right - $left + 1;
        $this->shiftLeftRight($key, $delta);

        if ($left >= $key)
        {
          $left += $delta;
          $right += $delta;
        }

        $condition = $db->quoteColumnName($this->_leftAttribute) . '>=' . $left . ' AND ' . $db->quoteColumnName($this->
          _rightAttribute) . '<=' . $right;
        $params = array();

        if ($this->_hasManyRoots)
        {
          $condition .= ' AND ' . $db->quoteColumnName($this->_rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::
            $paramCount;
          $params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $owner->{$this->_rootAttribute};
        }

        $owner->updateAll(array($this->_levelAttribute => new CDbExpression($db->quoteColumnName($this->_levelAttribute) .
            sprintf('%+d', $levelDelta))), $condition, $params);

        foreach (array($this->_leftAttribute, $this->_rightAttribute) as $attribute)
        {
          $condition = $db->quoteColumnName($attribute) . '>=' . $left . ' AND ' . $db->quoteColumnName($attribute) . '<=' .
            $right;
          $params = array();

          if ($this->_hasManyRoots)
          {
            $condition .= ' AND ' . $db->quoteColumnName($this->_rootAttribute) . '=' . CDbCriteria::PARAM_PREFIX . CDbCriteria::
              $paramCount;
            $params[CDbCriteria::PARAM_PREFIX . CDbCriteria::$paramCount++] = $owner->{$this->_rootAttribute};
          }

          $owner->updateAll(array($attribute => new CDbExpression($db->quoteColumnName($attribute) . sprintf('%+d', $key - $left))),
            $condition, $params);
        }

        $this->shiftLeftRight($right + 1, -$delta);

        if (isset($transaction))
          $transaction->commit();

        $this->correctCachedOnMoveNode($key, $levelDelta);
      }
    }
    catch (exception $e)
    {
      if (isset($transaction))
        $transaction->rollback();

      throw $e;
    }

    return true;
  }

  /**
   * Correct cache for {@link NestedSetBehavior::delete()} and {@link NestedSetBehavior::deleteNode()}.
   */
  private function correctCachedOnDelete()
  {
    $owner = $this->getOwner();
    $left = $owner->{$this->_leftAttribute};
    $right = $owner->{$this->_rightAttribute};
    $key = $right + 1;
    $delta = $left - $right - 1;

    foreach (self::$_cached[get_class($owner)] as $node)
    {
      if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
      {
        continue;
      }

      if ($this->_hasManyRoots && $owner->{$this->_rootAttribute} !== $node->{$this->_rootAttribute})
      {
        continue;
      }

      if ($node->{$this->_leftAttribute} >= $left && $node->{$this->_rightAttribute} <= $right)
      {
        $node->setIsDeletedRecord(true);
      }
      else
      {
        if ($node->{$this->_leftAttribute} >= $key)
        {
          $node->{$this->_leftAttribute} += $delta;
        }
        if ($node->{$this->_rightAttribute} >= $key)
        {
          $node->{$this->_rightAttribute} += $delta;
        }
      }
    }
  }

  /**
   * Correct cache for {@link NestedSetBehavior::addNode()}.
   * @param int $key.
   */
  private function correctCachedOnAddNode($key)
  {
    $owner = $this->getOwner();

    if (!isset(self::$_cached[get_class($owner)]))
    {
      return;
    }

    foreach (self::$_cached[get_class($owner)] as $node)
    {
      if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
      {
        continue;
      }

      if ($this->_hasManyRoots && $owner->{$this->_rootAttribute} !== $node->{$this->_rootAttribute})
      {
        continue;
      }

      if ($owner === $node)
      {
        continue;
      }

      if ($node->{$this->_leftAttribute} >= $key)
        $node->{$this->_leftAttribute} += 2;

      if ($node->{$this->_rightAttribute} >= $key)
        $node->{$this->_rightAttribute} += 2;
    }
  }

  /**
   * Correct cache for {@link NestedSetBehavior::moveNode()}.
   * @param int $key.
   * @param int $levelDelta.
   */
  private function correctCachedOnMoveNode($key, $levelDelta)
  {
    $owner = $this->getOwner();
    $left = $owner->{$this->_leftAttribute};
    $right = $owner->{$this->_rightAttribute};
    $delta = $right - $left + 1;

    if ($left >= $key)
    {
      $left += $delta;
      $right += $delta;
    }

    $delta2 = $key - $left;

    foreach (self::$_cached[get_class($owner)] as $node)
    {

      if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
      {
        continue;
      }

      if ($this->_hasManyRoots && $owner->{$this->_rootAttribute} !== $node->{$this->_rootAttribute})
      {
        continue;
      }

      if ($node->{$this->_leftAttribute} >= $key)
      {
        $node->{$this->_leftAttribute} += $delta;
      }

      if ($node->{$this->_rightAttribute} >= $key)
      {
        $node->{$this->_rightAttribute} += $delta;
      }

      if ($node->{$this->_leftAttribute} >= $left && $node->{$this->_rightAttribute} <= $right)
      {
        $node->{$this->_levelAttribute} += $levelDelta;
      }

      if ($node->{$this->_leftAttribute} >= $left && $node->{$this->_leftAttribute} <= $right)
      {
        $node->{$this->_leftAttribute} += $delta2;
      }

      if ($node->{$this->_rightAttribute} >= $left && $node->{$this->_rightAttribute} <= $right)
      {
        $node->{$this->_rightAttribute} += $delta2;
      }

      if ($node->{$this->_leftAttribute} >= $right + 1)
      {
        $node->{$this->_leftAttribute} -= $delta;
      }

      if ($node->{$this->_rightAttribute} >= $right + 1)
      {
        $node->{$this->_rightAttribute} -= $delta;
      }
    }
  }

  /**
   * Correct cache for {@link NestedSetBehavior::moveNode()}.
   * @param int $key.
   * @param int $levelDelta.
   * @param int $root.
   */
  private function correctCachedOnMoveBetweenTrees($key, $levelDelta, $root)
  {
    $owner = $this->getOwner();
    $left = $owner->{$this->_leftAttribute};
    $right = $owner->{$this->_rightAttribute};
    $delta = $right - $left + 1;
    $delta2 = $key - $left;
    $delta3 = $left - $right - 1;

    foreach (self::$_cached[get_class($owner)] as $node)
    {
      if ($node->getIsNewRecord() || $node->getIsDeletedRecord())
      {
        continue;
      }

      if ($node->{$this->_rootAttribute} === $root)
      {
        if ($node->{$this->_leftAttribute} >= $key)
        {
          $node->{$this->_leftAttribute} += $delta;
        }
        if ($node->{$this->_rightAttribute} >= $key)
        {
          $node->{$this->_rightAttribute} += $delta;
        }
      }
      else
      {
        if ($node->{$this->_rootAttribute} === $owner->{$this->_rootAttribute})
        {
          if ($node->{$this->_leftAttribute} >= $left && $node->{$this->_rightAttribute} <= $right)
          {
            $node->{$this->_leftAttribute} += $delta2;
            $node->{$this->_rightAttribute} += $delta2;
            $node->{$this->_levelAttribute} += $levelDelta;
            $node->{$this->_rootAttribute} = $root;
          }
          else
          {

            if ($node->{$this->_leftAttribute} >= $right + 1)
            {
              $node->{$this->_leftAttribute} += $delta3;
            }

            if ($node->{$this->_rightAttribute} >= $right + 1)
            {
              $node->{$this->_rightAttribute} += $delta3;
            }
          }
        }
      }
    }
  }

  public function getOwner()
  {
    return $this->_owner;
  }

  public function setOwner($owner)
  {
    $this->_owner = $owner;
  }

  public function getIsNewRecord()
  {
    return $this->getOwner()->getDirtyState() == \Phalcon\Mvc\Model::DIRTY_STATE_TRANSIENT;
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

  /**
   * Destructor.
   */
  public function __destruct()
  {
    unset(self::$_cached[get_class($this->getOwner())][$this->_id]);
  }
}
