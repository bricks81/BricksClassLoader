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

namespace Bricks\ClassLoader;

use Bricks\Config\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Config\ConfigAwareInterface;

class ClassLoader implements ServiceLocatorAwareInterface, ConfigAwareInterface {
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	/**
	 * @var ConfigInterface
	 */
	protected $config;
	
	/**
	 * @var \Bricks\ClassLoader\DefaultInstantiator
	 */
	protected $defaultInstantiator;
	
	/**
	 * @var array
	 */
	protected $instantiators = array();
	
	/**
	 * @var array
	 */
	protected $defaultFactories = array();
	
	/**
	 * @var array
	 */
	protected $factories = array();
	
	/**
	 * @var array
	 */
	protected $instances = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
	 */
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	/**
	 * @param ConfigInterface $config
	 */
	public function setConfig(ConfigInterface $config){
		$this->config = $config;
	}
	
	/**
	 * @return ConfigInterface
	 */
	public function getConfig(){
		return $this->config;		
	}
	
	public function initialize(){		
		
		$classMap = $this->getClassMap();		
		$instantiatorClass = $this->aliasToClass('BricksClassLoader.defaultInstantiator');
		$instantiatorClass = $this->getClassOverwrite($instantiatorClass);
		if(isset($classMap[$class]['instantiator'])){
			$instantiatorClass = $classMap[$class]['instantiator'];
		}
		
		$factories = array();
		$array = $this->getConfig()->get('BricksClassLoader.defaultFactories');
		foreach($array AS $key => $className){
			array_push($factories,$this->createFactory($className));			
		}
		$this->setDefaultFactories($factories);
		
	}
	
	/**
	 * @return array
	 */
	public function getAliasMap(){		
		return $this->getConfig()->get('BricksClassLoader.aliasMap');		
	}
	
	/**
	 * @return array
	 */
	public function getClassMap(){		
		return $this->getConfig()->get('BricksClassLoader.classMap');		
	}
	
	/**
	 * @param string $alias
	 * @return string|null
	 */
	public function aliasToClass($alias){		
		$aliasMap = $this->getAliasMap();
		$parts = explode('.',$alias);
		$class = null;
		$name = array_pop($parts);
		$current = &$aliasMap;				
		if(isset($aliasMap[$name])){
			$class = $aliasMap[$name];
		} else {			
			foreach($parts AS $key){				
				if(!isset($current[$key])){
					break;
				}
				$current = &$current[$key];
				if(isset($current[$name])){					
					$class = $current[$name];
					break;
				}				
			}
		}
		return $class;
	}
	
	/**
	 * @param string $class	 
	 * @return string
	 */
	public function getClassOverwrite($class){
		$return = $class;
		$classMap = $this->getClassMap();
		while(isset($classMap[$class])){
			$return = $classMap[$class];
			if(is_array($return) && isset($return['class'])){
				$return = $return['class'];
			} elseif(is_array($return) && count($return) > 0){
				$return = array_slice($ret,0,1);
			}
			unset($classMap[$class]);
			$class = $return;			
		}		
		return $return;
	}
	
	/**
	 * @param string $classOrAlias
	 * @return \Bricks\ClassLoader\InstantiatorInterface
	 */
	public function createInstantiator($classOrAlias){
		$classMap = $this->getClassMap();
		$instantiatorClass = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$instantiatorClass = $this->getClassOverwrite($instantiatorClass);
		if(isset($classMap[$class]['instantiator'])){
			$instantiatorClass = $classMap[$class]['instantiator'];
		}
		return new $instantiatorClass($this);
	}
	
	/**
	 * @param InstantiatorInterface $instantiator
	 */
	public function setDefaultInstantiator(InstantiatorInterface $instantiator){
		$this->defaultInstantiator = $instantiator;
	}
	
	/**
	 * @return InstantiatorInterface 
	 */
	public function getDefaultInstantiator(){
		return $this->defaultInstantiator;
	}
	
	/**
	 * @param InstantiatorInterface $instantiator
	 * @param string $classOrAlias	 
	 */
	public function setInstantiator(InstantiatorInterface $instantiator,$classOrAlias){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		if(!isset($this->instantiators[$class][$namespace])){
			$this->instantiators[$class][$namespace] = $instantiator;
		}
	}
	
	/**
	 * @param string $classOrAlias	 
	 */
	public function removeInstantiator($classOrAlias){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		if(isset($this->instantiators[$class][$namespace])){
			unset($this->instantiators[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias	 
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias){		
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
						
		if($class && !isset($this->instantiators[$class][$namespace])){			
			$this->instantiators[$class][$namespace] = $this->getDefaultInstantiator();			
		}
		if(isset($this->instantiators[$class][$namespace])){
			return $this->instantiators[$class][$namespace];
		}
	}
	
	/**
	 * @param string $classOrAlias
	 */
	public function createFactory($classOrAlias){
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		return new $class($this);
	}
	
	/**
	 * @param array $factories
	 */
	public function setDefaultFactories(array $factories){
		$this->defaultFactories = $factories;
	}
	
	/**
	 * @return array
	 */
	public function getDefaultFactories(){
		return $this->defaultFactories;
	}
	
	/**
	 * @param FactoryInterface $factory
	 * @param string $classOrAlias
	 */
	public function addFactory(FactoryInterface $factory,$classOrAlias){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		if(!isset($this->factories[$class][$namespace])){
			$this->factories[$class][$namespace][] = $factory;
		}
	}
	
	/**
	 * @param string $classOrAlias
	 */
	public function removeFactory($classOrAlias,$alias){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		if(isset($this->factories[$class][$namespace])){
			unset($this->factories[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @return array
	 */
	public function getFactories($classOrAlias){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);		
		if(!isset($this->factories[$class][$namespace])){
			$this->factories[$class][$namespace] = $this->getDefaultFactories();
		}
		
		if(isset($this->factories[$class][$namespace])){
			return $this->factories[$class][$namespace];
		}
	}
	
	/**
	 * @param array $factories	 
	 */
	public function sortFactories(array &$factories){
		usort($factories,function($a,$b){
			return $a->getPriority()>$b->getPriority()?$a:$b;
		});
	}
	
	/**
	 * @param string $classOrAlias	 
	 * @param array $params
	 * @return object	 
	 */
	public function instantiate($classOrAlias,array $params=array()){
		$object = null;
		$namespace = $this->getCOnfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		$instantiator = $this->getInstantiator($class);		
		return $instantiator->instantiate($class,$params);		
	}

	/**
	 * @param object $object
	 * @param string $classOrAlias	 
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array()){		
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		$factories = $this->getFactories($class);
		$this->sortFactories($factories);
		foreach($factories AS $factory){
			$factory->build($object,$params);
		}
	}
	
	/**	
	 * @param string $classOrAlias	 
	 * @param array $params
	 * @return object
	 */
	public function singleton($classOrAlias,array $params=array()){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		if(!isset($this->instances[$class][$namespace])){
			$this->instances[$class][$namespace] = $this->get($class,$params);
		}		
		return $this->instances[$class][$namespace];
	}
	
	/**
	 * 
	 * @param string $classOrAlias	 
	 */
	public function removeSingleton($classOrAlias){
		$namespace = $this->getConfig()->getCurrentNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		if(isset($this->instances[$class][$namespace])){
			unset($this->instances[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function get($classOrAlias,array $params=array()){
		if(!is_string($classOrAlias)){
			throw new \RuntimeException('invalid class or alias');
		}
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		$object = $this->instantiate($class,$params);		
		$this->factory($object,$class,$params);
		return $object;
	}
	
}
