<?php

namespace Obullo\Mvc;

/**
 * View model
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ViewModel implements ViewModelInterface
{
    /**
     * Template
     *
     * @var mixed
     */
    protected $template = null;

    /**
     * Variables
     *
     * @var array
     */
    protected $variables = array();

    /**
     * Constructor
     *
     * @param array variables
     */
    public function __construct($variables = array())
    {
        $this->variables = $variables;
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
     * Set template
     *
     * @param string|object $template name or object
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Returns to template
     *
     * @return string|object
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
