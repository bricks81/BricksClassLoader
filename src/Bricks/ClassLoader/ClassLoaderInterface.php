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