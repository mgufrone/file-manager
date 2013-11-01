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
	public function upload($file, $to)
	{
		$app = $this->app;
		$baseDir = $app['file']['path'];
		$result = false;
		if(file_exists($baseDir.'/'.$to))
		{
			$file->move($baseDir.'/'.$to, $file->getClientOriginalName());
			$uploaded_file = $baseDir.'/'.$to.$file->getClientOriginalName();
			$result = array_merge(pathinfo($uploaded_file),array(
				'size'=>filesize($uploaded_file),
				'type'=>filetype($uploaded_file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$uploaded_file)
			));
		}
		return $result;
	}
	public function rename($from, $to)
	{
		$app = $this->app;
		$result = false;
		if(rename($from, dirname($from).'/'.$to))
		{
			$to = dirname($from).'/'.$to;
			$result = array_merge(pathinfo($to),array(
				'size'=>filesize($to),
				'type'=>filetype($to),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
			));
		}
		return $result;
	}
	public function move($from, $to)
	{
		$app = $this->app;
		$baseDir = $app['file']['path'];
		$from = $baseDir.'/'.$from;
		$to = $baseDir.'/'.$to.($to!=''?'/':'').basename($from);
		$result = false;
		if(rename($from, $to))
		{
			$result = array_merge(pathinfo($to),array(
				'size'=>filesize($to),
				'type'=>filetype($to),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
			));
		}
		return $result;
	}
	public function delete($path, $directory=false)
	{
		$app = $this->app;
		$baseDir = $app['file']['path'];
		$file = $baseDir.'/'.$path;
		// print $file;
		return file_exists($file)?($directory?rmdir($file):unlink($file)):false;
	}
	public function copy($from, $to)
	{
		$app = $this->app;
		$result = false;
		if(copy($from, $to))
			$result = array_merge(pathinfo($to),array(
				'size'=>filesize($to),
				'type'=>filetype($to),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$to)
			));
		return $result;
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