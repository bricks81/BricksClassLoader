<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

interface InstantiatorInterface extends ClassLoaderAwareInterface {
	
	/**
	 * @return string
	 */
	public function getOnClass();
	
	/**
	 * @param string $class
	 */
	public function setOnClass($class);
	
	/**
	 * @return string
	 */
	public function getOnMethod();
	
	/**
	 * @param string $method
	 */
	public function setOnMethod($method);
	
	/**
	 * @param string $class
	 * @param array $params
	 */
	public function instantiate($class,array $params=array());
	
}