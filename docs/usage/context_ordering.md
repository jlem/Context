Since the main Context object and the included filters all make use of `array_merge` behind the scenes for cascading/overriding, the order in which you define things matters:

* The order in which you define your `$contextData` matters
* The order in which you add filters to `$Context` matters
* The order in which you add conditions to the `'conditions'` array matters
* The order of everything else *DOES NOT MATTER*

Overriding happens first to last, and falls back from last to first. Using the context data from the previous example:

```php
$contextData = [
    'user' => 'Admin',
    'country' => 'UK',
    'manufacturer' => 'Ford'
];
```

`'Ford'` overrides `'UK'` which overrides `'Admin'`. However, if there is any configuration key defined in `'Admin'` that isn't defined in either `'UK'` or `'Ford'`, then it still gets included in the config array, thanks to `array_merge`.

You can change the context order for all filters, at any time in the request cycle.


# Changing the order globally

```php
$Context->reorderContext(['country', 'user', 'manufacturer']);
$Context->reorderContext('country,user,manufacturer'); // alternative comma-separated string syntax

$filteredContext = $Context->get();
```

Should now return
```php
[
    'date_format' => 'j M, Y',              // Determined by the 'UK' default
    'show_comment_ip' => true,              // Determined by the 'Admin' default
    'comment_query_criteria' => 'Acme\Comment\Criteria\Admin', // Determined by the 'Admin' default
    'show_tuner_truck_module' => false,     // Determined by the 'UK' default
    'manually_approve_comments' => true,    // Determined by the 'Ford' default
    'logo' => 'ford_uk.png',                // Determined by the 'Ford' default
]
```

Note here that you're re-ordering by the context keys, rather than defining a whole new context array. That way you can re-order the underlying context facets at any point, without having to define a whole new context data array.


# Changing order per filter

In addition to chaging the context globally for all filters, you can specify certain context orders for certain filters. These will always override any global context reordering.

```php
$Context->getFilter('defaults')->reorderContext(['manufacturer', 'user', 'country']);
$Context->reorderFilterContext('defaults', ['manufacturer', 'user', 'country']); // Alternative

$Context->getFilter('conditions')->reorderContext(['country', 'manufacturer', 'user']);
$Context->reorderFilterContext('conditions', ['country', 'manufacturer', 'user']); // Alternative
```

# Reducing Context Scope
TODO
