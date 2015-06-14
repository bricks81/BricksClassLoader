<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

interface FactoryInterface extends ClassLoaderAwareInterface {
	
	/**
	 * If this factory can instantiate a new class
	 * There can only be one instantiator on factory stack 
	 * 
	 * @return bool
	 */
	public function isInstantiator();

	/**
	 * @param boolean $bool
	 */
	public function setIsInstantiator($bool);
	
	/**
	 * @return int
	 */
	public function getPriority();
	
	/**
	 * @param int $priority
	 */
	public function setPriority($priority);
	
	/**
	 * On which class will this factory be executed
	 * 
	 * @return string
	 */
	public function getOnClass();
	
	/**
	 * @param string $class
	 */
	public function setOnClass($class);
	
	/**
	 * On which method will this factory be executed
	 */
	public function getOnMethod();
	
	/**
	 * @param string $method
	 */
	public function setOnMethod($method);
	
	/**
	 * @param string $class	 
	 * @return object
	 */
	public function instantiate($class,array $factoryParams=array());
	
	/**
	 * @param object $object
	 * @param array $factoryParams	 
	 */
	public function build($object,array $factoryParams=array());
	
}