<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Yaml\Parser;
use FileService\FileManagerService;
$app = new Silex\Application();
Request::enableHttpMethodParameterOverride();
$yaml = new Parser;
$config = $yaml->parse(file_get_contents(__DIR__.'/config/config.yml'));
$config['file']['path'] = dirname(__DIR__).$config['file']['path'];
$app['baseDir'] = dirname(__DIR__).'/';
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new FileManagerService(), $config);
$app->get('/',function() use($app){
	return $app['twig']->render();
})->bind('home');
// print $app['file.path'];

$app->get('files.json',function() use($app){
	$request = $app['request'];
	$data = $app['filemanager']->get();
	return $app->json($data);
})->bind('list-files');

$app->post('copy.json',function(Request $req) use($app){
	$from = $req->get('from');
	$to = $req->get('to');

	$result = $app['filemanager']->copy($from, $to);
	if($result)
		$data = array('message'=>'Copy success','status'=>200, 'result'=>$result);
	else
		$data = array('message'=>'Copy error','status'=>500);
	return $app->json($data);
})->bind('copy-file');


$app->post('rename.json',function(Request $req) use($app){
	$from = $req->get('from');
	$to = $req->get('to');

	$result = $app['filemanager']->rename($from, $to);
	if($result)
		$data = array('message'=>'Rename success','status'=>200, 'result'=>$result);
	else
		$data = array('message'=>'Rename error','status'=>500);
	return $app->json($data);
})->bind('rename-file');

$app->post('delete.json',function(Request $req) use($app){
	$file = $req->get('file');

	$result = $app['filemanager']->delete($file);
	if($result)
		$data = array('message'=>'Delete success','status'=>200, 'result'=>array('file'=>$file));
	else
		$data = array('message'=>'Delete failed','status'=>500);

	return $app->json($data);
})->bind('delete-file');

$app->post('create.json',function(Request $req) use($app){
	$location = $req->get('location');
	$dirname = $req->get('dirname');

	$result = $app['filemanager']->createDirectory($location, $dirname);
	if($result)
		$data = array('message'=>'Create directory success','status'=>200, 'result'=>$result);
	else
		$data = array('message'=>'Create directory failed','status'=>500);
	return $app->json($data);
})->bind('create-directory');

return $app;