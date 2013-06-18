<?php
namespace Backend\Controllers;

class FilemanagerController extends ControllerBase
{
	public function connectorAction()
	{
		$this->view->disable();
		  
			//$elFinderPath = Yii::getPathOfAlias('ext.elrte.lib.elfinder.php');
      $elFinderPath = $this->url->path('/../common/plugins/elrte/php/');

			include $elFinderPath . 'elFinderConnector.class.php';
			include $elFinderPath . 'elFinder.class.php';
			include $elFinderPath . 'elFinderVolumeDriver.class.php';
			include $elFinderPath . 'elFinderVolumeLocalFileSystem.class.php'; 
			function access($attr, $path, $data, $volume)
			{
				return strpos(basename($path), '.') === 0 // if file/folder begins with '.' (dot)
					? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
					: null; // else elFinder decide it itself
			}

			$opts = array( // 'debug' => true,
					'roots' => array(array(
						'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
						'path' => 'files', // path to files (REQUIRED)
						'URL' => '/public/files/', // URL to files (REQUIRED)
						'accessControl' => 'access' // disable and hide dot starting files (OPTIONAL)
							)));

			// run elFinder
			$connector = new \elFinderConnector(new \elFinder($opts));
			$connector->run();
			exit;
	}
}
