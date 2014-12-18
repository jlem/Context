<?php

require './vendor/autoload.php';

use Jlem\Context\ContextSet;
use Jlem\Context\ConfigurationSet;
use Jlem\Context\Mergers\SimpleMerger;
use Jlem\Context\Mergers\ComplexMerger;
use Jlem\Context\Filters\CommonFilter;
use Jlem\Context\Filters\DefaultsFilter;
use Jlem\Context\Filters\ConditionFilter;
use Jlem\Context\Filters\Condition;
use Jlem\Context\Context;

use Jlem\ArrayOk\ArrayOk;


$configs = [
    'common' => [
        'what' => 'now',
    ],
    'defaults' => [
        'bbhd' => [
            'what' => 'then',
            'hey' => 'ya'
        ],
        'ca' => [
            'what' => 'are you doing?',
            'hey' => 'there'
        ],
        'admin' => [
            'yolo' => 'swaggins'
        ]
    ],
    'conditions' => [
    ]
];

$context = [
	'country' => 'us',
	'user' => 'admin'
];


$context = new ArrayOk($context);
$config = new ArrayOk($configs);


$Context = new Context($context);
$Context->addFilter('common', new CommonFilter($config));
$Context->addFilter('defaults', new DefaultsFilter($config));
$Context->addFilter('conditions', new ConditionFilter($config));
$Context->getContext()->append('bbs', 'game');
// In request process
$results = $Context->get();

var_dump($results);
