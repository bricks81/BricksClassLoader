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
	'BricksClassLoader' => array(
		'YourModule' => array(
			'YourModule' => array( // as your namespace
				'classAlias' => 'YourModule\Class'
				'setOfClasses' => array(
					'class1' => 'YourModule\ClassOne',
					'class2' => 'YourModule\ClassTwo',
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
	$classLoader = $serviceManager->get('BricksClassLoader')->getClassLoader('YourModule');

	// get a object
	$object = $classLoader->newInstance(__CLASS__,__FUNCTION__,'classAlias','YourNamespace',array( // the factory parameters
		'AnyKey' => $anyVar,
	));

	// create a singleton
	$object = $classLoader->getSingleton(__CLASS__,__FUNCTION__,'classAlias','YourNamespace',array(
		'AnyKey' => $anyVar
	));

	// after this you can call the singleton everywhere in the code as follows
	$object = $classLoader->getSingleton(__CLASS__,__METHOD__,'classAlias');

	// ...
```

## Note

Hope you will enjoy it.