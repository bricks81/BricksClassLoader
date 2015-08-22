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
					'anyClass' => 'BricksClassLoaderTest\TestObjectExtended',						
				),
			),
			'BricksClassLoaderTest' => array(
				'aliases' => array(					
					'BricksClassLoaderTest\TestObject' => 'BricksClassLoaderTest\TestObject2',
					'BricksClassLoaderTest\TestObjectExtended' => 'BricksClassLoaderTest\TestObjectExtended2',					
				),
			),
		),
	),
);