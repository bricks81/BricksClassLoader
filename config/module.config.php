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
				'defaultFactories' => array(),
				'classMap' => array( 
					'BricksClassLoader' => array( // Module to load from
						'BricksClassLoader' => array( // Namespace
							'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
							'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
							'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
							'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',							
						),
					),
				),			
			),
		),	
	),
);