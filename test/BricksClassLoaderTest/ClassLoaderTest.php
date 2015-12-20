<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use Zend\Config\Config;
use BricksClassLoaderTest\Bootstrap;

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function getInstance($config=null){
		$config = null==$config?Bootstrap::getServiceManager()->get('BricksConfig'):$config;
		$classLoaderClass = $config->get('BricksClassLoader.aliasMap.classLoaderClass','BricksClassLoader');
		$classLoader = new $classLoaderClass();
		$classLoader->setConfig($config);
		return $classLoader;
	}
	
	public function testGetInstance(){
		$classLoader = $this->getInstance();
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$classLoader);
	}
	
	public function testAlias(){
		$classLoader = $this->getInstance();
	
		$class = $classLoader->aliasToClass('BricksClassLoader.classLoaderClass');
		$this->assertEquals('Bricks\ClassLoader\ClassLoader',$class);
	
		$classLoader->getConfig()->setNamespace('BricksClassLoaderTest');
		
		$class = $classLoader->aliasToClass('BricksClassLoader.classLoaderClass');
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		
		$class = $classLoader->aliasToClass('deeper.class.hierarchy');
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		
		$classLoader->getConfig()->resetNamespace();
		$classLoader->getConfig()->setNamespace('BricksClassLoaderTest2');
		
		$class = $classLoader->aliasToClass('deeper.class.hierarchy');
		$this->assertEquals('BricksClassLoaderTest\TestObject2',$class);
		
		$classLoader->getConfig()->resetNamespace();
	}
	
	public function testClassLoader(){
		$classLoader = $this->getInstance();
		
		$class = $classLoader->getConfig()->get('BricksClassLoader.classLoaderClass');
		$object = $classLoader->get($class,array(
			'BricksConfig' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);		
		$object = $classLoader->get('Bricks\ClassLoader\ClassLoader',array(
			'BricksConfig' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);
		
		$classLoader->getConfig()->setNamespace('BricksClassLoaderTest');
		
		$object = $classLoader->get('BricksClassLoader.classLoaderClass',array(
			'BricksConfig' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		$object = $classLoader->get('deeper.class.hierarchy');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$classLoader->getConfig()->resetNamespace();
		$classLoader->getConfig()->setNamespace('BricksClassLoaderTest2');
		
		$object = $classLoader->get('BricksClassLoader.classLoaderClass',array(
			'BricksConfig' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		$object = $classLoader->get('Bricks\ClassLoader\ClassLoader',array(
			'BricksConfig' => $classLoader->getConfig()
		));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		$object = $classLoader->get('deeper.class.hierarchy');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		
		$classLoader->getConfig()->resetNamespace();
				
	}
	
	
	
}