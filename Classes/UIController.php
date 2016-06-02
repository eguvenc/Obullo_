<?php

use Obullo\Controller;
use UI\HeaderController;
use UI\FooterController;

/**
 * UI Controller.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class UIController extends Controller
{
    /**
     * Constructor
     * 
     * @param container $container container
     */
    public function __construct($container)
    {
        $header = new HeaderController($container);
        $header->create();

        $footer = new FooterController($container);
        $footer->create();
    }
}