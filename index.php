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


$config = [

    // 'common' is the fallback default value for all variants.
    // Useful for defining commonalities shared by all contexts

    'common' => [
        'show_tuner_truck_module' => true,
        'date_format' => 'M j, Y',
        'comment_query_criteria' => 'Acme\Comment\Criteria\Member', // Give this to a repository
        'show_comment_ip' => false
    ],

    // 'defaults' are the default configurations for each specific context value
    // These override 'common' configs, if present

    'defaults' => [
        'UK' => [
            'date_format' => 'j M, Y',
            'show_comment_ip' => false
        ],
        'Honda' => [
            'show_tuner_truck_module' => false
        ],
        'Admin' => [
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin', // Give this to a repository
            'show_comment_ip' => true
        ],
        'Moderator' => [
            'comment_query_criteria' => 'Acme\Comment\Criteria\Moderator' // Give this to a repository
        ]

    // 'conditions' represent configurations for arbitrary *combinations* of contexts
    // These override both 'defaults' and 'common', if the context of the request matches

    ],
    'conditions' => [
        'ford_uk' => new Condition(['country' => 'UK', 'manufacturer' => 'Ford'], 
                                   ['show_tuner_truck_module' => false])
    ]
];

$context = [
    'userType' => 'Admin',     // maybe get this from Session
    'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
    'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
];


$Context = new Context($context);
    
$Context->addFilter('common', new CommonFilter($config));
$Context->addFilter('defaults', new DefaultsFilter($config));
$Context->addFilter('conditions', new ConditionFilter($config));

// In request process
$results = $Context->get();

var_dump($results);
