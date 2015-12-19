<?php

return array(
	'service_manager' => array(
		'factories' => array(
			'BricksClassLoader' => 'Bricks\ClassLoader\ServiceManager\ClassLoaderFactory',
		),
	),
	'BricksConfig' => array(
		'__DEFAULT_NAMESPACE__' => array(
			'BricksClassLoader' => array(
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
				'defaultFactories' => array(					
					'Bricks\ClassLoader\Factories\DefaultFactory',
					'Bricks\ClassLoader\Factories\ClassLoaderAwareFactory',
					'Bricks\ClassLoader\Factories\InitializerFactory',
				),				
			),
		),			
	),
);