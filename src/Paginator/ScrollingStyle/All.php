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

/**
 * A scrolling style that returns every page in the collection.
 * Useful when it is necessary to make every page available at
 * once--for example, when using a drop-down menu pagination control.
 */
class All implements ScrollingStyleInterface
{
    /**
     * Returns an array of all pages given a page number and range.
     *
     * @param  Paginator\Paginator $paginator
     * @param  int $pageRange Unused
     * @return array
     */
    public function getPages(Paginator $paginator, $pageRange = null)
    {
        return $paginator->getPagesInRange(1, $paginator->count());
    }
}