<?php

return array(
	'service_manager' => array(
		'factories' => array(
			'BricksClassLoader' => 'Bricks\Classloader\ServiceManager\ClassLoaderFactory',
		),
	),
	'BricksClassLoader' => array(
		/*
		 	'module-to-configure' => array(
		 		'name-of-specific-module' => array(
		 			'alias-of-class' => array(
		 				'traversed.backward.classname' => array(
		 					'instantiators' => array(
		 						'instantiator',
		 						array(
		 							'instantiator' => 'instantiator-class-name',
		 							'class' => false,
		 							'method' => false
		 						),
		 					),
		 					'factories' => array(
		 						'factory',
			 					array(
			 						'factory' => 'factory-class-name',
			 						'class' => '__CLASS__',
			 						'method' => '__METHOD__',
			 					),
			 				),
		 				),
		 			),
		 		),
		 	),
		 */	
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
			),
		),
	),
	'BricksConfig' => array(
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(				
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
				'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
				'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',
				'defaultFactories' => array(
					/*
					'factory',
					array(
						'factory' => 'factory',
						'class' => '__CLASS__',
						'method' => '__METHOD__',						
					),
					*/
				),
			),
		),
	),
);