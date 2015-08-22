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
use Zend\Config\Config as ZendConfig;

class ClassLoader implements ServiceLocatorAwareInterface {
	
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
	 * @var array
	 */
	protected $instanceNamespaces = array();
	
	/**
	 * @var array
	 */
	protected $classLoaders = array();
	
	/**
	 * @param ConfigInterface $config
	 */
	public function __construct(ConfigInterface $config){
		$this->setConfig($config);						
	}
	
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
	 * @param string $module
	 * @return ConfigInterface
	 */
	public function getConfig($module=null){
		if(null==$module){
			return $this->config;
		} else {
			return $this->config->getConfig($module);
		}
	}
	
	/**
	 * @param string $module
	 * @param ClassLoaderInterface $classLoader
	 */
	public function setClassLoader($module,ClassLoaderInterface $classLoader){
		$this->classLoaders[$module] = $classLoader;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getClassLoader()
	 */
	public function getClassLoader($module=null){
		if(null===$module){
			return $this;
		}
		if(!$this->hasClassLoader($module)){					
			$alias = 'defaultClassLoaderClass';			
			$this->classLoaders[$module] = $this->newInstance(__CLASS__,__FUNCTION__,$alias,'BricksClassLoader',$module,array(
				'ClassLoader' => $this,
				'moduleName' => $module,				
			));
		}
		return $this->classLoaders[$module];
	}
	
	/**
	 * @param string $module
	 * @return boolean
	 */
	public function hasClassLoader($module){
		return isset($this->classLoaders[$module]);
	}

	/**
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @return string
	 */
	public function solveAlias($alias,$module,$namespace=null){
		$namespace = null==$namespace?$module:$namespace;
		$_cfg = $this->getConfig('BricksClassLoader')->getArray($namespace);
		$aliases = array(
			'defaultClassLoaderClass' => $_cfg['defaultClassLoaderClass'],
			'defaultInstantiator' => $_cfg['defaultInstantiator'],
			'defaultFactory' => $_cfg['defaultFactory'], 
		);
		$aliases = array_merge_recursive(
			$aliases,			
			$this->getConfig($module)->getArray($namespace)['aliases']
		);
		$parts = explode('.',$alias);
		if(1 == count($parts) && !isset($aliases[$alias])){
			return $alias;
		}				
		
		$aliasName = array_pop($parts);
		$pointer = &$aliases;
		$class = $alias;		
		if(0==count($parts)){
			if(!isset($pointer[$aliasName])){
				return $alias;
			}
			return $pointer[$aliasName];
		}
		
		foreach($parts AS $key){
			if(isset($pointer[$aliasName])) {
				if(is_array($pointer[$aliasName])){					
					if(isset($pointer[$aliasName]['class'])){
						$class = $pointer[$aliasName]['class'];
					}
				} else {					
					$class = $pointer[$aliasName];
				}
			}
			if(isset($pointer[$key])){
				$pointer = &$pointer[$key];
			}
		}
		if(isset($pointer[$aliasName])){
			if(is_array($pointer[$aliasName])){
				if(isset($pointer[$aliasName]['class'])){
					$class = $pointer[$aliasName]['class'];
				}
			} else {
				$class = $pointer[$aliasName];
			}
		}		
		return $class;
		
	}
	
	/**
	 * @param string $instantiator
	 * @param string $class
	 * @param string $method
	 * @return object
	 */
	public function instantiateInstantiator($instantiator,$class=false,$method=false){
		$instantiator = new $instantiator();		
		$instantiator->setOnClass($class);
		$instantiator->setOnMethod($method);
		return $instantiator;
	}
	
	/**
	 * @param InstantiatorInterface $instantiator
	 * @param string $namespace
	 */
	public function addInstantiator(InstantiatorInterface $instantiator,$alias,$module,$namespace=null){
		$namespace = null === $namespace ? 'BricksClassLoader' : $namespace;
		if(!isset($this->instantiators[$module][$namespace])){
			$this->getInstantiators($alias,$module,$namespace);
		}
		$this->instantiators[$module][$namespace][] = $instantiator;		
	}
	
	/**
	 * @param string $className
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 */
	public function removeInstantiator($className,$alias,$module,$namespace=null){
		$namespace = null === $namespace ? 'BricksClassLoader' : $namespace;
		if(!isset($this->instantiators[$module][$namespace])){
			$this->getInstantiators($alias,$module,$namespace);
		}
		foreach($this->instantiators[$module][$namespace] AS $key => $_inst){
			if(get_class($_inst) == $className){
				unset($this->instantiators[$module][$namespace][$key]);
			}
		}
	}
	
	/**
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @return array
	 */
	public function getInstantiators($alias,$module,$namespace=null){		
		$namespace = null === $namespace ? $module : $namespace;
		if(!isset($this->instantiators[$module][$namespace])){
			$this->instantiators[$module][$namespace] = array();
			$aliases = $this->getConfig($module)->getArray($namespace)['aliases'];			
			if(false !== strpos($alias,'.') && !isset($aliases[$alias])){	
				return array();
			}
			
			$parts = explode('.',$alias);
			$aliasName = array_pop($parts);
			$pointer = &$aliases;
			$class = $alias;
			if(0==count($parts)){
				$parts[] = $aliasName;
			}
			foreach($parts AS $key){
				if(
					isset($pointer[$aliasName]['instantiators'])
					&& is_array($pointer[$aliasName]['instantiators'])
				){
					$this->processInstantiatorConfig($pointer[$aliasName]['instantiators'],$this->instantiators[$module][$namespace]);
				}
				if(isset($pointer[$key])){					
					$pointer = &$pointer[$key];					
				}
			}
			if(isset($pointer['instantiators']) && is_array($pointer['instantiators'])){
				$this->processInstantiatorConfig($pointer['instantiators'],$this->instantiators[$module][$namespace]);
			}	
		}		
		return $this->instantiators[$module][$namespace];
	}
	
	/**
	 * @param string $factory
	 * @return \Bricks\ClassLoader\FactoryInterface
	 */
	public function instantiateFactory($factory,$class=false,$method=false){
		$factory = new $factory();
		$factory->setClassLoader($this);
		$factory->setOnClass($class);
		$factory->setOnMethod($class);
		return $factory;
	}
	
	/**
	 * @param FactoryInterface $factory
	 * @param string $namespace
	 */
	public function addDefaultFactory(FactoryInterface $factory,$namespace=null){
		$namespace = null == $namespace ? 'BricksClassLoader' : $namespace;
		if(!isset($this->defaultFactories[$namespace])){
			$this->getDefaultFactories($namespace);
		}
		$this->defaultFactories[$namespace][] = $factory;
		$this->sortFactories($this->defaultFactories[$namespace]);		
	}
	
	/**
	 * @param string $className
	 * @param string $namespace
	 */
	public function removeDefaultFactory($className,$namespace){
		$namespace = null == $namespace ? 'BricksClassLoader' : $namespace;
		if(!isset($this->defaultFactories[$namespace])){
			$this->getDefaultFactories($namespace);
		}
		foreach($this->defaultFactories[$namespace] AS $key => $_factory){
			if(get_class($_factory) == $className){
				unset($this->defaultFactories[$namespace][$key]);
			}
		}
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getDefaultFactories($namespace=null){
		$namespace = null === $namespace ? 'BricksClassLoader' : $namespace;		
		if(!isset($this->defaultFactories[$namespace])){
			$this->defaultFactories[$namespace] = array();
			$config = $this->getConfig()->get('defaultFactories',$namespace);
			if( is_array($config) && count($config) ){
				if(!isset($this->defaultFactories[$namespace])){
					$this->defaultFactories[$namespace] = array();
				}		
				$this->processFactoryConfig($config,$this->defaultFactories[$namespace]);				
			}
			$this->sortFactories($this->defaultFactories[$namespace]);
		}
		return $this->defaultFactories[$namespace];
	}
	
	/**
	 * @param array $config
	 * @param array &$ref
	 */
	protected function processFactoryConfig($config,&$ref){
		foreach($config AS $data){
			if(!is_array($data)){
				$data = array(
					'class' => false,
					'method' => false,
					'factory' => $data
				);
			}
			$exists = false;
			foreach($ref AS $check){
				$check['factory'] = get_class($check['factory']);
				if($data==$check){
					$exists = true;
					break;
				}
			}
			if($exists){
				continue;
			}
			$ref[] = $this->instantiateFactory($data['factory'],$data['class'],$data['method']);
		}
	}
	
	/**
	 * @param array $config
	 * @param array &$ref
	 */
	protected function processInstantiatorConfig($config,&$ref){
		foreach($config AS $data){					
			if(!is_array($data)){
				$data = array(
					'class' => false,
					'method' => false,
					'instantiator' => $data
				);
			}
			$exists = false;
			foreach($ref AS $check){
				$check['instantiator'] = get_class($check['instantiator']);
				if($data==$check){
					$exists = true;
					break;
				}
			}
			if($exists){
				continue;
			}			
			$ref[] = $this->instantiateInstantiator($data['instantiator'],$data['class'],$data['method']);
		}
	}
	
	/**
	 * @param array $instantiators
	 * @param string $namespace
	 */
	public function addDefaultInstantiatorIfNeeded(array &$instantiators,$namespace=null){
		$namespace = null === $namespace ? 'BricksClassLoader' : $namespace;
		if(0 == count($instantiators)){
			$class = $this->getConfig('BricksClassLoader')->get('defaultInstantiator',$namespace);
			$instantiator = $this->instantiateInstantiator($class);
		}
		array_push($instantiators,$instantiator);
	}
	
	/**
	 * @param array $factories
	 * @param string $namespace
	 * @return boolean
	 */
	public function addDefaultFactoryIfNeeded(array &$factories,$namespace=null){
		$namespace = null === $namespace ? 'BricksClassLoader' : $namespace;
		if(0 == count($factories)){
			$lowest = -10000;
			foreach($factories AS $factory){
				if($factory->getPriority()<$lowest){
					$lowest = $factory->getPriority()-1;
				}
			}
			$factory = $this->instantiateFactory($this->getConfig()->get('defaultFactory',$namespace));
			$factory->setPriority($lowest);			
			array_unshift($factories,$factory);
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
	 * @param FactoryInterface $factory
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 */
	public function addFactory(FactoryInterface $factory,$alias,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		if(!isset($this->factories[$module][$namespace])){
			$this->getFactories($alias,$module,$namespace);
		}
		$this->factories[$module][$namespace][] = $factory;
		$this->sortFactories($this->factories[$module][$namespace]);
	}
	
	/**
	 * @param string $className
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 */
	public function removeFactory($className,$alias,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		if(!isset($this->factories[$module][$namespace])){
			$this->getFactories($alias, $module, $namespace);
		}  
		foreach($this->factories[$module][$namespace] AS $key => $_factory){
			if(get_class($_factory) == $className){
				unset($this->factories[$module][$namespace][$key]);
			}
		}
	}
	
	/**
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($alias,$module,$namespace=null){
		$namespace = null==$namespace?$module:$namespace;
		if(!isset($this->factories[$module][$namespace])){
			$this->factories[$module][$namespace] = array();
			$aliases = array_replace_recursive(
				$this->getConfig()->getArray($module),
				$this->getConfig()->getArray($namespace)
			);			
			$parts = explode('.',$alias);			
			if(1 == count($parts) && !isset($aliases[$alias])){
				return array();
			}
			
			$parts = explode('.',$alias);
			$aliasName = array_pop($parts);
			$pointer = &$aliases;
			$class = $alias;
			if(0==count($parts)){
				$parts[] = $aliasName;
			}
			foreach($parts AS $key){
				if(isset($pointer[$aliasName]['factories'])
						&& is_array($pointer[$aliasName]['factories'])
				){
					$this->processFactoryConfig($pointer[$aliasName]['factories'],$this->factories[$module][$namespace]);
				}
				if(isset($pointer[$key])){
					$pointer = &$pointer[$key];
				}
			}
			if(isset($pointer[$aliasName]['factories']) && is_array($pointer[$aliasName]['factories'])){
				$this->processFactoryConfig($pointer[$aliasName]['factories'],$this->factories[$module][$namespace]);
			}	
			$this->sortFactories($this->factories[$module][$namespace]);			
		}
		return $this->factories[$module][$namespace];
	}
	
	/**
	 * @param string $class
	 * @param object $object
	 * @param string $module
	 * @param string $namespace
	 */
	public function setInstance($class,$object,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		$this->instances[$module][$namespace][$class] = $object;
	}
	
	/**
	 * @param string $class
	 * @param string $module
	 * @param string $namespace
	 */
	public function hasInstance($class,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		return isset($this->instances[$module][$namespace][$class]);
	}
	
	/**
	 * @param string $class
	 * @param string $module
	 * @param string $namespace
	 */
	public function getInstance($class,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		if($this->hasInstance($class, $module, $namespace)){
			return $this->instances[$module][$namespace][$class];
		}
	}
	
	/**
	 * @param string $class
	 * @param string $module
	 * @param string $namespace
	 */
	public function removeInstance($class,$module,$namespace=null){
		$namespace = null === $namespace ? $module : $namespace;
		if($this->hasInstance($class, $module, $namespace)){
			unset($this->instances[$module][$namespace][$class]);
		}
	}
	
	/**
	 * @param string $class
	 * @param string $module
	 * @param string $namespace
	 * @param array $factoryParams
	 * @throws CouldNotInstantiateException
	 * @return object
	 */
	public function instantiate($alias,$module,$namespace=null,array $factoryParams=array()){
		$object = null;
		
		$namespace = null === $namespace ? $module : $namespace;
		
		$instantiators = $this->getInstantiators($alias,$module,$namespace);
		$this->addDefaultInstantiatorIfNeeded($instantiators,$namespace);
		
		$class = $this->solveAlias($alias,$module,$namespace);		
		
		foreach($instantiators AS $instantiator){			
			$object = $instantiator->instantiate($class,$factoryParams);
			if(is_object($object)){
				break;
			}			
		}
		if(null === $object){
			throw new CouldNotInstantiateException('class "'.$class.'" could not be instantiated because of missing instantiator');
		}		
		return $object;
	}

	/**
	 * @param string $class
	 * @param string $method
	 * @param object $object
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @param array $factoryParams
	 */
	public function factory($class,$method,$object,$alias,$module,$namespace=null,array $factoryParams=array()){
		$namespace = null === $namespace ? $module : $namespace;
		
		$factories = array_merge(
			$this->getDefaultFactories($namespace),
			$this->getFactories($alias,$module,$namespace)
		);
		$this->addDefaultFactoryIfNeeded($factories);
		
		foreach($factories AS $key => $factory){
			if(false != $class){
				if(false != $factory->getOnClass()){
					if($class != $factory->getOnMethod()){
						unset($factories[$key]);
					}
				}
			}
			if(false != $method){
				if(false != $factory->getOnMethod()){
					if($method != $factory->getOnMethod()){
						unset($factories[$key]);
					}
				}
			}
		}
		
		foreach($factories AS $factory){
			$factory->build($object,$factoryParams);
		}		
	}
	
	/**	
	 * @param string $class
	 * @param string $method
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @param array $factoryParams
	 * @param boolean $refactor
	 * @return object
	 */
	public function getSingleton($class,$method,$alias,$module,$namespace=null,array $factoryParams=array(),$refactor=false){
		if(null === $namespace && isset($this->instanceNamespaces[$module][$alias])){
			$namespace = $this->instanceNamespaces[$module][$alias];
		} else {
			$namespace = null === $namespace ? $module : $namespace;
			$this->instanceNamespaces[$module][$alias] = $namespace;
		}		
		
		$className = $this->solveAlias($alias, $module, $namespace);
		
		if(!$this->hasInstance($className,$module,$namespace)){			
			$object = $this->newInstance($class,$method,$alias,$module,$namespace,$factoryParams);
			$this->setInstance($class,$object,$module,$namespace);			
		} else {
			$object = $this->getInstance($class,$module,$namespace);			
			if($refactor){
				$this->factory($class,$method,$object,$alias,$module,$namespace,$factoryParams,$refactor);				
			}
		}
		return $object;
	}
	
	/**
	 * @param string $class
	 * @param string $method
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @param array $factoryParams
	 * @return object
	 */
	public function newInstance($class,$method,$alias,$module,$namespace=null,array $factoryParams=array()){
		$namespace = null === $namespace ? $module : $namespace;		
		$object = $this->instantiate($alias, $module, $namespace, $factoryParams);
		$this->factory($class,$method,$object,$alias,$module,$namespace,$factoryParams);
		return $object;
	}
	
}