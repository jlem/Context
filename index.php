<?php

require './vendor/autoload.php';

use Jlem\Context\ContextSet;
use Jlem\Context\ConfigurationSet;
use Jlem\Context\Mergers\SimpleMerger;
use Jlem\Context\Mergers\ComplexMerger;
use Jlem\Context\Context;

use Jlem\ArrayOk\ArrayOk;


$configs = [
	'common' => [
		'costs_are_editable' => true,
		'default_play_cost' => '3.00',
		'allowed_scoring_types' => ['High Score', 'Cumulative Bucks', 'Cumulative Score'],
        'plaque_image_name' => 'plaque.jpg'
	],
	'overrides' => [
		'ca' => [
            'bbhd' => [
                'costs_are_editable' => false,
            ]
		]
    ],
    'defaults' => [
        'bbhd' => [
            'allowed_scoring_types' => ['High Score'],
            'costs_are_editable' => 'default'
        ],
        'admin' => [
            'allowed_scoring_types' => ['Low Score'],
            'costs_are_editable' => 'smurfs'
        ],
        'operator' => [
            'game_machine_repo' => 'OperatorGameMachineRepository'
        ]
    ]
];


$contexts = [
	'user' => 'operator',
	'game' => 'bbhd',
	'country' => 'ca'
];


$Context = new ContextSet($contexts);
$Config = new ConfigurationSet($configs);
$Merger = new ComplexMerger($Context, $Config);
$Context = new Context($Merger);


$Context->load('country.game.user');
$actual = $Context->get();
var_dump($actual);
