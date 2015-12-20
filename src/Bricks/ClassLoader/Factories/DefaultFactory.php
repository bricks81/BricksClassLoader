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

namespace Bricks\ClassLoader\Factories;

use Bricks\ClassLoader\ClassLoader;

class DefaultFactory implements FactoryInterface {
	
	/**
	 * @var integer
	 */
	protected $priority = 0;
	
	/**
	 * @var ClassLoader
	 */
	protected $classLoader;
	
	public function __construct(ClassLoader $classLoader,$priority=0){
		$this->setClassLoader($classLoader);
		$this->setPriority($priority);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::getPriority()
	 */
	public function getPriority(){
		return $this->priority;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::setPriority()
	 */
	public function setPriority($priority){
		$this->priority = $priority;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderAwareInterface::setClassLoader()
	 */
	public function setClassLoader(ClassLoader $classLoader){
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
	 * @see \Bricks\ClassLoader\FactoryInterface::build()
	 */
	public function build($object,array $factoryParams=array()){
		return $object;
	}	
	
}