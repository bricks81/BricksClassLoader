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
				'defaultInstantiator' => 'Bricks\ClassLoader\Instantiators\DefaultInstantiator',
				'defaultFactories' => array(
					'Bricks\ClassLoader\Factories\ClassLoaderAwareFactory',
					'Bricks\ClassLoader\Factories\ClassLoaderServiceAwareFactory',
				),
				'aliasMap' => array(
					'BricksClassLoader' => array(
						'defaultClassLoader' => 'Bricks\ClassLoader\ClassLoader\DefaultClassLoader',
					),
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