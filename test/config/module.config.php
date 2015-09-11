<?php
return array(
	'BricksConfig' => array(
		'BricksClassLoader' => array( // Module to configure
			'BricksClassLoader' => array( // namespace
				'defaultFactories' => array(),
				'classMap' => array(
					'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
					'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
					'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory'
				),
				'aliasMap' => array()
			),
			'BricksClassLoaderTest' => array(
				'aliasMap' => array(
					'anyClass' => 'BricksClassLoaderTest\TestObject',
					'anyClassExtended' => 'BricksClassLoaderTest\TestObjectExtended',
					'deeper' => array(
						'class' => array(
							'hierarchy' => 'BricksClassLoaderTest\TestObject'
						)
					)
				)
			)
		),
		'BricksClassLoaderTest2' => array(
			'classMap' => array(
				'BricksClassLoaderTest\TestObjectExtended' => 'BricksClassLoaderTest\TestObjectExtended2',
				'BricksClassLoaderTest\TestObject' => 'BricksClassLoaderTest\TestObject2'
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
);