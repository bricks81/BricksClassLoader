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
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Bricks\ClassLoader\ClassLoader\ClassLoaderInterface;

class ClassLoaderService implements ServiceLocatorAwareInterface, 
	ConfigAwareInterface, EventManagerAwareInterface, ClassLoaderServiceInterface {
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	/**
	 * @var EventManagerInterface
	 */
	protected $eventManager;
	
	/**
	 * @var array
	 */
	protected $listeners = array();
	
	/**
	 * @var ConfigInterface
	 */
	protected $config;
	
	/**
	 * @var array
	 */
	protected $classLoaders = array();
	
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
	 * @param EventManagerInterface $eventManager
	 */
	public function setEventManager(EventManagerInterface $eventManager){
		$identifiers = $eventManager->getIdentifiers();
		if(false === array_search('BricksClassLoader',$identifiers)){
			$identifiers[] = 'BricksClassLoader';
		}
		$eventManager->setIdentifiers($identifiers);
		$this->eventManager = $eventManager;
		
		$hash = spl_object_hash($eventManager);
		if(!isset($this->listeners[$hash])){
			$this->attachAliasListener();
			$this->attachClassmapListener();
			$this->attachDefaultInstantiatorListener();
			$this->attachInstantiatorListener();
			$this->attachDefaultFactoriesListener();
			$this->attachFactoriesListener();
			$this->listeners[$hash] = true;
		}
	}
	
	/**
	 * @return EventManagerInterface
	 */
	public function getEventManager(){
		return $this->eventManager;
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
	
	protected function attachAliasListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','afterSet',function($e) {
			if(0 === strpos('BricksClassLoader.aliasMap',$e->getParam('path'))){
				$path = $e->getParam('path');
				$parts = explode('.',$path);
				if(isset($parts[2])){
					$alias = $parts[2];
					$class = $this->aliasToClass($alias)?:$alias;
					$class = $this->getClassOverwrite($class);
					if(isset($this->instances[$class])){
						unset($this->instances[$class]);
					}
				}			
			}
		});
	}
	
	protected function attachClassmapListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','afterSet',function($e) {
			if(0 === strpos('BricksClassLoader.classMap',$e->getParam('path'))){
				$path = $e->getParam('path');
				$parts = explode('.',$path);
				if(isset($parts[2])){
					$class = $parts[2];
					$class = $this->getClassOverwrite($class);
					if(isset($this->instances[$class])){
						unset($this->instances[$class]);
					}
				}
			}
		});
	}
	
	protected function attachDefaultInstantiatorListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','afterSet',function($e) {
			if(0 === strpos('BricksClassLoader.defaultInstantiator',$e->getParam('path'))){
				$path = $e->getParam('path');
				$parts = explode('.',$path);
				if(isset($parts[2])){
					$class = $parts[2];
					$class = $this->getClassOverwrite($class);
					if(isset($this->instances[$class])){
						unset($this->instances[$class]);
					}
				}
			}
		});
	}
	
	protected function attachInstantiatorListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','beforeSet',function($e) {
			if(0 === strpos('BricksClassLoader.instantiator',$e->getParam('path'))){
				$path = $e->getParam('path');
				$parts = explode('.',$path);
				if(isset($parts[2])){
					$class = $parts[2];				
					$class = $this->getClassOverwrite($class);
					if(isset($this->instances[$class])){
						unset($this->instances[$class]);
					}
				}
			}
		});
	}
	
	protected function attachDefaultFactoriesListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','beforeSet',function() {
			if(0 === strpos('BricksClassLoader.defaultFactories',$e->getParam('path'))){
				$factories = $this->getConfig()->get('BricksClassLoader.defaultFactories');
				foreach($factories AS $factory){
					unset($this->instances[$factory]);
				}
			}
		});		
	}
	
	protected function attachFactoriesListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','beforeSet',function($e) {
			if(0 === strpos('BricksClassLoader.factories',$e->getParam('path'))){
				$path = $e->getParam('path');
				$parts = explode('.',$path);
				if(isset($parts[2])){
					$class = $parts[2];
					$class = $this->getClassOverwrite($class);
					if(isset($this->instances[$class])){
						unset($this->instances[$class]);
					}
				}
			}
		});
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoaderServiceInterface::getClassLoader()
	 */
	public function getClassLoader($moduleName){
		$object = $this->get('BricksClassLoader.defaultClassLoader');
		if($object instanceof ClassLoaderInterface){
			$object->setNamespace($this->getConfig()->getNamespace());
			$object->setModule($moduleName);
		}
		$this->setClassLoader($object);
		return $object;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoaderServiceInterface::setClassLoader()
	 */
	public function setClassLoader(ClassLoaderInterface $classLoader){
		$module = $classLoader->getModule();
		$this->classLoaders[$module] = $classLoader;
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getAliasMap($namespace=null){
		$namespace = $namespace?:$this->getConfig()->getNamespace();
		return $this->getConfig()->get('BricksClassLoader.aliasMap',$namespace);		
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getClassMap($namespace=null){		
		$namespace = $namespace?:$this->getConfig()->getNamespace();
		return $this->getConfig()->get('BricksClassLoader.classMap',$namespace);		
	}
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @return string|null
	 */
	public function aliasToClass($alias,$namespace=null){		
		$aliasMap = $this->getAliasMap($namespace);
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
	 * @param string $namespace	 
	 * @return string
	 */
	public function getClassOverwrite($class,$namespace=null){
		$return = $class;
		$classMap = $this->getClassMap($namespace);
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
	 * @param string $namespace
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getConfig()->getNamespace();
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		$instantiator = $this->getConfig()->get('BricksClassLoader.instantiator.'.$class,$namespace);
		if($instantiator){
			if(!isset($this->instances[$class][$namespace])){
				$this->instances[$class][$namespace] = new $instantiator($this);
			}
		} else {
			$defaultInstantiator = $this->getConfig()->get('BricksClassLoader.defaultInstantiator',$namespace);
			if(!isset($this->instances[$class][$namespace])){
				$this->instances[$class][$namespace] = new $defaultInstantiator($this);
			}
		}
		return $this->instances[$class][$namespace];
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getConfig()->getNamespace();
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		$defaultFactories = $this->getConfig()->get('BricksClassLoader.defaultFactories',$namespace);
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
			return $a->getPriority()>$b->getPriority()?1:0;
		});		
	}
	
	/**
	 * @param string $classOrAlias	 
	 * @param array $params
	 * @param string $namespace
	 * @return object	 
	 */
	public function instantiate($classOrAlias,array $params=array(),$namespace=null){
		$object = null;		
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		$instantiator = $this->getInstantiator($class,$namespace);		
		return $instantiator->instantiate($class,$params,$namespace);		
	}

	/**
	 * @param object $object
	 * @param string $classOrAlias	 
	 * @param string $namespace
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array(),$namespace=null){		
		$factories = $this->getFactories($classOrAlias,$namespace);
		$this->sortFactories($factories);
		foreach($factories AS $factory){
			$factory->build($object,$params);
		}
	}
	
	/**	
	 * @param string $classOrAlias	 
	 * @param array $params
	 * @param $namespace
	 * @return object
	 */
	public function singleton($classOrAlias,array $params=array(),$namespace=null){
		$namespace = $namespace?:$this->getConfig()->getNamespace();
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(!isset($this->instances[$class][$namespace])){
			$this->instances[$class][$namespace] = $this->get($class,$params,$namespace);
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
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(isset($this->instances[$class][$namespace])){
			unset($this->instances[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @param string $namespace
	 * @return object
	 */
	public function get($classOrAlias,array $params=array(),$namespace){		
		$object = $this->instantiate($classOrAlias,$params,$namespace);		
		$this->factory($object,$classOrAlias,$params,$namespace);
		return $object;
	}
	
}
