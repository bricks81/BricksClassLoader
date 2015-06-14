<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

class DefaultClassLoader implements ClassLoaderInterface {
	
	/**
	 * @var ClassLoader
	 */
	protected $classLoader;
	
	/**
	 * @var string
	 */
	protected $module;
	
	/**
	 * @param ClassLoader $classLoader
	 * @param string $module
	 */
	public function __construct(ClassLoader $classLoader,$module){
		$this->setClassLoader($classLoader);
		$this->setModule($module);
	}
	
	/**
	 * @param ClassLoader|ClassLoaderInterface $classLoader
	 */
	public function setClassLoader($classLoader){
		$this->classLoader = $classLoader;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getClassLoader()
	 */
	public function getClassLoader($module=null){
		return $this->classLoader->getClassLoader($module);
	}
	
	/**
	 * @param string $module
	 */
	public function setModule($module){
		$this->module = $module;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getModule()
	 */
	public function getModule(){
		return $this->module;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::solveAlias()
	 */
	public function solveAlias($alias,$namespace=null){
		return $this->getClassLoader()->solveAlias($alias, $this->getModule(), $namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::addInstantiator()
	 */
	public function addInstantiator(InstantiatorInterface $instantiator,$alias,$namespace=null){
		$this->getClassLoader()->addInstantiator($instantiator,$alias,$this->getModule(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::removeInstantiator()
	 */
	public function removeInstantiator($className,$alias,$module,$namespace=null){
		$this->removeInstantiator($className,$alias,$this->getModule(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getInstantiators()
	 */
	public function getInstantiators($alias,$module,$namespace=null){
		return $this->getClassLoader()->getInstantiators($alias,$this->getModule(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::addFactory()
	 */
	public function addFactory(FactoryInterface $factory,$alias,$namespace=null){
		$this->getClassLoader()->addFactory($factory, $alias, $this->getModule(), $namespace);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::removeFactory()
	 */
	public function removeFactory($className, $alias, $namespace=null){
		$this->getClassLoader()->removeFactory($className, $alias, $this->getModule(), $namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getFactories()
	 */
	public function getFactories($alias, $namespace=null){
		return $this->getClassLoader()->removeFactory($className, $alias, $this->getModule(), $namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::setInstance()
	 */
	public function setInstance($class, $object, $namespace=null){
		$this->getClassLoader()->setInstance($class, $object, $this->getModule(), $namespace);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::hasInstance()
	 */
	public function hasInstance($class, $namespace=null){
		return $this->getClassLoader()->hasInstance($class, $this->getModule(), $namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getInstance()
	 */
	public function getInstance($class, $namespace=null){
		return $this->getClassLoader()->getInstance($class,$this->getModule(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::removeInstance()
	 */
	public function removeInstance($class, $namespace=null){
		$this->getClassLoader()->removeInstance($class,$this->getModule(),$namespace);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::instantiate()
	 */
	public function instantiate($alias, $namespace=null, array $factoryParams = array()){
		return $this->getClassLoader()->instantiate($alias,$this->getModule(),$namespace,$factoryParams);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::factory()
	 */
	public function factory($object, $alias, $namespace=null, array $factoryParams = array()){
		return $this->getClassLoader()->factory($object, $alias, $this->getModule(), $namespace, $factoryParams);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::getSingleton()
	 */
	public function getSingleton($class,$method,$alias,$namespace=null,array $factoryParams=array(),$refactor=false){		
		return $this->getClassLoader()->getSingleton($class,$method,$alias,$this->getModule(),$namespace,$factoryParams,$refactor);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderInterface::newInstance()
	 */
	public function newInstance($class,$method,$alias,$namespace=null,array $factoryParams=array()){
		return $this->getClassLoader()->newInstance($class,$method,$alias,$this->getModule(),$namespace,$factoryParams);
	}	
	
}