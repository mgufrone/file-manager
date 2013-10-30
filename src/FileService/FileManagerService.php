<?php
namespace FileService;

use Silex\Application;
use Silex\ServiceProviderInterface;

class FileManagerService implements ServiceProviderInterface
{
	public function boot(Application $app)
	{

	}
	public function register(Application $app)
	{
		$app['file.path'] = '';
		$app['filemanager'] = $app->share(function($app){
			// print $app['file.path'];
			$class = new FileManager($app, $app['file']);
			return $class;
		});

	}
}