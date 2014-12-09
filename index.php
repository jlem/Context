<?php

require './vendor/autoload.php';

use Jlem\Context\ContextSet;
use Jlem\Context\ConfigurationSet;
use Jlem\Context\Settings;

$configs = [
	'default' => [
		'this' => true,
		'that' => false,
		'those' => ['orange', 'apple', 'banana']
	],
	'game' => [
		'starcraft2' => [
			'this' => true,
			'that' => false,
			'those' => ['protoss', 'terran', 'zerg']
		],
		'dota2' => [
			'this' => true,
			'that' => false,
			'those' => ['something', 'something', 'something']
		]
	],
	'user' => [
		'moderator' => [
			'this' => true,
			'that' => false,
			'those' => []
		],
		'admin' => [
			'this' => true,
			'that' => false,
			'those' => ['up', 'down']
		],
	],
	'country' => [
		'us' => [
			'this' => true,
			'that' => false,
			'those' => ['long', 'list', 'of', 'settings']
		],
		'ca' => [
			'this' => true,
			'that' => false,
			'those' => ['orange', 'apple', 'banana']
		],
	]
];

$contexts = [
	'game' => 'starcraft2',
	'user' => 'admin',
	'country' => 'us'
];

$Context = new ContextSet($contexts);
$Config = new ConfigurationSet($configs);
$Settings = new Settings($Context, $Config);

$Settings->load(['country!', 'game', 'user']);
var_dump($Settings->get('those'));

$Settings->load('country');
var_dump($Settings->get('those'));

$Settings->load('game');
var_dump($Settings->get('those'));