<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use Zend\Config\Config;
use BricksClassLoaderTest\Bootstrap;

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function getInstance($config=null){
		$config = null==$config?Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksClassLoader'):$config;
		$classLoaderClass = $config->get('classMap.classLoaderClass','BricksClassLoader');
		$classLoader = new $classLoaderClass($config);
		return $classLoader;
	}
	
	public function testGetInstance(){
		$classLoader = $this->getInstance();
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$classLoader);
	}
	
	public function testClassLoader(){
		$classLoader = $this->getInstance();	
		$object = $classLoader->get('Bricks\ClassLoader\ClassLoader','BricksClassLoader',array(
			'Config' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);		
		
		$object = $classLoader->get('Bricks\ClassLoader\DefaultInstantiator','BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultInstantiator',$object);
		
		$object = $classLoader->newInstance('Bricks\ClassLoader\DefaultFactory','BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultFactory',$object);		
	}
	
	public function testAlias(){
		$classLoader = $this->getInstance();
	}
	
}