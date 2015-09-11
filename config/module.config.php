<?php

return array(
	'service_manager' => array(
		'factories' => array(
			'BricksClassLoader' => 'Bricks\ClassLoader\ServiceManager\ClassLoaderFactory',
		),
	),
	'BricksConfig' => array(
		'BricksClassLoader' => array( // Module to configure	
			'BricksClassLoader' => array( // Namespace
				'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
				'defaultFactories' => array(
					'Bricks\ClassLoader\DefaultFactory'
				),
				'classMap' => array( 
					'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
					'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
					'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
					'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',						
					
				),
				'aliasMap' => array(
					'user' => array(
						'userClass' => 'Default\User\Class',
						'deeper' => array(
							'useClass' => 'Other\User\Class', 
						),
					),
				),			
			),
			'BricksMapper' => array(
				'classMap' => array(
					'Bricks\Mapper\Mapper' => 'Bricks\Mapper\Mapper',
				),
			),
		),	
		
	),
);