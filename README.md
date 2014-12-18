A very simple library for juggling configuration settings based on a given combination of multiple contexts

# Usecase

Suppose you have a page that shows information about various car models or parts from different manufacturers (with comments), but *depending on which manufacturer it is, which country you're from, and which user group you are* the format, layout, and information on this page can vary a little bit:

* Ford has a section about tuner trucks that Honda doesn't have
* Except Ford UK doesn't show this because tuner trucks aren't as popular in the UK
* All manufacturer pages in the UK show dates formatted as Day Month, Year
* Admins can see all comments (including pending/soft-deleted) as well IP addresses associated with comments
* However, let's pretend UK has privacy laws that prohibits displaying IP addresses to non-employees/owners
* Moderators can see all public comments as well as see/approve pending comments
* Regular users can only see publically visible comments

Whew, that's a lot of highly specific business rules, don't you think? 

If you didn't care about maintainability, you may be tempted to do something like this:

```php
if ($country === 'uk') {
    if ($manufacturer == 'ford') {
        ...
    } else {
        ...
    }
} else if (....) {
  ...
}
```

You get the idea. This quickly becomes spaghetti code, especially if that logic spreads around different layers of your application - controllers, models, views/templates, javascript. God forbid you have to add a new manufacturer or change the presentation of Honda in Canada...

Maybe you can handle some of these buisiness rules via different URIs that point to different controllers that load different views, but that may not be desirable and could lead to code duplication.

There has to a better way of handling these business rules and context facets, right?

There is!


# Examples

## Basic Example

You'll likely do all of this early in the bootstrapping process, but you can really do it at any time in the request cycle before you need to use the data

#### 1. configure your variants with respect to the appropriate context.

```php
$config = [

    // 'common' is the fallback default value for all variants.
    // Useful for defining commonalities shared by all contexts
  
    'common' => [
        'show_tuner_truck_module' => true,
        'date_format' => 'M j, Y'
        'comment_query_criteria' => 'Acme\Comment\Criteria\Member' // Give this to a repository
        'show_comment_ip' => false
    ],
  
    // 'defaults' are the default configurations for each specific context value
    // These override 'common' configs, if present
  
    'defaults' => [
        'UK' => [
            'date_format' => 'j M, Y'
            'show_comment_ip' => false
        ],
        'Honda' => [
            'show_tuner_truck_module' => false
        ],
        'Admin' => [
            'comment_query_criteria' => 'Acme\Comment\Criteria\Admin' // Give this to a repository
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

#### 2. Then define the context of the request itself (ORDER MATTERS!!!)

A note about order: the order determines the order in which Context and its filters apply `array_merge`. It override from first to last, and falls back from last to first. That is, given the below in the `defaults` filter, `'Ford'` overrides `'UK'` which overrides `'Admin'`. However, if there is any configuration key defined in `'Admin'` that isn't defined in either `'UK'` or `'Ford'`, then it still gets included in the config array, thanks to `array_merge`

```php
$context = [
    'userType' => 'Admin',     // maybe get this from Session
    'country' => 'UK',         // maybe from a subdomain or user-agent query as part of the request
    'manufacturer' => 'Ford'   // maybe from a query param, route slug, or what have you
];
```

#### 3. Create the Context and use the desired filters (order doesn't matter here!)

```php
$Context = new Context($context);
$Context->addFilter('common', new CommonFilter($config));
$Context->addFilter('defaults', new DefaultsFilter($config));
$Context->addFilter('conditions', new ConditionsFilter($config));
```

#### 4. Retrieve the configuration that represents the request's context

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


## Changing Context Order Globally

If you want, you can change the context order for all filters, at any time in the request cycle.

#### Option 1: re-defining and resetting.
```php
$context = [
    'country' => 'UK',         // Notice that UK comes before Admin now
    'userType' => 'Admin',
    'manufacturer' => 'Ford'
];


$Context->setContext($context);
$filteredConfig = $Context->get();
```

Should now return
```php
[
    'show_tuner_truck_module' => false,     // Determined by the 'ford_uk' condition
    'date_format' => 'j M, Y'               // Determined by the 'UK' default
    'comment_query_criteria' => 'Acme\Comment\Criteria\Admin' // Determined by the 'Admin' default
    'show_comment_ip' => true               // Determined by the 'Admin' default
]
```

#### Option 2: changing the order on the fly
```php
$Context->getContext()->orderBy('country.userType.manufacturer');
```
Note here that you're re-ordering by the context keys to switch the values around, rather than defining a whole new context array. The reason for this is so that you can re-order by the underlying contexts, rather than having to worry about the values of those contexts.

You can also order by an array of the keys rather than string dot notation
```php
$Context->getContext()->orderBy(['country', 'userType', 'manufacturer']); // Same as dot notation
```

## Changing Context Order Per Filter

In addition to chaging the context globally for all filters, you can specify which filter gets the new context. Useful for supplying different matching combinations for `conditions` than for `defaults`

```php
$Context->getFilter('defaults')->changeContextSequence('manufacturer.userType.country');
$Context->getFilter('conditions')->changeContextSequence('');

$filtereConfig = $Context->get();
```

This effectively disables the conditions filter, and allows `defaults` to use a different fallback/override order.

## Reducing Context

Even if your initial context contained three facets, you don't necessarily need to utilize all three when re-ordering.

```php
$Conext->getContext()->orderByAndGetIntersection(['country', 'manufacturer']);
$Conext->getContext()->orderByAndGetIntersection('country.manufacturer'); // dot notation is valid as well
```
By doing the above, you've effectively dropped 'userType' out of the context scope entirely, and then re-ordered accordingly, meaning that no matter what the 'userType' was set to, it will never match against any defaults or conditions that reference it.

This of course can also be accomplished by resetting with a new context entirely:

```php
$context = [
    'country' => 'UK',
    'manufacturer' => 'Ford'
];


$Context->setContext($context);
$filteredConfig = $Context->get();
```

You can also choose to re-order only a subset of facets *without* dropping any of them, allowing you to re-order only the facets you really care about for the given request.

```php
$Conext->getContext()->orderBy('country.manufacturer');
```

This will simply leave any unspecified facets in their original order, at the *end* of the newly ordered array. So in this above situation, the context would look like this, internally:
```php
[
    'country' => 'UK',
    'manufacturer' => 'Ford',
    'userType' => 'Admin' // userType wasn't specified, so it appears at the end
];
