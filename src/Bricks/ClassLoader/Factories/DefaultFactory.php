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
use Bricks\ClassLoader\ClassLoaderServiceAwareInterface;
use Bricks\ClassLoader\ClassLoaderServiceInterface;

class DefaultFactory implements FactoryInterface, ClassLoaderServiceAwareInterface {
	
	/**
	 * @var integer
	 */
	protected $priority = 0;
	
	/**
	 * @var ClassLoaderServiceInterface
	 */
	protected $classLoaderService;
	
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
	 * @see \Bricks\ClassLoader\ClassLoaderServiceAwareInterface::setClassLoader()
	 */
	public function setClassLoaderService(ClassLoaderServiceInterface $classLoaderService){
		$this->classLoaderService = $classLoaderService;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\ClassLoaderServiceAwareInterface::getClassLoader()
	 */
	public function getClassLoaderService(){
		return $this->classLoaderService;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Bricks\ClassLoader\FactoryInterface::build()
	 */
	public function build($object,array $factoryParams=array()){
		return $object;
	}	
	
}