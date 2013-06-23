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
namespace Paginator\ScrollingStyle;

use Paginator\Paginator;

interface ScrollingStyleInterface
{
    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @param  Paginator\Paginator $paginator
     * @param  int $pageRange (Optional) Page range
     * @return array
     */
    public function getPages(Paginator $paginator, $pageRange = null);
}