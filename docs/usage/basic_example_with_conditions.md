Now let's say you've discovered some of your business rules aren't complete. It turns out that you want a different logo/header in the Ford UK section. 

This specific configuration can be achieved by adding a Condition Filter.

```php

// Define your config data

$configData = [

    'common' => [
        'date_format' => 'M j, Y',
        'show_comment_ip' => false
        'comment_query_criteria' => 'Acme\Comment\Criteria\Member', 
        'show_tuner_truck_module' => true,
        'manually_approve_comments' => false
    ],

    'defaults' => [
        'UK' => [
            'date_format' => 'j M, Y',
            'show_comment_ip' => false,
            'show_tuner_truck_module' => false
        ],
        'Ford' => [
            'manually_approve_comments' => true,
            'logo' => 'ford.png'
        ]
        'Honda' => [
            'show_tuner_truck_module' => false,
            'logo' => 'honda.png'
        ],
        'Admin' => [
            'show_comment_ip' => true,
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin'
        ],
        'Moderator' => [
            'comment_query_criteria' => 'Acme\Comment\Criteria\Moderator'
        ]
    ],

    /* 
    | conditions represent configurations for arbitrary combinations
    | of contexts. These override both 'defaults' and 'common', 
    | if the context of the request matches.
    |
    */

    'conditions' => [
        'ford_uk' => new Condition(['country' => 'UK', 'manufacturer' => 'Ford'], 
                                   ['logo' => 'ford_uk.png'])
    ]
];


$contextData = [
    'user' => 'Admin',
    'country' => 'UK'
    'manufacturer' => 'Ford',
];


$Context = new Context($contextData);
$Context->addFilter('common', new CommonFilter($configData));
$Context->addFilter('defaults', new DefaultsFilter($configData));
$Context->addFilter('conditions', new ConditionsFilter($configData));


$filteredConfig = $Context->get();
```

Based on the context defined in step 2, the above call will return the following array:

```php
[
    'date_format' => 'j M, Y',              // Determined by the 'UK' default
    'show_comment_ip' => false,             // Determined by the 'UK' default
    'comment_query_criteria' => 'Acme\Comment\Criteria\Admin', // Determined by the 'Admin' default
    'show_tuner_truck_module' => false,     // Determined by the 'UK' default
    'manually_approve_comments' => true,    // Determined by the 'Ford' default
    'logo' => 'ford_uk.png'                 // Determined by the 'ford_uk' condition
]
```
