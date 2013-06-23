<?php

namespace Demo;

use Paginator;
use Silex;

require __DIR__ . '/../vendor/autoload.php';

$page = isset($argv[1]) ? $argv[1] : 1;

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

$app->get('/', function() use ($app, $page) {

    $paginator = new Paginator\Paginator();
    $paginator->setCurrentPageNumber($page);
    $paginator->setTotalItemCount(150);

    return $app['twig']->render('test.html.twig', array(
        'paginator' => $paginator
    ));
});

$app->run();
