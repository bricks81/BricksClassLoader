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

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Bricks\Config\ConfigServiceAwareInterface;
use Bricks\ClassLoader\Instantiators\InstantiatorInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Bricks\ClassLoader\ClassLoader\ClassLoaderInterface;
use Bricks\Config\ConfigServiceInterface;

class ClassLoaderService implements ServiceLocatorAwareInterface, 
	ConfigServiceAwareInterface, EventManagerAwareInterface, ClassLoaderServiceInterface {
	
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
	 * @var ConfigServiceInterface
	 */
	protected $configService;
	
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
	}
	
	public function initialize(){
		$this->attachAliasListener();
		$this->attachClassmapListener();
		$this->attachDefaultInstantiatorListener();
		$this->attachInstantiatorListener();
		$this->attachDefaultFactoriesListener();
		$this->attachFactoriesListener();
	}
	
	/**
	 * @return EventManagerInterface
	 */
	public function getEventManager(){
		return $this->eventManager;
	}
	
	/**
	 * @param ConfigServiceInterface $configService
	 */
	public function setConfigService(ConfigServiceInterface $configService){
		$this->configService = $configService;
	}
	
	/**
	 * @return ConfigServiceInterface
	 */
	public function getConfigService(){
		return $this->configService;
	}
	
	/**
	 * @param string $classOrAlias
	 * @param mixed $object
	 * @param string $namespace
	 */
	public function setInstance($classOrAlias,$object,$namespace=null){
		$namespace = $namespace?:$this->getConfigService()->getDefaultNamespace();
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);		
		$this->instances[$class][$namespace] = $object;
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function getInstance($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getConfigService()->getDefaultNamespace();
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(isset($this->instances[$class][$namespace])){
			return $this->instances[$class][$namespace];
		}
		if($defaultNamespace != $namespace){
			$class = $this->aliasToClass($classOrAlias,$defaultNamespace)?:$classOrAlias;
			$class = $this->getClassOverwrite($class,$defaultNamespace);
			if(isset($this->instances[$class][$defaultNamespace])){
				return $this->instances[$class][$defaultNamespace];
			}
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function unsetInstance($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getConfigService()->getDefaultNamespace();
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(isset($this->instances[$class][$namespace])){
			unset($this->instances[$class][$namespace]);
			if(!count($this->instances[$class])){
				unset($this->instances[$class]);
			}
		}
	}
	
	protected function attachAliasListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','afterSet',function($e) {
			if(0 !== strpos('BricksClassLoader.aliasMap',$e->getParam('path'))){
				return;
			}
			$path = $e->getParam('path');				
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$alias = $parts[2];
				$this->unsetInstance($alias,$e->getParam('namespace'));
			}
		});
	}
	
	protected function attachClassmapListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','afterSet',function($e) {
			if(0 === strpos('BricksClassLoader.classMap',$e->getParam('path'))){
				return;
			}
			$path = $e->getParam('path');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];
				$this->unsetInstance($class,$e->getParam('namespace'));
			}
		});
	}
	
	protected function attachDefaultInstantiatorListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','afterSet',function($e) {
			if(0 === strpos('BricksClassLoader.defaultInstantiator',$e->getParam('path'))){
				return;
			}
			$path = $e->getParam('path');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];
				$this->unsetInstance($class,$e->getParam('namespace'));
			}
		});
	}
	
	protected function attachInstantiatorListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','beforeSet',function($e) {
			if(0 === strpos('BricksClassLoader.instantiator',$e->getParam('path'))){
				return;
			}
			$path = $e->getParam('path');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];				
				$this->unsetInstance($class,$e->getParam('namespace'));
			}
		});
	}
	
	protected function attachDefaultFactoriesListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','beforeSet',function() {
			if(0 === strpos('BricksClassLoader.defaultFactories',$e->getParam('path'))){
				return;
			}
			$factories = $this->getConfig()->get('BricksClassLoader.defaultFactories',$e->getParam('namespace'));
			foreach($factories AS $factory){
				$this->unsetInstance($factory,$e->getParam('namespace'));
			}			
		});
	}
	
	protected function attachFactoriesListener(){
		$this->getEventManager()->getSharedManager()->attach('BricksConfig','beforeSet',function($e) {
			if(0 === strpos('BricksClassLoader.factories',$e->getParam('path'))){
				return;
			}
			$path = $e->getParam('path');
			$parts = explode('.',$path);
			if(isset($parts[2])){
				$class = $parts[2];
				$this->unsetInstance($class,$e->getParam('namespace'));
			}
		});
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoaderServiceInterface::getClassLoader()
	 */
	public function getClassLoader($namespace=null){
		$namespace = $namespace?:$this->getConfigService()->getDefaultNamespace();
		$object = $this->singleton('BricksClassLoader.defaultClassLoader',array('namespace'=>$namespace),$namespace);		
		$this->setClassLoader($object);
		return $object;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Bricks\ClassLoader\ClassLoaderServiceInterface::setClassLoader()
	 */
	public function setClassLoader(ClassLoaderInterface $classLoader){
		$this->classLoaders[$classLoader->getNamespace()] = $classLoader;
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getAliasMap($namespace=null){
		return $this->getConfig()->get('BricksClassLoader.aliasMap',$namespace);		
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getClassMap($namespace=null){
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
		$instantiator = $this->getConfig()->get('BricksClassLoader.instantiator.'.$class,$namespace);
		if($instantiator){
			$instance = $this->getInstance($classOrAlias,$namespace);
			if(!$instance){
				$instance = new $instantiator($this);
				$this->setInstance($classOrAlias,$instance,$namespace);
			}
		} else {
			$defaultInstantiator = $this->getConfig()->get('BricksClassLoader.defaultInstantiator',$namespace);
			$instance = $this->getInstance($classOrAlias,$namespace);
			if(!$instance){
				$instance = new $defaultInstantiator($this);
				$this->setInstance($classOrAlias,$instance,$namespace);
			}
		}
		return $instance;
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($classOrAlias,$namespace=null){
		$config = $this->getConfigService()->getConfig($namespace);
		$defaultFactories = $config->get('BricksClassLoader.defaultFactories');
		$factories = $this->getConfig()->get('BricksClassLoader.factories.'.$class);
		$return = array();
		foreach($defaultFactories AS $factory){
			$instance = $this->getInstance($factory,$instanceNamespace);
			if(!$instance){
				$instance = new $factory($this);
				$this->setInstance($factory,$instance,$instanceNamespace);
			}
			$return[] = $instance;
		}
		if($factories){
			foreach($factories AS $factory){
				$instance = $this->getInstance($factory,$instanceNamespace);
				if(!$instance){
					$instance = new $factory($this);
					$this->setInstance($factory,$instance,$instanceNamespace);
				}
				$return[] = $instance;
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
		$namespace = $namespace?:$this->getConfig()->getDefaultNamespace();
		$instance = $this->getInstance($classOrAlias,$namespace);
		if(!$instance){
			$instance = $this->get($classOrAlias,$params,$namespace);
			$this->setInstance($classOrAlias,$instance,$instanceNamespace);
		}
		return $instance;		
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
