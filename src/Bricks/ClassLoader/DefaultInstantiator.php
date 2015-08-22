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