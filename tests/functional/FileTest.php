<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileTest extends WebTestCase
{
	public function createApplication()
	{
		$app = require __DIR__.'/../../app/boot.php';
		$app['debug'] = true;
   		$app['exception_handler']->disable();
		return $app;
		// return require __DIR__.'/path/to/app.php';
	}
	public function testGetFiles()
	{
		$client = $this->createClient();
		$crawler = $client->request('GET','files.json');
		$this->assertTrue($client->getResponse()->isOk());

		$config = $this->app['file'];
		$path = $config['path'];

		$files = scandir($path);
		$allFiles = array();
		$app = $this->app;
		foreach($files as $file)
		{
			if($file != '.' && $file != '..')
			$allFiles[] = array_merge(pathinfo($path.'/'.$file),array(
				'size'=>filesize($path.'/'.$file),
				'type'=>filetype($path.'/'.$file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
			));
		}
		
		$this->assertEquals(json_encode($allFiles),$client->getResponse()->getContent());
		// print $client->getResponse()->getContent();
	}

	public function testCopyFile()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$app = $this->app;
		$client = $this->createClient();
		$file = 'test2.txt';
		$crawler = $client->request('POST','copy.json',array('from'=>$path.'/test.txt','to'=>$path.'/test2.txt'));
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(file_exists($path.'/test2.txt'));
		$this->assertEquals(json_encode(array('message'=>'Copy success','status'=>200,'result'=>array_merge(pathinfo($path.'/'.$file),array(
				'size'=>filesize($path.'/'.$file),
				'type'=>filetype($path.'/'.$file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
			)))), $client->getResponse()->getContent());
		if(file_exists($path.'/test2.txt'))
			unlink($path.'/test2.txt');
	}

	public function testRenameFile()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$app = $this->app;
		$client = $this->createClient();
		$crawler = $client->request('POST','rename.json',array('from'=>$path.'/test.txt','to'=>'test2.txt'));
		$file = 'test2.txt';
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(file_exists($path.'/test2.txt'));
		$this->assertTrue(!file_exists($path.'/test.txt'));
		$this->assertEquals(json_encode(array('message'=>'Rename success','status'=>200,'result'=>array_merge(pathinfo($path.'/'.$file),array(
				'size'=>filesize($path.'/'.$file),
				'type'=>filetype($path.'/'.$file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
			)))), $client->getResponse()->getContent());
		$file = 'test.txt';
		$crawler = $client->request('POST','rename.json',array('from'=>$path.'/test2.txt','to'=>'test.txt'));
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(!file_exists($path.'/test2.txt'));
		$this->assertTrue(file_exists($path.'/test.txt'));
		$this->assertEquals(json_encode(array('message'=>'Rename success','status'=>200,'result'=>array_merge(pathinfo($path.'/'.$file),array(
				'size'=>filesize($path.'/'.$file),
				'type'=>filetype($path.'/'.$file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
			)))), $client->getResponse()->getContent());

	}

	public function testDeleteFile()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$client = $this->createClient();
		$app = $this->app;
		$crawler = $client->request('POST','delete.json',array('file'=>'test.txt'));
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(!file_exists($path.'/test.txt'));
		$file = 'test.txt';
		// $this->assertTrue(file_exists($path.'/test.txt'));
		$this->assertEquals(json_encode(array('message'=>'Delete success','status'=>200,'result'=>array('file'=>$file))), $client->getResponse()->getContent());
		$file = fopen($path.'/test.txt','w+');
		fclose($file);
	}

	public function testMoveFile()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$app = $this->app;
		$client = $this->createClient();

		$crawler = $client->request('POST','create.json',array('location'=>'/','dirname'=>'hello'));
		$crawler = $client->request('POST','move.json',array('from'=>'test.txt','to'=>'hello'));

		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(file_exists($path.'/hello/'));
		$this->assertTrue(is_dir($path.'/hello/'));
		$this->assertTrue(file_exists($path.'/hello/test.txt'));
		$this->assertTrue(!file_exists($path.'/test.txt'));
		// $this->assertTrue(file_exists($path.'/test.txt'));
		$file = 'hello/test.txt';
		$this->assertEquals(json_encode(array('message'=>'Moving file success','status'=>200,'result'=>array_merge(pathinfo($path.'/'.$file),array(
				'size'=>filesize($path.'/'.$file),
				'type'=>filetype($path.'/'.$file),
				'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
			)))), $client->getResponse()->getContent());
		
		$client->request('POST','move.json',array('from'=>'hello/test.txt','to'=>''));
		$client->request('POST','delete.json',array('folder'=>'hello'));
	}

	public function testUpload()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$app = $this->app;
		$client = $this->createClient();
		$photo = new UploadedFile(
			dirname(__DIR__).'/assets/test.jpg',
			'test.jpg',
			'image/jpeg',
			filesize(dirname(__DIR__).'/assets/test.jpg')
		);
		$client->request(
			'POST',
			'/upload.json',
			array('folder'=>''),
			array('file' => $photo)
		);
		$file = 'test.jpg';

		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(file_exists($path.'/'.$file));
		$this->assertEquals(json_encode(array('message'=>'Upload file success','status'=>200,'result'=>array_merge(pathinfo($path.'/'.$file),array(
			'size'=>filesize($path.'/'.$file),
			'type'=>filetype($path.'/'.$file),
			'url'=>$app['url_generator']->generate('home').str_replace($app['baseDir'],'',$path.'/'.$file)
		)))), $client->getResponse()->getContent());
		rename($path.'/'.$file, dirname(__DIR__).'/assets/'.$file);
	}
	public function testNotAllowedFileUpload()
	{
		$config = $this->app['file'];
		$path = $config['path'];
		$app = $this->app;
		$client = $this->createClient();
		$photo = new UploadedFile(
			dirname(__DIR__).'/assets/test.csv',
			'test.jpg',
			'image/jpeg',
			filesize(dirname(__DIR__).'/assets/test.csv')
		);
		$client->request(
			'POST',
			'/upload.json',
			array('folder'=>''),
			array('file' => $photo)
		);
		$file = 'test.csv';

		$this->assertTrue($client->getResponse()->isOk());
		$this->assertTrue(!file_exists($path.'/'.$file));
		$this->assertEquals(json_encode(array('message'=>'File you upload is not allowed','status'=>500)),$client->getResponse()->getContent());
	}
}