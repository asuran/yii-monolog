# Monolog integration for Yii 1.x

Inspired by [enlitepro/enlite-monolog](https://github.com/enlitepro/enlite-monolog) and [smartapps-fr/yii-monolog](https://github.com/smartapps-fr/yii-monolog)

## Install

The recommended way to install is through composer from command line.

```
composer require asuran/yii-monolog
```

## Usage

1. Add the component to the preload list
   
```php
<?php 

return [
    'preload' => [
        'monolog',
    ],
];
```
   
2. Configure the component

```php
<?php
return [
    'components' => [
        'monolog' => [
            'class' => 'YiiMonolog\MonologComponent',
            'name' => 'MyApplication',
            'handlers' => [
                'file' => [
                    'class' => 'Monolog\Handler\StreamHandler',
                    'stream' => '/runtime/app.log',
                    'formatter' => 'Monolog\Formatter\LineFormatter',
                ],
            ],
            'processors' => [
                'Monolog\Processor\ProcessIdProcessor',
            ],
        ],
    ],
];
```

3. Add log route

```php
<?php
return [
    'components' => [
        'log' => [
            'class' => 'CLogRouter',
            'routes' => [
                'monolog' => [
                    'class' => 'YiiMonolog\MonologLogRoute',
                ],
            ],
        ],
    ],
];

```

4. Add exception handler
```php
<?php
return [
    'components' => [
        'errorHandler' => [
            'class' => 'YiiMonolog\MonologErrorHandler',
            'errorAction' => 'site/error',
        ],
    ],
];
```

5. Use it
```php
<?php

# Using info with variables
Yii::log("A new store user did a self-register on the system", \Monolog\Logger::INFO, [
                    'id' => $form->usuario->id,
                    'category' => 'users',
                ]);

# Using debug with variables
Yii::log("Info about the new store user created", \Monolog\Logger::DEBUG, [
                    'id' => $form->usuario->id,
                    'cnpj' => $form->usuario->usu_login,
                    'email' => $form->usuario->usu_email,
                    'category' => 'users',
                    ]);

# Using error without variables.
# System will convert the 3th parameter in "category" parameter and store it as an array of variables
# This first example keeps the Yii::log compatible with old logs in your system
Yii::log("File not found", CLogger::LEVEL_ERROR, "command.gearman");

# This another example allows to use all monolog levels for messages
Yii::log("File not found", \Monolog\Logger::ERROR, "command.gearman");  

```