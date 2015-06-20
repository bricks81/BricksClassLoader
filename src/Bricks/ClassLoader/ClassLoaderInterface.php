<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

interface ClassLoaderInterface extends ClassLoaderAwareInterface {
	
	/**
	 * @return string
	 */
	public function getModule();
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @return string
	 */
	public function solveAlias($alias,$namespace=null);
	
	/**
	 * @param string $instantiator
	 * @param string $alias
	 * @param string $namespace
	 */
	public function addInstantiator(InstantiatorInterface $instantiator,$alias,$namespace=null);
		
	/**
	 * @param string $className
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 */
	public function removeInstantiator($className,$alias,$module,$namespace=null);	
	
	/**
	 * @param string $alias
	 * @param string $module
	 * @param string $namespace
	 * @return array
	 */
	public function getInstantiators($alias,$module,$namespace=null);
	
	/**
	 * @param FactoryInterface $factory
	 * @param string $alias
	 * @param string $namespace
	 */
	public function addFactory(FactoryInterface $factory,$alias,$namespace=null);
	
	/**
	 * @param string $className
	 * @param string $alias
	 * @param string $namespace
	 */
	public function removeFactory($className, $alias, $namespace=null);
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($alias, $namespace=null);
	
	/**
	 * @param string $class
	 * @param object $object
	 * @param string $namespace
	 */
	public function setInstance($class,$object,$namespace=null);
	
	/**
	 * @param string $class
	 * @param string $namespace
	 * @return boolean
	 */
	public function hasInstance($class, $namespace=null);
	
	/**
	 * @param string $class
	 * @param string $namespace
	 */
	public function getInstance($class, $namespace=null);
	
	/**
	 * @param string $class
	 * @param string $namespace
	 * @return object
	 */
	public function removeInstance($class, $namespace=null);
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @param array $factoryParams
	 * @return object
	 */
	public function instantiate($alias, $namespace=null, array $factoryParams = array());
	
	/**
	 * @param object $object
	 * @param string $alias
	 * @param string $namespace
	 * @param array $factoryParams
	 */
	public function factory($object, $alias, $namespace=null, array $factoryParams = array());
	
	/**
	 * @param string $class
	 * @param string $method
	 * @param string $alias
	 * @param string $namespace
	 * @param array $factoryParams
	 * @param bool $refactor
	 * @return object
	 */
	public function getSingleton($class,$method,$alias,$namespace=null,array $factoryParams=array(),$refactor=false);
	
	/**
	 * @param string $class
	 * @param string $method
	 * @param string $alias
	 * @param string $namespace
	 * @param array $factoryParams
	 * @return object
	 */
	public function newInstance($class,$method,$alias,$namespace=null,array $factoryParams=array());
	
}