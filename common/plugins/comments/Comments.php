<?php
namespace Comments;

class Comments extends \Phalcon\Mvc\User\Plugin
{

	public $parents = array();
	public $children = array();
	public $comments;
	public $model;

	/**
	 * @param array $comments
	 */

	public function __construct()
	{
		if (isset($options['comments']))
		{
			$this->comments = $options['comments'];
		}
		foreach ($this->comments as $comment)
		{
			if ($comment->parent_comment_id === null)
			{
				$this->parents[$comment->id][] = $comment;
			} else
			{
				$this->children[$comment->parent_comment_id][] = $comment;
			}
		}
	}

	public function run()
	{
		foreach ($this->parents as $c)
		{
			$this->print_parent($c);
		}
	}

	/**
	 * @param array $comment
	 * @param int $depth 
	 */
	private function format_comment($comment, $depth)
	{
		$this->render('tComment', array(
			'comment' => $comment,
			'depth' => $depth,
			'model' => $this->model));
	}

	/**
	 * @param array $comment
	 * @param int $depth
	 */
	private function print_parent($comment, $depth = 0)
	{
		foreach ($comment as $c)
		{
			$this->format_comment($c, $depth);

			if (isset($this->children[$c['id']]))
			{
				$this->print_parent($this->children[$c['id']], $depth + 1);
			}
		}
	}

}
