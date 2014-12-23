You'll likely do all of this early in the bootstrapping process, but you can really do it at any time in the request cycle before you need to use the data

# 1. Define your configuration

```php
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
```


# 2. Define the context (order matters!)

A note about order: the order determines the order in which Context and its filters apply `array_merge`. It override from first to last, and falls back from last to first. That is, given the below in the `defaults` filter, `'Ford'` overrides `'UK'` which overrides `'Admin'`. However, if there is any configuration key defined in `'Admin'` that isn't defined in either `'UK'` or `'Ford'`, then it still gets included in the config array, thanks to `array_merge`

```php
$context = [
    'user' => 'Admin',
    'country' => 'UK',
    'manufacturer' => 'Ford'
];
```


# 3. Initalize Context, add filters (order matters!)

```php
$Context = new Context($context);
$Context->addFilter('common', new CommonFilter($config));
$Context->addFilter('defaults', new DefaultsFilter($config));
$Context->addFilter('conditions', new ConditionsFilter($config));
```


# 4. Get your filtered config data

```php
$filteredConfig = $Context->get();
```


Based on the context defined in step 2, the above call will return the following array:

```php
[
    'show_tuner_truck_module' => false,     // Determined by the 'ford_uk' condition
    'date_format' => 'j M, Y'               // Determined by the 'UK' default
    'comment_query_criteria' => 'Acme\Comment\Criteria\Admin' // Determined by the 'Admin' default
    'show_comment_ip' => false               // Determined by the 'UK' default
]
```

But wait, how come the UK default for `'show_comment_ip'` trumped the same configuration setting by the `'Admin'` default sibling? Because of the order in which the context was defined in step #2. Even though behind the scenes Context used both the `'UK'` and `'Admin'` defaults, the `'UK'` context was set *after* the `'Admin'` context, so it takes precedence. We can change this order to get different results, depending on our needs:

