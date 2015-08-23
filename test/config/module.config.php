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
					'anyClassExtended' => 'BricksClassLoaderTest\TestObjectExtended',
					'deeper' => array(
						'class' => array(
							'hierarchy' => 'BricksClassLoaderTest\TestObject', 
						),
					),
				),
			),
			'BricksClassLoaderTest' => array(
				'aliases' => array(
					'BricksClassLoaderTest\TestObjectExtended' => 'BricksClassLoaderTest\TestObjectExtended2',
					'BricksClassLoaderTest\TestObject' => 'BricksClassLoaderTest\TestObject2',					
				),
			),
		),		
	),
);