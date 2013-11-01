<?php
use Silex\WebTestCase;

class DirectoryTest extends WebTestCase
{

	public function createApplication()
	{
		$app = require __DIR__.'/../../app/boot.php';
		$app['debug'] = true;
    $app['exception_handler']->disable();
		return $app;
		// return require __DIR__.'/path/to/app.php';
	}
	public function testCreateDirectory()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$app = $this->app;
		$client = $this->createClient();
		$crawler = $client->request('POST','create.json',array('location'=>'/','dirname'=>'hello'));
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(file_exists($path.'/hello/'));
		$this->assertTrue(is_dir($path.'/hello/'));
		// $this->assertTrue(file_exists($path.'/test.txt'));
		$file = 'hello';
		$this->assertEquals(json_encode(array('message'=>'Create directory success','status'=>200,'result'=>array_merge(pathinfo($path.'/'.$file),array(
				'size'=>filesize($path.'/'.$file),
				'type'=>filetype($path.'/'.$file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
			)))), $client->getResponse()->getContent());
		if(file_exists($path.'/'.$file))
			rmdir($path.'/'.$file);
	}

	public function testDeleteDirectory()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$client = $this->createClient();
		$app = $this->app;
		$crawler = $client->request('POST','create.json',array('location'=>'/','dirname'=>'hello'));
		$crawler = $client->request('POST','delete.json',array('folder'=>'hello'));
		$this->assertTrue($client->getResponse()->isOk());
		// $this->assertTrue(!file_exists($path.'/test.txt'));
		$file = 'hello';
		// $this->assertTrue(file_exists($path.'/test.txt'));
		$this->assertEquals(json_encode(array('message'=>'Delete success','status'=>200,'result'=>array('folder'=>$file))), $client->getResponse()->getContent());
		
	}
}