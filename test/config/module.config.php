<?php

return array(
	'BricksConfig' => array(		
		'BricksClassLoader' => array( // Module to configure
			'BricksClassLoader' => array( // Config namespace
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
					'BricksClassLoaderTest' => array(
						'BricksClassLoaderTest' => array(
							'anyClass' => 'BricksClassLoaderTest\TestObject',
							'anyClassExtended' => 'BricksClassLoaderTest\TestObjectExtended',
							'deeper' => array(
								'class' => array(
									'hierarchy' => 'BricksClassLoaderTest\TestObject',
								),
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
		),
	),	
);