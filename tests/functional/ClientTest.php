<?php

use Silex\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ClientTest extends WebTestCase
{
	public function createApplication()
	{
		$app = require __DIR__.'/../../app/boot.php';
		$app['debug'] = true;
   		$app['exception_handler']->disable();
		return $app;
		// return require __DIR__.'/path/to/app.php';
	}
	public function testIndex()
	{
		$client = $this->createClient();
		$crawler = $client->request('GET', '/');
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertCount(1, $crawler->filter('h1:contains("File Manager")'));	
	}
}