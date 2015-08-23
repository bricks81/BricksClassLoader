<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use Zend\Config\Config;

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function getInstance($config=null){
		$config = null==$config?Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksClassLoader'):$config;
		$classLoaderClass = $config->get('classLoaderClass');
		$classLoader = new $classLoaderClass($config);
		return $classLoader;
	}
	
	public function testGetInstance(){
		$classLoader = $this->getInstance();
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$classLoader);
		$cl = $classLoader->getClassLoader('BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoaderInterface',$cl);
	}

	public function testLoadingClasses(){
		$classLoader = $this->getInstance();
		$cl = $classLoader->getClassLoader('BricksClassLoader');
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'anyClass','BricksClassLoader');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'anyClass','BricksClassLoader','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'anyClassExtended','BricksClassLoader');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'anyClassExtended','BricksClassLoader','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended2',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'deeper.class.hierarchy','BricksClassLoader');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'deeper.class.hierarchy','BricksClassLoader','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		
		// cl
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'anyClass');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'anyClass','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'anyClassExtended');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'anyClassExtended','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObjectExtended2',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'deeper.class.hierarchy');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject',$object);
		
		$object = $cl->newInstance(__CLASS__,__FUNCTION__,'deeper.class.hierarchy','BricksClassLoaderTest');
		$this->assertInstanceOf('BricksClassLoaderTest\TestObject2',$object);
	}
	
}