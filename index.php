<?php

require './vendor/autoload.php';

use Jlem\Context\Filters\CommonFilter;
use Jlem\Context\Filters\DefaultsFilter;
use Jlem\Context\Filters\ConditionFilter;
use Jlem\Context\Filters\Condition;
use Jlem\Context\Config;

use Jlem\ArrayOk\ArrayOk;


$config = [

    // 'common' is the fallback default value for all variants.
    // Useful for defining commonalities shared by all contexts

    'common' => [
        'show_tuner_truck_module' => true,
        'date_format' => 'M j, Y',
        'comment_query_criteria' => 'Acme\Comment\Criteria\Member', // Give this to a repository
        'show_comment_ip' => false,
        'some_vals' => ['one', 'two', 'three']
    ],

    // 'defaults' are the default configurations for each specific context value
    // These override 'common' configs, if present

    'defaults' => [
        'UK' => [
            'date_format' => 'j M, Y',
            'show_comment_ip' => false,
            'some_vals' => ['five']
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
        'admin_uk' => new Condition(['country' => 'UK', 'user' => 'Admin'],
                                    ['show_tuner_truck_module' => 'admin_uk']),
        'ford_uk' => new Condition(['country' => 'UK', 'manufacturer' => 'Ford'], 
                                   ['show_tuner_truck_module' => 'ford_uk']),
    ]
];

$context = [
    'user' => 'Admin',     // maybe get this from Session
    'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
    'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
];


$Config = new Config($context);
    
$Config->addFilter('common', new CommonFilter($config));
$Config->addFilter('defaults', new DefaultsFilter($config));
$Config->addFilter('conditions', new ConditionFilter($config));

// In request process
$results = $Config->load();

var_dump($results);
