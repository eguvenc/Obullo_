<?php

namespace Obullo\View;

/**
 * Template Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface TemplateInterface
{
    /**
     * Returns to template name
     * 
     * @return string
     */
    public function getName();

    /**
     * Create template variables
     * 
     * @return void
     */
    public function setVariables();

    /**
     * Returns to template variables
     * 
     * @return array
     */
    public function getVariables();
   
}