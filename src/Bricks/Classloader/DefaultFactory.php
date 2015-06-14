<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

class DefaultFactory implements FactoryInterface {
	
	/**
	 * @var boolean
	 */
	protected $isInstantiator = false;
	
	/**
	 * @var string|false
	 */
	protected $onClass = false;
	
	/**
	 * @var string|false
	 */
	protected $onMethod = false;
	
	/**
	 * @var integer
	 */
	protected $priority = 0;
	
	/**
	 * @var ClassLoaderInterface
	 */
	protected $classLoader;
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::isInstantiator()
	 */
	public function isInstantiator(){
		return $this->isInstantiator;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::setIsInstantiator()
	 */
	public function setIsInstantiator($bool){
		$this->isInstantiator = $bool?true:false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::getPriority()
	 */
	public function getPriority(){
		return $this->priority;
	}
	
	public function setPriority($priority){
		$this->priority = $priority;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::getOnClass()
	 */
	public function getOnClass(){
		return $this->onClass;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::setOnClass()
	 */
	public function setOnClass($class){
		$this->onClass = $class;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::getOnMethod()
	 */
	public function getOnMethod(){
		return $this->onMethod;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::setOnMethod()
	 */
	public function setOnMethod($method){
		$this->onMethod = $method;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderAwareInterface::setClassLoader()
	 */
	public function setClassLoader($classLoader){
		$this->classLoader = $classLoader;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderAwareInterface::getClassLoader()
	 */
	public function getClassLoader(){
		return $this->classLoader;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::instantiate()
	 */
	public function instantiate($class,array $params=array()){
		$params = array_values($params);
		switch(count($params)){
			case 0: return new $class(); break;
			case 1: return new $class($params[0]); break;
			case 2: return new $class($params[0],$params[1]); break;
			case 3: return new $class($params[0],$params[1],$params[2]); break;
			case 4: return new $class($params[0],$params[1],$params[2],$params[3]); break;
			case 5: return new $class($params[0],$params[1],$params[2],$params[3],$params[4]); break;
			case 6: return new $class($params[0],$params[1],$params[2],$params[3],$params[4],$params[5]); break;
			case 7: return new $class($params[0],$params[1],$params[2],$params[3],$params[4],$params[5],$params[6]); break;
			case 8: return new $class($params[0],$params[1],$params[2],$params[3],$params[4],$params[5],$params[6],$params[7]); break;
			default:
				$relection = new \ReflectionObject($class);
				return $reflection->newInstanceArgs($params);
		}
	}
	
	// class and method missing
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::build()
	 */
	public function build($object,array $factoryParams=array()){
		foreach($factoryParams AS $name => $var){
			$method = 'set'.ucfirst($name);
			if(method_exists($object,$method)){
				$object->$method($var);
			}
		}
		return $object;
		// ... check against interfaces
		// ... use defined setters
		// ... load factories and commit them
	}
	
}