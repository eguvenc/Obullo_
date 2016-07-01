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
     * Set cache data as removal
     * 
     * @var boolean
     */
    protected $removal = false;

    /**
     * Variables
     * 
     * @var array
     */
    protected $variables = array();

    /**
     * Expiration
     * 
     * @var null|int
     */
    protected $expiration = null;

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

    /**
     * Set view variables
     * 
     * @param mixed $key key
     * @param mixed $val val
     */
    public function setVariable($key, $val = null)
    {
        if (is_array($key)) {
            $this->variables = $key;
        } else {
            $this->variables[$key] = $val;
        }
    }

    /**
     * Get view variables
     * 
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Set cache expiration time
     * 
     * @param int $expiration time
     */
    public function setCache($expiration)
    {
        $this->expiration = $expiration;
    }

    /**
     * Remove cache data
     *
     * @param boolean $default switch
     * 
     * @return boolean
     */
    public function removeCache($default = true)
    {
        $this->removal = $default;
    }

    /**
     * Check cache data marked as removal
     * 
     * @return boolean
     */
    public function isRemoval()
    {
        return $this->removal;
    }

}