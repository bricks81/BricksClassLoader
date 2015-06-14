<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use Zend\Config\Config;

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function testGetInstance(){
		$cfg = new Config(Bootstrap::getServiceManager()->get('Config')['BricksClassLoader'],true);
		$config = Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksClassLoader');
		$classLoader = new ClassLoader($config,$cfg);
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$classLoader);
		$cl = $classLoader->getClassLoader('BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoaderInterface',$cl);
	}	

	public function testLoadingClasses(){
		$cfg = new Config(Bootstrap::getServiceManager()->get('Config')['BricksClassLoader'],true);
		$config = Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksClassLoader');
		$classLoader = new ClassLoader($config,$cfg);
		$cl = $classLoader->getClassLoader('BricksTest');
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'anyClass','BricksTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'anyClass','BricksTest','BricksTestExtended');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'anyClass');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'anyClass','BricksTestExtended');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended',$object);
		
		$object = $cl->getSingleton(__CLASS__,__FUNCTION__,'anyClass','BricksTestExtended');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended',$object);
		
		$object = $cl->getSingleton(__CLASS__,__FUNCTION__,'anyClass');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended',$object);
		
	}
	
}