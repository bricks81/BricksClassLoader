<?php
return array(
	'BricksConfig' => array(
		'__DEFAULT_NAMESPACE__' => array(
			'BricksClassLoader' => array(
				'classLoaderService' => 'Bricks\ClassLoader\ClassLoaderService',
				'defaultInstantiator' => 'Bricks\ClassLoader\Instantiators\DefaultInstantiator',
				'defaultFactories' => array(
					'Bricks\ClassLoader\Factories\ClassLoaderServiceAwareFactory',
					'Bricks\ClassLoader\Factories\ClassLoaderAwareFactory',
				),
				'classMap' => array(),
				'aliasMap' => array(
					'defaultClassLoader' => 'Bricks\ClassLoader\ClassLoader\DefaultClassLoader',
				),
			),
		),
		'BricksClassLoaderTest' => array(
			'BricksClassLoader' => array(
				'aliasMap' => array(
					'defaultClassLoader' => 'BricksClassLoaderTest\TestObject',
					'deeper' => array(
						'class' => array(
							'hierarchy' => 'BricksClassLoaderTest\TestObject'
						),
					),
				),
			),
		),
		'BricksClassLoaderTest2' => array(
			'BricksClassLoader' => array(
				'classMap' => array(
					'BricksClassLoaderTest\TestObject2' => 'BricksClassLoaderTest\TestObject3',
				),
				'aliasMap' => array(					
					'deeper' => array(
						'class' => array(
							'hierarchy' => 'BricksClassLoaderTest\TestObject2'
						)	
					)
				)
			)
		)
	)
);