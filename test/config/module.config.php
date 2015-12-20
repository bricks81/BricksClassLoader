<?php
return array(
	'BricksConfig' => array(
		'__DEFAULT_NAMESPACE__' => array(
			'BricksClassLoader' => array(
				'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				'defaultInstantiator' => 'Bricks\ClassLoader\Instantiators\DefaultInstantiator',
				'defaultFactories' => array(
					'Bricks\ClassLoader\Factories\DefaultFactory',
					'Bricks\ClassLoader\Factories\ClassLoaderAwareFactory',
				),
				'classMap' => array(),
				'aliasMap' => array(
					'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
				),
			),
		),
		'BricksClassLoaderTest' => array(
			'BricksClassLoader' => array(
				'aliasMap' => array(
					'classLoaderClass' => 'BricksClassLoaderTest\TestObject',
					'deeper' => array(
						'class' => array(
							'hierarchy' => 'BricksClassLoaderTest\TestObject'
						)
					)
				)
			),
		),
		'BricksClassLoaderTest2' => array(
			'BricksClassLoader' => array(
				'classMap' => array(
					'Bricks\ClassLoader\ClassLoader' => 'BricksClassLoaderTest\TestObject2',
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