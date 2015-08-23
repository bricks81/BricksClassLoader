<?php

return array(
	'BricksClassLoader' => array(
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
				'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
				'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',
				'defaultFactories' => array(),
			),
		),
		'BricksClassLoaderTest' => array(
			'BrickClassLoaderTest' => array(
				'anyClass' => 'BricksClassLoaderTest\TestObject',
				'anyClassExtended' => 'BricksClassLoaderTest\TestObjectExtended',
				'deeper' => array(
					'class' => array(
						'hierarchy' => 'BricksClassLoaderTest\TestObject',
					),
				),
			),		
			'BricksClassLoaderTest2' => array(
				'BricksClassLoaderTest\TestObjectExtended' => 'BricksClassLoaderTest\TestObjectExtended2',
				'BricksClassLoaderTest\TestObject' => 'BricksClassLoaderTest\TestObject2',
				'deeper' => array(
					'class' => array(
						'hierarchy' => 'BricksClassLoaderTest\TestObject2',
					),
				),
			),
		),
	),
	'BricksConfig' => array(
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(				
				
			),	
		),		
	),
);