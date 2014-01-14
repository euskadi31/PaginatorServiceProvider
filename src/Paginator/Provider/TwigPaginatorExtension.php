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

use Twig_Extension;
use Twig_Function_Method;
use Twig_Environment;
use InvalidArgumentException;
use Paginator\Paginator;

class TwigPaginatorExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'paginator' => new Twig_Function_Method($this, 'paginator', array(
                'needs_environment' => true, 
                'is_safe'           => array('html')
            ))
        );
    }

    /**
     * 
     * @param  Twig_Environment    $twig
     * @param  Paginator\Paginator $context
     * @return string
     */
    public function paginator(Twig_Environment $twig, Paginator $paginator)
    {
        $globals = $twig->getGlobals();
        
        $paginator->setPageRange($globals['app']['paginator.range']);

        $context = $paginator->getPages();

        $attributes = clone $globals['app']['request']->attributes;
        $attributes->remove('_route');
        $attributes->remove('_route_params');

        $context['query'] = $globals['app']['request']->query->all() + $attributes->all();

        return $globals['app']['twig']->render(
            $globals['app']['paginator.template'], 
            $context
        );
    }

    public function getName()
    {
        return 'paginator';
    }
}
