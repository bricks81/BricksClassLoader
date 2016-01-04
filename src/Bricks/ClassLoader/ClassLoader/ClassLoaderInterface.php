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

namespace Bricks\ClassLoader\ClassLoader;

interface ClassLoaderInterface {
	
	/**
	 * @return string
	 */
	public function getNamespace();
	
	/**
	 * @param string $classOrAlias
	 * @param object $object
	 */
	public function setInstance($classOrAlias,$object);
	
	/**
	 * @param string $classOrAlias
	 * @return object
	 */
	public function getInstance($classOrAlias);
	
	/**
	 * @param string $classOrAlias
	 */
	public function unsetInstance($classOrAlias);
	
	/**
	 * @return array
	 */
	public function getAliasMap();
	
	/**
	 * @return array
	 */
	public function getClassMap();
	
	/**
	 * @param string $alias
	 * @return string|null
	 */
	public function aliasToClass($alias);
	
	/**
	 * @param string $class
	 * @return string
	 */
	public function getClassOverwrite($class);
	
	/**
	 * @param string $classOrAlias
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias);
	
	/**
	 * @param string $classOrAlias
	 * @return array
	 */
	public function getFactories($classOrAlias);
	
	/**
	 * @param array $factories
	 */
	public function sortFactories(array &$factories);
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function instantiate($classOrAlias,array $params=array());
	
	/**
	 * @param object $object
	 * @param string $classOrAlias
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array());
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function singleton($classOrAlias,array $params=array());
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @return object
	 */
	public function get($classOrAlias,array $params=array());
	
}