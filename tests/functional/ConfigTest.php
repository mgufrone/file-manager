<?php

use Silex\WebTestCase;
use Symfony\Component\Yaml\Parser;


class ConfigTest extends WebTestCase
{
	public function createApplication()
	{
		$app = require __DIR__.'/../../app/boot.php';
		$app['debug'] = true;
		return $app;
		// return require __DIR__.'/path/to/app.php';
	}
	public function testConfig()
	{
		$configFile = __DIR__.'/../../app/config/config.yml';
		$this->assertTrue(file_exists($configFile));
		$yaml = new Parser;
		$config = $yaml->parse(file_get_contents($configFile));
		$this->assertTrue(is_array($config));

		$this->assertTrue(isset($config['file']['path']));
	}
}