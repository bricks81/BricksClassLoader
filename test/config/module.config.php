<?php

return array(
	'BricksClassLoader' => array(
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
			),
		),
		'BricksTest' => array(
			'BricksTest' => array(
				'anyClass' => 'BricksClassLoaderTest\TestObject',
			),
			'BricksTestExtended' => array(
				'anyClass' => 'BricksClassLoaderTest\TestObjectExtended',
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
				),
			),
		),
	),
);