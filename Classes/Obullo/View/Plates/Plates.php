<?php

namespace Obullo\View\Plates;

use Closure;
use League\Plates\Engine;
use Psr\Log\LoggerInterface as Logger;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

/**
 * Plates engine - http://platesphp.com/
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Plates extends Engine implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Constructor
     * 
     * @param stirng $path default
     */
    public function __construct($path)
    {
        parent::__construct($path);
    }

    /**
     * Create a new template and render it.
     * 
     * @param string $name name
     * @param array  $data data
     * 
     * @return string
     */
    public function render($name, array $data = array())
    {
        return $this->make($name)->render($data);
    }

    /**
     * Create a new template.
     * 
     * @param string $name name
     * 
     * @return Template
     */
    public function make($name)
    {
         $template = new Template($this, $name);
         $template->setContainer($this->getContainer());

         return $template;
    }

}