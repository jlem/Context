You'll likely do all of this early in the bootstrapping process, but you can really do it at any time in the request cycle before you need to use the data

```php

// Define your config data

$configData = [

    /* 
    | common contains all of the configuration keys/values
    | which are common to all requests. That is, they
    | are not affected by the request context.
    | 
    */
  
    'common' => [
        'date_format' => 'M j, Y',
        'show_comment_ip' => false
        'comment_query_criteria' => 'Acme\Comment\Criteria\Member', 
        'show_tuner_truck_module' => true,
    ],


    /* 
    | defaults are the default configurations for each possible
    | context value, and they override anything in 'common'. 
    | Each context value is represented as a config key.
    |
    */
  

    'defaults' => [
        'UK' => [
            'date_format' => 'j M, Y',
            'show_comment_ip' => false
        ],
        'Honda' => [
            'show_tuner_truck_module' => false
        ],
        'Admin' => [
            'show_comment_ip' => true
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin',
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
                                   ['show_tuner_truck_module' => false])
    ]
];



// Define the context data (order matters)

$contextData = [
    'user' => 'Admin',
    'country' => 'UK',
    'manufacturer' => 'Ford'
];



// Initialize Context, add filters (order matters)

$Context = new Context($contextData);
$Context->addFilter('common', new CommonFilter($configData));
$Context->addFilter('defaults', new DefaultsFilter($configData));
$Context->addFilter('conditions', new ConditionsFilter($configData));



// Retrieve your data when you need it

$filteredConfig = $Context->get();
```

Based on the context defined in step 2, the above call will return the following array:

```php
[
    'date_format' => 'j M, Y'               // Determined by the 'UK' default
    'show_comment_ip' => false               // Determined by the 'UK' default
    'comment_query_criteria' => 'Acme\Comment\Criteria\Admin' // Determined by the 'Admin' default
    'show_tuner_truck_module' => false,     // Determined by the 'ford_uk' condition
]
```

But wait, how come the UK default for `'show_comment_ip'` trumped the same configuration setting by the `'Admin'` default sibling? Because of the order in which the context was defined in step #2. Even though behind the scenes Context used both the `'UK'` and `'Admin'` defaults, the `'UK'` context was set *after* the `'Admin'` context, so it takes precedence. We can change this order to get different results, depending on our needs:

