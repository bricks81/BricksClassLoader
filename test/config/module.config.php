<?php

return array(	
	'BricksConfig' => array(
		'BricksClassLoader' => array(
			'BricksClassLoader' => array(				
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultClassLoaderClass' => 'Bricks\ClassLoader\DefaultClassLoader',
				'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
				'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',
				'defaultFactories' => array(
				),
				'aliases' => array(										
					'anyClass' => 'BricksClassLoaderTest\TestObject',						
					'BricksClassLoaderTest\TestObject' => 'BricksClassLoaderTest\TestObject2',
				),
			),
			'BricksClassLoaderTest' => array(
				'aliases' => array(
					'BricksClassLoaderTest\TestObjectExtended' => 'BricksClassLoaderTest\TestObjectExtended2',
					'anyClass' => 'BricksClassLoaderTest\TestObjectExtended',
				),
			),
		),		
	),
);