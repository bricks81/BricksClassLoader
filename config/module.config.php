<?php

return array(
	'service_manager' => array(
		'factories' => array(
			'BricksClassLoader' => 'Bricks\ClassLoader\ServiceManager\ClassLoaderFactory',
		),
	),
	'BricksConfig' => array(
		'BricksClassLoader' => array( // Module to configure	
			'BricksClassLoader' => array( // Module Namespace
				'aliasMap' => array(
					'BricksClassLoader' => array(
						'classLoaderClass' => 'Bricks\ClassLoader\ClassLoader',
						'defaultInstantiator' => 'Bricks\ClassLoader\DefaultInstantiator',
						'defaultFactories' => array(
							'defaultFactory' => 'Bricks\ClassLoader\DefaultFactory',
						),
					),
				),	
				'classMap' => array(
					'BricksClassLoader' => array(
						/*						
						'My/Class' => array(
							'class' => 'My/Class',
							'instantiator' => 'My/Class',
							'factories' => array(
								'order' => 'My/Factory',
							),
						),
						*/
					),					
				),		
			),			
		),	
		
	),
);