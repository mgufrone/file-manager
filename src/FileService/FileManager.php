<?php
namespace FileService;

use Silex\Application;
class FileManager
{
	public $path;
	public $allowedFiles;
	public $app;
	public function __construct(Application $app, $options=array())
	{
		$this->app = $app;
		foreach($options as $key=>$value)
			$this->$key = $value;
	}
	public function get($action='get', $options=array())
	{
		$path = $this->path;
		$app = $this->app;
		$files = scandir($path);
		$allFiles = array();
		foreach($files as $file)
		{
			if($file != '.' && $file != '..')
			{
				$to = $path.'/'.$file;
				$allFiles[] = array_merge(pathinfo($to),array(
					'size'=>filesize($to),
					'type'=>filetype($to),
					'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
				));
			}
		}
		return $allFiles;
	}
	public function upload($to)
	{

	}
	public function rename($from, $to)
	{
		$app = $this->app;
		if(rename($from, dirname($from).'/'.$to))
		{
			$to = dirname($from).'/'.$to;
			return array_merge(pathinfo($to),array(
				'size'=>filesize($to),
				'type'=>filetype($to),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
			));
		}
		else return false;
	}
	public function move($oldPath, $newPath)
	{

	}
	public function delete($path)
	{
		$app = $this->app;
		$baseDir = $app['file']['path'];
		$file = $baseDir.'/'.$path;
		// print $file;
		return file_exists($file)?unlink($file):false;
	}
	public function copy($from, $to)
	{
		$app = $this->app;
		if(copy($from, $to))
			return array_merge(pathinfo($to),array(
				'size'=>filesize($to),
				'type'=>filetype($to),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
			));
		else return false;
	}

	public function createDirectory($location, $dirname)
	{
		$app = $this->app;
		$baseDir = $app['file']['path'];
		$to = $baseDir.'/'.$dirname;
		// print $file;
		$created= !file_exists($to)?mkdir($to):false;
		return $created?array_merge(pathinfo($to),array(
			'size'=>filesize($to),
			'type'=>filetype($to),
			'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
		)):false;
	}
}