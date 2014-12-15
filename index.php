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
];

$context = [
	'country' => 'ca',
	'game' => 'bbhd',
	'user' => 'operator'
];


$context = new ArrayOk($context);
$config = new ArrayOk($configs);


$Context = new Context();
$Context->addFilter('common', new CommonFilter($config));
$Context->addFilter('defaults', new DefaultsFilter($config, $context));
$Context->addFilter('conditions', new ConditionFilter($config, $context));

// In request process
$results = $Context->get();

var_dump($results);
