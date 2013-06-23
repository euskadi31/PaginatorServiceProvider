<?php
/**
 * @package     Paginator
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2013 Axel Etcheverry (http://www.axel-etcheverry.com)
 * Displays     <a href="http://creativecommons.org/licenses/MIT/deed.fr">MIT</a>
 * @license     http://creativecommons.org/licenses/MIT/deed.fr    MIT
 */

/**
 * @namespace
 */
namespace Paginator\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Twig_Loader_Filesystem;

class PaginatorServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['paginator.template']  = 'Paginator.html.twig';
        $app['paginator.range']     = 5;

        $app['twig.loader']->addLoader(new Twig_Loader_Filesystem(__DIR__ . '/../View'));

        //$app['twig.path'][] = __DIR__ . '/../View';
        $app['twig']->addExtension(new TwigPaginatorExtension());
    }

    public function boot(Application $app)
    {

    }
}