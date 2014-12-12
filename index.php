<?php

require './vendor/autoload.php';

use Jlem\Context\ContextSet;
use Jlem\Context\ConfigurationSet;
use Jlem\Context\Mergers\SimpleMerger;
use Jlem\Context\Mergers\ComplexMerger;
use Jlem\Context\Filters\CommonFilter;
use Jlem\Context\Filters\DefaultsFilter;
use Jlem\Context\Filters\ConditionFilter;
use Jlem\Context\Context;

use Jlem\ArrayOk\ArrayOk;


$configs = [
	'common' => [
		'costs_are_editable' => true,
		'default_play_cost' => '3.00',
		'allowed_scoring_types' => ['High Score', 'Cumulative Bucks', 'Cumulative Score'],
        'plaque_image_name' => 'plaque.jpg'
	],
    'defaults' => [
        'bbhd' => [
            'allowed_scoring_types' => ['High Score'],
        ],
        '[country:us][game:bbhd]'
    ]
];

$context = [
	'country' => 'ca',
	'user' => 'admin',
	'game' => 'bbhd'
];


$context = new ArrayOk($context);
$config = new ArrayOk($configs);

$Filter = new CommonFilter($config);


// In bootstrapping process
$Context = new Context();
//$Context->addFilter('override', new OverrideFilter($config, $context));
$Context->addFilter('common', new CommonFilter($config));
$Context->addFilter('defaults', new DefaultsFilter($config, $context));
$Context->addFilter('condition', new ConditionFilter($config, $context));

// In request process
//$Context->filter('override')->by('game.country');
//$Context->filter('defaults')->by('country.game.user');

$Context->filter('condition')
        ->when('country', 'ca')
        ->andWhen('game', 'bbhd')
        ->then(['costs_are_editable' => false]);

$Context->filter('condition')
        ->when('user', 'admin')
        ->andWhen('game', 'bbhd')
        ->then(['costs_are_editable' => true]);

//var_dump($Context->get());

$results = $Context->get();

var_dump($results);
