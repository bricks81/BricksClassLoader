<?php

namespace BricksClassLoaderTest;

use PHPUnit_Framework_TestCase;
use Bricks\ClassLoader\ClassLoader;
use Zend\Config\Config;

class ClassLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function getInstance($config=null){
		$config = null==$config?Bootstrap::getServiceManager()->get('BricksConfig')->getConfig('BricksClassLoader'):$config;
		$classLoaderClass = $config->get('classMap.BricksClassLoader.BricksClassLoader.classLoaderClass');
		$classLoader = new $classLoaderClass($config);
		return $classLoader;
	}
	
	public function testGetInstance(){
		$classLoader = $this->getInstance();
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$classLoader);
		$cl = $classLoader->getClassLoader('BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoaderInterface',$cl);
	}

	public function testClassLoader(){
		$classLoader = $this->getInstance();
				
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'classLoaderClass','BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'defaultClassLoader','BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultClassLoader',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'defaultInstantiator','BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultInstantiator',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'defaultFactory','BricksClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultFactory',$object);		
	}
	
	public function testLoadDefaultClasses(){
		$classLoader = $this->getInstance();
		$classLoader = $classLoader->getClassLoader('BricksClassLoader');
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'classLoaderClass');
		$this->assertInstanceOf('Bricks\ClassLoader\ClassLoader',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'defaultClassLoader');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultClassLoader',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'defaultInstantiator');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultInstantiator',$object);
		
		$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'defaultFactory');
		$this->assertInstanceOf('Bricks\ClassLoader\DefaultFactory',$object);
	}
	
}