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
	protected $factories = array();
	
	/**
	 * @var array
	 */
	protected $instances = array();
	
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
	 * @return ConfigInterface
	 */
	public function getConfig($module=null){
		$module = $module?:'BricksClassLoader';
		return $this->config->getConfig($module);		
	}
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @return string
	 */
	public function getAliasClass($alias,$namespace){
		$namespace = $namespace?:'BricksClassLoader';
		$aliasMap = $this->getConfig()->get('aliasMap',$namespace);
		$parts = explode('.',$alias);
		if(1 == count($parts)){
			while(isset($aliases[$alias])){
				$key = $alias;
				$alias = $aliases[$alias];
				unset($aliases[$key]);
			}
			return $alias;
		}
	
		$aliasName = array_pop($parts);
		$pointer = &$aliases;
		$classOrAlias = $alias;
		if(0==count($parts)){
			if(!isset($pointer[$aliasName])){
				return $classOrAlias;
			}
			return $pointer[$aliasName];
		}
	
		foreach($parts AS $key){
			if(isset($pointer[$aliasName])) {
				if(is_array($pointer[$aliasName])){
					if(isset($pointer[$aliasName]['class'])){
						$classOrAlias = $pointer[$aliasName]['class'];
					}
				} else {
					$classOrAlias = $pointer[$aliasName];
				}
			}
			if(isset($pointer[$key])){
				$pointer = &$pointer[$key];
			}
		}
		if(isset($pointer[$aliasName])){
			if(is_array($pointer[$aliasName])){
				if(isset($pointer[$aliasName]['class'])){
					$classOrAlias = $pointer[$aliasName]['class'];
				}
			} else {
				$classOrAlias = $pointer[$aliasName];
			}
		}
	
		while(isset($aliases[$classOrAlias])){
			$key = $classOrAlias;
			$classOrAlias = $aliases[$classOrAlias];
			unset($aliases[$key]);
		}
	
		return $classOrAlias;
	}
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 */
	public function aliasToClass($alias,$namespace=null){
		
	}
	
	/**
	 * @param InstantiatorInterface $instantiator
	 * @param string class
	 * @param string namespace 
	 */
	public function setInstantiator(InstantiatorInterface $instantiator,$class,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		if(!isset($this->instantiators[$class][$namespace])){
			$this->instantiators[$class][$namespace] = $instantiator;
		}
	}
	
	/**
	 * @param string $class
	 * @param string $namespace
	 */
	public function removeInstantiator($class,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		if(isset($this->instantiators[$class][$namespace])){
			unset($this->instantiators[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $class
	 * @param string $namespace
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($class,$namespace=null){		
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		if(!isset($this->instantiators[$class][$namespace])){			
			
			// avoid loading many times the same object
			$className = $this->getConfig()->get('defaultInstantiator',$namespace);
			$className = $this->getConfig()->get('classMap.'.$className,$namespace)?:$class;
			foreach($this->instantiators[$class] AS $ns => $instance){
				if(get_class($instance) == $_inst){
					$this->setInstantiator($instance,$className,$namespace);
					break;					
				}			
			}	
			
			if(!isset($this->instantiators[$class][$namespace])){
				$this->setInstantiator(new $className($this),$class,$namespace);
			}
			
		}
		if(isset($this->instantiators[$class][$namespace])){
			return $this->instantiators[$class][$namespace];
		}
	}
	
	/**
	 * @param FactoryInterface $factory
	 * @param string $class
	 * @param string $namespace
	 */
	public function addFactory(FactoryInterface $factory,$class,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		if(!isset($this->factories[$class][$namespace])){
			$this->factories[$class][$namespace][] = $factory;
		}		
	}
	
	/**
	 * @param string $class
	 * @param string $namespace
	 */
	public function removeFactory($class,$alias,$module,$namespace=null){
		$namespace = $namespace?:$module;
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		if(isset($this->factories[$class][$namespace])){
			unset($this->factories[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $class
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($class,$namespace=null){
		$namespace = $namespace?:$module;
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;		
		if(!isset($this->factories[$class][$namespace])){

			// avoid loading more than one time
			$array = $this->getConfig()->get('defaultFactories',$namespace);
			foreach($array AS $key => $className){
				$className = $this->getConfig()->get('classMap.'.$className,$namespace);
				foreach($this->factories[$class] AS $ns => $instance){
					if(get_class($instance) == $className){
						$this->addFactory($instance,$class,$namespace);
						unset($array[$key]);						
					}
				}
			}
			
			foreach($array AS $className){
				$className = $this->getConfig()->get('classMap.'.$className,$namespace);
				$this->addFactory(new $className($this),$class,$namespace);
			}
									
		}
		if(isset($this->factories[$class][$namespace])){		
			return $this->factories[$module][$namespace];
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
	 * @param string $class
	 * @param string $namespace
	 * @param array $params
	 * @return object	 
	 */
	public function instantiate($class,$namespace=null,array $params=array()){
		$object = null;
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		$instantiator = $this->getInstantiator($class,$namespace);		
		return $instantiator->instantiate($class,$params);		
	}

	/**
	 * @param object $object
	 * @param string $class
	 * @param string $namespace
	 * @param array $params
	 */
	public function factory($object,$class,$namespace=null,array $params=array()){
		$namespace = $namespace?:'BricksClassLoader';	
		$class = $this->getConfig()->get('classMap.'.$class,$namespace)?:$class;
		$factories = $this->getFactories($class,$namespace);
		$this->sortFactories($factories);
		foreach($factories AS $factory){
			$factory->build($object,$params);
		}		
	}
	
	/**	
	 * @param string $class
	 * @param string $namespace
	 * @param array $params
	 * @return object
	 */
	public function singleton($class,$namespace=null,array $params=array()){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('aliasMap.'.$class,$namespace)?:$class;
		if(!isset($this->instances[$class][$namespace])){
			$this->instances[$class][$namespace] = $this->get($class,$namespace,$params);
		}		
		return $this->instances[$class][$namespace];
	}
	
	/**
	 * 
	 * @param string $class
	 * @param string $namespace
	 */
	public function removeSingleton($class,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('aliasMap.'.$class,$namespace)?:$class;
		if(isset($this->instances[$class][$namespace])){
			unset($this->instances[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $class
	 * @param string $namespace
	 * @param array $params
	 * @return object
	 */
	public function get($class,$namespace=null,array $params=array()){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->getConfig()->get('aliasMap.'.$class,$namespace)?:$class;
		$object = $this->instantiate($class,$namespace,$params);		
		$this->factory($object,$class,$namespace,$params);
		return $object;
	}
	
}
