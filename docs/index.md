Jlem\Context is a very simple library for juggling configuration settings based on multi-facted contexts

# Usecase

Suppose you have car tuner enthusiast site with a page that shows information about various car models or parts from different manufacturers, with comments. However, *depending on which manufacturer it is, which country you're from, and which user group you are* the format, layout, and information on this page can vary a little bit:

* Ford has a section about tuner trucks that Honda doesn't have
* Except Ford UK doesn't show this because tuner trucks aren't as popular in the UK
* All manufacturer pages in the UK show dates formatted as Day Month, Year
* Admins can see all comments (including pending/soft-deleted) as well IP addresses associated with comments
* However, let's pretend UK has privacy laws that prohibits displaying IP addresses to non-employees/owners
* Moderators can see all public comments as well as see/approve pending comments
* In the Ford section, comments must be manually approved before being displayed
* Regular users can only see publically visible comments

Whew, that's a lot of highly specific business rules overloading one request, don't you think? 

You may be tempted to do something like this:

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

Maybe you can handle some of these business rules via different URIs that point to different controllers that load different views, but that may not be desirable and could lead to code duplication.

There has to a better way of handling these business rules and context facets, right?

There is!
