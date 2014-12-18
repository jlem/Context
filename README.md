A very simple library for juggling configuration settings based on a given combination of multiple contexts

# Usecase

Suppose you have a page that shows information about various car models or parts from different manufacturers (with comments), but *depending on which manufacturer it is, which country you're from, and which user group you are* the format, layout, and information on this page can vary a little bit:

* Ford has a section about tuner trucks that Honda doesn't have
* Except Ford UK doesn't show this because tuner trucks aren't as popular in the UK
* All manufacturer pages in the UK show dates formatted as Day Month, Year
* Admins can see all comments (including pending/soft-deleted) as well IP addresses associated with comments
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

You get the idea. This becomes impossible to manage and maintain very quickly, especially if that logic spreads around different layers of your application - controllers, models, views/templates, javascript...

Maybe you can handle some of them via different URIs that point to different controllers that load different views, but that may not be desirable and could lead to code duplication.

There has to a better way of handling these complex contexts, right?

There is!


# Jlem/Context

The basic idea behind Jlem/Context: configure your variants with respect to the appropriate contexts

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

Then lookup the appropriate configuration values based on the provided context of the request itself:

```php
$context = [
  'userType' => 'Admin'     // maybe get this from Session
  'country' => 'UK'         // maybe from a subdomain or user-agent query as part of the request
  'manufacturer' => 'Ford'  // maybe from a query param, route slug, or what have you
];
```
