<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\ClassLoader;

interface ClassLoaderAwareInterface {
	
	/**
	 * @param ClassLoaderInterface|ClassLoader $classLoader
	 */
	public function setClassLoader($classLoader);
	
	/**
	 * @return ClassLoaderInterface
	 */
	public function getClassLoader();
	
}