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
				'classLoaderService' => 'Bricks\ClassLoader\ClassLoaderService',
				'defaultClassLoader' => 'Bricks\ClassLoader\ClassLoader\DefaultClassLoader',
				'defaultInstantiator' => 'Bricks\ClassLoader\Instantiators\DefaultInstantiator',
				'defaultFactories' => array(					
					'Bricks\ClassLoader\Factories\DefaultFactory',
					'Bricks\ClassLoader\Factories\ClassLoaderAwareFactory',					
				),
				/*
				'factories' => array(
					'Bricks\ClassLoader\ClassLoader' => array(
						'Bricks\ClassLoader\Factories\InitializerFactory'
					),
				),
				*/		
				/*
				'instantiator' => array(
					'Bricks\ClassLoader\ClassLoader' => 'Bricks\ClassLoader\Instantiators\DefaultInstantiator',
				),
				*/				
			),
		),			
	),
);