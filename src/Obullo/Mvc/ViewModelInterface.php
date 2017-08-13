<?php

namespace Obullo\Mvc;

/**
 * View Model Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ViewModelInterface
{
    /**
     * Set view variables
     *
     * @param mixed $key key
     * @param mixed $val val
     */
    public function setVariable($key, $val = null);

    /**
     * Set template
     *
     * @param string|object $template name or object
     */
    public function setTemplate($template);

    /**
     * Returns to template
     *
     * @return string|object
     */
    public function getTemplate();
}
