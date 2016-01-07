<?php

/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 bricks-cms.org
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Bricks\ClassLoader\ClassLoader;

use Bricks\ClassLoader\ClassLoaderServiceAwareInterface;
use Bricks\ClassLoader\ClassLoaderServiceInterface;

class DefaultClassLoader implements ClassLoaderInterface, ClassLoaderServiceAwareInterface {
	
	/**
	 * @var ClassLoaderServiceInterface
	 */
	protected $classLoaderService;
	
	/**
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * @param string $namespace
	 */
	public function __construct($namespace){		
		$this->namespace = $namespace;
	}
	
	/**
	 * @param ClassLoaderServiceInterface $classLoaderService
	 */
	public function setClassLoaderService(ClassLoaderServiceInterface $classLoaderService){
		$this->classLoaderService = $classLoaderService;
	}
	
	/**
	 * @return ClassLoaderServiceInterface
	 */
	public function getClassLoaderService(){
		return $this->classLoaderService;
	}
	
	/**
	 * @return string
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoader\ClassLoaderInterface::setInstance()
	 */
	public function setInstance($classOrAlias,$object){
		$this->getClassLoaderService()->setInstance($classOrAlias,$object,$this->getNamespace());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoader\ClassLoaderInterface::getInstance()
	 */
	public function getInstance($classOrAlias){		
		return $this->getClassLoaderService()->getInstance($classOrAlias,$this->getNamespace());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoader\ClassLoaderInterface::unsetInstance()
	 */
	public function unsetInstance($classOrAlias){
		return $this->getClassLoaderService()->unsetInstance($classOrAlias,$this->getNamespace());
	}
	
	/**
	 * @return array
	 */
	public function getAliasMap(){		
		return $this->getClassLoaderService()->getAliasMap($this->getNamespace());
	}
	
	/**
	 * @return array
	 */
	public function getClassMap(){		
		return $this->getClassLoaderService()->getClassMap($this->getNamespace());
	}
	
	/**
	 * @param string $alias
	 * @return string|null
	 */
	public function aliasToClass($alias){		
		return $this->getClassLoaderService()->aliasToClass($alias,$this->getNamespace());
	}
	
	/**
	 * @param string $class
	 * @return string
	 */
	public function getClassOverwrite($class){		
		return $this->getClassLoaderService()->getClassOverwrite($class,$this->getNamespace());
	}
	
	/**
	 * @param string $classOrAlias
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias){		
		return $this->getClassLoaderService()->getInstantiator($classOrAlias,$this->getNamespace());
	}
	
	/**
	 * @param string $classOrAlias
	 * @return array
	 */
	public function getFactories($classOrAlias){		
		return $this->getClassLoaderService()->getFactories($classOrAlias,$this->getNamespace());
	}
	
	/**
	 * @param array $factories
	 */
	public function sortFactories(array &$factories){
		return $this->getClassLoaderService()->sortFactories($factories);
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function instantiate($classOrAlias,array $params=array()){		
		return $this->getClassLoaderService()->instantiate($classOrAlias,$params,$this->getNamespace());
	}
	
	/**
	 * @param object $object
	 * @param string $classOrAlias
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array()){		
		return $this->getClassLoaderService()->factory($object,$classOrAlias,$params,$this->getNamespace());
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function singleton($classOrAlias,array $params=array()){		
		return $this->getClassLoaderService()->singleton($classOrAlias,$params,$this->getNamespace());
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function get($classOrAlias,array $params=array()){		
		return $this->getClassLoaderService()->get($classOrAlias,$params,$this->getNamespace());
	}
	
}