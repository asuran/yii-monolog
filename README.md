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