<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

class DefaultInstantiator implements InstantiatorInterface {
	
	/**
	 * @var string|false
	 */
	protected $onClass = false;
	
	/**
	 * @var string|false
	 */
	protected $onMethod = false;
	
	/**
	 * @var ClassLoaderInterface
	 */
	protected $classLoader;
	
	public function getOnClass(){
		return $this->onClass;
	}
	
	public function setOnClass($class){
		$this->onClass = $class;		
	}
	
	public function getOnMethod(){
		return $this->onMethod;
	}
	
	public function setOnMethod($method){
		$this->onMethod = $method;
	}
	
	public function setClassLoader($classLoader){
		$this->classLoader = $classLoader;
	}
	
	public function getClassLoader(){
		return $this->classLoader;
	}
	
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
	
}