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