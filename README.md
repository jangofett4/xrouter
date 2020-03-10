## XRouter

XRouter is a basic routing system written in plain PHP. It does not offer much of a feature, also it's not that secure.  
I wrote this router when I found out routing in PHP is pretty easy. Also there wasn't any small, working and up-to-date (as of writing this, works with PHP 7.1.33) router.  
I am pretty sure there is a safe and more modern router now, but if you want some pretty basic routing, this will do the job.

### Features

- Working in PHP 7.1.33
- No dependencies
- GET and POST is supported
- Regex-checked URL parameters

As I said, this is not a complete router, this is a pretty basic and small router.

### Installing

Just get `Route.php` and `Router.php` into anywhere in your project.

### Setting Up

First you need to enable URL rewriting, I covered Apache server here, but router works with nginx too.  
Locate your `httpd.conf` and find the following line:

```conf
# LoadModule rewrite_module modules/mod_rewrite.so
```

Remove the `#` at the start to enable URL rewriting.  
For your website you also need to redirect all requests to `/index.php`, so we can manually route it.  
To achieve this you need a `.htaccess` file in your website's PUBLIC directory (generally index directory):

```htaccess
RewriteEngine on
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteCond %{SCRIPT_FILENAME} !-l
RewriteRule ^(.*)$ index.php/$1
```

### Using - GET and POST

Setup your routes in a file:

```php
/* Routes.php */

include_once 'path/to/Router.php';

Router::get('/', function() {
    require('views/main_page.php');
});
```

Include `Routes.php` in `index.php` of your project and start router:

```php
/* index.php in '/' of your project */
include_once 'path/to/Router.php'; // Not needed, since included in 'Routes.php', but for safe measures
include_once 'Routes.php'

Router::start();
```

And your `main_page.php` in `views` directory:

```html
<html>
    <head>
        <title>Routing success</title>
    </head>
    <body>
        <h4>Congratultions, you have set-up a route for '/'</h4>
    </body>
</html>
```

And you pretty much have set-up your first route.  
Post requests are pretty much same with only difference is `Router::get(...)` to `Router::post(...)`  
To pass parameters between files (or from router to file) read next section.

### Using - With URL Parameters

Setup your routes in a file:

```php
/* Routes.php */

include_once 'path/to/Router.php';

Router::get('/user/:id', function($id) {
    $_GET['id'] = $id;
    require('views/user.php');
}, "/[0-9]+/");
```

This route will need a `id` parameter, this parameters is passed to lambda, and is ensured to be a numeric (/[0-9]+/ regex).
We also need to pass this down to our view, I used `$_GET` variable here, for post `$_POST` can be used, or if you want to you can use `$_SESSION` too.
Include `Routes.php` in `index.php` of your project and start router:

```php
/* index.php in '/' of your project */
include_once 'path/to/Router.php'; // Not needed, since included in 'Routes.php', but for safe measures
include_once 'Routes.php'

Router::start();
```

And your `user.php` in `views` directory:

```html
<html>
    <head>
        <title>User details</title>
    </head>
    <body>
        <h4>User issued details for <?= $_GET['id'] ?></h4>
    </body>
</html>
```

And you pretty much have set-up your first route.

### Performance

I optimized everything I can but since router re-creates all routes from scratch for every request, might be a littly loaddy on CPU (just a tad bit).  
Maybe a cache can be implemented in order to preserve compiled routes, then later use them instead of rebuilding.