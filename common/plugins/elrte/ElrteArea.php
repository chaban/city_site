<?php
namespace Elrte;
use Phalcon\Tag as Tag;
class ElrteArea extends \Phalcon\Mvc\User\Plugin
{
	static $initialized = false;
	public $theme = 'compant';
	public $height = 150;
	public $name = 'body';
	public $id = 'body';

	/**
	 * Initialize component
	 */
	public function __construct($options = null)
	{
		if (isset($options['theme']))
		{
			$this->theme = $options['theme'];
		}

		if (isset($options['height']))
		{
			$this->height = $options['height'];
		}

		if (isset($options['name']))
		{
			$this->name = $options['name'];
		}

		if (isset($options['id']))
		{
			$this->id = $options['id'];
		}

		if (self::$initialized === false)
		{
			self::$initialized = true;

			// Css
			$this->assets->addCss('elrte/elrte/css/elrte.min.css');
      $this->assets->addCss('elrte/elfinder/css/elfinder.min.css');
			$this->assets->addCss('elrte/elfinder/css/theme.css');
      $this->assets->addCss('elrte/elfinder/css/jquery-ui-1.8.14.custom.css');
      
			// Js
      $this->assets->addJs('elrte/elrte/js/jquery-ui-1.8.13.custom.min.js');
			$this->assets->addJs('elrte/elrte/js/elrte.min.js');
			$this->assets->addJs('elrte/elrte/js/i18n/elrte.ru.js');
			$this->assets->addJs('elrte/elfinder/js/elfinder.min.js');
			$this->assets->addJs('elrte/elfinder/js/i18n/elfinder.ru.js');
      $this->assets->addJs('elrte/helper.js');
		}
	}

	public function run()
	{
	 $this->assets->outputCss();
   $this->assets->outputJs();

		echo Tag::textArea(array(
			"$this->id",
			"name" => "$this->name",
			));
  echo '<div class="hint"><a onclick="return setupElrteEditor(\''.$this->id.'\', this, \''.$this->theme.'\', \''.$this->height.'\');">WYSIWYG</a></div>';
	}

}
