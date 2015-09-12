<?php
return array(
	'BricksConfig' => array(
		'BricksClassLoader' => array( // Module to configure
			'BricksClassLoader' => array( // namespace
				'defaultFactories' => array(),
				'classMap' => array(),
				'aliasMap' => array(
					'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
					'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
					'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory'
				)
			),
			'BricksClassLoaderTest' => array(
				'aliasMap' => array(
					'classLoaderClass' => 'BricksClassLoaderTest\TestObject',
					'deeper' => array(
						'class' => array(
							'hierarchy' => 'BricksClassLoaderTest\TestObject'
						)
					)
				)
			),
			'BricksClassLoaderTest2' => array(
				'classMap' => array(
					'Bricks\ClassLoader\ClassLoader' => 'BricksClassLoaderTest\TestObject2'
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