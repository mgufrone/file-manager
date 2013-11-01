<?php
/**
* Simple File Manager for your webapps
* @author Mochamad Gufron
* @email mgufronefendi@gmail.com
*/

require_once __DIR__.'/../vendor/autoload.php';

// Initiating needed dependencies
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

// registering needed service and providers
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

// Routing

/**
* @method GET
* @route /
* show index or landing page
*/
$app->get('/',function() use($app){
	// return $app['twig']->render();
})->bind('home');

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
	$deleteDirectory = false;
	if(empty($file))
	{
		$file = $req->get('folder');
		$deleteDirectory = true;
	}
	$result = $app['filemanager']->delete($file, $deleteDirectory);
	if($result)
		$data = array('message'=>'Delete success','status'=>200, 'result'=>$deleteDirectory?array('folder'=>$file):array('file'=>$file));
	else
		$data = array('message'=>'Delete failed','status'=>500);

	return $app->json($data);
})->bind('delete');

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

$app->post('move.json',function(Request $req) use($app){
	$from = $req->get('from');
	$to = $req->get('to');

	$result = $app['filemanager']->move($from, $to);
	if($result)
		$data = array('message'=>'Moving file success','status'=>200, 'result'=>$result);
	else
		$data = array('message'=>'Moving file error','status'=>500);
	return $app->json($data);
});


$app->post('upload.json',function(Request $req) use($app){
	$file = $req->files->get('file');
	$folder = $req->get('folder');
	$result = $app['filemanager']->upload($file, $folder);
	if($result)
		$data = array('message'=>'Upload file success','status'=>200, 'result'=>$result);
	else
		$data = array('message'=>'Upload file error','status'=>500);
	return $app->json($data);
});
return $app;