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
use Bricks\ClassLoader\Instantiators\InstantiatorInterface;

class ClassLoader implements ServiceLocatorAwareInterface, 
	ConfigAwareInterface, ClassLoaderInterface {
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	/**
	 * @var ConfigInterface
	 */
	protected $config;
	
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
		$this->attachAliasListener();
		$this->attachClassmapListener();
		$this->attachDefaultInstantiatorListener();
		$this->attachInstantiatorListener();
		$this->attachDefaultFactoriesListener();
		$this->attachFactoriesListener();
	}
	
	protected function attachAliasListener(){
		$this->getClassLoader()->getServiceLocator()->get('EventManager')
		->attach('BricksConfig::beforeSet(BricksMapper.aliasMap)',function($e) {
			$path = $e->getParam('calledPath');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$alias = $parts[2];
				$class = $this->aliasToClass($alias)?:$alias;
				$class = $this->getClassOverwrite($class);
				if(isset($this->instances[$class])){
					unset($this->instances[$class]);
				}
			}			
		});
	}
	
	protected function attachClassmapListener(){
		$this->getClassLoader()->getServiceLocator()->get('EventManager')
		->attach('BricksConfig::beforeSet(BricksMapper.classMap)',function($e) {
			$path = $e->getParam('calledPath');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];
				$class = $this->getClassOverwrite($class);
				if(isset($this->instances[$class])){
					unset($this->instances[$class]);
				}
			}
		});
	}
	
	protected function attachDefaultInstantiatorListener(){
		$this->getClassLoader()->getServiceLocator()->get('EventManager')
		->attach('BricksConfig::beforeSet(BricksMapper.defaultInstantiator)',function($e) {
			$path = $e->getParam('calledPath');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];
				$class = $this->getClassOverwrite($class);
				if(isset($this->instances[$class])){
					unset($this->instances[$class]);
				}
			}
		});
	}
	
	protected function attachInstantiatorListener(){
		$this->getClassLoader()->getServiceLocator()->get('EventManager')
		->attach('BricksConfig::beforeSet(BricksMapper.instantiator)',function($e) {
			$path = $e->getParam('calledPath');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];				
				$class = $this->getClassOverwrite($class);
				if(isset($this->instances[$class])){
					unset($this->instances[$class]);
				}
			}
		});
	}
	
	protected function attachDefaultFactoriesListener(){
		$this->getClassLoader()->getServiceLocator()->get('EventManager')
		->attach('BricksConfig::beforeSet(BricksMapper.defaultFactories)',function() {
			$factories = $this->getConfig()->get('BricksMapper.defaultFactories');
			foreach($factories AS $factory){
				unset($this->instances[$factory]);
			}
		});		
	}
	
	protected function attachFactoriesListener(){
		$this->getClassLoader()->getServiceLocator()->get('EventManager')
		->attach('BricksConfig::beforeSet(BricksMapper.factories)',function($e) {
			$path = $e->getParam('calledPath');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];
				$class = $this->getClassOverwrite($class);
				if(isset($this->instances[$class])){
					unset($this->instances[$class]);
				}
			}
		});
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
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias){
		$namespace = $this->getConfig()->getNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		$instantiator = $this->getConfig()->get('BricksClassLoader.instantiator.'.$class);
		if($instantiator){
			if(!isset($this->instances[$class][$namespace])){
				$this->instances[$class][$namespace] = new $instantiator($this);
			}
		} else {
			$defaultInstantiator = $this->getConfig()->get('BricksClassLoader.defaultInstantiator');
			if(!isset($this->instances[$class][$namespace])){
				$this->instances[$class][$namespace] = new $defaultInstantiator($this);
			}
		}
		return $this->instances[$class][$namespace];
	}
	
	/**
	 * @param string $classOrAlias
	 * @return array
	 */
	public function getFactories($classOrAlias){
		$namespace = $this->getConfig()->getNamespace();
		$class = $this->aliasToClass($classOrAlias)?:$classOrAlias;
		$class = $this->getClassOverwrite($class);
		$defaultFactories = $this->getConfig()->get('BricksClassLoader.defaultFactories');
		$factories = $this->getConfig()->get('BricksClassLoader.factories.'.$class);
		$return = array();
		foreach($defaultFactories AS $factory){
			if(!isset($this->instances[$factory][$namespace])){
				$this->instances[$factory][$namespace] = new $factory($this);
			}
			$return[] = $this->instances[$factory][$namespace];
		}
		if($factories){
			foreach($factories AS $factory){
				if(!isset($this->instances[$factory][$namespace])){
					$this->instances[$factory][$namespace] = new $factory($this);
				}
				$return[] = $this->instances[$factory][$namespace];
			}
		}
		return $return;
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
		$namespace = $this->getConfig()->getNamespace();
		$instantiator = $this->getInstantiator($classOrAlias);		
		return $instantiator->instantiate($class,$params);		
	}

	/**
	 * @param object $object
	 * @param string $classOrAlias	 
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array()){		
		$factories = $this->getFactories($classOrAlias);
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
		$namespace = $this->getConfig()->getNamespace();
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
	 * @param string $namespace	 
	 */
	public function removeSingleton($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getConfig()->getNamespace();
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
		$object = $this->instantiate($classOrAlias,$params);		
		$this->factory($object,$classOrAlias,$params);
		return $object;
	}
	
}
