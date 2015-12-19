## About

Bricks ClassLoader is another approach to solve instantiating classes and it's dependencies. The target is to support the replacement of parts of an application
in a fast relyable way. You can make decisions between where the class will be instantiated and in which namespace the class will be loaded. 

## Features
- Singleton instances
- Programmable instantiators
- Programmable Factories
- Namespaces

## Requires
- bricks81/BricksConfig

### Installation

#### Using Composer

    php composer.phar require bricks81/bricks-class-loader

#### Activate Modules

Add the modules in your application.config.php:

```php
	// ...    
	'modules' => array(
    	// ...
    	'BricksConfig',	
    	'BricksClassLoader',
    	'Application',
    	// ...	
    ),
	// ...
```

## Configuration

### What you've to do

Add the configuration for your module:

```php
	// ...
	'BricksConfig' => array(
		// ...
		'__DEFAULT_NAMESPACE__' => array(
			'BricksClassLoader' => array(		
				'aliasMap' => array(
					'MyModule' => array(
						'alias' => 'A\Class',
						'A\Class' => 'Path\To\Class',
					),
				),	
				'classMap' => array(
					'Path\To\Class' => array(
						'instantiator' => 'Instantiator\Class',
						'factories' => array(
							'List\Of\Factories'
						),
					),
				),
			),			
		),
	),	
	// ...
```

### Example instantiate a class that could be replaced

This example will demonstrate the api that shouldn't change in future.

```php
	// ...
	// instantiate the class loader for your module
	$classLoader = $serviceManager->get('BricksClassLoader');

	// get a object
	$object = $classLoader->get('Path/To/Your/Class',array( // the factory parameters
		'AnyKey' => $anyVar,
	));

	// diffrent Namespace
	$classLoader->getConfig()->setNamespace('YourNamespace');
	$object = $classLoader->get('BricksPlugin.pluginClass');
	$classLoader->resetNamespace();

	// create a singleton
	$object = $classLoader->singleton('Path/To/Your/Class',array(
		'AnyKey' => $anyVar
	));

	$object = $classLoader->singleton('BricksPlugin.pluginClass');

	// after this you can call the singleton everywhere in the code as follows
	$object = $classLoader->singleton('Path/To/Your/Class');

	// ...
```

## Note

Hope you will enjoy it.