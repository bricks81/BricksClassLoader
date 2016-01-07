<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use BricksClassLoaderTest\Bootstrap;
use Bricks\ClassLoader\ClassLoader\DefaultClassLoader;

class TestObject extends DefaultClassLoader{};
class TestObject2 extends DefaultClassLoader{};
class TestObject3 extends DefaultClassLoader{};

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function getClassLoaderService($configService=null,$eventManager=null){
		$configService = $configService?:Bootstrap::getServiceManager()->get('BricksConfig');
		$eventManager = $eventManager?:Bootstrap::getServiceManager()->get('EventManager');
		$config = $configService->getConfig();
		$classLoaderServiceClass = $config->get('BricksClassLoader.aliasMap.classLoaderService');
		$classLoaderService = new $classLoaderServiceClass();		
		$classLoaderService->setConfigService($configService);
		$classLoaderService->setEventManager($eventManager);
		return $classLoaderService;
	}
	
	public function testGetInstance(){
		$classLoaderService = $this->getClassLoaderService();
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoaderServiceInterface',$classLoaderService);
	}
	
	public function testAlias(){
		$classLoaderService = $this->getClassLoaderService();
	
		$classLoader = $classLoaderService->getClassLoader('BricksClassLoaderTest');
		$alias = 'BricksClassLoader.defaultClassLoader';
		$class = $classLoader->aliasToClass($alias)?:$alias;
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		$class = $classLoader->getClassOverwrite($class);
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		$alias = 'deeper.class.hierarchy';
		$class = $classLoader->aliasToClass($alias)?:$alias;		
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		$class = $classLoader->getClassOverwrite($class);
		$this->assertEquals('BricksClassLoaderTest\TestObject',$class);
		
		$classLoader = $classLoaderService->getClassLoader('BricksClassLoaderTest2');
		$alias = 'deeper.class.hierarchy';
		$class = $classLoader->aliasToClass($alias)?:$alias;
		$this->assertEquals('BricksClassLoaderTest\TestObject2',$class);
		$class = $classLoader->getClassOverwrite($class);
		$this->assertEquals('BricksClassLoaderTest\TestObject3',$class);
		
	}
	
	public function testClassLoader(){
		$classLoaderService = $this->getClassLoaderService();
		
		$classLoader = $classLoaderService->getClassLoader();
		$object = $classLoader->get('BricksClassLoader.defaultClassLoader',array('namespace' => $classLoader->getNamespace()));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader\DefaultClassLoader',$object);
		
		$classLoader = $classLoaderService->getClassLoader('BricksClassLoaderTest');
		$this->assertEquals('BricksClassLoaderTest',$classLoader->getNamespace());
		$object = $classLoader->get('BricksClassLoader.defaultClassLoader',array('namespace' => $classLoader->getNamespace()));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$classLoader = $classLoaderService->getClassLoader('BricksClassLoaderTest2');
		$this->assertEquals('BricksClassLoaderTest2',$classLoader->getNamespace());
		$object = $classLoader->get('BricksClassLoader.defaultClassLoader',array('namespace' => $classLoader->getNamespace()));
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader\DefaultClassLoader',$object);
		$object = $classLoader->get('deeper.class.hierarchy',array('namespace' => $classLoader->getNamespace()));
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject3',$object);		
		$class = $classLoader->aliasToClass('deeper.class.hierarchy');
		$this->assertEquals('BricksClassLoaderTest\TestObject2',$class);		
				
	}
	
}