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
				'classMap' => array(),
				'aliasMap' => array(
					'BricksClassLoader' => array(
						'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
						'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
						'defaultFactories' => array(
							'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',
						),
					),					
				),			
			),			
		),	
		
	),
);