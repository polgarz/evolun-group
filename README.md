Group module for Evolun
=======

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist polgarz/evolun-group "@dev"
```

or add

```
"polgarz/evolun-group": "@dev"
```

to the require section of your `composer.json` file.

Migration
-----
```
php yii migrate/up --migrationPath=@vendor/polgarz/evolun-group/migrations
```

Configuration
-----

```php
'modules' => [
    'group' => [
        'class' => 'evolun\group\Module',
        'typeList' => [
            'professional' => 'Professional groups',
            'maintenance'  => 'Maintenance groups',
        ],
    ],
],
```