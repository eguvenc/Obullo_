<?php

namespace Obullo\View\Gui;

/**
 * GUI Component ( HMVC Design pattern Derived from JAVA )
 *
 * http://www.javaworld.com/article/2076128/design-patterns/-
 * hmvc--the-layered-pattern-for-developing-strong-client-tiers.html
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ViewComponent implements ViewComponentInterface
{
    /**
     * Request path ( Call view controllers /header, /footer, /navbar )
     * 
     * @var string
     */
    protected $path;

    /**
     * Constructor
     * 
     * @param string $path hmvc request path
     */
    public function __construct($path)
    {
    	$this->path = $path;
    }

    /**
     * Returns to hmvc request path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

}