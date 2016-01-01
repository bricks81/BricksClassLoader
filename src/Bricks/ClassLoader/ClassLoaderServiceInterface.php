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

use Bricks\ClassLoader\ClassLoader\ClassLoaderInterface;
interface ClassLoaderServiceInterface {
	
	/**
	 * @param string $moduleName
	 * @return ClassLoaderInterface
	 */
	public function getClassLoader($moduleName);
	
	/**
	 * @param ClassLoaderInterface $classLoader
	 */
	public function setClassLoader(ClassLoaderInterface $classLoader);
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getAliasMap($namespace=null);
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getClassMap($namespace=null);
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @return string|null
	 */
	public function aliasToClass($alias,$namespace=null);
	
	/**
	 * @param string $class
	 * @param string $namespace	 
	 * @return string
	 */
	public function getClassOverwrite($class,$namespace=null);
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias,$namespace=null);
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($classOrAlias,$namespace=null);
	
	/**
	 * @param array $factories	 
	 */
	public function sortFactories(array &$factories);
	
	/**
	 * @param string $classOrAlias	 
	 * @param array $params
	 * @param string $namespace
	 * @return object	 
	 */
	public function instantiate($classOrAlias,array $params=array(),$namespace=null);

	/**
	 * @param object $object
	 * @param string $classOrAlias	 
	 * @param string $namespace
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array(),$namespace=null);
	
	/**	
	 * @param string $classOrAlias	 
	 * @param array $params
	 * @param $namespace
	 * @return object
	 */
	public function singleton($classOrAlias,array $params=array(),$namespace=null);
	
	/**
	 * 
	 * @param string $classOrAlias
	 * @param string $namespace	 
	 */
	public function removeSingleton($classOrAlias,$namespace=null);
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @param string $namespace
	 * @return object
	 */
	public function get($classOrAlias,array $params=array(),$namespace);
	
}
