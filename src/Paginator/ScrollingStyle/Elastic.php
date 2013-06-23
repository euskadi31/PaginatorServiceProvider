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
 * A Google-like scrolling style.  Incrementally expands the range to about
 * twice the given page range, then behaves like a slider.  See the example
 * link.
 *
 */
class Elastic extends Sliding
{
    /**
     * Returns an array of "local" pages given a page number and range.
     *
     * @param  Paginator\Paginator $paginator
     * @param  int $pageRange Unused
     * @return array
     */
    public function getPages(Paginator $paginator, $pageRange = null)
    {
        $pageRange  = $paginator->getPageRange();
        $pageNumber = $paginator->getCurrentPageNumber();

        $originalPageRange = $pageRange;
        $pageRange         = $pageRange * 2 - 1;

        if ($originalPageRange + $pageNumber - 1 < $pageRange) {
            $pageRange = $originalPageRange + $pageNumber - 1;
        } elseif ($originalPageRange + $pageNumber - 1 > count($paginator)) {
            $pageRange = $originalPageRange + count($paginator) - $pageNumber;
        }

        return parent::getPages($paginator, $pageRange);
    }
}