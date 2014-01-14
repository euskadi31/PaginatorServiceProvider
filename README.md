PaginatorServiceProvider
========================

Paginator service provider for Silex

Example
-------

```php
<?php

namespace App;

use Paginator;
use Silex;

require __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__,
    'twig.options' => array(
        'auto_reload' => true,
        'cache' => __DIR__ . '/cache'
    )
));
$app->register(new Paginator\Provider\PaginatorServiceProvider());

$app->get('/blog/{page}', function($page) use ($app) {

    $count = 150; // number of items

    $paginator = new Paginator\Paginator();
    $paginator->setCurrentPageNumber($page); // set current page
    $paginator->setItemCountPerPage(20); // items per page, default : 10
    $paginator->setTotalItemCount($count);

    return $app['twig']->render('test.html.twig', array(
        'paginator' => $paginator
    ));
})->assert('page', '\d+');

$app->run();
```

twig: test.html.twig
```html
{{ paginator(paginator) }}
```

Install
-------

Using composer installer and autoloader is probably the easiest way to install Paginator and get it running. 
What you need is just a composer.json file in the root directory of your project:
```json
{
    "require": {
        "euskadi31/paginator-service-provider": "dev-master"
    }
}
```
