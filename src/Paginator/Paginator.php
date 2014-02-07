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
namespace Paginator;

use Countable;
use Traversable;
use InvalidArgumentException;
use Paginator\ScrollingStyle\ScrollingStyleInterface;

class Paginator implements Countable
{
    /**
     * Default scrolling style
     *
     * @var string
     */
    protected static $defaultScrollingStyle = 'Sliding';

    /**
     * Default item count per page
     *
     * @var int
     */
    protected static $defaultItemCountPerPage = 10;

    /**
     * Current page number (starting from 1)
     *
     * @var int
     */
    protected $currentPageNumber = 1;

    /**
     * Number of items per page
     *
     * @var int
     */
    protected $itemCountPerPage = null;

    /**
     * Number of pages
     *
     * @var int
     */
    protected $pageCount = null;

    /**
     * Number of local pages (i.e., the number of discrete page numbers
     * that will be displayed, including the current page number)
     *
     * @var int
     */
    protected $pageRange = 5;

    /**
     * Pages
     *
     * @var array
     */
    protected $pages = null;

    /**
     * Number of total items
     *
     * @var array
     */
    protected $totalItemCount = 0;
    
    /**
     * Constructor
     *
     * @param int $totalItemsCount
     * @param int $itemCountPerPage
     * @param int $currentPageNumber
     */
    public function __construct($totalItemsCount = null, $itemCountPerPage = null, $currentPageNumber = null)
    {
        if($totalItemsCount) {
            $this->setTotalItemCount($totalItemsCount);
        }
        if($itemCountPerPage) {
            $this->setItemCountPerPage($itemCountPerPage);
        }
        if($currentPageNumber) {
            $this->setCurrentPageNumber($currentPageNumber);
        }
    }

    /**
     * Sets the default scrolling style.
     *
     * @param  string $scrollingStyle
     */
    public static function setDefaultScrollingStyle($scrollingStyle = 'Sliding')
    {
        static::$defaultScrollingStyle = $scrollingStyle;
    }

    /**
     * Returns the default scrolling style.
     *
     * @return  string
     */
    public static function getDefaultScrollingStyle()
    {
        return static::$defaultScrollingStyle;
    }

    /**
     * Set the default item count per page
     *
     * @param int $count
     */
    public static function setDefaultItemCountPerPage($count)
    {
        static::$defaultItemCountPerPage = (int) $count;
    }

    /**
     * Get the default item count per page
     *
     * @return int
     */
    public static function getDefaultItemCountPerPage()
    {
        return static::$defaultItemCountPerPage;
    }

    /**
     * Returns the number of pages.
     *
     * @return int
     */
    public function count()
    {
        if (!$this->pageCount) {
            $this->pageCount = $this->_calculatePageCount();
        }

        return $this->pageCount;
    }

    /**
     * Returns the current page number.
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->normalizePageNumber($this->currentPageNumber);
    }

    /**
     * Sets the current page number.
     *
     * @param  int $pageNumber Page number
     * @return Paginator $this
     */
    public function setCurrentPageNumber($pageNumber)
    {
        $this->currentPageNumber = (int) $pageNumber;
        $this->currentItems      = null;
        $this->currentItemCount  = null;

        return $this;
    }

    /**
     * Returns the number of items per page.
     *
     * @return int
     */
    public function getItemCountPerPage()
    {
        if (empty($this->itemCountPerPage)) {
            $this->itemCountPerPage = static::getDefaultItemCountPerPage();
        }

        return $this->itemCountPerPage;
    }

    /**
     * Sets the number of items per page.
     *
     * @param  int $itemCountPerPage
     * @return Paginator $this
     */
    public function setItemCountPerPage($itemCountPerPage = -1)
    {
        $this->itemCountPerPage = (int) $itemCountPerPage;
        if ($this->itemCountPerPage < 1) {
            $this->itemCountPerPage = $this->getTotalItemCount();
        }
        $this->pageCount        = $this->_calculatePageCount();
        $this->currentItems     = null;
        $this->currentItemCount = null;

        return $this;
    }

    /**
     * Returns the page range (see property declaration above).
     *
     * @return int
     */
    public function getPageRange()
    {
        return $this->pageRange;
    }

    /**
     * Sets the page range (see property declaration above).
     *
     * @param  int $pageRange
     * @return Paginator $this
     */
    public function setPageRange($pageRange)
    {
        $this->pageRange = (int) $pageRange;

        return $this;
    }

    /**
     * Returns the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return array
     */
    public function getPages($scrollingStyle = null)
    {
        if ($this->pages === null) {
            $this->pages = $this->_createPages($scrollingStyle);
        }

        return $this->pages;
    }

    /**
     * Returns a subset of pages within a given range.
     *
     * @param  int $lowerBound Lower bound of the range
     * @param  int $upperBound Upper bound of the range
     * @return array
     */
    public function getPagesInRange($lowerBound, $upperBound)
    {
        $lowerBound = $this->normalizePageNumber($lowerBound);
        $upperBound = $this->normalizePageNumber($upperBound);

        $pages = array();

        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        return $pages;
    }

    /**
     * Brings the item number in range of the page.
     *
     * @param  int $itemNumber
     * @return int
     */
    public function normalizeItemNumber($itemNumber)
    {
        $itemNumber = (int) $itemNumber;

        if ($itemNumber < 1) {
            $itemNumber = 1;
        }

        if ($itemNumber > $this->getItemCountPerPage()) {
            $itemNumber = $this->getItemCountPerPage();
        }

        return $itemNumber;
    }

    /**
     * Brings the page number in range of the paginator.
     *
     * @param  int $pageNumber
     * @return int
     */
    public function normalizePageNumber($pageNumber)
    {
        $pageNumber = (int) $pageNumber;

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }

        $pageCount = $this->count();

        if ($pageCount > 0 && $pageNumber > $pageCount) {
            $pageNumber = $pageCount;
        }

        return $pageNumber;
    }

    public function setTotalItemCount($count)
    {
        $this->totalItemCount = (int)$count;

        return $this;
    }

    public function getTotalItemCount()
    {
        return $this->totalItemCount;
    }

    public function render()
    {
        return '';
    }

    /**
     * Calculates the page count.
     *
     * @return int
     */
    protected function _calculatePageCount()
    {
        return (int) ceil($this->getTotalItemCount() / $this->getItemCountPerPage());
    }

    /**
     * Creates the page collection.
     *
     * @param  string $scrollingStyle Scrolling style
     * @return array
     */
    protected function _createPages($scrollingStyle = null)
    {
        $pageCount         = $this->count();
        $currentPageNumber = $this->getCurrentPageNumber();

        $pages = array(
            'pageCount'         => $pageCount,
            'itemCountPerPage'  => $this->getItemCountPerPage(),
            'first'             => 1,
            'current'           => $currentPageNumber,
            'last'              => $pageCount
        );

        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages['previous'] = $currentPageNumber - 1;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages['next'] = $currentPageNumber + 1;
        }

        // Pages in range
        $scrollingStyle = $this->_loadScrollingStyle($scrollingStyle);
        $pages['pagesInRange']     = $scrollingStyle->getPages($this);
        $pages['firstPageInRange'] = min($pages['pagesInRange']);
        $pages['lastPageInRange']  = max($pages['pagesInRange']);


        return $pages;
    }

    /**
     * Loads a scrolling style.
     *
     * @param string $scrollingStyle
     * @return ScrollingStyleInterface
     * @throws InvalidArgumentException
     */
    protected function _loadScrollingStyle($scrollingStyle = null)
    {
        if ($scrollingStyle === null) {
            $scrollingStyle = static::$defaultScrollingStyle;
        }

        switch (strtolower(gettype($scrollingStyle))) {
            case 'object':
                if (!$scrollingStyle instanceof ScrollingStyleInterface) {
                    throw new InvalidArgumentException(
                        'Scrolling style must implement Paginator\ScrollingStyle\ScrollingStyleInterface'
                    );
                }

                return $scrollingStyle;

            case 'string':
                $className = '\Paginator\ScrollingStyle\\' . $scrollingStyle;
                return new $className();

            case 'null':
                // Fall through to default case

            default:
                throw new InvalidArgumentException(
                    'Scrolling style must be a class ' .
                    'name or object implementing Paginator\ScrollingStyle\ScrollingStyleInterface'
                );
        }
    }

    /**
     * Serializes the object as a string.  Proxies to {@link render()}.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
