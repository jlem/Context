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

#### 2. Then define the context of the request itself (ORDER MATTERS!!!)

A note about order: the order determines the order in which Context and its filters apply `array_merge`. It override from first to last, and falls back from last to first. That is, given the below in the `defaults` filter, `'Ford'` overrides `'UK'` which overrides `'Admin'`. However, if there is any configuration key defined in `'Admin'` that isn't defined in either `'UK'` or `'Ford'`, then it still gets included in the config array, thanks to `array_merge`

```php
$context = [
    'user' => 'Admin',          // maybe get this from Session
    'country' => 'UK',          // maybe from a subdomain or user-agent query as part of the request
    'manufacturer' => 'Ford'    // maybe from a query param, route slug, or what have you
];
```

#### 3. Create the Context and use the desired filters (ORDER MATTERS!!!)

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


## Changing Context Order

If you want, you can change the context order for all filters, at any time in the request cycle.

#### Option 1: changing the order globally
```php
$Context->reorderContext('country.user.manufacturer');
$Context->reorderContext(['country', 'user', 'manufacturer']); // optional array syntax

$filteredContext = $Context->get();
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

Note here that you're re-ordering by the context keys, rather than defining a whole new context array. The reason for this is so that you can re-order by the underlying contexts, rather than having to worry about the values of those contexts.


#### Option 2: changing order per filter

In addition to chaging the context globally for all filters, you can specify certain context orders for certain filters. These will always override any global context reordering.

```php
$Context->getFilter('defaults')->reorderContext('manufacturer.user.country');
$Context->getFilter('conditions')->reorderContext('country.manufacturer.user');
```

## Reducing Context

Even if your initial context contained three facets, you don't necessarily need to utilize all three when re-ordering.

```php
$Conext->reorderContext('country.manufacturer');

// Context has become:
[
    'country' => 'UK',
    'manufacturer' => 'Ford'
];
```

By doing the above, you've effectively dropped `'user'` out of the context scope entirely, and then re-ordered accordingly, meaning that the `'Admin'` default settings are ignored for this request.

If you want to reorder just one or two facets, pass in `false` as the second parameter to keep all context facets, but place the specified ones at the beginning:

```php
$Conext->reorderContext('country.manufacturer', false);

// Context has become:
[
    'country' => 'UK',
    'manufacturer' => 'Ford',
    'user' => 'Admin'  // Was not specified, so it stays on the end (array_merge behavior)
];
```

## Resetting Context Order

**NOTE** When reorders are supplied (as opposed to full resets with data), they mutate the state of how the Context and Filter objects use the originally given context data, but do not mutate the originally given context data itself. This means that subsequent `get()`s will keep using the previously given context order, but can be reset to the original (or last supplied) context data at any time using:

```php
$Context->resetContextOrder();

// Context is now back to:
[
    'user' => 'Admin',
    'country' => 'UK',
    'manufacturer' => 'Ford'
];
```

Or reset per filter:

```php
$Context->getFilter('defaults')->resetContextOrder();
```
*note, if a global reorder was defined, then the filter will still inherit this, unless the global order is also reset (or the desired order is supplied for the filter*

## Disabling a Filter

```php
$Context->disableFilter('defaults');
```

Now only the `'common'` and `'conditions'` filters will be used for the request. To re-enable it:

```php
$Context->enableFilter('defaults');
```

## Disabling Context

```php
$Context->disableContext();
```

This effectively uses only the `'common'` filter, which does not make use of contexts at all. Put another way, it's the same as disabling *all but* the `'common'` filter.

To re-enable it:

```php
$Context->enableContext();
```
