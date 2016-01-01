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
	 * @var string
	 */
	protected $module;
	
	/**
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * @param string $moduleName
	 */
	public function setModule($moduleName){
		$this->module = $moduleName;
	}
	
	/**
	 * @return string
	 */
	public function getModule(){
		return $this->module;
	}
	
	/**
	 * @param string $namespace
	 */
	public function setNamespace($namespace){
		$this->namespace = $namespace;
	}
	
	/**
	 * @return string
	 */
	public function getNamespace(){
		return $this->namespace;
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getAliasMap($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->getAliasMap($namespace);
	}
	
	/**
	 * @param string $namespace
	 * @return array
	 */
	public function getClassMap($namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->getClassMap($namespace);
	}
	
	/**
	 * @param string $alias
	 * @param string $namespace
	 * @return string|null
	 */
	public function aliasToClass($alias,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->aliasToClass($alias,$namespace);
	}
	
	/**
	 * @param string $class
	 * @param string $namespace
	 * @return string
	 */
	public function getClassOverwrite($class,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->getClassOverwrite($class,$namespace);
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return InstantiatorInterface
	 */
	public function getInstantiator($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->getInstantiator($classOrAlias,$namespace);
	}
	
	/**
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @return array
	 */
	public function getFactories($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->getFactories($classOrAlias,$namespace);
	}
	
	/**
	 * @param array $factories
	 */
	public function sortFactories(array &$factories){
		return $this->getClassLoaderService()->sortFactories($factories);
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @param string $namespace
	 * @return object
	 */
	public function instantiate($classOrAlias,array $params=array(),$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->instantiate($classOrAlias,$params,$namespace);
	}
	
	/**
	 * @param object $object
	 * @param string $classOrAlias
	 * @param string $namespace
	 * @param array $params
	 */
	public function factory($object,$classOrAlias,array $params=array(),$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->factory($object,$classOrAlias,$params,$namespace);
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @param $namespace
	 * @return object
	 */
	public function singleton($classOrAlias,array $params=array(),$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->singleton($classOrAlias,$params,$namespace);
	}
	
	/**
	 *
	 * @param string $classOrAlias
	 * @param string $namespace
	 */
	public function removeSingleton($classOrAlias,$namespace=null){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->removeSingleton($classOrAlias,$namespace);
	}
	
	/**
	 * @param string $classOrAlias
	 * @param array $params
	 * @param string $namespace
	 * @return object
	 */
	public function get($classOrAlias,array $params=array(),$namespace){
		$namespace = $namespace?:$this->getNamespace();
		return $this->getClassLoaderService()->get($classOrAlias,$params,$namespace);
	}
	
}