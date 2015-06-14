<?php
/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * @link https://github.com/bricks81/BricksClassLoader
 * @license http://www.gnu.org/licenses/ (GPLv3)
 */
namespace Bricks\Classloader\ServiceManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;

class ClassLoaderFactory implements FactoryInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $sl){
		$classLoaderConfig = $sl->get('Config')['BricksClassLoader'];		
		$config = $sl->get('BricksConfig')->getConfig('BricksClassLoader');		
		$class = $config->get('classLoaderClass');		
		$service = new $class($config,new Config($classLoaderConfig));
		$service->setServiceLocator($sl);
		return $service;
	}
	
}