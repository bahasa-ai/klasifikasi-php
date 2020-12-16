# Klasifikasi for PHP
Official [Klasifikasi](https://klasifikasi.com/) API Client Library

## Requirement
- PHP >= 7.2.5
## Installation
Install klasifikasi-php with composert:
```bash
composer require bahasaai/klasifikasi-php
```

## Quick Start

You will need valid `clientId` & `clientSecret` of your model. You can get those
from credential section at your model page, which is both unique per model.
```php

use klasifikasi\Klasifikasi;

$klasifikasiInstance = Klasifikasi::build([
    [
        'clientId' => 'client-id-1',
        'clientSecret' => 'client-id-2'
    ]
]);
```
you can pass multiple `clientId` & `clientSecrert` too
```php
$klasifikasiInstance = Klasifikasi::build([
    [
        'clientId' => 'client-id-1',
        'clientSecret' => 'client-secret-1'
    ],
    [
        'clientId' => 'client-id-2',
        'clientSecret' => 'client-secret-2'
    ]
]);
```
## Classify
You will need you model `publicId` to start classifying with your model. You can get your model `publicId` from your model page, or you can get it from here :
```php
foreach ($klasifikasiInstance->getModels() as $publicId => $model) {
  echo $publicId;
}
```
Classifying example
```php
$result = $publicId->classify('publicId', 'query');
/**
 * $result example = array[
 *  'result' => array[
 *    [
 *      'label' => 'tag 1',
 *      'score' => 0.53
 *    ],
 *    [
 *      'label' => 'tag 2',
 *      'score' => 0.23
 *    ]
 *  ]
 * ]
*/
```

## Logs
You can get your classifying logs based on your model `publicId`
```php
$startedAtString = '10 December 2020';

$endedAtString = '16 December 2020';

$logs = $instance->logs('publicId', [
    'startedAt' => new DateTime($startedAtString),
    'endedAt' => new DateTime($endedAtString),
    'take' => 10
    'skip' => 1
]);
/**
 * $logs example = array[
 *  'histories' => array[
 *    [
 *      'id' => 1,
 *      'createdAt' => '2020-12-15T11:13:12+07:00',
 *      'query' => 'query',
 *      'modelResult' => array[
 *        [
 *          'label' => 'tag 1',
 *          'score' => 0.53
 *        ],
 *        [
 *          'label' => 'tag 2',
 *          'score' => 0.23
 *        ]
 *      ]
 *    ],
 *    ...
 *  ],
 *  'length' => int
 * ]
*/

```

## Error

All the function above will throw an Exception if something bad happen. Always run
each function inside `try` & `catch` block