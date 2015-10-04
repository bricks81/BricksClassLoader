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
	 * @return string|null
	 */
	public function aliasToClass($alias,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$aliasMap = $this->getConfig()->get('aliasMap',$namespace);
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
		$return = null;
		$namespace = $namespace?:'BricksClassLoader';
		$classMap = $this->getConfig()->get('classMap',$namespace);
		while(isset($classMap[$class])){
			$ret = $classMap[$class];
			if(is_array($ret) && isset($ret['class'])){
				$ret = $ret['class'];
			} elseif(is_array($ret) && count($ret) > 0){
				$ret = array_slice($ret,0,1);
			}
			unset($classMap[$class]);
			$class = $ret;
		}
		return $class;
	}
	
	/**
	 * @param InstantiatorInterface $instantiator
	 * @param string $classOrAlias
	 * @param string $namespace 
	 */
	public function setInstantiator(InstantiatorInterface $instantiator,$classOrAlias,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(!isset($this->instantiators[$class][$namespace])){
			$this->instantiators[$class][$namespace] = $instantiator;
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function removeInstantiator($classOrAlias,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(isset($this->instantiators[$class][$namespace])){
			unset($this->instantiators[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias,$namespace=null){		
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
						
		if($class && !isset($this->instantiators[$class][$namespace])){			
			
			if(!isset($this->instantiators[$class])){
				$this->instantiators[$class] = array();
			}
			
			$classMap = $this->getConfig()->get('classMap',$namespace);	
			$instantiatorClass = $this->aliasToClass('defaultInstantiator',$namespace);						
			$instantiatorClass = $this->getClassOverwrite($instantiatorClass,$namespace);
			if(isset($classMap[$class]['instantiator'])){
				$instantiatorClass = $classMap[$class]['instantiator'];
			}			
			
			// avoid loading many times the same object
			foreach($this->instantiators[$class] AS $ns => $instance){
				if(get_class($instance) == $class){
					$this->setInstantiator($instance,$className,$namespace);
					break;					
				}			
			}
			
			if(!isset($this->instantiators[$class][$namespace])){				
				$this->setInstantiator(new $instantiatorClass($this),$class,$namespace);
			}
			
		}
		if(isset($this->instantiators[$class][$namespace])){
			return $this->instantiators[$class][$namespace];
		}
	}
	
	/**
	 * @param FactoryInterface $factory
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function addFactory(FactoryInterface $factory,$classOrAlias,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(!isset($this->factories[$class][$namespace])){
			$this->factories[$class][$namespace][] = $factory;
		}		
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function removeFactory($classOrAlias,$alias,$module,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(isset($this->factories[$class][$namespace])){
			unset($this->factories[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($classOrAlias,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);		
		if(!isset($this->factories[$class][$namespace])){

			if(!isset($this->factories[$class])){
				$this->factories[$class] = array();
			}
			
			// avoid loading more than one time			
			$array = $this->getConfig()->get('aliasMap.defaultFactories',$namespace);
			foreach($array AS $key => $className){				
				$className = $this->getClassOverwrite($className,$namespace);
				foreach($this->factories[$class] AS $ns => $instance){
					if(get_class($instance) == $className){
						$this->addFactory($instance,$class,$namespace);
						unset($array[$key]);						
					}
				}
			}
			
			foreach($array AS $className){
				$className = $this->getClassOverwrite($className,$namespace);
				$this->addFactory(new $className($this),$class,$namespace);
			}
									
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
	 * @param string $namespace
	 * @param array $params
	 * @return object	 
	 */
	public function instantiate($classOrAlias,$namespace=null,array $params=array()){
		$object = null;
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		$instantiator = $this->getInstantiator($class,$namespace);		
		return $instantiator->instantiate($class,$params);		
	}

	/**
	 * @param object $object
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,$namespace=null,array $params=array()){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		$factories = $this->getFactories($class,$namespace);
		$this->sortFactories($factories);
		foreach($factories AS $factory){
			$factory->build($object,$params);
		}		
	}
	
	/**	
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @param array $params
	 * @return object
	 */
	public function singleton($classOrAlias,$namespace=null,array $params=array()){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(!isset($this->instances[$class][$namespace])){
			$this->instances[$class][$namespace] = $this->get($class,$namespace,$params);
		}		
		return $this->instances[$class][$namespace];
	}
	
	/**
	 * 
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function removeSingleton($classOrAlias,$namespace=null){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		if(isset($this->instances[$class][$namespace])){
			unset($this->instances[$class][$namespace]);
		}
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @param array $params
	 * @return object
	 */
	public function get($classOrAlias,$namespace=null,array $params=array()){
		$namespace = $namespace?:'BricksClassLoader';
		$class = $this->aliasToClass($classOrAlias,$namespace)?:$classOrAlias;
		$class = $this->getClassOverwrite($class,$namespace);
		$object = $this->instantiate($class,$namespace,$params);		
		$this->factory($object,$class,$namespace,$params);
		return $object;
	}
	
}
