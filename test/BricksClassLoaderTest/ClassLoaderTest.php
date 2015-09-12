<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use Zend\Config\Config;
use BricksClassLoaderTest\Bootstrap;

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function getInstance($config=null){
		$config = null==$config?Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksClassLoader'):$config;
		$classLoaderClass = $config->get('aliasMap.classLoaderClass','BricksClassLoader');
		$classLoader = new $classLoaderClass($config);
		return $classLoader;
	}
	
	public function testGetInstance(){
		$classLoader = $this->getInstance();
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$classLoader);
	}
	
	public function testAlias(){
		$classLoader = $this->getInstance();
	
		$class = $classLoader->aliasToClass('classLoaderClass','BricksClassLoader');
		$this->assertEquals('Bricks\ClassLoader\ClassLoader',$class);
	
		$class = $classLoader->aliasToClass('classLoaderClass','BricksClassLoaderTest');
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
	
		$class = $classLoader->aliasToClass('classLoaderClass','BricksClassLoaderTest2');
		$this->assertEquals('BricksClassLoaderTest\TestObject2',$class);
		
		$class = $classLoader->aliasToClass('depper.class.hierarchy','BricksClassLoader');
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		
		$class = $classLoader->aliasToClass('depper.class.hierarchy','BricksClassLoaderTest');
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		
		$class = $classLoader->aliasToClass('depper.class.hierarchy','BricksClassLoaderTest2');
		$this->assertEquals('BricksClassLoaderTest\TestObject2',$class);
	}
	
	public function testClassLoader(){
		$classLoader = $this->getInstance();	
		
		$object = $classLoader->get('classLoaderClass','BricksClassLoader',array(
			'Config' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);		
		
		$object = $classLoader->get('classLoaderClass','BricksClassLoaderTest',array(
			'Config' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject');
		
		$object = $classLoader->get('classLoaderClass','BricksClassLoaderTest2',array(
			'Config' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2');
		
		$object = $classLoader->get('Bricks\ClassLoader\ClassLoader','BricksClassLoader',array(
			'Config' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);
		
		$object = $classLoader->get('Bricks\ClassLoader\ClassLoader','BricksClassLoaderTest2',array(
			'Config' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		
		$object = $classLoader->get('deeper.class.hierarchy','BricksClassLoader');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $classLoader->get('deeper.class.hierarchy','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $classLoader->get('deeper.class.hierarchy','BricksClassLoaderTest2');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
				
	}
	
	
	
}