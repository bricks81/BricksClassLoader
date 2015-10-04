<?php
return array(
	'BricksConfig' => array(
		'BricksClassLoader' => array( // Module to configure
			'BricksClassLoader' => array( // namespace
				'classMap' => array(),
				'aliasMap' => array(
					'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
					'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
					'defaultFactories' => array(
						'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory'
					),
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
					'Bricks\ClassLoader\ClassLoader' => array(
						'class' => 'BricksClassLoaderTest\TestObject2',
						//'instantiator',
						//'factories',
					),
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